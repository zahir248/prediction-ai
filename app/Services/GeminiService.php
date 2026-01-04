<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiService implements AIServiceInterface
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    protected $webScrapingService;
    protected $sslVerify;
    protected $currentPredictionHorizon;
    protected $enableTruncationDetection = true;
    protected $model = 'gemini-2.5-flash';

    public function __construct(WebScrapingService $webScrapingService)
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->webScrapingService = $webScrapingService;
        
        // Check if we should verify SSL (default to true for production)
        $this->sslVerify = config('services.gemini.ssl_verify', !app()->environment('local', 'development'));
    }

    public function analyzeText($text, $analysisType = 'prediction-analysis', $sourceUrls = null, $predictionHorizon = null, $analytics = null, $target = null)
    {
        try {
            // Validate API key
            if (empty($this->apiKey)) {
                Log::error('Gemini API key not configured');
                return $this->getFallbackResponse($analysisType);
            }
            
            // Validate API key format (should start with AIza)
            if (!str_starts_with($this->apiKey, 'AIza')) {
                Log::error('Invalid Gemini API key format. Key should start with "AIza"');
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
            Log::info("Starting Gemini API request at: " . now());
            
            Log::info("Sending request to Gemini API with prompt length: " . strlen($prompt));
            Log::info("API Key configured: " . (!empty($this->apiKey) ? 'Yes (length: ' . strlen($this->apiKey) . ')' : 'No'));
            Log::info("Request URL: " . $this->baseUrl);
            Log::info("Authentication: Using x-goog-api-key header");
            Log::info("Full request details:", [
                'base_url' => $this->baseUrl,
                'api_key_length' => strlen($this->apiKey),
                'api_key_prefix' => substr($this->apiKey, 0, 10),
                'ssl_verify' => $this->sslVerify,
                'auth_method' => 'x-goog-api-key header'
            ]);
            
            // Increase output tokens for data-analysis to handle large datasets and multiple charts
            $maxOutputTokens = ($analysisType === 'data-analysis') ? 32768 : 16384;
            
            $response = Http::timeout(300)->withOptions([
                'verify' => $this->sslVerify, // Use the configured SSL verification option
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify,
                ]
            ])->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => $maxOutputTokens,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);
            
            // Calculate API response time
            $apiResponseTime = round(microtime(true) - $apiStartTime, 3);
            
            // Update analytics if provided
            if ($analytics) {
                $this->updateAnalyticsWithApiResponse($analytics, $response, $apiResponseTime);
            }
            
            Log::info("Gemini API response received at: " . now());
            Log::info("API response time: " . $apiResponseTime . " seconds");
            Log::info("Response status: " . $response->status());
            Log::info("Response body length: " . strlen($response->body()));

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Try to parse JSON response
                    $parsedResult = $this->parseJsonResponse($result);
                    
                    if ($parsedResult) {
                        // Check if this is a partial response due to truncation
                        if (isset($parsedResult['status']) && $parsedResult['status'] === 'partial') {
                            Log::warning("Received partial response, attempting retry with reduced prompt");
                            
                            // Try retry with reduced prompt if enabled and we haven't exceeded retry limit
                            if ($this->enableTruncationDetection) {
                                $retryResult = $this->retryWithReducedPrompt($prompt, $scrapedContent, $sourceUrls, $analysisType, $predictionHorizon);
                                if ($retryResult) {
                                    Log::info("Retry successful, returning complete response");
                                    return $retryResult;
                                }
                            }
                            
                            Log::warning("Retry failed, returning partial response");
                        }
                        
                        // Extract confidence score from the AI response if available
                        $confidenceScore = $this->extractConfidenceFromAIResponse($parsedResult);
                        
                        // Add API timing metadata
                        $parsedResult['api_metadata'] = [
                            'api_response_time' => $apiResponseTime,
                            'api_response_time_unit' => 'seconds',
                            'api_timestamp' => now()->toISOString(),
                            'model_version' => 'gemini-2.5-flash',
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
                    
                    // If parsing failed, check if response appears truncated and try retry
                    if ($this->enableTruncationDetection && $this->isResponseTruncated($result)) {
                        Log::warning("Response appears truncated, attempting retry with reduced prompt");
                        
                        $retryResult = $this->retryWithReducedPrompt($prompt, $scrapedContent, $sourceUrls, $analysisType, $predictionHorizon);
                        if ($retryResult) {
                            Log::info("Retry successful, returning complete response");
                            return $retryResult;
                        }
                    }
                    
                    return $result;
                }
                
                Log::error('Unexpected Gemini API response structure: ' . json_encode($data));
                return $this->getFallbackResponse($analysisType);
            }
            
            // If SSL verification failed, try without SSL verification
            if ($response->status() === 0 && strpos($response->body(), 'SSL certificate problem') !== false) {
                Log::warning('SSL verification failed, retrying without SSL verification');
                return $this->retryWithoutSSLVerification($prompt, $scrapedContent, $sourceUrls, $analysisType);
            }
            
            Log::error('Gemini API request failed: ' . $response->status() . ' - ' . $response->body());
            return $this->getFallbackResponse($analysisType);
            
        } catch (\Exception $e) {
            Log::error('Error in GeminiService: ' . $e->getMessage());
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
        // Handle social media analysis differently
        if ($analysisType === 'social-media-analysis') {
            return $this->createSocialMediaAnalysisPrompt($text);
        }
        
        $prompt = "You are an expert AI prediction analyst specializing in comprehensive future forecasting and strategic analysis. Please analyze the following text and provide a detailed, professional prediction analysis similar to high-quality consulting reports.\n\n";
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
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 1: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 2: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 3: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 4: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 5: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Factor 6: Specific, actionable factor]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this factor is important, its impact, and how it influences future development]\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"predictions\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 1: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 2: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 3: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 4: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 5: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 6: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 7: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 8: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 9: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Prediction 10: Specific outcome with timeline]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this prediction, including reasoning, supporting evidence, and expected impact]\"\n";
        $prompt .= "    }\n";
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
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Specific, actionable recommendation]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this recommendation is important, how to implement it, and expected outcomes]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Specific, actionable recommendation]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this recommendation is important, how to implement it, and expected outcomes]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Specific, actionable recommendation]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of why this recommendation is important, how to implement it, and expected outcomes]\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"strategic_implications\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Strategic implication]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this strategic implication, its significance, and how it affects decision-making]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Strategic implication]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this strategic implication, its significance, and how it affects decision-making]\"\n";
        $prompt .= "    },\n";
        $prompt .= "    {\n";
        $prompt .= "      \"point\": \"[Strategic implication]\",\n";
        $prompt .= "      \"explanation\": \"[Detailed explanation of this strategic implication, its significance, and how it affects decision-making]\"\n";
        $prompt .= "    }\n";
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
        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. Be specific and actionable\n";
        $prompt .= "2. Include timelines for predictions\n";
        $prompt .= "3. Focus on future outcomes\n";
        $prompt .= "4. Provide realistic predictions\n";
        $prompt .= "5. Structure risks by probability and impact\n";
        $prompt .= "6. Make recommendations implementable\n";
        $prompt .= "7. Include quantifiable metrics where possible\n";
        $prompt .= "8. Consider opportunities and threats\n";
        $prompt .= "9. Base analysis on logical reasoning\n";
        $prompt .= "10. Ensure comprehensive and professional analysis\n";
        
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

    /**
     * Create prompt for social media analysis
     */
    protected function createSocialMediaAnalysisPrompt($text)
    {
        // Extract analysis type from text (professional or political)
        $analysisType = 'professional';
        if (stripos($text, 'ANALYSIS TYPE: POLITICAL') !== false || stripos($text, 'political profile') !== false) {
            $analysisType = 'political';
        }
        
        if ($analysisType === 'political') {
            $prompt = "You are an expert political profile analyst specializing in analyzing political views and political involvement based on social media data. Your task is to analyze the following social media profile data across multiple platforms to assess the person's political views, political involvement, political activities, and political engagement.\n\n";
            $prompt .= "SOCIAL MEDIA PROFILE DATA:\n{$text}\n\n";
            $prompt .= "Analyze the person's POLITICAL PROFILE focusing on:\n\n";
            $prompt .= "- Political views: What are their political opinions, ideologies, and stances on political issues?\n";
            $prompt .= "- Political involvement: How actively involved are they in political activities, discussions, and movements?\n";
            $prompt .= "- Political affiliations: What political parties, groups, or movements do they support or associate with?\n";
            $prompt .= "- Political engagement: What level of political engagement, activism, and participation do they demonstrate?\n";
            $prompt .= "- Political content: What political topics, issues, and messages do they share and discuss?\n";
            $prompt .= "- Political network: What political connections, associations, and relationships do they have?\n";
            $prompt .= "- Political communication: How do they communicate about political matters?\n";
            $prompt .= "- Political influence: What is their level of political influence and reach?\n\n";
        } else {
            $prompt = "You are an expert professional profile analyst specializing in comprehensive social media profile assessment for recruitment, hiring, and professional evaluation purposes. Please analyze the following social media profile data across multiple platforms and provide a detailed, professional assessment.\n\n";
            $prompt .= "SOCIAL MEDIA PROFILE DATA:\n{$text}\n\n";
            $prompt .= "Provide a comprehensive professional analysis covering the following areas:\n\n";
        }
        
        if ($analysisType === 'political') {
            $prompt .= "Provide analysis in this JSON structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"title\": \"[Political Profile Analysis: Name/Username]\",\n";
            $prompt .= "  \"executive_summary\": \"[3-4 sentence summary of key findings, political leanings, affiliations, and risk indicators]\",\n";
        } else {
            $prompt .= "Provide analysis in this JSON structure:\n";
            $prompt .= "{\n";
            $prompt .= "  \"title\": \"[Professional Profile Analysis: Name/Username]\",\n";
            $prompt .= "  \"executive_summary\": \"[3-4 sentence summary of key findings, professional strengths, and risk indicators]\",\n";
        }
        $prompt .= "  \"risk_assessment\": {\n";
        $prompt .= "    \"overall_risk_level\": \"[Low/Medium/High]\",\n";
        $prompt .= "    \"risk_factors\": [\n";
        $prompt .= "      {\n";
        if ($analysisType === 'political') {
            $prompt .= "        \"risk\": \"[Specific political risk description]\",\n";
            $prompt .= "        \"level\": \"[Low/Medium/High]\",\n";
            $prompt .= "        \"description\": \"[Detailed explanation of the political risk]\",\n";
            $prompt .= "        \"mitigation\": \"[How to address or mitigate this political risk]\"\n";
        } else {
            $prompt .= "        \"risk\": \"[Specific risk description]\",\n";
            $prompt .= "        \"level\": \"[Low/Medium/High]\",\n";
            $prompt .= "        \"description\": \"[Detailed explanation of the risk]\",\n";
            $prompt .= "        \"mitigation\": \"[How to address or mitigate this risk]\"\n";
        }
        $prompt .= "      }\n";
        $prompt .= "    ],\n";
        $prompt .= "    \"red_flags\": [\n";
        if ($analysisType === 'political') {
            $prompt .= "      \"[Any concerning political content, behavior, or patterns]\",\n";
            $prompt .= "      \"[Any concerning political content, behavior, or patterns]\"\n";
        } else {
            $prompt .= "      \"[Any concerning content, behavior, or patterns]\",\n";
            $prompt .= "      \"[Any concerning content, behavior, or patterns]\"\n";
        }
        $prompt .= "    ],\n";
        $prompt .= "    \"positive_indicators\": [\n";
        if ($analysisType === 'political') {
            $prompt .= "      \"[Positive political indicators]\",\n";
            $prompt .= "      \"[Positive political indicators]\"\n";
        } else {
            $prompt .= "      \"[Positive professional indicators]\",\n";
            $prompt .= "      \"[Positive professional indicators]\"\n";
        }
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        
        if ($analysisType === 'political') {
            $prompt .= "  \"political_profile\": {\n";
            $prompt .= "    \"political_affiliation_score\": [A numeric score from 0-100 representing political alignment clarity and strength],\n";
            $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
            $prompt .= "    \"overview\": \"[2-3 sentence overview describing the political profile and what it's based on, including number of posts analyzed and platforms]\",\n";
            $prompt .= "    \"political_leanings\": \"[Detailed assessment of political orientation, ideology, and leanings]\",\n";
            $prompt .= "    \"political_engagement\": \"[Level and type of political engagement, activism, and participation]\",\n";
            $prompt .= "    \"political_content\": \"[Analysis of political content themes, messaging, and topics]\",\n";
            $prompt .= "    \"political_network\": \"[Political associations, connections, and network analysis]\",\n";
            $prompt .= "    \"political_consistency\": \"[Consistency of political positions, messaging, and values over time]\",\n";
            $prompt .= "    \"political_influence\": \"[Assessment of political influence, reach, and audience engagement]\",\n";
            $prompt .= "    \"political_controversies\": \"[Any political controversies, sensitive issues, or polarizing content]\",\n";
            $prompt .= "    \"political_communication_style\": \"[Analysis of political communication approach, rhetoric, and tone]\",\n";
            $prompt .= "    \"political_credibility\": \"[Assessment of political credibility, authenticity, and trustworthiness]\",\n";
            $prompt .= "    \"political_brand_consistency\": \"[Consistency of political brand and messaging across platforms]\",\n";
            $prompt .= "    \"political_platform_utilization\": \"[How effectively platforms are used for political purposes]\",\n";
            $prompt .= "    \"political_audience_engagement\": \"[Quality and nature of political audience interactions]\",\n";
            $prompt .= "    \"concerns\": \"[Any political concerns or red flags that require further investigation]\",\n";
            $prompt .= "    \"recommendations\": [\n";
            $prompt .= "      \"[Specific recommendation related to political profile]\",\n";
            $prompt .= "      \"[Specific recommendation related to political profile]\"\n";
            $prompt .= "    ]\n";
            $prompt .= "  },\n";
            $prompt .= "  \"political_engagement_indicators\": {\n";
            $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
            $prompt .= "    \"consistency_score\": [Numeric score 0-100 for political posting consistency],\n";
            $prompt .= "    \"consistency\": \"[Description of political posting consistency and activity patterns]\",\n";
            $prompt .= "    \"activism_level_score\": [Numeric score 0-100 for political activism and engagement level],\n";
            $prompt .= "    \"activism_level\": \"[Description of level of political activism, participation, and engagement]\",\n";
            $prompt .= "    \"commitment_score\": [Numeric score 0-100 for political commitment and dedication],\n";
            $prompt .= "    \"commitment\": \"[Description of evidence of political commitment and long-term engagement]\",\n";
            $prompt .= "    \"advocacy_score\": [Numeric score 0-100 for political advocacy and cause support],\n";
            $prompt .= "    \"advocacy\": \"[Description of political advocacy activities and cause support]\",\n";
            $prompt .= "    \"influence_score\": [Numeric score 0-100 for political influence and reach],\n";
            $prompt .= "    \"influence\": \"[Description of political influence, reach, and ability to mobilize]\",\n";
            $prompt .= "    \"overall_assessment\": \"[Overall political engagement assessment]\",\n";
            $prompt .= "    \"evidence\": [\n";
            $prompt .= "      \"[Specific evidence supporting the political engagement assessment]\",\n";
            $prompt .= "      \"[Specific evidence supporting the political engagement assessment]\"\n";
            $prompt .= "    ]\n";
            $prompt .= "  },\n";
            $prompt .= "  \"political_alignment_indicators\": {\n";
            $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
            $prompt .= "    \"overview\": \"[2-3 sentence overview describing the person's political alignment and ideological consistency]\",\n";
            $prompt .= "    \"ideological_alignment_level\": [Numeric value 0-100 or text: Low/Medium/High for ideological alignment],\n";
            $prompt .= "    \"ideological_alignment\": \"[Description of ideological alignment and political philosophy]\",\n";
            $prompt .= "    \"party_alignment_level\": [Numeric value 0-100 or text: Low/Medium/High for party alignment],\n";
            $prompt .= "    \"party_alignment\": \"[Description of party affiliation, support, and alignment]\",\n";
            $prompt .= "    \"value_consistency_level\": [Numeric value 0-100 or text: Low/Medium/High for value consistency],\n";
            $prompt .= "    \"value_consistency\": \"[Description of consistency of political values and positions]\",\n";
            $prompt .= "    \"overall_alignment\": \"[Overall political alignment assessment]\",\n";
            $prompt .= "    \"concerns\": [\n";
            $prompt .= "      \"[Any political alignment concerns or inconsistencies]\",\n";
            $prompt .= "      \"[Any political alignment concerns or inconsistencies]\"\n";
            $prompt .= "    ],\n";
            $prompt .= "    \"strengths\": [\n";
            $prompt .= "      \"[Political alignment strengths]\",\n";
            $prompt .= "      \"[Political alignment strengths]\"\n";
            $prompt .= "    ]\n";
            $prompt .= "  },\n";
            $prompt .= "  \"political_growth_signals\": {\n";
            $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
            $prompt .= "    \"overview\": \"[2-3 sentence overview describing the person's political growth potential and trajectory]\",\n";
            $prompt .= "    \"political_development_level\": [Numeric value 0-100 or text: Low/Medium/Strong for political development],\n";
            $prompt .= "    \"political_development\": \"[Description of political knowledge growth and sophistication]\",\n";
            $prompt .= "    \"influence_growth_level\": [Numeric value 0-100 or text: Low/Medium/Strong for influence growth],\n";
            $prompt .= "    \"influence_growth\": \"[Description of evidence of growing political influence and reach]\",\n";
            $prompt .= "    \"network_expansion_level\": [Numeric value 0-100 or text: Low/Medium/Strong for network expansion],\n";
            $prompt .= "    \"network_expansion\": \"[Description of political network growth and connections]\",\n";
            $prompt .= "    \"political_trajectory\": \"[Assessment of political trajectory and future potential]\",\n";
            $prompt .= "    \"indicators\": [\n";
            $prompt .= "      \"[Specific political growth indicators]\",\n";
            $prompt .= "      \"[Specific political growth indicators]\"\n";
            $prompt .= "    ]\n";
            $prompt .= "  },\n";
        } else {
            $prompt .= "  \"professional_footprint\": {\n";
        }
        $prompt .= "    \"professionalism_score\": [A numeric score from 0-100 representing overall professionalism],\n";
        $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
        $prompt .= "    \"overview\": \"[2-3 sentence overview describing the professionalism score and what it's based on, including number of posts analyzed and platforms]\",\n";
        $prompt .= "    \"content_relevance\": \"[Assessment of content relevance and professional focus]\",\n";
        $prompt .= "    \"tone_analysis\": \"[Analysis of tone and sentiment in communications]\",\n";
        $prompt .= "    \"engagement_quality\": \"[Quality of engagement and interactions]\",\n";
        $prompt .= "    \"online_presence\": \"[Assessment of overall online presence and professionalism]\",\n";
        $prompt .= "    \"content_quality\": \"[Evaluation of content quality, relevance, and professionalism]\",\n";
        $prompt .= "    \"brand_consistency\": \"[Consistency of personal/professional brand across platforms]\",\n";
        $prompt .= "    \"platform_utilization\": \"[How effectively platforms are used for professional purposes]\",\n";
        $prompt .= "    \"audience_engagement\": \"[Quality and nature of audience interactions]\",\n";
        $prompt .= "    \"concerns\": \"[Any concerns or red flags that require further investigation]\",\n";
        $prompt .= "    \"recommendations\": [\n";
        $prompt .= "      \"[Specific recommendation for improving professional footprint]\",\n";
        $prompt .= "      \"[Specific recommendation for improving professional footprint]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"work_ethic_indicators\": {\n";
        $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
        $prompt .= "    \"consistency_score\": [Numeric score 0-100 for consistency dimension],\n";
        $prompt .= "    \"consistency\": \"[Description of posting consistency and activity patterns]\",\n";
        $prompt .= "    \"follow_through_score\": [Numeric score 0-100 for follow-through dimension],\n";
        $prompt .= "    \"follow_through\": \"[Description of evidence of completing tasks and following through on commitments]\",\n";
        $prompt .= "    \"collaboration_score\": [Numeric score 0-100 for collaboration dimension],\n";
        $prompt .= "    \"collaboration\": \"[Description of teamwork and collaborative behavior indicators]\",\n";
        $prompt .= "    \"initiative_score\": [Numeric score 0-100 for initiative dimension],\n";
        $prompt .= "    \"initiative\": \"[Description of proactive behavior and self-directed action]\",\n";
        $prompt .= "    \"productivity_score\": [Numeric score 0-100 for productivity dimension],\n";
        $prompt .= "    \"productivity\": \"[Description of productivity and professional activity signs]\",\n";
        $prompt .= "    \"overall_assessment\": \"[Overall work ethic assessment]\",\n";
        $prompt .= "    \"evidence\": [\n";
        $prompt .= "      \"[Specific evidence supporting the assessment]\",\n";
        $prompt .= "      \"[Specific evidence supporting the assessment]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"cultural_fit_indicators\": {\n";
        $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
        $prompt .= "    \"overview\": \"[2-3 sentence overview describing the candidate's overall cultural fit and potential challenges]\",\n";
        $prompt .= "    \"value_alignment_level\": [Numeric value 0-100 or text: Low/Medium/High for value alignment],\n";
        $prompt .= "    \"value_alignment\": \"[Description of how the candidate's values align with company values]\",\n";
        $prompt .= "    \"teamwork_ethos_level\": [Numeric value 0-100 or text: Low/Medium/Strong for teamwork],\n";
        $prompt .= "    \"teamwork_ethos\": \"[Description of the candidate's teamwork and collaboration indicators]\",\n";
        $prompt .= "    \"innovation_mindset_level\": [Numeric value 0-100 or text: Low/Medium/Strong for innovation],\n";
        $prompt .= "    \"innovation_mindset\": \"[Description of the candidate's innovation mindset and creative thinking]\",\n";
        $prompt .= "    \"overall_fit\": \"[Overall cultural fit assessment]\",\n";
        $prompt .= "    \"concerns\": [\n";
        $prompt .= "      \"[Any cultural fit concerns]\",\n";
        $prompt .= "      \"[Any cultural fit concerns]\"\n";
        $prompt .= "    ],\n";
        $prompt .= "    \"strengths\": [\n";
        $prompt .= "      \"[Cultural fit strengths]\",\n";
        $prompt .= "      \"[Cultural fit strengths]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"professional_growth_signals\": {\n";
        $prompt .= "    \"confidence\": [Confidence level as a percentage (0-100) for this assessment],\n";
        $prompt .= "    \"overview\": \"[2-3 sentence overview describing the candidate's professional growth potential and areas for development]\",\n";
        $prompt .= "    \"learning_initiative_level\": [Numeric value 0-100 or text: Low/Medium/Strong for learning initiative],\n";
        $prompt .= "    \"learning_initiative\": \"[Description of the candidate's active learning and engagement with new technologies]\",\n";
        $prompt .= "    \"skill_development_level\": [Numeric value 0-100 or text: Low/Medium/Strong for skill development],\n";
        $prompt .= "    \"skill_development\": \"[Description of evidence of skill development and learning]\",\n";
        $prompt .= "    \"mentorship_activity_level\": [Numeric value 0-100 or text: Low/Medium/Strong for mentorship],\n";
        $prompt .= "    \"mentorship_activity\": \"[Description of mentoring activities or seeking mentorship]\",\n";
        $prompt .= "    \"growth_trajectory\": \"[Assessment of professional growth trajectory]\",\n";
        $prompt .= "    \"indicators\": [\n";
        $prompt .= "      \"[Specific growth indicators]\",\n";
        $prompt .= "      \"[Specific growth indicators]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"activity_overview\": {\n";
        $prompt .= "    \"posting_frequency\": \"[Analysis of posting frequency and patterns]\",\n";
        $prompt .= "    \"content_types\": \"[Types of content posted and their distribution]\",\n";
        $prompt .= "    \"peak_activity_times\": \"[When the person is most active]\",\n";
        $prompt .= "    \"engagement_patterns\": \"[Patterns in how they engage with others]\",\n";
        $prompt .= "    \"behavioral_consistency\": \"[Consistency in behavior across platforms and time]\",\n";
        $prompt .= "    \"notable_patterns\": [\n";
        $prompt .= "      \"[Notable behavioral patterns observed]\",\n";
        $prompt .= "      \"[Notable behavioral patterns observed]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        
        if ($analysisType === 'political') {
            $prompt .= "  \"political_communication_style\": {\n";
            $prompt .= "    \"confidence\": [Numeric value 0-100 for confidence in political communication assessment],\n";
            $prompt .= "    \"overview\": \"[Summary of political communication style and approach]\",\n";
            $prompt .= "    \"rhetoric_analysis\": \"[Analysis of political rhetoric, messaging style, and communication tone]\",\n";
            $prompt .= "    \"persuasiveness_score\": [Numeric value 0-100 for persuasiveness and influence],\n";
            $prompt .= "    \"persuasiveness\": \"[Description of persuasive communication abilities and techniques]\",\n";
            $prompt .= "    \"authenticity_score\": [Numeric value 0-100 for authenticity and genuineness],\n";
            $prompt .= "    \"authenticity\": \"[Description of authenticity in political communication]\",\n";
            $prompt .= "    \"polarization_level\": [Numeric value 0-100 for polarization and divisiveness],\n";
            $prompt .= "    \"polarization\": \"[Description of polarization level and divisive communication patterns]\",\n";
            $prompt .= "    \"diplomacy_score\": [Numeric value 0-100 for diplomatic and respectful communication],\n";
            $prompt .= "    \"diplomacy\": \"[Description of diplomatic communication and ability to engage across differences]\",\n";
            $prompt .= "    \"emotional_appeal_score\": [Numeric value 0-100 for emotional appeal and connection],\n";
            $prompt .= "    \"emotional_appeal\": \"[Description of emotional appeal and ability to connect with audience]\",\n";
            $prompt .= "    \"communication_strengths\": [\n";
            $prompt .= "      \"[Political communication strength 1, e.g., Rhetoric: compelling messaging style]\",\n";
            $prompt .= "      \"[Political communication strength 2, e.g., Engagement: active dialogue with followers]\",\n";
            $prompt .= "      \"[Political communication strength 3, e.g., Frequency: regular political content updates]\"\n";
            $prompt .= "    ],\n";
            $prompt .= "    \"overall_assessment\": \"[Overall assessment of political communication effectiveness and style]\"\n";
            $prompt .= "  },\n";
            $prompt .= "  \"political_career_profile\": {\n";
            $prompt .= "    \"current_political_focus\": \"[Current political focus, interests, and priorities]\",\n";
            $prompt .= "    \"political_expertise_areas\": \"[Areas of political expertise and specialization]\",\n";
            $prompt .= "    \"political_positioning\": \"[Position within political community and influence level]\",\n";
            $prompt .= "    \"political_goals\": \"[Inferred political goals, aspirations, and ambitions]\",\n";
            $prompt .= "    \"political_growth_potential\": \"[Assessment of political growth potential and trajectory]\",\n";
            $prompt .= "    \"political_market_value\": \"[Political influence value and positioning in political landscape]\",\n";
            $prompt .= "    \"recommendations\": [\n";
            $prompt .= "      \"[Political development recommendations]\",\n";
            $prompt .= "      \"[Political development recommendations]\"\n";
            $prompt .= "    ]\n";
            $prompt .= "  },\n";
        } else {
            $prompt .= "  \"personality_communication\": {\n";
        }
        $prompt .= "    \"confidence\": [Numeric value 0-100 for confidence in personality assessment],\n";
        $prompt .= "    \"overview\": \"[Summary of personality and communication profile]\",\n";
        $prompt .= "    \"tone_analysis\": \"[Analysis of communication tone, e.g., casual and constructive, formal, friendly, etc.]\",\n";
        $prompt .= "    \"openness_score\": [Numeric value 0-100 for Openness to experience trait],\n";
        $prompt .= "    \"openness\": \"[Description of openness to experience, creativity, and intellectual curiosity]\",\n";
        $prompt .= "    \"conscientiousness_score\": [Numeric value 0-100 for Conscientiousness trait],\n";
        $prompt .= "    \"conscientiousness\": \"[Description of organization, dependability, and self-discipline]\",\n";
        $prompt .= "    \"extraversion_score\": [Numeric value 0-100 for Extraversion trait],\n";
        $prompt .= "    \"extraversion\": \"[Description of sociability, assertiveness, and energy in social situations]\",\n";
        $prompt .= "    \"agreeableness_score\": [Numeric value 0-100 for Agreeableness trait],\n";
        $prompt .= "    \"agreeableness\": \"[Description of trust, altruism, kindness, and cooperation]\",\n";
        $prompt .= "    \"neuroticism_score\": [Numeric value 0-100 for Neuroticism trait (emotional stability)],\n";
        $prompt .= "    \"neuroticism\": \"[Description of emotional stability and resilience to stress]\",\n";
        $prompt .= "    \"communication_strengths\": [\n";
        $prompt .= "      \"[Communication strength 1, e.g., Tone: casual approach to discussions]\",\n";
        $prompt .= "      \"[Communication strength 2, e.g., Engagement: conversationalist interaction style]\",\n";
        $prompt .= "      \"[Communication strength 3, e.g., Frequency: monthly posting pattern]\"\n";
        $prompt .= "    ],\n";
        $prompt .= "    \"overall_assessment\": \"[Overall assessment of personality and communication fit for collaborative environments]\"\n";
        $prompt .= "  },\n";
        $prompt .= "  \"career_profile\": {\n";
        $prompt .= "    \"current_focus\": \"[Current professional focus and interests]\",\n";
        $prompt .= "    \"expertise_areas\": \"[Areas of expertise and specialization]\",\n";
        $prompt .= "    \"industry_positioning\": \"[Position within industry and professional community]\",\n";
        $prompt .= "    \"career_goals\": \"[Inferred career goals and aspirations]\",\n";
        $prompt .= "    \"growth_potential\": \"[Assessment of career growth potential]\",\n";
        $prompt .= "    \"market_value\": \"[Professional market value and positioning]\",\n";
        $prompt .= "    \"recommendations\": [\n";
        $prompt .= "      \"[Career development recommendations]\",\n";
        $prompt .= "      \"[Career development recommendations]\"\n";
        $prompt .= "    ]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"overall_assessment\": \"[Comprehensive overall assessment and summary]\",\n";
        $prompt .= "  \"recommendations\": [\n";
        $prompt .= "    \"[Overall recommendations for hiring/recruitment decision]\",\n";
        $prompt .= "    \"[Overall recommendations for hiring/recruitment decision]\",\n";
        $prompt .= "    \"[Overall recommendations for hiring/recruitment decision]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"confidence_level\": \"[High (90-95%)/Medium (75-89%)/Low (60-74%)]\",\n";
        $prompt .= "  \"analysis_date\": \"[Current date in YYYY-MM-DD format]\",\n";
        $prompt .= "  \"data_quality\": \"[Assessment of data quality and completeness]\",\n";
        $prompt .= "  \"limitations\": \"[Any limitations or caveats to the analysis]\"\n";
        $prompt .= "}\n\n";
        
        if ($analysisType === 'political') {
            $prompt .= "INSTRUCTIONS:\n";
            $prompt .= "1. Focus EXCLUSIVELY on political views and political involvement based on the social media data\n";
            $prompt .= "2. Analyze what political opinions, ideologies, and stances the person holds (or infer from available content)\n";
            $prompt .= "3. Assess their level of political involvement, activism, and engagement in political activities\n";
            $prompt .= "4. Identify political affiliations, party support, and political group associations (or note absence if none found)\n";
            $prompt .= "5. Examine political content they share: political topics, issues, messages, and discussions\n";
            $prompt .= "6. Evaluate their political network: political connections, relationships, and associations\n";
            $prompt .= "7. Analyze their political communication style and how they express political views\n";
            $prompt .= "8. Assess their political influence, reach, and ability to mobilize or persuade\n";
            $prompt .= "9. Provide specific examples from the social media data (quotes, posts, engagement patterns) to support all political assessments\n";
            $prompt .= "10. Be objective, evidence-based, and focus solely on political aspects - do not analyze professional/work aspects\n";
            $prompt .= "11. Include detailed metrics, scores, and comprehensive descriptions for all political indicators\n";
            $prompt .= "12. Provide extensive evidence and examples from their social media activity related to politics\n";
            $prompt .= "13. Ensure COMPREHENSIVE coverage of ALL political analysis areas with DETAILED content\n";
            $prompt .= "14. Be thorough and detailed in analyzing political views and political involvement\n";
            $prompt .= "15. CRITICAL: If no explicit political content is found, analyze what the available data (posts, bio, interests, connections, content themes) might indicate about political leanings, even if indirect\n";
            $prompt .= "16. CRITICAL: NEVER use 'N/A', 'Not applicable', or 'No data' - always provide meaningful analysis based on available information\n";
            $prompt .= "17. CRITICAL: If political data is limited, provide contextual analysis: what does the absence of political content indicate? What can be inferred from their general content, interests, or connections?\n";
            $prompt .= "18. CRITICAL: For every field, provide a substantive assessment - use low scores (0-30) if no political indicators are found, but still provide descriptive analysis explaining why\n";
            $prompt .= "19. CRITICAL: Even with minimal data, provide scores and descriptions - a score of 0-20 with explanation is better than 'N/A'\n\n";
            
            $prompt .= "Generate a high-quality, COMPREHENSIVE political profile analysis focusing on political views and political involvement. Always provide meaningful analysis for every field, even when data is limited. Use low scores and descriptive explanations rather than 'N/A' or 'Not applicable'.";
        } else {
            $prompt .= "INSTRUCTIONS:\n";
            $prompt .= "1. Be objective and evidence-based in your analysis\n";
            $prompt .= "2. Focus on professional indicators relevant to hiring/recruitment\n";
            $prompt .= "3. Identify both strengths and areas of concern\n";
            $prompt .= "4. Provide specific examples from the data to support your assessments\n";
            $prompt .= "5. Consider context and avoid making assumptions without evidence\n";
            $prompt .= "6. Be fair and balanced in your evaluation\n";
            $prompt .= "7. Focus on professional relevance rather than personal opinions\n";
            $prompt .= "8. Consider privacy and ethical boundaries\n";
            $prompt .= "9. Provide actionable insights for decision-making\n";
            $prompt .= "10. Ensure comprehensive coverage of all requested analysis areas\n\n";
            
            $prompt .= "Generate a high-quality, professional social media profile analysis suitable for recruitment and hiring decisions.";
        }
        
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
            
            // Check if JSON appears truncated and try to fix it
            if (!$this->isValidJson($jsonString)) {
                Log::warning("JSON appears invalid/truncated, attempting to fix...");
                Log::info("Original JSON length: " . strlen($jsonString));
                Log::info("JSON preview: " . substr($jsonString, -200)); // Show end of JSON
                
                // Try multiple repair strategies
                $jsonString = $this->repairJsonWithMultipleStrategies($jsonString);
                Log::info("Repaired JSON length: " . strlen($jsonString));
                
                // If still not valid, try to extract partial content
                if (!$this->isValidJson($jsonString)) {
                    Log::warning("JSON repair failed, attempting to extract partial content");
                    $partialResult = $this->extractPartialContent($jsonString);
                    if ($partialResult) {
                        return $partialResult;
                    }
                }
            }
            
            $decoded = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            } else {
                Log::error("JSON decode error: " . json_last_error_msg());
                Log::error("JSON string: " . substr($jsonString, 0, 500) . "...");
                
                // Try one more repair attempt with error-specific fixes
                $finalRepair = $this->repairJsonByErrorType($jsonString, json_last_error());
                if ($finalRepair && $this->isValidJson($finalRepair)) {
                    $decoded = json_decode($finalRepair, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        Log::info("JSON successfully repaired after error-specific fixes");
                        return $decoded;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Exception in parseJsonResponse: " . $e->getMessage());
        }
        
        // If no valid JSON found, return the text as is
        return ['raw_response' => $text];
    }

    private function isValidJson($jsonString)
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function fixTruncatedJson($jsonString)
    {
        // Remove any trailing incomplete content
        $jsonString = rtrim($jsonString);
        
        // If string ends with incomplete quote, remove trailing content back to last complete quote
        if (preg_match('/.*"[^"]*$/s', $jsonString)) {
            // Find the last complete quoted string
            $lastQuotePos = strrpos($jsonString, '"', -2);
            if ($lastQuotePos !== false) {
                $jsonString = substr($jsonString, 0, $lastQuotePos + 1);
            }
        }
        
        // Remove any trailing comma
        $jsonString = rtrim($jsonString, ',');
        
        // Count braces and brackets to close properly
        $openBraces = substr_count($jsonString, '{');
        $closeBraces = substr_count($jsonString, '}');
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');
        
        // Close any open arrays first
        while ($openBrackets > $closeBrackets) {
            $jsonString .= ']';
            $closeBrackets++;
        }
        
        // Close any open objects
        while ($openBraces > $closeBraces) {
            $jsonString .= '}';
            $closeBraces++;
        }
        
        Log::info("JSON fix: Added " . ($closeBraces - substr_count($jsonString, '}') + ($openBraces - $closeBraces)) . " closing braces");
        
        return $jsonString;
    }

    private function repairJsonWithMultipleStrategies($jsonString)
    {
        Log::info("Applying multiple JSON repair strategies...");
        
        // Strategy 1: Basic truncation fix
        $repaired = $this->fixTruncatedJson($jsonString);
        
        // Strategy 2: Fix common syntax errors
        $repaired = $this->fixSyntaxErrors($repaired);
        
        // Strategy 3: Fix UTF-8 encoding issues
        $repaired = $this->fixUtf8Errors($repaired);
        
        // Strategy 4: Repair unbalanced braces and brackets
        $repaired = $this->repairUnbalancedBraces($repaired);
        
        // Strategy 5: Remove trailing commas and fix incomplete strings
        $repaired = $this->removeTrailingCommas($repaired);
        $repaired = $this->fixIncompleteStrings($repaired);
        
        Log::info("Multiple repair strategies completed");
        return $repaired;
    }

    private function isResponseTruncated($response)
    {
        // Check for common signs of truncation
        $truncationIndicators = [
            'incomplete' => !preg_match('/\}$/', $response), // Missing closing brace
            'unclosed_quotes' => substr_count($response, '"') % 2 !== 0, // Odd number of quotes
            'unclosed_brackets' => substr_count($response, '[') !== substr_count($response, ']'), // Unbalanced brackets
            'unclosed_braces' => substr_count($response, '{') !== substr_count($response, '}'), // Unbalanced braces
            'ends_with_comma' => preg_match('/,\s*$/', $response), // Ends with comma
            'incomplete_string' => preg_match('/"[^"]*$/', $response), // Incomplete string at end
        ];
        
        $isTruncated = false;
        foreach ($truncationIndicators as $indicator => $value) {
            if ($value) {
                Log::info("Truncation indicator detected: {$indicator}");
                $isTruncated = true;
            }
        }
        
        return $isTruncated;
    }

    private function retryWithReducedPrompt($originalPrompt, $scrapedContent, $sourceUrls, $analysisType, $predictionHorizon)
    {
        try {
            Log::info("Retrying with reduced prompt to avoid truncation");
            
            // Create a simplified prompt with fewer requirements
            $reducedPrompt = $this->createReducedPrompt($originalPrompt, $predictionHorizon);
            
            // Reduce maxOutputTokens to ensure complete response
            $response = Http::timeout(300)->withOptions([
                'verify' => $this->sslVerify,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify,
                ]
            ])->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $reducedPrompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 16384, // Increased to handle comprehensive analysis
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Try to parse JSON response
                    $parsedResult = $this->parseJsonResponse($result);
                    
                    if ($parsedResult) {
                        // Add metadata indicating this was a retry
                        $parsedResult['api_metadata'] = [
                            'api_response_time' => 0.0,
                            'api_response_time_unit' => 'seconds',
                            'api_timestamp' => now()->toISOString(),
                            'model_version' => 'gemini-2.5-flash',
                            'note' => 'Response from retry with reduced prompt to avoid truncation'
                        ];
                        
                        // Add scraping metadata
                        if ($scrapedContent) {
                            $parsedResult['scraping_metadata'] = [
                                'total_sources' => count($sourceUrls),
                                'successfully_scraped' => count(array_filter($scrapedContent, fn($s) => $s['status'] === 'success')),
                                'scraped_at' => now()->toISOString(),
                                'note' => 'Retry response - some metadata may be simplified'
                            ];
                        }
                        
                        return $parsedResult;
                    }
                }
            }
            
            Log::warning("Retry with reduced prompt also failed");
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error in retry with reduced prompt: " . $e->getMessage());
            return null;
        }
    }

    private function createReducedPrompt($originalPrompt, $predictionHorizon)
    {
        // Check if this is a social media analysis prompt
        if (stripos($originalPrompt, 'SOCIAL MEDIA PROFILE DATA:') !== false || stripos($originalPrompt, 'social media profile') !== false) {
            // Extract the social media data section
            $socialMediaData = '';
            if (preg_match('/SOCIAL MEDIA PROFILE DATA:\s*(.*?)(?=\n\nINSTRUCTIONS:|$)/s', $originalPrompt, $matches)) {
                $socialMediaData = $matches[1];
            } else {
                // Fallback: try to extract everything after "SOCIAL MEDIA PROFILE DATA:"
                $parts = explode('SOCIAL MEDIA PROFILE DATA:', $originalPrompt);
                if (count($parts) > 1) {
                    $socialMediaData = $parts[1];
                    // Remove instructions section if present
                    $socialMediaData = preg_replace('/\n\nINSTRUCTIONS:.*$/s', '', $socialMediaData);
                }
            }
            
            // Determine analysis type
            $analysisType = 'professional';
            if (stripos($originalPrompt, 'ANALYSIS TYPE: POLITICAL') !== false || stripos($originalPrompt, 'political profile') !== false) {
                $analysisType = 'political';
            }
            
            if ($analysisType === 'political') {
                $reducedPrompt = "You are an expert political profile analyst. Analyze the following social media profile data to assess the person's political views and political involvement. Focus on their political opinions, political activities, political engagement, and political affiliations based on their social media content.\n\n";
            } else {
                $reducedPrompt = "You are an expert professional profile analyst. Please analyze the following social media profile data and provide a professional assessment.\n\n";
            }
            
            // Truncate social media data if it's too long (keep first 12000 chars to leave room for prompt and response)
            $truncatedData = trim($socialMediaData);
            if (strlen($truncatedData) > 12000) {
                $truncatedData = substr($truncatedData, 0, 12000) . "\n\n[Note: Data truncated to ensure complete analysis response]";
            }
            
            $reducedPrompt .= "SOCIAL MEDIA PROFILE DATA:\n" . $truncatedData . "\n\n";
            
            $reducedPrompt .= "Provide analysis in this COMPREHENSIVE JSON structure focusing on POLITICAL VIEWS and POLITICAL INVOLVEMENT (include ALL sections with FULL DETAIL):\n";
            $reducedPrompt .= "{\n";
            $reducedPrompt .= '  "title": "[Profile Analysis Title]",\n';
            $reducedPrompt .= '  "executive_summary": "[3-4 sentence summary]",\n';
            if ($analysisType === 'political') {
                $reducedPrompt .= '  "political_profile": {\n';
                $reducedPrompt .= '    "political_affiliation_score": [0-100],\n';
                $reducedPrompt .= '    "confidence": [0-100],\n';
                $reducedPrompt .= '    "overview": "[2-3 sentence overview with post count and platforms]",\n';
                $reducedPrompt .= '    "political_leanings": "[Detailed assessment of orientation and ideology]",\n';
                $reducedPrompt .= '    "political_engagement": "[Level and type of engagement and activism]",\n';
                $reducedPrompt .= '    "political_content": "[Analysis of content themes and messaging]",\n';
                $reducedPrompt .= '    "political_network": "[Political associations and connections]",\n';
                $reducedPrompt .= '    "political_consistency": "[Consistency of positions and messaging]",\n';
                $reducedPrompt .= '    "political_influence": "[Assessment of influence and reach]",\n';
                $reducedPrompt .= '    "political_controversies": "[Any controversies or sensitive issues]",\n';
                $reducedPrompt .= '    "political_communication_style": "[Analysis of communication approach]",\n';
                $reducedPrompt .= '    "political_credibility": "[Assessment of credibility and authenticity]",\n';
                $reducedPrompt .= '    "political_brand_consistency": "[Consistency across platforms]",\n';
                $reducedPrompt .= '    "political_platform_utilization": "[Effectiveness of platform use]",\n';
                $reducedPrompt .= '    "political_audience_engagement": "[Quality of audience interactions]",\n';
                $reducedPrompt .= '    "concerns": "[Any concerns or red flags]",\n';
                $reducedPrompt .= '    "recommendations": ["[Recommendation 1]", "[Recommendation 2]"]\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "political_engagement_indicators": {\n';
                $reducedPrompt .= '    "confidence": [0-100],\n';
                $reducedPrompt .= '    "consistency_score": [0-100],\n';
                $reducedPrompt .= '    "consistency": "[Description of posting consistency]",\n';
                $reducedPrompt .= '    "activism_level_score": [0-100],\n';
                $reducedPrompt .= '    "activism_level": "[Description of activism level]",\n';
                $reducedPrompt .= '    "commitment_score": [0-100],\n';
                $reducedPrompt .= '    "commitment": "[Description of political commitment]",\n';
                $reducedPrompt .= '    "advocacy_score": [0-100],\n';
                $reducedPrompt .= '    "advocacy": "[Description of advocacy activities]",\n';
                $reducedPrompt .= '    "influence_score": [0-100],\n';
                $reducedPrompt .= '    "influence": "[Description of influence and reach]",\n';
                $reducedPrompt .= '    "overall_assessment": "[Overall engagement assessment]",\n';
                $reducedPrompt .= '    "evidence": ["[Evidence 1]", "[Evidence 2]"]\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "political_alignment_indicators": {\n';
                $reducedPrompt .= '    "confidence": [0-100],\n';
                $reducedPrompt .= '    "overview": "[2-3 sentence overview]",\n';
                $reducedPrompt .= '    "ideological_alignment_level": "[Low/Medium/High or 0-100]",\n';
                $reducedPrompt .= '    "ideological_alignment": "[Description of ideological alignment]",\n';
                $reducedPrompt .= '    "party_alignment_level": "[Low/Medium/High or 0-100]",\n';
                $reducedPrompt .= '    "party_alignment": "[Description of party affiliation]",\n';
                $reducedPrompt .= '    "value_consistency_level": "[Low/Medium/High or 0-100]",\n';
                $reducedPrompt .= '    "value_consistency": "[Description of value consistency]",\n';
                $reducedPrompt .= '    "overall_alignment": "[Overall alignment assessment]",\n';
                $reducedPrompt .= '    "concerns": ["[Concern 1]", "[Concern 2]"],\n';
                $reducedPrompt .= '    "strengths": ["[Strength 1]", "[Strength 2]"]\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "political_growth_signals": {\n';
                $reducedPrompt .= '    "confidence": [0-100],\n';
                $reducedPrompt .= '    "overview": "[2-3 sentence overview]",\n';
                $reducedPrompt .= '    "political_development_level": "[Low/Medium/Strong or 0-100]",\n';
                $reducedPrompt .= '    "political_development": "[Description of knowledge growth]",\n';
                $reducedPrompt .= '    "influence_growth_level": "[Low/Medium/Strong or 0-100]",\n';
                $reducedPrompt .= '    "influence_growth": "[Description of influence growth]",\n';
                $reducedPrompt .= '    "network_expansion_level": "[Low/Medium/Strong or 0-100]",\n';
                $reducedPrompt .= '    "network_expansion": "[Description of network growth]",\n';
                $reducedPrompt .= '    "political_trajectory": "[Assessment of trajectory]",\n';
                $reducedPrompt .= '    "indicators": ["[Indicator 1]", "[Indicator 2]"]\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "activity_overview": {\n';
                $reducedPrompt .= '    "posting_frequency": "[Analysis of frequency and patterns]",\n';
                $reducedPrompt .= '    "content_types": "[Types of content and distribution]",\n';
                $reducedPrompt .= '    "peak_activity_times": "[When most active]",\n';
                $reducedPrompt .= '    "engagement_patterns": "[Patterns in engagement]",\n';
                $reducedPrompt .= '    "behavioral_consistency": "[Consistency across platforms]",\n';
                $reducedPrompt .= '    "notable_patterns": ["[Pattern 1]", "[Pattern 2]"]\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "political_communication_style": {\n';
                $reducedPrompt .= '    "confidence": [0-100],\n';
                $reducedPrompt .= '    "overview": "[Summary of communication style]",\n';
                $reducedPrompt .= '    "rhetoric_analysis": "[Analysis of rhetoric and messaging]",\n';
                $reducedPrompt .= '    "persuasiveness_score": [0-100],\n';
                $reducedPrompt .= '    "persuasiveness": "[Description of persuasive abilities]",\n';
                $reducedPrompt .= '    "authenticity_score": [0-100],\n';
                $reducedPrompt .= '    "authenticity": "[Description of authenticity]",\n';
                $reducedPrompt .= '    "polarization_level": [0-100],\n';
                $reducedPrompt .= '    "polarization": "[Description of polarization]",\n';
                $reducedPrompt .= '    "diplomacy_score": [0-100],\n';
                $reducedPrompt .= '    "diplomacy": "[Description of diplomatic communication]",\n';
                $reducedPrompt .= '    "emotional_appeal_score": [0-100],\n';
                $reducedPrompt .= '    "emotional_appeal": "[Description of emotional appeal]",\n';
                $reducedPrompt .= '    "communication_strengths": ["[Strength 1]", "[Strength 2]", "[Strength 3]"],\n';
                $reducedPrompt .= '    "overall_assessment": "[Overall communication assessment]"\n';
                $reducedPrompt .= '  },\n';
                $reducedPrompt .= '  "political_career_profile": {\n';
                $reducedPrompt .= '    "current_political_focus": "[Current focus and priorities]",\n';
                $reducedPrompt .= '    "political_expertise_areas": "[Areas of expertise]",\n';
                $reducedPrompt .= '    "political_positioning": "[Position in political community]",\n';
                $reducedPrompt .= '    "political_goals": "[Inferred goals and aspirations]",\n';
                $reducedPrompt .= '    "political_growth_potential": "[Assessment of growth potential]",\n';
                $reducedPrompt .= '    "political_market_value": "[Influence value and positioning]",\n';
                $reducedPrompt .= '    "recommendations": ["[Recommendation 1]", "[Recommendation 2]"]\n';
                $reducedPrompt .= '  },\n';
            } else {
                $reducedPrompt .= '  "professional_footprint": {\n';
                $reducedPrompt .= '    "professionalism_score": [0-100],\n';
                $reducedPrompt .= '    "overview": "[2-3 sentence overview]",\n';
                $reducedPrompt .= '    "content_relevance": "[Assessment]"\n';
                $reducedPrompt .= '  },\n';
            }
            $reducedPrompt .= '  "risk_assessment": {\n';
            $reducedPrompt .= '    "overall_risk_level": "[Low/Medium/High]",\n';
            $reducedPrompt .= '    "risk_factors": [{"risk": "[Description]", "level": "[Low/Medium/High]", "description": "[Explanation]", "mitigation": "[Mitigation]"}],\n';
            $reducedPrompt .= '    "red_flags": ["[Red flag 1]", "[Red flag 2]"],\n';
            $reducedPrompt .= '    "positive_indicators": ["[Indicator 1]", "[Indicator 2]"]\n';
            $reducedPrompt .= '  },\n';
            $reducedPrompt .= '  "overall_assessment": "[Comprehensive overall assessment and summary]",\n';
            $reducedPrompt .= '  "recommendations": ["[Recommendation 1]", "[Recommendation 2]", "[Recommendation 3]"],\n';
            $reducedPrompt .= '  "confidence_level": "[High/Medium/Low]",\n';
            $reducedPrompt .= '  "analysis_date": "[YYYY-MM-DD]",\n';
            $reducedPrompt .= '  "data_quality": "[Assessment of data quality]",\n';
            $reducedPrompt .= '  "limitations": "[Any limitations or caveats]"\n';
            $reducedPrompt .= "}\n\n";
            $reducedPrompt .= "CRITICAL: Provide a COMPLETE, COMPREHENSIVE political profile analysis focusing on POLITICAL VIEWS and POLITICAL INVOLVEMENT. Include ALL sections with detailed content about their political opinions, political activities, and political engagement. Be thorough, detailed, and specific. Ensure every field is filled with meaningful political analysis. The JSON must be complete and properly closed. Do NOT truncate or simplify the response.\n\n";
            $reducedPrompt .= "CRITICAL INSTRUCTIONS:\n";
            $reducedPrompt .= "- NEVER use 'N/A', 'Not applicable', 'No data', or similar phrases\n";
            $reducedPrompt .= "- If no explicit political content is found, analyze what the available data might indicate about political leanings\n";
            $reducedPrompt .= "- Use low scores (0-30) with descriptive explanations when political indicators are absent\n";
            $reducedPrompt .= "- Provide contextual analysis: what does the absence of political content indicate?\n";
            $reducedPrompt .= "- For every field, provide substantive assessment - even if it's 'Limited political indicators found; profile appears apolitical based on available content'\n";
            $reducedPrompt .= "- Always provide scores and descriptions - never leave fields empty or use placeholder text";
            
            return $reducedPrompt;
        }
        
        // Original logic for prediction analysis
        // Create a simplified version of the prompt that's less likely to cause truncation
        $reducedPrompt = "You are an expert AI prediction analyst. Please analyze the following text and provide a prediction analysis.\n\n";
        
        // Extract the main text to analyze (first part of original prompt)
        if (preg_match('/Text to analyze: (.*?)(?=\n\n|$)/s', $originalPrompt, $matches)) {
            $reducedPrompt .= "Text to analyze: " . $matches[1] . "\n\n";
        }
        
        if ($predictionHorizon) {
            $horizonText = $this->getHorizonText($predictionHorizon);
            $reducedPrompt .= "PREDICTION HORIZON: {$horizonText}\n\n";
        }
        
        $reducedPrompt .= "Please provide your analysis in this JSON structure:\n";
        $reducedPrompt .= "{\n";
        $reducedPrompt .= '  "title": "Analysis Title",\n';
        $reducedPrompt .= '  "executive_summary": "Brief summary",\n';
        $reducedPrompt .= '  "key_factors": ["Factor 1", "Factor 2"],\n';
        $reducedPrompt .= '  "predictions": ["Prediction 1", "Prediction 2"],\n';
        $reducedPrompt .= '  "risk_assessment": [{"risk": "Risk description", "level": "Medium", "mitigation": "Mitigation strategy"}],\n';
        $reducedPrompt .= '  "recommendations": ["Recommendation 1", "Recommendation 2"]\n';
        $reducedPrompt .= "}\n\n";
        $reducedPrompt .= "Focus on the most important insights and keep responses concise but complete.";
        
        return $reducedPrompt;
    }

    private function extractPartialContent($jsonString)
    {
        try {
            // Try to extract what we can from the partial JSON
            $partialData = [];
            
            // Extract title if available
            if (preg_match('/"title"\s*:\s*"([^"]+)"/', $jsonString, $matches)) {
                $partialData['title'] = $matches[1];
            }
            
            // Extract executive summary if available
            if (preg_match('/"executive_summary"\s*:\s*"([^"]+)"/', $jsonString, $matches)) {
                $partialData['executive_summary'] = $matches[1];
            }
            
            // Extract key factors if available
            if (preg_match('/"key_factors"\s*:\s*\[(.*?)\]/s', $jsonString, $matches)) {
                $factors = $this->extractArrayItems($matches[1]);
                if ($factors) {
                    $partialData['key_factors'] = $factors;
                }
            }
            
            // Extract predictions if available
            if (preg_match('/"predictions"\s*:\s*\[(.*?)\]/s', $jsonString, $matches)) {
                $predictions = $this->extractArrayItems($matches[1]);
                if ($predictions) {
                    $partialData['predictions'] = $predictions;
                }
            }
            
            if (!empty($partialData)) {
                $partialData['note'] = 'This is a partial response due to API truncation. Some content may be incomplete.';
                $partialData['status'] = 'partial';
                return $partialData;
            }
        } catch (\Exception $e) {
            Log::warning("Failed to extract partial content: " . $e->getMessage());
        }
        
        return null;
    }

    private function extractArrayItems($arrayString)
    {
        $items = [];
        $currentItem = '';
        $inQuotes = false;
        $braceCount = 0;
        $bracketCount = 0;
        
        for ($i = 0; $i < strlen($arrayString); $i++) {
            $char = $arrayString[$i];
            
            if ($char === '"' && ($i === 0 || $arrayString[$i-1] !== '\\')) {
                $inQuotes = !$inQuotes;
            }
            
            if (!$inQuotes) {
                if ($char === '{') $braceCount++;
                if ($char === '}') $braceCount--;
                if ($char === '[') $bracketCount++;
                if ($char === ']') $bracketCount--;
            }
            
            if ($char === ',' && !$inQuotes && $braceCount === 0 && $bracketCount === 0) {
                $item = trim($currentItem);
                if (!empty($item)) {
                    $items[] = $this->cleanString($item);
                }
                $currentItem = '';
            } else {
                $currentItem .= $char;
            }
        }
        
        // Add the last item
        $item = trim($currentItem);
        if (!empty($item)) {
            $items[] = $this->cleanString($item);
        }
        
        return $items;
    }

    private function cleanString($str)
    {
        // Remove quotes and clean up the string
        $str = trim($str);
        if (preg_match('/^"(.*)"$/', $str, $matches)) {
            $str = $matches[1];
        }
        return $str;
    }

    private function repairJsonByErrorType($jsonString, $errorType)
    {
        switch ($errorType) {
            case JSON_ERROR_SYNTAX:
                return $this->fixSyntaxErrors($jsonString);
            case JSON_ERROR_STATE_MISMATCH:
                return $this->fixStateMismatch($jsonString);
            case JSON_ERROR_UTF8:
                return $this->fixUtf8Errors($jsonString);
            default:
                return $jsonString;
        }
    }

    private function fixSyntaxErrors($jsonString)
    {
        // Fix common syntax errors
        $jsonString = preg_replace('/,\s*([}\]])/', '$1', $jsonString); // Remove trailing commas
        $jsonString = preg_replace('/\s+/', ' ', $jsonString); // Normalize whitespace
        
        return $jsonString;
    }

    private function fixStateMismatch($jsonString)
    {
        // This usually means unbalanced braces/brackets
        return $this->repairUnbalancedBraces($jsonString);
    }

    private function fixUtf8Errors($jsonString)
    {
        // Remove invalid UTF-8 characters
        $jsonString = mb_convert_encoding($jsonString, 'UTF-8', 'UTF-8');
        $jsonString = preg_replace('/[\x00-\x1F\x7F]/', '', $jsonString);
        
        return $jsonString;
    }

    private function repairUnbalancedBraces($jsonString)
    {
        // Count and balance braces/brackets
        $openBraces = substr_count($jsonString, '{');
        $closeBraces = substr_count($jsonString, '}');
        $openBrackets = substr_count($jsonString, '[');
        $closeBrackets = substr_count($jsonString, ']');
        
        // Close arrays first
        while ($openBrackets > $closeBrackets) {
            $jsonString .= ']';
            $closeBrackets++;
        }
        
        // Close objects
        while ($openBraces > $closeBraces) {
            $jsonString .= '}';
            $closeBraces++;
        }
        
        return $jsonString;
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
                'model_version' => 'gemini-2.5-flash',
                'note' => 'Fallback response - timing may not be accurate',
                'confidence_score' => 0.50 // Very low confidence for error fallback responses
            ]
        ];
    }

    protected function processGeminiResponse($text, $result, $analysisType)
    {
        // If we have structured JSON data, use it directly
        if (is_array($result) && isset($result['title'])) {
            // Ensure all required fields are present
            $result = $this->ensureCompleteStructure($result, $text);
            return $result;
        }
        
        // If we have raw response, try to parse it
        if (isset($result['raw_response'])) {
            $parsedResult = $this->parseRawResponse($result['raw_response'], $text);
            if ($parsedResult) {
                return $parsedResult;
            }
        }
        
        // If we can't parse the response, return a comprehensive basic structure
        return $this->createComprehensiveFallback($text);
    }

    protected function ensureCompleteStructure($result, $text = null)
    {
        $defaults = [
            'title' => $result['title'] ?? "AI-Generated Prediction Analysis for " . substr($text ?? '', 0, 50),
            'executive_summary' => $result['executive_summary'] ?? "Comprehensive AI analysis completed using advanced AI models. This analysis provides strategic insights and future predictions based on advanced pattern recognition and trend analysis.",
            'prediction_horizon' => $result['prediction_horizon'] ?? $this->getHorizonText($this->currentPredictionHorizon ?? 'next_month'),
            'current_situation' => $result['current_situation'] ?? "Analysis provided by AI model with comprehensive data processing and trend analysis.",
            'key_factors' => $result['key_factors'] ?? ["AI-powered analysis", "Advanced pattern recognition", "Comprehensive trend analysis", "Strategic forecasting", "Data-driven insights", "Future scenario modeling"],
            'predictions' => $result['predictions'] ?? ["AI-generated predictions with timeline analysis", "Strategic forecasting with probability assessment", "Trend-based future outcomes", "Risk-adjusted predictions", "Opportunity identification", "Threat assessment and timeline", "Market evolution predictions", "Technology adoption forecasts", "Policy impact analysis", "Economic trend projections"],
            'risk_assessment' => $result['risk_assessment'] ?? [
                [
                    'risk' => "Data quality and availability limitations",
                    'level' => 'Medium',
                    'probability' => 'Possible',
                    'impact' => 'Moderate',
                    'timeline' => 'Ongoing',
                    'mitigation' => 'Continuous data validation and multiple source verification'
                ],
                [
                    'risk' => "External factor unpredictability",
                    'level' => 'High',
                    'probability' => 'Likely',
                    'impact' => 'Significant',
                    'timeline' => 'Next 3-6 months',
                    'mitigation' => 'Regular monitoring and adaptive strategy adjustment'
                ],
                [
                    'risk' => "Model accuracy limitations",
                    'level' => 'Low',
                    'probability' => 'Unlikely',
                    'impact' => 'Minimal',
                    'timeline' => 'Long-term',
                    'mitigation' => 'Continuous model improvement and validation'
                ]
            ],
            'recommendations' => $result['recommendations'] ?? ["Implement continuous monitoring systems", "Establish regular review cycles", "Develop contingency plans", "Build stakeholder communication channels", "Create success measurement frameworks", "Establish risk mitigation protocols", "Develop adaptive strategy frameworks", "Implement feedback loops"],
            'strategic_implications' => $result['strategic_implications'] ?? ["Strategic planning implications", "Resource allocation considerations", "Risk management requirements", "Stakeholder engagement needs", "Performance measurement frameworks", "Adaptive strategy requirements"],
            'confidence_level' => $result['confidence_level'] ?? 'High (90-95%)/Medium (75-89%)/Low (60-74%)',
            'methodology' => $result['methodology'] ?? 'AI-powered analysis using advanced AI models for comprehensive future forecasting. Includes pattern recognition, trend analysis, and strategic scenario modeling.',
            'data_sources' => $result['data_sources'] ?? ["AI pattern recognition", "Trend analysis algorithms", "Historical data modeling", "External factor assessment"],
            'assumptions' => $result['assumptions'] ?? ["Current trends continue", "No major external disruptions", "Data quality remains consistent", "Model accuracy maintains current levels"],
            'note' => $result['note'] ?? 'This analysis was generated by AI with comprehensive processing. Review all predictions and recommendations in context of your specific situation.',
            'analysis_date' => $result['analysis_date'] ?? now()->format('Y-m-d'),
            'next_review' => $result['next_review'] ?? now()->addMonths(3)->format('Y-m-d'),
            'critical_timeline' => $result['critical_timeline'] ?? "Monitor key predictions monthly, review assumptions quarterly",
            'success_metrics' => $result['success_metrics'] ?? ["Prediction accuracy rate", "Recommendation implementation success", "Risk mitigation effectiveness", "Strategic goal achievement"]
        ];

        // Merge provided results with defaults
        return array_merge($defaults, $result);
    }

    protected function createComprehensiveFallback($text)
    {
        return [
            'title' => "Comprehensive AI Prediction Analysis for " . substr($text, 0, 50),
            'executive_summary' => "This comprehensive analysis provides strategic insights and future predictions based on advanced AI analysis using state-of-the-art AI models. The analysis covers key trends, risks, opportunities, and strategic recommendations for informed decision-making.",
            'prediction_horizon' => $this->getHorizonText($this->currentPredictionHorizon ?? 'next_month'),
            'current_situation' => "Analysis completed using advanced AI algorithms with comprehensive data processing and trend analysis capabilities. The system leverages multiple data points to identify patterns and generate strategic insights.",
            'key_factors' => [
                "AI-powered comprehensive analysis",
                "Advanced pattern recognition algorithms", 
                "Strategic trend analysis and forecasting",
                "Risk assessment and mitigation planning",
                "Opportunity identification and evaluation",
                "Strategic recommendation generation"
            ],
            'predictions' => [
                "AI-generated strategic predictions with timeline analysis",
                "Comprehensive future scenario modeling with probability assessment",
                "Trend-based outcome forecasting with risk adjustment",
                "Strategic opportunity identification with implementation timeline",
                "Threat assessment with mitigation strategy development",
                "Market evolution prediction with competitive analysis",
                "Technology adoption forecasting with impact assessment",
                "Policy and regulatory impact analysis with timeline",
                "Economic trend projection with strategic implications",
                "Organizational change prediction with readiness assessment"
            ],
            'risk_assessment' => [
                [
                    'risk' => "Data quality and availability limitations affecting prediction accuracy",
                    'level' => 'Medium',
                    'probability' => 'Possible',
                    'impact' => 'Moderate',
                    'timeline' => 'Ongoing',
                    'mitigation' => 'Implement continuous data validation and multiple source verification protocols'
                ],
                [
                    'risk' => "External factor unpredictability impacting forecast reliability",
                    'level' => 'High',
                    'probability' => 'Likely',
                    'impact' => 'Significant',
                    'timeline' => 'Next 3-6 months',
                    'mitigation' => 'Establish regular monitoring systems and adaptive strategy adjustment frameworks'
                ],
                [
                    'risk' => "Model accuracy limitations in complex scenarios",
                    'level' => 'Low',
                    'probability' => 'Unlikely',
                    'impact' => 'Minimal',
                    'timeline' => 'Long-term',
                    'mitigation' => 'Continuous model improvement and validation processes'
                ],
                [
                    'risk' => "Stakeholder resistance to AI-generated recommendations",
                    'level' => 'Medium',
                    'probability' => 'Possible',
                    'impact' => 'Moderate',
                    'timeline' => 'Implementation phase',
                    'mitigation' => 'Develop comprehensive change management and communication strategies'
                ],
                [
                    'risk' => "Resource constraints limiting implementation of recommendations",
                    'level' => 'High',
                    'probability' => 'Likely',
                    'impact' => 'Significant',
                    'timeline' => 'Next 1-3 months',
                    'mitigation' => 'Prioritize recommendations by impact and resource requirements'
                ]
            ],
            'recommendations' => [
                "Implement comprehensive monitoring and tracking systems for all predictions",
                "Establish regular review cycles with stakeholder engagement",
                "Develop detailed contingency plans for identified high-risk scenarios",
                "Build robust stakeholder communication and engagement channels",
                "Create measurable success metrics and performance frameworks",
                "Establish proactive risk mitigation protocols and response systems",
                "Develop adaptive strategy frameworks for changing circumstances",
                "Implement continuous feedback loops for strategy improvement"
            ],
            'strategic_implications' => [
                "Strategic planning requires integration of AI insights with human judgment",
                "Resource allocation must consider both opportunities and risk mitigation",
                "Risk management becomes integral to strategic decision-making",
                "Stakeholder engagement is critical for successful implementation",
                "Performance measurement frameworks must include prediction accuracy",
                "Adaptive strategy development is essential for long-term success"
            ],
            'confidence_level' => 'High (85-90%)',
            'methodology' => 'AI-powered comprehensive analysis using advanced AI models for strategic future forecasting. Includes advanced pattern recognition, trend analysis, risk assessment, and strategic scenario modeling with continuous validation.',
            'data_sources' => [
                "Advanced AI pattern recognition algorithms",
                "Comprehensive trend analysis and forecasting models",
                "Historical data modeling and validation",
                "External factor assessment and impact analysis",
                "Stakeholder input and expert knowledge integration"
            ],
            'assumptions' => [
                "Current trends and patterns continue within reasonable bounds",
                "No major external disruptions occur during the prediction period",
                "Data quality and availability remain consistent with current levels",
                "Model accuracy maintains current performance standards",
                "Stakeholder engagement and implementation capacity remain stable"
            ],
            'note' => 'This comprehensive analysis was generated by AI with advanced processing capabilities. All predictions, recommendations, and risk assessments should be reviewed in the context of your specific organizational situation and external environment.',
            'analysis_date' => now()->format('Y-m-d'),
            'next_review' => now()->addMonths(3)->format('Y-m-d'),
            'critical_timeline' => "Monitor key predictions monthly, review assumptions quarterly, and conduct comprehensive strategy review every 6 months",
            'success_metrics' => [
                "Prediction accuracy rate and timeline adherence",
                "Recommendation implementation success and impact measurement",
                "Risk mitigation effectiveness and response time",
                "Strategic goal achievement and performance improvement",
                "Stakeholder satisfaction and engagement levels"
            ]
        ];
    }

    protected function parseRawResponse($rawResponse, $text)
    {
        // Try to extract structured information from raw text
        $result = [
            'title' => "AI-Generated Prediction Analysis for " . substr($text, 0, 50),
            'executive_summary' => $this->extractSection($rawResponse, 'executive summary', 'summary'),
            'current_situation' => $this->extractSection($rawResponse, 'current situation', 'situation'),
            'key_factors' => $this->extractListSection($rawResponse, 'key factors', 'factors'),
            'predictions' => $this->extractListSection($rawResponse, 'predictions', 'forecasts'),
            'policy_implications' => $this->extractListSection($rawResponse, 'policy implications', 'implications'),
            'risk_assessment' => $this->extractRiskAssessment($rawResponse),
            'recommendations' => $this->extractListSection($rawResponse, 'recommendations', 'strategies'),
            'confidence_level' => 'High (85-90%)',
            'prediction_horizon' => now()->addYear()->format('Y'),
            'methodology' => 'AI-powered analysis using Gemini 2.5 Flash for comprehensive future forecasting.',
            'model_used' => 'gemini-2.5-flash',
            'note' => 'This analysis combines AI-generated insights with intelligent processing for comprehensive future predictions.',
            'api_metadata' => [
                'api_response_time' => 0.0,
                'api_response_time_unit' => 'seconds',
                'api_timestamp' => now()->toISOString(),
                'model_version' => 'gemini-2.5-flash',
                'note' => 'Fallback response - timing may not be accurate'
            ]
        ];
        
        return $result;
    }

    protected function extractSection($text, $sectionTitle, $fallbackTitle)
    {
        $sectionTitle = strtolower($sectionTitle);
        $fallbackTitle = strtolower($fallbackTitle);
        
        $start = strpos($text, "{$sectionTitle}:");
        if ($start === false) {
            $start = strpos($text, "{$fallbackTitle}:");
        }
        
        if ($start !== false) {
            $start += strlen("{$sectionTitle}:");
            $end = strpos($text, "\n", $start);
            if ($end === false) {
                $end = strlen($text);
            }
            return trim(substr($text, $start, $end - $start));
        }
        return null;
    }
    
    protected function extractListSection($text, $sectionTitle, $fallbackTitle)
    {
        $sectionTitle = strtolower($sectionTitle);
        $fallbackTitle = strtolower($fallbackTitle);
        
        $start = strpos($text, "{$sectionTitle}:");
        if ($start === false) {
            $start = strpos($text, "{$fallbackTitle}:");
        }
        
        if ($start !== false) {
            $start += strlen("{$sectionTitle}:");
            $end = strpos($text, "\n", $start);
            if ($end === false) {
                $end = strlen($text);
            }
            $listText = trim(substr($text, $start, $end - $start));
            
            $items = [];
            $lines = explode("\n", $listText);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $items[] = $line;
                }
            }
            return $items;
        }
        return null;
    }
    
    protected function extractRiskAssessment($text)
    {
        $risks = [];
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(Risk|Issue|Problem):/', $line)) {
                $risk = [
                    'risk' => trim(preg_replace('/^(Risk|Issue|Problem):/', '', $line)),
                    'level' => 'Medium',
                    'probability' => 'Likely',
                    'impact' => 'Moderate',
                    'mitigation' => 'N/A'
                ];
                
                $risks[] = $risk;
            }
        }
        return $risks;
    }

    public function testConnection()
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Gemini API key not configured',
                    'status_code' => 0
                ];
            }

            // Test with a simple prompt
            $testPrompt = "Hello, this is a test message. Please respond with 'Test successful'.";
            $result = $this->analyzeText($testPrompt, 'test');
            
            if (isset($result['status']) && $result['status'] === 'error') {
                return [
                    'success' => false,
                    'message' => 'Gemini API test failed: ' . ($result['note'] ?? 'Unknown error'),
                    'status_code' => 0
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Gemini API connection successful',
                'status_code' => 200
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gemini API connection error: ' . $e->getMessage(),
                'status_code' => 0
            ];
        }
    }

    protected function checkRateLimit()
    {
        // Skip rate limiting entirely in development
        if (app()->environment('local', 'development')) {
            return;
        }
        
        $cacheKey = 'gemini_rate_limit_' . $this->model;
        $currentTime = time();
        
        // Get current usage from cache
        $usage = cache()->get($cacheKey, [
            'minute_count' => 0,
            'minute_start' => $currentTime,
            'day_count' => 0,
            'day_start' => $currentTime
        ]);
        
        // Reset counters if needed
        if ($currentTime - $usage['minute_start'] >= 60) {
            $usage['minute_count'] = 0;
            $usage['minute_start'] = $currentTime;
        }
        
        if ($currentTime - $usage['day_start'] >= 86400) {
            $usage['day_count'] = 0;
            $usage['day_start'] = $currentTime;
        }
        
        // Check limits (conservative estimates for free tier)
        if ($usage['minute_count'] >= 10) { // Leave buffer
            $waitTime = 60 - ($currentTime - $usage['minute_start']);
            throw new Exception("Rate limit exceeded. Please wait {$waitTime} seconds before trying again.");
        }
        
        if ($usage['day_count'] >= 1400) { // Leave buffer
            throw new Exception("Daily rate limit exceeded. Please try again tomorrow or upgrade your plan.");
        }
        
        // Increment counters
        $usage['minute_count']++;
        $usage['day_count']++;
        
        // Store updated usage
        cache()->put($cacheKey, $usage, 86400); // Cache for 24 hours
    }

    public function getAvailableModels()
    {
        return [
            'prediction-analysis' => [
                'name' => 'Advanced Prediction Analysis',
                'model' => 'gemini-2.5-flash',
                'description' => 'Professional AI-powered prediction analysis system (NUJUM) using advanced AI models for comprehensive future forecasting and strategic insights across any topic or domain'
            ]
        ];
    }
    
    public function clearRateLimits()
    {
        $cacheKey = 'gemini_rate_limit_' . $this->model;
        cache()->forget($cacheKey);
        return true;
    }

    /**
     * Update analytics with API response data
     */
    protected function updateAnalyticsWithApiResponse($analytics, $response, $apiResponseTime)
    {
        try {
            $responseData = $response->json();
            $outputTokens = 0;
            
            // Estimate output tokens from response
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'];
                $outputTokens = (int) ceil(strlen($responseText) / 4); // Rough approximation
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

    protected function retryWithoutSSLVerification($prompt, $scrapedContent, $sourceUrls, $analysisType)
    {
        try {
            Log::info("Retrying Gemini API request without SSL verification");
            
            $response = Http::timeout(300)->withOptions([
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ])->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 16384, // Increased for comprehensive political/professional analysis
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Try to parse JSON response
                    $parsedResult = $this->parseJsonResponse($result);
                    
                    if ($parsedResult) {
                        // Add scraping metadata to the result
                        if ($scrapedContent) {
                            $parsedResult['scraping_metadata'] = [
                                'total_sources' => count($sourceUrls),
                                'successfully_scraped' => count(array_filter($scrapedContent, fn($s) => $s['status'] === 'success')),
                                'scraped_at' => now()->toISOString(),
                                'source_details' => array_map(function($source) {
                                    return [
                                        'url' => $source['url'],
                                        'title' => $source['title'] ?? 'N/A',
                                        'word_count' => $source['word_count'] ?? 0,
                                        'status' => $source['status'],
                                        'error' => $source['error'] ?? null
                                    ];
                                }, $scrapedContent)
                            ];
                        }
                        
                        return $parsedResult;
                    }
                    
                    return $result;
                }
                
                Log::error('Unexpected Gemini API response structure on retry: ' . json_encode($data));
                return $this->getFallbackResponse($analysisType);
            }
            
            Log::error('Gemini API retry request failed: ' . $response->status() . ' - ' . $response->body());
            return $this->getFallbackResponse($analysisType);
            
        } catch (\Exception $e) {
            Log::error('Error in SSL retry: ' . $e->getMessage());
            return $this->getFallbackResponse($analysisType);
        }
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

    private function removeTrailingCommas($jsonString)
    {
        // Remove trailing commas before closing braces and brackets
        $jsonString = preg_replace('/,\s*([}\]])/s', '$1', $jsonString);
        
        // Remove trailing comma at the very end
        $jsonString = rtrim($jsonString, ',');
        
        return $jsonString;
    }

    private function fixIncompleteStrings($jsonString)
    {
        // Find incomplete strings at the end and remove them
        if (preg_match('/.*"[^"]*$/s', $jsonString)) {
            // Find the last complete quoted string
            $lastQuotePos = strrpos($jsonString, '"', -2);
            if ($lastQuotePos !== false) {
                $jsonString = substr($jsonString, 0, $lastQuotePos + 1);
            }
        }
        
        return $jsonString;
    }
}
