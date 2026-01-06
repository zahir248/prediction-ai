<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analysis Dashboard Report</title>
    @php
        $isPdfExport = true;
        $GLOBALS['isPdfExport'] = true;
    @endphp
    <style>
        @page {
            margin: 1.2cm;
            margin-bottom: 2.5cm;
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
        
        .insights-section {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .insights-summary {
            color: #374151;
            font-size: 10pt;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        
        .key-findings {
            margin-top: 12px;
        }
        
        .key-findings-title {
            font-size: 11pt;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .key-findings-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .key-findings-list li {
            padding: 6px 0;
            padding-left: 24px;
            position: relative;
            color: #4b5563;
            font-size: 9pt;
        }
        
        .key-findings-list li:before {
            content: "•";
            position: absolute;
            left: 8px;
            color: #667eea;
            font-weight: bold;
            font-size: 14px;
        }
        
        .metrics-section {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metrics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin: 0;
        }
        
        .metrics-table td {
            padding: 5px;
            vertical-align: middle;
            width: 25%;
        }
        
        .metric-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px 8px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
            height: 75px;
            position: relative;
        }
        
        .metric-label {
            font-size: 7.5pt;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 0;
            line-height: 1.2;
            word-wrap: break-word;
            display: block;
            position: absolute;
            top: 10px;
            left: 8px;
            right: 8px;
        }
        
        .metric-value {
            font-size: 18pt;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
            word-break: break-word;
            display: block;
            position: absolute;
            bottom: 10px;
            left: 8px;
            right: 8px;
        }
        
        .chart-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .chart-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .chart-title {
            font-size: 12pt;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .chart-description {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 12px;
            font-style: italic;
        }
        
        .chart-placeholder {
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 4px;
            padding: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 9pt;
        }
        
        .chart-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            margin: 15px 0;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            display: block;
        }
        
        .chart-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        
        .chart-data-table th {
            background: #f3f4f6;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #e5e7eb;
        }
        
        .chart-data-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
        }
        
        .chart-data-table tr:nth-child(even) {
            background: #f9fafb;
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
            
            .chart-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Analysis Dashboard Report</h1>
        <p class="subtitle">{{ $dataAnalysis->file_name }}</p>
        <p>Generated on {{ date('F d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Analysis Information -->
    <div class="section">
        <div class="section-title">Analysis Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">File Name</div>
                <div class="info-value">{{ $dataAnalysis->file_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">{{ ucfirst($dataAnalysis->status) }}</div>
            </div>
            @if($dataAnalysis->processing_time)
            <div class="info-row">
                <div class="info-label">Processing Time</div>
                <div class="info-value">{{ number_format($dataAnalysis->processing_time, 2) }} seconds</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Analyzed On</div>
                <div class="info-value">{{ $dataAnalysis->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- AI Insights Section -->
    @if(!empty($dashboardData['summary']) || !empty($dashboardData['key_findings']))
    <div class="section avoid-break">
        <div class="section-title">AI Insights</div>
        <div class="insights-section">
            @if(!empty($dashboardData['summary']))
            <div class="insights-summary">
                <strong>Summary:</strong> {{ $dashboardData['summary'] }}
            </div>
            @endif
            
            @if(!empty($dashboardData['key_findings']) && is_array($dashboardData['key_findings']))
            <div class="key-findings">
                <div class="key-findings-title">Key Findings:</div>
                <ul class="key-findings-list">
                    @foreach($dashboardData['key_findings'] as $finding)
                        <li>{{ is_array($finding) ? json_encode($finding) : $finding }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Metrics Section -->
    @if(!empty($dashboardData['metrics']))
    <div class="section avoid-break">
        <div class="section-title">Key Metrics</div>
        <div class="metrics-section">
            @php
                $metrics = $dashboardData['metrics'];
                $metricsArray = [];
                foreach ($metrics as $name => $value) {
                    $metricsArray[] = ['name' => $name, 'value' => $value];
                }
                $chunkSize = 4; // Number of metrics per row
                $metricChunks = array_chunk($metricsArray, $chunkSize);
            @endphp
            
            <table class="metrics-table">
                @foreach($metricChunks as $chunk)
                <tr>
                    @foreach($chunk as $metric)
                    <td style="width: {{ 100 / $chunkSize }}%;">
                        <div class="metric-card">
                            <div class="metric-label">{{ $metric['name'] }}</div>
                            <div class="metric-value">{{ number_format($metric['value']) }}</div>
                        </div>
                    </td>
                    @endforeach
                    {{-- Fill empty cells if last row has fewer items --}}
                    @if($loop->last && count($chunk) < $chunkSize)
                        @for($i = count($chunk); $i < $chunkSize; $i++)
                            <td style="width: {{ 100 / $chunkSize }}%;"></td>
                        @endfor
                    @endif
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    <!-- Charts Section -->
    @if(!empty($dashboardData['chart_configs']) && count($dashboardData['chart_configs']) > 0)
        @foreach($dashboardData['chart_configs'] as $index => $chart)
            @php
                $chartTitle = $chart['title'] ?? 'Chart ' . ($index + 1);
                $chartDescription = $chart['description'] ?? '';
                $chartType = $chart['type'] ?? 'bar';
                $chartData = $chart['data'] ?? [];
                $labels = $chartData['labels'] ?? [];
                $datasets = $chartData['datasets'] ?? [];
                $coordinates = $chart['coordinates'] ?? null;
                $isMapChart = stripos($chartTitle, 'map') !== false 
                    || stripos($chartType, 'map') !== false
                    || stripos($chartDescription, 'map') !== false;
            @endphp
            
            <div class="section chart-section avoid-break">
                <div class="chart-card">
                    <div class="chart-title">{{ $chartTitle }}</div>
                    @if(!empty($chartDescription))
                    <div class="chart-description">{{ $chartDescription }}</div>
                    @endif
                    
                    @if(!empty($labels) && !empty($datasets))
                        @if(isset($chartImages[$index]) && !empty($chartImages[$index]))
                            <!-- Display actual chart image -->
                            <img src="{{ $chartImages[$index] }}" alt="{{ $chartTitle }}" class="chart-image" />
                        @else
                            <!-- Fallback: Show chart type if image generation failed -->
                            <div class="chart-placeholder">
                                Chart Type: {{ ucfirst($chartType) }}
                            </div>
                        @endif
                        
                        <!-- Chart Data Table - Always show below chart/graph/map -->
                        <div style="margin-top: 15px;">
                            <div style="font-size: 10pt; font-weight: 600; color: #374151; margin-bottom: 8px;">Data Table</div>
                            <table class="chart-data-table">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        @if($isMapChart && $coordinates)
                                            <th>Coordinates</th>
                                        @endif
                                        @foreach($datasets as $dataset)
                                            <th>{{ $dataset['label'] ?? 'Data ' . ($loop->index + 1) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($labels as $labelIndex => $label)
                                    <tr>
                                        <td><strong>{{ $label }}</strong></td>
                                        @if($isMapChart && $coordinates && isset($coordinates[$labelIndex]))
                                            @php
                                                $coord = $coordinates[$labelIndex];
                                                $coordStr = is_array($coord) && count($coord) >= 2 
                                                    ? number_format($coord[0], 4) . ', ' . number_format($coord[1], 4)
                                                    : '-';
                                            @endphp
                                            <td>{{ $coordStr }}</td>
                                        @endif
                                        @foreach($datasets as $dataset)
                                            @php
                                                $data = $dataset['data'] ?? [];
                                                $value = isset($data[$labelIndex]) ? $data[$labelIndex] : '-';
                                            @endphp
                                            <td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                    <div class="chart-placeholder">
                        No data available for this chart
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
    <div class="section">
        <div class="chart-card">
            <div class="chart-title">No Charts Available</div>
            <div class="chart-placeholder">
                No charts were generated for this analysis.
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the NUJUM System</p>
        <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
        <p>For questions or support, please visit <a href="https://iesb.com.my/" style="color: #666; text-decoration: none;">https://iesb.com.my/</a></p>
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

