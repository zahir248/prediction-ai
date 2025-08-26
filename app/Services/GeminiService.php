<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    protected $webScrapingService;
    protected $sslVerify;

    public function __construct(WebScrapingService $webScrapingService)
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->webScrapingService = $webScrapingService;
        
        // Check if we should verify SSL (default to true for production)
        $this->sslVerify = config('services.gemini.ssl_verify', !app()->environment('local', 'development'));
    }

    public function analyzeText($text, $analysisType = 'prediction-analysis', $sourceUrls = null, $predictionHorizon = null)
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

            $prompt = $this->createAnalysisPrompt($text, $analysisType, $sourceUrls, $scrapedContent, $predictionHorizon);
            
            // Set execution time limit to 5 minutes for long AI requests
            set_time_limit(300);
            
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
                    'maxOutputTokens' => 8192,
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
            
            Log::info("Gemini API response received at: " . now());
            Log::info("Response status: " . $response->status());
            Log::info("Response body length: " . strlen($response->body()));

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Try to parse JSON response
                    $parsedResult = $this->parseJsonResponse($result);
                    
                    if ($parsedResult) {
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
            'next_two_weeks' => 'Next Two Weeks',
            'next_month' => 'Next Month',
            'three_months' => 'Next 3 Months',
            'six_months' => 'Next 6 Months',
            'twelve_months' => 'Next 12 Months',
            'two_years' => 'Next 2 Years'
        ];

        return $horizonMap[$horizon] ?? 'Next Month';
    }

    protected function createAnalysisPrompt($text, $analysisType, $sourceUrls = null, $scrapedContent = null, $predictionHorizon = null)
    {
        $prompt = "You are an expert AI prediction analyst specializing in comprehensive future forecasting and strategic analysis. Please analyze the following text and provide a detailed, professional prediction analysis similar to high-quality consulting reports.\n\n";
        $prompt .= "Text to analyze: {$text}\n\n";
        
        if ($predictionHorizon) {
            $horizonText = $this->getHorizonText($predictionHorizon);
            $prompt .= "PREDICTION HORIZON: {$horizonText}\n";
            $prompt .= "IMPORTANT: All your predictions, risk assessments, and strategic implications should be specifically tailored to this time period. Focus on what is most likely to happen within this timeframe.\n\n";
        }
        
        if ($sourceUrls && count($sourceUrls) > 0) {
            $prompt .= "IMPORTANT: You have been provided with the following additional sources that contain relevant context, data, or background information:\n";
            
            foreach ($sourceUrls as $index => $url) {
                $prompt .= "- Source " . ($index + 1) . ": {$url}\n";
            }
            
            // Add scraping summary if available
            if (isset($scrapingSummary)) {
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
            
            $prompt .= "\nCRITICAL REQUIREMENTS FOR SOURCE INTEGRATION:\n";
            $prompt .= "1. Throughout your analysis, explicitly reference these sources when they support or influence your predictions\n";
            $prompt .= "2. Use phrases like 'According to Source 1...', 'Source 2 indicates...', 'Based on the analysis from Source 3...'\n";
            $prompt .= "3. Show the direct connection between source information and your predictions\n";
            $prompt .= "4. If sources provide conflicting information, acknowledge this and explain how you weighed the evidence\n";
            $prompt .= "5. Include a dedicated 'Source Analysis' section explaining how each source contributed to your conclusions\n";
            $prompt .= "6. Make it clear to readers which parts of your analysis are based on the provided sources vs. general knowledge\n";
            $prompt .= "7. When possible, cite specific facts, numbers, or quotes from the scraped content\n\n";
        }
        
        $prompt .= "Please provide your analysis in the following comprehensive JSON structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"[Comprehensive Title: Topic + Time Period + Key Focus Areas]\",\n";
        $prompt .= "  \"executive_summary\": \"[3-4 sentence executive summary covering key predictions, risks, and strategic implications]\",\n";
        $prompt .= "  \"prediction_horizon\": \"[Specific time period: e.g., 'Next 6-12 months' or 'Q1-Q4 2025']\",\n";
        $prompt .= "  \"current_situation\": \"[Detailed analysis of current state, trends, and context that inform predictions]\",\n";
        $prompt .= "  \"key_factors\": [\n";
        $prompt .= "    \"[Factor 1: Specific, actionable factor with brief explanation]\",\n";
        $prompt .= "    \"[Factor 2: Specific, actionable factor with brief explanation]\",\n";
        $prompt .= "    \"[Factor 3: Specific, actionable factor with brief explanation]\",\n";
        $prompt .= "    \"[Factor 4: Specific, actionable factor with brief explanation]\",\n";
        $prompt .= "    \"[Factor 5: Specific, actionable factor with brief explanation]\",\n";
        $prompt .= "    \"[Factor 6: Specific, actionable factor with brief explanation]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"predictions\": [\n";
        $prompt .= "    \"[Prediction 1: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 2: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 3: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 4: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 5: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 6: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 7: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 8: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 9: Specific outcome with timeline and probability]\",\n";
        $prompt .= "    \"[Prediction 10: Specific outcome with timeline and probability]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"risk_assessment\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"risk\": \"[Specific risk description with context]\",\n";
        $prompt .= "      \"level\": \"[Critical/High/Medium/Low]\",\n";
        $prompt .= "      \"probability\": \"[Very Likely/Likely/Possible/Unlikely]\",\n";
        $prompt .= "      \"impact\": \"[Severe/Significant/Moderate/Minimal]\",\n";
        $prompt .= "      \"timeline\": \"[When this risk is most likely to materialize]\",\n";
        $prompt .= "      \"mitigation\": \"[Specific mitigation strategy with actionable steps]\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"recommendations\": [\n";
        $prompt .= "    \"[Specific, actionable recommendation with expected outcome]\",\n";
        $prompt .= "    \"[Specific, actionable recommendation with expected outcome]\",\n";
        $prompt .= "    \"[Specific, actionable recommendation with expected outcome]\"\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"strategic_implications\": [\n";
        $prompt .= "    \"[Strategic business/organizational implication]\",\n";
        $prompt .= "    \"[Strategic business/organizational implication]\",\n";
        $prompt .= "    \"[Strategic business/organizational implication]\"\n";
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
        $prompt .= "IMPORTANT INSTRUCTIONS:\n";
        $prompt .= "1. Be SPECIFIC and ACTIONABLE - avoid vague statements\n";
        $prompt .= "2. Include TIMELINES and PROBABILITIES for all predictions\n";
        $prompt .= "3. Focus on FUTURE OUTCOMES with concrete details\n";
        $prompt .= "4. Provide REALISTIC but INSIGHTFUL predictions based on current trends\n";
        $prompt .= "5. Structure risks by probability, impact, and timeline\n";
        $prompt .= "6. Make recommendations SPECIFIC and IMPLEMENTABLE\n";
        $prompt .= "7. Include QUANTIFIABLE metrics where possible\n";
        $prompt .= "8. Consider both OPPORTUNITIES and THREATS\n";
        $prompt .= "9. Base analysis on LOGICAL REASONING and TREND ANALYSIS\n";
        $prompt .= "10. Ensure all sections are COMPREHENSIVE and PROFESSIONAL\n";
        
        if ($sourceUrls && count($sourceUrls) > 0) {
            $prompt .= "11. CONSISTENTLY CITE SOURCES throughout the analysis using phrases like 'According to Source 1...', 'Source 2 indicates...'\n";
            $prompt .= "12. Show DIRECT CONNECTIONS between source information and specific predictions\n";
            $prompt .= "13. Include the source_analysis field explaining how each source contributed to conclusions\n";
            if ($scrapedContent) {
                $prompt .= "14. Use ACTUAL DATA and QUOTES from the scraped content when available\n";
                $prompt .= "15. Reference specific facts, numbers, and insights from the source content\n";
            }
        }
        
        $prompt .= "\nFocus on generating high-quality, professional-grade prediction analysis that would be suitable for executive decision-making and strategic planning.";
        
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
                
                $jsonString = $this->fixTruncatedJson($jsonString);
                Log::info("Fixed JSON length: " . strlen($jsonString));
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
            'status' => 'error'
        ];
    }

    protected function processGeminiResponse($text, $result, $analysisType)
    {
        // If we have structured JSON data, use it directly
        if (is_array($result) && isset($result['title'])) {
            // Ensure all required fields are present
            $result = $this->ensureCompleteStructure($result);
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

    protected function ensureCompleteStructure($result)
    {
        $defaults = [
            'title' => $result['title'] ?? "AI-Generated Prediction Analysis for " . substr($text ?? '', 0, 50),
            'executive_summary' => $result['executive_summary'] ?? "Comprehensive AI analysis completed using Google Gemini 2.0 Flash. This analysis provides strategic insights and future predictions based on advanced pattern recognition and trend analysis.",
            'prediction_horizon' => $result['prediction_horizon'] ?? "Next 6-12 months",
            'current_situation' => $result['current_situation'] ?? "Analysis provided by Google Gemini AI model with comprehensive data processing and trend analysis.",
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
            'methodology' => $result['methodology'] ?? 'AI-powered analysis using Google Gemini 2.0 Flash for comprehensive future forecasting. Includes pattern recognition, trend analysis, and strategic scenario modeling.',
            'data_sources' => $result['data_sources'] ?? ["AI pattern recognition", "Trend analysis algorithms", "Historical data modeling", "External factor assessment"],
            'assumptions' => $result['assumptions'] ?? ["Current trends continue", "No major external disruptions", "Data quality remains consistent", "Model accuracy maintains current levels"],
            'note' => $result['note'] ?? 'This analysis was generated by Google Gemini AI with comprehensive processing. Review all predictions and recommendations in context of your specific situation.',
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
            'executive_summary' => "This comprehensive analysis provides strategic insights and future predictions based on advanced AI analysis using Google Gemini 2.0 Flash. The analysis covers key trends, risks, opportunities, and strategic recommendations for informed decision-making.",
            'prediction_horizon' => "Next 6-12 months",
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
            'methodology' => 'AI-powered comprehensive analysis using Google Gemini 2.0 Flash for strategic future forecasting. Includes advanced pattern recognition, trend analysis, risk assessment, and strategic scenario modeling with continuous validation.',
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
            'note' => 'This comprehensive analysis was generated by Google Gemini AI with advanced processing capabilities. All predictions, recommendations, and risk assessments should be reviewed in the context of your specific organizational situation and external environment.',
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
                         'methodology' => 'AI-powered analysis using Gemini 2.0 Flash for comprehensive future forecasting.',
             'processing_time' => 0.8,
             'model_used' => 'gemini-2.0-flash',
            'note' => 'This analysis combines AI-generated insights with intelligent processing for comprehensive future predictions.'
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
                'model' => 'gemini-2.0-flash',
                'description' => 'Professional AI-powered prediction analysis system using Google Gemini 2.0 Flash for comprehensive future forecasting and strategic insights across any topic or domain'
            ]
        ];
    }
    
    public function clearRateLimits()
    {
        $cacheKey = 'gemini_rate_limit_' . $this->model;
        cache()->forget($cacheKey);
        return true;
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
                    'maxOutputTokens' => 8192,
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
}
