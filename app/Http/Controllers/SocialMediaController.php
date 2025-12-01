<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAnalysis;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SocialMediaController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
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
    public function history()
    {
        $analyses = Auth::user()->socialMediaAnalyses()->latest()->paginate(10);
        
        // Get total counts for stats
        $allAnalyses = Auth::user()->socialMediaAnalyses();
        $stats = [
            'total' => $allAnalyses->count(),
            'completed' => $allAnalyses->where('status', SocialMediaAnalysis::STATUS_COMPLETED)->count(),
            'processing' => $allAnalyses->where('status', SocialMediaAnalysis::STATUS_PROCESSING)->count(),
            'failed' => $allAnalyses->where('status', SocialMediaAnalysis::STATUS_FAILED)->count(),
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
            'username' => 'required|string'
        ]);

        try {
            // Increase execution time limit for this operation
            set_time_limit(600); // 10 minutes
            
            $username = trim($request->input('username'));

            Log::info('Searching all platforms', [
                'user_id' => Auth::id(),
                'username' => $username
            ]);

            $results = $this->socialMediaService->searchAllPlatforms($username);

            // Save platform data immediately after search (if we have results)
            if (isset($results['platforms']) && $results['total_found'] > 0) {
                $this->savePlatformData($username, $results['platforms']);
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
     * Save platform data after search (without AI analysis)
     */
    protected function savePlatformData($username, $platformData)
    {
        try {
            // Check if we already have platform data for this username
            // Prefer records without AI analysis, but also check completed ones to update with fresh data
            $existing = SocialMediaAnalysis::where('username', $username)
                ->where('user_id', Auth::id())
                ->where(function($query) {
                    $query->whereNull('ai_analysis')
                          ->orWhere('status', SocialMediaAnalysis::STATUS_FAILED);
                })
                ->where('status', '!=', SocialMediaAnalysis::STATUS_PROCESSING) // Don't update processing ones
                ->latest()
                ->first();

            if ($existing) {
                // Update existing record with fresh platform data
                $existing->update([
                    'platform_data' => $platformData,
                    'status' => SocialMediaAnalysis::STATUS_PENDING, // Reset to pending
                    'updated_at' => now()
                ]);
                
                Log::info('Updated existing platform data', [
                    'analysis_id' => $existing->id,
                    'username' => $username
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
                        'processing_time' => null
                    ]);
                    
                    Log::info('Created new platform data record (previous analysis exists)', [
                        'analysis_id' => $analysis->id,
                        'username' => $username
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
                        'processing_time' => null
                    ]);
                    
                    Log::info('Saved new platform data', [
                        'analysis_id' => $analysis->id,
                        'username' => $username
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
        
        // Get selected platforms from request
        $selectedPlatforms = $request->input('selected_platforms', null);
        
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

            // Prepare analysis text
            $analysisText = $this->prepareAnalysisText($platformData);

            // Get AI service
            $aiService = \App\Services\AIServiceFactory::create();

            // Perform AI analysis
            $aiResult = $aiService->analyzeText($analysisText, 'social-media-analysis');

            $endTime = microtime(true);
            $processingTime = $endTime - $startTime;

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
                'processing_time' => $processingTime
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Re-analysis completed successfully.',
                'analysis_id' => $newAnalysis->id,
                'analysis' => $analysisResult
            ]);

        } catch (\Exception $e) {
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
            $analysis = SocialMediaAnalysis::where('username', $username)
                ->where('user_id', Auth::id())
                ->whereNotNull('platform_data')
                ->latest()
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
            'use_existing' => 'nullable|boolean'
        ]);

        $startTime = microtime(true);
        $username = $request->input('username');
        $useExisting = $request->input('use_existing', false);
        $platformData = $request->input('platform_data');

        try {
            // If use_existing is true, try to get existing platform data
            if ($useExisting) {
                $existing = SocialMediaAnalysis::where('username', $username)
                    ->where('user_id', Auth::id())
                    ->whereNotNull('platform_data')
                    ->latest()
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
                    $existing = SocialMediaAnalysis::where('username', $username)
                        ->where('user_id', Auth::id())
                        ->whereNotNull('platform_data')
                        ->latest()
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
                    $existing = SocialMediaAnalysis::where('username', $username)
                        ->where('user_id', Auth::id())
                        ->whereNull('ai_analysis')
                        ->where('status', '!=', SocialMediaAnalysis::STATUS_PROCESSING)
                        ->latest()
                        ->first();

                    if ($existing) {
                        $analysis = $existing;
                        $analysis->update([
                            'platform_data' => $platformData,
                            'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
                        ]);
                    } else {
                        $analysis = SocialMediaAnalysis::create([
                            'username' => $username,
                            'platform_data' => $platformData,
                            'user_id' => Auth::id(),
                            'status' => SocialMediaAnalysis::STATUS_PROCESSING,
                            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider()
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

            // Get AI service
            $aiService = \App\Services\AIServiceFactory::create();
            
            // Prepare text data from all platforms
            $analysisText = $this->prepareAnalysisText($platformData);
            
            // Perform AI analysis
            $result = $aiService->analyzeText(
                $analysisText,
                'social-media-analysis',
                null,
                null,
                null,
                null
            );

            // Calculate processing time
            $processingTime = round(microtime(true) - $startTime, 3);

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
    protected function prepareAnalysisText($platformData)
    {
        $text = "SOCIAL MEDIA PROFILE ANALYSIS REQUEST\n\n";
        $text .= "Please analyze the following social media profile data across multiple platforms to provide a comprehensive professional assessment.\n\n";
        
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
            $socialMediaAnalysis->delete();

            Log::info('Social media analysis deleted', [
                'user_id' => Auth::id(),
                'analysis_id' => $socialMediaAnalysis->id,
                'username' => $username
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

        // Generate PDF using the analysis data
        $pdf = Pdf::loadView('social-media.export-pdf', compact('socialMediaAnalysis'));
        
        // Set PDF options for better formatting and page break handling
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'defaultMediaType' => 'screen',
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => false,
            'isJavascriptEnabled' => false,
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'dpi' => 150,
            'fontHeightRatio' => 0.9
        ]);

        // Generate filename
        $filename = 'social_media_analysis_' . $socialMediaAnalysis->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }
}

