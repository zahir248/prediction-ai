<?php

namespace App\Http\Controllers;

use App\Models\DataAnalysis;
use App\Services\DataAnalysisService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DataAnalysisController extends Controller
{
    protected $dataAnalysisService;
    protected $analyticsService;

    public function __construct(DataAnalysisService $dataAnalysisService, AnalyticsService $analyticsService)
    {
        $this->dataAnalysisService = $dataAnalysisService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show data analysis upload form
     */
    public function index()
    {
        return view('data-analysis.index');
    }

    /**
     * Show history of data analyses
     */
    public function history(Request $request)
    {
        $query = Auth::user()->dataAnalyses();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('file_name', 'like', '%' . $search . '%');
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
        
        $analyses = $query->select('id', 'file_name', 'status', 'ai_insights', 'created_at', 'processing_time')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get stats
        $stats = [
            'total' => Auth::user()->dataAnalyses()->count(),
            'completed' => Auth::user()->dataAnalyses()->where('status', DataAnalysis::STATUS_COMPLETED)->count(),
            'processing' => Auth::user()->dataAnalyses()->where('status', DataAnalysis::STATUS_PROCESSING)->count(),
            'failed' => Auth::user()->dataAnalyses()->where('status', DataAnalysis::STATUS_FAILED)->count(),
        ];
        
        return view('data-analysis.history', compact('analyses', 'stats'));
    }

    /**
     * Upload and process Excel file
     */
    public function upload(Request $request)
    {
        // Check if Summary option is selected
        $useSummary = $request->has('use_summary') && $request->input('use_summary') == '1';
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'custom_insights' => $useSummary ? 'nullable|string' : 'required|string|min:3',
        ]);

        try {
            $file = $request->file('excel_file');
            
            // Create data analysis record (will be deleted if analysis fails)
            $dataAnalysis = DataAnalysis::create([
                'user_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => '',
                'status' => DataAnalysis::STATUS_PROCESSING,
            ]);

            try {
                // Process Excel file
                $processedData = $this->dataAnalysisService->processExcelFile($file);
                
                // Update file path
                $dataAnalysis->update([
                    'file_path' => $processedData['file_path'],
                    'excel_data' => $processedData,
                ]);
            } catch (\Exception $e) {
                // If file processing fails, delete the record
                $dataAnalysis->delete();
                throw $e;
            }

            // Analyze with AI
            $startTime = microtime(true);
            $useSummary = $request->has('use_summary') && $request->input('use_summary') == '1';
            $customInsights = $useSummary ? '' : $request->input('custom_insights', '');
            
            // Prepare text for analytics (summary of data)
            $dataSummaryText = '';
            if (isset($processedData['sheets']) && is_array($processedData['sheets'])) {
                foreach ($processedData['sheets'] as $sheet) {
                    if (isset($sheet['data']) && is_array($sheet['data'])) {
                        $dataSummaryText .= json_encode($sheet['data']) . "\n";
                    }
                }
            }
            
            // Start analytics tracking
            $analytics = $this->analyticsService->startAnalysisWithoutPrediction(Auth::id(), [
                'text' => $dataSummaryText,
                'uploaded_files' => [['size' => $file->getSize() ?? 0]],
                'analysis_type' => 'data-analysis',
            ]);
            
            try {
                // Pass analytics to the service (we'll need to update the service method)
                $analysisResult = $this->dataAnalysisService->analyzeData($processedData, $customInsights, $useSummary, $analytics);
                $processingTime = round(microtime(true) - $startTime, 3);
                
                // Complete analytics tracking
                if ($analytics) {
                    $this->analyticsService->completeAnalysis($analytics, [
                        'total_processing_time' => $processingTime,
                        'api_error_message' => (isset($analysisResult['insights']) && !empty($analysisResult['insights'])) ? null : 'Analysis failed'
                    ]);
                }
                
                // Check if we have valid analysis results with charts
                $hasValidInsights = isset($analysisResult['insights']) && is_array($analysisResult['insights']) && !empty($analysisResult['insights']);
                $hasCharts = isset($analysisResult['chart_configs']) && is_array($analysisResult['chart_configs']) && !empty($analysisResult['chart_configs']);
                
                if ($hasValidInsights && $hasCharts) {
                    // Check if this is a fallback response (failed analysis)
                    $isFallbackResponse = (
                        isset($analysisResult['insights']['summary']) && 
                        (strpos($analysisResult['insights']['summary'], 'Due to technical difficulties') !== false ||
                         strpos($analysisResult['insights']['summary'], 'comprehensive analysis could not be generated') !== false)
                    ) || (
                        isset($analysisResult['insights']['error']) && 
                        !empty($analysisResult['insights']['error'])
                    );
                    
                    if ($isFallbackResponse) {
                        // Delete the data analysis record entirely - don't save AI API failures
                        $dataAnalysis->delete();
                        
                        $errorMessage = 'Analysis failed due to technical difficulties. Please try again.';
                        
                        // Return JSON for AJAX requests
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'error' => $errorMessage,
                                'analysis_id' => null // No analysis ID since it was deleted
                            ], 500);
                        }
                        
                        return redirect()->back()
                            ->with('error', $errorMessage)
                            ->withInput();
                    }
                    
                    // We have a properly structured response with charts (not a fallback)
                    $dataAnalysis->update([
                        'ai_insights' => $analysisResult['insights'],
                        'chart_configs' => $analysisResult['chart_configs'],
                        'model_used' => $analysisResult['model_used'] ?? 'gemini-2.5-flash',
                        'processing_time' => $analysisResult['processing_time'] ?? $processingTime,
                        'status' => DataAnalysis::STATUS_COMPLETED,
                    ]);
                    
                    // Refresh to get updated data
                    $dataAnalysis->refresh();
                    
                    // Return JSON for AJAX requests
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => true,
                            'analysis_id' => $dataAnalysis->id,
                            'status' => $dataAnalysis->status,
                            'message' => 'File uploaded and analyzed successfully!',
                        ]);
                    }
                    
                    return redirect()->route('data-analysis.show', $dataAnalysis)
                        ->with('success', 'File uploaded and analyzed successfully!');
                } else {
                    // Invalid response or no charts generated - delete the record (don't save to database)
                    // This will show the same "Service Unavailable" UI as other AI API failures
                    
                    // Complete analytics tracking before deleting (track the failure)
                    if (isset($analytics) && $analytics) {
                        $this->analyticsService->completeAnalysis($analytics, [
                            'total_processing_time' => $processingTime,
                            'api_error_message' => 'Analysis failed: Invalid response or no charts generated'
                        ]);
                    }
                    
                    $dataAnalysis->delete();
                    
                    // Use consistent error message for all AI API failures (triggers Service Unavailable UI)
                    $errorMessage = 'Analysis failed due to technical difficulties. Please try again.';
                    
                    // Return JSON for AJAX requests
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => $errorMessage,
                            'analysis_id' => null // No analysis ID since it was deleted
                        ], 500);
                    }
                    
                    return redirect()->back()
                        ->with('error', $errorMessage)
                        ->withInput();
                }
            } catch (\Exception $e) {
                // Calculate processing time even for exceptions
                $processingTime = round(microtime(true) - $startTime, 3);
                
                // Complete analytics tracking with error
                if (isset($analytics) && $analytics) {
                    $this->analyticsService->completeAnalysis($analytics, [
                        'total_processing_time' => $processingTime,
                        'api_error_message' => 'Analysis failed: ' . $e->getMessage()
                    ]);
                }
                
                Log::error('AI analysis failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Delete the data analysis record - don't save failed analyses
                $dataAnalysis->delete();
                
                // Use consistent error message for all AI API failures
                $errorMessage = 'Analysis failed due to technical difficulties. Please try again.';
                
                // Return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => $errorMessage,
                        'analysis_id' => null // No analysis ID since it was deleted
                    ], 500);
                }
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->withInput();
            }
                
        } catch (\Exception $e) {
            Log::error('Data analysis upload failed: ' . $e->getMessage());
            
            // Ensure no failed records are saved (safety net for any edge cases)
            if (isset($dataAnalysis) && $dataAnalysis->exists) {
                $dataAnalysis->delete();
            }
            
            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to process file: ' . $e->getMessage(),
                    'analysis_id' => null
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to process file: ' . $e->getMessage()]);
        }
    }

    /**
     * Get analysis HTML for display in right panel
     */
    public function getAnalysisHtml(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access',
            ], 403);
        }

        try {
            $html = view('data-analysis.partials.analysis-content', compact('dataAnalysis'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating analysis HTML: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate analysis HTML: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show individual data analysis
     */
    public function show(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            abort(403, 'Unauthorized access');
        }

        // If AJAX request, return just the analysis content HTML
        if (request()->ajax() || request()->wantsJson()) {
            try {
                $html = view('data-analysis.partials.analysis-content', compact('dataAnalysis'))->render();
                return response($html)->header('Content-Type', 'text/html');
            } catch (\Exception $e) {
                Log::error('Error generating analysis HTML: ' . $e->getMessage());
                return response('<div style="text-align: center; color: #ef4444; padding: 48px;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Failed to load analysis results</p></div>', 500)
                    ->header('Content-Type', 'text/html');
            }
        }

        return view('data-analysis.show', compact('dataAnalysis'));
    }

    /**
     * Show dashboard view for data analysis
     */
    public function dashboard(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access',
                ], 403);
            }
            abort(403, 'Unauthorized access');
        }

        // Check if analysis is completed
        if ($dataAnalysis->status !== DataAnalysis::STATUS_COMPLETED) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Analysis not completed yet',
                ], 400);
            }
            abort(400, 'Analysis not completed yet');
        }

        try {
            // Extract dashboard data from AI insights and chart configs
            $dashboardData = $this->prepareDashboardDataFromInsights($dataAnalysis);
            
            // If AJAX request, return just the dashboard HTML
            if (request()->ajax() || request()->wantsJson()) {
                $html = view('data-analysis.dashboard', compact('dataAnalysis', 'dashboardData'))->render();
                return response($html)->header('Content-Type', 'text/html');
            }

            return view('data-analysis.dashboard', compact('dataAnalysis', 'dashboardData'));
        } catch (\Exception $e) {
            Log::error('Error generating dashboard: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response('<div style="text-align: center; color: #ef4444; padding: 48px;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Failed to load dashboard</p></div>', 500)
                    ->header('Content-Type', 'text/html');
            }
            return back()->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Prepare dashboard data from AI insights and chart configs
     */
    protected function prepareDashboardDataFromInsights(DataAnalysis $dataAnalysis): array
    {
        $aiInsights = $dataAnalysis->ai_insights ?? [];
        $chartConfigs = $dataAnalysis->chart_configs ?? [];
        
        $dashboardData = [
            'insights' => $aiInsights,
            'chart_configs' => $chartConfigs,
            'summary' => $aiInsights['summary'] ?? '',
            'key_findings' => $aiInsights['key_findings'] ?? [],
            'metrics' => $this->extractMetricsFromCharts($chartConfigs),
            'filter_options' => $this->extractFilterOptionsFromCharts($chartConfigs),
        ];

        return $dashboardData;
    }

    /**
     * Extract metrics from chart configs
     */
    protected function extractMetricsFromCharts(array $chartConfigs): array
    {
        $metrics = [];
        
        foreach ($chartConfigs as $chart) {
            $chartData = $chart['data'] ?? [];
            $labels = $chartData['labels'] ?? [];
            $datasets = $chartData['datasets'] ?? [];
            
            // Extract totals from datasets
            foreach ($datasets as $dataset) {
                $data = $dataset['data'] ?? [];
                if (is_array($data) && count($data) > 0) {
                    $total = array_sum(array_filter($data, function($val) {
                        return is_numeric($val);
                    }));
                    
                    $chartTitle = $chart['title'] ?? '';
                    if (!empty($chartTitle)) {
                        $metrics[$chartTitle] = $total;
                    }
                }
            }
        }
        
        return $metrics;
    }

    /**
     * Extract filter options from chart configs
     */
    protected function extractFilterOptionsFromCharts(array $chartConfigs): array
    {
        $filters = [
            'categories' => [],
            'labels' => [],
            'datasets' => [],
        ];
        
        foreach ($chartConfigs as $chart) {
            $chartData = $chart['data'] ?? [];
            $labels = $chartData['labels'] ?? [];
            $datasets = $chartData['datasets'] ?? [];
            
            // Collect unique labels
            foreach ($labels as $label) {
                if (!in_array($label, $filters['labels'])) {
                    $filters['labels'][] = $label;
                }
            }
            
            // Collect dataset names
            foreach ($datasets as $dataset) {
                $label = $dataset['label'] ?? '';
                if (!empty($label) && !in_array($label, $filters['datasets'])) {
                    $filters['datasets'][] = $label;
                }
            }
            
            // Collect chart categories/types
            $chartType = $chart['type'] ?? '';
            if (!empty($chartType) && !in_array($chartType, $filters['categories'])) {
                $filters['categories'][] = $chartType;
            }
        }
        
        return $filters;
    }

    /**
     * Get Excel data preview
     */
    public function excelPreview(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access',
            ], 403);
        }

        try {
            return response()->json([
                'success' => true,
                'excel_data' => $dataAnalysis->excel_data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting Excel preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load Excel data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export data analysis dashboard to PDF
     */
    public function export(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            abort(403, 'Unauthorized access');
        }

        // Check if analysis is completed
        if ($dataAnalysis->status !== DataAnalysis::STATUS_COMPLETED) {
            abort(400, 'Analysis not completed yet');
        }

        try {
            // Increase execution time limit for PDF generation
            set_time_limit(120); // 2 minutes
            
            // Prepare dashboard data from AI insights and chart configs
            $dashboardData = $this->prepareDashboardDataFromInsights($dataAnalysis);
            
            // Generate chart images for PDF with timeout handling
            $chartImages = [];
            $startTime = microtime(true);
            $maxGenerationTime = 25; // Maximum time to spend generating chart images (seconds)
            
            if (!empty($dashboardData['chart_configs']) && is_array($dashboardData['chart_configs'])) {
                // Don't limit charts - generate images for all of them
                // But add timeout protection per chart
                $chartConfigs = $dashboardData['chart_configs'];
                
                foreach ($chartConfigs as $index => $chartConfig) {
                    // Check if we're running out of time
                    $elapsed = microtime(true) - $startTime;
                    if ($elapsed > $maxGenerationTime) {
                        Log::info('Stopping chart image generation due to time limit. Generated ' . count($chartImages) . ' charts.');
                        break;
                    }
                    
                    try {
                        // Check if this is a map chart for debugging
                        $chartTitle = $chartConfig['title'] ?? '';
                        $chartType = $chartConfig['type'] ?? 'unknown';
                        $isMapChart = stripos($chartTitle, 'map') !== false 
                            || stripos($chartType, 'map') !== false;
                        
                        // Log chart generation attempt
                        $isComparisonChart = stripos($chartTitle, 'vs.') !== false 
                            || stripos($chartTitle, 'versus') !== false 
                            || stripos($chartTitle, 'comparison') !== false;
                        
                        Log::info('Generating chart image', [
                            'index' => $index,
                            'title' => $chartTitle,
                            'type' => $chartType,
                            'is_map' => $isMapChart,
                            'is_comparison' => $isComparisonChart,
                            'has_data' => !empty($chartConfig['data']),
                            'labels_count' => isset($chartConfig['data']['labels']) ? count($chartConfig['data']['labels']) : 0,
                            'datasets_count' => isset($chartConfig['data']['datasets']) ? count($chartConfig['data']['datasets']) : 0,
                        ]);
                        
                        if ($isMapChart) {
                            Log::info('Generating map image', [
                                'index' => $index,
                                'title' => $chartTitle,
                                'has_coordinates' => isset($chartConfig['coordinates']),
                                'coordinates_count' => isset($chartConfig['coordinates']) && is_array($chartConfig['coordinates']) 
                                    ? count($chartConfig['coordinates']) : 0,
                            ]);
                        }
                        
                        // Use smaller image size for faster generation
                        $chartImage = \App\Services\ChartImageService::generateChartImage($chartConfig, 600, 300);
                        if ($chartImage) {
                            $chartImages[$index] = $chartImage;
                            Log::info('Chart image generated successfully', [
                                'index' => $index,
                                'title' => $chartTitle,
                                'type' => $chartType,
                            ]);
                        } else {
                            Log::warning('Chart image generation returned null', [
                                'index' => $index,
                                'title' => $chartTitle,
                                'type' => $chartType,
                                'is_map' => $isMapChart,
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Log but continue with other charts
                        Log::warning('Failed to generate chart image for index ' . $index . ': ' . $e->getMessage(), [
                            'title' => $chartConfig['title'] ?? 'Unknown',
                            'type' => $chartConfig['type'] ?? 'unknown',
                            'trace' => $e->getTraceAsString(),
                        ]);
                        continue;
                    }
                }
            }
            
            // Render the dashboard view as HTML for PDF
            $html = view('data-analysis.export-pdf', compact('dataAnalysis', 'dashboardData', 'chartImages'))->render();
            
            // Generate PDF using the rendered HTML
            $pdf = Pdf::loadHTML($html);
            
            // Set PDF options for better formatting and page break handling
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => false, // Disable to speed up
                'isPhpEnabled' => true,
                'isJavascriptEnabled' => false,
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait',
                'dpi' => 120, // Reduced from 150 to 120 for faster processing
                'fontHeightRatio' => 0.9,
                'enable-smart-shrinking' => true,
                'enable-local-file-access' => true,
                'chroot' => public_path(), // Set chroot for security and performance
            ]);
            
            // Add page numbers using DomPDF callback
            $dompdf = $pdf->getDomPDF();
            $dompdf->setCallbacks([
                [
                    'event' => 'end_page', 'f' => function ($infos) {
                        $canvas = $infos["canvas"];
                        $fontMetrics = $infos["fontMetrics"];
                        $font = $fontMetrics->getFont("Times New Roman", "normal");
                        $size = 9;
                        $pageText = "{PAGE_NUM}";
                        $y = $canvas->get_height() - 24;
                        $pageWidth = 595.28; // A4 width in points
                        $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                        $x = ($pageWidth / 2) - ($textWidth / 2) + 25;
                        $canvas->page_text($x, $y, $pageText, $font, $size, array(0, 0, 0));
                    }
                ]
            ]);

            // Generate filename
            $filename = 'data_analysis_' . $dataAnalysis->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Return PDF for download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting data analysis PDF: ' . $e->getMessage());
            abort(500, 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Delete data analysis
     */
    public function destroy(DataAnalysis $dataAnalysis)
    {
        // Check ownership
        if ((int)Auth::id() !== (int)$dataAnalysis->user_id) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access',
                ], 403);
            }
            abort(403, 'Unauthorized access');
        }

        // Delete file
        if ($dataAnalysis->file_path && Storage::disk('public')->exists($dataAnalysis->file_path)) {
            Storage::disk('public')->delete($dataAnalysis->file_path);
        }

        $analysisId = $dataAnalysis->id;
        $createdAt = $dataAnalysis->created_at;
        $updatedAt = $dataAnalysis->updated_at;
        
        // Preserve analytics by finding and keeping analytics records
        // Analytics records are independent (no foreign key), so they remain automatically
        // But we log this for clarity
        $analyticsCount = \App\Models\AnalysisAnalytics::where('user_id', Auth::id())
            ->where('analysis_type', 'data-analysis')
            ->where('created_at', '>=', $createdAt->subMinutes(5))
            ->where('created_at', '<=', $updatedAt->addMinutes(5))
            ->count();
        
        $dataAnalysis->delete();
        
        Log::info('Data analysis deleted', [
            'user_id' => Auth::id(),
            'analysis_id' => $analysisId,
            'analytics_preserved' => $analyticsCount > 0,
            'analytics_count' => $analyticsCount
        ]);

        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data analysis deleted successfully',
            ]);
        }

        return redirect()->route('data-analysis.history')
            ->with('success', 'Data analysis deleted successfully');
    }
}
