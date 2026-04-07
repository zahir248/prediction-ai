<?php

namespace App\Http\Controllers;

use App\Models\SentimentComparison;
use App\Models\SocialMediaAnalysis;
use App\Services\AnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService) {}

    public function index()
    {
        $completedAnalyses = collect();
        if (Auth::check()) {
            $completedAnalyses = Auth::user()
                ->socialMediaAnalyses()
                ->where('status', SocialMediaAnalysis::STATUS_COMPLETED)
                ->whereNotNull('ai_analysis')
                ->orderByDesc('created_at')
                ->get(['id', 'username', 'created_at', 'platform_data']);
        }

        return view('sentiment-analysis.index', compact('completedAnalyses'));
    }

    public function history(Request $request)
    {
        $query = Auth::user()->sentimentComparisons()
            ->with([
                'socialMediaAnalysisA:id,username,platform_data',
                'socialMediaAnalysisB:id,username,platform_data',
            ]);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('socialMediaAnalysisA', fn ($q2) => $q2->where('username', 'like', '%'.$search.'%'))
                    ->orWhereHas('socialMediaAnalysisB', fn ($q2) => $q2->where('username', 'like', '%'.$search.'%'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        if ($request->filled('report_language')) {
            $lang = $request->get('report_language');
            if (in_array($lang, ['en', 'ms'], true)) {
                $query->where('report_language', $lang);
            }
        }

        $comparisons = $query->orderByDesc('created_at')->limit(500)->get();

        return view('sentiment-analysis.history', compact('comparisons'));
    }

    public function compare(Request $request)
    {
        $wantsJson = $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $request->validate([
            'analysis_a_id' => 'required|integer|exists:social_media_analyses,id',
            'analysis_b_id' => 'required|integer|exists:social_media_analyses,id|different:analysis_a_id',
            'report_language' => 'required|in:en,ms',
        ]);

        $reportLanguage = $request->input('report_language');

        $analysisA = SocialMediaAnalysis::findOrFail($request->integer('analysis_a_id'));
        $analysisB = SocialMediaAnalysis::findOrFail($request->integer('analysis_b_id'));

        if ((int) Auth::id() !== (int) $analysisA->user_id || (int) Auth::id() !== (int) $analysisB->user_id) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to one or both analyses.',
                ], 403);
            }
            abort(403, 'Unauthorized access to one or both analyses.');
        }

        if ($analysisA->status !== SocialMediaAnalysis::STATUS_COMPLETED
            || $analysisB->status !== SocialMediaAnalysis::STATUS_COMPLETED) {
            $msg = 'Both profiles must have a completed social media analysis.';
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['compare' => [$msg]],
                ], 422);
            }

            return back()->withErrors([
                'compare' => $msg,
            ])->withInput();
        }

        if (empty($analysisA->ai_analysis) || ! is_array($analysisA->ai_analysis)
            || empty($analysisB->ai_analysis) || ! is_array($analysisB->ai_analysis)) {
            $msg = 'Both analyses must include AI profile results. Re-run analysis if needed.';
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['compare' => [$msg]],
                ], 422);
            }

            return back()->withErrors([
                'compare' => $msg,
            ])->withInput();
        }

        $startTime = microtime(true);

        $payload = [
            'profile_a' => [
                'username' => $analysisA->username,
                'social_media_analysis_id' => $analysisA->id,
                'ai_analysis' => $analysisA->ai_analysis,
            ],
            'profile_b' => [
                'username' => $analysisB->username,
                'social_media_analysis_id' => $analysisB->id,
                'ai_analysis' => $analysisB->ai_analysis,
            ],
        ];

        $text = "TASK: Compare sentiment and emotional tone between two social media profile analyses (structured JSON below).\n"
            ."Use only the provided analysis content; do not invent platform data.\n\n"
            .json_encode($payload, JSON_UNESCAPED_UNICODE);

        $analytics = $this->analyticsService->startAnalysisWithoutPrediction((int) Auth::id(), [
            'text' => $text,
            'analysis_type' => 'sentiment-comparison',
        ]);

        $aiService = \App\Services\AIServiceFactory::create();
        $result = $aiService->analyzeText(
            $text,
            'sentiment-comparison',
            null,
            null,
            $analytics,
            null,
            $reportLanguage
        );

        $processingTime = round(microtime(true) - $startTime, 3);

        if ($analytics) {
            $this->analyticsService->completeAnalysis($analytics, [
                'total_processing_time' => $processingTime,
                'api_error_message' => $this->isSuccessfulSentimentResult($result) ? null : 'Sentiment comparison failed',
            ]);
        }

        $success = $this->isSuccessfulSentimentResult($result);
        if (is_array($result)) {
            $result['report_language'] = $reportLanguage;
        }

        $record = SentimentComparison::create([
            'user_id' => Auth::id(),
            'social_media_analysis_a_id' => $analysisA->id,
            'social_media_analysis_b_id' => $analysisB->id,
            'ai_result' => is_array($result) ? $result : ['raw_response' => $result],
            'report_language' => $reportLanguage,
            'processing_time' => $processingTime,
            'model_used' => \App\Services\AIServiceFactory::getCurrentProvider(),
        ]);

        if (! $success) {
            Log::warning('Sentiment comparison completed with fallback or invalid result', [
                'user_id' => Auth::id(),
                'sentiment_comparison_id' => $record->id,
            ]);
        }

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'comparison_id' => $record->id,
                'incomplete' => ! $success,
                'message' => $success
                    ? 'Sentiment comparison is ready.'
                    : 'Comparison was saved but the AI returned an incomplete result. You may retry.',
            ]);
        }

        return redirect()
            ->route('sentiment-analysis.show', $record)
            ->with($success ? 'status' : 'warning', $success
                ? 'Sentiment comparison is ready.'
                : 'Comparison was saved but the AI returned an incomplete result. You may retry.');
    }

    public function show(SentimentComparison $sentimentComparison)
    {
        if ((int) Auth::id() !== (int) $sentimentComparison->user_id) {
            abort(403);
        }

        $sentimentComparison->load([
            'socialMediaAnalysisA:id,username,status,created_at,platform_data',
            'socialMediaAnalysisB:id,username,status,created_at,platform_data',
        ]);

        return view('sentiment-analysis.show', ['comparison' => $sentimentComparison]);
    }

    public function comparisonHtml(SentimentComparison $sentimentComparison)
    {
        if ((int) Auth::id() !== (int) $sentimentComparison->user_id) {
            abort(403);
        }

        $sentimentComparison->load([
            'socialMediaAnalysisA:id,username,status,created_at,platform_data',
            'socialMediaAnalysisB:id,username,status,created_at,platform_data',
        ]);

        return view('sentiment-analysis.partials.comparison-content', [
            'comparison' => $sentimentComparison,
        ]);
    }

    /**
     * Export sentiment comparison to PDF (same DomPDF setup as social media analysis export).
     */
    public function export(SentimentComparison $sentimentComparison)
    {
        if (! Auth::check()) {
            abort(401, 'User not authenticated.');
        }

        if ((int) Auth::id() !== (int) $sentimentComparison->user_id) {
            abort(403, 'Unauthorized access to comparison.');
        }

        $sentimentComparison->load([
            'socialMediaAnalysisA:id,username,platform_data',
            'socialMediaAnalysisB:id,username,platform_data',
        ]);

        $html = view('sentiment-analysis.export-pdf', compact('sentimentComparison'))->render();

        $pdf = Pdf::loadHTML($html);

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
            'enable-local-file-access' => true,
        ]);

        $dompdf = $pdf->getDomPDF();
        $dompdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_page', 'f' => function ($infos) {
                    $canvas = $infos['canvas'];
                    $fontMetrics = $infos['fontMetrics'];
                    $font = $fontMetrics->getFont('Times New Roman', 'normal');
                    $size = 9;
                    $pageText = '{PAGE_NUM}';
                    $y = $canvas->get_height() - 24;
                    $pageWidth = 595.28;
                    $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                    $x = ($pageWidth / 2) - ($textWidth / 2) + 25;
                    $canvas->page_text($x, $y, $pageText, $font, $size, [0, 0, 0]);
                },
            ],
        ]);

        $filename = 'sentiment_comparison_'.$sentimentComparison->id.'_'.date('Y-m-d_H-i-s').'.pdf';

        return $pdf->download($filename);
    }

    public function destroy(SentimentComparison $sentimentComparison)
    {
        if ((int) Auth::id() !== (int) $sentimentComparison->user_id) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized access'], 403);
            }
            abort(403);
        }

        $sentimentComparison->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comparison deleted successfully',
            ]);
        }

        return redirect()
            ->route('sentiment-analysis.history')
            ->with('status', 'Comparison removed.');
    }

    private function isSuccessfulSentimentResult(mixed $result): bool
    {
        if (! is_array($result) || ! isset($result['title'])) {
            return false;
        }
        if (($result['status'] ?? '') === 'error') {
            return false;
        }

        return true;
    }
}
