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
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Analysis Report</title>
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
        }
        
        .data-point {
            padding: 6px;
            margin: 6px 0;
            border-left: 3px solid #b3d9ff;
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
            text-align: left;
            border: 1px solid #333;
            font-size: 9pt;
        }
        
        .risk-table td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9pt;
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
        <h1>Prediction Analysis Report</h1>
        <p class="subtitle">{{ $prediction->topic }}</p>
        @if($prediction->target)
        <p style="color: #059669; font-weight: bold; margin: 8px 0;">Target Focus: {{ $prediction->target }}</p>
        @endif
        <p>Generated on {{ date('F d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Executive Summary -->
    <div class="section major-section">
        <div class="section-title">Executive Summary</div>
        <div class="prediction-content">
            @if($prediction->target)
            <div class="highlight-box" style="border-left-color: #4caf50;">
                <strong>Target-Focused Analysis:</strong><br>
                This analysis specifically focuses on how predictions, risks, and strategic implications will affect: <strong>{{ $prediction->target }}</strong>
            </div>
            @endif
            
            <p>This comprehensive analysis provides detailed insights into the prediction results generated by our AI system. The analysis covers key findings, risk assessments, and strategic recommendations based on the input data and AI-generated predictions.</p>
            
            @if($prediction->status === 'completed' && $prediction->prediction_result)
                @if(isset($prediction->prediction_result['executive_summary']))
                    <div class="highlight-box">
                        <strong>AI-Generated Summary:</strong><br>
                        {!! convertMarkdownBold($prediction->prediction_result['executive_summary']) !!}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Analysis Information -->
    <div class="section major-section">
        <div class="section-title">Analysis Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Topic</div>
                <div class="info-value">{{ $prediction->topic }}</div>
            </div>
            @if($prediction->target)
            <div class="info-row">
                <div class="info-label">Target Focus</div>
                <div class="info-value">{{ $prediction->target }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Prediction Horizon</div>
                <div class="info-value">{{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @php
                        $statusClass = 'status-' . strtolower($prediction->status);
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($prediction->status) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Confidence Score</div>
                <div class="info-value">
                    @if(isset($prediction->confidence_score) && $prediction->confidence_score !== null && is_numeric($prediction->confidence_score))
                        <div class="confidence-badge">
                            {{ number_format((float) $prediction->confidence_score * 100, 1) }}%
                        </div>
                    @else
                        <span style="color: #666;">Not available</span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Processing Time</div>
                <div class="info-value">
                    @if(isset($prediction->processing_time) && is_numeric($prediction->processing_time))
                        {{ number_format((float) $prediction->processing_time, 3) }} seconds
                    @else
                        <span style="color: #666;">Not available</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Created At</div>
                <div class="info-value">{{ $prediction->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Input Data Analysis -->
    <div class="section major-section">
        <div class="section-title">Input Data Analysis</div>
        <div class="prediction-content">
            <h3>Analysis Request</h3>
            <div class="data-point">
                <p>{{ is_string($prediction->input_data['text'] ?? '') ? $prediction->input_data['text'] : 'No input data available' }}</p>
            </div>
            
            <h3>Data Context</h3>
            <p>This analysis is based on the provided input data and leverages advanced AI algorithms to generate comprehensive predictions and insights. The system processes multiple data points to identify patterns, risks, and opportunities.</p>
        </div>
    </div>

    <!-- Source References -->
    @if($prediction->source_urls && count($prediction->source_urls) > 0)
    <div class="section major-section">
        <div class="section-title">Source References</div>
        <div class="prediction-content">
            <h3>Additional Source Information</h3>
            <div class="data-point">
                @foreach($prediction->source_urls as $index => $sourceUrl)
                <p style="margin-bottom: 6px;"><strong>Source {{ $index + 1 }}:</strong> <a href="{{ $sourceUrl }}">{{ $sourceUrl }}</a></p>
                @endforeach
            </div>
            <p>These sources were referenced during the AI analysis to provide additional context and data points for the prediction. The analysis incorporates information from both the user input and these external sources.</p>
        </div>
    </div>
    @endif

    <!-- Source Analysis Section -->
    @if($prediction->source_urls && count($prediction->source_urls) > 0 && isset($prediction->prediction_result['source_analysis']))
    <div class="section major-section">
        <div class="section-title">Source Analysis & Influence</div>
        <div class="prediction-content">
            <h3>How Sources Influenced This Analysis</h3>
            <div class="data-point">
                <p>{!! nl2br(convertMarkdownBold($prediction->prediction_result['source_analysis'])) !!}</p>
            </div>
            <p>This analysis shows how each provided source contributed to specific predictions and conclusions, ensuring transparency and traceability of insights.</p>
        </div>
    </div>
    @endif

    <!-- AI Analysis Results - Force page break for this major section -->
    @if($prediction->status === 'completed' && $prediction->prediction_result)
        <div class="section major-section force-break">
            <div class="section-title">AI Analysis Results</div>
            
            @if(isset($prediction->prediction_result['note']) && is_string($prediction->prediction_result['note']))
                <div class="highlight-box">
                    <strong>Important Note:</strong> {!! convertMarkdownBold($prediction->prediction_result['note']) !!}
                </div>
            @endif
            
            @if(isset($prediction->prediction_result['title']) && is_string($prediction->prediction_result['title']))
                @php $report = $prediction->prediction_result; @endphp
                
                <!-- Title and Horizon -->
                <div class="prediction-content">
                    <h3 style="color: #000; text-align: center; margin-bottom: 8px;">{{ $report['title'] }}</h3>
                    @if(isset($report['prediction_horizon']) && is_string($report['prediction_horizon']))
                        <p style="text-align: center; color: #666; margin-bottom: 15px;">
                            <strong>Prediction Horizon:</strong> {{ $report['prediction_horizon'] }}
                        </p>
                    @endif
                </div>
                
                <!-- Executive Summary -->
                @if(isset($report['executive_summary']))
                    <div class="prediction-content avoid-break">
                        <h3>Executive Summary</h3>
                        @if(is_array($report['executive_summary']))
                            @foreach($report['executive_summary'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
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
                        <h3>Current Situation & Future Implications</h3>
                        @if(is_array($report['current_situation']))
                            @foreach($report['current_situation'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
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
                        <h3>Key Factors for Future Development</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['key_factors'] as $factor)
                                <li style="margin-bottom: 12px;">
                                    @if(is_array($factor) && isset($factor['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($factor['point']) !!}</div>
                                        @if(isset($factor['explanation']) && !empty($factor['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                {!! convertMarkdownBold($factor['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($factor))
                                        <!-- Legacy format handling -->
                                        @foreach($factor as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($factor))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($factor) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold((string)$factor) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Future Predictions -->
                @if(isset($report['future_predictions']) && is_array($report['future_predictions']))
                    <div class="prediction-content avoid-break">
                        <h3>Future Predictions</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['future_predictions'] as $prediction_item)
                                <li style="margin-bottom: 12px;">
                                    @if(is_array($prediction_item) && isset($prediction_item['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($prediction_item['point']) !!}</div>
                                        @if(isset($prediction_item['explanation']) && !empty($prediction_item['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                {!! convertMarkdownBold($prediction_item['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($prediction_item))
                                        <!-- Legacy format handling -->
                                        @foreach($prediction_item as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($prediction_item))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($prediction_item) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold((string)$prediction_item) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif(isset($report['predictions']))
                    <div class="prediction-content avoid-break">
                        <h3>Future Predictions</h3>
                        @if(is_array($report['predictions']))
                            <ul class="factors-list" style="list-style: none; padding-left: 0;">
                                @foreach($report['predictions'] as $prediction_item)
                                    <li style="margin-bottom: 12px;">
                                        @if(is_array($prediction_item) && isset($prediction_item['point']))
                                            <!-- New format with point and explanation -->
                                            <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($prediction_item['point']) !!}</div>
                                            @if(isset($prediction_item['explanation']) && !empty($prediction_item['explanation']))
                                                <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                    {!! convertMarkdownBold($prediction_item['explanation']) !!}
                                                </div>
                                            @endif
                                        @elseif(is_array($prediction_item))
                                            <!-- Legacy format handling -->
                                            @foreach($prediction_item as $key => $value)
                                                @if(is_string($value))
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}
                                                @elseif(is_array($value))
                                                    <div style="margin-top: 6px;">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        <ul style="margin: 6px 0 0 18px;">
                                                            @foreach($value as $item)
                                                                <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                                @endif
                                            @endforeach
                                        @elseif(is_string($prediction_item))
                                            <!-- Legacy string format -->
                                            <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($prediction_item) !!}</div>
                                        @else
                                            <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold((string)$prediction_item) !!}</div>
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
                        <h3>Risk Assessment</h3>
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
                                            <th style="width: 35%;">Risk</th>
                                            <th style="width: 15%;">Level</th>
                                            <th style="width: 15%;">Probability</th>
                                            <th style="width: 35%;">Mitigation Strategy</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report['risk_assessment'] as $risk)
                                            @if(is_array($risk))
                                                <tr>
                                                    <td>{!! isset($risk['risk']) ? (is_string($risk['risk']) ? convertMarkdownBold($risk['risk']) : convertMarkdownBold((string)$risk['risk'])) : 'N/A' !!}</td>
                                                    <td>{!! isset($risk['level']) ? (is_string($risk['level']) ? convertMarkdownBold($risk['level']) : convertMarkdownBold((string)$risk['level'])) : '-' !!}</td>
                                                    <td>{!! isset($risk['probability']) ? (is_string($risk['probability']) ? convertMarkdownBold($risk['probability']) : convertMarkdownBold((string)$risk['probability'])) : '-' !!}</td>
                                                    <td>{!! isset($risk['mitigation']) ? (is_string($risk['mitigation']) ? convertMarkdownBold($risk['mitigation']) : convertMarkdownBold((string)$risk['mitigation'])) : '-' !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <!-- Display as key-value pairs if it's a simple associative array -->
                                @foreach($report['risk_assessment'] as $key => $value)
                                    @if(is_string($value))
                                        <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                    @elseif(is_array($value))
                                        <div style="margin-bottom: 12px;">
                                            <p style="font-weight: bold; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                            <ul style="margin: 0; padding-left: 18px;">
                                                @foreach($value as $item)
                                                    <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
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
                        <h3>Policy Implications</h3>
                        @if(is_array($report['policy_implications']))
                            @foreach($report['policy_implications'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 12px;">
                                        <p style="font-weight: bold; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 18px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 3px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
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
                        <h3>Strategic Recommendations</h3>
                        <ul class="recommendations-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['recommendations'] as $recommendation)
                                <li style="margin-bottom: 12px;">
                                    @if(is_array($recommendation) && isset($recommendation['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($recommendation['point']) !!}</div>
                                        @if(isset($recommendation['explanation']) && !empty($recommendation['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                {!! convertMarkdownBold($recommendation['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($recommendation))
                                        <!-- Legacy format handling -->
                                        @foreach($recommendation as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($recommendation))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($recommendation) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold((string)$recommendation) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Confidence Level -->
                @if(isset($report['confidence_level']))
                    <div class="prediction-content avoid-break">
                        <h3>Prediction Confidence</h3>
                        <div class="confidence-badge" style="font-size: 11pt; padding: 6px 14px;">
                            {{ is_string($report['confidence_level']) ? $report['confidence_level'] : (string)$report['confidence_level'] }}
                        </div>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 12px; color: #666; font-size: 8pt;">
                                <strong>Methodology:</strong> {!! convertMarkdownBold($report['methodology']) !!}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Strategic Implications -->
                @if(isset($report['strategic_implications']) && is_array($report['strategic_implications']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #ffc107;">
                        <h3>Strategic Implications</h3>
                        <ul class="factors-list" style="list-style: none; padding-left: 0;">
                            @foreach($report['strategic_implications'] as $implication)
                                <li style="margin-bottom: 12px;">
                                    @if(is_array($implication) && isset($implication['point']))
                                        <!-- New format with point and explanation -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($implication['point']) !!}</div>
                                        @if(isset($implication['explanation']) && !empty($implication['explanation']))
                                            <div style="color: #666; font-size: 9pt; line-height: 1.5; margin-left: 12px; padding-left: 8px; border-left: 2px solid #ddd; margin-top: 4px;">
                                                {!! convertMarkdownBold($implication['explanation']) !!}
                                            </div>
                                        @endif
                                    @elseif(is_array($implication))
                                        <!-- Legacy format handling -->
                                        @foreach($implication as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold($value) !!}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 6px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 6px 0 0 18px;">
                                                        @foreach($value as $item)
                                                            <li>{!! is_string($item) ? convertMarkdownBold($item) : (is_array($item) ? e(json_encode($item)) : convertMarkdownBold((string)$item)) !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {!! convertMarkdownBold((string)$value) !!}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($implication))
                                        <!-- Legacy string format -->
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold($implication) !!}</div>
                                    @else
                                        <div style="font-weight: bold; margin-bottom: 4px;">{!! convertMarkdownBold((string)$implication) !!}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Data Sources -->
                @if(isset($report['data_sources']) && is_array($report['data_sources']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #2196f3;">
                        <h3>Data Sources & Methodology</h3>
                        <ul class="factors-list">
                            @foreach($report['data_sources'] as $source)
                                <li>{!! is_string($source) ? convertMarkdownBold($source) : convertMarkdownBold((string)$source) !!}</li>
                            @endforeach
                        </ul>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 12px; font-style: italic;">
                                <strong>Methodology:</strong> {!! convertMarkdownBold($report['methodology']) !!}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Assumptions -->
                @if(isset($report['assumptions']) && is_array($report['assumptions']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #9c27b0;">
                        <h3>Key Assumptions</h3>
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
                        <h3>Success Metrics & KPIs</h3>
                        <ul class="factors-list">
                            @foreach($report['success_metrics'] as $metric)
                                <li>{{ is_string($metric) ? $metric : (string)$metric }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Timeline Information -->
                @if(isset($report['critical_timeline']) || isset($report['next_review']))
                    <div class="prediction-content avoid-break" style="border-left: 3px solid #ff9800;">
                        <h3>Timeline & Review Schedule</h3>
                        @if(isset($report['critical_timeline']) && is_string($report['critical_timeline']))
                            <p><strong>Critical Timeline:</strong> {{ $report['critical_timeline'] }}</p>
                        @endif
                        @if(isset($report['next_review']) && is_string($report['next_review']))
                            <p><strong>Next Review Date:</strong> {{ $report['next_review'] }}</p>
                        @endif
                        @if(isset($report['analysis_date']) && is_string($report['analysis_date']))
                            <p><strong>Analysis Date:</strong> {{ $report['analysis_date'] }}</p>
                        @endif
                    </div>
                @endif
            @else
                <div class="prediction-content">
                    <h3>Analysis Results</h3>
                    <div class="data-point">
                        <pre style="white-space: pre-wrap; font-family: 'Times New Roman', serif; font-size: 8pt;">{{ json_encode($prediction->prediction_result, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    @elseif($prediction->status === 'failed')
        <div class="section major-section">
            <div class="section-title">Analysis Status</div>
            <div class="prediction-content" style="border-left: 3px solid #f44336; color: #c62828;">
                <h3>Analysis Failed</h3>
                <p>The AI prediction analysis could not be completed. Please try again or contact support.</p>
            </div>
        </div>
    @else
        <div class="section major-section">
            <div class="section-title">Analysis Status</div>
            <div class="prediction-content" style="border-left: 3px solid #ff9800; color: #ef6c00;">
                <h3>Processing...</h3>
                <p>Your prediction analysis is being processed by AI. This may take a few moments.</p>
            </div>
        </div>
    @endif

    <!-- Technical Details -->
    <div class="section major-section">
        <div class="section-title">Technical Specifications</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Analysis Type</div>
                <div class="info-value">{{ is_string($prediction->input_data['analysis_type'] ?? '') ? $prediction->input_data['analysis_type'] : 'Prediction Analysis' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Data Processing</div>
                <div class="info-value">AI-powered pattern recognition and predictive modeling</div>
            </div>
            <div class="info-row">
                <div class="info-label">Quality Assurance</div>
                <div class="info-value">Multi-layer validation and confidence scoring</div>
            </div>
        </div>
    </div>

    <!-- NUJUM Disclaimer Footnote -->
    <div class="section major-section" style="margin-top: 30px; margin-bottom: 25px;">
        <div class="section-title" style="color: #6c757d; border-left-color: #6c757d; font-size: 11pt;">Disclaimer</div>
        <div style="border-left: 3px solid #6c757d; padding: 12px; font-size: 8pt; line-height: 1.3; color: #6c757d;">
            <p style="margin: 0 0 6px 0;">NUJUM makes no representations as to its accuracy, reliability or completeness. To the fullest extent permitted by law, NUJUM excludes all conditions, warranties and other obligations in connection with the preparation of this report and instead limits its liability to the amount paid by the recipient of this report.</p>
            <p style="margin: 0;">In no event shall NUJUM be liable to the recipient or any third party for any consequential loss or damage, including loss of profits, in connection with the preparation of this report.</p>
        </div>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the NUJUM System</p>
        <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>


</body>
</html>
