<?php

namespace App\Http\Controllers;

use App\Models\DataAnalysis;
use App\Services\DataAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataAnalysisController extends Controller
{
    protected $dataAnalysisService;

    public function __construct(DataAnalysisService $dataAnalysisService)
    {
        $this->dataAnalysisService = $dataAnalysisService;
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
            
            try {
                $analysisResult = $this->dataAnalysisService->analyzeData($processedData, $customInsights, $useSummary);
                $processingTime = round(microtime(true) - $startTime, 3);
                
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

        $dataAnalysis->delete();

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
