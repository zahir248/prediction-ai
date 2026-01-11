<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialMediaService
{
    protected $apifyApiToken;
    protected $apifyBaseUrl = 'https://api.apify.com/v2';
    protected $timeout;
    protected $sslVerify;

    // Apify Actor IDs for social media scraping
    // These can be configured in config/services.php or use defaults
    protected $facebookActorId;
    protected $instagramActorId;
    protected $tiktokActorId;
    protected $twitterActorId;

    public function __construct()
    {
        $this->apifyApiToken = config('services.apify.api_token');
        $this->timeout = config('services.apify.timeout', 300);
        
        // Check if we should verify SSL (default to false for development, true for production)
        $this->sslVerify = config('services.apify.ssl_verify', !app()->environment('production'));
        
        // Get actor IDs from config or use defaults
        $this->facebookActorId = config('services.apify.facebook_actor_id', 'apify/facebook-posts-scraper');
        $this->instagramActorId = config('services.apify.instagram_actor_id', 'apify/instagram-scraper');
        $this->tiktokActorId = config('services.apify.tiktok_actor_id', 'clockworks/tiktok-profile-scraper');
        $this->twitterActorId = config('services.apify.twitter_actor_id', 'apify/twitter-scraper');
        
        if (empty($this->apifyApiToken)) {
            Log::warning('Apify API token is not configured', [
                'config_value' => config('services.apify.api_token'),
                'env_value' => env('APIFY_API_TOKEN'),
            ]);
        } else {
            Log::info('Apify API token loaded successfully', [
                'token_length' => strlen($this->apifyApiToken),
                'token_prefix' => substr($this->apifyApiToken, 0, 10) . '...',
                'ssl_verify' => $this->sslVerify
            ]);
        }
    }

    /**
     * Get and validate Apify API token
     */
    protected function getApiToken()
    {
        $token = $this->apifyApiToken ?? config('services.apify.api_token');
        
        if (empty($token) || $token === 'your_apify_token') {
            $token = env('APIFY_API_TOKEN');
        }
        
        if (empty($token) || $token === 'your_apify_token') {
            return null;
        }
         
        return $token;
    }

    /**
     * Run an Apify actor and wait for results
     */
    protected function runApifyActor($actorId, $input, $waitForFinish = true)
    {
        // Track Apify usage
        $apifyStartTime = microtime(true);
        $platform = $this->getPlatformFromActorId($actorId);
        
        // Increase execution time limit for Apify operations
        // Longer timeout for fetching all posts (up to 10,000 posts)
        // Instagram scraping can be slower, so give it more time
        // Instagram post scraper can take 10-15 minutes for some accounts
        if (strpos($actorId, 'facebook') !== false && ($input['maxPosts'] ?? 0) > 1000) {
            $timeout = 600; // 10 minutes for large Facebook requests
        } elseif (strpos($actorId, 'instagram') !== false) {
            $timeout = 900; // 15 minutes for Instagram (can be very slow due to rate limiting and account size)
        } else {
            $timeout = 300; // 5 minutes for others
        }
        set_time_limit($timeout);
        
        try {
            $token = $this->getApiToken();
            if (!$token) {
                $apifyResponseTime = round(microtime(true) - $apifyStartTime, 4);
                return [
                    'success' => false,
                    'error' => 'Apify API token not configured. Please set APIFY_API_TOKEN in your .env file and run: php artisan config:clear',
                    'apify_usage' => [
                        'platform' => $platform,
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            // Start the actor run
            // Apify API v2 format: POST /v2/acts/{actorId}/runs
            // Actor ID format: username~actor-name (e.g., apify~facebook-posts-scraper)
            $normalizedActorId = str_replace('/', '~', $actorId);
            
            // Build request body (actorId goes in URL path, not body)
            // Include all possible input fields that different actors might need
            $requestBody = array_filter([
                'startUrls' => $input['startUrls'] ?? [],
                'searchTerms' => $input['searchTerms'] ?? null, // apidojo/tweet-scraper uses this
                'profiles' => $input['profiles'] ?? null, // TikTok profile scraper uses this
                'usernames' => $input['usernames'] ?? null, // Instagram scrapers often use this
                'username' => $input['username'] ?? null,
                'maxPosts' => $input['maxPosts'] ?? $input['maxItems'] ?? 10,
                'maxItems' => $input['maxItems'] ?? null,
                'maxComments' => $input['maxComments'] ?? null,
                'resultsLimit' => $input['resultsLimit'] ?? null,
                'resultsType' => $input['resultsType'] ?? null,
                'sort' => $input['sort'] ?? null, // apidojo/tweet-scraper uses this
                'tweetLanguage' => $input['tweetLanguage'] ?? null, // apidojo/tweet-scraper uses this
                'dateFrom' => $input['dateFrom'] ?? null, // Optional: start date for historical posts
                'dateTo' => $input['dateTo'] ?? null, // Optional: end date for historical posts
                'extendOutputFunction' => $input['extendOutputFunction'] ?? null,
                'extendScraperFunction' => $input['extendScraperFunction'] ?? null,
                'proxyConfiguration' => $input['proxyConfiguration'] ?? null,
            ], function($value) {
                return $value !== null;
            });

            Log::info('Starting Apify actor run', [
                'actor_id' => $actorId,
                'normalized_id' => $normalizedActorId,
                'endpoint' => "{$this->apifyBaseUrl}/acts/{$normalizedActorId}/runs"
            ]);

            $response = Http::timeout(30)
                ->withOptions([
                'verify' => $this->sslVerify,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apifyBaseUrl}/acts/{$normalizedActorId}/runs", $requestBody);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Apify actor start failed', [
                    'actor_id' => $actorId,
                    'normalized_id' => $normalizedActorId ?? $actorId,
                    'error' => $error,
                    'status' => $response->status(),
                    'endpoint_tried' => $response->status() === 404 ? 'both /actor-runs and /acts/{id}/runs' : 'primary endpoint'
                ]);

                $errorMessage = $error['error']['message'] ?? 'Failed to start Apify actor';
                
                // Add helpful message for 404 errors
                if ($response->status() === 404) {
                    $errorMessage .= '. The actor ID "' . $actorId . '" may not exist or may be incorrect. ';
                    $errorMessage .= 'Please verify the actor ID in your Apify console or check config/services.php.';
                        }
                        
                $apifyResponseTime = round(microtime(true) - $apifyStartTime, 4);
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'error_code' => $error['error']['code'] ?? $response->status(),
                    'actor_id' => $actorId,
                    'suggestions' => $response->status() === 404 ? [
                        'Verify the actor ID is correct in config/services.php',
                        'Check the Apify console to find the correct actor ID',
                        'The actor ID format should be: username~actor-name (e.g., apify~facebook-posts-scraper)',
                        'Visit https://console.apify.com/actors to find available actors'
                    ] : [],
                    'apify_usage' => [
                        'platform' => $platform,
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            $runData = $response->json();
            $runId = $runData['data']['id'] ?? null;

            if (!$runId) {
                $apifyResponseTime = round(microtime(true) - $apifyStartTime, 4);
                return [
                    'success' => false,
                    'error' => 'Failed to get run ID from Apify',
                    'apify_usage' => [
                        'platform' => $platform,
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            Log::info('Apify actor run started', [
                'actor_id' => $actorId,
                'run_id' => $runId
            ]);

            // Wait for the run to finish
            // Use the calculated timeout (matches execution time limit) or config timeout, whichever is higher
            // Add a buffer (60 seconds) to account for status check delays and network latency
            // For Instagram, we need extra time as it can take 10-15 minutes
            $waitTimeout = max($timeout, $this->timeout) + 60;
            
            if ($waitForFinish) {
                $result = $this->waitForRunCompletion($runId, $token, $waitTimeout, $actorId, $apifyStartTime, $platform, $input);
                // Calculate cost if not already set
                if (isset($result['apify_usage']) && ($result['apify_usage']['cost'] == 0.00 || !isset($result['apify_usage']['cost'])) && $result['success']) {
                    $result['apify_usage']['cost'] = $this->calculateApifyCost($platform, $input);
                }
                return $result;
            }
            
            $apifyResponseTime = round(microtime(true) - $apifyStartTime, 4);
            return [
                'success' => true,
                'run_id' => $runId,
                'status' => 'RUNNING',
                'apify_usage' => [
                    'platform' => $platform,
                    'success' => true,
                    'response_time' => $apifyResponseTime,
                    'cost' => $this->calculateApifyCost($platform, $input)
                ]
            ];
            
        } catch (Exception $e) {
            $apifyResponseTime = round(microtime(true) - $apifyStartTime, 4);
            Log::error('Apify actor run exception', [
                'actor_id' => $actorId,
                'exception' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'apify_usage' => [
                    'platform' => $platform,
                    'success' => false,
                    'response_time' => $apifyResponseTime,
                    'cost' => 0.00
                ]
            ];
        }
    }
    
    /**
     * Get platform name from actor ID
     */
    protected function getPlatformFromActorId($actorId)
    {
        $actorIdLower = strtolower($actorId);
        if (strpos($actorIdLower, 'facebook') !== false) {
            return 'facebook';
        } elseif (strpos($actorIdLower, 'instagram') !== false) {
            return 'instagram';
        } elseif (strpos($actorIdLower, 'tiktok') !== false) {
            return 'tiktok';
        } elseif (strpos($actorIdLower, 'twitter') !== false || strpos($actorIdLower, 'tweet') !== false) {
            return 'twitter';
        }
        return 'unknown';
    }
    
    /**
     * Calculate estimated Apify cost based on platform and input
     * Note: This is an estimate. Actual costs depend on Apify pricing
     */
    protected function calculateApifyCost($platform, $input)
    {
        // Base cost estimates per platform (these are approximate and should be adjusted based on actual Apify pricing)
        $baseCosts = [
            'facebook' => 0.10,  // $0.10 per run
            'instagram' => 0.15, // $0.15 per run
            'tiktok' => 0.12,    // $0.12 per run
            'twitter' => 0.10,   // $0.10 per run
        ];
        
        $baseCost = $baseCosts[$platform] ?? 0.10;
        
        // Add cost based on maxPosts/maxItems (more data = more cost)
        $maxItems = $input['maxPosts'] ?? $input['maxItems'] ?? 10;
        if ($maxItems > 1000) {
            $baseCost *= 1.5; // 50% more for large requests
        } elseif ($maxItems > 100) {
            $baseCost *= 1.2; // 20% more for medium requests
        }
        
        return round($baseCost, 6);
    }
    
    /**
     * Wait for Apify run to complete and get results
     */
    protected function waitForRunCompletion($runId, $token, $maxWaitTime = 180, $actorId = null, $apifyStartTime = null, $platform = null, $input = [])
    {
        $loopStartTime = time();
        $checkInterval = 5; // Check every 5 seconds
        
        // Extend execution time limit to ensure we can wait for the full duration
        // Add extra buffer (5 minutes) to account for the wait time
        $currentTimeLimit = ini_get('max_execution_time');
        $requiredTimeLimit = $maxWaitTime + 300; // maxWaitTime + 5 minute buffer
        if ($currentTimeLimit > 0 && $currentTimeLimit < $requiredTimeLimit) {
            set_time_limit($requiredTimeLimit);
            Log::info('Extended execution time limit for Apify wait', [
                'run_id' => $runId,
                'previous_limit' => $currentTimeLimit,
                'new_limit' => $requiredTimeLimit,
                'max_wait_time' => $maxWaitTime
            ]);
        }

        while (true) {
            $elapsedTime = time() - $loopStartTime;
            
            if ($elapsedTime > $maxWaitTime) {
                Log::warning('Apify actor run approaching timeout - checking final status', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'elapsed_time' => $elapsedTime,
                    'max_wait_time' => $maxWaitTime,
                    'remaining_execution_time' => ini_get('max_execution_time'),
                    'platform' => $platform
                ]);
                
                // Check final status before timing out to provide better error message
                $statusResponse = Http::timeout(60)
                    ->withOptions([
                    'verify' => $this->sslVerify,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                        CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                    ]
                    ])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                    ])
                    ->get("{$this->apifyBaseUrl}/actor-runs/{$runId}");

                $finalStatus = 'UNKNOWN';
                if ($statusResponse->successful()) {
                    $statusData = $statusResponse->json();
                    $finalStatus = $statusData['data']['status'] ?? 'UNKNOWN';
                }

                $apifyResponseTime = $apifyStartTime ? round(microtime(true) - $apifyStartTime, 4) : $maxWaitTime;
                Log::warning('Apify actor run timeout', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'max_wait_time' => $maxWaitTime,
                    'elapsed_time' => $elapsedTime,
                    'final_status' => $finalStatus,
                    'suggestion' => $finalStatus === 'RUNNING' ? 'Run is still in progress. Consider increasing timeout or checking run status later via Apify console.' : 'Run may have failed or is taking longer than expected.'
                ]);

                // If actor is still running, return a special status indicating it's processing
                // This prevents the frontend from showing "not found" when the actor is still working
                if ($finalStatus === 'RUNNING') {
                    return [
                        'success' => false,
                        'status' => 'processing',
                        'message' => 'Instagram scraping is still in progress. This may take several more minutes.',
                        'error' => 'Apify actor run is still processing after ' . $maxWaitTime . ' seconds',
                        'final_status' => $finalStatus,
                        'run_id' => $runId,
                        'apify_usage' => [
                            'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                            'success' => false,
                            'response_time' => $apifyResponseTime,
                            'cost' => 0.00
                        ]
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Apify actor run timed out after ' . $maxWaitTime . ' seconds',
                    'final_status' => $finalStatus,
                    'run_id' => $runId,
                    'apify_usage' => [
                        'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            // Check run status
            $statusResponse = Http::timeout(60)
                ->withOptions([
                'verify' => $this->sslVerify,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->apifyBaseUrl}/actor-runs/{$runId}");

            if (!$statusResponse->successful()) {
                $apifyResponseTime = $apifyStartTime ? round(microtime(true) - $apifyStartTime, 4) : 0;
                Log::error('Failed to check Apify run status', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'status_code' => $statusResponse->status(),
                    'response' => $statusResponse->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to check run status',
                    'apify_usage' => [
                        'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            $statusData = $statusResponse->json();
            $status = $statusData['data']['status'] ?? null;
            
            // Log status changes
            if ($status === 'SUCCEEDED' || $status === 'FAILED' || $status === 'ABORTED') {
                Log::info('Apify actor run status changed', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'status' => $status,
                    'elapsed_time' => time() - $loopStartTime,
                    'status_data' => json_encode($statusData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
            }

            if ($status === 'SUCCEEDED') {
                // Get the dataset items
                $datasetResponse = Http::timeout(60)
                    ->withOptions([
                'verify' => $this->sslVerify,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                    CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                ]
                    ])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                    ])
                    ->get("{$this->apifyBaseUrl}/datasets/{$statusData['data']['defaultDatasetId']}/items");

                if ($datasetResponse->successful()) {
                    $items = $datasetResponse->json();
                    
                    // Calculate total response time
                    $apifyResponseTime = $apifyStartTime ? round(microtime(true) - $apifyStartTime, 4) : 0;
                    
                    $itemsCount = is_array($items) ? count($items) : 0;
                    
                    // Log the full raw response from Apify API
                    Log::info('Apify dataset items retrieved - FULL RESPONSE', [
                        'run_id' => $runId,
                        'actor_id' => $actorId,
                        'items_count' => $itemsCount,
                        'is_empty' => empty($items),
                        'is_array' => is_array($items),
                        'full_response' => json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'first_item_structure' => !empty($items[0]) && is_array($items[0]) ? array_keys($items[0]) : [],
                        'first_item_full' => !empty($items[0]) ? json_encode($items[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                        'has_errors' => !empty($items[0]) && is_array($items[0]) && (isset($items[0]['error']) || isset($items[0]['errorDescription']))
                    ]);
                    
                    // Log warning if dataset is empty
                    if (empty($items) || $itemsCount === 0) {
                        Log::warning('Apify dataset is empty after successful run', [
                            'run_id' => $runId,
                            'actor_id' => $actorId,
                            'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                            'elapsed_time' => $apifyResponseTime
                        ]);
                    }
                    
                    // Calculate cost based on platform and input
                    $calculatedPlatform = $platform ?? $this->getPlatformFromActorId($actorId ?? '');
                    $calculatedCost = $this->calculateApifyCost($calculatedPlatform, $input);
                    
                    return [
                        'success' => true,
                        'data' => $items,
                        'run_id' => $runId,
                        'apify_usage' => [
                            'platform' => $calculatedPlatform,
                            'success' => true,
                            'response_time' => $apifyResponseTime,
                            'cost' => $calculatedCost
                        ]
                    ];
                } else {
                    $apifyResponseTime = $apifyStartTime ? round(microtime(true) - $apifyStartTime, 4) : 0;
                    Log::error('Failed to fetch dataset items', [
                        'run_id' => $runId,
                        'status' => $datasetResponse->status(),
                        'response' => $datasetResponse->body()
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Failed to fetch dataset items',
                        'apify_usage' => [
                            'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                            'success' => false,
                            'response_time' => $apifyResponseTime,
                            'cost' => 0.00
                        ]
                    ];
                }
            } elseif ($status === 'FAILED' || $status === 'ABORTED') {
                $apifyResponseTime = $apifyStartTime ? round(microtime(true) - $apifyStartTime, 4) : 0;
                Log::error('Apify actor run failed or aborted', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'status' => $status,
                    'elapsed_time' => $apifyStartTime ? time() - $apifyStartTime : 0
                ]);
                return [
                    'success' => false,
                    'error' => 'Apify actor run ' . strtolower($status),
                    'status' => $status,
                    'apify_usage' => [
                        'platform' => $platform ?? $this->getPlatformFromActorId($actorId ?? ''),
                        'success' => false,
                        'response_time' => $apifyResponseTime,
                        'cost' => 0.00
                    ]
                ];
            }

            // Log progress every 30 seconds for long-running runs
            $elapsedTime = time() - $loopStartTime;
            if ($elapsedTime > 0 && $elapsedTime % 30 < $checkInterval) {
                Log::info('Apify actor run in progress', [
                    'run_id' => $runId,
                    'actor_id' => $actorId,
                    'status' => $status,
                    'elapsed_time' => $elapsedTime,
                    'max_wait_time' => $maxWaitTime,
                    'remaining_time' => $maxWaitTime - $elapsedTime
                ]);
            }

            // Wait before next check
            sleep($checkInterval);
        }
    }
    
    /**
     * Check status of a running Apify actor and get results if completed
     * This is useful for checking long-running Instagram scrapes
     */
    public function checkApifyRunStatus($runId, $platform = 'instagram')
    {
        try {
            $token = $this->getApiToken();
            if (!$token) {
                return [
                    'success' => false,
                    'error' => 'Apify API token not configured'
                ];
            }

            // Check run status
            $statusResponse = Http::timeout(60)
                ->withOptions([
                    'verify' => $this->sslVerify,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                        CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                    ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->apifyBaseUrl}/actor-runs/{$runId}");

            if (!$statusResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to check run status',
                    'status_code' => $statusResponse->status()
                ];
            }

            $statusData = $statusResponse->json();
            $status = $statusData['data']['status'] ?? null;
            $actorId = $statusData['data']['actId'] ?? null;

            if ($status === 'SUCCEEDED') {
                // Get the dataset items
                $datasetResponse = Http::timeout(60)
                    ->withOptions([
                        'verify' => $this->sslVerify,
                        'curl' => [
                            CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                            CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
                        ]
                    ])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                    ])
                    ->get("{$this->apifyBaseUrl}/datasets/{$statusData['data']['defaultDatasetId']}/items");

                if ($datasetResponse->successful()) {
                    $items = $datasetResponse->json();
                    
                    // Format the data based on platform
                    if ($platform === 'instagram') {
                        $formattedData = $this->formatApifyInstagramData($items);
                        return [
                            'success' => true,
                            'status' => 'completed',
                            'data' => $formattedData,
                            'run_id' => $runId
                        ];
                    }
                    
                    return [
                        'success' => true,
                        'status' => 'completed',
                        'data' => $items,
                        'run_id' => $runId
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Failed to fetch dataset items',
                        'status' => $status
                    ];
                }
            } elseif ($status === 'FAILED' || $status === 'ABORTED') {
                return [
                    'success' => false,
                    'error' => 'Apify actor run ' . strtolower($status),
                    'status' => $status
                ];
            } else {
                // Still running
                return [
                    'success' => false,
                    'status' => 'processing',
                    'message' => 'Instagram scraping is still in progress',
                    'run_id' => $runId
                ];
            }
        } catch (\Exception $e) {
            Log::error('Check Apify run status exception', [
                'run_id' => $runId,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get Facebook page or profile information using Apify
     * Works with both Facebook Pages and personal profiles (if public)
     */
    public function getFacebookPageInfo($identifier, $isUsername = false)
    {
        try {
            // Clean identifier
            $cleanIdentifier = trim($identifier);
            $cleanIdentifier = preg_replace('/^@/', '', $cleanIdentifier);
            $cleanIdentifier = preg_replace('/^https?:\/\/(www\.)?facebook\.com\//', '', $cleanIdentifier);
            $cleanIdentifier = preg_replace('/^m\.facebook\.com\//', '', $cleanIdentifier); // Handle mobile URLs
            $cleanIdentifier = trim($cleanIdentifier, '/');

            // Build Facebook URL - works for both pages and profiles
            // Examples:
            // - Pages: https://www.facebook.com/yourpage
            // - Profiles: https://www.facebook.com/username or https://www.facebook.com/profile.php?id=123456
            $facebookUrl = "https://www.facebook.com/{$cleanIdentifier}";

            // Facebook Posts Scraper input format
            // Works for both Pages and personal profiles (if public)
            // To fetch ALL posts, we set a very high limit and let the scraper fetch everything
            $input = [
                'startUrls' => [
                    ['url' => $facebookUrl]
                ],
                'maxPosts' => 10000,  // Very high limit to fetch all available posts (not just recent)
                'maxComments' => 5, // Optional: limit comments per post
                // Note: The scraper will fetch posts chronologically, but with high maxPosts it should get all available
            ];
            
            Log::info('Scraping Facebook account', [
                'identifier' => $identifier,
                'cleaned_identifier' => $cleanIdentifier,
                'facebook_url' => $facebookUrl,
                'max_posts' => 10000,
                'note' => 'Fetching ALL available posts (not just recent) - up to 10,000 posts. Works for both Pages and personal profiles (if public)'
            ]);

            $result = $this->runApifyActor($this->facebookActorId, $input);
            
            // Log raw Facebook API response
            Log::info('Facebook API raw response', [
                'identifier' => $identifier,
                'success' => $result['success'] ?? false,
                'raw_data' => isset($result['data']) ? json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'error' => $result['error'] ?? null,
                'data_count' => isset($result['data']) && is_array($result['data']) ? count($result['data']) : 0,
                'sample_keys' => isset($result['data'][0]) && is_array($result['data'][0]) ? array_keys($result['data'][0]) : []
            ]);

            if ($result['success']) {
                $data = $this->formatApifyFacebookData($result['data'], $cleanIdentifier);
                
                // Check if formatting returned an error (account doesn't exist)
                if (isset($data['error'])) {
                    Log::info('Facebook account not found', [
                        'identifier' => $identifier,
                        'error' => $data['error']
                    ]);
                    return [
                        'success' => false,
                        'error' => $data['error'] ?? 'Account not found or not accessible',
                        'apify_usage' => $result['apify_usage'] ?? null
                    ];
                }
                
                // Log formatted Facebook data
                Log::info('Facebook formatted data', [
                    'identifier' => $identifier,
                    'formatted_data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
                
            return [
                    'success' => true,
                    'platform' => 'facebook',
                    'data' => $data,
                    'apify_usage' => $result['apify_usage'] ?? null
                ];
            }

            return $result;
            
        } catch (Exception $e) {
            Log::error('Facebook page info exception', [
                'identifier' => $identifier,
                'exception' => $e->getMessage()
            ]);

            // If we have a result with apify_usage, preserve it even on exception
            $apifyUsage = null;
            if (isset($result) && isset($result['apify_usage'])) {
                $apifyUsage = $result['apify_usage'];
            }

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'apify_usage' => $apifyUsage
            ];
        }
    }
    
    /**
     * Search for Facebook page by username
     */
    public function searchFacebookPageByUsername($username)
    {
        return $this->getFacebookPageInfo($username, true);
    }

    /**
     * Get Instagram account information using Apify
     */
    public function getInstagramAccountInfo($identifier, $isUsername = false)
    {
        try {
            // Clean identifier
            $originalIdentifier = trim($identifier);
            
            // Check if it's already a full URL (profile or post)
            $isUrl = preg_match('/^https?:\/\/(www\.)?instagram\.com\//', $originalIdentifier);
            
            if ($isUrl) {
                // It's already a URL, use it as-is
                $inputValue = $originalIdentifier;
                $cleanIdentifier = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//', '', $originalIdentifier);
                $cleanIdentifier = trim($cleanIdentifier, '/');
                } else {
                // It's a username, clean it and use as-is
                $cleanIdentifier = preg_replace('/^@/', '', $originalIdentifier);
                $cleanIdentifier = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//', '', $cleanIdentifier);
                $cleanIdentifier = trim($cleanIdentifier, '/');
                $inputValue = $cleanIdentifier; // Just the username
            }

            // Build Instagram URL for logging
            $instagramUrl = $isUrl ? $inputValue : "https://www.instagram.com/{$cleanIdentifier}/";

            Log::info('Scraping Instagram account', [
                'identifier' => $identifier,
                'cleaned_identifier' => $cleanIdentifier,
                'is_url' => $isUrl,
                'instagram_url' => $instagramUrl,
                'actor_id' => $this->instagramActorId
            ]);

            // Try different input formats based on actor type
            // apify/instagram-post-scraper accepts: username, profile URL, or post URL
            // apify/instagram-profile-scraper uses 'usernames' array
            // apify/instagram-scraper uses 'startUrls'
            if (strpos($this->instagramActorId, 'instagram-post-scraper') !== false) {
                // Instagram Post Scraper format - accepts username, profile URL, or post URL
                // The 'username' field must be an array (can contain username, profile URL, or post URL)
                $input = [
                    'username' => [$inputValue], // Must be an array - can contain username, profile URL, or post URL
                    'maxItems' => 10, // Maximum number of posts to retrieve
                ];
            } elseif (strpos($this->instagramActorId, 'instagram-profile-scraper') !== false) {
                // Instagram Profile Scraper format
                $input = [
                    'usernames' => [$cleanIdentifier], // Just the username, no @ symbol
                    'resultsLimit' => 10,
                ];
            } else {
                // Instagram Scraper format (original)
                $input = [
                    'startUrls' => [
                        ['url' => $instagramUrl]
                    ],
                    'resultsLimit' => 10,
                    'addParentData' => true, // Include parent profile data with posts
                ];
            }

            $result = $this->runApifyActor($this->instagramActorId, $input);
            
            // Log raw Instagram API response
            Log::info('Instagram API raw response', [
                    'identifier' => $identifier,
                'username' => $cleanIdentifier,
                'input_value' => $inputValue,
                'success' => $result['success'] ?? false,
                'raw_data' => isset($result['data']) ? json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'error' => $result['error'] ?? null,
                'error_code' => $result['error_code'] ?? null,
                'has_data' => isset($result['data']) && !empty($result['data'])
            ]);

            // Check if the result indicates the actor is still processing
            if (isset($result['status']) && $result['status'] === 'processing') {
                Log::info('Instagram actor still processing', [
                    'identifier' => $identifier,
                    'run_id' => $result['run_id'] ?? null,
                    'message' => $result['message'] ?? null
                ]);
                return [
                    'success' => false,
                    'status' => 'processing',
                    'platform' => 'instagram',
                    'message' => $result['message'] ?? 'Instagram scraping is still in progress. This may take several more minutes.',
                    'run_id' => $result['run_id'] ?? null,
                    'error' => $result['error'] ?? 'Processing',
                    'apify_usage' => $result['apify_usage'] ?? null
                ];
            }

            if ($result['success']) {
                $data = $this->formatApifyInstagramData($result['data']);
                
                // Log formatted Instagram data
                Log::info('Instagram formatted data', [
                    'identifier' => $identifier,
                    'formatted_data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
                
                // Check if the formatted data contains an error
                if (isset($data['error'])) {
                    return [
                        'success' => false,
                        'platform' => 'instagram',
                        'error' => $data['error'],
                        'error_code' => $data['error_code'] ?? null,
                        'suggestion' => 'The account might be private, or Instagram is blocking the scraper. Please ensure the account is public and try again.',
                        'apify_usage' => $result['apify_usage'] ?? null
                    ];
                }

                return [
                    'success' => true,
                    'platform' => 'instagram',
                    'data' => $data,
                    'apify_usage' => $result['apify_usage'] ?? null
                ];
            }

            return $result;

        } catch (Exception $e) {
            Log::error('Instagram account info exception', [
                'identifier' => $identifier,
                'exception' => $e->getMessage()
            ]);

            // If we have a result with apify_usage, preserve it even on exception
            $apifyUsage = null;
            if (isset($result) && isset($result['apify_usage'])) {
                $apifyUsage = $result['apify_usage'];
            }

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'apify_usage' => $apifyUsage
            ];
        }
    }

    /**
     * Search for Instagram account by username
     */
    public function searchInstagramAccountByUsername($username)
    {
        return $this->getInstagramAccountInfo($username, true);
    }

    /**
     * Get Instagram account from Facebook Page (not applicable with Apify, but kept for compatibility)
     */
    public function getInstagramAccountFromFacebookPage($pageId)
    {
        // With Apify, we can't directly get Instagram from Facebook Page
        // Return error suggesting to search Instagram directly
                return [
                    'success' => false,
            'error' => 'To get Instagram account, please search by Instagram username directly. Apify scrapes Instagram independently of Facebook.'
        ];
    }

    /**
     * Format Apify Facebook Posts Scraper data
     */
    protected function formatApifyFacebookData($items, $identifier = null)
    {
        if (empty($items) || !is_array($items)) {
            return [
                'error' => 'No data returned from Apify'
            ];
        }

        // Check for error messages indicating account doesn't exist or isn't accessible
        // Only return error if ALL items are errors with specific "not available" messages
        $errorItems = [];
        $validItems = [];
        
        foreach ($items as $item) {
            if (isset($item['error']) || isset($item['errorDescription'])) {
                $error = $item['error'] ?? '';
                $errorDescription = $item['errorDescription'] ?? '';
                
                // Check for common error messages indicating account doesn't exist
                $errorLower = strtolower($errorDescription);
                if (
                    $error === 'not_available' || 
                    $error === 'no_items' ||
                    strpos($errorLower, "isn't available") !== false ||
                    strpos($errorLower, 'not available') !== false ||
                    strpos($errorLower, 'content isn\'t available') !== false ||
                    strpos($errorLower, 'only shared it with a small group') !== false ||
                    strpos($errorLower, 'changed who can see it') !== false ||
                    strpos($errorLower, 'been deleted') !== false
                ) {
                    $errorItems[] = $item;
                } else {
                    // Other errors might not be fatal, keep the item
                    $validItems[] = $item;
                }
            } else {
                // No error, valid item
                $validItems[] = $item;
            }
        }

        // Only return error if ALL items are "not available" errors
        if (count($errorItems) > 0 && count($validItems) === 0) {
            Log::info('Facebook account not accessible - all items are errors', [
                'identifier' => $identifier,
                'error_items_count' => count($errorItems)
            ]);
            return [
                'error' => 'Account not found or not accessible',
                'error_code' => $errorItems[0]['error'] ?? null
            ];
        }

        // Use valid items (filter out only the "not available" errors)
        $items = $validItems;
        
        // If no valid items after filtering, account doesn't exist
        if (empty($items)) {
            Log::info('Facebook scraper returned only error items', [
                'identifier' => $identifier
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }

        // Facebook Posts Scraper returns posts directly
        // Try to extract page info from first post or separate it
        $pageData = [];
        $posts = [];

        foreach ($items as $item) {
            // Check if this is page/profile info or a post
            if (isset($item['type']) && $item['type'] === 'Profile') {
                $pageData = $item;
            } elseif (isset($item['type']) && $item['type'] === 'Post') {
                $posts[] = $item;
            } elseif (isset($item['postId']) || isset($item['post_id']) || isset($item['text'])) {
                // This looks like a post
                $posts[] = $item;
                } else {
                // Might be page data
                if (empty($pageData)) {
                    $pageData = $item;
                }
            }
        }

        // If no posts found, assume all items are posts
        if (empty($posts) && !empty($items)) {
            $posts = $items;
        }

        // Extract page info from first post if available
        $firstPost = $posts[0] ?? [];
        $pageName = $pageData['name'] ?? $firstPost['pageName'] ?? $firstPost['authorName'] ?? null;
        
        // Get username to construct profile URL
        $username = $pageData['username'] ?? $pageData['handle'] ?? null;
        
        // Construct profile URL - prefer constructed URL over post URLs
        // Check if pageUrl is a profile URL (not a post/reel URL)
        $pageUrl = $pageData['url'] ?? null;
        if ($pageUrl && (strpos($pageUrl, '/reel/') !== false || strpos($pageUrl, '/posts/') !== false || strpos($pageUrl, '/videos/') !== false || strpos($pageUrl, '/photo') !== false || strpos($pageUrl, '/watch') !== false)) {
            // This is a post/reel/video URL, not a profile URL - construct profile URL from username or identifier
            $pageUrl = null;
        }
        
        // If no valid profile URL, construct from username or identifier
        if (!$pageUrl && $username) {
            $pageUrl = "https://www.facebook.com/{$username}";
        } elseif (!$pageUrl && !empty($identifier)) {
            $pageUrl = "https://www.facebook.com/{$identifier}";
        }

        // Validate that we have actual account data
        // Only check if we have posts OR page data (more lenient validation)
        $hasValidData = false;
        
        // If we have any posts, consider it valid (even if they don't have text/message)
        if (!empty($posts)) {
            $hasValidData = true;
        }
        
        // Also check if we have page/profile data with name or ID
        if (!$hasValidData && !empty($pageData)) {
            if (isset($pageData['name']) || isset($pageData['id']) || isset($pageData['pageId']) || isset($pageData['url'])) {
                $hasValidData = true;
            }
        }
        
        // Also check if we have a page name from posts
        if (!$hasValidData && !empty($pageName)) {
            $hasValidData = true;
        }
        
        // Only return error if we truly have no data at all
        if (!$hasValidData && empty($posts) && empty($pageData)) {
            Log::info('Facebook account has no valid data', [
                'identifier' => $identifier,
                'posts_count' => count($posts),
                'has_page_data' => !empty($pageData),
                'page_name' => $pageName
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }

        // Format posts
        $formattedPosts = $this->formatApifyFacebookPosts($posts);
        
        // Get follower count - try multiple sources
        $followersCount = $pageData['followersCount'] 
            ?? $pageData['fanCount'] 
            ?? $pageData['likes'] 
            ?? $firstPost['pageFollowersCount'] 
            ?? $firstPost['authorFollowersCount']
            ?? $firstPost['followersCount']
            ?? 0;
        
        // If still 0, try to get from any post
        if ($followersCount == 0 && !empty($posts)) {
            foreach ($posts as $post) {
                if (isset($post['pageFollowersCount']) && $post['pageFollowersCount'] > 0) {
                    $followersCount = $post['pageFollowersCount'];
                    break;
                }
                if (isset($post['authorFollowersCount']) && $post['authorFollowersCount'] > 0) {
                    $followersCount = $post['authorFollowersCount'];
                    break;
                }
            }
        }
        
        // Calculate engagement metrics from ALL posts (comprehensive analysis)
        $engagementMetrics = $this->calculateFacebookEngagementMetrics($formattedPosts, $followersCount);

        return [
            'id' => $pageData['id'] ?? $pageData['pageId'] ?? $firstPost['pageId'] ?? null,
            'name' => $pageName,
            'username' => $username,
            'about' => $pageData['about'] ?? $pageData['description'] ?? null,
            'description' => $pageData['description'] ?? $pageData['about'] ?? null,
            'category' => $pageData['category'] ?? null,
            'fan_count' => $pageData['fanCount'] ?? $pageData['likes'] ?? 0,
            'followers_count' => $followersCount,
            'phone' => $pageData['phone'] ?? null,
            'website' => $pageData['website'] ?? null,
            'location' => $pageData['location'] ?? null,
            'link' => $pageUrl,
            'profile_url' => $pageUrl,
            'cover_photo' => $pageData['coverPhoto'] ?? $pageData['cover'] ?? null,
            'profile_picture' => $pageData['profilePicture'] ?? $pageData['picture'] ?? $firstPost['pagePicture'] ?? null,
            'recent_posts' => $formattedPosts,
            'engagement' => $engagementMetrics,
            'stats' => [
                'total_fans' => $pageData['fanCount'] ?? $pageData['likes'] ?? 0,
                'total_followers' => $followersCount,
                'recent_posts_count' => count($posts)
            ]
        ];
    }

    /**
     * Calculate engagement analysis and valuation metrics
     */
    protected function calculateEngagementAnalysis($posts, $followersCount)
    {
        if (empty($posts) || $followersCount == 0) {
            return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_comments' => 0,
                'average_shares' => 0,
                'total_posts_analyzed' => count($posts),
                'best_post' => null,
                'post_frequency' => null,
                'valuation_score' => 0,
                'valuation_tier' => 'Low'
            ];
        }

        $totalLikes = 0;
        $totalComments = 0;
        $totalShares = 0;
        $totalEngagement = 0;
        $bestPost = null;
        $maxEngagement = 0;
        $postDates = [];

        foreach ($posts as $post) {
            $likes = (int)($post['likes'] ?? 0);
            $comments = (int)($post['comments'] ?? 0);
            $shares = (int)($post['shares'] ?? 0);
            $postEngagement = $likes + $comments + $shares;

            $totalLikes += $likes;
            $totalComments += $comments;
            $totalShares += $shares;
            $totalEngagement += $postEngagement;

            // Track best performing post
            if ($postEngagement > $maxEngagement) {
                $maxEngagement = $postEngagement;
                $bestPost = [
                    'id' => $post['id'],
                    'message' => $post['message'],
                    'likes' => $likes,
                    'comments' => $comments,
                    'shares' => $shares,
                    'total_engagement' => $postEngagement,
                    'url' => $post['url'] ?? null,
                    'created_time' => $post['created_time'] ?? null
                ];
            }

            // Track post dates for frequency calculation
            if (!empty($post['created_time'])) {
                $postDates[] = strtotime($post['created_time']);
            }
        }

        $postCount = count($posts);
        $averageLikes = $postCount > 0 ? round($totalLikes / $postCount, 2) : 0;
        $averageComments = $postCount > 0 ? round($totalComments / $postCount, 2) : 0;
        $averageShares = $postCount > 0 ? round($totalShares / $postCount, 2) : 0;
        $averageEngagement = $postCount > 0 ? round($totalEngagement / $postCount, 2) : 0;
        
        // Calculate engagement rate (percentage)
        $engagementRate = $followersCount > 0 ? round(($totalEngagement / ($followersCount * $postCount)) * 100, 2) : 0;

        // Calculate post frequency
        $postFrequency = null;
        if (count($postDates) > 1) {
            sort($postDates);
            $timeSpan = $postDates[count($postDates) - 1] - $postDates[0];
            $days = $timeSpan / (60 * 60 * 24);
            if ($days > 0) {
                $postsPerDay = round($postCount / $days, 2);
                $postsPerWeek = round($postsPerDay * 7, 2);
                $postFrequency = [
                    'posts_per_day' => $postsPerDay,
                    'posts_per_week' => $postsPerWeek,
                    'days_analyzed' => round($days, 1)
                ];
            }
        }

        // Calculate valuation score (0-100)
        // Factors: Engagement rate, average engagement, follower count, post frequency
        $valuationScore = $this->calculateValuationScore($engagementRate, $averageEngagement, $followersCount, $postFrequency);
        $valuationTier = $this->getValuationTier($valuationScore);

                        return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_comments' => $averageComments,
            'average_shares' => $averageShares,
            'total_posts_analyzed' => $postCount,
            'best_post' => $bestPost,
            'post_frequency' => $postFrequency,
            'valuation_score' => $valuationScore,
            'valuation_tier' => $valuationTier,
            'engagement_breakdown' => [
                'total_likes' => $totalLikes,
                'total_comments' => $totalComments,
                'total_shares' => $totalShares,
                'likes_percentage' => $totalEngagement > 0 ? round(($totalLikes / $totalEngagement) * 100, 2) : 0,
                'comments_percentage' => $totalEngagement > 0 ? round(($totalComments / $totalEngagement) * 100, 2) : 0,
                'shares_percentage' => $totalEngagement > 0 ? round(($totalShares / $totalEngagement) * 100, 2) : 0,
            ]
        ];
    }

    /**
     * Calculate valuation score (0-100)
     */
    protected function calculateValuationScore($engagementRate, $avgEngagement, $followers, $postFrequency)
    {
        $score = 0;

        // Engagement rate score (0-40 points)
        // Excellent: >5%, Good: 2-5%, Average: 1-2%, Low: <1%
        if ($engagementRate >= 5) {
            $score += 40;
        } elseif ($engagementRate >= 2) {
            $score += 30;
        } elseif ($engagementRate >= 1) {
            $score += 20;
        } elseif ($engagementRate > 0) {
            $score += 10;
        }

        // Average engagement score (0-25 points)
        // Based on followers: >1% of followers = excellent
        if ($followers > 0) {
            $engagementRatio = ($avgEngagement / $followers) * 100;
            if ($engagementRatio >= 1) {
                $score += 25;
            } elseif ($engagementRatio >= 0.5) {
                $score += 18;
            } elseif ($engagementRatio >= 0.2) {
                $score += 12;
            } elseif ($engagementRatio > 0) {
                $score += 6;
            }
        }

        // Follower count score (0-20 points)
        // Scale: 100k+ = 20, 50k+ = 15, 10k+ = 10, 1k+ = 5
        if ($followers >= 100000) {
            $score += 20;
        } elseif ($followers >= 50000) {
            $score += 15;
        } elseif ($followers >= 10000) {
            $score += 10;
        } elseif ($followers >= 1000) {
            $score += 5;
        }

        // Post frequency score (0-15 points)
        // Active: >5 posts/week = 15, Regular: 2-5 = 10, Occasional: 1-2 = 5
        if ($postFrequency && isset($postFrequency['posts_per_week'])) {
            $postsPerWeek = $postFrequency['posts_per_week'];
            if ($postsPerWeek >= 5) {
                $score += 15;
            } elseif ($postsPerWeek >= 2) {
                $score += 10;
            } elseif ($postsPerWeek >= 1) {
                $score += 5;
            }
        }

        return min(100, round($score, 1));
    }

    /**
     * Get valuation tier based on score
     */
    protected function getValuationTier($score)
    {
        if ($score >= 80) {
            return 'Excellent';
        } elseif ($score >= 60) {
            return 'Very Good';
        } elseif ($score >= 40) {
            return 'Good';
        } elseif ($score >= 20) {
            return 'Average';
                } else {
            return 'Low';
        }
    }

    /**
     * Format Apify Facebook posts
     */
    protected function formatApifyFacebookPosts($posts)
    {
        if (empty($posts) || !is_array($posts)) {
            return [];
        }

        return array_map(function($post) {
            $likes = (int)($post['likes'] ?? $post['likeCount'] ?? $post['reactions'] ?? 0);
            $comments = (int)($post['comments'] ?? $post['commentCount'] ?? (isset($post['commentsData']) ? count($post['commentsData']) : 0));
            $shares = (int)($post['shares'] ?? $post['shareCount'] ?? 0);
            $totalEngagement = $likes + $comments + $shares;
            
            return [
                'id' => $post['postId'] ?? $post['post_id'] ?? $post['id'] ?? null,
                'message' => $post['text'] ?? $post['message'] ?? $post['postText'] ?? null,
                'created_time' => $post['time'] ?? $post['createdTime'] ?? $post['timestamp'] ?? $post['date'] ?? null,
                'likes' => $likes,
                'comments' => $comments,
                'shares' => $shares,
                'total_engagement' => $totalEngagement,
                'url' => $post['postUrl'] ?? $post['url'] ?? null,
                'image' => $post['image'] ?? $post['imageUrl'] ?? null,
                'video' => $post['video'] ?? $post['videoUrl'] ?? null,
            ];
        }, $posts);
    }

    /**
     * Format Apify Instagram data
     * Handles data from:
     * - apify/instagram-post-scraper (returns posts with profile data)
     * - apify/instagram-profile-scraper (returns profile data)
     * - apify/instagram-scraper (returns profiles and posts)
     */
    protected function formatApifyInstagramData($items)
    {
        if (empty($items) || !is_array($items)) {
            Log::warning('Instagram scraper returned empty data', [
                'items_count' => is_array($items) ? count($items) : 0,
                'items_type' => gettype($items),
                'full_response' => json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
            return [
                'error' => 'No data returned from Apify. The username may not exist or the account may be private.',
                'error_code' => 'NO_DATA'
            ];
        }

        Log::info('Formatting Instagram data', [
            'items_count' => count($items),
            'first_item_keys' => !empty($items[0]) && is_array($items[0]) ? array_keys($items[0]) : [],
            'first_item_sample' => !empty($items[0]) ? json_encode(array_slice($items[0], 0, 5, true)) : null,
            'full_items' => json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ]);

        // Check if the response contains an error
        if (!empty($items[0]) && is_array($items[0]) && (isset($items[0]['error']) || isset($items[0]['errorDescription']))) {
            $errorMsg = $items[0]['errorDescription'] ?? $items[0]['error'] ?? 'Instagram scraper returned an error';
            $errorCode = $items[0]['error'] ?? null;
            
            // Check for specific error messages that indicate username not found
            $errorLower = strtolower($errorMsg);
            if (strpos($errorLower, 'not found') !== false || 
                strpos($errorLower, 'doesn\'t exist') !== false ||
                strpos($errorLower, 'does not exist') !== false ||
                strpos($errorLower, 'user not found') !== false ||
                strpos($errorLower, 'account not found') !== false ||
                $errorCode === 'USER_NOT_FOUND' ||
                $errorCode === 'NOT_FOUND') {
                Log::warning('Instagram username not found', [
                    'error' => $errorCode,
                    'errorDescription' => $errorMsg,
                    'full_item' => json_encode($items[0])
                ]);
                return [
                    'error' => 'Username not found. The Instagram account does not exist or the username is incorrect.',
                    'error_code' => $errorCode ?? 'USER_NOT_FOUND'
                ];
            }
            
            Log::warning('Instagram scraper returned error', [
                'error' => $errorCode,
                'errorDescription' => $errorMsg,
                'full_item' => json_encode($items[0])
            ]);
            return [
                'error' => $errorMsg,
                'error_code' => $errorCode
            ];
        }
        
        // Filter out error items and log what we're filtering
        $errorItems = [];
        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && (isset($item['error']) || isset($item['errorDescription']))) {
                $errorItems[] = $item;
            } else {
                $validItems[] = $item;
            }
        }
        
        if (!empty($errorItems)) {
            Log::info('Instagram scraper returned error items', [
                'error_items_count' => count($errorItems),
                'valid_items_count' => count($validItems),
                'error_items' => json_encode($errorItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
        }
        
        // Use only valid items
        $items = $validItems;
        
        if (empty($items)) {
            // Check if error items indicate username not found
            $usernameNotFound = false;
            foreach ($errorItems as $errorItem) {
                $errorMsg = strtolower($errorItem['errorDescription'] ?? $errorItem['error'] ?? '');
                if (strpos($errorMsg, 'not found') !== false || 
                    strpos($errorMsg, 'doesn\'t exist') !== false ||
                    strpos($errorMsg, 'does not exist') !== false ||
                    strpos($errorMsg, 'user not found') !== false ||
                    strpos($errorMsg, 'account not found') !== false) {
                    $usernameNotFound = true;
                    break;
                }
            }
            
            if ($usernameNotFound) {
                Log::warning('Instagram username not found - all items are errors indicating not found');
                return [
                    'error' => 'Username not found. The Instagram account does not exist or the username is incorrect.',
                    'error_code' => 'USER_NOT_FOUND'
                ];
            }
            
            Log::warning('Instagram scraper returned only error items', [
                'error_items' => json_encode($errorItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
            return [
                'error' => 'Instagram scraper returned no valid data. The account might be private or the scraper encountered an error.',
                'error_code' => 'NO_VALID_DATA'
            ];
        }

        // apify/instagram-scraper returns items that can be profiles or posts
        // Look for profile data first
        $profileData = null;
        $mediaItems = [];

        foreach ($items as $item) {
            // Skip error items
            if (isset($item['error']) || isset($item['errorDescription'])) {
                continue;
            }
            
            // Check if this is a post (instagram-post-scraper format - posts have ownerUsername, shortCode, type, etc.)
            if (isset($item['ownerUsername']) || isset($item['shortCode']) || isset($item['caption']) || 
                (isset($item['type']) && in_array($item['type'], ['Post', 'Media', 'GraphImage', 'GraphVideo', 'Image', 'Video', 'Carousel']))) {
                // This is a post/media item
                $mediaItems[] = $item;
                continue;
            }
            
            // Check if this is a profile/user item
            if (isset($item['type'])) {
                if ($item['type'] === 'User' || $item['type'] === 'Profile') {
                    $profileData = $item;
                    continue;
                }
            }
            
            if (isset($item['username']) || isset($item['fullName']) || isset($item['biography']) || isset($item['full_name'])) {
                // This looks like profile data (not a post with ownerUsername)
                if (!$profileData) {
                    $profileData = $item;
                }
            } elseif (isset($item['id']) && !isset($item['error'])) {
                // Might be a post or profile - check if it has post-like fields
                if (isset($item['likesCount']) || isset($item['commentsCount']) || isset($item['displayUrl'])) {
                    $mediaItems[] = $item;
                } elseif (!$profileData) {
                    $profileData = $item;
                }
            } elseif (!$profileData && !isset($item['error'])) {
                // If no profile found yet, assume first item might be profile (but not if it's an error)
                $profileData = $item;
            }
        }

        // If still no profile data, try to extract from posts (instagram-post-scraper includes owner info in posts)
        if (!$profileData && !empty($mediaItems)) {
            $firstPost = $mediaItems[0];
            // Check if post contains owner/profile information
            if (isset($firstPost['ownerUsername']) || isset($firstPost['ownerFullName'])) {
                $profileData = [
                    'username' => $firstPost['ownerUsername'] ?? null,
                    'fullName' => $firstPost['ownerFullName'] ?? $firstPost['ownerName'] ?? null,
                    'id' => $firstPost['ownerId'] ?? null,
                    'profilePicUrl' => $firstPost['ownerProfilePicUrl'] ?? null,
                ];
            }
        }
        
        // Also check if items contain posts with owner info (instagram-post-scraper format)
        if (!$profileData && !empty($items)) {
            foreach ($items as $item) {
                if (isset($item['ownerUsername']) || isset($item['ownerFullName'])) {
                    $profileData = [
                        'username' => $item['ownerUsername'] ?? null,
                        'fullName' => $item['ownerFullName'] ?? $item['ownerName'] ?? null,
                        'id' => $item['ownerId'] ?? null,
                        'profilePicUrl' => $item['ownerProfilePicUrl'] ?? null,
                    ];
                    break;
                }
            }
        }
        
        // If still no profile data, use first item
        if (!$profileData && !empty($items)) {
            $profileData = $items[0];
        }

        $formattedMedia = $this->formatApifyInstagramMedia($mediaItems);
        
        $followersCount = $profileData['followersCount'] ?? $profileData['followers'] ?? $profileData['edge_followed_by']['count'] ?? $profileData['ownerFollowersCount'] ?? 0;
        
        // Calculate engagement metrics from posts
        $engagementMetrics = $this->calculateInstagramEngagementMetrics($formattedMedia, $followersCount);

        Log::info('Formatted Instagram data', [
            'has_profile' => !empty($profileData),
            'media_count' => count($mediaItems),
            'followers_count' => $followersCount,
            'username' => $profileData['username'] ?? 'N/A'
        ]);
                
                $username = $profileData['username'] ?? $profileData['ownerUsername'] ?? (!empty($mediaItems) ? ($mediaItems[0]['ownerUsername'] ?? null) : null);
                $profileUrl = $username ? "https://www.instagram.com/{$username}/" : null;
                
                return [
            'id' => $profileData['id'] ?? $profileData['userId'] ?? $profileData['pk'] ?? null,
            'username' => $username,
            'name' => $profileData['fullName'] ?? $profileData['name'] ?? $profileData['full_name'] ?? null,
            'account_type' => isset($profileData['isBusinessAccount']) && $profileData['isBusinessAccount'] ? 'BUSINESS' : (isset($profileData['is_verified']) && $profileData['is_verified'] ? 'VERIFIED' : 'PERSONAL'),
            'biography' => $profileData['biography'] ?? $profileData['bio'] ?? $profileData['biography'] ?? null,
            'website' => $profileData['website'] ?? $profileData['external_url'] ?? null,
            'profile_picture' => $profileData['profilePicUrl'] ?? $profileData['profilePicture'] ?? $profileData['profile_pic_url'] ?? $profileData['profile_pic_url_hd'] ?? null,
            'profile_url' => $profileUrl,
            'followers_count' => $followersCount,
            'media_count' => $profileData['mediaCount'] ?? $profileData['postsCount'] ?? $profileData['edge_owner_to_timeline_media']['count'] ?? $profileData['ownerMediaCount'] ?? count($mediaItems),
            'recent_media' => $formattedMedia,
            'stats' => [
                'total_followers' => $followersCount,
                'total_media' => $profileData['mediaCount'] ?? $profileData['postsCount'] ?? $profileData['edge_owner_to_timeline_media']['count'] ?? $profileData['ownerMediaCount'] ?? count($mediaItems),
                'recent_media_count' => count($mediaItems)
            ],
            'engagement' => $engagementMetrics,
            'best_posts' => $this->getBestPerformingPosts($formattedMedia, 10)
        ];
    }
    
    /**
     * Calculate Instagram engagement metrics
     */
    protected function calculateInstagramEngagementMetrics($media, $followersCount)
    {
        if (empty($media)) {
                return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_comments' => 0,
                'total_posts_analyzed' => 0,
                'total_likes' => 0,
                'total_comments' => 0,
                'post_frequency' => 0
            ];
        }

        $totalLikes = 0;
        $totalComments = 0;
        $totalEngagement = 0;
        $postCount = count($media);

        foreach ($media as $item) {
            // Ensure non-negative values (match Facebook's approach)
            $likes = max(0, (int)($item['like_count'] ?? 0));
            $comments = max(0, (int)($item['comments_count'] ?? 0));
            $totalLikes += $likes;
            $totalComments += $comments;
            $totalEngagement += ($likes + $comments);
        }

        $averageLikes = $postCount > 0 ? round($totalLikes / $postCount, 2) : 0;
        $averageComments = $postCount > 0 ? round($totalComments / $postCount, 2) : 0;
        $averageEngagement = $postCount > 0 ? round($totalEngagement / $postCount, 2) : 0;
        
        // Calculate engagement rate: (average engagement per post / followers) * 100
        // This gives the percentage of followers who engage with each post on average
        // Match Facebook's calculation formula
        $engagementRate = ($followersCount > 0 && $averageEngagement > 0) 
            ? round(($averageEngagement / $followersCount) * 100, 2) 
            : 0;

            return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_comments' => $averageComments,
            'total_posts_analyzed' => $postCount,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'post_frequency' => $postCount // Posts per analysis period
        ];
    }
    
    /**
     * Get best performing posts
     */
    protected function getBestPerformingPosts($media, $limit = 10)
    {
        if (empty($media)) {
            return [];
        }

        // Sort by total engagement (likes + comments)
        usort($media, function($a, $b) {
            $engagementA = ($a['like_count'] ?? 0) + ($a['comments_count'] ?? 0);
            $engagementB = ($b['like_count'] ?? 0) + ($b['comments_count'] ?? 0);
            return $engagementB <=> $engagementA; // Descending order
        });

        return array_slice($media, 0, $limit);
    }
    
    /**
     * Calculate Facebook engagement metrics
     */
    protected function calculateFacebookEngagementMetrics($posts, $followersCount)
    {
        if (empty($posts)) {
            return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_comments' => 0,
                'average_shares' => 0,
                'total_posts_analyzed' => 0,
                'total_likes' => 0,
                'total_comments' => 0,
                'total_shares' => 0,
                'post_frequency' => 0
            ];
        }

        $totalLikes = 0;
        $totalComments = 0;
        $totalShares = 0;
        $totalEngagement = 0;
        $postCount = count($posts);

        foreach ($posts as $post) {
            $likes = (int)($post['likes'] ?? 0);
            $comments = (int)($post['comments'] ?? 0);
            $shares = (int)($post['shares'] ?? 0);
            $totalLikes += $likes;
            $totalComments += $comments;
            $totalShares += $shares;
            $totalEngagement += ($likes + $comments + $shares);
        }

        $averageLikes = $postCount > 0 ? round($totalLikes / $postCount, 2) : 0;
        $averageComments = $postCount > 0 ? round($totalComments / $postCount, 2) : 0;
        $averageShares = $postCount > 0 ? round($totalShares / $postCount, 2) : 0;
        $averageEngagement = $postCount > 0 ? round($totalEngagement / $postCount, 2) : 0;
        
        // Calculate engagement rate: (average engagement per post / followers) * 100
        // This gives the percentage of followers who engage with each post on average
        $engagementRate = ($followersCount > 0 && $averageEngagement > 0) 
            ? round(($averageEngagement / $followersCount) * 100, 2) 
            : 0;

            return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_comments' => $averageComments,
            'average_shares' => $averageShares,
            'total_posts_analyzed' => $postCount,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'total_shares' => $totalShares,
            'post_frequency' => $postCount // Posts per analysis period
        ];
    }

    /**
     * Calculate Instagram engagement analysis
     */
    protected function calculateInstagramAnalysis($media, $followersCount)
    {
        if (empty($media) || $followersCount == 0) {
            return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_comments' => 0,
                'total_posts_analyzed' => count($media),
                'valuation_score' => 0,
                'valuation_tier' => 'Low'
            ];
        }

        $totalLikes = 0;
        $totalComments = 0;
        $totalEngagement = 0;

        foreach ($media as $item) {
            $likes = (int)($item['like_count'] ?? 0);
            $comments = (int)($item['comments_count'] ?? 0);
            $totalLikes += $likes;
            $totalComments += $comments;
            $totalEngagement += ($likes + $comments);
        }

        $postCount = count($media);
        $averageLikes = $postCount > 0 ? round($totalLikes / $postCount, 2) : 0;
        $averageComments = $postCount > 0 ? round($totalComments / $postCount, 2) : 0;
        $averageEngagement = $postCount > 0 ? round($totalEngagement / $postCount, 2) : 0;
        $engagementRate = $followersCount > 0 ? round(($totalEngagement / ($followersCount * $postCount)) * 100, 2) : 0;

        $valuationScore = $this->calculateValuationScore($engagementRate, $averageEngagement, $followersCount, null);
        $valuationTier = $this->getValuationTier($valuationScore);

        return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_comments' => $averageComments,
            'total_posts_analyzed' => $postCount,
            'valuation_score' => $valuationScore,
            'valuation_tier' => $valuationTier
        ];
    }

    /**
     * Format Apify Instagram media
     */
    /**
     * Format Apify Instagram media
     * Handles different data formats from apify/instagram-scraper
     */
    protected function formatApifyInstagramMedia($media)
    {
        if (empty($media) || !is_array($media)) {
            return [];
        }

        return array_map(function($item) {
            // apify/instagram-post-scraper returns: likesCount, commentsCount (camelCase)
            // apify/instagram-scraper returns: like_count, comments_count (snake_case)
            // Handle various field names that might be in the response
            $likeCount = $item['likesCount'] ?? $item['like_count'] ?? $item['likes'] ?? $item['edge_media_preview_like']['count'] ?? 0;
            $commentCount = $item['commentsCount'] ?? $item['comments_count'] ?? $item['comments'] ?? $item['edge_media_to_comment']['count'] ?? 0;
            
            return [
                'id' => $item['id'] ?? $item['shortCode'] ?? $item['pk'] ?? null,
                'shortCode' => $item['shortCode'] ?? null,
                'caption' => $item['caption'] ?? $item['text'] ?? (isset($item['edge_media_to_caption']['edges'][0]['node']['text']) ? $item['edge_media_to_caption']['edges'][0]['node']['text'] : null) ?? null,
                'media_type' => $item['type'] ?? $item['mediaType'] ?? (isset($item['is_video']) ? ($item['is_video'] ? 'VIDEO' : 'IMAGE') : null) ?? null,
                'media_url' => $item['displayUrl'] ?? $item['imageUrl'] ?? $item['videoUrl'] ?? $item['display_url'] ?? $item['video_url'] ?? null,
                'permalink' => $item['url'] ?? $item['permalink'] ?? (isset($item['shortCode']) ? "https://www.instagram.com/p/{$item['shortCode']}/" : null) ?? null,
                'like_count' => max(0, (int)$likeCount),
                'comments_count' => max(0, (int)$commentCount),
                'timestamp' => $item['timestamp'] ?? $item['takenAt'] ?? $item['taken_at_timestamp'] ?? $item['created_time'] ?? null,
                'hashtags' => $item['hashtags'] ?? [],
                'mentions' => $item['mentions'] ?? []
            ];
        }, $media);
    }

    /**
     * Search TikTok account by username
     */
    public function searchTikTokAccount($username)
    {
        try {
            $cleanUsername = trim($username);
            $cleanUsername = preg_replace('/^@/', '', $cleanUsername);
            $cleanUsername = preg_replace('/^https?:\/\/(www\.)?(tiktok\.com\/@|vm\.tiktok\.com\/)/', '', $cleanUsername);
            $cleanUsername = trim($cleanUsername, '/');

            // clockworks/tiktok-profile-scraper accepts profiles as usernames or URLs
            // Can use either 'profiles' (array of usernames) or 'startUrls' (array of URLs)
            $input = [
                'profiles' => [$cleanUsername], // Use username directly
                'maxItems' => 100, // Get more posts for better engagement analysis
            ];
            
            Log::info('Scraping TikTok account', [
                'username' => $username,
                'clean_username' => $cleanUsername,
                'actor_id' => $this->tiktokActorId,
                'input_format' => 'profiles array'
            ]);

            $result = $this->runApifyActor($this->tiktokActorId, $input);
            
            // Log raw TikTok API response
            $tiktokUrl = "https://www.tiktok.com/@{$cleanUsername}";
            Log::info('TikTok API raw response', [
                'username' => $username,
                'clean_username' => $cleanUsername,
                'tiktok_url' => $tiktokUrl,
                'success' => $result['success'] ?? false,
                'raw_data' => isset($result['data']) ? json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'error' => $result['error'] ?? null
            ]);

            if ($result['success']) {
                $data = $this->formatApifyTikTokData($result['data']);
                
                // Check if formatting returned an error (account doesn't exist or no valid data)
                if (isset($data['error'])) {
                    Log::info('TikTok account not found', [
                        'username' => $username,
                        'error' => $data['error']
                    ]);
                    return [
                        'success' => false,
                        'error' => $data['error'] ?? 'Account not found or not accessible'
                    ];
                }
                
                // Log formatted TikTok data
                Log::info('TikTok formatted data', [
                    'username' => $username,
                    'formatted_data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
                    
                    return [
                        'success' => true,
                        'platform' => 'tiktok',
                        'data' => $data,
                        'apify_usage' => $result['apify_usage'] ?? null
                    ];
            }

            return $result;

        } catch (Exception $e) {
            Log::error('TikTok account search exception', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);

            // If we have a result with apify_usage, preserve it even on exception
            $apifyUsage = null;
            if (isset($result) && isset($result['apify_usage'])) {
                $apifyUsage = $result['apify_usage'];
            }

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'apify_usage' => $apifyUsage
            ];
        }
    }

    /**
     * Search Twitter/X account by username
     */
    public function searchTwitterAccount($username)
    {
        try {
            $cleanUsername = trim($username);
            $cleanUsername = preg_replace('/^@/', '', $cleanUsername);
            $cleanUsername = preg_replace('/^https?:\/\/(www\.)?(twitter\.com\/|x\.com\/)/', '', $cleanUsername);
            $cleanUsername = trim($cleanUsername, '/');

            // apidojo/tweet-scraper uses searchTerms with "from:username" format
            $input = [
                'searchTerms' => ["from:{$cleanUsername}"],
                'maxItems' => 100, // Get more tweets for better engagement analysis
                'sort' => 'Latest',
                'tweetLanguage' => 'en',
            ];
            
            Log::info('Scraping Twitter/X account', [
                'username' => $username,
                'clean_username' => $cleanUsername,
                'actor_id' => $this->twitterActorId,
                'input_format' => 'searchTerms array'
            ]);

            $result = $this->runApifyActor($this->twitterActorId, $input);
            
            // Log raw Twitter API response
            $twitterUrl = "https://twitter.com/{$cleanUsername}";
            Log::info('Twitter/X API raw response', [
                'username' => $username,
                'clean_username' => $cleanUsername,
                'twitter_url' => $twitterUrl,
                'success' => $result['success'] ?? false,
                'raw_data' => isset($result['data']) ? json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'error' => $result['error'] ?? null
            ]);

            if ($result['success']) {
                $data = $this->formatApifyTwitterData($result['data']);
                
                // Check if formatting returned an error (account doesn't exist or no valid data)
                if (isset($data['error'])) {
                    Log::info('Twitter/X account not found', [
                        'username' => $username,
                        'error' => $data['error']
                    ]);
                    return [
                        'success' => false,
                        'error' => $data['error'] ?? 'Account not found or not accessible'
                    ];
                }
                
                // Log formatted Twitter data
                Log::info('Twitter/X formatted data', [
                    'username' => $username,
                    'formatted_data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
                    
                return [
                    'success' => true,
                    'platform' => 'twitter',
                    'data' => $data,
                    'apify_usage' => $result['apify_usage'] ?? null
                ];
            }

            return $result;

        } catch (Exception $e) {
            Log::error('Twitter/X account search exception', [
                'username' => $username,
                'exception' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'apify_usage' => null // No Apify usage if exception occurred before scraping
            ];
        }
    }

    /**
     * Search all social media platforms simultaneously
     */
    public function searchAllPlatforms($username, $selectedPlatforms = null)
    {
        // Increase execution time limit for multi-platform search
        // Instagram scraping can take 15+ minutes, so set a high limit
        set_time_limit(1200); // 20 minutes for all platforms (Instagram needs extra time)
        
        // Default to all platforms if none specified
        if ($selectedPlatforms === null) {
            $selectedPlatforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
        }
        
        // Initialize results structure for all platforms
        $allPlatforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
        $results = [
            'username' => $username,
            'platforms' => [],
            'total_found' => 0,
            'apify_usage' => [] // Collect Apify usage from all platform searches
        ];
        
        // Initialize all platforms as not found
        foreach ($allPlatforms as $platform) {
            $results['platforms'][$platform] = ['found' => false, 'data' => null, 'error' => null];
        }

        // Search only selected platforms sequentially with timeout protection
        if (in_array('facebook', $selectedPlatforms)) {
            try {
                $facebookResult = $this->searchFacebookPageByUsername($username);
                if ($facebookResult['success']) {
                    $results['platforms']['facebook'] = ['found' => true, 'data' => $facebookResult['data'], 'error' => null];
                    $results['total_found']++;
                } else {
                    $results['platforms']['facebook']['error'] = $facebookResult['error'] ?? 'Not found';
                }
                // Collect Apify usage
                if (isset($facebookResult['apify_usage'])) {
                    $results['apify_usage'][] = $facebookResult['apify_usage'];
                }
            } catch (\Exception $e) {
                Log::error('Facebook search exception in searchAllPlatforms', [
                    'username' => $username,
                    'exception' => $e->getMessage()
                ]);
                $results['platforms']['facebook']['error'] = 'Search timeout or error: ' . $e->getMessage();
            }
        }

        if (in_array('instagram', $selectedPlatforms)) {
            try {
                $instagramResult = $this->searchInstagramAccountByUsername($username);
                if ($instagramResult['success']) {
                    $results['platforms']['instagram'] = ['found' => true, 'data' => $instagramResult['data'], 'error' => null];
                    $results['total_found']++;
                } else {
                    // Check if Instagram is still processing
                    if (isset($instagramResult['status']) && $instagramResult['status'] === 'processing') {
                        $results['platforms']['instagram'] = [
                            'found' => false,
                            'data' => null,
                            'error' => null,
                            'status' => 'processing',
                            'message' => $instagramResult['message'] ?? 'Instagram scraping is still in progress. This may take several more minutes.',
                            'run_id' => $instagramResult['run_id'] ?? null
                        ];
                        Log::info('Instagram search still processing', [
                            'username' => $username,
                            'run_id' => $instagramResult['run_id'] ?? null
                        ]);
                    } else {
                        $results['platforms']['instagram']['error'] = $instagramResult['error'] ?? 'Not found';
                    }
                }
                // Collect Apify usage
                if (isset($instagramResult['apify_usage'])) {
                    $results['apify_usage'][] = $instagramResult['apify_usage'];
                }
            } catch (\Exception $e) {
                Log::error('Instagram search exception in searchAllPlatforms', [
                    'username' => $username,
                    'exception' => $e->getMessage()
                ]);
                $results['platforms']['instagram']['error'] = 'Search timeout or error: ' . $e->getMessage();
            }
        }

        if (in_array('tiktok', $selectedPlatforms)) {
            try {
                $tiktokResult = $this->searchTikTokAccount($username);
                if ($tiktokResult['success']) {
                    $results['platforms']['tiktok'] = ['found' => true, 'data' => $tiktokResult['data'], 'error' => null];
                    $results['total_found']++;
                } else {
                    $results['platforms']['tiktok']['error'] = $tiktokResult['error'] ?? 'Not found';
                }
                // Collect Apify usage
                if (isset($tiktokResult['apify_usage'])) {
                    $results['apify_usage'][] = $tiktokResult['apify_usage'];
                }
            } catch (\Exception $e) {
                Log::error('TikTok search exception in searchAllPlatforms', [
                    'username' => $username,
                    'exception' => $e->getMessage()
                ]);
                $results['platforms']['tiktok']['error'] = 'Search timeout or error: ' . $e->getMessage();
            }
        }

        if (in_array('twitter', $selectedPlatforms)) {
            try {
                $twitterResult = $this->searchTwitterAccount($username);
                if ($twitterResult['success']) {
                    $results['platforms']['twitter'] = ['found' => true, 'data' => $twitterResult['data'], 'error' => null];
                    $results['total_found']++;
                } else {
                    $results['platforms']['twitter']['error'] = $twitterResult['error'] ?? 'Not found';
                }
                // Collect Apify usage
                if (isset($twitterResult['apify_usage'])) {
                    $results['apify_usage'][] = $twitterResult['apify_usage'];
                }
            } catch (\Exception $e) {
                Log::error('Twitter/X search exception in searchAllPlatforms', [
                    'username' => $username,
                    'exception' => $e->getMessage()
                ]);
                $results['platforms']['twitter']['error'] = 'Search timeout or error: ' . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Format Apify TikTok data
     */
    protected function formatApifyTikTokData($items)
    {
        if (empty($items) || !is_array($items)) {
            return ['error' => 'No data returned from Apify'];
        }

        // clockworks/tiktok-profile-scraper returns videos with authorMeta containing profile data
        $profileData = null;
        $videos = [];

        foreach ($items as $item) {
            // Check if this is a video with authorMeta (new scraper format)
            if (isset($item['authorMeta']) && is_array($item['authorMeta'])) {
                $videos[] = $item;
                // Extract profile data from first video's authorMeta
                if (!$profileData) {
                    $authorMeta = $item['authorMeta'];
                    $profileData = [
                        'id' => $authorMeta['id'] ?? null,
                        'username' => $authorMeta['name'] ?? null, // 'name' is the username in this scraper
                        'uniqueId' => $authorMeta['name'] ?? null,
                        'nickname' => $authorMeta['nickName'] ?? null,
                        'name' => $authorMeta['nickName'] ?? null,
                        'signature' => $authorMeta['signature'] ?? null,
                        'bio' => $authorMeta['signature'] ?? null,
                        'avatar' => $authorMeta['avatar'] ?? $authorMeta['originalAvatarUrl'] ?? null,
                        'profilePicture' => $authorMeta['avatar'] ?? $authorMeta['originalAvatarUrl'] ?? null,
                        'profileUrl' => $authorMeta['profileUrl'] ?? null,
                        'followerCount' => $authorMeta['fans'] ?? 0,
                        'followers' => $authorMeta['fans'] ?? 0,
                        'followingCount' => $authorMeta['following'] ?? 0,
                        'following' => $authorMeta['following'] ?? 0,
                        'heartCount' => $authorMeta['heart'] ?? 0,
                        'totalLikes' => $authorMeta['heart'] ?? 0,
                        'videoCount' => $authorMeta['video'] ?? 0,
                    ];
                }
            } elseif (isset($item['type']) && $item['type'] === 'Profile') {
                // Old format - separate profile object
                $profileData = $item;
            } elseif (isset($item['type']) && $item['type'] === 'Video') {
                // Old format - separate video object
                $videos[] = $item;
            } elseif (!$profileData) {
                // Fallback - assume first item might be profile
                $profileData = $item;
            }
        }

        // If no profile data found, try to extract from first video
        if (!$profileData && !empty($videos) && isset($videos[0]['authorMeta'])) {
            $authorMeta = $videos[0]['authorMeta'];
            $profileData = [
                'id' => $authorMeta['id'] ?? null,
                'username' => $authorMeta['name'] ?? null,
                'uniqueId' => $authorMeta['name'] ?? null,
                'nickname' => $authorMeta['nickName'] ?? null,
                'name' => $authorMeta['nickName'] ?? null,
                'signature' => $authorMeta['signature'] ?? null,
                'bio' => $authorMeta['signature'] ?? null,
                'avatar' => $authorMeta['avatar'] ?? $authorMeta['originalAvatarUrl'] ?? null,
                'profilePicture' => $authorMeta['avatar'] ?? $authorMeta['originalAvatarUrl'] ?? null,
                'profileUrl' => $authorMeta['profileUrl'] ?? null,
                'followerCount' => $authorMeta['fans'] ?? 0,
                'followers' => $authorMeta['fans'] ?? 0,
                'followingCount' => $authorMeta['following'] ?? 0,
                'following' => $authorMeta['following'] ?? 0,
                'heartCount' => $authorMeta['heart'] ?? 0,
                'totalLikes' => $authorMeta['heart'] ?? 0,
                'videoCount' => $authorMeta['video'] ?? 0,
            ];
        }

        // If still no profile data, use first item as fallback
        if (!$profileData && !empty($items)) {
            $profileData = $items[0];
        }

        // Validate that we have actual account data
        // Check if we have videos with valid content or profile data
        $hasValidData = false;
        
        // If we have any videos, check if they have valid content
        if (!empty($videos)) {
            foreach ($videos as $video) {
                // Check if video has valid content (id, text, or engagement data)
                if (isset($video['id']) || isset($video['text']) || isset($video['diggCount']) || isset($video['playCount'])) {
                    $hasValidData = true;
                    break;
                }
            }
        }
        
        // Also check if we have valid profile data with username or ID
        if (!$hasValidData && !empty($profileData)) {
            $username = $profileData['username'] ?? $profileData['uniqueId'] ?? $profileData['name'] ?? null;
            if ($username || isset($profileData['id']) || isset($profileData['userId'])) {
                $hasValidData = true;
            }
        }
        
        // If no valid data found, account doesn't exist
        if (!$hasValidData && empty($videos) && empty($profileData)) {
            Log::info('TikTok account has no valid data', [
                'videos_count' => count($videos),
                'has_profile_data' => !empty($profileData)
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }

        $formattedVideos = $this->formatApifyTikTokVideos($videos);
        
        // Get profile info
        $username = $profileData['username'] ?? $profileData['uniqueId'] ?? $profileData['name'] ?? null;
        $profileUrl = $profileData['profileUrl'] ?? ($username ? "https://www.tiktok.com/@{$username}" : null);
        
        // Get follower count
        $followersCount = $profileData['followerCount'] ?? $profileData['followers'] ?? $profileData['fans'] ?? 0;
        
        // Validate we have at least some engagement data or profile info
        // If no videos and no username/profile data, it's not a valid account
        if (empty($formattedVideos) && !$username && !isset($profileData['id']) && !isset($profileData['userId'])) {
            Log::info('TikTok account has no videos and no profile data', [
                'videos_count' => count($formattedVideos),
                'username' => $username,
                'has_profile_id' => isset($profileData['id']) || isset($profileData['userId'])
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }
        
        // Calculate engagement metrics
        $engagementMetrics = $this->calculateTikTokEngagementMetrics($formattedVideos, $followersCount);
        
        return [
            'id' => $profileData['id'] ?? $profileData['userId'] ?? null,
            'username' => $username,
            'name' => $profileData['nickname'] ?? $profileData['name'] ?? $profileData['nickName'] ?? null,
            'bio' => $profileData['signature'] ?? $profileData['bio'] ?? null,
            'profile_picture' => $profileData['avatar'] ?? $profileData['profilePicture'] ?? $profileData['originalAvatarUrl'] ?? null,
            'profile_url' => $profileUrl,
            'followers_count' => $followersCount,
            'following_count' => $profileData['followingCount'] ?? $profileData['following'] ?? 0,
            'likes_count' => $profileData['heartCount'] ?? $profileData['totalLikes'] ?? $profileData['heart'] ?? 0,
            'videos_count' => $profileData['videoCount'] ?? $profileData['video'] ?? count($videos),
            'recent_videos' => $formattedVideos,
            'stats' => [
                'total_followers' => $followersCount,
                'total_following' => $profileData['followingCount'] ?? $profileData['following'] ?? 0,
                'total_likes' => $profileData['heartCount'] ?? $profileData['totalLikes'] ?? $profileData['heart'] ?? 0,
                'total_videos' => $profileData['videoCount'] ?? $profileData['video'] ?? count($videos),
                'recent_videos_count' => count($videos)
            ],
            'engagement' => $engagementMetrics,
        ];
    }

    /**
     * Calculate TikTok engagement metrics
     */
    protected function calculateTikTokEngagementMetrics($videos, $followersCount)
    {
        if (empty($videos)) {
            return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_comments' => 0,
                'average_shares' => 0,
                'average_views' => 0,
                'total_posts_analyzed' => 0,
                'total_likes' => 0,
                'total_comments' => 0,
                'total_shares' => 0,
                'total_views' => 0,
            ];
        }

        $totalLikes = 0;
        $totalComments = 0;
        $totalShares = 0;
        $totalViews = 0;
        $totalEngagement = 0;
        $videoCount = count($videos);

        foreach ($videos as $video) {
            $likes = max(0, (int)($video['likes'] ?? $video['like_count'] ?? 0));
            $comments = max(0, (int)($video['comments'] ?? $video['comments_count'] ?? 0));
            $shares = max(0, (int)($video['shares'] ?? $video['share_count'] ?? 0));
            $views = max(0, (int)($video['views'] ?? $video['view_count'] ?? 0));
            
            $totalLikes += $likes;
            $totalComments += $comments;
            $totalShares += $shares;
            $totalViews += $views;
            $totalEngagement += ($likes + $comments + $shares);
        }

        $averageLikes = $videoCount > 0 ? round($totalLikes / $videoCount, 2) : 0;
        $averageComments = $videoCount > 0 ? round($totalComments / $videoCount, 2) : 0;
        $averageShares = $videoCount > 0 ? round($totalShares / $videoCount, 2) : 0;
        $averageViews = $videoCount > 0 ? round($totalViews / $videoCount, 2) : 0;
        $averageEngagement = $videoCount > 0 ? round($totalEngagement / $videoCount, 2) : 0;
        
        // Calculate engagement rate: (average engagement per post / followers) * 100
        // This gives the percentage of followers who engage with each post on average
        $engagementRate = ($followersCount > 0 && $averageEngagement > 0) 
            ? round(($averageEngagement / $followersCount) * 100, 2) 
            : 0;

        return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_comments' => $averageComments,
            'average_shares' => $averageShares,
            'average_views' => $averageViews,
            'total_posts_analyzed' => $videoCount,
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'total_shares' => $totalShares,
            'total_views' => $totalViews,
        ];
    }

    /**
     * Format Apify TikTok videos
     */
    protected function formatApifyTikTokVideos($videos)
    {
        if (empty($videos) || !is_array($videos)) {
            return [];
        }

        return array_map(function($video) {
            $likes = max(0, (int)($video['diggCount'] ?? $video['likes'] ?? 0));
            $comments = max(0, (int)($video['commentCount'] ?? $video['comments'] ?? 0));
            $shares = max(0, (int)($video['shareCount'] ?? $video['shares'] ?? 0));
            $views = max(0, (int)($video['playCount'] ?? $video['views'] ?? 0));
            $totalEngagement = $likes + $comments + $shares;
            
            return [
                'id' => $video['id'] ?? $video['videoId'] ?? null,
                'description' => $video['text'] ?? $video['description'] ?? null,
                'created_time' => $video['createTimeISO'] ?? $video['createTime'] ?? $video['timestamp'] ?? null,
                'timestamp' => $video['createTime'] ?? $video['timestamp'] ?? null,
                'likes' => $likes,
                'like_count' => $likes,
                'comments' => $comments,
                'comments_count' => $comments,
                'shares' => $shares,
                'share_count' => $shares,
                'views' => $views,
                'view_count' => $views,
                'total_engagement' => $totalEngagement,
                'url' => $video['webVideoUrl'] ?? $video['url'] ?? null,
                'permalink' => $video['webVideoUrl'] ?? $video['url'] ?? null,
                'cover' => $video['videoMeta']['coverUrl'] ?? $video['cover'] ?? $video['coverUrl'] ?? null,
            ];
        }, $videos);
    }

    /**
     * Format Apify Twitter/X data
     */
    protected function formatApifyTwitterData($items)
    {
        if (empty($items) || !is_array($items)) {
            return ['error' => 'No data returned from Apify'];
        }

        // apify/twitter-scraper returns tweets with user data
        $profileData = null;
        $tweets = [];

        foreach ($items as $item) {
            // Check if this is a tweet with user data
            if (isset($item['user']) && is_array($item['user'])) {
                $tweets[] = $item;
                // Extract profile data from first tweet's user object
                if (!$profileData) {
                    $user = $item['user'];
                    $profileData = [
                        'id' => $user['id'] ?? $user['userId'] ?? null,
                        'username' => $user['username'] ?? $user['screenName'] ?? null,
                        'name' => $user['name'] ?? $user['displayName'] ?? null,
                        'bio' => $user['description'] ?? $user['bio'] ?? null,
                        'profile_picture' => $user['profileImageUrl'] ?? $user['profileImageUrlHttps'] ?? $user['avatar'] ?? null,
                        'profile_url' => $user['url'] ?? ($user['username'] ? "https://twitter.com/{$user['username']}" : null),
                        'followers_count' => $user['followersCount'] ?? $user['followers'] ?? 0,
                        'following_count' => $user['followingCount'] ?? $user['following'] ?? 0,
                        'tweets_count' => $user['tweetsCount'] ?? $user['statusesCount'] ?? 0,
                    ];
                }
            } elseif (isset($item['type']) && $item['type'] === 'Profile') {
                // Separate profile object
                $profileData = $item;
            } elseif (isset($item['type']) && $item['type'] === 'Tweet') {
                // Separate tweet object
                $tweets[] = $item;
            } elseif (!$profileData && isset($item['username'])) {
                // Fallback - assume first item might be profile
                $profileData = $item;
            } else {
                // Assume it's a tweet
                $tweets[] = $item;
            }
        }

        // If no profile data found, try to extract from first tweet
        if (!$profileData && !empty($tweets) && isset($tweets[0]['user'])) {
            $user = $tweets[0]['user'];
            $profileData = [
                'id' => $user['id'] ?? $user['userId'] ?? null,
                'username' => $user['username'] ?? $user['screenName'] ?? null,
                'name' => $user['name'] ?? $user['displayName'] ?? null,
                'bio' => $user['description'] ?? $user['bio'] ?? null,
                'profile_picture' => $user['profileImageUrl'] ?? $user['profileImageUrlHttps'] ?? $user['avatar'] ?? null,
                'profile_url' => $user['url'] ?? ($user['username'] ? "https://twitter.com/{$user['username']}" : null),
                'followers_count' => $user['followersCount'] ?? $user['followers'] ?? 0,
                'following_count' => $user['followingCount'] ?? $user['following'] ?? 0,
                'tweets_count' => $user['tweetsCount'] ?? $user['statusesCount'] ?? 0,
            ];
        }

        // If still no profile data, use first item as fallback
        if (!$profileData && !empty($items)) {
            $profileData = $items[0];
        }

        // Validate that we have actual account data
        $hasValidData = false;
        
        // If we have any tweets, check if they have valid content
        if (!empty($tweets)) {
            foreach ($tweets as $tweet) {
                // Check if tweet has valid content (id, text, or engagement data)
                if (isset($tweet['id']) || isset($tweet['text']) || isset($tweet['likeCount']) || isset($tweet['retweetCount'])) {
                    $hasValidData = true;
                    break;
                }
            }
        }
        
        // Also check if we have valid profile data with username or ID
        if (!$hasValidData && !empty($profileData)) {
            $username = $profileData['username'] ?? $profileData['screenName'] ?? null;
            if ($username || isset($profileData['id']) || isset($profileData['userId'])) {
                $hasValidData = true;
            }
        }
        
        // If no valid data found, account doesn't exist
        if (!$hasValidData && empty($tweets) && empty($profileData)) {
            Log::info('Twitter/X account has no valid data', [
                'tweets_count' => count($tweets),
                'has_profile_data' => !empty($profileData)
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }

        $formattedTweets = $this->formatApifyTwitterTweets($tweets);
        
        // Get profile info
        $username = $profileData['username'] ?? $profileData['screenName'] ?? null;
        $profileUrl = $profileData['profile_url'] ?? $profileData['url'] ?? ($username ? "https://twitter.com/{$username}" : null);
        
        // Get follower count
        $followersCount = $profileData['followers_count'] ?? $profileData['followersCount'] ?? $profileData['followers'] ?? 0;
        
        // Validate we have at least some engagement data or profile info
        if (empty($formattedTweets) && !$username && !isset($profileData['id']) && !isset($profileData['userId'])) {
            Log::info('Twitter/X account has no tweets and no profile data', [
                'tweets_count' => count($formattedTweets),
                'username' => $username,
                'has_profile_id' => isset($profileData['id']) || isset($profileData['userId'])
            ]);
            return [
                'error' => 'Account not found or not accessible'
            ];
        }
        
        // Calculate engagement metrics
        $engagementMetrics = $this->calculateTwitterEngagementMetrics($formattedTweets, $followersCount);
        
        return [
            'id' => $profileData['id'] ?? $profileData['userId'] ?? null,
            'username' => $username,
            'name' => $profileData['name'] ?? $profileData['displayName'] ?? null,
            'bio' => $profileData['bio'] ?? $profileData['description'] ?? null,
            'profile_picture' => $profileData['profile_picture'] ?? $profileData['profileImageUrl'] ?? $profileData['profileImageUrlHttps'] ?? $profileData['avatar'] ?? null,
            'profile_url' => $profileUrl,
            'followers_count' => $followersCount,
            'following_count' => $profileData['following_count'] ?? $profileData['followingCount'] ?? $profileData['following'] ?? 0,
            'tweets_count' => $profileData['tweets_count'] ?? $profileData['tweetsCount'] ?? $profileData['statusesCount'] ?? count($tweets),
            'recent_tweets' => $formattedTweets,
            'stats' => [
                'total_followers' => $followersCount,
                'total_following' => $profileData['following_count'] ?? $profileData['followingCount'] ?? $profileData['following'] ?? 0,
                'total_tweets' => $profileData['tweets_count'] ?? $profileData['tweetsCount'] ?? $profileData['statusesCount'] ?? count($tweets),
                'recent_tweets_count' => count($tweets)
            ],
            'engagement' => $engagementMetrics,
        ];
    }

    /**
     * Calculate Twitter/X engagement metrics
     */
    protected function calculateTwitterEngagementMetrics($tweets, $followersCount)
    {
        if (empty($tweets)) {
            return [
                'total_engagement' => 0,
                'average_engagement_per_post' => 0,
                'engagement_rate' => 0,
                'average_likes' => 0,
                'average_retweets' => 0,
                'average_replies' => 0,
                'total_posts_analyzed' => 0,
                'total_likes' => 0,
                'total_retweets' => 0,
                'total_replies' => 0,
            ];
        }

        $totalLikes = 0;
        $totalRetweets = 0;
        $totalReplies = 0;
        $totalEngagement = 0;
        $tweetCount = count($tweets);

        foreach ($tweets as $tweet) {
            $likes = max(0, (int)($tweet['likes'] ?? $tweet['like_count'] ?? $tweet['likeCount'] ?? 0));
            $retweets = max(0, (int)($tweet['retweets'] ?? $tweet['retweet_count'] ?? $tweet['retweetCount'] ?? 0));
            $replies = max(0, (int)($tweet['replies'] ?? $tweet['reply_count'] ?? $tweet['replyCount'] ?? 0));
            
            $totalLikes += $likes;
            $totalRetweets += $retweets;
            $totalReplies += $replies;
            $totalEngagement += ($likes + $retweets + $replies);
        }

        $averageLikes = $tweetCount > 0 ? round($totalLikes / $tweetCount, 2) : 0;
        $averageRetweets = $tweetCount > 0 ? round($totalRetweets / $tweetCount, 2) : 0;
        $averageReplies = $tweetCount > 0 ? round($totalReplies / $tweetCount, 2) : 0;
        $averageEngagement = $tweetCount > 0 ? round($totalEngagement / $tweetCount, 2) : 0;
        
        // Calculate engagement rate: (average engagement per post / followers) * 100
        $engagementRate = ($followersCount > 0 && $averageEngagement > 0) 
            ? round(($averageEngagement / $followersCount) * 100, 2) 
            : 0;

        return [
            'total_engagement' => $totalEngagement,
            'average_engagement_per_post' => $averageEngagement,
            'engagement_rate' => $engagementRate,
            'average_likes' => $averageLikes,
            'average_retweets' => $averageRetweets,
            'average_replies' => $averageReplies,
            'total_posts_analyzed' => $tweetCount,
            'total_likes' => $totalLikes,
            'total_retweets' => $totalRetweets,
            'total_replies' => $totalReplies,
        ];
    }

    /**
     * Format Apify Twitter/X tweets
     */
    protected function formatApifyTwitterTweets($tweets)
    {
        if (empty($tweets) || !is_array($tweets)) {
            return [];
        }

        return array_map(function($tweet) {
            $likes = max(0, (int)($tweet['likeCount'] ?? $tweet['likes'] ?? $tweet['like_count'] ?? 0));
            $retweets = max(0, (int)($tweet['retweetCount'] ?? $tweet['retweets'] ?? $tweet['retweet_count'] ?? 0));
            $replies = max(0, (int)($tweet['replyCount'] ?? $tweet['replies'] ?? $tweet['reply_count'] ?? 0));
            $totalEngagement = $likes + $retweets + $replies;
            
            return [
                'id' => $tweet['id'] ?? $tweet['tweetId'] ?? null,
                'text' => $tweet['text'] ?? $tweet['fullText'] ?? $tweet['content'] ?? null,
                'created_time' => $tweet['createdAt'] ?? $tweet['created_at'] ?? $tweet['timestamp'] ?? null,
                'timestamp' => $tweet['timestamp'] ?? $tweet['created_at'] ?? null,
                'likes' => $likes,
                'like_count' => $likes,
                'retweets' => $retweets,
                'retweet_count' => $retweets,
                'replies' => $replies,
                'reply_count' => $replies,
                'total_engagement' => $totalEngagement,
                'url' => $tweet['url'] ?? $tweet['permalink'] ?? null,
                'permalink' => $tweet['url'] ?? $tweet['permalink'] ?? null,
            ];
        }, $tweets);
    }

    /**
     * Analyze social media account by username (primary method)
     * Currently only supports Facebook Posts Scraper
     */
    public function analyzeSocialMediaAccount($identifier, $platform = 'auto')
    {
        try {
            // For now, only support Facebook
            if ($platform === 'instagram') {
                return [
                    'success' => false,
                    'error' => 'Instagram scraping is currently disabled. Only Facebook Posts Scraper is available.',
                    'suggestions' => [
                        'Try using platform: "facebook" or "auto"',
                        'Enter a Facebook page URL or username'
                    ]
                ];
            }

            // Try Facebook
            $facebookResult = $this->searchFacebookPageByUsername($identifier);
                
                if ($facebookResult['success']) {
                    return [
                        'success' => true,
                        'facebook' => $facebookResult['data'],
                        'instagram' => null,
                        'has_instagram' => false,
                    'search_method' => 'username'
                ];
            }

            // Return error if Facebook search failed
            return [
                'success' => false,
                'error' => 'Could not find Facebook account with the provided username. ' . ($facebookResult['error'] ?? 'Unknown error'),
                'suggestions' => [
                    'Verify the username is correct (try without @ or domain)',
                    'Ensure the Facebook page or profile is public (private accounts cannot be scraped)',
                    'Check that your Apify API token is valid',
                    'Try using the full Facebook URL (e.g., https://www.facebook.com/yourpage or https://www.facebook.com/username)',
                    'Works with both Facebook Pages and personal profiles (if public)',
                    'For personal profiles, use the username from the profile URL'
                ],
                'debug_info' => [
                    'identifier' => $identifier,
                    'platform' => $platform,
                ]
            ];

        } catch (Exception $e) {
            Log::error('Social media analysis exception', [
                'identifier' => $identifier,
                'platform' => $platform,
                'exception' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
