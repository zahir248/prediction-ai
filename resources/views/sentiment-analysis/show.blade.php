@extends('layouts.app')

@section('content')
<div class="social-show-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div class="social-show-wrapper" style="max-width: 900px; margin: 0 auto;">
        <div class="social-show-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 28px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div style="flex: 1; min-width: 200px;">
                        <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #7c3aed; margin: 0;">Sentiment analysis</p>
                    </div>
                    <div class="social-header-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('sentiment-analysis.history') }}" class="social-action-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            ← History
                        </a>
                        <button type="button" onclick="confirmSentimentShowExport({{ $comparison->id }})" class="social-action-btn" style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
                            Export PDF
                        </button>
                        <a href="{{ route('sentiment-analysis.index') }}" class="social-action-btn" style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                            Compare again
                        </a>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">{{ session('status') }}</div>
            @endif
            @if (session('warning'))
                <div style="background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">{{ session('warning') }}</div>
            @endif

            @include('sentiment-analysis.partials.comparison-content', ['comparison' => $comparison])
        </div>
    </div>
</div>

<div id="sentimentShowExportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;"><span style="font-size: 48px; color: #10b981;">📄</span></div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this sentiment comparison as a PDF report?</p>
        @php $vsWord = ($comparison->report_language ?? 'en') === 'ms' ? 'lwn' : 'vs'; @endphp
        <p id="sentimentShowExportLabel" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">{{ $comparison->socialMediaAnalysisA?->profileDisplayLabel() }} {{ $vsWord }} {{ $comparison->socialMediaAnalysisB?->profileDisplayLabel() }}</p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all comparison details and NUJUM insights.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="button" onclick="closeSentimentShowExportModal()" style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer;">Cancel</button>
            <button type="button" id="confirmSentimentShowExportBtn" style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">Export PDF</button>
        </div>
    </div>
</div>

@include('sentiment-analysis.partials.sentiment-charts-init')
<script>
var sentimentShowExportId = null;
function confirmSentimentShowExport(id) {
    sentimentShowExportId = id;
    var m = document.getElementById('sentimentShowExportModal');
    if (m) {
        m.style.display = 'flex';
    }
}
function closeSentimentShowExportModal() {
    sentimentShowExportId = null;
    var m = document.getElementById('sentimentShowExportModal');
    if (m) {
        m.style.display = 'none';
    }
}
function doSentimentShowExport() {
    if (!sentimentShowExportId) {
        return;
    }
    var n = parseInt(sentimentShowExportId, 10);
    closeSentimentShowExportModal();
    window.location.href = '{{ url('/sentiment-analysis') }}/' + n + '/export';
}
document.addEventListener('DOMContentLoaded', function () {
    var inner = document.querySelector('.sentiment-comparison-inner');
    if (inner && typeof window.initSentimentReportChartsFromDom === 'function') {
        window.initSentimentReportChartsFromDom(inner);
    }
    var b = document.getElementById('confirmSentimentShowExportBtn');
    if (b) {
        b.onclick = doSentimentShowExport;
    }
    var modal = document.getElementById('sentimentShowExportModal');
    if (modal) {
        modal.onclick = function (e) {
            if (e.target === modal) {
                closeSentimentShowExportModal();
            }
        };
    }
});
</script>
@endsection
