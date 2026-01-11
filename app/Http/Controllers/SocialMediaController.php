<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAnalysis;
use App\Services\SocialMediaService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SocialMediaController extends Controller
{
    protected $socialMediaService;
    protected $analyticsService;

    public function __construct(SocialMediaService $socialMediaService, AnalyticsService $analyticsService)
    {
        $this->socialMediaService = $socialMediaService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show social media analysis form (New Analysis)
     */
    public function index()
    {
        return view('social-media.index');
    }

    /**
     * Show history of social media analyses
     */
    public function history(Request $request)
    {
        $query = Auth::user()->socialMediaAnalyses();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('username', 'like', '%' . $search . '%');
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Optimize query: select only necessary columns and limit results to prevent memory issues
        // Select only columns needed for the history page display
        $analyses = $query->select('id', 'username', 'status', 'ai_analysis', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(1000) // Limit to prevent memory issues on servers with limited sort buffer
            ->get();
        
        // Get total counts for stats (optimized - use select to avoid loading full records)
        $stats = [
            'total' => Auth::user()->socialMediaAnalyses()->count(),
            'completed' => Auth::user()->socialMediaAnalyses()->where('status', SocialMediaAnalysis::STATUS_COMPLETED)->count(),
            'processing' => Auth::user()->socialMediaAnalyses()->where('status', SocialMediaAnalysis::STATUS_PROCESSING)->count(),
            'failed' => Auth::user()->socialMediaAnalyses()->where('status', SocialMediaAnalysis::STATUS_FAILED)->count(),
        ];
        
        return view('social-media.history', compact('analyses', 'stats'));
    }

    /**
     * Show individual social media analysis
     */
    public function show(SocialMediaAnalysis $socialMediaAnalysis)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'User not authenticated.');
        }
        
        // Ensure user owns this analysis
        // Fix: Convert both values to integers for comparison to handle string vs integer mismatch
        if ((int)Auth::id() !== (int)$socialMediaAnalysis->user_id) {
            Log::warning('Unauthorized social media analysis access attempt', [
                'user_id' => Auth::id(),
                'analysis_id' => $socialMediaAnalysis->id,
                'analysis_user_id' => $socialMediaAnalysis->user_id,
                'user_role' => Auth::user()->role ?? 'unknown'
            ]);
            abort(403, 'Unauthorized access');
        }
        
        // Return HTML content for AJAX requests (for history page)
        if (request()->ajax() && request()->header('X-Requested-With') === 'XMLHttpRequest') {
            // Check if requesting engagement metrics
            if (request()->get('view') === 'engagement') {
                return view('social-media.partials.engagement-metrics', compact('socialMediaAnalysis'))->render();
            }
            return view('social-media.partials.content', compact('socialMediaAnalysis'))->render();
        }
        
        return view('social-media.show', compact('socialMediaAnalysis'));
    }

    /**
     * Analyze social media account
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'platform' => 'nullable|in:auto,facebook,instagram'
        ]);

        try {
            $identifier = trim($request->input('identifier'));
            $platform = $request->input('platform', 'auto');

            Log::info('Social media analysis requested', [
                'user_id' => Auth::id(),
                'identifier' => $identifier,
                'platform' => $platform
            ]);

            $result = $this->socialMediaService->analyzeSocialMediaAccount($identifier, $platform);

            if ($result['success']) {
                return view('social-media.results', [
                    'result' => $result,
                    'identifier' => $identifier,
                    'platform' => $platform
                ]);
            } else {
                // Store the full result in session to show detailed error in the view
                session()->flash('analysis_error', $result);
                
                return back()->withErrors([
                    'error' => $result['error'] ?? 'Failed to analyze social media account'
                ])->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Social media analysis exception', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'An error occurred while analyzing the account: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Get Facebook page information
     */
    public function getFacebookInfo(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string'
        ]);

        try {
            $identifier = trim($request->input('identifier'));
            $isUsername = !is_numeric($identifier);

            if ($isUsername) {
                $result = $this->socialMediaService->searchFacebookPageByUsername($identifier);
            } else {
                $result = $this->socialMediaService->getFacebookPageInfo($identifier, false);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Facebook info fetch exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Instagram account information
     */
    public function getInstagramInfo(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string'
        ]);

        try {
            $identifier = trim($request->input('identifier'));
            $isUsername = !is_numeric($identifier);

            if ($isUsername) {
                // For Instagram, try to get from Facebook Page if we have the default ID
                $defaultPageId = '863876976151079';
                $result = $this->socialMediaService->getInstagramAccountFromFacebookPage($defaultPageId);
            } else {
                $result = $this->socialMediaService->getInstagramAccountInfo($identifier, false);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Instagram info fetch exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Instagram account from Facebook Page ID
     */
    public function getInstagramFromFacebookPage(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string'
        ]);

        try {
            $pageId = trim($request->input('page_id'));
            $result = $this->socialMediaService->getInstagramAccountFromFacebookPage($pageId);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Instagram from Facebook Page exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search all social media platforms
     */
    public function searchAll(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:facebook,instagram,tiktok,twitter'
        ]);

        try {
            // Increase execution time limit for this operation
            // Instagram scraping can take 15+ minutes, so set a high limit
            set_time_limit(1200); // 20 minutes to allow for Instagram scraping
            
            $username = trim($request->input('username'));
            $selectedPlatforms = $request->input('platforms', ['facebook', 'instagram', 'tiktok', 'twitter']); // Default to all

            Log::info('Searching platforms', [
                'user_id' => Auth::id(),
                'username' => $username,
                'platforms' => $selectedPlatforms
            ]);

            $results = $this->socialMediaService->searchAllPlatforms($username, $selectedPlatforms);

            // Save platform data and Apify usage immediately after search
            // Save even if no platforms found (total_found = 0) to track Apify usage for failed searches
            // Wrap in try-catch to ensure save failures don't affect the search response
            if (isset($results['platforms'])) {
                try {
                    $apifyUsage = $results['apify_usage'] ?? [];
                    // Save Apify usage even if no platforms were found (to track failed scraping attempts)
                    if (!empty($apifyUsage) || $results['total_found'] > 0) {
                        $this->savePlatformData($username, $results['platforms'], $apifyUsage);
                    }
                } catch (\Exception $saveException) {
                    // Log but don't fail the request - search was successful
                    Log::error('Failed to save platform data after search', [
                        'username' => $username,
                        'user_id' => Auth::id(),
                        'error' => $saveException->getMessage(),
                        'trace' => $saveException->getTraceAsString()
                    ]);
                }
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Search all platforms exception', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check status of a running Apify actor run
     */
    public function checkRunStatus(Request $request)
    {
        $request->validate([
            'run_id' => 'required|string',
            'platform' => 'nullable|string|in:instagram,facebook,tiktok,twitter'
        ]);

        try {
            $runId = $request->input('run_id');
            $platform = $request->input('platform', 'instagram');

            $result = $this->socialMediaService->checkApifyRunStatus($runId, $platform);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Check run status exception', [
                'run_id' => $request->input('run_id'),
                'exception' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save platform data after search (without AI analysis)
     */
    protected function savePlatformData($username, $platformData, $apifyUsage = [])
    {
        try {
            // Calculate Apify usage totals
            $apifyCallsCount = count($apifyUsage);
            $apifyTotalCost = 0.00;
            $apifyTotalResponseTime = 0.00;
            $apifySuccessfulCalls = 0;
            $apifyFailedCalls = 0;
            
            foreach ($apifyUsage as $usage) {
                if (isset($usage['cost'])) {
                    $apifyTotalCost += (float) $usage['cost'];
                }
                if (isset($usage['response_time'])) {
                    $apifyTotalResponseTime += (float) $usage['response_time'];
                }
                if (isset($usage['success'])) {
                    if ($usage['success']) {
                        $apifySuccessfulCalls++;
                    } else {
                        $apifyFailedCalls++;
                    }
                }
            }
            
            // Check if we already have platform data for this username
            // Prefer records without AI analysis (PENDING status from searchAll)
            // Use orderBy('id', 'desc') instead of latest() to avoid sorting large JSON fields
            // This prevents "Out of sort memory" errors
            $existing = SocialMediaAnalysis::where('username', $username)
                ->where('user_id', Auth::id())
                ->where(function($query) {
                    // Prefer records without AI analysis (PENDING status from searchAll)
                    $query->where(function($q) {
                        $q->whereNull('ai_analysis')
                          ->where('status', SocialMediaAnalysis::STATUS_PENDING);
                    })
                    ->orWhere('status', SocialMediaAnalysis::STATUS_FAILED);
                })
                ->where('status', '!=', SocialMediaAnalysis::STATUS_PROCESSING) // Don't update processing ones
                ->orderBy('id', 'desc')
                ->first();

            if ($existing) {
                // Update existing record with fresh platform data
                $existing->update([
                    'platform_data' => $platformData,
                    'status' => SocialMediaAnalysis::STATUS_PENDING, // Reset to pending
                    'apify_calls_count' => $apifyCallsCount,
                    'apify_usage_details' => $apifyUsage,
                    'apify_total_cost' => round($apifyTotalCost, 6),
                    'apify_total_response_time' => round($apifyTotalResponseTime, 4),
                    'updated_at' => now()
                ]);
                
                Log::info('Updated existing platform data', [
                    'analysis_id' => $existing->id,
                    'username' => $username,
                    'apify_calls' => $apifyCallsCount
                ]);
            } else {
                // Check if there's a completed analysis - if so, create a new one for fresh search
                $hasCompleted = SocialMediaAnalysis::where('username', $username)
                    ->where('user_id', Auth::id())
                    ->where('status', SocialMediaAnalysis::STATUS_COMPLETED)
                    ->exists();

                if ($hasCompleted) {
                    // Create new record for fresh search (user might want to re-analyze with updated data)
                    $analysis = SocialMediaAnalysis::create([
                        'username' => $username,
                        'platform_data' => $platformData,
                        'user_id' => Auth::id(),
                        'status' => SocialMediaAnalysis::STATUS_PENDING,
                        'ai_analysis' => null,
                        'model_used' => null,
                        'processing_time' => null,
                        'apify_calls_count' => $apifyCallsCount,
                        'apify_usage_details' => $apifyUsage,
                        'apify_total_cost' => round($apifyTotalCost, 6),
                        'apify_total_response_time' => round($apifyTotalResponseTime, 4)
                    ]);
                    
                    Log::info('Created new platform data record (previous analysis exists)', [
                        'analysis_id' => $analysis->id,
                        'username' => $username,
                        'apify_calls' => $apifyCallsCount
                    ]);
                } else {
                    // Create new record with platform data
                    $analysis = SocialMediaAnalysis::create([
                        'username' => $username,
                        'platform_data' => $platformData,
                        'user_id' => Auth::id(),
                        'status' => SocialMediaAnalysis::STATUS_PENDING,
                        'ai_analysis' => null,
                        'model_used' => null,
                        'processing_time' => null,
                        'apify_calls_count' => $apifyCallsCount,
                        'apify_usage_details' => $apifyUsage,
                        'apify_total_cost' => round($apifyTotalCost, 6),
                        'apify_total_response_time' => round($apifyTotalResponseTime, 4)
                    ]);
                    
                    Log::info('Saved new platform data', [
                        'analysis_id' => $analysis->id,
                        'username' => $username,
                        'apify_calls' => $apifyCallsCount
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to save platform data', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            // Don't throw - search should still succeed even if save fails
        }
    }

    /**
     * Re-analyze an existing social media analysis
     */
    public function reAnalyze(Request $request, SocialMediaAnalysis $socialMediaAnalysis)
    {
        // Check if user owns this analysis
        // Fix: Convert both values to integers for comparison to handle string vs integer mismatch
        if ((int)Auth::id() !== (int)$socialMediaAnalysis->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access to this analysis.'
            ], 403);
        }

        // Check if platform data exists
        if (!$socialMediaAnalysis->platform_data || !is_array($socialMediaAnalysis->platform_data)) {
            return response()->json([
                'success' => false,
                'error' => 'No platform data available for re-analysis. Please search again first.'
            ], 400);
        }

        $startTime = microtime(true);
        $username = $socialMediaAnalysis->username;
        $platformData = $socialMediaAnalysis->platform_data;
        
        // Get selected platforms and analysis type from request
        $selectedPlatforms = $request->input('selected_platforms', null);
        $analysisType = $request->input('analysis_type', 'professional');
        
        // Validate analysis type
        if (!in_array($analysisType, ['professional', 'political'])) {
            $analysisType = 'professional';
        }
        
        // Filter platform data based on selected platforms
        if ($selectedPlatforms && is_array($selectedPlatforms) && count($selectedPlatforms) > 0) {
            $filteredPlatformData = [];
            foreach ($selectedPlatforms as $platform) {
                if (isset($platformData[$platform])) {
                    $filteredPlatformData[$platform] = $platformData[$platform];
                }
            }
            $platformData = $filteredPlatformData;
        }

        try {
            // Create a new analysis record for re-analysis (preserve history)
            $newAnalysis = SocialMediaAnalysis::create([
                'username' => $username,
                'platform_data' => $platformData,
                'user_id' => Auth::id(),
                'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
            ]);

            // Start analytics tracking
            $analytics = $this->analyticsService->startAnalysisWithoutPrediction(Auth::id(), [
                'text' => $this->prepareAnalysisText($platformData, $analysisType),
                'analysis_type' => 'social-media-analysis',
                'social_media_analysis_id' => $newAnalysis->id,
            ]);

            // Prepare analysis text with analysis type
            $analysisText = $this->prepareAnalysisText($platformData, $analysisType);

            // Get AI service
            $aiService = \App\Services\AIServiceFactory::create();

            // Perform AI analysis (pass analytics for tracking)
            $aiResult = $aiService->analyzeText($analysisText, 'social-media-analysis', null, null, $analytics, null);

            $endTime = microtime(true);
            $processingTime = $endTime - $startTime;
            
            // Complete analytics tracking
            if ($analytics) {
                $this->analyticsService->completeAnalysis($analytics, [
                    'total_processing_time' => $processingTime,
                    'api_error_message' => (is_array($aiResult) && isset($aiResult['title'])) ? null : 'Analysis failed'
                ]);
            }

            // Parse AI response
            $analysisResult = null;
            if (is_string($aiResult)) {
                // Try to decode JSON response
                $decoded = json_decode($aiResult, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $analysisResult = $decoded;
                } else {
                    // If not JSON, treat as plain text
                    $analysisResult = ['analysis' => $aiResult];
                }
            } elseif (is_array($aiResult)) {
                $analysisResult = $aiResult;
            }

            // Store analysis type in result if it's an array
            if (is_array($analysisResult)) {
                $analysisResult['analysis_type'] = $analysisType;
            }

            // Update analysis record
            $newAnalysis->update([
                'ai_analysis' => $analysisResult,
                'status' => SocialMediaAnalysis::STATUS_COMPLETED,
                'processing_time' => $processingTime,
                'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
            ]);

            Log::info('Re-analysis completed', [
                'original_analysis_id' => $socialMediaAnalysis->id,
                'new_analysis_id' => $newAnalysis->id,
                'username' => $username,
                'analysis_type' => $analysisType,
                'processing_time' => $processingTime
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Re-analysis completed successfully.',
                'analysis_id' => $newAnalysis->id,
                'analysis' => $analysisResult
            ]);

        } catch (\Exception $e) {
            // Calculate processing time for analytics
            $processingTime = round(microtime(true) - $startTime, 3);
            
            // Complete analytics tracking with error
            if (isset($analytics) && $analytics) {
                $this->analyticsService->completeAnalysis($analytics, [
                    'total_processing_time' => $processingTime,
                    'api_error_message' => 'Re-analysis failed: ' . $e->getMessage()
                ]);
            }
            
            Log::error('Re-analysis failed', [
                'analysis_id' => $socialMediaAnalysis->id,
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update status to failed
            if (isset($newAnalysis)) {
                $newAnalysis->update([
                    'status' => SocialMediaAnalysis::STATUS_FAILED
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Re-analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing platform data for a username
     */
    public function getExistingPlatformData(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);

        try {
            $username = trim($request->input('username'));

            // Find most recent analysis with platform data for this username
            // Use orderBy('id', 'desc') instead of latest() to avoid sorting large JSON fields
            $analysis = SocialMediaAnalysis::where('username', $username)
                ->where('user_id', Auth::id())
                ->whereNotNull('platform_data')
                ->orderBy('id', 'desc')
                ->first();

            if ($analysis && $analysis->platform_data) {
                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'analysis_id' => $analysis->id,
                    'platform_data' => $analysis->platform_data,
                    'has_ai_analysis' => !is_null($analysis->ai_analysis),
                    'created_at' => $analysis->created_at->toIso8601String(),
                    'updated_at' => $analysis->updated_at->toIso8601String()
                ]);
            }

            return response()->json([
                'success' => true,
                'exists' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Get existing platform data exception', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform AI analysis on social media data
     */
    public function aiAnalysis(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'platform_data' => 'nullable|array',
            'use_existing' => 'nullable|boolean',
            'analysis_type' => 'nullable|in:professional,political'
        ]);

        $startTime = microtime(true);
        $username = $request->input('username');
        $useExisting = $request->input('use_existing', false);
        $platformData = $request->input('platform_data');
        $analysisType = $request->input('analysis_type', 'professional');
        
        // Validate analysis type
        if (!in_array($analysisType, ['professional', 'political'])) {
            $analysisType = 'professional';
        }

        try {
            // If use_existing is true, try to get existing platform data
            if ($useExisting) {
                // Use orderBy('id', 'desc') instead of latest() to avoid sorting large JSON fields
                $existing = SocialMediaAnalysis::where('username', $username)
                    ->where('user_id', Auth::id())
                    ->whereNotNull('platform_data')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($existing && $existing->platform_data) {
                    $platformData = $existing->platform_data;
                    $analysis = $existing;
                    // Update status to processing
                    $analysis->update([
                        'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                        'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'No existing platform data found for this username. Please search first.'
                    ], 400);
                }
            } else {
                // Use provided platform data or get from existing record
                if (!$platformData) {
                    // Use orderBy('id', 'desc') instead of latest() to avoid sorting large JSON fields
                    $existing = SocialMediaAnalysis::where('username', $username)
                        ->where('user_id', Auth::id())
                        ->whereNotNull('platform_data')
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($existing && $existing->platform_data) {
                        $platformData = $existing->platform_data;
                        $analysis = $existing;
                        $analysis->update([
                            'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'error' => 'Platform data is required. Please search first or provide platform data.'
                        ], 400);
                    }
                } else {
                    // Create or update analysis record with provided platform data
                    // First, try to find the most recent record for this username (created by searchAll)
                    // Prefer records without AI analysis, but also check pending ones
                    // Use orderBy('id', 'desc') instead of latest() to avoid sorting large JSON fields
                    $existing = SocialMediaAnalysis::where('username', $username)
                        ->where('user_id', Auth::id())
                        ->where(function($query) {
                            // Prefer records without AI analysis, or with pending status
                            $query->whereNull('ai_analysis')
                                  ->orWhere('status', SocialMediaAnalysis::STATUS_PENDING);
                        })
                        ->where('status', '!=', SocialMediaAnalysis::STATUS_PROCESSING)
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($existing) {
                        // Update existing record instead of creating a new one
                        $analysis = $existing;
                        $analysis->update([
                            'platform_data' => $platformData,
                            'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
                        ]);
                        
                        Log::info('Updated existing social media analysis for AI processing', [
                            'analysis_id' => $analysis->id,
                            'username' => $username,
                            'previous_status' => $existing->status
                        ]);
                    } else {
                        // Only create new record if no existing one found
                        $analysis = SocialMediaAnalysis::create([
                            'username' => $username,
                            'platform_data' => $platformData,
                            'user_id' => Auth::id(),
                            'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
                        ]);
                        
                        Log::info('Created new social media analysis for AI processing', [
                            'analysis_id' => $analysis->id,
                            'username' => $username
                        ]);
                    }
                }
            }
            
            Log::info('AI analysis requested for social media', [
                'user_id' => Auth::id(),
                'analysis_id' => $analysis->id,
                'username' => $username,
                'platforms' => array_keys($platformData)
            ]);

            // Prepare text data from all platforms with analysis type
            $analysisText = $this->prepareAnalysisText($platformData, $analysisType);
            
            // Start analytics tracking
            $analytics = $this->analyticsService->startAnalysisWithoutPrediction(Auth::id(), [
                'text' => $analysisText,
                'analysis_type' => 'social-media-analysis',
                'social_media_analysis_id' => $analysis->id,
            ]);

            // Get AI service
            $aiService = \App\Services\AIServiceFactory::create();
            
            // Perform AI analysis (pass analytics for tracking)
            $result = $aiService->analyzeText(
                $analysisText,
                'social-media-analysis',
                null,
                null,
                $analytics,
                null
            );

            // Calculate processing time
            $processingTime = round(microtime(true) - $startTime, 3);
            
            // Complete analytics tracking
            if ($analytics) {
                $this->analyticsService->completeAnalysis($analytics, [
                    'total_processing_time' => $processingTime,
                    'api_error_message' => isset($result['title']) ? null : 'Analysis failed'
                ]);
            }
            
            // Store analysis type in result if it's an array
            if (is_array($result)) {
                $result['analysis_type'] = $analysisType;
            }

            // Update analysis record with results
            $analysis->update([
                'ai_analysis' => $result,
                'status' => isset($result['title']) ? SocialMediaAnalysis::STATUS_COMPLETED : SocialMediaAnalysis::STATUS_FAILED,
                'processing_time' => $processingTime,
                'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
            ]);

            return response()->json([
                'success' => true,
                'analysis' => $result,
                'analysis_id' => $analysis->id
            ]);

        } catch (\Exception $e) {
            // Calculate processing time for analytics
            $processingTime = round(microtime(true) - $startTime, 3);
            
            // Complete analytics tracking with error
            if (isset($analytics) && $analytics) {
                $this->analyticsService->completeAnalysis($analytics, [
                    'total_processing_time' => $processingTime,
                    'api_error_message' => 'Analysis failed: ' . $e->getMessage()
                ]);
            }
            
            Log::error('AI analysis exception', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update analysis record if it was created
            if (isset($analysis)) {
                $analysis->update([
                    'status' => SocialMediaAnalysis::STATUS_FAILED
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare analysis text from platform data
     */
    protected function prepareAnalysisText($platformData, $analysisType = 'professional')
    {
        if ($analysisType === 'political') {
            $text = "SOCIAL MEDIA PROFILE ANALYSIS REQUEST\n\n";
            $text .= "ANALYSIS TYPE: POLITICAL PROFILE ANALYSIS\n\n";
            $text .= "Please analyze the following social media profile data across multiple platforms to assess the person's POLITICAL VIEWS and POLITICAL INVOLVEMENT.\n\n";
            $text .= "Focus on:\n";
            $text .= "- Their political views, opinions, and ideologies\n";
            $text .= "- Their level of political involvement and engagement\n";
            $text .= "- Their political activities and participation\n";
            $text .= "- Their political affiliations and associations\n\n";
        } else {
            $text = "SOCIAL MEDIA PROFILE ANALYSIS REQUEST\n\n";
            $text .= "ANALYSIS TYPE: PROFESSIONAL ANALYSIS\n\n";
            $text .= "Please analyze the following social media profile data across multiple platforms to provide a comprehensive professional assessment.\n\n";
        }
        
        foreach ($platformData as $platform => $data) {
            if (!$data || !isset($data['data'])) {
                continue;
            }
            
            $platformName = ucfirst($platform);
            $profileData = $data['data'];
            
            $text .= "=== {$platformName} PROFILE ===\n\n";
            
            // Basic profile info
            if (isset($profileData['name'])) {
                $text .= "Name: {$profileData['name']}\n";
            }
            if (isset($profileData['username'])) {
                $text .= "Username: @{$profileData['username']}\n";
            }
            if (isset($profileData['bio']) || isset($profileData['biography']) || isset($profileData['about']) || isset($profileData['description'])) {
                $bio = $profileData['bio'] ?? $profileData['biography'] ?? $profileData['about'] ?? $profileData['description'] ?? '';
                $text .= "Bio/Description: {$bio}\n";
            }
            
            // Stats
            if (isset($profileData['followers_count'])) {
                $text .= "Followers: " . number_format($profileData['followers_count']) . "\n";
            }
            if (isset($profileData['stats'])) {
                $stats = $profileData['stats'];
                if (isset($stats['total_followers'])) {
                    $text .= "Total Followers: " . number_format($stats['total_followers']) . "\n";
                }
                if (isset($stats['total_media']) || isset($stats['total_videos'])) {
                    $mediaCount = $stats['total_media'] ?? $stats['total_videos'] ?? 0;
                    $text .= "Total Posts/Videos: " . number_format($mediaCount) . "\n";
                }
            }
            
            // Engagement metrics
            if (isset($profileData['engagement'])) {
                $engagement = $profileData['engagement'];
                $text .= "\nEngagement Metrics:\n";
                if (isset($engagement['engagement_rate'])) {
                    $text .= "- Engagement Rate: {$engagement['engagement_rate']}%\n";
                }
                if (isset($engagement['average_engagement_per_post'])) {
                    $text .= "- Average Engagement per Post: " . number_format($engagement['average_engagement_per_post']) . "\n";
                }
                if (isset($engagement['average_likes'])) {
                    $text .= "- Average Likes: " . number_format($engagement['average_likes']) . "\n";
                }
                if (isset($engagement['average_comments'])) {
                    $text .= "- Average Comments: " . number_format($engagement['average_comments']) . "\n";
                }
                if (isset($engagement['total_posts_analyzed'])) {
                    $text .= "- Posts Analyzed: " . number_format($engagement['total_posts_analyzed']) . "\n";
                }
            }
            
            // Recent posts/content
            $posts = $profileData['recent_posts'] ?? $profileData['recent_media'] ?? $profileData['recent_videos'] ?? [];
            if (!empty($posts) && count($posts) > 0) {
                $text .= "\nRecent Content (showing up to 20 posts):\n";
                $postCount = min(20, count($posts));
                for ($i = 0; $i < $postCount; $i++) {
                    $post = $posts[$i];
                    $text .= "\nPost " . ($i + 1) . ":\n";
                    if (isset($post['message']) || isset($post['text']) || isset($post['caption']) || isset($post['description'])) {
                        $content = $post['message'] ?? $post['text'] ?? $post['caption'] ?? $post['description'] ?? '';
                        $text .= "Content: " . substr($content, 0, 500) . "\n";
                    }
                    if (isset($post['created_time']) || isset($post['timestamp'])) {
                        $text .= "Date: " . ($post['created_time'] ?? $post['timestamp']) . "\n";
                    }
                    if (isset($post['likes']) || isset($post['like_count'])) {
                        $text .= "Likes: " . number_format($post['likes'] ?? $post['like_count'] ?? 0) . "\n";
                    }
                    if (isset($post['comments']) || isset($post['comments_count'])) {
                        $text .= "Comments: " . number_format($post['comments'] ?? $post['comments_count'] ?? 0) . "\n";
                    }
                    if (isset($post['shares']) || isset($post['share_count'])) {
                        $text .= "Shares: " . number_format($post['shares'] ?? $post['share_count'] ?? 0) . "\n";
                    }
                }
            }
            
            $text .= "\n" . str_repeat("=", 50) . "\n\n";
        }
        
        return $text;
    }

    /**
     * Delete a social media analysis
     */
    public function destroy(SocialMediaAnalysis $socialMediaAnalysis)
    {
        // Ensure the user owns this analysis
        // Fix: Convert both values to integers for comparison to handle string vs integer mismatch
        if ((int)Auth::id() !== (int)$socialMediaAnalysis->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized. You do not have permission to delete this analysis.'
            ], 403);
        }

        try {
            $username = $socialMediaAnalysis->username;
            $analysisId = $socialMediaAnalysis->id;
            
            // Preserve analytics by finding and keeping analytics records
            // Analytics records are independent (no foreign key), so they remain automatically
            // But we log this for clarity
            $analyticsCount = \App\Models\AnalysisAnalytics::where('user_id', Auth::id())
                ->where('analysis_type', 'social-media-analysis')
                ->where('created_at', '>=', $socialMediaAnalysis->created_at->subMinutes(5))
                ->where('created_at', '<=', $socialMediaAnalysis->updated_at->addMinutes(5))
                ->count();
            
            $socialMediaAnalysis->delete();

            Log::info('Social media analysis deleted', [
                'user_id' => Auth::id(),
                'analysis_id' => $analysisId,
                'username' => $username,
                'analytics_preserved' => $analyticsCount > 0,
                'analytics_count' => $analyticsCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Analysis deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting social media analysis', [
                'user_id' => Auth::id(),
                'analysis_id' => $socialMediaAnalysis->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to delete analysis. Please try again.'
            ], 500);
        }
    }

    /**
     * Export social media analysis to PDF
     */
    public function export(SocialMediaAnalysis $socialMediaAnalysis)
    {
        // Check if user owns this analysis
        if (!Auth::check()) {
            abort(401, 'User not authenticated.');
        }
        
        if ((int)Auth::id() !== (int)$socialMediaAnalysis->user_id) {
            abort(403, 'Unauthorized access to analysis.');
        }

        // Render the view as HTML first (this ensures all partials with charts are rendered)
        $html = view('social-media.export-pdf', compact('socialMediaAnalysis'))->render();
        
        // Generate PDF using the rendered HTML
        $pdf = Pdf::loadHTML($html);
        
        // Set PDF options for better formatting and page break handling
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'defaultMediaType' => 'screen',
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'dpi' => 150,
            'fontHeightRatio' => 0.9,
            'enable-smart-shrinking' => true,
            'enable-local-file-access' => true
        ]);
        
        // Add page numbers using DomPDF callback - runs on every page
        $dompdf = $pdf->getDomPDF();
        $dompdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_page', 'f' => function ($infos) {
                    $canvas = $infos["canvas"];
                    $fontMetrics = $infos["fontMetrics"];
                    $font = $fontMetrics->getFont("Times New Roman", "normal");
                    $size = 9;
                    $pageText = "{PAGE_NUM}";
                    $y = $canvas->get_height() - 24;
                    // Get page dimensions - A4 is 595.28 x 841.89 points (at 72 DPI)
                    // Account for 1.2cm margins (33.87 points each side)
                    $pageWidth = 595.28; // A4 width in points
                    $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                    // Center the text on the page, with adjustment to the right
                    $x = ($pageWidth / 2) - ($textWidth / 2) + 25; // +25 points to shift right
                    $canvas->page_text($x, $y, $pageText, $font, $size, array(0, 0, 0)); // Black color
                }
            ]
        ]);

        // Generate filename
        $filename = 'social_media_analysis_' . $socialMediaAnalysis->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }

    /**
     * Get rendered analysis HTML for modal display
     */
    public function getAnalysisHtml(SocialMediaAnalysis $socialMediaAnalysis)
    {
        // Check if user owns this analysis
        if (!Auth::check()) {
            abort(401, 'User not authenticated.');
        }
        
        if ((int)Auth::id() !== (int)$socialMediaAnalysis->user_id) {
            abort(403, 'Unauthorized access to analysis.');
        }

        if ($socialMediaAnalysis->status !== 'completed' || !$socialMediaAnalysis->ai_analysis) {
            return response()->json([
                'success' => false,
                'error' => 'Analysis not completed yet'
            ], 400);
        }

        // Render the analysis content using the same partials as the view page
        // Pass only socialMediaAnalysis - the partial will extract ai_analysis from it
        $html = view('social-media.partials.analysis-content', [
            'socialMediaAnalysis' => $socialMediaAnalysis
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}

