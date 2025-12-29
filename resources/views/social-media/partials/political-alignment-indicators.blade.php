@php
    $data = $analysis['political_alignment_indicators'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 75;
    $overview = $data['overview'] ?? '';
    
    // Helper function to convert text/number to score
    if (!function_exists('politicalAlignment_getScore')) {
        function politicalAlignment_getScore($value) {
            if (is_numeric($value)) {
                return min(100, max(0, (int)$value));
            }
            $value = strtolower(trim($value ?? ''));
            if (strpos($value, 'low') !== false) return 25;
            if (strpos($value, 'medium') !== false || strpos($value, 'moderate') !== false) return 50;
            if (strpos($value, 'high') !== false || strpos($value, 'strong') !== false) return 75;
            return 50;
        }
    }
    
    // Extract scores for the dimensions
    $dimensions = [
        'ideological_alignment' => [
            'score' => politicalAlignment_getScore($data['ideological_alignment_level'] ?? $data['ideological_alignment'] ?? null),
            'description' => $data['ideological_alignment'] ?? 'Description of how the candidate\'s ideology aligns with specific political philosophies.',
            'label' => 'Ideological Alignment'
        ],
        'party_alignment' => [
            'score' => politicalAlignment_getScore($data['party_alignment_level'] ?? $data['party_alignment'] ?? null),
            'description' => $data['party_alignment'] ?? 'Description of the candidate\'s party affiliation and support indicators.',
            'label' => 'Party Alignment'
        ],
        'value_consistency' => [
            'score' => politicalAlignment_getScore($data['value_consistency_level'] ?? $data['value_consistency'] ?? null),
            'description' => $data['value_consistency'] ?? 'Description of the consistency of the candidate\'s political values and principles.',
            'label' => 'Value Consistency'
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

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0; letter-spacing: -0.02em; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Political Alignment Indicators</h3>
        @if($confidence)
            <span style="background: #f1f5f9; color: #64748b; padding: 8px 16px; border-radius: 10px; font-size: 14px; font-weight: 500; display: inline-block; margin-top: 6px; border: 1px solid #e2e8f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>
    
    @if($overview)
        <p style="color: #64748b; line-height: 1.8; font-size: 16px; margin-bottom: 32px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
            {{ $overview }}
        </p>
    @endif
    
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
                '<polygon points="%s" fill="rgba(139, 92, 246, 0.2)" stroke="#8b5cf6" stroke-width="2"/>',
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
                    '<circle cx="%s" cy="%s" r="6" fill="#7c3aed" stroke="white" stroke-width="2"/>',
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
                <img src="{{ $svgDataUri }}" alt="Political Alignment Radar Chart" style="width: 100%; height: auto; max-width: 500px; margin: 0 auto; display: block;" />
            @else
                <!-- For Web: Use inline SVG with interactivity -->
                <svg class="radar-chart-svg" viewBox="0 0 500 500" style="width: 100%; height: auto; max-width: 500px; overflow: visible;">
                    @for($i = 1; $i <= 5; $i++)
                        <circle cx="{{ $centerX }}" cy="{{ $centerY }}" r="{{ ($i / 5) * $radius }}" 
                                fill="none" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="2,2"/>
                    @endfor
                    
                    @foreach($angles as $angle)
                        <line x1="{{ $centerX }}" y1="{{ $centerY }}" 
                              x2="{{ $centerX + ($radius * cos($angle)) }}" 
                              y2="{{ $centerY + ($radius * sin($angle)) }}" 
                              stroke="#e5e7eb" stroke-width="1"/>
                    @endforeach
                    
                    <polygon points="@foreach($points as $p){{ round($p['x'], 2) }},{{ round($p['y'], 2) }} @endforeach" 
                             fill="rgba(139, 92, 246, 0.2)" stroke="#8b5cf6" stroke-width="2"/>
                    
                    @foreach($points as $index => $point)
                        <g class="radar-point" data-dimension="{{ $point['key'] }}" style="cursor: pointer;">
                            <circle cx="{{ round($point['x'], 2) }}" cy="{{ round($point['y'], 2) }}" 
                                    r="6" fill="#7c3aed" stroke="white" stroke-width="2"/>
                            <circle cx="{{ round($point['x'], 2) }}" cy="{{ round($point['y'], 2) }}" 
                                    r="12" fill="transparent" stroke="none"/>
                        </g>
                        
                        @php
                            $labelAngle = $angles[$index];
                            $labelDistance = $radius + 35;
                            $labelX = $centerX + ($labelDistance * cos($labelAngle));
                            $labelY = $centerY + ($labelDistance * sin($labelAngle));
                            $textAnchor = abs($labelX - $centerX) < 10 ? 'middle' : ($labelX > $centerX ? 'start' : 'end');
                        @endphp
                        <text x="{{ round($labelX, 2) }}" y="{{ round($labelY, 2) }}" 
                              text-anchor="{{ $textAnchor }}" fill="#374151" font-size="13" font-weight="600"
                              class="radar-label" data-dimension="{{ $point['key'] }}"
                              style="cursor: pointer; pointer-events: all;">
                            {{ $point['label'] }}
                        </text>
                    @endforeach
                </svg>
            @endif
        </div>
    </div>
    
    <!-- Tooltip Element -->
    <div id="political-alignment-tooltip" style="position: fixed; background: #1f2937; color: white; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 400; white-space: pre-line; width: 280px; z-index: 10000; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); line-height: 1.6; pointer-events: none; opacity: 0; transition: opacity 0.2s; text-align: left; max-width: 280px;"></div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltip = document.getElementById('political-alignment-tooltip');
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
    
    <!-- Dimension Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-top: 32px;">
        @foreach($dimensions as $key => $dim)
            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">
                    {{ $dim['label'] }}: {{ $dim['score'] }}/100
                </h4>
                <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ $dim['description'] }}
                </p>
            </div>
        @endforeach
    </div>
    
    @if(isset($data['overall_alignment']) || isset($data['concerns']) || isset($data['strengths']))
        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; font-size: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
            @if(isset($data['overall_alignment']) && is_string($data['overall_alignment']))
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Overall Alignment:</strong> 
                    <span style="color: #64748b; line-height: 1.6;">{{ $data['overall_alignment'] }}</span>
                </div>
            @endif
            
            @if(isset($data['strengths']) && is_array($data['strengths']) && count($data['strengths']) > 0)
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Strengths:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['strengths'] as $strength)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                {{ is_string($strength) ? $strength : json_encode($strength) }}
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
                                {{ is_string($concern) ? $concern : json_encode($concern) }}
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
            fill: #8b5cf6;
            font-size: 14;
        }
    @media (max-width: 768px) {
        .radar-chart-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .radar-chart-wrapper {
            min-width: 400px;
        }
    }
</style>

