<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date')) : now()->subMonth();
        $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date')) : now();

        $analytics = $this->analyticsService->getSystemAnalytics($startDate, $endDate);

        return view('admin.analytics', compact('analytics'));
    }

    /**
     * Get analytics data as JSON for AJAX requests
     */
    public function getData(Request $request)
    {
        $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date')) : now()->subMonth();
        $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date')) : now();

        $analytics = $this->analyticsService->getSystemAnalytics($startDate, $endDate);

        return response()->json($analytics);
    }

    /**
     * Get user-specific analytics
     */
    public function getUserAnalytics(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date')) : now()->subMonth();
        $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date')) : now();

        $analytics = $this->analyticsService->getUserAnalytics($user, $startDate, $endDate);

        return response()->json($analytics);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date') ? \Carbon\Carbon::parse($request->get('start_date')) : now()->subMonth();
        $endDate = $request->get('end_date') ? \Carbon\Carbon::parse($request->get('end_date')) : now();

        $analytics = $this->analyticsService->getSystemAnalytics($startDate, $endDate);

        // Generate CSV export
        $filename = 'analytics_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($analytics) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Metric',
                'Value',
                'Description'
            ]);
            
            // Data
            fputcsv($file, ['Total Analyses', $analytics['total_analyses'], 'Total number of AI analyses performed']);
            fputcsv($file, ['Total Tokens', $analytics['total_tokens'], 'Total tokens consumed across all analyses']);
            fputcsv($file, ['Total Cost', '$' . number_format($analytics['total_cost'], 6), 'Estimated total cost in USD']);
            fputcsv($file, ['Success Rate', number_format($analytics['success_rate'], 1) . '%', 'Percentage of successful analyses']);
            fputcsv($file, ['Average Processing Time', number_format($analytics['average_processing_time'], 3) . 's', 'Average time to complete analysis']);
            fputcsv($file, ['Active Users', $analytics['active_users'], 'Number of unique users who performed analyses']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
