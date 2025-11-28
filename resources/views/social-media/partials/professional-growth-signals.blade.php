@php
    $data = $analysis['professional_growth_signals'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? 75;
    $overview = $data['overview'] ?? $data['summary'] ?? $data['description'] ?? '';
    
    // Extract indicators with slider values
    $indicators = [
        'learning_initiative' => [
            'label' => 'Learning Initiative',
            'value' => $data['learning_initiative_level'] ?? $data['learning_initiative'] ?? $data['adaptability'] ?? null,
            'description' => $data['learning_initiative'] ?? $data['adaptability'] ?? 'Evidence of active learning and skill development.',
            'color_scheme' => 'yellow-green' // Yellow to Green
        ],
        'skill_development' => [
            'label' => 'Skill Development',
            'value' => $data['skill_development_level'] ?? $data['skill_development'] ?? null,
            'description' => $data['skill_development'] ?? 'Evidence of skill development and learning.',
            'color_scheme' => 'blue-purple' // Light Blue to Purple
        ],
        'mentorship_activity' => [
            'label' => 'Mentorship Activity',
            'value' => $data['mentorship_activity_level'] ?? $data['mentorship'] ?? $data['knowledge_sharing'] ?? null,
            'description' => $data['mentorship_activity'] ?? $data['mentorship'] ?? $data['knowledge_sharing'] ?? 'Evidence of mentoring activities or seeking mentorship.',
            'color_scheme' => 'pink-red' // Pink to Red
        ]
    ];
    
    // Function to convert text value to slider position (unique name for this partial)
    if (!function_exists('growthSignals_getSliderValue')) {
        function growthSignals_getSliderValue($value) {
            if (is_numeric($value)) {
                return min(100, max(0, (int)$value));
            }
            
            $value = strtolower(trim($value ?? ''));
            
            if (strpos($value, 'low') !== false || strpos($value, 'limited') !== false || strpos($value, 'weak') !== false || strpos($value, 'no') !== false) {
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
    if (!function_exists('growthSignals_getSliderLabel')) {
        function growthSignals_getSliderLabel($value) {
            if ($value <= 30) return 'Low';
            if ($value <= 60) return 'Medium';
            return 'Strong';
        }
    }
    
    // Function to get slider gradient based on color scheme
    if (!function_exists('growthSignals_getSliderGradient')) {
        function growthSignals_getSliderGradient($value, $colorScheme) {
            if ($colorScheme === 'yellow-green') {
                // Yellow to Green gradient
                return [
                    'start' => '#fbbf24', // Yellow
                    'end' => '#10b981'    // Green
                ];
            } elseif ($colorScheme === 'blue-purple') {
                // Light Blue to Purple gradient
                return [
                    'start' => '#60a5fa', // Light Blue
                    'end' => '#8b5cf6'    // Purple
                ];
            } elseif ($colorScheme === 'pink-red') {
                // Pink to Red gradient
                return [
                    'start' => '#f472b6', // Pink
                    'end' => '#ef4444'    // Red
                ];
            }
            
            // Default theme colors
            return [
                'start' => '#667eea',
                'end' => '#764ba2'
            ];
        }
    }
    
    // Function to get slider color for handle
    if (!function_exists('growthSignals_getSliderHandleColor')) {
        function growthSignals_getSliderHandleColor($value, $colorScheme) {
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
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Professional Growth Signals</h3>
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
    
    <!-- Indicators with Sliders -->
    <div style="display: grid; gap: 24px;">
        @foreach($indicators as $key => $indicator)
            @php
                $sliderValue = growthSignals_getSliderValue($indicator['value']);
                $sliderLabel = growthSignals_getSliderLabel($sliderValue);
                $gradient = growthSignals_getSliderGradient($sliderValue, $indicator['color_scheme']);
                $handleColor = growthSignals_getSliderHandleColor($sliderValue, $indicator['color_scheme']);
            @endphp
            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 32px 0;">
                    {{ $indicator['label'] }}
                </h4>
                
                <!-- Slider -->
                <div style="margin-bottom: 12px;">
                    <div style="position: relative; height: 12px; background: linear-gradient(to right, #f1f5f9 0%, #e2e8f0 100%); border-radius: 8px; overflow: visible; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                        <!-- Slider track with beautiful gradient -->
                        <div style="position: absolute; left: 0; top: 0; height: 100%; width: {{ $sliderValue }}%; 
                            background: linear-gradient(90deg, {{ $gradient['start'] }} 0%, {{ $gradient['end'] }} 100%); 
                            border-radius: 8px; 
                            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                            box-shadow: 0 2px 8px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.2);"></div>
                        
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
    @if(isset($data['growth_trajectory']) || isset($data['indicators']))
        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            @if(isset($data['growth_trajectory']) && is_string($data['growth_trajectory']))
                <div style="margin-bottom: 16px;">
                    <strong style="color: #374151;">Growth Trajectory:</strong> 
                    <span style="color: #64748b; line-height: 1.6;">{{ $data['growth_trajectory'] }}</span>
                </div>
            @endif
            
            @if(isset($data['indicators']) && is_array($data['indicators']) && count($data['indicators']) > 0)
                <div>
                    <strong style="color: #374151;">Indicators:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['indicators'] as $indicator)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($indicator))
                                    {{ $indicator }}
                                @else
                                    {{ json_encode($indicator) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>

