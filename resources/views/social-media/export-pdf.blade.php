<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Analysis Report</title>
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
                    <p><strong>Overall Risk Level:</strong> {{ $analysis['risk_assessment']['overall_risk_level'] }}</p>
                @endif
                
                @if(isset($analysis['risk_assessment']['risk_factors']) && is_array($analysis['risk_assessment']['risk_factors']))
                    <div class="subsection-title">Risk Factors</div>
                    <ul class="factors-list">
                        @foreach($analysis['risk_assessment']['risk_factors'] as $risk)
                            <li>
                                @if(is_array($risk))
                                    <strong>{{ $risk['risk'] ?? 'Risk' }}</strong>
                                    @if(isset($risk['level'])) ({{ $risk['level'] }})@endif
                                    @if(isset($risk['description']))<br>{{ $risk['description'] }}@endif
                                @else
                                    {{ $risk }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                
                @if(isset($analysis['risk_assessment']['red_flags']) && is_array($analysis['risk_assessment']['red_flags']))
                    <div class="subsection-title">Red Flags</div>
                    <ul class="factors-list">
                        @foreach($analysis['risk_assessment']['red_flags'] as $flag)
                            <li>{{ is_string($flag) ? $flag : json_encode($flag) }}</li>
                        @endforeach
                    </ul>
                @endif
                
                @if(isset($analysis['risk_assessment']['positive_indicators']) && is_array($analysis['risk_assessment']['positive_indicators']))
                    <div class="subsection-title">Positive Indicators</div>
                    <ul class="factors-list">
                        @foreach($analysis['risk_assessment']['positive_indicators'] as $indicator)
                            <li>{{ is_string($indicator) ? $indicator : json_encode($indicator) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @endif

        <!-- Professional Footprint -->
        @if(isset($analysis['professional_footprint']))
        <div class="section major-section avoid-break">
            <div class="section-title">Professional Footprint Analysis</div>
            @php
                $footprint = $analysis['professional_footprint'];
                $score = null;
                if (isset($footprint['professionalism_score'])) {
                    $score = is_numeric($footprint['professionalism_score']) ? (int)$footprint['professionalism_score'] : null;
                } elseif (isset($footprint['score'])) {
                    $score = is_numeric($footprint['score']) ? (int)$footprint['score'] : null;
                }
                if ($score === null) $score = 50;
                $maxScore = 100;
                $percentage = round(($score / $maxScore) * 100);
                $scoreColor = '#ef4444';
                if ($percentage >= 70) {
                    $scoreColor = '#10b981';
                } elseif ($percentage >= 50) {
                    $scoreColor = '#f59e0b';
                }
                $radius = 50;
                $circumference = 2 * M_PI * $radius;
                $offset = $circumference - ($percentage / 100) * $circumference;
                $confidence = $footprint['confidence'] ?? $footprint['confidence_level'] ?? null;
                $overview = $footprint['overview'] ?? $footprint['summary'] ?? '';
            @endphp
            <div class="content-box">
                @if($confidence)
                    <p style="margin-bottom: 12px;"><strong>Confidence:</strong> {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}</p>
                @endif
                @if($overview)
                    <p style="margin-bottom: 20px;">{{ $overview }}</p>
                @endif
                
                <!-- Professionalism Score Display -->
                <div style="margin: 20px 0; padding: 16px; background: #f8fafc; border-left: 4px solid {{ $scoreColor }}; border-radius: 4px;">
                    <div style="font-size: 14pt; font-weight: bold; color: #1e293b; margin-bottom: 8px;">Professionalism Score</div>
                    <div style="font-size: 32pt; font-weight: bold; color: {{ $scoreColor }}; margin-bottom: 4px;">{{ $score }}/{{ $maxScore }}</div>
                    <div style="font-size: 10pt; color: #64748b;">
                        @if($percentage >= 70)
                            Excellent - Strong professional presence
                        @elseif($percentage >= 50)
                            Good - Moderate professional presence
                        @else
                            Needs Improvement - Limited professional presence
                        @endif
                    </div>
                </div>
                
                @if(isset($footprint['content_relevance']) && is_string($footprint['content_relevance']))
                    <div class="subsection-title">Content Relevance</div>
                    <p>{{ $footprint['content_relevance'] }}</p>
                @endif
                @if(isset($footprint['tone_analysis']) && is_string($footprint['tone_analysis']))
                    <div class="subsection-title">Tone Analysis</div>
                    <p>{{ $footprint['tone_analysis'] }}</p>
                @endif
                @if(isset($footprint['engagement_quality']) && is_string($footprint['engagement_quality']))
                    <div class="subsection-title">Engagement Quality</div>
                    <p>{{ $footprint['engagement_quality'] }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Work Ethic Indicators -->
        @if(isset($analysis['work_ethic_indicators']))
        <div class="section major-section avoid-break page-break">
            <div class="section-title">Work Ethic Indicators</div>
            @php
                $workEthic = $analysis['work_ethic_indicators'];
                $confidence = $workEthic['confidence'] ?? $workEthic['confidence_level'] ?? 75;
                
                $dimensions = [
                    'consistency' => [
                        'score' => $workEthic['consistency_score'] ?? $workEthic['consistency'] ?? null,
                        'description' => $workEthic['consistency'] ?? $workEthic['consistency_description'] ?? 'Assessment of posting consistency and activity patterns.',
                        'label' => 'Consistency'
                    ],
                    'follow_through' => [
                        'score' => $workEthic['follow_through_score'] ?? $workEthic['follow_through'] ?? $workEthic['followthrough'] ?? null,
                        'description' => $workEthic['follow_through'] ?? $workEthic['follow_through_description'] ?? $workEthic['followthrough'] ?? 'Evidence of completing tasks and following through on commitments.',
                        'label' => 'Follow-through'
                    ],
                    'collaboration' => [
                        'score' => $workEthic['collaboration_score'] ?? $workEthic['collaboration'] ?? null,
                        'description' => $workEthic['collaboration'] ?? $workEthic['collaboration_description'] ?? 'Indicators of teamwork and collaborative behavior.',
                        'label' => 'Collaboration'
                    ],
                    'initiative' => [
                        'score' => $workEthic['initiative_score'] ?? $workEthic['initiative'] ?? null,
                        'description' => $workEthic['initiative'] ?? $workEthic['initiative_description'] ?? 'Signs of proactive behavior and self-directed action.',
                        'label' => 'Initiative'
                    ],
                    'productivity' => [
                        'score' => $workEthic['productivity_score'] ?? $workEthic['productivity'] ?? $workEthic['productivity_signals'] ?? null,
                        'description' => $workEthic['productivity'] ?? $workEthic['productivity_signals'] ?? $workEthic['productivity_description'] ?? 'Signs of productivity and professional activity.',
                        'label' => 'Productivity'
                    ]
                ];
                
                foreach ($dimensions as $key => &$dim) {
                    if ($dim['score'] === null || !is_numeric($dim['score'])) {
                        $text = strtolower($dim['description']);
                        if (strpos($text, 'strong') !== false || strpos($text, 'excellent') !== false || strpos($text, 'high') !== false) {
                            $dim['score'] = 80;
                        } elseif (strpos($text, 'moderate') !== false || strpos($text, 'average') !== false) {
                            $dim['score'] = 50;
                        } elseif (strpos($text, 'low') !== false || strpos($text, 'lacks') !== false || strpos($text, 'limited') !== false) {
                            $dim['score'] = 30;
                        } else {
                            $dim['score'] = 50;
                        }
                    } else {
                        $dim['score'] = (int)$dim['score'];
                    }
                    $dim['score'] = max(0, min(100, $dim['score']));
                }
                unset($dim);
                
                $centerX = 150;
                $centerY = 150;
                $radius = 120;
                $numAxes = 5;
                $angleStep = (2 * M_PI) / $numAxes;
                
                $points = [];
                $angles = [];
                
                foreach ($dimensions as $key => $dim) {
                    $i = array_search($key, array_keys($dimensions));
                    $angle = ($i * $angleStep) - (M_PI / 2);
                    $angles[] = $angle;
                    $score = $dim['score'];
                    $distance = ($score / 100) * $radius;
                    $x = $centerX + ($distance * cos($angle));
                    $y = $centerY + ($distance * sin($angle));
                    $points[] = [
                        'x' => $x, 
                        'y' => $y, 
                        'score' => $score, 
                        'label' => $dim['label'],
                        'description' => $dim['description'],
                        'key' => $key
                    ];
                }
            @endphp
            <div class="content-box">
                @if($confidence)
                    <p style="margin-bottom: 12px;"><strong>Confidence:</strong> {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}</p>
                @endif
                <p style="margin-bottom: 20px;">Based on analysis of online activities and communication patterns, the following work ethic indicators have been assessed across five key dimensions:</p>
                
                <!-- Work Ethic Dimensions - Text Format -->
                <div style="margin: 20px 0;">
                    @foreach($dimensions as $key => $dim)
                        @php
                            $percentage = $dim['score'];
                            $color = '#3b82f6';
                            $level = 'Moderate';
                            if ($percentage >= 70) {
                                $color = '#10b981';
                                $level = 'Strong';
                            } elseif ($percentage >= 50) {
                                $color = '#f59e0b';
                                $level = 'Moderate';
                            } else {
                                $color = '#ef4444';
                                $level = 'Low';
                            }
                        @endphp
                        <div style="margin-bottom: 16px; padding: 12px; background: #f8fafc; border-left: 4px solid {{ $color }}; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <div style="font-size: 11pt; font-weight: bold; color: #1e293b;">{{ $dim['label'] }}</div>
                                <div style="font-size: 14pt; font-weight: bold; color: {{ $color }};">{{ $dim['score'] }}/100</div>
                            </div>
                            <div style="font-size: 9pt; color: #64748b; margin-bottom: 4px;">
                                <strong>Level:</strong> {{ $level }}
                            </div>
                            <div style="font-size: 9pt; color: #374151; line-height: 1.5;">
                                {{ $dim['description'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Cultural Fit Indicators -->
        @if(isset($analysis['cultural_fit_indicators']))
        <div class="section major-section avoid-break page-break">
            <div class="section-title">Cultural Fit Indicators</div>
            @php
                $culturalFit = $analysis['cultural_fit_indicators'];
                $confidence = $culturalFit['confidence'] ?? $culturalFit['confidence_level'] ?? null;
                $overview = $culturalFit['overview'] ?? $culturalFit['summary'] ?? '';
                
                $indicators = [
                    'value_alignment' => [
                        'label' => 'Value Alignment',
                        'value' => $culturalFit['value_alignment_level'] ?? $culturalFit['values_alignment'] ?? $culturalFit['value_alignment'] ?? null,
                        'description' => $culturalFit['value_alignment'] ?? $culturalFit['values_alignment'] ?? 'Assessment of values and principles shown in content.',
                        'color_scheme' => 'yellow-green'
                    ],
                    'teamwork_ethos' => [
                        'label' => 'Teamwork Ethos',
                        'value' => $culturalFit['teamwork_ethos_level'] ?? $culturalFit['team_collaboration'] ?? $culturalFit['teamwork'] ?? null,
                        'description' => $culturalFit['teamwork_ethos'] ?? $culturalFit['team_collaboration'] ?? 'Indicators of team-oriented behavior and collaboration.',
                        'color_scheme' => 'blue-purple'
                    ],
                    'innovation_mindset' => [
                        'label' => 'Innovation Mindset',
                        'value' => $culturalFit['innovation_mindset_level'] ?? $culturalFit['innovation'] ?? null,
                        'description' => $culturalFit['innovation_mindset'] ?? $culturalFit['innovation'] ?? 'Attitude toward innovation and creative thinking.',
                        'color_scheme' => 'pink-red'
                    ]
                ];
                
                if (!function_exists('pdf_culturalFit_getSliderValue')) {
                    function pdf_culturalFit_getSliderValue($value) {
                        if (is_numeric($value)) {
                            return min(100, max(0, (int)$value));
                        }
                        $value = strtolower(trim($value ?? ''));
                        if (strpos($value, 'low') !== false || strpos($value, 'limited') !== false || strpos($value, 'weak') !== false) {
                            return 20;
                        } elseif (strpos($value, 'medium') !== false || strpos($value, 'moderate') !== false || strpos($value, 'average') !== false) {
                            return 50;
                        } elseif (strpos($value, 'high') !== false || strpos($value, 'strong') !== false || strpos($value, 'excellent') !== false) {
                            return 80;
                        }
                        return 50;
                    }
                }
                
                if (!function_exists('pdf_culturalFit_getSliderGradient')) {
                    function pdf_culturalFit_getSliderGradient($value, $colorScheme) {
                        if ($colorScheme === 'yellow-green') {
                            return ['start' => '#fbbf24', 'mid' => '#f59e0b', 'end' => '#10b981'];
                        } elseif ($colorScheme === 'blue-purple') {
                            return ['start' => '#60a5fa', 'mid' => '#7c3aed', 'end' => '#8b5cf6'];
                        } elseif ($colorScheme === 'pink-red') {
                            return ['start' => '#f472b6', 'mid' => '#f87171', 'end' => '#ef4444'];
                        }
                        return ['start' => '#667eea', 'mid' => '#764ba2', 'end' => '#5a67d8'];
                    }
                }
                
                if (!function_exists('pdf_culturalFit_getSliderHandleColor')) {
                    function pdf_culturalFit_getSliderHandleColor($value, $colorScheme) {
                        if ($colorScheme === 'yellow-green') {
                            return $value > 60 ? '#10b981' : ($value > 30 ? '#f59e0b' : '#fbbf24');
                        } elseif ($colorScheme === 'blue-purple') {
                            return $value > 60 ? '#8b5cf6' : ($value > 30 ? '#7c3aed' : '#60a5fa');
                        } elseif ($colorScheme === 'pink-red') {
                            return $value > 60 ? '#ef4444' : ($value > 30 ? '#f87171' : '#f472b6');
                        }
                        return '#667eea';
                    }
                }
                
                if (!function_exists('pdf_culturalFit_getSliderLabel')) {
                    function pdf_culturalFit_getSliderLabel($value) {
                        if ($value >= 70) {
                            return 'Strong';
                        } elseif ($value >= 40) {
                            return 'Medium';
                        } else {
                            return 'Low';
                        }
                    }
                }
            @endphp
            <div class="content-box">
                @if($confidence)
                    <p style="margin-bottom: 12px;"><strong>Confidence:</strong> {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}</p>
                @endif
                @if($overview)
                    <p style="margin-bottom: 20px;">{{ $overview }}</p>
                @endif
                
                <!-- Indicators with Sliders -->
                @foreach($indicators as $key => $indicator)
                    @php
                        $sliderValue = pdf_culturalFit_getSliderValue($indicator['value']);
                        $gradient = pdf_culturalFit_getSliderGradient($sliderValue, $indicator['color_scheme']);
                        $handleColor = pdf_culturalFit_getSliderHandleColor($sliderValue, $indicator['color_scheme']);
                    @endphp
                    <div style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border-left: 3px solid {{ $handleColor }};">
                        <div style="font-weight: bold; color: #1e293b; margin-bottom: 12px; font-size: 10pt;">{{ $indicator['label'] }}</div>
                        
                        @php
                            $sliderLabel = pdf_culturalFit_getSliderLabel($sliderValue);
                        @endphp
                        <!-- Level Display -->
                        <div style="margin-bottom: 8px;">
                            <div style="font-size: 12pt; font-weight: bold; color: {{ $handleColor }}; margin-bottom: 4px;">
                                Level: {{ $sliderLabel }}
                            </div>
                            <div style="font-size: 10pt; color: #64748b;">
                                Score: {{ $sliderValue }}%
                            </div>
                        </div>
                        
                        <div style="color: #666; font-size: 9pt;">{{ $indicator['description'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Professional Growth Signals -->
        @if(isset($analysis['professional_growth_signals']))
        <div class="section major-section avoid-break page-break">
            <div class="section-title">Professional Growth Signals</div>
            @php
                $growth = $analysis['professional_growth_signals'];
                $confidence = $growth['confidence'] ?? $growth['confidence_level'] ?? null;
                $overview = $growth['overview'] ?? $growth['summary'] ?? '';
                
                $indicators = [
                    'learning_initiative' => [
                        'label' => 'Learning Initiative',
                        'value' => $growth['learning_initiative_level'] ?? $growth['learning_initiative'] ?? $growth['adaptability'] ?? null,
                        'description' => $growth['learning_initiative'] ?? $growth['adaptability'] ?? 'Evidence of active learning and skill development.',
                        'color_scheme' => 'yellow-green'
                    ],
                    'skill_development' => [
                        'label' => 'Skill Development',
                        'value' => $growth['skill_development_level'] ?? $growth['skill_development'] ?? null,
                        'description' => $growth['skill_development'] ?? 'Evidence of skill development and learning.',
                        'color_scheme' => 'blue-purple'
                    ],
                    'mentorship_activity' => [
                        'label' => 'Mentorship Activity',
                        'value' => $growth['mentorship_activity_level'] ?? $growth['mentorship'] ?? $growth['knowledge_sharing'] ?? null,
                        'description' => $growth['mentorship_activity'] ?? $growth['mentorship'] ?? $growth['knowledge_sharing'] ?? 'Evidence of mentoring activities or seeking mentorship.',
                        'color_scheme' => 'pink-red'
                    ]
                ];
                
                if (!function_exists('pdf_growthSignals_getSliderValue')) {
                    function pdf_growthSignals_getSliderValue($value) {
                        if (is_numeric($value)) {
                            return min(100, max(0, (int)$value));
                        }
                        $value = strtolower(trim($value ?? ''));
                        if (strpos($value, 'low') !== false || strpos($value, 'limited') !== false || strpos($value, 'weak') !== false || strpos($value, 'no') !== false) {
                            return 20;
                        } elseif (strpos($value, 'medium') !== false || strpos($value, 'moderate') !== false || strpos($value, 'average') !== false) {
                            return 50;
                        } elseif (strpos($value, 'high') !== false || strpos($value, 'strong') !== false || strpos($value, 'excellent') !== false) {
                            return 80;
                        }
                        return 50;
                    }
                }
                
                if (!function_exists('pdf_growthSignals_getSliderGradient')) {
                    function pdf_growthSignals_getSliderGradient($value, $colorScheme) {
                        if ($colorScheme === 'yellow-green') {
                            return ['start' => '#fbbf24', 'end' => '#10b981'];
                        } elseif ($colorScheme === 'blue-purple') {
                            return ['start' => '#60a5fa', 'end' => '#8b5cf6'];
                        } elseif ($colorScheme === 'pink-red') {
                            return ['start' => '#f472b6', 'end' => '#ef4444'];
                        }
                        return ['start' => '#667eea', 'end' => '#5a67d8'];
                    }
                }
                
                if (!function_exists('pdf_growthSignals_getSliderHandleColor')) {
                    function pdf_growthSignals_getSliderHandleColor($value, $colorScheme) {
                        if ($colorScheme === 'yellow-green') {
                            return $value > 60 ? '#10b981' : ($value > 30 ? '#f59e0b' : '#fbbf24');
                        } elseif ($colorScheme === 'blue-purple') {
                            return $value > 60 ? '#8b5cf6' : ($value > 30 ? '#7c3aed' : '#60a5fa');
                        } elseif ($colorScheme === 'pink-red') {
                            return $value > 60 ? '#ef4444' : ($value > 30 ? '#f87171' : '#f472b6');
                        }
                        return '#667eea';
                    }
                }
                
                if (!function_exists('pdf_growthSignals_getSliderLabel')) {
                    function pdf_growthSignals_getSliderLabel($value) {
                        if ($value >= 70) {
                            return 'Strong';
                        } elseif ($value >= 40) {
                            return 'Medium';
                        } else {
                            return 'Low';
                        }
                    }
                }
            @endphp
            <div class="content-box">
                @if($confidence)
                    <p style="margin-bottom: 12px;"><strong>Confidence:</strong> {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}</p>
                @endif
                @if($overview)
                    <p style="margin-bottom: 20px;">{{ $overview }}</p>
                @endif
                
                <!-- Indicators with Sliders -->
                @foreach($indicators as $key => $indicator)
                    @php
                        $sliderValue = pdf_growthSignals_getSliderValue($indicator['value']);
                        $gradient = pdf_growthSignals_getSliderGradient($sliderValue, $indicator['color_scheme']);
                        $handleColor = pdf_growthSignals_getSliderHandleColor($sliderValue, $indicator['color_scheme']);
                        $sliderLabel = pdf_growthSignals_getSliderLabel($sliderValue);
                    @endphp
                    <div style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border-left: 3px solid {{ $handleColor }};">
                        <div style="font-weight: bold; color: #1e293b; margin-bottom: 12px; font-size: 10pt;">{{ $indicator['label'] }}</div>
                        
                        <!-- Level Display -->
                        <div style="margin-bottom: 8px;">
                            <div style="font-size: 12pt; font-weight: bold; color: {{ $handleColor }}; margin-bottom: 4px;">
                                Level: {{ $sliderLabel }}
                            </div>
                            <div style="font-size: 10pt; color: #64748b;">
                                Score: {{ $sliderValue }}%
                            </div>
                        </div>
                        
                        <div style="color: #666; font-size: 9pt;">{{ $indicator['description'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Activity Overview -->
        @if(isset($analysis['activity_overview']))
        <div class="section major-section">
            <div class="section-title">Activity Overview & Behavioral Patterns</div>
            <div class="content-box">
                @php
                    $activity = $analysis['activity_overview'];
                @endphp
                @if(isset($activity['overview']) && is_string($activity['overview']))
                    <p>{{ $activity['overview'] }}</p>
                @endif
                @if(isset($activity['posting_frequency']) && is_string($activity['posting_frequency']))
                    <p><strong>Posting Frequency:</strong> {{ $activity['posting_frequency'] }}</p>
                @endif
                @if(isset($activity['content_types']) && is_string($activity['content_types']))
                    <p><strong>Content Types:</strong> {{ $activity['content_types'] }}</p>
                @endif
                @if(isset($activity['peak_activity_times']) && is_string($activity['peak_activity_times']))
                    <p><strong>Peak Activity Times:</strong> {{ $activity['peak_activity_times'] }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Personality & Communication -->
        @if(isset($analysis['personality_communication']))
        <div class="section major-section avoid-break page-break">
            <div class="section-title">Personality & Communication Snapshot</div>
            @php
                $personality = $analysis['personality_communication'];
                $confidence = $personality['confidence'] ?? $personality['confidence_level'] ?? null;
                $overview = $personality['overview'] ?? $personality['summary'] ?? '';
                $toneAnalysis = $personality['tone_analysis'] ?? $personality['communication_style'] ?? '';
                
                $traits = [
                    'openness' => [
                        'score' => $personality['openness_score'] ?? $personality['openness'] ?? null,
                        'description' => $personality['openness'] ?? $personality['openness_description'] ?? 'Openness to experience, creativity, and intellectual curiosity.',
                        'label' => 'Openness'
                    ],
                    'conscientiousness' => [
                        'score' => $personality['conscientiousness_score'] ?? $personality['conscientiousness'] ?? null,
                        'description' => $personality['conscientiousness'] ?? $personality['conscientiousness_description'] ?? 'Organization, dependability, and self-discipline.',
                        'label' => 'Conscientiousness'
                    ],
                    'extraversion' => [
                        'score' => $personality['extraversion_score'] ?? $personality['extraversion'] ?? null,
                        'description' => $personality['extraversion'] ?? $personality['extraversion_description'] ?? 'Sociability, assertiveness, and energy in social situations.',
                        'label' => 'Extraversion'
                    ],
                    'agreeableness' => [
                        'score' => $personality['agreeableness_score'] ?? $personality['agreeableness'] ?? null,
                        'description' => $personality['agreeableness'] ?? $personality['agreeableness_description'] ?? 'Trust, altruism, kindness, and cooperation.',
                        'label' => 'Agreeableness'
                    ],
                    'neuroticism' => [
                        'score' => $personality['neuroticism_score'] ?? $personality['neuroticism'] ?? null,
                        'description' => $personality['neuroticism'] ?? $personality['neuroticism_description'] ?? 'Emotional stability and resilience to stress.',
                        'label' => 'Neuroticism'
                    ]
                ];
                
                foreach ($traits as $key => &$trait) {
                    if ($trait['score'] === null || !is_numeric($trait['score'])) {
                        $text = strtolower($trait['description']);
                        if (strpos($text, 'strong') !== false || strpos($text, 'excellent') !== false || strpos($text, 'high') !== false) {
                            $trait['score'] = 80;
                        } elseif (strpos($text, 'moderate') !== false || strpos($text, 'average') !== false) {
                            $trait['score'] = 50;
                        } elseif (strpos($text, 'low') !== false || strpos($text, 'lacks') !== false || strpos($text, 'limited') !== false) {
                            $trait['score'] = 30;
                        } else {
                            $trait['score'] = 50;
                        }
                    } else {
                        $trait['score'] = (int)$trait['score'];
                    }
                    $trait['score'] = max(0, min(100, $trait['score']));
                }
                unset($trait);
                
                $centerX = 150;
                $centerY = 150;
                $radius = 120;
                $numAxes = 5;
                $angleStep = (2 * M_PI) / $numAxes;
                
                $traitOrder = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];
                
                $communicationStrengths = [];
                if (isset($personality['communication_strengths']) && is_array($personality['communication_strengths'])) {
                    $communicationStrengths = $personality['communication_strengths'];
                } else {
                    if ($toneAnalysis) {
                        $communicationStrengths[] = 'Tone: ' . strtolower($toneAnalysis);
                    }
                }
            @endphp
            <div class="content-box">
                @if($confidence)
                    <p style="margin-bottom: 12px;"><strong>Confidence:</strong> {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}</p>
                @endif
                @if($overview || $toneAnalysis)
                    <p style="margin-bottom: 20px;">
                        @if($overview)
                            {{ $overview }}
                        @else
                            Analysis of public communications suggests a balanced personality profile and effective communication style.
                        @endif
                        @if($toneAnalysis)
                            Tone analysis indicates a <strong style="color: #10b981;">{{ strtolower($toneAnalysis) }}</strong> in professional interactions.
                        @endif
                    </p>
                @endif
                
                <!-- Personality Traits - Text Format -->
                <div style="margin: 20px 0;">
                    @foreach($traitOrder as $traitKey)
                        @if(isset($traits[$traitKey]))
                            @php
                                $trait = $traits[$traitKey];
                                $percentage = $trait['score'];
                                $color = '#3b82f6';
                                $level = 'Moderate';
                                if ($percentage >= 70) {
                                    $color = '#10b981';
                                    $level = 'High';
                                } elseif ($percentage >= 50) {
                                    $color = '#f59e0b';
                                    $level = 'Moderate';
                                } else {
                                    $color = '#ef4444';
                                    $level = 'Low';
                                }
                            @endphp
                            <div style="margin-bottom: 16px; padding: 12px; background: #f8fafc; border-left: 4px solid {{ $color }}; border-radius: 4px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div style="font-size: 11pt; font-weight: bold; color: #1e293b;">{{ $trait['label'] }}</div>
                                    <div style="font-size: 14pt; font-weight: bold; color: {{ $color }};">{{ $trait['score'] }}/100</div>
                                </div>
                                <div style="font-size: 9pt; color: #64748b; margin-bottom: 4px;">
                                    <strong>Level:</strong> {{ $level }}
                                </div>
                                <div style="font-size: 9pt; color: #374151; line-height: 1.5;">
                                    {{ $trait['description'] }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Communication Strengths -->
                @if(count($communicationStrengths) > 0)
                    <div style="margin-top: 20px; padding: 12px; background: #f9f9f9; border-left: 3px solid #3b82f6;">
                        <div style="font-weight: bold; color: #1e293b; margin-bottom: 8px; font-size: 10pt;">Communication Strengths:</div>
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach($communicationStrengths as $strength)
                                <li style="margin-bottom: 4px; font-size: 9pt; color: #666;">
                                    @if(is_string($strength))
                                        {{ $strength }}
                                    @elseif(is_array($strength))
                                        {{ $strength['trait'] ?? $strength['label'] ?? '' }}: {{ $strength['description'] ?? '' }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
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
                <p>Your social media analysis is being processed by AI. This may take a few moments.</p>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the NUJUM System</p>
        <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>
</body>
</html>

