@php
    $data = $analysis['cultural_fit_indicators'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 70;
    $overview = $data['overview'] ?? $data['summary'] ?? $data['description'] ?? '';
    
    // Extract indicators with slider values
    $indicators = [
        'value_alignment' => [
            'label' => 'Value Alignment',
            'value' => $data['value_alignment_level'] ?? $data['values_alignment'] ?? $data['value_alignment'] ?? null,
            'description' => $data['value_alignment'] ?? $data['values_alignment'] ?? 'Assessment of values and principles shown in content.',
            'color_scheme' => 'yellow-green' // Yellow to Green
        ],
        'teamwork_ethos' => [
            'label' => 'Teamwork Ethos',
            'value' => $data['teamwork_ethos_level'] ?? $data['team_collaboration'] ?? $data['teamwork'] ?? null,
            'description' => $data['teamwork_ethos'] ?? $data['team_collaboration'] ?? 'Indicators of team-oriented behavior and collaboration.',
            'color_scheme' => 'blue-purple' // Light Blue to Purple
        ],
        'innovation_mindset' => [
            'label' => 'Innovation Mindset',
            'value' => $data['innovation_mindset_level'] ?? $data['innovation'] ?? null,
            'description' => $data['innovation_mindset'] ?? $data['innovation'] ?? 'Attitude toward innovation and creative thinking.',
            'color_scheme' => 'pink-red' // Pink to Red
        ]
    ];
    
    // Function to convert text value to slider position (unique name for this partial)
    if (!function_exists('culturalFit_getSliderValue')) {
        function culturalFit_getSliderValue($value) {
            if (is_numeric($value)) {
                return min(100, max(0, (int)$value));
            }
            
            $value = strtolower(trim($value ?? ''));
            
            if (strpos($value, 'low') !== false || strpos($value, 'limited') !== false || strpos($value, 'weak') !== false) {
                return 20; // Low
            } elseif (strpos($value, 'medium') !== false || strpos($value, 'moderate') !== false || strpos($value, 'average') !== false) {
                return 50; // Medium
            } elseif (strpos($value, 'high') !== false || strpos($value, 'strong') !== false || strpos($value, 'excellent') !== false) {
                return 80; // High/Strong
            }
            
            return 50; // Default to medium
        }
    }
    
    // Function to get slider label (unique name for this partial)
    if (!function_exists('culturalFit_getSliderLabel')) {
        function culturalFit_getSliderLabel($value) {
            if ($value <= 30) return 'Low';
            if ($value <= 60) return 'Medium';
            return 'Strong';
        }
    }
    
    // Function to get slider gradient based on color scheme (same as Professional Growth Signals)
    if (!function_exists('culturalFit_getSliderGradient')) {
        function culturalFit_getSliderGradient($value, $colorScheme) {
            if ($colorScheme === 'yellow-green') {
                // Yellow to Green gradient
                return [
                    'start' => '#fbbf24', // Yellow
                    'mid' => '#f59e0b',   // Orange
                    'end' => '#10b981'     // Green
                ];
            } elseif ($colorScheme === 'blue-purple') {
                // Light Blue to Purple gradient
                return [
                    'start' => '#60a5fa', // Light Blue
                    'mid' => '#7c3aed',   // Medium Purple
                    'end' => '#8b5cf6'    // Purple
                ];
            } elseif ($colorScheme === 'pink-red') {
                // Pink to Red gradient
                return [
                    'start' => '#f472b6', // Pink
                    'mid' => '#f87171',   // Light Red
                    'end' => '#ef4444'    // Red
                ];
            }
            
            // Default theme colors
            return [
                'start' => '#667eea',
                'mid' => '#764ba2',
                'end' => '#5a67d8'
            ];
        }
    }
    
    // Function to get slider color for handle
    if (!function_exists('culturalFit_getSliderHandleColor')) {
        function culturalFit_getSliderHandleColor($value, $colorScheme) {
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
@endphp

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Cultural Fit Indicators</h3>
        @if($confidence)
            <span style="background: #f1f5f9; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>
    
    <!-- Overview Text -->
    @if($overview)
        <p style="color: #64748b; line-height: 1.8; font-size: 14px; margin-bottom: 32px;">
            {{ $overview }}
        </p>
    @endif
    
    @php
        // Prepare data for spider graph
        $dimensions = [
            'value_alignment' => [
                'score' => culturalFit_getSliderValue($indicators['value_alignment']['value']),
                'label' => 'Value Alignment',
                'description' => $indicators['value_alignment']['description']
            ],
            'teamwork_ethos' => [
                'score' => culturalFit_getSliderValue($indicators['teamwork_ethos']['value']),
                'label' => 'Teamwork Ethos',
                'description' => $indicators['teamwork_ethos']['description']
            ],
            'innovation_mindset' => [
                'score' => culturalFit_getSliderValue($indicators['innovation_mindset']['value']),
                'label' => 'Innovation Mindset',
                'description' => $indicators['innovation_mindset']['description']
            ]
        ];
        
        // Radar chart configuration
        $centerX = 250;
        $centerY = 250;
        $radius = 150;
        $numAxes = 3;
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
    @endphp
    
    <!-- Spider Graph -->
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
                '<polygon points="%s" fill="rgba(102, 126, 234, 0.2)" stroke="#667eea" stroke-width="2"/>',
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
                    '<circle cx="%s" cy="%s" r="6" fill="#667eea" stroke="white" stroke-width="2"/>',
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
                <img src="{{ $svgDataUri }}" alt="Cultural Fit Radar Chart" style="width: 100%; height: auto; max-width: 500px; margin: 0 auto; display: block;" />
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
                             fill="rgba(102, 126, 234, 0.2)" 
                             stroke="#667eea" 
                             stroke-width="2"/>
                    
                    <!-- Data points and labels -->
                    @foreach($points as $index => $point)
                        <g class="radar-point" data-dimension="{{ $point['key'] }}" style="cursor: pointer;">
                            <circle cx="{{ round($point['x'], 2) }}" 
                                    cy="{{ round($point['y'], 2) }}" 
                                    r="6" 
                                    fill="#667eea" 
                                    stroke="white" 
                                    stroke-width="2"/>
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
    
    <!-- Tooltip Element -->
    <div id="cultural-fit-tooltip" style="position: fixed; background: #1f2937; color: white; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 400; white-space: pre-line; width: 280px; z-index: 10000; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); line-height: 1.6; pointer-events: none; opacity: 0; transition: opacity 0.2s; text-align: left; max-width: 280px;"></div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltip = document.getElementById('cultural-fit-tooltip');
        const labels = document.querySelectorAll('.radar-label');
        const dimensions = @json($dimensions);
        const svg = document.querySelector('.radar-chart-svg');
        
        function showTooltip(event, key) {
            const dim = dimensions[key];
            if (!dim) return;
            
            const tooltipText = dim.label + '\n\n' + dim.description + '\n\nScore: ' + dim.score + '/100';
            tooltip.textContent = tooltipText;
            
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
            tooltip.style.display = 'block';
            
            let x, y;
            if (event && event.target) {
                const target = event.target;
                if (target.tagName === 'text' || target.classList.contains('radar-label')) {
                    if (event.clientX && event.clientY) {
                        x = event.clientX;
                        y = event.clientY;
                    } else {
                        const textRect = target.getBoundingClientRect();
                        x = textRect.left + (textRect.width / 2);
                        y = textRect.top + (textRect.height / 2);
                    }
                } else {
                    x = event.clientX || event.pageX;
                    y = event.clientY || event.pageY;
                }
            } else {
                x = event.clientX || event.pageX;
                y = event.clientY || event.pageY;
            }
            
            const tooltipWidth = 280;
            const tooltipHeight = tooltip.offsetHeight || 150;
            let left = x - (tooltipWidth / 2);
            let top = y + 20;
            
            if (left < 10) left = 10;
            if (left + tooltipWidth > window.innerWidth - 10) {
                left = window.innerWidth - tooltipWidth - 10;
            }
            if (top < 10) top = y + 20;
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
    
    <!-- Indicators with Sliders -->
    <div style="display: grid; gap: 24px;">
        @foreach($indicators as $key => $indicator)
            @php
                $sliderValue = culturalFit_getSliderValue($indicator['value']);
                $sliderLabel = culturalFit_getSliderLabel($sliderValue);
                $gradient = culturalFit_getSliderGradient($sliderValue, $indicator['color_scheme']);
                $handleColor = culturalFit_getSliderHandleColor($sliderValue, $indicator['color_scheme']);
            @endphp
            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 32px 0;">
                    {{ $indicator['label'] }}
                </h4>
                
                <!-- Slider -->
                <div style="margin-bottom: 12px;">
                    <div style="position: relative; height: 12px; background: linear-gradient(to right, #f1f5f9 0%, #e2e8f0 100%); border-radius: 8px; overflow: visible; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                        <!-- Slider track with beautiful gradient (theme colors) -->
                        <div style="position: absolute; left: 0; top: 0; height: 100%; width: {{ $sliderValue }}%; 
                            background: linear-gradient(90deg, 
                                {{ $gradient['start'] }} 0%, 
                                {{ $gradient['mid'] }} 50%,
                                {{ $gradient['end'] }} 100%); 
                            border-radius: 8px; 
                            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25), inset 0 1px 0 rgba(255,255,255,0.2);"></div>
                        
                        <!-- Slider handle with glow effect -->
                        <div style="position: absolute; left: {{ $sliderValue }}%; top: 50%; transform: translate(-50%, -50%); 
                            width: 24px; height: 24px; 
                            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); 
                            border: 3px solid {{ $handleColor }}; 
                            border-radius: 50%; 
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15), 0 0 0 4px rgba(0,0,0,0.05), 
                                        inset 0 2px 4px rgba(255,255,255,0.8); 
                            z-index: 10;
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        
                        <!-- Labels above slider track -->
                        <div style="position: absolute; left: 0; top: -24px; width: 100%; display: flex; justify-content: space-between; align-items: center; pointer-events: none;">
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Low</span>
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Medium</span>
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Strong</span>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">
                    {{ $indicator['description'] }}
                </p>
            </div>
        @endforeach
    </div>
    
    <!-- Additional Details -->
    @if(isset($data['overall_fit']) || isset($data['concerns']) || isset($data['strengths']))
        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            @if(isset($data['overall_fit']) && is_string($data['overall_fit']))
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Overall Fit:</strong> 
                    <span style="color: #64748b; line-height: 1.6;">{{ $data['overall_fit'] }}</span>
                </div>
            @endif
            
            @if(isset($data['strengths']) && is_array($data['strengths']) && count($data['strengths']) > 0)
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Strengths:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['strengths'] as $strength)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($strength))
                                    {{ $strength }}
                                @else
                                    {{ json_encode($strength) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(isset($data['concerns']) && is_array($data['concerns']) && count($data['concerns']) > 0)
                <div>
                    <strong style="color: #374151;">Concerns:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['concerns'] as $concern)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($concern))
                                    {{ $concern }}
                                @else
                                    {{ json_encode($concern) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .radar-label {
        transition: fill 0.2s ease, font-size 0.2s ease;
    }
    .radar-label:hover {
        fill: #667eea;
        font-size: 14;
    }
</style>

