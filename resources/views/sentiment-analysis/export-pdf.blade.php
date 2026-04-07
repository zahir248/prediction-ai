<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Comparison Report</title>
    @php
        // Set a flag to indicate this is for PDF export
        $isPdfExport = true;
        $GLOBALS['isPdfExport'] = true;
    @endphp
    <style>
        @page {
            margin: 1.2cm;
            margin-bottom: 2.5cm;
            size: A4;
        }
        
        /* Page numbering using footer element */
        .page-number {
            position: fixed;
            bottom: 0.5cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            font-family: 'Times New Roman', serif;
            z-index: 1000;
        }
        
        /* Alternative: Use CSS for page numbers if supported */
        @page {
            @bottom-center {
                content: counter(page);
                font-size: 9pt;
                color: #666;
                font-family: 'Times New Roman', serif;
            }
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
        }
        
        .header .subtitle {
            font-size: 12pt;
            margin-top: 8px;
            font-weight: normal;
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
        
        .content-box {
            padding: 8px;
            margin-bottom: 12px;
        }
        
        .content-box h3 {
            color: #000;
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 10pt;
            font-weight: bold;
        }
        
        .content-box p {
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
        
        .avoid-break {
            page-break-inside: avoid;
        }
        
        .highlight-box {
            padding: 8px;
            margin: 8px 0;
            border-left: 3px solid #ffcc00;
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
        
        ul, ol {
            margin: 4px 0;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 3px;
        }
        
        p {
            margin: 4px 0;
        }
        
        .major-section {
            page-break-inside: avoid;
        }
        
        table {
            page-break-inside: avoid;
        }
        
        tr {
            page-break-inside: avoid;
        }
        
        /* SVG: avoid height:auto — DomPDF often gives SVG zero height (charts vanish). Chart partials set explicit px width/height. */
        svg {
            max-width: 100%;
        }

        .sentiment-pdf-chart-svg {
            page-break-inside: avoid;
            margin: 12px 0;
            text-align: center;
            min-height: 48px;
            width: 100%;
        }

        .sentiment-pdf-chart-svg svg {
            overflow: visible;
        }

        /* Charts embedded like social-media PDF (data-uri SVG as <img>) */
        .sentiment-pdf-chart-svg img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 100%;
        }
        
        /* Professional Footprint Analysis Styles */
        .professional-footprint-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .professional-footprint-container h3 {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .professional-footprint-container svg {
            display: block;
            margin: 0 auto;
        }
        
        .professional-footprint-gauge {
            position: relative;
            display: inline-block;
        }
        
        .professional-footprint-score {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        /* Cultural Fit Indicators Styles */
        .cultural-fit-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .cultural-fit-container h3,
        .cultural-fit-container h4,
        .cultural-fit-container p {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .cultural-fit-container img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }
        
        /* Professional Growth Signals Styles */
        .professional-growth-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .professional-growth-container h3,
        .professional-growth-container h4,
        .professional-growth-container p {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        .professional-growth-container img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }
        
        .radar-chart-container,
        .radar-chart-wrapper {
            page-break-inside: avoid;
            text-align: center;
            margin: 0 auto;
        }
        
        .radar-chart-container {
            display: block;
            width: 100%;
            text-align: center;
        }
        
        .radar-chart-wrapper {
            display: inline-block;
            margin: 0 auto;
            text-align: center;
        }
        
        .radar-chart-container img,
        .radar-chart-wrapper img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
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
            
            /* Ensure charts don't break across pages */
            .radar-chart-container,
            .radar-chart-wrapper,
            svg {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
        }

        /*
         * Sentiment comparison report body — align with social-media/export-pdf.blade.php:
         * Times New Roman, 10pt base, 9pt prose, .section-title strip for numbered headings,
         * 18px section spacing, no rounded “web app” cards.
         */
        .sentiment-pdf-mode {
            font-family: 'Times New Roman', Times, serif !important;
        }

        .sentiment-pdf-mode p,
        .sentiment-pdf-mode li,
        .sentiment-pdf-mode td,
        .sentiment-pdf-mode th,
        .sentiment-pdf-mode dd,
        .sentiment-pdf-mode dt,
        .sentiment-pdf-mode dl {
            font-family: 'Times New Roman', Times, serif !important;
        }

        .sentiment-pdf-mode > div {
            margin-bottom: 18px !important;
            page-break-inside: avoid;
            orphans: 3;
            widows: 3;
        }

        .sentiment-pdf-mode > div:not(.sentiment-pdf-keep-border) {
            padding: 0 !important;
            background: transparent !important;
            background-image: none !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .sentiment-pdf-mode > div.sentiment-pdf-keep-border {
            padding: 8px !important;
            margin-bottom: 18px !important;
            border-radius: 0 !important;
            background: #fff !important;
        }

        .sentiment-pdf-mode > div > h2,
        .sentiment-pdf-mode > div > h3:first-child {
            background-color: #f0f0f0 !important;
            color: #000 !important;
            padding: 6px 10px !important;
            font-size: 12pt !important;
            font-weight: bold !important;
            border-left: 3px solid #000 !important;
            margin: 0 0 12px 0 !important;
            border-bottom: none !important;
            border-radius: 0 !important;
            letter-spacing: 0.3px !important;
            text-transform: uppercase !important;
        }

        .sentiment-pdf-exec-summary {
            padding: 0 !important;
            margin-bottom: 18px !important;
            background: transparent !important;
        }

        .sentiment-pdf-exec-summary h3 {
            background-color: #f0f0f0 !important;
            color: #000 !important;
            padding: 6px 10px !important;
            font-size: 12pt !important;
            font-weight: bold !important;
            border-left: 3px solid #000 !important;
            margin: 0 0 12px 0 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
        }

        .sentiment-pdf-exec-summary p {
            color: #333 !important;
            font-size: 9pt !important;
            line-height: 1.4 !important;
            text-align: justify !important;
            margin: 6px 0 !important;
            padding: 8px !important;
        }

        .sentiment-pdf-alert-error {
            border-radius: 0 !important;
            font-size: 9pt !important;
            padding: 8px !important;
            margin-bottom: 18px !important;
        }

        .sentiment-pdf-mode > div > p,
        .sentiment-pdf-mode > div > ul {
            font-size: 9pt !important;
            color: #333 !important;
            line-height: 1.4 !important;
        }

        .sentiment-pdf-mode > div > p {
            text-align: justify !important;
            margin: 6px 0 !important;
        }

        .sentiment-pdf-mode > div > ul {
            margin: 6px 0 !important;
            padding-left: 18px !important;
        }

        .sentiment-pdf-mode > div > ul > li {
            margin-bottom: 4px !important;
            font-size: 9pt !important;
        }

        .sentiment-pdf-mode > div > div h3 {
            font-size: 11pt !important;
            font-weight: bold !important;
            color: #000 !important;
            margin: 15px 0 8px 0 !important;
            padding: 0 0 4px 0 !important;
            border-bottom: 1px solid #ccc !important;
            background: none !important;
            border-left: none !important;
            text-transform: none !important;
            letter-spacing: normal !important;
        }

        .sentiment-pdf-mode [style*="display: grid"],
        .sentiment-pdf-mode [style*="display:grid"] {
            display: block !important;
        }

        .sentiment-pdf-mode [style*="display: flex"],
        .sentiment-pdf-mode [style*="display:flex"] {
            display: block !important;
        }

        .sentiment-pdf-mode > div > div[style*="border"] {
            margin-bottom: 12px !important;
            padding: 8px !important;
            border: 1px solid #ddd !important;
            border-radius: 0 !important;
            background: #fafafa !important;
        }

        .sentiment-pdf-mode > div > div > div[style*="border"] {
            margin-bottom: 12px !important;
            padding: 8px !important;
            border: 1px solid #ddd !important;
            border-radius: 0 !important;
            background: #fafafa !important;
        }

        .sentiment-pdf-mode [style*="font-size: 14px"],
        .sentiment-pdf-mode [style*="font-size: 16px"] {
            font-size: 9pt !important;
        }

        .sentiment-pdf-mode dl {
            font-size: 9pt !important;
        }

        .sentiment-pdf-mode dt,
        .sentiment-pdf-mode dd {
            font-size: 9pt !important;
            color: #333 !important;
        }

        .sentiment-pdf-mode table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 9pt !important;
        }

        .sentiment-pdf-mode table th,
        .sentiment-pdf-mode table td {
            padding: 4px 8px !important;
            border: 1px solid #ddd !important;
            vertical-align: top !important;
        }

        .sentiment-pdf-mode table thead tr {
            background: #f9f9f9 !important;
        }

        .sentiment-pdf-mode svg text {
            font-family: DejaVu Sans, Arial, sans-serif !important;
        }
    </style>
</head>
<body>
@php
    $sentimentComparison->loadMissing([
        'socialMediaAnalysisA:id,username,platform_data',
        'socialMediaAnalysisB:id,username,platform_data',
    ]);
    $r = $sentimentComparison->ai_result ?? [];
    $__reportLangMs = (($r['report_language'] ?? $sentimentComparison->report_language ?? 'en') === 'ms');
    $__ui = function (string $en, string $ms) use ($__reportLangMs): string {
        return $__reportLangMs ? $ms : $en;
    };
    $nameA = $r['profile_a_username'] ?? $sentimentComparison->socialMediaAnalysisA->username ?? '—';
    $nameB = $r['profile_b_username'] ?? $sentimentComparison->socialMediaAnalysisB->username ?? '—';
    $userA = $sentimentComparison->socialMediaAnalysisA?->profileDisplayLabel($nameA) ?? $nameA.' (—)';
    $userB = $sentimentComparison->socialMediaAnalysisB?->profileDisplayLabel($nameB) ?? $nameB.' (—)';
    $reportLangLabel = $__reportLangMs ? 'Bahasa Melayu' : 'English';
@endphp
    <div class="header">
        <h1>{{ $__ui('Sentiment Comparison Report', 'Laporan Perbandingan Sentimen') }}</h1>
        <p class="subtitle">{{ $userA }} {{ $__ui('vs', 'lwn') }} {{ $userB }}</p>
        <p>{{ $__ui('Generated on', 'Dijana pada') }} {{ date('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="section major-section">
        <div class="section-title">{{ $__ui('Comparison Information', 'Maklumat Perbandingan') }}</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">{{ $__ui('First profile', 'Profil pertama') }}</div>
                <div class="info-value">{{ $userA }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Second profile', 'Profil kedua') }}</div>
                <div class="info-value">{{ $userB }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $__ui('Report language', 'Bahasa laporan') }}</div>
                <div class="info-value">{{ $reportLangLabel }}</div>
            </div>
            @if ($sentimentComparison->processing_time)
            <div class="info-row">
                <div class="info-label">{{ $__ui('Processing Time', 'Masa Pemprosesan') }}</div>
                <div class="info-value">{{ number_format((float) $sentimentComparison->processing_time, 2) }} {{ $__ui('seconds', 'saat') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">{{ $__ui('Compared On', 'Dibandingkan Pada') }}</div>
                <div class="info-value">{{ $sentimentComparison->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>

    @include('sentiment-analysis.partials.comparison-content', ['comparison' => $sentimentComparison, 'isPdfExport' => true])

    <div class="footer">
        <p>{{ $__ui('This report was automatically generated by the NUJUM System', 'Laporan ini dijana secara automatik oleh Sistem NUJUM') }}</p>
        <p>{{ $__ui('Generated:', 'Dijana:') }} {{ date('Y-m-d H:i:s') }}</p>
        <p>{{ $__ui('For questions or support, please visit', 'Untuk pertanyaan atau sokongan, sila lawati') }} <a href="https://iesb.com.my/" style="color: #666; text-decoration: none;">https://iesb.com.my/</a></p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Times New Roman", "normal");
            $size = 9;
            $pageText = "{PAGE_NUM}";
            $y = $pdf->get_height() - 24;
            $pageWidth = $pdf->get_width();
            $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
            $x = ($pageWidth / 2) - ($textWidth / 2) + 25;
            $pdf->page_text($x, $y, $pageText, $font, $size, array(0, 0, 0));
        }
    </script>
</body>
</html>
