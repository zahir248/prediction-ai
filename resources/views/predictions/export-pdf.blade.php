@php
    // Helper function to convert markdown **text** to HTML <strong>text</strong>
    function convertMarkdownBold($text) {
        if (!is_string($text)) {
            return $text;
        }
        // Escape HTML first for security
        $escaped = e($text);
        // Convert **text** to <strong>text</strong>
        $converted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
        return $converted;
    }

    $__inputData = is_array($prediction->input_data ?? null) ? $prediction->input_data : [];
    $__reportLangMs = (($__inputData['report_language'] ?? 'en') === 'ms');
    $__pdfLang = $__reportLangMs ? 'ms' : 'en';
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
<!DOCTYPE html>
<html lang="{{ $__pdfLang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $__ui('Prediction Analysis Report', 'Laporan Analisis Ramalan') }}</title>
    <style>
        @page {
            margin: 1.2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
            font-size: 10pt;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #000;
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .header p {
            color: #333;
            margin: 3px 0 0 0;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
        }
        
        .header .subtitle {
            font-size: 12pt;
            margin-top: 8px;
            font-weight: normal;
            text-align: center;
        }
        
        .header h1 {
            text-align: center;
        }
        
        .section {
            margin-bottom: 18px;
            page-break-inside: avoid;
            orphans: 3;
            widows: 3;
        }
        
        .section-title {
            background-color: #f0f0f0;
            color: #000;
            padding: 6px 10px;
            font-size: 12pt;
            font-weight: bold;
            border-left: 3px solid #000;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .subsection-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 4px 8px;
            background-color: #f9f9f9;
            font-weight: bold;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9pt;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 4px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9pt;
        }
        
        .prediction-content {
            padding: 8px;
            margin-bottom: 12px;
        }
        
        .prediction-content h3 {
            color: #000;
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 10pt;
            font-weight: bold;
        }
        
        .prediction-content p {
            margin: 6px 0;
            text-align: justify;
            font-size: 9pt;
        }
        
        .factors-list, .recommendations-list {
            margin: 6px 0;
            padding-left: 18px;
        }
        
        .factors-list li, .recommendations-list li {
            margin-bottom: 4px;
            font-size: 9pt;
            text-align: justify;
        }
        
        .confidence-badge {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 8pt;
        }
        
        .footer p {
            text-align: center;
            margin: 4px 0;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .page-break-after {
            page-break-after: always;
        }
        
        .avoid-break {
            page-break-inside: avoid;
        }
        
        .highlight-box {
            padding: 8px;
            margin: 8px 0;
            border-left: 3px solid #ffcc00;
            text-align: justify;
        }
        
        .data-point {
            padding: 6px;
            margin: 6px 0;
            border-left: 3px solid #b3d9ff;
            text-align: justify;
        }
        
        .risk-level {
            font-weight: bold;
            color: #d32f2f;
        }
        
        .status-badge {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .status-completed {
            color: #2e7d32;
            background-color: #e8f5e9;
        }
        
        .status-processing {
            color: #f57c00;
            background-color: #fff3e0;
        }
        
        .status-failed {
            color: #d32f2f;
            background-color: #ffebee;
        }
        
        .status-pending {
            color: #1976d2;
            background-color: #e3f2fd;
        }
        
        .mitigation {
            padding: 8px;
            margin: 8px 0;
            border-left: 3px solid #4caf50;
        }
        
        .mitigation h4 {
            color: #2e7d32;
            margin-top: 0;
            font-size: 10pt;
        }
        
        .recommendations {
            padding: 10px;
            margin: 12px 0;
            border-left: 3px solid #ff9800;
        }
        
        .recommendations h4 {
            color: #e65100;
            margin-top: 0;
            font-size: 11pt;
        }
        
        .page-number {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 15px;
        }
        
        /* Compact spacing for lists */
        ul, ol {
            margin: 4px 0;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 3px;
        }
        
        /* Compact spacing for paragraphs */
        p {
            margin: 4px 0;
            text-align: justify;
        }
        
        /* Ensure proper page breaks for major sections */
        .major-section {
            page-break-before: auto;
            page-break-after: auto;
            page-break-inside: avoid;
        }
        
        /* Force page break for very long sections */
        .force-break {
            page-break-before: always;
        }
        
        /* Additional page break controls */
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }
        
        .page-break-before-auto {
            page-break-before: auto;
        }
        
        .page-break-after-avoid {
            page-break-after: avoid;
        }
        
        /* Ensure content doesn't get cut at page boundaries */
        .content-wrapper {
            min-height: 100px;
            page-break-inside: avoid;
        }
        
        /* Compact table styling */
        .compact-table {
            font-size: 9pt;
            line-height: 1.2;
        }
        
        .compact-table td, .compact-table th {
            padding: 3px 6px;
            border: 1px solid #ddd;
        }
        
        /* Risk Assessment table styling */
        .risk-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        
        .risk-table th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #333;
            font-size: 9pt;
        }
        
        .risk-table td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 9pt;
            text-align: center;
        }
        
        .risk-table td.mitigation-cell {
            text-align: justify;
        }
        
        .risk-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .section {
                page-break-inside: avoid;
                orphans: 3;
                widows: 3;
            }
            
            .major-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $__ui('Prediction Analysis Report', 'Laporan Analisis Ramalan') }}</h1>
        <p class="subtitle">{{ $prediction->topic }}</p>
        @if($prediction->target)
        <p style="color: #059669; font-weight: bold; margin: 8px 0; text-align: center;">{{ $__ui('Target Focus:', 'Fokus Sasaran:') }} {{ $prediction->target }}</p>
        @endif
        <p style="text-align: center;">{{ $__ui('Generated on', 'Dijana pada') }} {{ date('F d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Executive Summary -->
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Executive Summary', 'Ringkasan Eksekutif') }}</div>
        <div class="prediction-content">
            @if($prediction->target)
            <div class="highlight-box" style="border-left-color: #4caf50;">
                <strong>{{ $__ui('Target-Focused Analysis:', 'Analisis Berfokus Sasaran:') }}</strong><br>
                {{ $__ui('This analysis specifically focuses on how predictions, risks, and strategic implications will affect:', 'Analisis ini tertumpu kepada bagaimana ramalan, risiko, dan implikasi strategik akan mempengaruhi:') }} <strong>{{ $prediction->target }}</strong>
            </div>
            @endif
            
            <p>{{ $__ui('This comprehensive analysis provides detailed insights into the prediction results generated by our NUJUM system. The analysis covers key findings, risk assessments, and strategic recommendations based on the input data and NUJUM-generated predictions.', 'Analisis komprehensif ini memberikan pandangan terperinci ke atas keputusan ramalan yang dijana oleh sistem NUJUM kami. Analisis merangkumi penemuan utama, penilaian risiko, dan cadangan strategik berdasarkan data input dan ramalan NUJUM.') }}</p>
            
            @if(($prediction->status === 'completed' || $prediction->status === 'completed_with_warnings') && $prediction->prediction_result)
                @if(isset($prediction->prediction_result['executive_summary']))
                    <div class="highlight-box">
                        <strong>{{ $__ui('NUJUM-Generated Summary:', 'Ringkasan Dijana NUJUM:') }}</strong><br>
                        {!! convertMarkdownBold($prediction->prediction_result['executive_summary']) !!}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Analysis Information -->
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Analysis Information', 'Maklumat Analisis') }}</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">{{ $__ui('Topic', 'Topik') }}</div>
                <div class="info-value">{{ $prediction->topic }}</div>
            </div>
            @if($prediction->target)
            <div class="info-row">
                <div class="info-label">{{ $__ui('Target Focus', 'Fokus Sasaran') }}</div>
                <div class="info-value">{{ $prediction->target }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">{{ $__ui('Prediction Horizon', 'Horizon Ramalan') }}</div>
                <div class="info-value">{{ $__horizonLabel($prediction->prediction_horizon) }}</div>
            </div>
            @php
                $__pdfReportLang = $__inputData['report_language'] ?? null;
            @endphp
            @if($__pdfReportLang === 'en' || $__pdfReportLang === 'ms')
            <div class="info-row">
                <div class="info-label">{{ $__ui('Report language', 'Bahasa laporan') }}</div>
                <div class="info-value">{{ $__pdfReportLang === 'ms' ? 'Bahasa Melayu' : 'English' }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">{{ $__ui('Status', 'Status') }}</div>
                <div class="info-value">
                    @php
                        $statusClass = 'status-' . strtolower($prediction->status);
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $__statusLabel($prediction->status) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Confidence Score', 'Skor Keyakinan') }}</div>
                <div class="info-value">
                    @if(isset($prediction->confidence_score) && $prediction->confidence_score !== null && is_numeric($prediction->confidence_score))
                        <div class="confidence-badge">
                            {{ number_format((float) $prediction->confidence_score * 100, 1) }}%
                        </div>
                    @else
                        <span style="color: #666;">{{ $__ui('Not available', 'Tidak tersedia') }}</span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">{{ $__ui('Processing Time', 'Masa Pemprosesan') }}</div>
                <div class="info-value">
                    @if(isset($prediction->processing_time) && is_numeric($prediction->processing_time))
                        {{ number_format((float) $prediction->processing_time, 3) }} {{ $__ui('seconds', 'saat') }}
                    @else
                        <span style="color: #666;">{{ $__ui('Not available', 'Tidak tersedia') }}</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Created At', 'Dicipta Pada') }}</div>
                <div class="info-value">{{ $prediction->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Input Data Analysis -->
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Input Data Analysis', 'Analisis Data Input') }}</div>
        <div class="prediction-content">
            <h3>{{ $__ui('Analysis Request', 'Permintaan Analisis') }}</h3>
            <div class="data-point">
                <p>{{ is_string($__inputData['text'] ?? '') ? $__inputData['text'] : $__ui('No input data available', 'Tiada data input tersedia') }}</p>
            </div>
            
            <h3>{{ $__ui('Data Context', 'Konteks Data') }}</h3>
            <p>{{ $__ui('This analysis is based on the provided input data and leverages advanced NUJUM algorithms to generate comprehensive predictions and insights. The system processes multiple data points to identify patterns, risks, and opportunities.', 'Analisis ini berdasarkan data input yang diberikan dan memanfaatkan algoritma NUJUM termaju untuk menjana ramalan dan pandangan komprehensif. Sistem memproses pelbagai titik data untuk mengenal pasti corak, risiko, dan peluang.') }}</p>
        </div>
    </div>

    <!-- Source References -->
    @if($prediction->source_urls && count($prediction->source_urls) > 0)
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Source References', 'Rujukan Sumber') }}</div>
        <div class="prediction-content">
            <h3>{{ $__ui('Additional Source Information', 'Maklumat Sumber Tambahan') }}</h3>
            <div class="data-point">
                @foreach($prediction->source_urls as $index => $sourceUrl)
                <p style="margin-bottom: 6px;"><strong>{{ $__ui('Source', 'Sumber') }} {{ $index + 1 }}:</strong> <a href="{{ $sourceUrl }}">{{ $sourceUrl }}</a></p>
                @endforeach
            </div>
            <p>{{ $__ui('These sources were referenced during the NUJUM analysis to provide additional context and data points for the prediction. The analysis incorporates information from both the user input and these external sources.', 'Sumber ini dirujuk semasa analisis NUJUM untuk memberikan konteks tambahan dan titik data bagi ramalan. Analisis menggabungkan maklumat daripada input pengguna dan sumber luaran ini.') }}</p>
        </div>
    </div>
    @endif

    <!-- Source Analysis Section -->
    @if($prediction->source_urls && count($prediction->source_urls) > 0 && isset($prediction->prediction_result['source_analysis']))
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Source Analysis & Influence', 'Analisis & Pengaruh Sumber') }}</div>
        <div class="prediction-content">
            <h3>{{ $__ui('How Sources Influenced This Analysis', 'Bagaimana Sumber Mempengaruhi Analisis Ini') }}</h3>
            <div class="data-point">
                <p>{!! nl2br(convertMarkdownBold($prediction->prediction_result['source_analysis'])) !!}</p>
            </div>
            <p>{{ $__ui('This analysis shows how each provided source contributed to specific predictions and conclusions, ensuring transparency and traceability of insights.', 'Analisis ini menunjukkan bagaimana setiap sumber menyumbang kepada ramalan dan kesimpulan tertentu, demi ketelusan dan kebolehkesanan pandangan.') }}</p>
        </div>
    </div>
    @endif

    <!-- NUJUM Analysis Results - Force page break for this major section -->
    @if(($prediction->status === 'completed' || $prediction->status === 'completed_with_warnings') && $prediction->prediction_result)
        <div class="section major-section force-break">
            <div class="section-title">{{ $__ui('NUJUM Analysis Results', 'Keputusan Analisis NUJUM') }}</div>
            
            @if(isset($prediction->prediction_result['note']) && is_string($prediction->prediction_result['note']))
                <div class="highlight-box">
                    <strong>{{ $__ui('Important Note:', 'Nota Penting:') }}</strong> {!! convertMarkdownBold($prediction->prediction_result['note']) !!}
                </div>
            @endif
            
            @if(isset($prediction->prediction_result['title']) && is_string($prediction->prediction_result['title']))
                @php $report = $prediction->prediction_result; @endphp
                
                <!-- Title and Horizon -->
                <div class="prediction-content">
                    <h3 style="color: #000; text-align: center; margin-bottom: 8px;">{{ $report['title'] }}</h3>
                    @if(isset($report['prediction_horizon']) && is_string($report['prediction_horizon']))
                        <p style="text-align: center; color: #666; margin-bottom: 15px;">
                            <strong>{{ $__ui('Prediction Horizon:', 'Horizon Ramalan:') }}</strong> {{ $report['prediction_horizon'] }}
                        </p>
                    @endif
                </div>
                
                <!-- Executive Summary -->
                @if(isset($report['executive_summary']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Executive Summary', 'Ringkasan Eksekutif') }}</h3>
                        @if(is_array($report['executive_summary']))
                            @foreach($report['executive_summary'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ $__jsonKeyLabel($key) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['executive_summary']))
                            <p>{!! convertMarkdownBold($report['executive_summary']) !!}</p>
                        @else
                            <p>{!! convertMarkdownBold((string)$report['executive_summary']) !!}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Current Situation -->
                @if(isset($report['current_situation']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Current Situation & Future Implications', 'Situasi Semasa & Implikasi Masa Hadapan') }}</h3>
                        @if(is_array($report['current_situation']))
                            @foreach($report['current_situation'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ $__jsonKeyLabel($key) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['current_situation']))
                            <p>{!! convertMarkdownBold($report['current_situation']) !!}</p>
                        @else
                            <p>{!! convertMarkdownBold((string)$report['current_situation']) !!}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Key Factors -->
                @if(isset($report['key_factors']) && is_array($report['key_factors']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Key Factors for Future Development', 'Faktor Utama untuk Pembangunan Masa Hadapan') }}</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['key_factors'] as $factor)
                                <li style="margin-bottom: 12px; text-align: justify;">
                                    @if(is_array($factor) && isset($factor['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($factor['point']) !!}</div>
                                        @if(isset($factor['explanation']) && !empty($factor['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px; text-align: justify;">
                                                {!! convertMarkdownBold($factor['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($factor))
                                        <!-- Legacy format handling -->
                                        @foreach($factor as $key => $value)
                                            @if(is_string($value))
                                                <div style="text-align: justify;"><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</div>
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($factor))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($factor) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold((string)$factor) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Future Predictions -->
                @if(isset($report['future_predictions']) && is_array($report['future_predictions']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Future Predictions', 'Ramalan Masa Hadapan') }}</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['future_predictions'] as $prediction_item)
                                <li style="margin-bottom: 12px; text-align: justify;">
                                    @if(is_array($prediction_item) && isset($prediction_item['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($prediction_item['point']) !!}</div>
                                        @if(isset($prediction_item['explanation']) && !empty($prediction_item['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px; text-align: justify;">
                                                {!! convertMarkdownBold($prediction_item['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($prediction_item))
                                        <!-- Legacy format handling -->
                                        @foreach($prediction_item as $key => $value)
                                            @if(is_string($value))
                                                <div style="text-align: justify;"><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</div>
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($prediction_item))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($prediction_item) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold((string)$prediction_item) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif(isset($report['predictions']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Future Predictions', 'Ramalan Masa Hadapan') }}</h3>
                        @if(is_array($report['predictions']))
                            <ul class="factors-list" style="list-style: none; padding-left: 0;">
                                @foreach($report['predictions'] as $prediction_item)
                                    <li style="margin-bottom: 12px; text-align: justify;">
                                        @if(is_array($prediction_item) && isset($prediction_item['point']))
                                            <!-- New format with point and explanation -->
                                            <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($prediction_item['point']) !!}</div>
                                            @if(isset($prediction_item['explanation']) && !empty($prediction_item['explanation']))
                                                <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                    {!! convertMarkdownBold($prediction_item['explanation']) !!}
                                                </div>
                                            @endif
                                        @elseif(is_array($prediction_item))
                                            <!-- Legacy format handling -->
                                            @foreach($prediction_item as $key => $value)
                                                @if(is_string($value))
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}
                                                @elseif(is_array($value))
                                                    <div style="margin-top: 6px;">
                                                        <strong>{{ $__jsonKeyLabel($key) }}:</strong>
                                                        <ul style="margin: 6px 0 0 18px;">
                                                            @foreach($value as $item)
                                                                <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                                @endif
                                            @endforeach
                                        @elseif(is_string($prediction_item))
                                            <!-- Legacy string format -->
                                            <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($prediction_item) !!}</div>
                                        @else
                                            <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold((string)$prediction_item) !!}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @elseif(is_string($report['predictions']))
                            <p>{{ $report['predictions'] }}</p>
                        @else
                            <p>{{ (string)$report['predictions'] }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Risk Assessment -->
                @if(isset($report['risk_assessment']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Risk Assessment', 'Penilaian Risiko') }}</h3>
                        @if(is_array($report['risk_assessment']))
                            @php
                                $isArrayOfObjects = false;
                                $firstItem = reset($report['risk_assessment']);
                                if (is_array($firstItem) && isset($firstItem['risk'])) {
                                    $isArrayOfObjects = true;
                                }
                            @endphp
                            
                            @if($isArrayOfObjects)
                                <!-- Display as table if it's an array of risk objects -->
                                <table class="risk-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">{{ $__ui('Risk', 'Risiko') }}</th>
                                            <th style="width: 15%;">{{ $__ui('Level', 'Tahap') }}</th>
                                            <th style="width: 15%;">{{ $__ui('Probability', 'Kebarangkalian') }}</th>
                                            <th style="width: 35%;">{{ $__ui('Mitigation Strategy', 'Strategi Pengurangan') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report['risk_assessment'] as $risk)
                                            @if(is_array($risk))
                                                <tr>
                                                    <td>{!! isset($risk['risk']) ? (is_string($risk['risk']) ? convertMarkdownBold($risk['risk']) : convertMarkdownBold((string)$risk['risk'])) : $__ui('N/A', 'T/B') !!}</td>
                                                    <td>{!! isset($risk['level']) ? convertMarkdownBold($__riskScaleLabel($risk['level'])) : '-' !!}</td>
                                                    <td>{!! isset($risk['probability']) ? convertMarkdownBold($__riskScaleLabel($risk['probability'])) : '-' !!}</td>
                                                    <td class="mitigation-cell">{!! isset($risk['mitigation']) ? (is_string($risk['mitigation']) ? convertMarkdownBold($risk['mitigation']) : convertMarkdownBold((string)$risk['mitigation'])) : '-' !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <!-- Display as key-value pairs if it's a simple associative array -->
                                @foreach($report['risk_assessment'] as $key => $value)
                                    @if(is_string($value))
                                        <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                    @elseif(is_array($value))
                                        <div style="margin-bottom: 12px;">
                                            <p style="font-weight: bold; margin-bottom: 6px;">{{ $__jsonKeyLabel($key) }}:</p>
                                            <ul style="margin: 0; padding-left: 18px;">
                                                @foreach($value as $item)
                                                    <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {{ (string)$value }}</p>
                                    @endif
                                @endforeach
                            @endif
                        @elseif(is_string($report['risk_assessment']))
                            <p>{!! convertMarkdownBold($report['risk_assessment']) !!}</p>
                        @else
                            <p>{!! convertMarkdownBold((string)$report['risk_assessment']) !!}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Policy Implications -->
                @if(isset($report['policy_implications']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Policy Implications', 'Implikasi Dasar') }}</h3>
                        @if(is_array($report['policy_implications']))
                            @foreach($report['policy_implications'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ $__jsonKeyLabel($key) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ $__jsonKeyLabel($key) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['policy_implications']))
                            <p>{!! convertMarkdownBold($report['policy_implications']) !!}</p>
                        @else
                            <p>{!! convertMarkdownBold((string)$report['policy_implications']) !!}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Recommendations -->
                @if(isset($report['recommendations']) && is_array($report['recommendations']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Strategic Recommendations', 'Cadangan Strategik') }}</h3>
                        <ul class="recommendations-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['recommendations'] as $recommendation)
                                <li style="margin-bottom: 12px; text-align: justify;">
                                    @if(is_array($recommendation) && isset($recommendation['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($recommendation['point']) !!}</div>
                                        @if(isset($recommendation['explanation']) && !empty($recommendation['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px; text-align: justify;">
                                                {!! convertMarkdownBold($recommendation['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($recommendation))
                                        <!-- Legacy format handling -->
                                        @foreach($recommendation as $key => $value)
                                            @if(is_string($value))
                                                <div style="text-align: justify;"><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</div>
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($recommendation))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($recommendation) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold((string)$recommendation) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Confidence Level -->
                @if(isset($report['confidence_level']))
                    <div class="prediction-content avoid-break">
                        <h3>{{ $__ui('Prediction Confidence', 'Keyakinan Ramalan') }}</h3>
                        <div class="confidence-badge" style="font-size: 11pt; padding: 6px 14px;">
                            {{ $__localizeScaleInText(is_string($report['confidence_level']) ? $report['confidence_level'] : (string) $report['confidence_level']) }}
                        </div>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 12px; color: #666; font-size: 8pt;">
                                <strong>{{ $__ui('Methodology:', 'Metodologi:') }}</strong> {!! convertMarkdownBold($report['methodology']) !!}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Strategic Implications -->
                @if(isset($report['strategic_implications']) && is_array($report['strategic_implications']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #ffc107;">
                        <h3>{{ $__ui('Strategic Implications', 'Implikasi Strategik') }}</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['strategic_implications'] as $implication)
                                <li style="margin-bottom: 12px; text-align: justify;">
                                    @if(is_array($implication) && isset($implication['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($implication['point']) !!}</div>
                                        @if(isset($implication['explanation']) && !empty($implication['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px; text-align: justify;">
                                                {!! convertMarkdownBold($implication['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($implication))
                                        <!-- Legacy format handling -->
                                        @foreach($implication as $key => $value)
                                            @if(is_string($value))
                                                <div style="text-align: justify;"><strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold($value) !!}</div>
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ $__jsonKeyLabel($key) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ $__jsonKeyLabel($key) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($implication))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold($implication) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px; text-align: justify;">{!! convertMarkdownBold((string)$implication) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Data Sources -->
                @if(isset($report['data_sources']) && is_array($report['data_sources']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #2196f3;">
                        <h3>{{ $__ui('Data Sources & Methodology', 'Sumber Data & Metodologi') }}</h3>
                        <ul class="factors-list">
                            @foreach($report['data_sources'] as $source)
                                <li>{!! is_string($source) ? convertMarkdownBold($source) : convertMarkdownBold((string)$source) !!}</li>
                            @endforeach
                        </ul>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 12px; font-style: italic;">
                                <strong>{{ $__ui('Methodology:', 'Metodologi:') }}</strong> {!! convertMarkdownBold($report['methodology']) !!}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Assumptions -->
                @if(isset($report['assumptions']) && is_array($report['assumptions']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #9c27b0;">
                        <h3>{{ $__ui('Key Assumptions', 'Andaian Utama') }}</h3>
                        <ul class="factors-list">
                            @foreach($report['assumptions'] as $assumption)
                                <li>{!! is_string($assumption) ? convertMarkdownBold($assumption) : convertMarkdownBold((string)$assumption) !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Success Metrics -->
                @if(isset($report['success_metrics']) && is_array($report['success_metrics']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #4caf50;">
                        <h3>{{ $__ui('Success Metrics & KPIs', 'Metrik Kejayaan & KPI') }}</h3>
                        <ul class="factors-list">
                            @foreach($report['success_metrics'] as $metric)
                                <li>{{ is_string($metric) ? $metric : (string)$metric }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Timeline Information -->
                @if(isset($report['critical_timeline']) || isset($report['next_review']) || isset($report['analysis_date']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #ff9800;">
                        <h3>Timeline & Review Schedule</h3>
                        @if(isset($report['critical_timeline']) && is_string($report['critical_timeline']))
                            <p><strong>{{ $__ui('Critical Timeline:', 'Garis Masa Kritikal:') }}</strong> {{ $report['critical_timeline'] }}</p>
                        @endif
                        <p><strong>{{ $__ui('Analysis Date:', 'Tarikh Analisis:') }}</strong> {{ $prediction->created_at->format('Y-m-d') }}</p>
                        <p><strong>{{ $__ui('Next Review Date:', 'Tarikh Semakan Seterusnya:') }}</strong> {{ $prediction->created_at->copy()->addMonth()->format('Y-m-d') }}</p>
                    </div>
                @endif
            @else
                <div class="prediction-content">
                    <h3>{{ $__ui('Analysis Results', 'Keputusan Analisis') }}</h3>
                    <div class="data-point">
                        <pre style="white-space: pre-wrap; font-family: 'Times New Roman', serif; font-size: 8pt;">{{ json_encode($prediction->prediction_result, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    @elseif($prediction->status === 'failed')
        <div class="section major-section">
            <div class="section-title">{{ $__ui('Analysis Status', 'Status Analisis') }}</div>
            <div class="prediction-content" style="border-left: 3px solid #f44336; color: #c62828;">
                <h3>{{ $__ui('Analysis Failed', 'Analisis Gagal') }}</h3>
                <p>{{ $__ui('The NUJUM prediction analysis could not be completed. Please try again or contact support.', 'Analisis ramalan NUJUM tidak dapat diselesaikan. Sila cuba lagi atau hubungi sokongan.') }}</p>
            </div>
        </div>
    @else
        <div class="section major-section">
            <div class="section-title">{{ $__ui('Analysis Status', 'Status Analisis') }}</div>
            <div class="prediction-content" style="border-left: 3px solid #ff9800; color: #ef6c00;">
                <h3>{{ $__ui('Processing...', 'Memproses...') }}</h3>
                <p>{{ $__ui('Your prediction analysis is being processed by NUJUM. This may take a few moments.', 'Analisis ramalan anda sedang diproses oleh NUJUM. Ini mungkin mengambil masa beberapa ketika.') }}</p>
            </div>
        </div>
    @endif

    <!-- Technical Details -->
    <div class="section major-section">
        <div class="section-title">{{ $__ui('Technical Specifications', 'Spesifikasi Teknikal') }}</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">{{ $__ui('Analysis Type', 'Jenis Analisis') }}</div>
                <div class="info-value">{{ is_string($__inputData['analysis_type'] ?? '') ? $__inputData['analysis_type'] : $__ui('Prediction Analysis', 'Analisis Ramalan') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Data Processing', 'Pemprosesan Data') }}</div>
                <div class="info-value">{{ $__ui('NUJUM-powered pattern recognition and predictive modeling', 'Pengecaman corak dan pemodelan ramalan dikuasakan NUJUM') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Quality Assurance', 'Jaminan Kualiti') }}</div>
                <div class="info-value">{{ $__ui('Multi-layer validation and confidence scoring', 'Pengesahan berbilang lapisan dan penskoran keyakinan') }}</div>
            </div>
        </div>
    </div>

    <!-- NUJUM Disclaimer Footnote -->
    <div class="section major-section" style="margin-top: 30px; margin-bottom: 25px;">
        <div class="section-title" style="color: #6c757d; border-left-color: #6c757d; font-size: 11pt;">{{ $__ui('Disclaimer', 'Penafian') }}</div>
        <div style="border-left: 3px solid #6c757d; padding: 12px; font-size: 8pt; line-height: 1.3; color: #6c757d;">
            <p style="margin: 0 0 6px 0;">{{ $__ui('NUJUM makes no representations as to its accuracy, reliability or completeness. To the fullest extent permitted by law, NUJUM excludes all conditions, warranties and other obligations in connection with the preparation of this report and instead limits its liability to the amount paid by the recipient of this report.', 'NUJUM tidak membuat sebarang perwakilan mengenai ketepatan, kebolehpercayaan atau kelengkapannya. Setakat yang dibenarkan oleh undang-undang, NUJUM mengecualikan semua syarat, jaminan dan obligasi lain berhubung penyediaan laporan ini dan mengehadkan liabiliti kepada amaun yang dibayar oleh penerima laporan ini.') }}</p>
            <p style="margin: 0;">{{ $__ui('In no event shall NUJUM be liable to the recipient or any third party for any consequential loss or damage, including loss of profits, in connection with the preparation of this report.', 'Dalam apa jua keadaan NUJUM tidak bertanggungjawab kepada penerima atau mana-mana pihak ketiga bagi sebarang kerugian atau kerosakan susulan, termasuk kehilangan keuntungan, berhubung penyediaan laporan ini.') }}</p>
        </div>
    </div>

    <div class="footer">
        <p>{{ $__ui('This report was automatically generated by the NUJUM System', 'Laporan ini dijana secara automatik oleh Sistem NUJUM') }}</p>
        <p>{{ $__ui('Generated:', 'Dijana:') }} {{ date('Y-m-d H:i:s') }}</p>
        <p>{{ $__ui('For questions or support, please visit', 'Untuk pertanyaan atau sokongan, sila lawati') }} <a href="https://iesb.com.my/" style="color: #666; text-decoration: none;">https://iesb.com.my/</a></p>
    </div>


</body>
</html>
