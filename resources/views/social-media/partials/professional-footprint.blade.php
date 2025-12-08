@php
    $data = $analysis['professional_footprint'] ?? [];
    
    // Try to extract score from various possible fields
    $score = null;
    if (isset($data['professionalism_score'])) {
        $score = is_numeric($data['professionalism_score']) ? (int)$data['professionalism_score'] : null;
    } elseif (isset($data['score'])) {
        $score = is_numeric($data['score']) ? (int)$data['score'] : null;
    } elseif (isset($data['overall_score'])) {
        $score = is_numeric($data['overall_score']) ? (int)$data['overall_score'] : null;
    }
    
    // If no score, try to calculate from available data or use default
    if ($score === null) {
        // Try to infer score from text descriptions (basic heuristic)
        $positiveIndicators = 0;
        $totalIndicators = 0;
        
        $fieldsToCheck = ['online_presence', 'content_quality', 'brand_consistency', 'platform_utilization', 'audience_engagement'];
        foreach ($fieldsToCheck as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $text = strtolower($data[$field]);
                $totalIndicators++;
                // Simple heuristic: check for positive words
                if (strpos($text, 'positive') !== false || strpos($text, 'good') !== false || 
                    strpos($text, 'strong') !== false || strpos($text, 'professional') !== false ||
                    strpos($text, 'excellent') !== false || strpos($text, 'high') !== false) {
                    $positiveIndicators++;
                }
            }
        }
        
        // Calculate score based on positive indicators (default to 50 if no data)
        if ($totalIndicators > 0) {
            $score = round(($positiveIndicators / $totalIndicators) * 100);
        } else {
            $score = 50; // Default score if no data available
        }
    }
    
    $maxScore = 100;
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 55; // Default confidence
    $overview = $data['overview'] ?? $data['summary'] ?? $data['description'] ?? '';
    $breakdown = $data['breakdown'] ?? $data['metrics'] ?? [];
    
    // Try to extract post count and platform count from overview or data
    $totalPosts = $data['total_posts'] ?? $data['posts_analyzed'] ?? null;
    $platformsCount = $data['platforms_analyzed'] ?? $data['platforms'] ?? null;
    
    // Extract from platform_data if available
    if (!$totalPosts && isset($socialMediaAnalysis->platform_data)) {
        $totalPosts = 0;
        foreach ($socialMediaAnalysis->platform_data as $platform => $platformInfo) {
            if (isset($platformInfo['data']['stats']['total_media'])) {
                $totalPosts += $platformInfo['data']['stats']['total_media'];
            } elseif (isset($platformInfo['data']['stats']['total_videos'])) {
                $totalPosts += $platformInfo['data']['stats']['total_videos'];
            }
        }
    }
    
    if (!$platformsCount && isset($socialMediaAnalysis->platform_data)) {
        $platformsCount = count(array_filter($socialMediaAnalysis->platform_data, function($p) {
            return isset($p['found']) && $p['found'];
        }));
    }
    
    // Calculate percentage for gauge
    $percentage = round(($score / $maxScore) * 100);
    
    // Determine color based on score
    $scoreColor = '#ef4444'; // red
    if ($percentage >= 70) {
        $scoreColor = '#10b981'; // green
    } elseif ($percentage >= 50) {
        $scoreColor = '#f59e0b'; // orange
    }
    
    // Calculate stroke-dasharray for circular gauge (circumference = 2 * π * radius)
    $radius = 60;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference - ($percentage / 100) * $circumference;
@endphp

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Professional Footprint Analysis</h3>
        @if($confidence)
            <span style="background: #f1f5f9; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>

    <!-- Overview Text -->
    @if($overview)
        <div style="margin-bottom: 24px; color: #374151; line-height: 1.8; font-size: 14px;">
            {!! nl2br(e($overview)) !!}
        </div>
    @else
        <div style="margin-bottom: 24px; color: #374151; line-height: 1.8; font-size: 14px;">
            @php
                $username = $socialMediaAnalysis->username ?? 'This profile';
                $postsText = $totalPosts ? "based on analysis of {$totalPosts} posts" : '';
                $platformsText = $platformsCount ? "across {$platformsCount} platform" . ($platformsCount > 1 ? 's' : '') : '';
                $contextText = trim($postsText . ' ' . $platformsText);
            @endphp
            {{ $username }} demonstrates a professionalism score of <strong style="color: {{ $scoreColor }};">{{ $score }}/{{ $maxScore }}</strong>@if($contextText), {{ $contextText }}@endif.
        </div>
    @endif

    <!-- Circular Gauge and Score Display -->
    @php
        $isPdfExport = isset($GLOBALS['isPdfExport']) && $GLOBALS['isPdfExport'] === true;
        
        if ($isPdfExport) {
            // For PDF: Generate SVG as base64 data URI
            $gaugeSvg = '<?xml version="1.0" encoding="UTF-8"?>';
            $gaugeSvg .= '<svg xmlns="http://www.w3.org/2000/svg" width="140" height="140" style="transform: rotate(-90deg);">';
            $gaugeSvg .= sprintf(
                '<circle cx="70" cy="70" r="%s" fill="none" stroke="#e5e7eb" stroke-width="12" stroke-linecap="round"/>',
                $radius
            );
            $gaugeSvg .= sprintf(
                '<circle cx="70" cy="70" r="%s" fill="none" stroke="%s" stroke-width="12" stroke-linecap="round" stroke-dasharray="%s" stroke-dashoffset="%s"/>',
                $radius, htmlspecialchars($scoreColor), $circumference, $offset
            );
            $gaugeSvg .= '</svg>';
            $gaugeDataUri = 'data:image/svg+xml;base64,' . base64_encode($gaugeSvg);
        }
    @endphp
    
    @if(true)
        <div style="display: flex; justify-content: center; align-items: center; margin: 32px 0; text-align: center;">
            <div style="position: relative; width: 140px; height: 140px; margin: 0 auto;">
                @if($isPdfExport)
                    <!-- For PDF: Use img tag with base64 SVG -->
                    <img src="{{ $gaugeDataUri }}" alt="Professionalism Score Gauge" style="width: 140px; height: 140px; margin: 0 auto; display: block;" />
                    <!-- Score Text in Center -->
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 36px; font-weight: 700; color: {{ $scoreColor }}; line-height: 1;">{{ $score }}</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">out of {{ $maxScore }}</div>
                    </div>
                @else
                    <!-- For Web: Use inline SVG -->
                    <svg width="140" height="140" style="transform: rotate(-90deg);">
                        <!-- Background circle -->
                        <circle cx="70" cy="70" r="{{ $radius }}" 
                                fill="none" 
                                stroke="#e5e7eb" 
                                stroke-width="12" 
                                stroke-linecap="round"/>
                        <!-- Progress circle -->
                        <circle cx="70" cy="70" r="{{ $radius }}" 
                                fill="none" 
                                stroke="{{ $scoreColor }}" 
                                stroke-width="12" 
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}"
                                style="transition: stroke-dashoffset 0.5s ease;"/>
                    </svg>
                    <!-- Score Text in Center -->
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 36px; font-weight: 700; color: {{ $scoreColor }}; line-height: 1;">{{ $score }}</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">out of {{ $maxScore }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Breakdown of Metrics -->
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
        <div style="color: #374151; line-height: 1.8; font-size: 14px;">
            <strong style="color: #1e293b;">Breaking down the professionalism metrics:</strong>
            <div style="margin-top: 12px;">
                @if(!empty($breakdown) && is_array($breakdown))
                    @foreach($breakdown as $key => $value)
                        @if(is_string($value))
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                                <span style="color: #64748b;">{{ $value }}</span>
                            </div>
                        @elseif(is_array($value))
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                                <span style="color: #64748b;">{{ implode(', ', array_filter($value, 'is_string')) }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif
                
                @if(isset($data['content_relevance']) || isset($data['tone_analysis']) || isset($data['engagement_quality']))
                    @if(isset($data['content_relevance']))
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #374151;">Content Relevance:</strong> 
                            <span style="color: #64748b;">{{ is_string($data['content_relevance']) ? $data['content_relevance'] : 'Shows professional focus through industry-related posts.' }}</span>
                        </div>
                    @endif
                    @if(isset($data['tone_analysis']))
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #374151;">Tone Analysis:</strong> 
                            <span style="color: #64748b;">{{ is_string($data['tone_analysis']) ? $data['tone_analysis'] : 'Indicates mixed sentiment in public communications.' }}</span>
                        </div>
                    @endif
                    @if(isset($data['engagement_quality']))
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #374151;">Engagement Quality:</strong> 
                            <span style="color: #64748b;">{{ is_string($data['engagement_quality']) ? $data['engagement_quality'] : 'Reflects conversational interactions across platforms.' }}</span>
                        </div>
                    @endif
                    @if(isset($data['concerns']) || isset($data['concerns_notes']))
                        <div style="margin-top: 12px; padding: 12px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px;">
                            <strong style="color: #92400e;">Note:</strong> 
                            <span style="color: #78350f;">{{ is_string($data['concerns'] ?? $data['concerns_notes']) ? ($data['concerns'] ?? $data['concerns_notes']) : 'However, concerns were noted that require further investigation.' }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Details -->
    @foreach($data as $key => $value)
        @if(!in_array($key, ['professionalism_score', 'score', 'max_score', 'confidence', 'confidence_level', 'overview', 'summary', 'description', 'breakdown', 'metrics', 'total_posts', 'posts_analyzed', 'platforms_analyzed', 'platforms', 'content_relevance', 'tone_analysis', 'engagement_quality', 'concerns', 'concerns_notes']))
            @if(is_string($value) && trim($value) !== '')
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                    <span style="color: #64748b; line-height: 1.6;">{{ $value }}</span>
                </div>
            @elseif(is_array($value) && count($value) > 0)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($value as $item)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($item))
                                    {{ $item }}
                                @else
                                    {{ json_encode($item) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
    @endforeach
    </div>
</div>

