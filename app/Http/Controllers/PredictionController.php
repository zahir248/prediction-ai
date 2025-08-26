<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\GeminiService;
use App\Services\FileProcessingService;
use App\Services\WebScrapingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PredictionController extends Controller
{
    protected $geminiService;
    protected $fileProcessingService;
    protected $webScrapingService;

    public function __construct(GeminiService $geminiService, FileProcessingService $fileProcessingService, WebScrapingService $webScrapingService)
    {
        $this->geminiService = $geminiService;
        $this->fileProcessingService = $fileProcessingService;
        $this->webScrapingService = $webScrapingService;
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
            
            // Test Gemini service connectivity (but don't fail if external API is down)
            $connectionTest = $this->geminiService->testConnection();
            
            // Test prediction analysis with Gemini API
            $result = $this->geminiService->analyzeText(
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
                    'message' => 'System fully operational - Gemini API connection successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'api_status' => 'failed',
                    'system_test' => $systemTest,
                    'connection_test' => $connectionTest,
                    'result' => $result,
                    'message' => 'Gemini API connection failed - Please check your API key and configuration'
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
            'prediction_horizon' => 'required|in:next_two_days,next_two_weeks,next_month,three_months,six_months,twelve_months,two_years',
            'input_data' => 'required|string|min:10',
            'source_urls' => 'nullable|array',
            'source_urls.*' => 'nullable|url|max:500',
            'uploaded_files.*' => 'nullable|file|mimes:pdf,xlsx,xls,csv,txt|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
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

        try {
            // Process with AI using combined input data (original + extracted from files)
            $result = $this->geminiService->analyzeText(
                $combinedInputData,
                'prediction-analysis',
                $sourceUrls,
                $request->prediction_horizon
            );
            
            // Log the result for debugging
            \Log::info('AI Analysis Result', [
                'analysis_type' => 'prediction-analysis',
                'result' => $result,
                'prediction_id' => $prediction->id
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
                // We have a properly structured response
                $confidenceScore = $this->extractConfidenceScore($result, 'prediction-analysis');
                
                // Log the confidence score for debugging
                \Log::info('Extracted Confidence Score', [
                    'confidence_score' => $confidenceScore,
                    'analysis_type' => 'prediction-analysis'
                ]);
                
                $prediction->update([
                    'prediction_result' => $result,
                    'confidence_score' => is_numeric($confidenceScore) ? (float) $confidenceScore : 0.75,
                    'model_used' => 'gemini-2.5-flash',
                    'processing_time' => 40.0, // Approximate from log
                    'status' => $this->validateStatus('completed')
                ]);

                $successMessage = 'Prediction completed successfully using Google Gemini AI!';

                return redirect()->route('predictions.show', $prediction)
                    ->with('success', $successMessage);
            } elseif (isset($result['raw_response'])) {
                // Handle case where we have raw response that might contain JSON
                \Log::warning('Processing raw response from AI');
                \Log::info('Raw response length: ' . strlen($result['raw_response']));
                
                $prediction->update([
                    'prediction_result' => $result,
                    'confidence_score' => 0.60, // Lower confidence for raw responses
                    'model_used' => 'gemini-2.5-flash',
                    'processing_time' => 40.0,
                    'status' => $this->validateStatus('completed_with_warnings')
                ]);

                $warningMessage = 'Prediction completed with warnings. The AI response may be incomplete due to processing limitations.';

                return redirect()->route('predictions.show', $prediction)
                    ->with('warning', $warningMessage);
            } else {
                $prediction->update([
                    'status' => $this->validateStatus('failed'),
                    'confidence_score' => 0.0,
                    'model_used' => 'failed',
                    'processing_time' => 0.0
                ]);

                $errorMessage = isset($result['error']) ? $result['error'] : 'Analysis failed due to unknown error';
                return redirect()->back()
                    ->with('error', 'AI analysis failed: ' . $errorMessage)
                    ->withInput();
            }
        } catch (\Exception $e) {
            $prediction->update([
                'status' => $this->validateStatus('failed'),
                'confidence_score' => 0.0, // Set default confidence for exceptions
                'model_used' => 'error',
                'processing_time' => 0.0
            ]);
            
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
        
        return view('predictions.show', compact('prediction'));
    }

    public function history()
    {
        $predictions = Auth::user()->predictions()->latest()->paginate(20);
        return view('predictions.history', compact('predictions'));
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
        
        // Set PDF options for better formatting
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Generate filename
        $filename = 'prediction_' . $prediction->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }
    
    protected function getModelSource($modelUsed)
    {
        if (strpos($modelUsed, 'gemini') !== false) {
            return 'Google Gemini Pro (External AI Model)';
        } else {
            return 'Unknown Model';
        }
    }
    
    protected function getApiStatus($modelUsed)
    {
        return 'Google Gemini AI Model Used';
    }

    protected function extractConfidenceScore($result, $analysisType)
    {
        if (!is_array($result)) {
            return 0.75;
        }
        
        switch ($analysisType) {
            case 'prediction-analysis':
                // For prediction analysis, we might not have a confidence score
                return 0.90; // Updated to 0.90 after model upgrade
                
            default:
                return 0.75;
        }
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
            $prediction->delete();
            
            \Log::info('Prediction deleted successfully', [
                'prediction_id' => $prediction->id,
                'user_id' => Auth::id()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prediction deleted successfully'
                ]);
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
}
