@php
    $data = $analysis['work_ethic_indicators'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 75;
    
    // Extract scores for the 5 dimensions
    $dimensions = [
        'consistency' => [
            'score' => $data['consistency_score'] ?? $data['consistency'] ?? null,
            'description' => $data['consistency'] ?? $data['consistency_description'] ?? 'Assessment of posting consistency and activity patterns.',
            'icon' => '📅',
            'label' => 'Consistency'
        ],
        'follow_through' => [
            'score' => $data['follow_through_score'] ?? $data['follow_through'] ?? $data['followthrough'] ?? null,
            'description' => $data['follow_through'] ?? $data['follow_through_description'] ?? $data['followthrough'] ?? 'Evidence of completing tasks and following through on commitments.',
            'icon' => '✅',
            'label' => 'Follow-through'
        ],
        'collaboration' => [
            'score' => $data['collaboration_score'] ?? $data['collaboration'] ?? null,
            'description' => $data['collaboration'] ?? $data['collaboration_description'] ?? 'Indicators of teamwork and collaborative behavior.',
            'icon' => '🤝',
            'label' => 'Collaboration'
        ],
        'initiative' => [
            'score' => $data['initiative_score'] ?? $data['initiative'] ?? null,
            'description' => $data['initiative'] ?? $data['initiative_description'] ?? 'Signs of proactive behavior and self-directed action.',
            'icon' => '💡',
            'label' => 'Initiative'
        ],
        'productivity' => [
            'score' => $data['productivity_score'] ?? $data['productivity'] ?? $data['productivity_signals'] ?? null,
            'description' => $data['productivity'] ?? $data['productivity_signals'] ?? $data['productivity_description'] ?? 'Signs of productivity and professional activity.',
            'icon' => '⚡',
            'label' => 'Productivity'
        ]
    ];
    
    // Try to extract numeric scores from descriptions if scores aren't provided
    foreach ($dimensions as $key => &$dim) {
        if ($dim['score'] === null || !is_numeric($dim['score'])) {
            // Try to extract number from description
            if (preg_match('/\b(\d{1,2})\b/', $dim['description'], $matches)) {
                $dim['score'] = (int)$matches[1];
            } else {
                // Default score based on keywords
                $text = strtolower($dim['description']);
                if (strpos($text, 'strong') !== false || strpos($text, 'excellent') !== false || strpos($text, 'high') !== false) {
                    $dim['score'] = 80;
                } elseif (strpos($text, 'moderate') !== false || strpos($text, 'average') !== false) {
                    $dim['score'] = 50;
                } elseif (strpos($text, 'low') !== false || strpos($text, 'lacks') !== false || strpos($text, 'limited') !== false) {
                    $dim['score'] = 30;
                } else {
                    $dim['score'] = 50; // Default
                }
            }
        } else {
            $dim['score'] = (int)$dim['score'];
        }
        // Ensure score is between 0-100
        $dim['score'] = max(0, min(100, $dim['score']));
    }
    unset($dim);
    
    // Radar chart configuration
    $centerX = 250;
    $centerY = 250;
    $radius = 150;
    $numAxes = 5;
    $angleStep = (2 * M_PI) / $numAxes;
    
    // Calculate points for each dimension
    $points = [];
    $angles = [];
    
    foreach ($dimensions as $key => $dim) {
        $i = array_search($key, array_keys($dimensions));
        $angle = ($i * $angleStep) - (M_PI / 2); // Start from top
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
    
    // Create polygon path
    $polygonPath = '';
    foreach ($points as $point) {
        $polygonPath .= ($polygonPath ? ' L ' : 'M ') . round($point['x'], 2) . ',' . round($point['y'], 2);
    }
    $polygonPath .= ' Z';
@endphp

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Work Ethic Indicators</h3>
        @if($confidence)
            <span style="background: #f1f5f9; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>
    
    <!-- Introduction Text -->
    <p style="color: #64748b; line-height: 1.8; font-size: 14px; margin-bottom: 32px;">
        Based on analysis of online activities and communication patterns, the following work ethic indicators have been assessed across five key dimensions:
    </p>
    
    <!-- Radar Chart -->
    @php
        $isPdfExport = isset($GLOBALS['isPdfExport']) && $GLOBALS['isPdfExport'] === true;
        
        // Generate SVG for PDF (as base64 data URI) or inline SVG for web
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
                <img src="{{ $svgDataUri }}" alt="Work Ethic Radar Chart" style="width: 100%; height: auto; max-width: 500px; margin: 0 auto; display: block;" />
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
                        <g class="radar-point" data-dimension="{{ $point['key'] }}" style="cursor: pointer;">
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
                              class="radar-label radar-label-text"
                              data-dimension="{{ $point['key'] }}"
                              style="cursor: pointer; pointer-events: all;">
                            {{ $point['label'] }}
                        </text>
                    @endforeach
                </svg>
            @endif
        </div>
    </div>
    
    <!-- Dimension Cards -->
    <div class="dimension-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-top: 32px;">
        @foreach($dimensions as $key => $dim)
            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">
                    {{ $dim['label'] }}
                </h4>
                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">
                    {{ $dim['description'] }}
                </p>
            </div>
        @endforeach
    </div>
    
    <!-- Tooltip Element -->
    <div id="radar-tooltip" style="position: fixed; background: #1f2937; color: white; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 400; white-space: pre-line; width: 280px; z-index: 10000; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); line-height: 1.6; pointer-events: none; opacity: 0; transition: opacity 0.2s; text-align: left; max-width: 280px;"></div>
    
    <style>
        .radar-point circle:first-child {
            transition: r 0.2s ease, fill 0.2s ease;
        }
        .radar-point:hover circle:first-child {
            r: 8;
            fill: #3b82f6;
        }
        .radar-label {
            transition: fill 0.2s ease, font-size 0.2s ease;
        }
        .radar-label:hover {
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
            
            /* Dimension cards grid */
            .dimension-cards-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
            
            .dimension-cards-grid > div {
                padding: 16px !important;
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
            
            /* Dimension cards */
            .dimension-cards-grid {
                gap: 10px !important;
            }
            
            .dimension-cards-grid > div {
                padding: 12px !important;
            }
            
            .dimension-cards-grid h4 {
                font-size: 14px !important;
            }
            
            .dimension-cards-grid p {
                font-size: 12px !important;
            }
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltip = document.getElementById('radar-tooltip');
        const points = document.querySelectorAll('.radar-point');
        const labels = document.querySelectorAll('.radar-label');
        const dimensions = @json($dimensions);
        const svg = document.querySelector('svg');
        
        function showTooltip(event, key) {
            const dim = dimensions[key];
            if (!dim) return;
            
            // Same tooltip format for both points and labels
            const tooltipText = dim.label + '\n\n' + dim.description + '\n\nScore: ' + dim.score + '/100';
            tooltip.textContent = tooltipText;
            
            // Make tooltip visible to calculate dimensions
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
            tooltip.style.display = 'block';
            
            // Get exact position from SVG coordinates or mouse position
            let x, y;
            
            if (event && event.target) {
                const target = event.target;
                
                if (target.tagName === 'circle' || (target.parentElement && target.parentElement.classList.contains('radar-point'))) {
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
                } else if (target.tagName === 'text' || target.classList.contains('radar-label')) {
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
            const key = label.getAttribute('data-dimension');
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
    
    <!-- Additional Details -->
    @if(isset($data['overall_assessment']) || isset($data['evidence']))
        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            @if(isset($data['overall_assessment']) && is_string($data['overall_assessment']))
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Overall Assessment:</strong> 
                    <span style="color: #64748b; line-height: 1.6;">{{ $data['overall_assessment'] }}</span>
                </div>
            @endif
            
            @if(isset($data['evidence']) && is_array($data['evidence']) && count($data['evidence']) > 0)
                <div>
                    <strong style="color: #374151;">Evidence:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['evidence'] as $item)
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
        </div>
    @endif
</div>

