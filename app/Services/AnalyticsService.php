<?php

namespace App\Services;

use App\Models\AnalysisAnalytics;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    /**
     * Start tracking an analysis
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
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return [
            'total_analyses' => AnalysisAnalytics::byUser($user->id)
                ->byDateRange($startDate, $endDate)
                ->count(),
            
            'total_tokens' => AnalysisAnalytics::getTotalTokenUsage($user->id, $startDate, $endDate),
            
            'total_cost' => AnalysisAnalytics::getTotalCost($user->id, $startDate, $endDate),
            
            'average_processing_time' => AnalysisAnalytics::getAverageProcessingTime($user->id, $startDate, $endDate) ?? 0,
            
            'success_rate' => AnalysisAnalytics::getSuccessRate($user->id, $startDate, $endDate),
            
            'token_usage_trend' => $this->getTokenUsageTrend($user->id, $startDate, $endDate),
            
            'cost_trend' => $this->getCostTrend($user->id, $startDate, $endDate),
            
            'analysis_type_breakdown' => $this->getAnalysisTypeBreakdown($user->id, $startDate, $endDate),
            
            'prediction_horizon_breakdown' => $this->getPredictionHorizonBreakdown($user->id, $startDate, $endDate),
        ];
    }

    /**
     * Get system-wide analytics
     */
    public function getSystemAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return [
            'total_analyses' => AnalysisAnalytics::byDateRange($startDate, $endDate)->count(),
            
            'total_tokens' => AnalysisAnalytics::getTotalTokenUsage(null, $startDate, $endDate),
            
            'total_cost' => AnalysisAnalytics::getTotalCost(null, $startDate, $endDate),
            
            'average_processing_time' => AnalysisAnalytics::getAverageProcessingTime(null, $startDate, $endDate) ?? 0,
            
            'success_rate' => AnalysisAnalytics::getSuccessRate(null, $startDate, $endDate),
            
            'active_users' => AnalysisAnalytics::byDateRange($startDate, $endDate)
                ->distinct('user_id')
                ->count('user_id'),
            
            'analysis_type_breakdown' => $this->getAnalysisTypeBreakdown(null, $startDate, $endDate),
            
            'prediction_horizon_breakdown' => $this->getPredictionHorizonBreakdown(null, $startDate, $endDate),
            
            'top_users_by_usage' => $this->getTopUsersByUsage($startDate, $endDate),
            
            'daily_usage_trend' => $this->getDailyUsageTrend($startDate, $endDate),
            
            'detailed_records' => $this->getDetailedRecords($startDate, $endDate),
        ];
    }

    /**
     * Get token usage trend over time
     */
    private function getTokenUsageTrend($userId, $startDate, $endDate)
    {
        return AnalysisAnalytics::byUser($userId)
            ->byDateRange($startDate, $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_tokens) as total_tokens')
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
        return AnalysisAnalytics::byUser($userId)
            ->byDateRange($startDate, $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(estimated_cost) as total_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_cost', 'date')
            ->toArray();
    }

    /**
     * Get analysis type breakdown
     */
    private function getAnalysisTypeBreakdown($userId, $startDate, $endDate)
    {
        $query = AnalysisAnalytics::byDateRange($startDate, $endDate);
        
        if ($userId) {
            $query = $query->byUser($userId);
        }
        
        return $query->selectRaw('analysis_type, COUNT(*) as count')
            ->groupBy('analysis_type')
            ->pluck('count', 'analysis_type')
            ->toArray();
    }

    /**
     * Get prediction horizon breakdown
     */
    private function getPredictionHorizonBreakdown($userId, $startDate, $endDate)
    {
        $query = AnalysisAnalytics::byDateRange($startDate, $endDate);
        
        if ($userId) {
            $query = $query->byUser($userId);
        }
        
        return $query->selectRaw('prediction_horizon, COUNT(*) as count')
            ->groupBy('prediction_horizon')
            ->pluck('count', 'prediction_horizon')
            ->toArray();
    }

    /**
     * Get top users by usage
     */
    private function getTopUsersByUsage($startDate, $endDate)
    {
        return AnalysisAnalytics::byDateRange($startDate, $endDate)
            ->selectRaw('user_id, SUM(total_tokens) as total_tokens, COUNT(*) as analysis_count')
            ->groupBy('user_id')
            ->orderByDesc('total_tokens')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();
    }

    /**
     * Get daily usage trend
     */
    private function getDailyUsageTrend($startDate, $endDate)
    {
        return AnalysisAnalytics::byDateRange($startDate, $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as analysis_count, SUM(total_tokens) as total_tokens')
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
    private function getDetailedRecords($startDate, $endDate)
    {
        return AnalysisAnalytics::byDateRange($startDate, $endDate)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(100) // Limit to prevent performance issues
            ->get();
    }
}
