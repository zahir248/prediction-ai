<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatGPTService implements AIServiceInterface
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1/chat/completions';
    protected $webScrapingService;
    protected $sslVerify;
    protected $currentPredictionHorizon;
    protected $model = 'gpt-4o';

    public function __construct(WebScrapingService $webScrapingService)
    {
        $this->apiKey = config('services.chatgpt.api_key');
        $this->webScrapingService = $webScrapingService;
        
        // Check if we should verify SSL (default to true for production)
        $this->sslVerify = config('services.chatgpt.ssl_verify', !app()->environment('local', 'development'));
    }

    public function analyzeText($text, $analysisType = 'prediction-analysis', $sourceUrls = null, $predictionHorizon = null, $analytics = null, $target = null)
    {
        try {
            // Validate API key
            if (empty($this->apiKey)) {
                Log::error('ChatGPT API key not configured');
                return $this->getFallbackResponse($analysisType);
            }
            
            // Validate API key format (should start with sk-)
            if (!str_starts_with($this->apiKey, 'sk-')) {
                Log::error('Invalid ChatGPT API key format. Key should start with "sk-"');
                return $this->getFallbackResponse($analysisType);
            }

            // Scrape content from source URLs if provided
            $scrapedContent = null;
            $scrapingSummary = null;
            if ($sourceUrls && count($sourceUrls) > 0) {
                Log::info("Starting to scrape " . count($sourceUrls) . " source URLs");
                $scrapedContent = $this->webScrapingService->scrapeMultipleUrls($sourceUrls);
                
                // Create scraping summary for better user feedback
                $successfulScrapes = array_filter($scrapedContent, fn($s) => $s['status'] === 'success');
                $failedScrapes = array_filter($scrapedContent, fn($s) => $s['status'] === 'error');
                
                $scrapingSummary = [
                    'total_urls' => count($sourceUrls),
                    'successful_scrapes' => count($successfulScrapes),
                    'failed_scrapes' => count($failedScrapes),
                    'success_rate' => count($sourceUrls) > 0 ? round((count($successfulScrapes) / count($sourceUrls)) * 100, 1) : 0,
                    'failed_urls' => array_map(function($failed) {
                        return [
                            'url' => $failed['url'],
                            'error' => $failed['error'] ?? 'Unknown error',
                            'status_code' => $failed['status_code'] ?? null
                        ];
                    }, $failedScrapes)
                ];
                
                Log::info("Completed scraping URLs. Results: " . json_encode(array_column($scrapedContent, 'status')));
                Log::info("Scraping summary: " . json_encode($scrapingSummary));
                
                // If no URLs were successfully scraped, provide warning
                if (count($successfulScrapes) === 0) {
                    Log::warning("No URLs were successfully scraped. All URLs may be inaccessible or blocked.");
                }
            }

            // Store the current prediction horizon for fallback responses
            $this->currentPredictionHorizon = $predictionHorizon;
            
            $prompt = $this->createAnalysisPrompt($text, $analysisType, $sourceUrls, $scrapedContent, $predictionHorizon, $scrapingSummary, $target);
            
            // Set execution time limit to 5 minutes for long AI requests
            set_time_limit(300);
            
            // Record start time for API request timing
            $apiStartTime = microtime(true);
            
            Log::info("Execution time limit set to: " . ini_get('max_execution_time') . " seconds");
            Log::info("Memory limit: " . ini_get('memory_limit'));
            Log::info("Starting ChatGPT API request at: " . now());
            
            Log::info("Sending request to ChatGPT API with prompt length: " . strlen($prompt));
            Log::info("API Key configured: " . (!empty($this->apiKey) ? 'Yes (length: ' . strlen($this->apiKey) . ')' : 'No'));
            Log::info("Request URL: " . $this->baseUrl);
            Log::info("Authentication: Using Bearer token");
            
            $response = Http::timeout(300)->withOptions([
                'verify' => $this->sslVerify,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify,
                ]
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 1,
                'max_completion_tokens' => 6000
            ]);
            
            // Calculate API response time
            $apiResponseTime = round(microtime(true) - $apiStartTime, 3);
            
            // Update analytics if provided
            if ($analytics) {
                $this->updateAnalyticsWithApiResponse($analytics, $response, $apiResponseTime);
            }
            
            Log::info("ChatGPT API response received at: " . now());
            Log::info("API response time: " . $apiResponseTime . " seconds");
            Log::info("Response status: " . $response->status());
            Log::info("Response body length: " . strlen($response->body()));

            if ($response->successful()) {
                $data = $response->json();
                
                // Debug: Log the full response structure
                Log::info('ChatGPT API response structure debug', [
                    'response_keys' => array_keys($data),
                    'has_choices' => isset($data['choices']),
                    'choices_count' => isset($data['choices']) ? count($data['choices']) : 0,
                    'has_choices_0' => isset($data['choices'][0]),
                    'choices_0_keys' => isset($data['choices'][0]) ? array_keys($data['choices'][0]) : 'N/A',
                    'has_message' => isset($data['choices'][0]['message']),
                    'message_keys' => isset($data['choices'][0]['message']) ? array_keys($data['choices'][0]['message']) : 'N/A',
                    'has_content' => isset($data['choices'][0]['message']['content']),
                    'content_length' => isset($data['choices'][0]['message']['content']) ? strlen($data['choices'][0]['message']['content']) : 0,
                    'content_preview' => isset($data['choices'][0]['message']['content']) ? substr($data['choices'][0]['message']['content'], 0, 200) : 'N/A'
                ]);
                
                if (isset($data['choices'][0]['message']['content'])) {
                    $result = $data['choices'][0]['message']['content'];
                    
                    // Debug: Log the actual content received
                    Log::info('ChatGPT API content received', [
                        'content_length' => strlen($result),
                        'content_type' => gettype($result),
                        'content_preview' => substr($result, 0, 500),
                        'is_empty' => empty($result),
                        'is_null' => is_null($result)
                    ]);
                    
                    // Try to parse JSON response
                    $parsedResult = $this->parseJsonResponse($result);
                    
                    if ($parsedResult) {
                        // Extract confidence score from the AI response if available
                        $confidenceScore = $this->extractConfidenceFromAIResponse($parsedResult);
                        
                        // Add API timing metadata
                        $parsedResult['api_metadata'] = [
                            'api_response_time' => $apiResponseTime,
                            'api_response_time_unit' => 'seconds',
                            'api_timestamp' => now()->toISOString(),
                            'model_version' => $this->model,
                            'confidence_score' => $confidenceScore
                        ];
                        
                        // Add scraping metadata to the result
                        if ($scrapedContent || $scrapingSummary) {
                            $parsedResult['scraping_metadata'] = [
                                'total_sources' => count($sourceUrls),
                                'successfully_scraped' => count(array_filter($scrapedContent, fn($s) => $s['status'] === 'success')),
                                'scraped_at' => now()->toISOString(),
                                'scraping_summary' => $scrapingSummary,
                                'source_details' => array_map(function($source) {
                                    return [
                                        'url' => $source['url'],
                                        'title' => $source['title'] ?? 'N/A',
                                        'word_count' => $source['word_count'] ?? 0,
                                        'status' => $source['status'],
                                        'error' => $source['error'] ?? null,
                                        'status_code' => $source['status_code'] ?? null
                                    ];
                                }, $scrapedContent)
                            ];
                        }
                        
                        return $parsedResult;
                    }
                    
                    return $result;
                } else {
                    // Log detailed information about missing content
                    Log::warning('ChatGPT API response missing content', [
                        'response_structure' => $data,
                        'choices_exists' => isset($data['choices']),
                        'choices_0_exists' => isset($data['choices'][0]),
                        'message_exists' => isset($data['choices'][0]['message']),
                        'content_exists' => isset($data['choices'][0]['message']['content']),
                        'content_value' => $data['choices'][0]['message']['content'] ?? 'NOT_SET',
                        'content_type' => gettype($data['choices'][0]['message']['content'] ?? null)
                    ]);
                }
                
                Log::error('Unexpected ChatGPT API response structure: ' . json_encode($data));
                return $this->getFallbackResponse($analysisType);
            }
            
            Log::error('ChatGPT API request failed: ' . $response->status() . ' - ' . $response->body());
            return $this->getFallbackResponse($analysisType);
            
        } catch (\Exception $e) {
            Log::error('Error in ChatGPTService: ' . $e->getMessage());
            return $this->getFallbackResponse($analysisType);
        }
    }

    /**
     * Convert prediction horizon enum to human-readable text
     */
    protected function getHorizonText($horizon)
    {
        $horizonMap = [
            'next_two_days' => 'Next Two Days',
            'next_two_weeks' => 'Next Two Weeks',
            'next_month' => 'Next Month',
            'three_months' => 'Next 3 Months',
            'six_months' => 'Next 6 Months',
            'twelve_months' => 'Next 12 Months',
            'two_years' => 'Next 2 Years'
        ];

        return $horizonMap[$horizon] ?? 'Next Month';
    }

    protected function createAnalysisPrompt($text, $analysisType, $sourceUrls = null, $scrapedContent = null, $predictionHorizon = null, $scrapingSummary = null, $target = null)
    {
        $prompt = "You are a world-class AI prediction analyst with expertise in comprehensive future forecasting and strategic analysis. You are known for providing exceptionally detailed, thorough, and complex analysis that rivals top-tier consulting firms like McKinsey, BCG, and Bain. Your analysis should be comprehensive, nuanced, and deeply insightful.\n\n";
        $prompt .= "Text to analyze: {$text}\n\n";
        
        if ($target) {
            $prompt .= "TARGET: {$target}\n";
            $prompt .= "Focus analysis on how predictions, risks, and implications affect {$target}.\n\n";
        }
        
        if ($predictionHorizon) {
            $horizonText = $this->getHorizonText($predictionHorizon);
            $prompt .= "HORIZON: {$horizonText}\n";
            $prompt .= "Tailor all predictions and assessments to this timeframe.\n\n";
        }
        
        if ($sourceUrls && count($sourceUrls) > 0) {
            $prompt .= "IMPORTANT: You have been provided with the following additional sources that contain relevant context, data, or background information:\n";
            
            foreach ($sourceUrls as $index => $url) {
                $prompt .= "- Source " . ($index + 1) . ": {$url}\n";
            }
            
            // Add scraping summary if available
            if (isset($scrapingSummary) && $scrapingSummary) {
                $prompt .= "\nSCRAPING SUMMARY:\n";
                $prompt .= "Total URLs provided: {$scrapingSummary['total_urls']}\n";
                $prompt .= "Successfully scraped: {$scrapingSummary['successful_scrapes']}\n";
                $prompt .= "Failed to scrape: {$scrapingSummary['failed_scrapes']}\n";
                $prompt .= "Success rate: {$scrapingSummary['success_rate']}%\n";
                
                if ($scrapingSummary['failed_scrapes'] > 0) {
                    $prompt .= "\nFAILED URLS (These could not be accessed due to anti-bot protection, server errors, or other issues):\n";
                    foreach ($scrapingSummary['failed_urls'] as $failed) {
                        $prompt .= "- {$failed['url']}: {$failed['error']}";
                        if ($failed['status_code']) {
                            $prompt .= " (HTTP {$failed['status_code']})";
                        }
                        $prompt .= "\n";
                    }
                    $prompt .= "\nNote: For failed URLs, rely on your existing knowledge about the topic or source.\n";
                }
            }
            
            // Add actual scraped content if available
            if ($scrapedContent && count($scrapedContent) > 0) {
                $prompt .= "\nACTUAL CONTENT FROM SUCCESSFULLY SCRAPED SOURCES:\n";
                $prompt .= "The following is the real content extracted from the accessible URLs. Use this actual data in your analysis:\n\n";
                
                foreach ($scrapedContent as $index => $source) {
                    if ($source['status'] === 'success' && !empty($source['content'])) {
                        $prompt .= "=== SOURCE " . ($index + 1) . " ===\n";
                        $prompt .= "URL: {$source['url']}\n";
                        if (!empty($source['title'])) {
                            $prompt .= "Title: {$source['title']}\n";
                        }
                        $prompt .= "Content: {$source['content']}\n";
                        $prompt .= "Word Count: {$source['word_count']}\n";
                        $prompt .= "==================\n\n";
                    }
                }
            }
            
            $prompt .= "\nSOURCE INTEGRATION:\n";
            $prompt .= "1. Reference sources when supporting predictions\n";
            $prompt .= "2. Use 'Source 1...', 'Source 2...' format\n";
            $prompt .= "3. Include 'Source Analysis' section\n";
            $prompt .= "4. Cite specific facts/numbers from sources\n\n";
        }
        
        $prompt .= "Provide analysis in this JSON structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"[Topic + Time Period + Focus]\",\n";
        $prompt .= "  \"executive_summary\": \"[3-4 sentence summary of key predictions, risks, implications]\",\n";
        $prompt .= "  \"prediction_horizon\": \"[Time period]\",\n";
        $prompt .= "  \"current_situation\": \"[Current state and trends analysis]\",\n";
        $prompt .= "  \"key_factors\": [\n";
        $prompt .= "    \"[Factor 1: Specific, actionable factor]\",\n";
        $prompt .= "    \"[Factor 2: Specific, actionable factor]\",\n";
        $prompt .= "    \"[Factor 3: Specific, actionable factor]\",\n";
        $prompt .= "    \"[Factor 4: Specific, actionable factor]\",\n";
        $prompt .= "    \"[Factor 5: Specific, actionable factor]\",\n";
        $prompt .= "    \"[Factor 6: Specific, actionable factor]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"predictions\": [\n";
        $prompt .= "    \"[Prediction 1: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 2: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 3: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 4: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 5: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 6: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 7: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 8: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 9: Specific outcome with timeline]\",\n";
        $prompt .= "    \"[Prediction 10: Specific outcome with timeline]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"risk_assessment\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"risk\": \"[Risk description]\",\n";
        $prompt .= "      \"level\": \"[Critical/High/Medium/Low]\",\n";
        $prompt .= "      \"probability\": \"[Very Likely/Likely/Possible/Unlikely]\",\n";
        $prompt .= "      \"mitigation\": \"[Mitigation strategy]\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"recommendations\": [\n";
        $prompt .= "    \"[Specific, actionable recommendation]\",\n";
        $prompt .= "    \"[Specific, actionable recommendation]\",\n";
        $prompt .= "    \"[Specific, actionable recommendation]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"strategic_implications\": [\n";
        $prompt .= "    \"[Strategic implication]\",\n";
        $prompt .= "    \"[Strategic implication]\",\n";
        $prompt .= "    \"[Strategic implication]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"confidence_level\": \"[High (90-95%)/Medium (75-89%)/Low (60-74%)]\",\n";
        $prompt .= "  \"methodology\": \"[AI analysis approach, data sources, and validation methods]\",\n";
        $prompt .= "  \"data_sources\": [\n";
        $prompt .= "    \"[Data source with relevance]\",\n";
        $prompt .= "    \"[Data source with relevance]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"assumptions\": [\n";
        $prompt .= "    \"[Key assumption underlying predictions]\",\n";
        $prompt .= "    \"[Key assumption underlying predictions]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"note\": \"[Important note about analysis limitations or key considerations]\",\n";
        $prompt .= "  \"analysis_date\": \"[Current date in YYYY-MM-DD format]\",\n";
        $prompt .= "  \"next_review\": \"[Recommended next review date]\",\n";
        $prompt .= "  \"critical_timeline\": \"[Critical dates or milestones to watch]\",\n";
        $prompt .= "  \"success_metrics\": [\n";
        $prompt .= "    \"[How to measure success of predictions]\",\n";
        $prompt .= "    \"[How to measure success of predictions]\"\n";
        $prompt .= "  ]";
        
        if ($sourceUrls && count($sourceUrls) > 0) {
            $prompt .= ",\n";
            $prompt .= "  \"source_analysis\": \"[Detailed explanation of how each provided source influenced your analysis and predictions. Use specific examples and show direct connections between source information and conclusions.]\"";
        }
        
        $prompt .= "\n}\n\n";
        $prompt .= "DETAILED ANALYSIS INSTRUCTIONS:\n";
        $prompt .= "1. Be exceptionally specific and actionable with concrete examples\n";
        $prompt .= "2. Include detailed timelines with specific dates and milestones\n";
        $prompt .= "3. Focus on future outcomes with comprehensive scenario analysis\n";
        $prompt .= "4. Provide realistic predictions with supporting rationale and evidence\n";
        $prompt .= "5. Structure risks by probability, impact, and provide detailed mitigation strategies\n";
        $prompt .= "6. Make recommendations highly implementable with step-by-step guidance\n";
        $prompt .= "7. Include quantifiable metrics, KPIs, and measurable success indicators\n";
        $prompt .= "8. Consider opportunities, threats, and competitive landscape implications\n";
        $prompt .= "9. Base analysis on logical reasoning with clear cause-and-effect relationships\n";
        $prompt .= "10. Ensure comprehensive, professional analysis with deep insights and nuanced understanding\n";
        $prompt .= "11. Provide detailed context, background information, and industry expertise\n";
        $prompt .= "12. Include multiple perspectives and alternative scenarios\n";
        $prompt .= "13. Add specific examples, case studies, and real-world applications\n";
        $prompt .= "14. Consider long-term implications and second-order effects\n";
        $prompt .= "15. Provide detailed methodology and analytical framework explanations\n";
        $prompt .= "16. Use advanced analytical frameworks (SWOT, PESTEL, Porter's Five Forces, etc.)\n";
        $prompt .= "17. Include detailed financial implications and cost-benefit analysis\n";
        $prompt .= "18. Provide comprehensive stakeholder impact analysis\n";
        $prompt .= "19. Consider regulatory, technological, and market disruption factors\n";
        $prompt .= "20. Include detailed competitive analysis and market positioning\n\n";
        
        $prompt .= "ANALYSIS DEPTH REQUIREMENTS:\n";
        $prompt .= "- Each prediction must include: specific timeline, probability assessment, supporting evidence, and potential variations\n";
        $prompt .= "- Each risk must include: detailed impact analysis, probability assessment, early warning indicators, and comprehensive mitigation strategies\n";
        $prompt .= "- Each recommendation must include: implementation steps, resource requirements, timeline, success metrics, and potential challenges\n";
        $prompt .= "- Provide detailed reasoning for all conclusions with clear logical flow\n";
        $prompt .= "- Include quantitative data, statistics, and measurable indicators wherever possible\n";
        $prompt .= "- Consider multiple scenarios: optimistic, realistic, and pessimistic outcomes\n";
        $prompt .= "- Provide detailed context about industry trends, market dynamics, and external factors\n";
        $prompt .= "- Include specific examples, case studies, and comparable situations\n";
        $prompt .= "- Address potential objections and alternative viewpoints\n";
        $prompt .= "- Provide detailed methodology explanation for analytical approach\n\n";
        
        if ($sourceUrls && count($sourceUrls) > 0) {
            $prompt .= "11. Cite sources using 'Source 1...', 'Source 2...'\n";
            $prompt .= "12. Show connections between sources and predictions\n";
            $prompt .= "13. Include source_analysis field\n";
            if ($scrapedContent) {
                $prompt .= "14. Use actual data and quotes from sources\n";
                $prompt .= "15. Reference specific facts and numbers\n";
            }
        }
        
        $prompt .= "\nGenerate high-quality, professional prediction analysis suitable for executive decision-making.";
        
        return $prompt;
    }

    protected function parseJsonResponse($text)
    {
        try {
            // First, try to extract JSON from markdown blocks
            if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
                $jsonString = trim($matches[1]);
            } else {
                // Try to find JSON-like content
                if (preg_match('/\{.*\}/s', $text, $matches)) {
                    $jsonString = $matches[0];
                } else {
                    Log::warning("No JSON structure found in response");
                    return ['raw_response' => $text];
                }
            }
            
            $decoded = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            } else {
                Log::error("JSON decode error: " . json_last_error_msg());
                Log::error("JSON string: " . substr($jsonString, 0, 500) . "...");
            }
        } catch (\Exception $e) {
            Log::error("Exception in parseJsonResponse: " . $e->getMessage());
        }
        
        // If no valid JSON found, return the text as is
        return ['raw_response' => $text];
    }

    protected function getFallbackResponse($analysisType)
    {
        return [
            'title' => 'Analysis Failed - Fallback Response',
            'executive_summary' => 'Due to technical difficulties, a comprehensive analysis could not be generated. Please try again or contact support.',
            'prediction_horizon' => 'Unable to determine',
            'current_situation' => 'Analysis failed to complete',
            'key_factors' => ['Technical error prevented analysis'],
            'predictions' => ['Unable to generate predictions at this time'],
            'risk_assessment' => [
                [
                    'risk' => 'Analysis system failure',
                    'level' => 'Critical',
                    'probability' => 'Very Likely',
                    'impact' => 'Severe',
                    'timeline' => 'Immediate',
                    'mitigation' => 'Retry analysis or contact technical support'
                ]
            ],
            'recommendations' => ['Retry the analysis', 'Check system status', 'Contact support if problem persists'],
            'strategic_implications' => ['Analysis system requires attention'],
            'confidence_level' => 'Unable to determine',
            'methodology' => 'Fallback response due to system error',
            'data_sources' => ['System error prevented data analysis'],
            'assumptions' => ['System is experiencing technical difficulties'],
            'note' => 'This is a fallback response due to technical difficulties. Please retry your analysis.',
            'analysis_date' => now()->format('Y-m-d'),
            'next_review' => 'Immediate retry recommended',
            'critical_timeline' => 'Immediate attention required',
            'success_metrics' => ['System restoration', 'Successful analysis completion'],
            'status' => 'error',
            'api_metadata' => [
                'api_response_time' => 0.0,
                'api_response_time_unit' => 'seconds',
                'api_timestamp' => now()->toISOString(),
                'model_version' => $this->model,
                'note' => 'Fallback response - timing may not be accurate',
                'confidence_score' => 0.50 // Very low confidence for error fallback responses
            ]
        ];
    }

    public function testConnection()
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'ChatGPT API key not configured',
                    'status_code' => 0
                ];
            }

            // Test with a simple prompt
            $testPrompt = "Hello, this is a test message. Please respond with 'Test successful'.";
            $result = $this->analyzeText($testPrompt, 'test');
            
            if (isset($result['status']) && $result['status'] === 'error') {
                return [
                    'success' => false,
                    'message' => 'ChatGPT API test failed: ' . ($result['note'] ?? 'Unknown error'),
                    'status_code' => 0
                ];
            }
            
            return [
                'success' => true,
                'message' => 'ChatGPT API connection successful',
                'status_code' => 200
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'ChatGPT API connection error: ' . $e->getMessage(),
                'status_code' => 0
            ];
        }
    }

    public function getAvailableModels()
    {
        return [
            'prediction-analysis' => [
                'name' => 'Advanced Prediction Analysis',
                'model' => $this->model,
                'description' => 'Professional AI-powered prediction analysis system (NUJUM) using advanced AI models for comprehensive future forecasting and strategic insights across any topic or domain'
            ]
        ];
    }

    protected function extractConfidenceFromAIResponse($parsedResult)
    {
        // Look for a confidence score in the parsed result
        if (isset($parsedResult['confidence_level'])) {
            return $parsedResult['confidence_level'];
        }

        // If not found, try to extract from the raw response if available
        if (isset($parsedResult['raw_response'])) {
            $rawResponse = $parsedResult['raw_response'];
            if (preg_match('/Confidence Score: (\d+\.\d+)/', $rawResponse, $matches)) {
                return $matches[1];
            }
        }

        return null; // No confidence score found
    }

    /**
     * Update analytics with API response data
     */
    protected function updateAnalyticsWithApiResponse($analytics, $response, $apiResponseTime)
    {
        try {
            $responseData = $response->json();
            $outputTokens = 0;
            
            // Get output tokens from response
            if (isset($responseData['usage']['completion_tokens'])) {
                $outputTokens = $responseData['usage']['completion_tokens'];
            }
            
            // Update analytics with API response data
            $analytics->update([
                'output_tokens' => $outputTokens,
                'api_response_time' => $apiResponseTime,
                'http_status_code' => $response->status(),
                'retry_attempts' => $analytics->retry_attempts ?? 0,
            ]);
            
            // Calculate total tokens and cost
            $analytics->calculateTotalTokens();
            $analytics->calculateEstimatedCost();
            $analytics->save();
            
            Log::info('Analytics updated with API response data', [
                'analytics_id' => $analytics->id,
                'output_tokens' => $outputTokens,
                'total_tokens' => $analytics->total_tokens,
                'estimated_cost' => $analytics->estimated_cost
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update analytics with API response', [
                'analytics_id' => $analytics->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
}
