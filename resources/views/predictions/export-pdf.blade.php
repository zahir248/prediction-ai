<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Analysis Report</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
            font-size: 11pt;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #000;
            margin: 0;
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header p {
            color: #333;
            margin: 5px 0 0 0;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 14pt;
            margin-top: 10px;
            font-weight: normal;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f0f0f0;
            color: #000;
            padding: 8px 12px;
            font-size: 14pt;
            font-weight: bold;
            border-left: 4px solid #000;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subsection-title {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 6px 10px;
            background-color: #f9f9f9;
            font-weight: bold;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 6px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .prediction-content {
            background-color: #fafafa;
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .prediction-content h3 {
            color: #000;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 11pt;
            font-weight: bold;
        }
        
        .prediction-content p {
            margin: 8px 0;
            text-align: justify;
            font-size: 10pt;
        }
        
        .factors-list, .recommendations-list {
            margin: 8px 0;
            padding-left: 20px;
        }
        
        .factors-list li, .recommendations-list li {
            margin-bottom: 6px;
            font-size: 10pt;
        }
        
        .confidence-badge {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 9pt;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .highlight-box {
            background-color: #ffffcc;
            border: 1px solid #ffcc00;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        
        .data-point {
            background-color: #e8f4fd;
            border: 1px solid #b3d9ff;
            padding: 8px;
            margin: 8px 0;
            border-radius: 3px;
        }
        
        .risk-level {
            font-weight: bold;
            color: #d32f2f;
        }
        
        .mitigation {
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        
        .mitigation h4 {
            color: #2e7d32;
            margin-top: 0;
            font-size: 11pt;
        }
        
        .recommendations {
            background-color: #fff3e0;
            border: 1px solid #ff9800;
            padding: 12px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .recommendations h4 {
            color: #e65100;
            margin-top: 0;
            font-size: 12pt;
        }
        
        .page-number {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-top: 20px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Prediction Analysis Report</h1>
        <p class="subtitle">{{ $prediction->topic }}</p>
        <p>Generated on {{ date('F d, Y \a\t g:i A') }}</p>
        <p>Powered by Google Gemini AI</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="prediction-content">
            <p>This comprehensive analysis provides detailed insights into the prediction results generated by our AI system. The analysis covers key findings, risk assessments, and strategic recommendations based on the input data and AI-generated predictions.</p>
            
            @if($prediction->status === 'completed' && $prediction->prediction_result)
                @if(isset($prediction->prediction_result['executive_summary']))
                    <div class="highlight-box">
                        <strong>AI-Generated Summary:</strong><br>
                        {{ $prediction->prediction_result['executive_summary'] }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Analysis Information -->
    <div class="section">
        <div class="section-title">Analysis Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Prediction ID</div>
                <div class="info-value">#{{ $prediction->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Topic</div>
                <div class="info-value">{{ $prediction->topic }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="risk-level">{{ ucfirst($prediction->status) }}</span>
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
                <div class="info-label">Model Used</div>
                <div class="info-value">{{ $prediction->model_used ?? 'Not specified' }}</div>
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
    <div class="section">
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
    <div class="section">
        <div class="section-title">Source References</div>
        <div class="prediction-content">
            <h3>Additional Source Information</h3>
            <div class="data-point">
                @foreach($prediction->source_urls as $index => $sourceUrl)
                <p style="margin-bottom: 8px;"><strong>Source {{ $index + 1 }}:</strong> <a href="{{ $sourceUrl }}">{{ $sourceUrl }}</a></p>
                @endforeach
            </div>
            <p>These sources were referenced during the AI analysis to provide additional context and data points for the prediction. The analysis incorporates information from both the user input and these external sources.</p>
        </div>
    </div>
    @endif

    <!-- Source Analysis Section -->
    @if($prediction->source_urls && count($prediction->source_urls) > 0 && isset($prediction->prediction_result['source_analysis']))
    <div class="section">
        <div class="section-title">Source Analysis & Influence</div>
        <div class="prediction-content">
            <h3>How Sources Influenced This Analysis</h3>
            <div class="data-point">
                <p>{!! nl2br(e($prediction->prediction_result['source_analysis'])) !!}</p>
            </div>
            <p>This analysis shows how each provided source contributed to specific predictions and conclusions, ensuring transparency and traceability of insights.</p>
        </div>
    </div>
    @endif

    <!-- AI Analysis Results -->
    @if($prediction->status === 'completed' && $prediction->prediction_result)
        <div class="section">
            <div class="section-title">AI Analysis Results</div>
            
            @if(isset($prediction->prediction_result['note']) && is_string($prediction->prediction_result['note']))
                <div class="highlight-box">
                    <strong>Important Note:</strong> {{ $prediction->prediction_result['note'] }}
                </div>
            @endif
            
            @if(isset($prediction->prediction_result['title']) && is_string($prediction->prediction_result['title']))
                @php $report = $prediction->prediction_result; @endphp
                
                <!-- Title and Horizon -->
                <div class="prediction-content">
                    <h3 style="color: #000; text-align: center; margin-bottom: 10px;">{{ $report['title'] }}</h3>
                    @if(isset($report['prediction_horizon']) && is_string($report['prediction_horizon']))
                        <p style="text-align: center; color: #666; margin-bottom: 20px;">
                            <strong>Prediction Horizon:</strong> {{ $report['prediction_horizon'] }}
                        </p>
                    @endif
                </div>
                
                <!-- Executive Summary -->
                @if(isset($report['executive_summary']))
                    <div class="prediction-content">
                        <h3>Executive Summary</h3>
                        @if(is_array($report['executive_summary']))
                            @foreach($report['executive_summary'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 15px;">
                                        <p style="font-weight: bold; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['executive_summary']))
                            <p>{{ $report['executive_summary'] }}</p>
                        @else
                            <p>{{ (string)$report['executive_summary'] }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Current Situation -->
                @if(isset($report['current_situation']))
                    <div class="prediction-content">
                        <h3>Current Situation & Future Implications</h3>
                        @if(is_array($report['current_situation']))
                            @foreach($report['current_situation'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 15px;">
                                        <p style="font-weight: bold; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['current_situation']))
                            <p>{{ $report['current_situation'] }}</p>
                        @else
                            <p>{{ (string)$report['current_situation'] }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Key Factors -->
                @if(isset($report['key_factors']) && is_array($report['key_factors']))
                    <div class="prediction-content">
                        <h3>Key Factors for Future Development</h3>
                        <ul class="factors-list">
                            @foreach($report['key_factors'] as $factor)
                                <li>
                                    @if(is_array($factor))
                                        @foreach($factor as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 8px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 8px 0 0 20px;">
                                                        @foreach($value as $item)
                                                            <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($factor))
                                        {{ $factor }}
                                    @else
                                        {{ (string)$factor }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Future Predictions -->
                @if(isset($report['future_predictions']) && is_array($report['future_predictions']))
                    <div class="prediction-content">
                        <h3>Future Predictions</h3>
                        <ul class="factors-list">
                            @foreach($report['future_predictions'] as $prediction_item)
                                <li>
                                    @if(is_array($prediction_item))
                                        @foreach($prediction_item as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 8px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 8px 0 0 20px;">
                                                        @foreach($value as $item)
                                                            <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($prediction_item))
                                        {{ $prediction_item }}
                                    @else
                                        {{ (string)$prediction_item }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif(isset($report['predictions']))
                    <div class="prediction-content">
                        <h3>Future Predictions</h3>
                        @if(is_array($report['predictions']))
                            <ul class="factors-list">
                                @foreach($report['predictions'] as $prediction_item)
                                    <li>
                                        @if(is_array($prediction_item))
                                            @foreach($prediction_item as $key => $value)
                                                @if(is_string($value))
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                @elseif(is_array($value))
                                                    <div style="margin-top: 8px;">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        <ul style="margin: 8px 0 0 20px;">
                                                            @foreach($value as $item)
                                                                <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                                @endif
                                            @endforeach
                                        @elseif(is_string($prediction_item))
                                            {{ $prediction_item }}
                                        @else
                                            {{ (string)$prediction_item }}
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
                
                <!-- Policy Implications -->
                @if(isset($report['policy_implications']))
                    <div class="prediction-content">
                        <h3>Policy Implications</h3>
                        @if(is_array($report['policy_implications']))
                            @foreach($report['policy_implications'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 15px;">
                                        <p style="font-weight: bold; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['policy_implications']))
                            <p>{{ $report['policy_implications'] }}</p>
                        @else
                            <p>{{ (string)$report['policy_implications'] }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Risk Assessment -->
                @if(isset($report['risk_assessment']))
                    <div class="prediction-content">
                        <h3>Risk Assessment</h3>
                        @if(is_array($report['risk_assessment']))
                            @foreach($report['risk_assessment'] as $key => $value)
                                @if(is_string($value))
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                @elseif(is_array($value))
                                    <div style="margin-bottom: 15px;">
                                        <p style="font-weight: bold; margin-bottom: 8px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</p>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            @foreach($value as $item)
                                                <li style="margin-bottom: 4px;">{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}</p>
                                @endif
                            @endforeach
                        @elseif(is_string($report['risk_assessment']))
                            <p>{{ $report['risk_assessment'] }}</p>
                        @else
                            <p>{{ (string)$report['risk_assessment'] }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Recommendations -->
                @if(isset($report['recommendations']) && is_array($report['recommendations']))
                    <div class="prediction-content">
                        <h3>Strategic Recommendations</h3>
                        <ul class="recommendations-list">
                            @foreach($report['recommendations'] as $recommendation)
                                <li>
                                    @if(is_array($recommendation))
                                        @foreach($recommendation as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 8px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 8px 0 0 20px;">
                                                        @foreach($value as $item)
                                                            <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($recommendation))
                                        {{ $recommendation }}
                                    @else
                                        {{ (string)$recommendation }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Confidence Level -->
                @if(isset($report['confidence_level']))
                    <div class="prediction-content">
                        <h3>Prediction Confidence</h3>
                        <div class="confidence-badge" style="font-size: 12pt; padding: 8px 16px;">
                            {{ is_string($report['confidence_level']) ? $report['confidence_level'] : (string)$report['confidence_level'] }}
                        </div>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 15px; color: #666; font-size: 9pt;">
                                <strong>Methodology:</strong> {{ $report['methodology'] }}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Strategic Implications -->
                @if(isset($report['strategic_implications']) && is_array($report['strategic_implications']))
                    <div class="prediction-content" style="background-color: #fff8e1; border-color: #ffc107;">
                        <h3>Strategic Implications</h3>
                        <ul class="factors-list">
                            @foreach($report['strategic_implications'] as $implication)
                                <li>
                                    @if(is_array($implication))
                                        @foreach($implication as $key => $value)
                                            @if(is_string($value))
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                            @elseif(is_array($value))
                                                <div style="margin-top: 8px;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <ul style="margin: 8px 0 0 20px;">
                                                        @foreach($value as $item)
                                                            <li>{{ is_string($item) ? $item : (is_array($item) ? json_encode($item) : (string)$item) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ (string)$value }}
                                            @endif
                                        @endforeach
                                    @elseif(is_string($implication))
                                        {{ $implication }}
                                    @else
                                        {{ (string)$implication }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Data Sources -->
                @if(isset($report['data_sources']) && is_array($report['data_sources']))
                    <div class="prediction-content" style="background-color: #e3f2fd; border-color: #2196f3;">
                        <h3>Data Sources & Methodology</h3>
                        <ul class="factors-list">
                            @foreach($report['data_sources'] as $source)
                                <li>{{ is_string($source) ? $source : (string)$source }}</li>
                            @endforeach
                        </ul>
                        @if(isset($report['methodology']) && is_string($report['methodology']))
                            <p style="margin-top: 15px; font-style: italic;">
                                <strong>Methodology:</strong> {{ $report['methodology'] }}
                            </p>
                        @endif
                    </div>
                @endif
                
                <!-- Assumptions -->
                @if(isset($report['assumptions']) && is_array($report['assumptions']))
                    <div class="prediction-content" style="background-color: #f3e5f5; border-color: #9c27b0;">
                        <h3>Key Assumptions</h3>
                        <ul class="factors-list">
                            @foreach($report['assumptions'] as $assumption)
                                <li>{{ is_string($assumption) ? $assumption : (string)$assumption }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Success Metrics -->
                @if(isset($report['success_metrics']) && is_array($report['success_metrics']))
                    <div class="prediction-content" style="background-color: #e8f5e8; border-color: #4caf50;">
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
                    <div class="prediction-content" style="background-color: #fff3e0; border-color: #ff9800;">
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
                        <pre style="white-space: pre-wrap; font-family: 'Times New Roman', serif; font-size: 9pt;">{{ json_encode($prediction->prediction_result, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    @elseif($prediction->status === 'failed')
        <div class="section">
            <div class="section-title">Analysis Status</div>
            <div class="prediction-content" style="background-color: #ffebee; border-color: #f44336; color: #c62828;">
                <h3>Analysis Failed</h3>
                <p>The AI prediction analysis could not be completed. Please try again or contact support.</p>
            </div>
        </div>
    @else
        <div class="section">
            <div class="section-title">Analysis Status</div>
            <div class="prediction-content" style="background-color: #fff3e0; border-color: #ff9800; color: #ef6c00;">
                <h3>Processing...</h3>
                <p>Your prediction analysis is being processed by AI. This may take a few moments.</p>
            </div>
        </div>
    @endif

    <!-- Risk Assessment -->
    @if($prediction->status === 'completed')
        <div class="section">
            <div class="section-title">Risk Assessment & Mitigation</div>
            
            @if(isset($prediction->prediction_result['risk_assessment']) && is_array($prediction->prediction_result['risk_assessment']))
                @foreach($prediction->prediction_result['risk_assessment'] as $index => $risk)
                    <div class="prediction-content" style="background-color: #ffebee; border-color: #f44336; margin-bottom: 15px;">
                        <h3 style="color: #d32f2f; margin-top: 0;">Risk {{ $index + 1 }}</h3>
                        <p style="margin: 8px 0;"><strong>Description:</strong> {{ is_string($risk['risk'] ?? '') ? $risk['risk'] : 'Risk description not available' }}</p>
                        
                        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin: 10px 0;">
                            @if(isset($risk['level']) && is_string($risk['level']))
                                <span style="background-color: #ffcdd2; color: #c62828; padding: 4px 10px; border-radius: 12px; font-size: 9pt; font-weight: bold;">Level: {{ $risk['level'] }}</span>
                            @endif
                            @if(isset($risk['probability']) && is_string($risk['probability']))
                                <span style="background-color: #fff3e0; color: #ef6c00; padding: 4px 10px; border-radius: 12px; font-size: 9pt; font-weight: bold;">Probability: {{ $risk['probability'] }}</span>
                            @endif
                            @if(isset($risk['impact']) && is_string($risk['impact']))
                                <span style="background-color: #ffebee; color: #c62828; padding: 4px 10px; border-radius: 12px; font-size: 9pt; font-weight: bold;">Impact: {{ $risk['impact'] }}</span>
                            @endif
                        </div>
                        
                        @if(isset($risk['timeline']) && is_string($risk['timeline']))
                            <p style="margin: 8px 0;"><strong>Timeline:</strong> {{ $risk['timeline'] }}</p>
                        @endif
                        @if(isset($risk['mitigation']) && is_string($risk['mitigation']))
                            <p style="margin: 8px 0;"><strong>Mitigation Strategy:</strong> {{ $risk['mitigation'] }}</p>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="subsection-title">Key Risk Factors</div>
                <div class="prediction-content">
                    <p>Based on the analysis, several risk factors have been identified that require attention and proactive management:</p>
                    <ul class="factors-list">
                        <li><strong>Data Quality:</strong> The accuracy of predictions depends on the quality and completeness of input data</li>
                        <li><strong>External Factors:</strong> Unforeseen events or changes in external conditions may impact prediction accuracy</li>
                        <li><strong>Implementation Challenges:</strong> Success depends on effective execution of recommended strategies</li>
                    </ul>
                </div>
                
                <div class="subsection-title">Mitigation Strategies</div>
                <div class="mitigation">
                    <h4>Recommended Actions</h4>
                    <ul class="factors-list">
                        <li>Regular monitoring and validation of prediction outcomes</li>
                        <li>Continuous data collection and analysis updates</li>
                        <li>Flexible strategy adaptation based on changing circumstances</li>
                        <li>Stakeholder engagement and communication</li>
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Strategic Recommendations -->
    @if($prediction->status === 'completed')
        <div class="section">
            <div class="section-title">Strategic Recommendations</div>
            
            <div class="recommendations">
                <h4>Immediate Actions (0-30 days)</h4>
                <ul class="factors-list">
                    <li>Review and validate all prediction data points</li>
                    <li>Identify key stakeholders and decision-makers</li>
                    <li>Develop initial response strategies</li>
                </ul>
            </div>
            
            <div class="recommendations">
                <h4>Short-term Actions (1-3 months)</h4>
                <ul class="factors-list">
                    <li>Implement monitoring and tracking systems</li>
                    <li>Begin stakeholder engagement programs</li>
                    <li>Develop contingency plans for identified risks</li>
                </ul>
            </div>
            
            <div class="recommendations">
                <h4>Long-term Actions (3-12 months)</h4>
                <ul class="factors-list">
                    <li>Evaluate prediction accuracy and adjust models</li>
                    <li>Scale successful strategies and initiatives</li>
                    <li>Establish ongoing monitoring and review processes</li>
                </ul>
            </div>
        </div>
    @endif

    <!-- Technical Details -->
    <div class="section">
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

    <div class="footer">
        <p>This report was automatically generated by the AI Prediction Analysis System</p>
        <p>Report ID: {{ $prediction->id }} | Generated: {{ date('Y-m-d H:i:s') }}</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>

    <div class="page-number">
        Page 1 of 1
    </div>
</body>
</html>
