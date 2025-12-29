@php
    $data = $analysis['personality_communication'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 75;
    $overview = $data['overview'] ?? $data['summary'] ?? $data['description'] ?? '';
    $toneAnalysis = $data['tone_analysis'] ?? $data['communication_style'] ?? '';
    
    // Extract Big Five personality traits
    $traits = [
        'openness' => [
            'score' => $data['openness_score'] ?? $data['openness'] ?? null,
            'description' => $data['openness'] ?? $data['openness_description'] ?? 'Openness to experience, creativity, and intellectual curiosity.',
            'label' => 'Openness'
        ],
        'conscientiousness' => [
            'score' => $data['conscientiousness_score'] ?? $data['conscientiousness'] ?? null,
            'description' => $data['conscientiousness'] ?? $data['conscientiousness_description'] ?? 'Organization, dependability, and self-discipline.',
            'label' => 'Conscientiousness'
        ],
        'extraversion' => [
            'score' => $data['extraversion_score'] ?? $data['extraversion'] ?? null,
            'description' => $data['extraversion'] ?? $data['extraversion_description'] ?? 'Sociability, assertiveness, and energy in social situations.',
            'label' => 'Extraversion'
        ],
        'agreeableness' => [
            'score' => $data['agreeableness_score'] ?? $data['agreeableness'] ?? null,
            'description' => $data['agreeableness'] ?? $data['agreeableness_description'] ?? 'Trust, altruism, kindness, and cooperation.',
            'label' => 'Agreeableness'
        ],
        'neuroticism' => [
            'score' => $data['neuroticism_score'] ?? $data['neuroticism'] ?? null,
            'description' => $data['neuroticism'] ?? $data['neuroticism_description'] ?? 'Emotional stability and resilience to stress.',
            'label' => 'Neuroticism'
        ]
    ];
    
    // Try to extract numeric scores from descriptions if scores aren't provided
    foreach ($traits as $key => &$trait) {
        if ($trait['score'] === null || !is_numeric($trait['score'])) {
            // Try to extract number from description
            if (preg_match('/\b(\d{1,2})\b/', $trait['description'], $matches)) {
                $trait['score'] = (int)$matches[1];
            } else {
                // Default score based on keywords
                $text = strtolower($trait['description']);
                if (strpos($text, 'strong') !== false || strpos($text, 'excellent') !== false || strpos($text, 'high') !== false) {
                    $trait['score'] = 80;
                } elseif (strpos($text, 'moderate') !== false || strpos($text, 'average') !== false) {
                    $trait['score'] = 50;
                } elseif (strpos($text, 'low') !== false || strpos($text, 'lacks') !== false || strpos($text, 'limited') !== false) {
                    $trait['score'] = 30;
                } else {
                    $trait['score'] = 50; // Default
                }
            }
        } else {
            $trait['score'] = (int)$trait['score'];
        }
        // Ensure score is between 0-100
        $trait['score'] = max(0, min(100, $trait['score']));
    }
    unset($trait);
    
    // Radar chart configuration
    $centerX = 250;
    $centerY = 250;
    $radius = 150;
    $numAxes = 5;
    $angleStep = (2 * M_PI) / $numAxes;
    
    // Calculate points for each trait (order: Openness, Conscientiousness, Extraversion, Agreeableness, Neuroticism)
    $traitOrder = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];
    $points = [];
    $angles = [];
    
    foreach ($traitOrder as $traitKey) {
        if (!isset($traits[$traitKey])) continue;
        
        $i = array_search($traitKey, $traitOrder);
        $angle = ($i * $angleStep) - (M_PI / 2); // Start from top
        $angles[] = $angle;
        $score = $traits[$traitKey]['score'];
        $distance = ($score / 100) * $radius;
        $x = $centerX + ($distance * cos($angle));
        $y = $centerY + ($distance * sin($angle));
        $points[] = [
            'x' => $x, 
            'y' => $y, 
            'score' => $score, 
            'label' => $traits[$traitKey]['label'],
            'description' => $traits[$traitKey]['description'],
            'key' => $traitKey
        ];
    }
    
    // Create polygon path
    $polygonPath = '';
    foreach ($points as $point) {
        $polygonPath .= ($polygonPath ? ' L ' : 'M ') . round($point['x'], 2) . ',' . round($point['y'], 2);
    }
    $polygonPath .= ' Z';
    
    // Extract communication strengths
    $communicationStrengths = [];
    if (isset($data['communication_strengths']) && is_array($data['communication_strengths'])) {
        $communicationStrengths = $data['communication_strengths'];
    } else {
        // Build from available data
        if ($toneAnalysis) {
            $communicationStrengths[] = 'Tone: ' . strtolower($toneAnalysis);
        }
        if (isset($data['engagement_patterns']) && is_string($data['engagement_patterns'])) {
            $communicationStrengths[] = 'Engagement: ' . strtolower($data['engagement_patterns']);
        }
        if (isset($data['posting_frequency']) && is_string($data['posting_frequency'])) {
            $communicationStrengths[] = 'Frequency: ' . strtolower($data['posting_frequency']);
        }
    }
    
    // Overall assessment
    $overallAssessment = $data['overall_assessment'] ?? $data['assessment'] ?? $data['conclusion'] ?? '';
@endphp

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <!-- Header -->
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0; letter-spacing: -0.02em; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Personality & Communication Snapshot</h3>
        @if($confidence)
            <span style="background: #f1f5f9; color: #64748b; padding: 8px 16px; border-radius: 10px; font-size: 14px; font-weight: 500; display: inline-block; margin-top: 6px; border: 1px solid #e2e8f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>
    
    <!-- Introduction Text with highlighted tone -->
    @if($overview || $toneAnalysis)
        <p style="color: #64748b; line-height: 1.8; font-size: 16px; margin-bottom: 32px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
            @if($overview)
                {{ $overview }}
            @else
                Analysis of public communications suggests a balanced personality profile and effective communication style.
            @endif
            @if($toneAnalysis)
                Tone analysis indicates a <span style="color: #10b981; font-weight: 600;">{{ strtolower($toneAnalysis) }}</span> in professional interactions.
            @endif
        </p>
    @endif
    
    <!-- Radar Chart -->
    @php
        $isPdfExport = isset($GLOBALS['isPdfExport']) && $GLOBALS['isPdfExport'] === true;
        
        if ($isPdfExport) {
            // For PDF: Generate SVG and convert to base64 data URI
            $svg = '<?xml version="1.0" encoding="UTF-8"?>';
            $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">';
            
            // Grid circles
            for ($i = 1; $i <= 5; $i++) {
                $svg .= sprintf(
                    '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="2,2"/>',
                    $centerX, $centerY, ($i / 5) * $radius
                );
            }
            
            // Axis lines
            foreach ($angles as $angle) {
                $svg .= sprintf(
                    '<line x1="%s" y1="%s" x2="%s" y2="%s" stroke="#e5e7eb" stroke-width="1"/>',
                    $centerX, $centerY,
                    $centerX + ($radius * cos($angle)),
                    $centerY + ($radius * sin($angle))
                );
            }
            
            // Data polygon
            $pointsString = implode(' ', array_map(function($p) {
                return round($p['x'], 2) . ',' . round($p['y'], 2);
            }, $points));
            $svg .= sprintf(
                '<polygon points="%s" fill="rgba(59, 130, 246, 0.2)" stroke="#3b82f6" stroke-width="2"/>',
                htmlspecialchars($pointsString)
            );
            
            // Data points and labels
            foreach ($points as $index => $point) {
                $labelAngle = $angles[$index];
                $labelDistance = $radius + 35;
                $labelX = $centerX + ($labelDistance * cos($labelAngle));
                $labelY = $centerY + ($labelDistance * sin($labelAngle));
                $textAnchor = abs($labelX - $centerX) < 10 ? 'middle' : ($labelX > $centerX ? 'start' : 'end');
                
                // Point
                $svg .= sprintf(
                    '<circle cx="%s" cy="%s" r="6" fill="#1e40af" stroke="white" stroke-width="2"/>',
                    round($point['x'], 2), round($point['y'], 2)
                );
                
                // Label
                $svg .= sprintf(
                    '<text x="%s" y="%s" text-anchor="%s" fill="#374151" font-size="13" font-weight="600" font-family="Arial, sans-serif">%s</text>',
                    round($labelX, 2), round($labelY, 2), htmlspecialchars($textAnchor), htmlspecialchars($point['label'])
                );
            }
            
            $svg .= '</svg>';
            $svgDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
    @endphp
    
    <div class="radar-chart-container" style="display: {{ $isPdfExport ? 'block' : 'flex' }}; justify-content: center; margin: 40px 0; position: relative; width: 100%; overflow: hidden; text-align: center;">
        <div class="radar-chart-wrapper" style="position: relative; width: 100%; max-width: 400px; padding: 20px; margin: 0 auto; text-align: center;">
            @if($isPdfExport)
                <!-- For PDF: Use img tag with base64 SVG -->
                <img src="{{ $svgDataUri }}" alt="Personality & Communication Radar Chart" style="width: 100%; height: auto; max-width: 500px; margin: 0 auto; display: block;" />
            @else
                <!-- For Web: Use inline SVG with interactivity -->
                <svg class="radar-chart-svg" viewBox="0 0 500 500" style="width: 100%; height: auto; max-width: 500px; overflow: visible;">
                    <!-- Grid circles -->
                    @for($i = 1; $i <= 5; $i++)
                        <circle cx="{{ $centerX }}" cy="{{ $centerY }}" r="{{ ($i / 5) * $radius }}" 
                                fill="none" 
                                stroke="#e5e7eb" 
                                stroke-width="1" 
                                stroke-dasharray="2,2"/>
                    @endfor
                    
                    <!-- Axis lines -->
                    @foreach($angles as $angle)
                        <line x1="{{ $centerX }}" 
                              y1="{{ $centerY }}" 
                              x2="{{ $centerX + ($radius * cos($angle)) }}" 
                              y2="{{ $centerY + ($radius * sin($angle)) }}" 
                              stroke="#e5e7eb" 
                              stroke-width="1"/>
                    @endforeach
                    
                    <!-- Data polygon -->
                    <polygon points="@foreach($points as $p){{ round($p['x'], 2) }},{{ round($p['y'], 2) }} @endforeach" 
                             fill="rgba(59, 130, 246, 0.2)" 
                             stroke="#3b82f6" 
                             stroke-width="2"/>
                    
                    <!-- Data points and labels -->
                    @foreach($points as $index => $point)
                        <!-- Point with tooltip area -->
                        <g class="personality-point" data-trait="{{ $point['key'] }}" style="cursor: pointer;">
                            <circle cx="{{ round($point['x'], 2) }}" 
                                    cy="{{ round($point['y'], 2) }}" 
                                    r="6" 
                                    fill="#1e40af" 
                                    stroke="white" 
                                    stroke-width="2"/>
                            <!-- Invisible larger circle for easier hover -->
                            <circle cx="{{ round($point['x'], 2) }}" 
                                    cy="{{ round($point['y'], 2) }}" 
                                    r="12" 
                                    fill="transparent" 
                                    stroke="none"/>
                        </g>
                        
                        <!-- Label -->
                        @php
                            $labelAngle = $angles[$index];
                            $labelDistance = $radius + 35;
                            $labelX = $centerX + ($labelDistance * cos($labelAngle));
                            $labelY = $centerY + ($labelDistance * sin($labelAngle));
                            $textAnchor = abs($labelX - $centerX) < 10 ? 'middle' : ($labelX > $centerX ? 'start' : 'end');
                        @endphp
                        <text x="{{ round($labelX, 2) }}" 
                              y="{{ round($labelY, 2) }}" 
                              text-anchor="{{ $textAnchor }}" 
                              fill="#374151" 
                              font-size="13" 
                              font-weight="600"
                              class="personality-label radar-label-text"
                              data-trait="{{ $point['key'] }}"
                              style="cursor: pointer; pointer-events: all;">
                            {{ $point['label'] }}
                        </text>
                    @endforeach
                </svg>
            @endif
        </div>
    </div>
    
    <!-- Tooltip Element -->
    <div id="personality-tooltip" style="position: fixed; background: #1f2937; color: white; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 400; white-space: pre-line; width: 280px; z-index: 10000; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); line-height: 1.6; pointer-events: none; opacity: 0; transition: opacity 0.2s; text-align: left; max-width: 280px;"></div>
    
    <style>
        .personality-point circle:first-child {
            transition: r 0.2s ease, fill 0.2s ease;
        }
        .personality-point:hover circle:first-child {
            r: 8;
            fill: #3b82f6;
        }
        .personality-label {
            transition: fill 0.2s ease, font-size 0.2s ease;
        }
        .personality-label:hover {
            fill: #3b82f6;
            font-size: 14;
        }
        
        /* Mobile responsive styles for radar chart */
        @media (max-width: 768px) {
            .radar-chart-container {
                margin: 24px 0 !important;
                padding: 0 !important;
                overflow-x: auto !important;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: touch !important;
                justify-content: flex-start !important;
            }
            
            .radar-chart-wrapper {
                padding: 15px 20px !important;
                min-width: 420px !important;
                max-width: 100% !important;
            }
            
            .radar-chart-svg {
                max-width: 100% !important;
                width: 100% !important;
                height: auto !important;
            }
            
            .radar-label-text {
                font-size: 11px !important;
            }
            
            /* Add scrollbar styling */
            .radar-chart-container::-webkit-scrollbar {
                height: 6px !important;
            }
            
            .radar-chart-container::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
                border-radius: 3px !important;
            }
            
            .radar-chart-container::-webkit-scrollbar-thumb {
                background: #cbd5e1 !important;
                border-radius: 3px !important;
            }
        }
        
        @media (max-width: 480px) {
            .radar-chart-container {
                margin: 20px 0 !important;
                padding: 0 !important;
            }
            
            .radar-chart-wrapper {
                padding: 10px 15px !important;
                min-width: 380px !important;
            }
            
            .radar-label-text {
                font-size: 10px !important;
            }
            
            /* Scale down the entire chart slightly on very small screens */
            .radar-chart-svg {
                transform: scale(0.85);
                transform-origin: center;
            }
        }
    </style>
    
    <!-- Communication Strengths -->
    @if(count($communicationStrengths) > 0)
        <div style="margin-top: 32px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="margin-bottom: 12px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Communication Strengths:</h4>
            </div>
            <ul style="margin: 0; padding-left: 20px; color: #64748b; font-size: 16px; line-height: 1.8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                @foreach($communicationStrengths as $strength)
                    <li style="margin-bottom: 8px;">
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
    
    <!-- Overall Assessment -->
    @if($overallAssessment)
        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            <p style="color: #374151; line-height: 1.8; font-size: 16px; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                {{ $overallAssessment }}
            </p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltip = document.getElementById('personality-tooltip');
    const points = document.querySelectorAll('.personality-point');
    const labels = document.querySelectorAll('.personality-label');
    const traits = @json($traits);
    const svg = document.querySelector('svg');
    
    function showTooltip(event, key) {
        const trait = traits[key];
        if (!trait) return;
        
        // Same tooltip format for both points and labels
        const tooltipText = trait.label + '\n\n' + trait.description + '\n\nScore: ' + trait.score + '/100';
        tooltip.textContent = tooltipText;
        
        // Make tooltip visible to calculate dimensions
        tooltip.style.opacity = '0';
        tooltip.style.visibility = 'hidden';
        tooltip.style.display = 'block';
        
        // Get exact position from SVG coordinates or mouse position
        let x, y;
        
        if (event && event.target) {
            const target = event.target;
            
            if (target.tagName === 'circle' || (target.parentElement && target.parentElement.classList.contains('personality-point'))) {
                // For circle points, get the exact circle center position
                const circle = target.tagName === 'circle' ? target : target.querySelector('circle');
                    if (circle) {
                        const svgRect = svg.getBoundingClientRect();
                        // SVG uses viewBox (500x500), so scale factor is based on actual rendered size
                        const scaleX = svgRect.width / 500;
                        const scaleY = svgRect.height / 500;
                        
                        const cx = parseFloat(circle.getAttribute('cx'));
                        const cy = parseFloat(circle.getAttribute('cy'));
                        
                        x = svgRect.left + (cx * scaleX);
                        y = svgRect.top + (cy * scaleY);
                } else {
                    // Fallback to mouse position
                    x = event.clientX || event.pageX;
                    y = event.clientY || event.pageY;
                }
            } else if (target.tagName === 'text' || target.classList.contains('personality-label')) {
                // For text labels, use mouse position for consistency
                if (event.clientX && event.clientY) {
                    x = event.clientX;
                    y = event.clientY;
                } else {
                    const textRect = target.getBoundingClientRect();
                    x = textRect.left + (textRect.width / 2);
                    y = textRect.top + (textRect.height / 2);
                }
            } else {
                // Use mouse position as fallback
                x = event.clientX || event.pageX;
                y = event.clientY || event.pageY;
            }
        } else {
            // Fallback to mouse position
            x = event.clientX || event.pageX;
            y = event.clientY || event.pageY;
        }
        
        // Calculate tooltip dimensions
        const tooltipWidth = 280;
        const tooltipHeight = tooltip.offsetHeight || 150;
        
        // Position tooltip below the point/label (same positioning for both)
        let left = x - (tooltipWidth / 2);
        let top = y + 20;
        
        // Adjust if tooltip goes off screen horizontally
        if (left < 10) left = 10;
        if (left + tooltipWidth > window.innerWidth - 10) {
            left = window.innerWidth - tooltipWidth - 10;
        }
        
        // Adjust if tooltip goes off screen vertically
        if (top < 10) {
            top = y + 20;
        }
        if (top + tooltipHeight > window.innerHeight - 10) {
            top = window.innerHeight - tooltipHeight - 10;
        }
        
        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
        tooltip.style.visibility = 'visible';
        tooltip.style.opacity = '1';
    }
    
    function hideTooltip() {
        tooltip.style.opacity = '0';
        setTimeout(() => {
            tooltip.style.visibility = 'hidden';
        }, 200);
    }
    
    // Add hover handlers to labels only (no tooltip for points)
    labels.forEach(label => {
        const key = label.getAttribute('data-trait');
        label.style.cursor = 'help';
        
        label.addEventListener('mouseenter', function(e) {
            showTooltip(e, key);
        });
        
        label.addEventListener('mousemove', function(e) {
            if (tooltip.style.opacity === '1') {
                showTooltip(e, key);
            }
        });
        
        label.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
});
</script>

