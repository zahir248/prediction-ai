<?php

namespace App\Services;

use App\Models\AnalysisAnalytics;
use App\Models\Prediction;
use App\Models\User;
use App\Models\SocialMediaAnalysis;
use App\Models\DataAnalysis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    /**
     * Start tracking an analysis (for predictions)
     */
    public function startAnalysis(Prediction $prediction, array $inputData = [])
    {
        try {
            $analytics = AnalysisAnalytics::create([
                'prediction_id' => $prediction->id,
                'user_id' => $prediction->user_id,
                'input_text_length' => strlen($inputData['text'] ?? ''),
                'scraped_urls_count' => count($inputData['source_urls'] ?? []),
                'uploaded_files_count' => count($inputData['uploaded_files'] ?? []),
                'total_file_size_bytes' => $this->calculateTotalFileSize($inputData['uploaded_files'] ?? []),
                'user_agent' => Request::userAgent(),
                'ip_address' => Request::ip(),
                'analysis_type' => $inputData['analysis_type'] ?? 'prediction-analysis',
                'prediction_horizon' => $inputData['prediction_horizon'] ?? null,
                'analysis_started_at' => now(),
                'api_endpoint' => config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'),
            ]);

            Log::info('Analysis tracking started', [
                'analytics_id' => $analytics->id,
                'prediction_id' => $prediction->id,
                'user_id' => $prediction->user_id
            ]);

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Failed to start analysis tracking', [
                'prediction_id' => $prediction->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Start tracking an analysis without a Prediction model (for social media and data analysis)
     */
    public function startAnalysisWithoutPrediction(int $userId, array $inputData = [])
    {
        try {
            // Get Apify usage if this is a social media analysis
            $apifyCallsCount = 0;
            $apifyPlatformsUsed = null;
            $apifyTotalCost = 0.00;
            $apifyTotalResponseTime = 0.00;
            $apifySuccessfulCalls = 0;
            $apifyFailedCalls = 0;
            
            if (isset($inputData['social_media_analysis_id'])) {
                $socialMediaAnalysis = \App\Models\SocialMediaAnalysis::find($inputData['social_media_analysis_id']);
                if ($socialMediaAnalysis) {
                    $apifyCallsCount = $socialMediaAnalysis->apify_calls_count ?? 0;
                    $apifyUsageDetails = $socialMediaAnalysis->apify_usage_details ?? [];
                    $apifyTotalCost = $socialMediaAnalysis->apify_total_cost ?? 0.00;
                    $apifyTotalResponseTime = $socialMediaAnalysis->apify_total_response_time ?? 0.00;
                    
                    // Extract platforms used
                    $platforms = [];
                    foreach ($apifyUsageDetails as $usage) {
                        if (isset($usage['platform'])) {
                            $platforms[] = $usage['platform'];
                        }
                        if (isset($usage['success'])) {
                            if ($usage['success']) {
                                $apifySuccessfulCalls++;
                            } else {
                                $apifyFailedCalls++;
                            }
                        }
                    }
                    $apifyPlatformsUsed = !empty($platforms) ? json_encode(array_unique($platforms)) : null;
                }
            }
            
            $analytics = AnalysisAnalytics::create([
                'prediction_id' => null,
                'user_id' => $userId,
                'input_text_length' => strlen($inputData['text'] ?? ''),
                'scraped_urls_count' => count($inputData['source_urls'] ?? []),
                'uploaded_files_count' => count($inputData['uploaded_files'] ?? []),
                'total_file_size_bytes' => $this->calculateTotalFileSize($inputData['uploaded_files'] ?? []),
                'user_agent' => Request::userAgent(),
                'ip_address' => Request::ip(),
                'analysis_type' => $inputData['analysis_type'] ?? 'unknown',
                'prediction_horizon' => null,
                'analysis_started_at' => now(),
                'api_endpoint' => config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'),
                'apify_calls_count' => $apifyCallsCount,
                'apify_platforms_used' => $apifyPlatformsUsed,
                'apify_total_cost' => round($apifyTotalCost, 6),
                'apify_total_response_time' => round($apifyTotalResponseTime, 4),
                'apify_successful_calls' => $apifySuccessfulCalls,
                'apify_failed_calls' => $apifyFailedCalls,
            ]);

            Log::info('Analysis tracking started (without prediction)', [
                'analytics_id' => $analytics->id,
                'user_id' => $userId,
                'analysis_type' => $inputData['analysis_type'] ?? 'unknown',
                'apify_calls' => $apifyCallsCount
            ]);

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Failed to start analysis tracking (without prediction)', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update analytics with API response data
     */
    public function updateWithApiResponse(AnalysisAnalytics $analytics, array $apiData)
    {
        try {
            $updateData = [
                'output_tokens' => $apiData['output_tokens'] ?? 0,
                'api_response_time' => $apiData['api_response_time'] ?? 0,
                'http_status_code' => $apiData['http_status_code'] ?? null,
                'retry_attempts' => $apiData['retry_attempts'] ?? 0,
                'retry_reason' => $apiData['retry_reason'] ?? null,
            ];

            // Calculate input tokens (approximate)
            $updateData['input_tokens'] = $this->estimateInputTokens($analytics->input_text_length);
            
            // Calculate total tokens and cost
            $updateData['total_tokens'] = $updateData['input_tokens'] + $updateData['output_tokens'];
            
            $analytics->update($updateData);
            
            // Calculate and update cost
            $analytics->calculateEstimatedCost();
            $analytics->save();

            Log::info('Analysis analytics updated with API response', [
                'analytics_id' => $analytics->id,
                'total_tokens' => $updateData['total_tokens'],
                'estimated_cost' => $analytics->estimated_cost
            ]);

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Failed to update analytics with API response', [
                'analytics_id' => $analytics->id,
                'error' => $e->getMessage()
            ]);
            return $analytics;
        }
    }

    /**
     * Complete analysis tracking
     */
    public function completeAnalysis(AnalysisAnalytics $analytics, array $completionData = [])
    {
        try {
            $updateData = [
                'analysis_completed_at' => now(),
                'total_processing_time' => $completionData['total_processing_time'] ?? 0,
            ];

            if (isset($completionData['api_error_message'])) {
                $updateData['api_error_message'] = $completionData['api_error_message'];
            }

            $analytics->update($updateData);

            Log::info('Analysis tracking completed', [
                'analytics_id' => $analytics->id,
                'total_processing_time' => $updateData['total_processing_time']
            ]);

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Failed to complete analysis tracking', [
                'analytics_id' => $analytics->id,
                'error' => $e->getMessage()
            ]);
            return $analytics;
        }
    }

    /**
     * Get comprehensive analytics for a user
     */
    public function getUserAnalytics(User $user, $startDate = null, $endDate = null)
    {
        // Get analytics from analysis_analytics table (tracks all statuses) for token/cost metrics
        $analyticsBreakdown = $this->getAnalysisTypeBreakdown($user->id, $startDate, $endDate);
        
        // Count directly from actual analysis tables to match history page counts
        // This ensures we show the same counts as the history pages and includes all historical data
        $socialMediaQuery = SocialMediaAnalysis::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $socialMediaQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $socialMediaCountFromTable = $socialMediaQuery->count(); // Count all statuses: pending, processing, completed, failed
            
        $dataAnalysisQuery = DataAnalysis::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $dataAnalysisQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $dataAnalysisCountFromTable = $dataAnalysisQuery->count(); // Count all statuses: pending, processing, completed, failed
        
        // Get predictions count - prefer analytics table if available, otherwise count from table
        $predictionsCountFromAnalytics = $analyticsBreakdown['prediction-analysis'] ?? 0;
        $predictionsQuery = Prediction::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $predictionsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $predictionsCountFromTable = $predictionsQuery->count(); // Count all statuses
        
        // Use counts from actual tables to match history page behavior
        // This ensures consistency between analytics page and history pages
        $analyticsBreakdown['social-media-analysis'] = $socialMediaCountFromTable;
        $analyticsBreakdown['data-analysis'] = $dataAnalysisCountFromTable;
        $analyticsBreakdown['prediction-analysis'] = $predictionsCountFromTable > 0 ? $predictionsCountFromTable : $predictionsCountFromAnalytics;

        // Calculate total analyses from all sources (all statuses included)
        $totalAnalyses = array_sum($analyticsBreakdown);

        // Calculate precise success rate from actual analysis tables
        // This gives accurate success rate across all three modules
        $socialMediaCompletedQuery = SocialMediaAnalysis::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $socialMediaCompletedQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $socialMediaCompleted = $socialMediaCompletedQuery->where('status', SocialMediaAnalysis::STATUS_COMPLETED)->count();
        $socialMediaFailed = SocialMediaAnalysis::where('user_id', $user->id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', SocialMediaAnalysis::STATUS_FAILED)
            ->count();
        
        $dataAnalysisCompletedQuery = DataAnalysis::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $dataAnalysisCompletedQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $dataAnalysisCompleted = $dataAnalysisCompletedQuery->where('status', DataAnalysis::STATUS_COMPLETED)->count();
        $dataAnalysisFailed = DataAnalysis::where('user_id', $user->id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', DataAnalysis::STATUS_FAILED)
            ->count();
        
        $predictionsCompletedQuery = Prediction::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $predictionsCompletedQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $predictionsCompleted = $predictionsCompletedQuery->where('status', 'completed')->count();
        $predictionsFailed = Prediction::where('user_id', $user->id)
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'failed')
            ->count();
        
        // Calculate success rate from actual tables (more accurate)
        $totalCompleted = $socialMediaCompleted + $dataAnalysisCompleted + $predictionsCompleted;
        $totalFailed = $socialMediaFailed + $dataAnalysisFailed + $predictionsFailed;
        $totalFinished = $totalCompleted + $totalFailed;
        
        // If we have tracked analyses in analytics table, use that for more detailed metrics
        // Otherwise fall back to table-based calculation
        $analyticsSuccessRate = AnalysisAnalytics::getSuccessRate($user->id, $startDate, $endDate);
        
        // Use table-based success rate (more accurate as it includes all analyses)
        // Fall back to analytics table if no finished analyses in tables
        $successRate = $totalFinished > 0 
            ? round(($totalCompleted / $totalFinished) * 100, 1) 
            : ($analyticsSuccessRate > 0 ? $analyticsSuccessRate : 0);

        return [
            'total_analyses' => $totalAnalyses,
            
            // AI Usage Metrics (separate from Apify)
            'total_tokens' => AnalysisAnalytics::getTotalTokenUsage($user->id, $startDate, $endDate),
            'total_cost' => AnalysisAnalytics::getTotalCost($user->id, $startDate, $endDate),
            
            // Apify Usage Metrics (separate from AI)
            'apify_total_calls' => AnalysisAnalytics::getTotalApifyCalls($user->id, $startDate, $endDate),
            'apify_total_cost' => AnalysisAnalytics::getTotalApifyCost($user->id, $startDate, $endDate),
            'apify_total_response_time' => AnalysisAnalytics::getTotalApifyResponseTime($user->id, $startDate, $endDate),
            
            'average_processing_time' => AnalysisAnalytics::getAverageProcessingTime($user->id, $startDate, $endDate) ?? 0,
            
            'success_rate' => $successRate,
            
            'token_usage_trend' => $this->getTokenUsageTrend($user->id, $startDate, $endDate),
            
            'cost_trend' => $this->getCostTrend($user->id, $startDate, $endDate),
            
            'analysis_type_breakdown' => $analyticsBreakdown,
            
            'prediction_horizon_breakdown' => $this->getPredictionHorizonBreakdown($user->id, $startDate, $endDate),
        ];
    }

    /**
     * Get system-wide analytics
     */
    public function getSystemAnalytics($startDate = null, $endDate = null, $organization = null)
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return [
            'total_analyses' => AnalysisAnalytics::byDateRange($startDate, $endDate)->byOrganization($organization)->count(),
            
            'total_tokens' => AnalysisAnalytics::getTotalTokenUsage(null, $startDate, $endDate, $organization),
            
            'total_cost' => AnalysisAnalytics::getTotalCost(null, $startDate, $endDate, $organization),
            
            'average_processing_time' => AnalysisAnalytics::getAverageProcessingTime(null, $startDate, $endDate, $organization) ?? 0,
            
            'success_rate' => AnalysisAnalytics::getSuccessRate(null, $startDate, $endDate, $organization),
            
            'active_users' => AnalysisAnalytics::byDateRange($startDate, $endDate)->byOrganization($organization)
                ->distinct('user_id')
                ->count('user_id'),
            
            'analysis_type_breakdown' => $this->getAnalysisTypeBreakdown(null, $startDate, $endDate, $organization),
            
            'prediction_horizon_breakdown' => $this->getPredictionHorizonBreakdown(null, $startDate, $endDate, $organization),
            
            'top_users_by_usage' => $this->getTopUsersByUsage($startDate, $endDate, $organization),
            
            'organization_breakdown' => $this->getOrganizationBreakdown($startDate, $endDate, $organization),
            
            'daily_usage_trend' => $this->getDailyUsageTrend($startDate, $endDate, $organization),
            
            'detailed_records' => $this->getDetailedRecords($startDate, $endDate, $organization),
        ];
    }

    /**
     * Get token usage trend over time
     */
    private function getTokenUsageTrend($userId, $startDate, $endDate)
    {
        $query = AnalysisAnalytics::byUser($userId);
        if ($startDate && $endDate) {
            $query = $query->byDateRange($startDate, $endDate);
        }
        return $query->selectRaw('DATE(created_at) as date, SUM(total_tokens) as total_tokens')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_tokens', 'date')
            ->toArray();
    }

    /**
     * Get cost trend over time
     */
    private function getCostTrend($userId, $startDate, $endDate)
    {
        $query = AnalysisAnalytics::byUser($userId);
        if ($startDate && $endDate) {
            $query = $query->byDateRange($startDate, $endDate);
        }
        return $query->selectRaw('DATE(created_at) as date, SUM(estimated_cost) as total_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_cost', 'date')
            ->toArray();
    }

    /**
     * Get analysis type breakdown
     */
    private function getAnalysisTypeBreakdown($userId, $startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::query();
        
        if ($startDate && $endDate) {
            $query = $query->byDateRange($startDate, $endDate);
        }
        
        if ($userId) {
            $query = $query->byUser($userId);
        }
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        return $query->selectRaw('analysis_type, COUNT(*) as count')
            ->groupBy('analysis_type')
            ->pluck('count', 'analysis_type')
            ->toArray();
    }

    /**
     * Get prediction horizon breakdown
     */
    private function getPredictionHorizonBreakdown($userId, $startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::query();
        
        if ($startDate && $endDate) {
            $query = $query->byDateRange($startDate, $endDate);
        }
        
        if ($userId) {
            $query = $query->byUser($userId);
        }
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        return $query->selectRaw('prediction_horizon, COUNT(*) as count')
            ->groupBy('prediction_horizon')
            ->pluck('count', 'prediction_horizon')
            ->toArray();
    }

    /**
     * Get top users by usage
     */
    private function getTopUsersByUsage($startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::byDateRange($startDate, $endDate);
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        $users = $query->selectRaw('user_id, SUM(total_tokens) as total_tokens, COUNT(*) as analysis_count')
            ->groupBy('user_id')
            ->orderByDesc('total_tokens')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();
        
        // Calculate percentages
        $totalTokens = $users->sum('total_tokens');
        return $users->map(function ($user) use ($totalTokens) {
            $user->percentage = $totalTokens > 0 ? ($user->total_tokens / $totalTokens) * 100 : 0;
            return $user;
        });
    }

    /**
     * Get organization breakdown
     */
    private function getOrganizationBreakdown($startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::query()
            ->whereBetween('analysis_analytics.created_at', [$startDate, $endDate]);
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        return $query->join('users', 'analysis_analytics.user_id', '=', 'users.id')
            ->selectRaw('users.organization, COUNT(*) as count, SUM(analysis_analytics.total_tokens) as total_tokens, SUM(analysis_analytics.estimated_cost) as total_cost')
            ->groupBy('users.organization')
            ->orderByDesc('total_tokens')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->organization => [
                    'count' => $item->count,
                    'total_tokens' => $item->total_tokens,
                    'total_cost' => $item->total_cost
                ]];
            })
            ->toArray();
    }

    /**
     * Get daily usage trend
     */
    private function getDailyUsageTrend($startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::byDateRange($startDate, $endDate);
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as analysis_count, SUM(total_tokens) as total_tokens')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => [
                    'analysis_count' => $item->analysis_count,
                    'total_tokens' => $item->total_tokens
                ]];
            })
            ->toArray();
    }

    /**
     * Calculate total file size from uploaded files
     */
    private function calculateTotalFileSize(array $uploadedFiles)
    {
        $totalSize = 0;
        foreach ($uploadedFiles as $file) {
            if (isset($file['size'])) {
                $totalSize += $file['size'];
            }
        }
        return $totalSize;
    }

    /**
     * Estimate input tokens based on text length
     * Rough approximation: 1 token ≈ 4 characters for English text
     */
    private function estimateInputTokens($textLength)
    {
        return (int) ceil($textLength / 4);
    }

    /**
     * Get detailed analytics records for admin view
     */
    private function getDetailedRecords($startDate, $endDate, $organization = null)
    {
        $query = AnalysisAnalytics::byDateRange($startDate, $endDate);
        
        if ($organization) {
            $query = $query->byOrganization($organization);
        }
        
        return $query->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(100) // Limit to prevent performance issues
            ->get();
    }
}
