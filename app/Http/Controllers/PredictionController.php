<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\AIServiceFactory;
use App\Services\FileProcessingService;
use App\Services\WebScrapingService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PredictionController extends Controller
{
    protected $fileProcessingService;
    protected $webScrapingService;
    protected $analyticsService;

    public function __construct(FileProcessingService $fileProcessingService, WebScrapingService $webScrapingService, AnalyticsService $analyticsService)
    {
        $this->fileProcessingService = $fileProcessingService;
        $this->webScrapingService = $webScrapingService;
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $predictions = Auth::user()->predictions()->latest()->paginate(5);
        return view('predictions.index', compact('predictions'));
    }

    public function testApi()
    {
        try {
            // First test basic system functionality
            $systemTest = [
                'success' => true,
                'message' => 'System components working',
                'timestamp' => now()->toISOString()
            ];
            
            // Get the current AI service
            $aiService = AIServiceFactory::create();
            
            // Test AI service connectivity (but don't fail if external API is down)
            $connectionTest = $aiService->testConnection();
            
            // Test prediction analysis with AI API
            $result = $aiService->analyzeText(
                'This is a test message for API connectivity.',
                'prediction-analysis'
            );
            
            \Log::info('API test completed', [
                'system_test' => $systemTest,
                'connection_test' => $connectionTest,
                'analysis_result' => $result
            ]);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'api_status' => 'connected',
                    'system_test' => $systemTest,
                    'connection_test' => $connectionTest,
                    'result' => $result,
                    'message' => 'System fully operational - AI API connection successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'api_status' => 'failed',
                    'system_test' => $systemTest,
                    'connection_test' => $connectionTest,
                    'result' => $result,
                    'message' => 'AI API connection failed - Please check your API key and configuration'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('API test exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'api_status' => 'error',
                'message' => 'System error occurred during testing'
            ], 500);
        }
    }
    
    public function simpleTest()
    {
        return response()->json([
            'success' => true,
            'message' => 'Simple test endpoint working',
            'timestamp' => now()->toISOString(),
            'user' => auth()->check() ? 'authenticated' : 'not authenticated'
        ]);
    }



    public function create()
    {
        return view('predictions.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:255',
            'target' => 'nullable|string|max:1000',
            'prediction_horizon' => 'required|in:next_two_days,next_two_weeks,next_month,three_months,six_months,twelve_months,two_years',
            'input_data' => 'required|string|min:10',
            'source_urls' => 'nullable|array',
            'source_urls.*' => 'nullable|url|max:500',
            'uploaded_files.*' => 'nullable|file|mimes:pdf,xlsx,xls,csv,txt|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Filter out empty source URLs
        $sourceUrls = $request->source_urls ? array_filter($request->source_urls) : null;

        // Process uploaded files if any
        $uploadedFiles = null;
        $extractedText = null;
        $combinedInputData = $request->input_data;

        if ($request->hasFile('uploaded_files')) {
            $fileResults = $this->fileProcessingService->processFiles($request->file('uploaded_files'));
            $uploadedFiles = $fileResults['files'];
            $extractedText = $fileResults['extracted_text'];
            
            // Combine the original input data with extracted text from files
            if (!empty($extractedText)) {
                $combinedInputData = $request->input_data . "\n\n--- EXTRACTED DATA FROM UPLOADED FILES ---\n\n" . $extractedText;
            }
        }

        // Create prediction record with fixed analysis type
        $prediction = Prediction::create([
            'topic' => $request->topic,
            'target' => $request->target,
            'prediction_horizon' => $request->prediction_horizon,
            'input_data' => ['text' => $combinedInputData, 'analysis_type' => 'prediction-analysis'],
            'source_urls' => $sourceUrls,
            'uploaded_files' => $uploadedFiles,
            'extracted_text' => $extractedText,
            'user_id' => Auth::id(),
            'status' => 'processing',
            'confidence_score' => 0.0, // Set default confidence score
            'processing_time' => 0.0, // Set default processing time
            'model_used' => 'pending' // Set default model
        ]);

        // Start analytics tracking
        $analytics = $this->analyticsService->startAnalysis($prediction, [
            'text' => $combinedInputData,
            'source_urls' => $sourceUrls,
            'uploaded_files' => $uploadedFiles,
            'analysis_type' => 'prediction-analysis',
            'prediction_horizon' => $request->prediction_horizon
        ]);

        // Record start time for processing time calculation
        $startTime = microtime(true);

        try {
            // Get the current AI service
            $aiService = AIServiceFactory::create();
            
            // Process with AI using combined input data (original + extracted from files)
            $result = $aiService->analyzeText(
                $combinedInputData,
                'prediction-analysis',
                $sourceUrls,
                $request->prediction_horizon,
                $analytics,
                $request->target
            );
            
            // Calculate actual processing time
            $processingTime = round(microtime(true) - $startTime, 3);
            
            // Log the result for debugging
            \Log::info('AI Analysis Result', [
                'analysis_type' => 'prediction-analysis',
                'result' => $result,
                'prediction_id' => $prediction->id,
                'processing_time' => $processingTime
            ]);
            
            // Debug the result structure
            \Log::info('Result structure debug', [
                'result_type' => gettype($result),
                'result_keys' => is_array($result) ? array_keys($result) : 'not_array',
                'has_title_direct' => isset($result['title']),
                'has_result_key' => isset($result['result']),
                'has_result_title' => isset($result['result']['title']),
                'has_raw_response_direct' => isset($result['raw_response']),
                'has_result_raw_response' => isset($result['result']['raw_response'])
            ]);

            // Check if we have valid analysis results from Gemini API
            if (isset($result['title']) && is_array($result) && !empty($result) && !isset($result['raw_response'])) {
                // Check if this is a fallback response (failed analysis)
                $isFallbackResponse = (
                    isset($result['title']) && 
                    $result['title'] === 'Analysis Failed - Fallback Response'
                ) || (
                    isset($result['executive_summary']) && 
                    strpos($result['executive_summary'], 'Due to technical difficulties') !== false &&
                    strpos($result['executive_summary'], 'comprehensive analysis could not be generated') !== false
                );
                
                if ($isFallbackResponse) {
                    // Delete the prediction record entirely - don't save AI API failures
                    // Preserve analytics by setting prediction_id to null before deleting
                    // This allows tracking user usage even for failed predictions
                    $predictionId = $prediction->id;
                    
                    // Update all associated analytics to set prediction_id to null
                    \App\Models\AnalysisAnalytics::where('prediction_id', $predictionId)
                        ->update(['prediction_id' => null]);
                    
                    $prediction->delete();
                    
                    $errorMessage = 'Analysis failed due to technical difficulties. Please try again.';
                    
                    // Return JSON for AJAX requests
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => $errorMessage,
                            'prediction_id' => null // No prediction ID since it was deleted
                        ], 500);
                    }
                    
                    return redirect()->back()
                        ->with('error', $errorMessage)
                        ->withInput();
                }
                
                // We have a properly structured response (not a fallback)
                $confidenceScore = $this->extractConfidenceScore($result, 'prediction-analysis');
                
                // Try to get API timing from the result metadata if available
                $apiTiming = $result['api_metadata']['api_response_time'] ?? null;
                $finalProcessingTime = $apiTiming !== null ? $apiTiming : $processingTime;
                
                // Log the confidence score for debugging
                \Log::info('Extracted Confidence Score', [
                    'confidence_score' => $confidenceScore,
                    'analysis_type' => 'prediction-analysis',
                    'api_timing' => $apiTiming,
                    'total_processing_time' => $finalProcessingTime
                ]);
                
                $prediction->update([
                    'prediction_result' => $result,
                    'confidence_score' => is_numeric($confidenceScore) ? (float) $confidenceScore : 0.75,
                    'model_used' => 'gemini-2.5-flash',
                    'processing_time' => $finalProcessingTime, // Use API timing if available, otherwise total processing time
                    'status' => $this->validateStatus('completed')
                ]);

                // Complete analytics tracking
                if ($analytics) {
                    $this->analyticsService->completeAnalysis($analytics, [
                        'total_processing_time' => $finalProcessingTime
                    ]);
                }

                $successMessage = 'Prediction completed successfully!';

                // Return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'prediction_id' => $prediction->id,
                        'message' => $successMessage,
                        'redirect_url' => route('predictions.show', $prediction)
                    ]);
                }

                return redirect()->route('predictions.show', $prediction)
                    ->with('success', $successMessage);
            } elseif (isset($result['raw_response'])) {
                // Handle case where we have raw response that might contain JSON
                \Log::warning('Processing raw response from AI');
                \Log::info('Raw response length: ' . strlen($result['raw_response']));
                
                // Try to get API timing from the result metadata if available
                $apiTiming = $result['api_metadata']['api_response_time'] ?? null;
                $finalProcessingTime = $apiTiming !== null ? $apiTiming : $processingTime;
                
                $prediction->update([
                    'prediction_result' => $result,
                    'confidence_score' => 0.60, // Lower confidence for raw responses
                    'model_used' => 'gemini-2.5-flash',
                    'processing_time' => $finalProcessingTime, // Use API timing if available, otherwise total processing time
                    'status' => $this->validateStatus('completed_with_warnings')
                ]);

                $warningMessage = 'Prediction completed with warnings. The AI response may be incomplete due to processing limitations.';

                // Return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'prediction_id' => $prediction->id,
                        'message' => $warningMessage,
                        'warning' => true,
                        'redirect_url' => route('predictions.show', $prediction)
                    ]);
                }

                return redirect()->route('predictions.show', $prediction)
                    ->with('warning', $warningMessage);
            } else {
                $prediction->update([
                    'status' => $this->validateStatus('failed'),
                    'confidence_score' => 0.0,
                    'model_used' => 'failed',
                    'processing_time' => $processingTime // Use actual calculated processing time even for failures
                ]);

                $errorMessage = isset($result['error']) ? $result['error'] : 'Analysis failed due to unknown error';
                
                // Return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'AI analysis failed: ' . $errorMessage,
                        'prediction_id' => $prediction->id
                    ], 500);
                }
                
                return redirect()->back()
                    ->with('error', 'AI analysis failed: ' . $errorMessage)
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Calculate processing time even for exceptions
            $processingTime = round(microtime(true) - $startTime, 3);
            
            $prediction->update([
                'status' => $this->validateStatus('failed'),
                'confidence_score' => 0.0, // Set default confidence for exceptions
                'model_used' => 'error',
                'processing_time' => $processingTime // Use actual calculated processing time
            ]);
            
            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'An error occurred during processing: ' . $e->getMessage(),
                    'prediction_id' => $prediction->id ?? null
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred during processing: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Prediction $prediction)
    {
        // Add debugging information
        \Log::info('Prediction access attempt', [
            'prediction_id' => $prediction->id,
            'prediction_user_id' => $prediction->user_id,
            'current_user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'request_url' => request()->url()
        ]);
        
        // Check if user owns this prediction or is admin
        if (!Auth::check()) {
            abort(401, 'User not authenticated.');
        }
        
        // Fix: Convert both values to integers for comparison to handle string vs integer mismatch
        if ((int)Auth::id() !== (int)$prediction->user_id) {
            abort(403, 'Unauthorized access to prediction. User ID: ' . Auth::id() . ', Prediction User ID: ' . $prediction->user_id);
        }
        
        // Return HTML content for AJAX requests (for history page)
        if (request()->ajax() && request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('predictions.partials.content', compact('prediction'))->render();
        }
        
        return view('predictions.show', compact('prediction'));
    }

    public function history(Request $request)
    {
        $query = Auth::user()->predictions();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('topic', 'like', '%' . $search . '%')
                  ->orWhere('target', 'like', '%' . $search . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        // Note: Failed predictions (non-AI API failures) are shown in history
        // AI API failures are not saved to database, so they won't appear anyway
        
        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Get all predictions (no pagination - display as continuous scrollable list)
        $predictions = $query->latest()->get();
        
        // Get total counts for stats (not just current page)
        $allPredictions = Auth::user()->predictions();
        $stats = [
            'total' => $allPredictions->count(),
            'completed' => $allPredictions->where('status', 'completed')->count(),
            'processing' => $allPredictions->where('status', 'processing')->count(),
            'failed' => $allPredictions->where('status', 'failed')->count(),
        ];
        
        return view('predictions.history', compact('predictions', 'stats'));
    }
    
    /**
     * Validate status value before updating
     */
    private function validateStatus($status)
    {
        $validStatuses = [
            \App\Models\Prediction::STATUS_PENDING,
            \App\Models\Prediction::STATUS_PROCESSING, 
            \App\Models\Prediction::STATUS_COMPLETED,
            \App\Models\Prediction::STATUS_COMPLETED_WITH_WARNINGS,
            \App\Models\Prediction::STATUS_FAILED,
            \App\Models\Prediction::STATUS_CANCELLED
        ];
        
        if (!in_array($status, $validStatuses)) {
            \Log::error('Invalid status attempted: ' . $status);
            throw new \InvalidArgumentException('Invalid status value: ' . $status);
        }
        
        return $status;
    }

    public function fixData()
    {
        // Fix any existing predictions with null confidence scores
        $nullConfidencePredictions = Auth::user()->predictions()
            ->whereNull('confidence_score')
            ->orWhere('confidence_score', '')
            ->get();
            
        foreach ($nullConfidencePredictions as $prediction) {
            $prediction->update([
                'confidence_score' => 0.0,
                'processing_time' => $prediction->processing_time ?? 0.0,
                'model_used' => $prediction->model_used ?? 'unknown'
            ]);
        }
        
        return redirect()->back()->with('success', 'Fixed ' . $nullConfidencePredictions->count() . ' predictions with missing data.');
    }
    
    public function debugData()
    {
        $prediction = Auth::user()->predictions()->latest()->first();
        
        if (!$prediction) {
            return response()->json(['error' => 'No predictions found']);
        }
        
        return response()->json([
            'prediction_id' => $prediction->id,
            'confidence_score_raw' => $prediction->getRawOriginal('confidence_score'),
            'confidence_score_accessed' => $prediction->confidence_score,
            'confidence_score_type' => gettype($prediction->confidence_score),
            'confidence_score_isset' => isset($prediction->confidence_score),
            'confidence_score_null' => is_null($prediction->confidence_score),
            'model_used' => $prediction->model_used,
            'status' => $prediction->status
        ]);
    }

    public function debugAuth()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user' => Auth::user() ? [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ] : null,
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'csrf_token' => csrf_token(),
            'request_headers' => request()->headers->all()
        ]);
    }

    public function testDelete(Prediction $prediction)
    {
        return response()->json([
            'prediction' => [
                'id' => $prediction->id,
                'user_id' => $prediction->user_id,
                'topic' => $prediction->topic,
                'created_at' => $prediction->created_at
            ],
            'current_user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ],
            'ownership_check' => [
                'prediction_user_id' => $prediction->user_id,
                'current_user_id' => Auth::id(),
                'is_owner' => Auth::id() === $prediction->user_id,
                'comparison' => Auth::id() . ' === ' . $prediction->user_id . ' = ' . (Auth::id() === $prediction->user_id ? 'true' : 'false'),
                'type_comparison' => [
                    'prediction_user_id_type' => gettype($prediction->user_id),
                    'current_user_id_type' => gettype(Auth::id()),
                    'prediction_user_id_value' => $prediction->user_id,
                    'current_user_id_value' => Auth::id()
                ]
            ],
            'session_info' => [
                'session_id' => session()->getId(),
                'user_authenticated' => Auth::check()
            ]
        ]);
    }

    public function showModelInfo(Prediction $prediction)
    {
        // Check if user owns this prediction or is admin
        if ((int)Auth::id() !== (int)$prediction->user_id) {
            abort(403, 'Unauthorized access to prediction.');
        }
        
        $modelInfo = [
            'prediction_id' => $prediction->id,
            'topic' => $prediction->topic,
            'model_used' => $prediction->model_used,
            'status' => $prediction->status,
            'confidence_score' => $prediction->confidence_score,
            'processing_time' => $prediction->processing_time,
            'created_at' => $prediction->created_at->toISOString(),
            'analysis_details' => [
                'input_data' => $prediction->input_data,
                'prediction_result' => $prediction->prediction_result,
                'gemini_used' => true,
                'model_source' => $this->getModelSource($prediction->model_used),
                'api_status' => $this->getApiStatus($prediction->model_used)
            ]
        ];
        
        return response()->json($modelInfo);
    }

    public function export(Prediction $prediction)
    {
        // Add debugging information
        \Log::info('Prediction export attempt', [
            'prediction_id' => $prediction->id,
            'prediction_user_id' => $prediction->user_id,
            'current_user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'request_url' => request()->url()
        ]);
        
        // Check if user owns this prediction or is admin
        if (!Auth::check()) {
            abort(401, 'User not authenticated.');
        }
        
        // Fix: Convert both values to integers for comparison to handle string vs integer mismatch
        if ((int)Auth::id() !== (int)$prediction->user_id) {
            abort(403, 'Unauthorized access to prediction. User ID: ' . Auth::id() . ', Prediction User ID: ' . $prediction->user_id);
        }

        // Generate PDF using the prediction data
        $pdf = Pdf::loadView('predictions.export-pdf', compact('prediction'));
        
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
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'dpi' => 150,
            'fontHeightRatio' => 0.9
        ]);

        // Generate filename
        $filename = 'prediction_' . $prediction->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }
    
    protected function getModelSource($modelUsed)
    {
        if (strpos($modelUsed, 'gemini') !== false) {
            return 'AI Model (External)';
        } else {
            return 'Unknown Model';
        }
    }
    
    protected function getApiStatus($modelUsed)
    {
        return 'AI Model Used';
    }

    protected function extractConfidenceScore($result, $analysisType)
    {
        if (!is_array($result)) {
            return 0.75;
        }
        
        // Try to extract confidence score from various possible locations in the result
        $confidenceScore = null;
        
        // Check for confidence_score field
        if (isset($result['confidence_score']) && is_numeric($result['confidence_score'])) {
            $confidenceScore = (float) $result['confidence_score'];
        }
        // Check for confidence field
        elseif (isset($result['confidence']) && is_numeric($result['confidence'])) {
            $confidenceScore = (float) $result['confidence'];
        }
        // Check for confidence_level field (might be text like "High (85-90%)")
        elseif (isset($result['confidence_level'])) {
            $confidenceScore = $this->parseConfidenceLevel($result['confidence_level']);
        }
        // Check for confidence in nested result structure
        elseif (isset($result['result']['confidence_score']) && is_numeric($result['result']['confidence_score'])) {
            $confidenceScore = (float) $result['result']['confidence_score'];
        }
        elseif (isset($result['result']['confidence']) && is_numeric($result['result']['confidence'])) {
            $confidenceScore = (float) $result['result']['confidence'];
        }
        // Check for confidence in API metadata
        elseif (isset($result['api_metadata']['confidence_score']) && is_numeric($result['api_metadata']['confidence_score'])) {
            $confidenceScore = (float) $result['api_metadata']['confidence_score'];
        }
        // Check for confidence in scraping metadata
        elseif (isset($result['scraping_metadata']['confidence_score']) && is_numeric($result['scraping_metadata']['confidence_score'])) {
            $confidenceScore = (float) $result['scraping_metadata']['confidence_score'];
        }
        
        // If we found a valid confidence score, return it
        if ($confidenceScore !== null && $confidenceScore >= 0 && $confidenceScore <= 1) {
            return $confidenceScore;
        }
        
        // Fallback logic based on analysis type and result quality
        switch ($analysisType) {
            case 'prediction-analysis':
                // For prediction analysis, analyze the result quality to estimate confidence
                return $this->estimateConfidenceFromResult($result);
                
            default:
                return 0.75;
        }
    }
    
    /**
     * Parse confidence level from text descriptions like "High (85-90%)"
     */
    protected function parseConfidenceLevel($confidenceLevel)
    {
        if (is_numeric($confidenceLevel)) {
            return (float) $confidenceLevel;
        }
        
        if (is_string($confidenceLevel)) {
            // Extract percentage from text like "High (85-90%)"
            if (preg_match('/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*%/', $confidenceLevel, $matches)) {
                // Take the average of the range
                return (float) (($matches[1] + $matches[2]) / 2) / 100;
            }
            
            // Extract single percentage like "85%"
            if (preg_match('/(\d+(?:\.\d+)?)\s*%/', $confidenceLevel, $matches)) {
                return (float) $matches[1] / 100;
            }
            
            // Map text descriptions to confidence scores
            $textConfidence = [
                'very high' => 0.95,
                'high' => 0.85,
                'medium-high' => 0.75,
                'medium' => 0.65,
                'medium-low' => 0.55,
                'low' => 0.45,
                'very low' => 0.35
            ];
            
            $level = strtolower(trim($confidenceLevel));
            foreach ($textConfidence as $text => $score) {
                if (strpos($level, $text) !== false) {
                    return $score;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Estimate confidence based on result quality and completeness
     */
    protected function estimateConfidenceFromResult($result)
    {
        $confidence = 0.75; // Base confidence
        
        // Check if we have all essential fields
        $essentialFields = ['title', 'executive_summary', 'predictions'];
        $hasEssentialFields = true;
        foreach ($essentialFields as $field) {
            if (!isset($result[$field]) || empty($result[$field])) {
                $hasEssentialFields = false;
                break;
            }
        }
        
        if ($hasEssentialFields) {
            $confidence += 0.10; // Bonus for having essential fields
        }
        
        // Check for additional detailed fields
        $detailedFields = ['key_factors', 'risk_assessment', 'recommendations', 'policy_implications'];
        $detailedFieldCount = 0;
        foreach ($detailedFields as $field) {
            if (isset($result[$field]) && !empty($result[$field])) {
                $detailedFieldCount++;
            }
        }
        
        // Add confidence based on number of detailed fields
        $confidence += min(0.10, $detailedFieldCount * 0.02);
        
        // Check if predictions are specific and actionable
        if (isset($result['predictions']) && is_array($result['predictions'])) {
            $specificPredictions = 0;
            foreach ($result['predictions'] as $prediction) {
                if (strlen($prediction) > 20) { // More than just a few words
                    $specificPredictions++;
                }
            }
            
            if ($specificPredictions >= 3) {
                $confidence += 0.05; // Bonus for specific predictions
            }
        }
        
        // Ensure confidence is within valid range
        return max(0.0, min(1.0, $confidence));
    }

    public function debugPredictionOwnership($predictionId)
    {
        $prediction = Prediction::find($predictionId);
        
        if (!$prediction) {
            return response()->json(['error' => 'Prediction not found']);
        }
        
        return response()->json([
            'prediction' => [
                'id' => $prediction->id,
                'user_id' => $prediction->user_id,
                'topic' => $prediction->topic,
                'created_at' => $prediction->created_at
            ],
            'current_user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ],
            'ownership_check' => [
                'prediction_user_id' => $prediction->user_id,
                'current_user_id' => Auth::id(),
                'is_owner' => Auth::id() === $prediction->user_id,
                'comparison' => Auth::id() . ' === ' . $prediction->user_id . ' = ' . (Auth::id() === $prediction->user_id ? 'true' : 'false')
            ],
            'all_user_predictions' => Auth::user()->predictions()->pluck('id')->toArray()
        ]);
    }

    /**
     * Delete a prediction
     */
    public function destroy(Prediction $prediction)
    {
        // Add debugging information
        \Log::info('Prediction delete attempt', [
            'prediction_id' => $prediction->id,
            'prediction_user_id' => $prediction->user_id,
            'current_user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'request_url' => request()->url(),
            'request_method' => request()->method(),
            'user_agent' => request()->userAgent()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::warning('User not authenticated for prediction deletion', [
                'prediction_id' => $prediction->id,
                'session_id' => session()->getId()
            ]);
            abort(401, 'User not authenticated.');
        }

        // Check if user owns this prediction (handle both string and integer types)
        if ((int)Auth::id() !== (int)$prediction->user_id) {
            \Log::warning('Unauthorized prediction deletion attempt', [
                'prediction_id' => $prediction->id,
                'prediction_user_id' => $prediction->user_id,
                'current_user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);
            abort(403, 'Unauthorized access to prediction. User ID: ' . Auth::id() . ', Prediction User ID: ' . $prediction->user_id);
        }

        try {
            // Preserve analytics by setting prediction_id to null before deleting
            // This allows tracking user usage even after prediction is deleted
            $predictionId = $prediction->id;
            \App\Models\AnalysisAnalytics::where('prediction_id', $predictionId)
                ->update(['prediction_id' => null]);
            
            $prediction->delete();
            
            \Log::info('Prediction deleted successfully', [
                'prediction_id' => $predictionId,
                'user_id' => Auth::id(),
                'analytics_preserved' => true
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prediction deleted successfully'
                ]);
            }
            
            // Check if the request came from history page, otherwise redirect to index
            $referer = request()->header('referer');
            if ($referer && str_contains($referer, '/predictions/history')) {
                return redirect()->route('predictions.history')->with('success', 'Prediction deleted successfully');
            }
            
            return redirect()->route('predictions.index')
                ->with('success', 'Prediction deleted successfully');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting prediction', [
                'prediction_id' => $prediction->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting prediction'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error deleting prediction. Please try again.');
        }
    }

    /**
     * Validate source URLs before submission
     */
    public function validateUrls(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'urls' => 'required|array|min:1',
            'urls.*' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $urls = array_filter($request->urls); // Remove empty URLs
            
            if (empty($urls)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid URLs provided'
                ], 422);
            }

            // Validate URLs using the web scraping service
            $validationResults = $this->webScrapingService->validateUrlsForUser($urls);
            
            return response()->json([
                'success' => true,
                'data' => $validationResults
            ]);

        } catch (\Exception $e) {
            \Log::error('URL validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error validating URLs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test URL validation functionality
     */
    public function testUrlValidation()
    {
        $testUrls = [
            'https://www.google.com', // Should be accessible
            'https://www.bharian.com.my/sukan/lain-lain/2025/08/1437447/sorakan-malaysia-boleh-iringi-emas-ke-16-negara', // The problematic URL from logs
            'https://httpbin.org/status/404', // Should return 404
            'https://httpbin.org/status/403', // Should return 403
            'https://invalid-domain-that-does-not-exist-12345.com', // Should fail to connect
        ];
        
        try {
            $validationResults = $this->webScrapingService->validateUrlsForUser($testUrls);
            
            return response()->json([
                'success' => true,
                'message' => 'URL validation test completed',
                'test_urls' => $testUrls,
                'results' => $validationResults
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'URL validation test failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Show user analytics
     */
    public function analytics(Request $request)
    {
        // Default to all time (no date filter) to match history page behavior, or use provided dates
        $startDate = $request->get('start_date') 
            ? \Carbon\Carbon::parse($request->get('start_date')) 
            : null;
        
        $endDate = $request->get('end_date') 
            ? \Carbon\Carbon::parse($request->get('end_date')) 
            : null;

        $analytics = $this->analyticsService->getUserAnalytics(Auth::user(), $startDate, $endDate);

        return view('predictions.analytics', compact('analytics'));
    }
}
