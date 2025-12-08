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
        // Handle social media analysis differently
        if ($analysisType === 'social-media-analysis') {
            return $this->createSocialMediaAnalysisPrompt($text);
        }
        
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
