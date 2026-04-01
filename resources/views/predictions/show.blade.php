@extends('layouts.app')

@php
    // Helper function to convert markdown **text** to HTML <strong>text</strong>
    function convertMarkdownBold($text) {
        if (!is_string($text)) {
            return $text;
        }
        // Escape HTML first for security
        $escaped = e($text);
        // Convert **text** to HTML <strong>text</strong>
        $converted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
        return $converted;
    }

    $__reportLangMs = (($prediction->input_data['report_language'] ?? 'en') === 'ms');
    $__ui = function (string $en, string $ms) use ($__reportLangMs): string {
        return $__reportLangMs ? $ms : $en;
    };
    $__statusLabel = function (string $status) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return ucfirst($status);
        }
        return match ($status) {
            'completed' => 'Selesai',
            'completed_with_warnings' => 'Selesai (dengan amaran)',
            'processing' => 'Memproses',
            'failed' => 'Gagal',
            'pending' => 'Menunggu',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    };
    $__horizonLabel = function (?string $horizon) use ($__reportLangMs): string {
        if ($horizon === null || $horizon === '') {
            return '';
        }
        if (!$__reportLangMs) {
            return ucwords(str_replace('_', ' ', $horizon));
        }
        $map = [
            'next_two_days' => 'Dua Hari Akan Datang',
            'next_two_weeks' => 'Dua Minggu Akan Datang',
            'next_month' => 'Bulan Akan Datang',
            'three_months' => '3 Bulan',
            'six_months' => '6 Bulan',
            'twelve_months' => '12 Bulan',
            'two_years' => '2 Tahun',
        ];
        return $map[$horizon] ?? ucwords(str_replace('_', ' ', $horizon));
    };
    $__jsonKeyLabel = function (string $key) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return ucfirst(str_replace('_', ' ', $key));
        }
        $map = [
            'point' => 'Perkara',
            'explanation' => 'Penjelasan',
            'risk' => 'Risiko',
            'level' => 'Tahap',
            'probability' => 'Kebarangkalian',
            'mitigation' => 'Strategi Pengurangan',
        ];
        return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
    };
    $__riskScaleLabel = function ($value) use ($__reportLangMs): string {
        $raw = trim(is_string($value) ? $value : (string) $value);
        if (!$__reportLangMs || $raw === '') {
            return $raw;
        }
        $norm = strtolower(preg_replace('/[\s_\-]+/u', ' ', $raw));
        $norm = trim($norm);
        static $map = [
            'critical' => 'Kritikal',
            'severe' => 'Teruk',
            'extreme' => 'Melampau',
            'catastrophic' => 'Katastrofik',
            'high' => 'Tinggi',
            'very high' => 'Sangat tinggi',
            'medium' => 'Sederhana',
            'moderate' => 'Sederhana',
            'low' => 'Rendah',
            'very low' => 'Sangat rendah',
            'minimal' => 'Minimum',
            'negligible' => 'Boleh diabaikan',
            'marginal' => 'Marginal',
            'significant' => 'Ketara',
            'substantial' => 'Bermakna',
            'almost certain' => 'Hampir pasti',
            'very likely' => 'Sangat berkemungkinan',
            'likely' => 'Berkemungkinan',
            'probable' => 'Berkemungkinan',
            'possible' => 'Mungkin',
            'unlikely' => 'Tidak berkemungkinan',
            'very unlikely' => 'Sangat tidak berkemungkinan',
            'rare' => 'Jarang',
            'remote' => 'Jauh (remeh)',
            'improbable' => 'Mustahil',
            'certain' => 'Pasti',
            'uncertain' => 'Tidak pasti',
            'frequent' => 'Kerap',
            'occasional' => 'Kadangkala',
            'seldom' => 'Jarang berlaku',
            'imminent' => 'Hampir berlaku',
            'high risk' => 'Risiko tinggi',
            'medium risk' => 'Risiko sederhana',
            'low risk' => 'Risiko rendah',
        ];
        return $map[$norm] ?? $raw;
    };
    $__localizeScaleInText = function (string $text) use ($__reportLangMs): string {
        if (!$__reportLangMs) {
            return $text;
        }
        static $ordered = null;
        if ($ordered === null) {
            $pairs = [
                'very high' => 'Sangat tinggi',
                'very low' => 'Sangat rendah',
                'very likely' => 'Sangat berkemungkinan',
                'very unlikely' => 'Sangat tidak berkemungkinan',
                'almost certain' => 'Hampir pasti',
                'high risk' => 'Risiko tinggi',
                'medium risk' => 'Risiko sederhana',
                'low risk' => 'Risiko rendah',
                'catastrophic' => 'Katastrofik',
                'substantial' => 'Bermakna',
                'significant' => 'Ketara',
                'negligible' => 'Boleh diabaikan',
                'improbable' => 'Mustahil',
                'moderate' => 'Sederhana',
                'critical' => 'Kritikal',
                'extreme' => 'Melampau',
                'probable' => 'Berkemungkinan',
                'unlikely' => 'Tidak berkemungkinan',
                'certain' => 'Pasti',
                'uncertain' => 'Tidak pasti',
                'minimal' => 'Minimum',
                'severe' => 'Teruk',
                'likely' => 'Berkemungkinan',
                'medium' => 'Sederhana',
                'possible' => 'Mungkin',
                'remote' => 'Jauh (remeh)',
                'rare' => 'Jarang',
                'high' => 'Tinggi',
                'low' => 'Rendah',
                'frequent' => 'Kerap',
                'occasional' => 'Kadangkala',
                'seldom' => 'Jarang berlaku',
                'imminent' => 'Hampir berlaku',
                'marginal' => 'Marginal',
            ];
            uksort($pairs, fn ($a, $b) => strlen($b) <=> strlen($a));
            $ordered = $pairs;
        }
        $out = $text;
        foreach ($ordered as $en => $ms) {
            $out = preg_replace('/\b' . preg_quote($en, '/') . '\b/iu', $ms, $out);
        }
        return $out;
    };
@endphp

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<div class="prediction-show-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div class="prediction-content-wrapper" style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="prediction-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1; min-width: 0;">
                        <h1 class="prediction-topic" style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0; word-wrap: break-word; overflow-wrap: break-word;">{{ $prediction->topic }}</h1>
                        @if($prediction->target)
                            <p class="prediction-target" style="color: #166534; font-size: 13px; margin: 8px 0; font-weight: 500; background: #f0fdf4; padding: 8px 12px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block; max-width: 100%; word-wrap: break-word; overflow-wrap: break-word;">
                                <strong>{{ $__ui('Target:', 'Sasaran:') }}</strong> {{ $prediction->target }}
                            </p>
                        @endif
                        <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                            {{ $__ui('Created on', 'Dicipta pada') }} {{ $prediction->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="header-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('predictions.history') }}" class="action-btn" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            <i class="bi bi-arrow-left"></i> {{ $__ui('Back', 'Kembali') }}
                        </a>
                        @if($prediction->status === 'completed')
                        <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" class="action-btn" style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); cursor: pointer;">
                            {{ $__ui('Export PDF', 'Eksport PDF') }}
                        </button>
                        @endif
                        <a href="{{ route('predictions.create') }}" class="action-btn" style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                            {{ $__ui('New Analysis', 'Analisis Baharu') }}
                        </a>
                    </div>
                </div>

                @include('predictions.partials.report_sections', compact('prediction'))

            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        /* Container and card padding */
        .prediction-show-container {
            padding: 16px 8px !important;
        }
        
        .prediction-content-wrapper {
            padding: 0 !important;
        }
        
        .prediction-main-card {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Header section */
        .prediction-topic {
            font-size: 18px !important;
            line-height: 1.4 !important;
        }
        
        .prediction-target {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        
        /* Header actions - keep in one row on mobile */
        .header-actions {
            flex-direction: row !important;
            width: 100% !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
        }
        
        .header-actions .action-btn {
            flex: 1 !important;
            min-width: 0 !important;
            justify-content: center !important;
            padding: 10px 8px !important;
            font-size: 11px !important;
            min-height: 44px !important;
        }
        
        /* Issue details */
        .issue-details-container {
            padding: 12px !important;
        }
        
        .issue-details-text {
            font-size: 13px !important;
        }
        
        /* File items */
        .file-item {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        
        .file-info {
            margin-bottom: 8px !important;
        }
        
        .file-name {
            font-size: 13px !important;
            word-break: break-word !important;
        }
        
        .file-download-btn {
            width: 100% !important;
            text-align: center !important;
        }
        
        /* Extracted text */
        .extracted-text-container {
            padding: 12px !important;
            max-height: 300px !important;
        }
        
        .extracted-text {
            font-size: 12px !important;
        }
        
        /* Source URLs */
        .source-url-link {
            font-size: 12px !important;
            padding: 10px !important;
        }
        
        .source-url-text {
            word-break: break-all !important;
        }
        
        /* Source detail URLs */
        .source-detail-url {
            word-break: break-all !important;
            display: inline-block !important;
            max-width: 100% !important;
        }
        
        /* Risk table - make scrollable */
        .risk-table-container {
            margin: 0 -16px !important;
            padding: 0 16px !important;
        }
        
        .risk-table {
            font-size: 12px !important;
        }
        
        .risk-table th,
        .risk-table td {
            padding: 8px 6px !important;
            font-size: 11px !important;
        }
        
        /* Section headings */
        h2 {
            font-size: 14px !important;
        }
        
        h3 {
            font-size: 16px !important;
        }
        
        h4 {
            font-size: 14px !important;
        }
        
        /* Text content */
        p, li, div[style*="font-size: 14px"] {
            font-size: 13px !important;
        }
        
        /* Code/pre blocks */
        pre {
            font-size: 11px !important;
            padding: 12px !important;
        }
        
        /* Extracted content section */
        div[style*="background: white; border-radius: 20px"] {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Status section */
        div[style*="display: flex; align-items: center; gap: 16px"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Very small screens */
        .prediction-show-container {
            padding: 12px 4px !important;
        }
        
        .prediction-main-card {
            padding: 16px 12px !important;
            border-radius: 10px !important;
        }
        
        .prediction-topic {
            font-size: 16px !important;
        }
        
        .header-actions {
            gap: 6px !important;
        }
        
        .header-actions .action-btn {
            padding: 10px 6px !important;
            font-size: 10px !important;
            min-height: 42px !important;
        }
        
        .issue-details-container {
            padding: 10px !important;
        }
        
        .issue-details-text {
            font-size: 12px !important;
        }
        
        .extracted-text-container {
            padding: 10px !important;
            max-height: 250px !important;
        }
        
        .extracted-text {
            font-size: 11px !important;
        }
        
        .risk-table {
            font-size: 11px !important;
            min-width: 500px !important;
        }
        
        .risk-table th,
        .risk-table td {
            padding: 6px 4px !important;
            font-size: 10px !important;
        }
        
        h2 {
            font-size: 13px !important;
        }
        
        h3 {
            font-size: 15px !important;
        }
        
        h4 {
            font-size: 13px !important;
        }
        
        p, li, div[style*="font-size: 14px"] {
            font-size: 12px !important;
        }
        
        pre {
            font-size: 10px !important;
            padding: 10px !important;
        }
        
        .source-url-link {
            font-size: 11px !important;
            padding: 8px !important;
        }
        
        /* Modal improvements for very small screens */
        .export-modal-overlay {
            padding: 12px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .export-modal-content {
            padding: 20px 16px !important;
        }
        
        .export-modal-content h3 {
            font-size: 16px !important;
        }
        
        .export-modal-content p {
            font-size: 12px !important;
        }
        
        .export-modal-content button {
            padding: 10px 16px !important;
            font-size: 12px !important;
            min-height: 42px !important;
        }
    }
    
    /* Modal responsive styles */
    @media (max-width: 768px) {
        .export-modal-overlay {
            padding: 16px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .export-modal-content {
            padding: 24px 20px !important;
            max-width: 100% !important;
        }
        
        .export-modal-content h3 {
            font-size: 18px !important;
        }
        
        .export-modal-content p {
            font-size: 14px !important;
        }
        
        .export-modal-content button {
            padding: 12px 20px !important;
            font-size: 14px !important;
            min-height: 44px !important;
        }
        
        .export-modal-content div[style*="display: flex; gap: 16px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .export-modal-content div[style*="display: flex; gap: 16px"] button {
            width: 100% !important;
        }
    }
    
    /* Ensure all text wraps properly */
    * {
        box-sizing: border-box;
    }
    
    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }
</style>

<!-- Export Confirmation Modal -->
<div id="exportModal" class="export-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="export-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #10b981;">📄</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">{{ $__ui('Export PDF Report', 'Laporan PDF Eksport') }}</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">{{ $__ui('Are you ready to export this prediction analysis as a PDF report?', 'Adakah anda bersedia untuk mengeksport analisis ramalan ini sebagai laporan PDF?') }}</p>
        <p id="exportTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">{{ $__ui('The report will include all analysis details and NUJUM insights.', 'Laporan akan merangkumi semua butiran analisis dan pandangan NUJUM.') }}</p>
        
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="closeExportModal()" 
                    style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                {{ $__ui('Cancel', 'Batal') }}
            </button>
            <button id="confirmExportBtn" 
                    style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                {{ $__ui('Export PDF', 'Eksport PDF') }}
            </button>
        </div>
    </div>
</div>

<script>
// Export modal functions
let currentExportId = null;

function confirmExport(predictionId, topic) {
    currentExportId = predictionId;
    document.getElementById('exportTopic').textContent = topic;
    document.getElementById('exportModal').style.display = 'flex';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
    currentExportId = null;
}

function exportPrediction() {
    if (!currentExportId) return;
    
    // Redirect to the export route
    window.location.href = '{{ url("/predictions") }}/' + currentExportId + '/export';
}

// Set up the confirm export button
document.getElementById('confirmExportBtn').onclick = exportPrediction;

// Close export modal when clicking outside
document.getElementById('exportModal').onclick = function(e) {
    if (e.target === this) {
        closeExportModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExportModal();
    }
});
</script>
@endsection
