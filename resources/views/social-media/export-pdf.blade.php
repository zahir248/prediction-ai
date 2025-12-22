<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Analysis Report</title>
    @php
        // Set a flag to indicate this is for PDF export
        $isPdfExport = true;
        $GLOBALS['isPdfExport'] = true;
    @endphp
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
        
        /* SVG and Chart Styles for PDF */
        svg {
            max-width: 100%;
            height: auto;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Social Media Analysis Report</h1>
        <p class="subtitle">{{ $socialMediaAnalysis->username }}</p>
        <p>Generated on {{ date('F d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Analysis Information -->
    <div class="section major-section">
        <div class="section-title">Analysis Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Username</div>
                <div class="info-value">{{ $socialMediaAnalysis->username }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @php
                        $statusClass = 'status-' . strtolower($socialMediaAnalysis->status);
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($socialMediaAnalysis->status) }}</span>
                </div>
            </div>
            @if($socialMediaAnalysis->status === 'completed' && $socialMediaAnalysis->ai_analysis)
            @php
                $analysisType = $socialMediaAnalysis->ai_analysis['analysis_type'] ?? 'professional';
            @endphp
            <div class="info-row">
                <div class="info-label">Analysis Type</div>
                <div class="info-value">
                    <span style="font-weight: 600; text-transform: capitalize;">{{ $analysisType === 'political' ? 'Political' : 'Professional' }}</span>
                </div>
            </div>
            @endif
            @if($socialMediaAnalysis->platform_count > 0 && is_array($socialMediaAnalysis->found_platforms) && count($socialMediaAnalysis->found_platforms) > 0)
            <div class="info-row">
                <div class="info-label">Platforms Found</div>
                <div class="info-value">{{ $socialMediaAnalysis->platform_count }} ({{ implode(', ', array_map('ucfirst', $socialMediaAnalysis->found_platforms)) }})</div>
            </div>
            @endif
            @if($socialMediaAnalysis->processing_time)
            <div class="info-row">
                <div class="info-label">Processing Time</div>
                <div class="info-value">{{ number_format($socialMediaAnalysis->processing_time, 2) }} seconds</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Analyzed On</div>
                <div class="info-value">{{ $socialMediaAnalysis->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>

    @if($socialMediaAnalysis->status === 'completed' && $socialMediaAnalysis->ai_analysis)
        @php
            $analysis = $socialMediaAnalysis->ai_analysis;
        @endphp

        <!-- Executive Summary -->
        @if(isset($analysis['executive_summary']) && is_string($analysis['executive_summary']))
        <div class="section major-section">
            <div class="section-title">Executive Summary & Risk Assessment</div>
            <div class="content-box">
                <p>{{ $analysis['executive_summary'] }}</p>
            </div>
        </div>
        @endif

        <!-- Risk Assessment -->
        @if(isset($analysis['risk_assessment']))
        <div class="section major-section">
            <div class="section-title">Risk Assessment</div>
            <div class="content-box">
                @if(isset($analysis['risk_assessment']['overall_risk_level']) && is_string($analysis['risk_assessment']['overall_risk_level']))
                    @php
                        $riskColor = $analysis['risk_assessment']['overall_risk_level'] === 'High' ? '#ef4444' : 
                                    ($analysis['risk_assessment']['overall_risk_level'] === 'Medium' ? '#f59e0b' : '#10b981');
                    @endphp
                    <p><strong>Overall Risk Level:</strong> <span style="color: {{ $riskColor }}; font-weight: 600;">{{ $analysis['risk_assessment']['overall_risk_level'] }}</span></p>
                @endif
                
                @if(isset($analysis['risk_assessment']['risk_factors']) && is_array($analysis['risk_assessment']['risk_factors']))
                    <div class="subsection-title">Risk Factors</div>
                    <ul class="factors-list">
                        @foreach($analysis['risk_assessment']['risk_factors'] as $risk)
                            <li>
                                @if(is_array($risk))
                                    <strong>{{ $risk['risk'] ?? 'Risk' }}</strong>
                                    @if(isset($risk['level'])) <span style="color: #ef4444;">({{ $risk['level'] }})</span>@endif
                                    @if(isset($risk['description']))<br><span style="font-size: 9pt;">{{ $risk['description'] }}</span>@endif
                                @else
                                    {{ $risk }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                
                @if(isset($analysis['risk_assessment']['red_flags']) && is_array($analysis['risk_assessment']['red_flags']))
                    <div class="subsection-title" style="color: #991b1b;">Red Flags</div>
                    <ul class="factors-list" style="background: #fef2f2; padding: 8px 8px 8px 24px; border-left: 4px solid #ef4444; border-radius: 4px;">
                        @foreach($analysis['risk_assessment']['red_flags'] as $flag)
                            <li style="color: #991b1b;">
                                @if(is_string($flag))
                                    {{ $flag }}
                                @else
                                    {{ json_encode($flag) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                
                @if(isset($analysis['risk_assessment']['positive_indicators']) && is_array($analysis['risk_assessment']['positive_indicators']))
                    <div class="subsection-title" style="color: #166534;">Positive Indicators</div>
                    <ul class="factors-list" style="background: #f0fdf4; padding: 8px 8px 8px 24px; border-left: 4px solid #10b981; border-radius: 4px;">
                        @foreach($analysis['risk_assessment']['positive_indicators'] as $indicator)
                            <li style="color: #166534;">
                                @if(is_string($indicator))
                                    {{ $indicator }}
                                @else
                                    {{ json_encode($indicator) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @endif

        @php
            $analysisType = $analysis['analysis_type'] ?? 'professional';
        @endphp

        @if($analysisType === 'professional')
            <!-- Professional Footprint -->
            @if(isset($analysis['professional_footprint']))
                <div class="section major-section avoid-break">
                    @include('social-media.partials.professional-footprint', ['analysis' => $analysis, 'socialMediaAnalysis' => $socialMediaAnalysis])
                </div>
            @endif

            <!-- Work Ethic Indicators -->
            @if(isset($analysis['work_ethic_indicators']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.work-ethic-indicators', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Cultural Fit Indicators -->
            @if(isset($analysis['cultural_fit_indicators']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.cultural-fit-indicators', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Professional Growth Signals -->
            @if(isset($analysis['professional_growth_signals']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.professional-growth-signals', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Personality & Communication -->
            @if(isset($analysis['personality_communication']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.personality-communication', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Career Profile -->
            @if(isset($analysis['career_profile']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.analysis-section', ['title' => 'Career Profile & Growth Signals', 'data' => $analysis['career_profile']])
                </div>
            @endif
        @elseif($analysisType === 'political')
            <!-- Political Profile -->
            @if(isset($analysis['political_profile']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.analysis-section', ['title' => 'Political Profile', 'data' => $analysis['political_profile']])
                </div>
            @endif

            <!-- Political Engagement Indicators -->
            @if(isset($analysis['political_engagement_indicators']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.political-engagement-indicators', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Political Alignment Indicators -->
            @if(isset($analysis['political_alignment_indicators']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.political-alignment-indicators', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Political Growth Signals -->
            @if(isset($analysis['political_growth_signals']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.political-growth-signals', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Political Communication Style -->
            @if(isset($analysis['political_communication_style']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.political-communication-style', ['analysis' => $analysis])
                </div>
            @endif

            <!-- Political Career Profile -->
            @if(isset($analysis['political_career_profile']))
                <div class="section major-section avoid-break page-break">
                    @include('social-media.partials.analysis-section', ['title' => 'Political Career Profile', 'data' => $analysis['political_career_profile']])
                </div>
            @endif
        @endif

        <!-- Activity Overview (shown for both types) -->
        @if(isset($analysis['activity_overview']))
            <div class="section major-section avoid-break page-break">
                @include('social-media.partials.activity-overview', ['analysis' => $analysis, 'socialMediaAnalysis' => $socialMediaAnalysis])
            </div>
        @endif

        <!-- Overall Assessment -->
        @if(isset($analysis['overall_assessment']) && is_string($analysis['overall_assessment']))
        <div class="section major-section">
            <div class="section-title">Overall Assessment</div>
            <div class="content-box">
                <p>{{ $analysis['overall_assessment'] }}</p>
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        @if(isset($analysis['recommendations']) && is_array($analysis['recommendations']))
        <div class="section major-section">
            <div class="section-title">Recommendations</div>
            <div class="content-box">
                <ul class="recommendations-list">
                    @foreach($analysis['recommendations'] as $rec)
                        <li>{{ is_string($rec) ? $rec : json_encode($rec) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

    @elseif($socialMediaAnalysis->status === 'failed')
        <div class="section major-section">
            <div class="section-title">Analysis Status</div>
            <div class="content-box" style="border-left: 3px solid #f44336; color: #c62828;">
                <h3>Analysis Failed</h3>
                <p>The social media analysis could not be completed. Please try again or contact support.</p>
            </div>
        </div>
    @else
        <div class="section major-section">
            <div class="section-title">Analysis Status</div>
            <div class="content-box" style="border-left: 3px solid #ff9800; color: #ef6c00;">
                <h3>Processing...</h3>
                <p>Your social media analysis is being processed by NUJUM. This may take a few moments.</p>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the NUJUM System</p>
        <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
        <p>For questions or support, please visit <a href="https://iesb.com.my/" style="color: #666; text-decoration: none;">https://iesb.com.my/</a></p>
    </div>
</body>
</html>

