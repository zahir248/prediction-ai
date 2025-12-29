@php
    $analysis = $analysis ?? $socialMediaAnalysis->ai_analysis;
    $analysisType = $analysis['analysis_type'] ?? 'professional';
@endphp

@if(isset($analysis['title']) && is_string($analysis['title']))
    <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">{{ $analysis['title'] }}</h2>
@endif

<!-- Executive Summary -->
@if(isset($analysis['executive_summary']) && is_string($analysis['executive_summary']))
    <div class="social-executive-summary" style="margin-bottom: 32px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;">Executive Summary & Risk Assessment</h3>
        <p style="color: rgba(255,255,255,0.95); line-height: 1.8; margin: 0; word-wrap: break-word; overflow-wrap: break-word;">{{ $analysis['executive_summary'] }}</p>
    </div>
@endif

<!-- Risk Assessment -->
@if(isset($analysis['risk_assessment']))
    <div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Risk Assessment</h3>
        
        @if(isset($analysis['risk_assessment']['overall_risk_level']) && is_string($analysis['risk_assessment']['overall_risk_level']))
            @php
                $riskColor = $analysis['risk_assessment']['overall_risk_level'] === 'High' ? '#ef4444' : 
                            ($analysis['risk_assessment']['overall_risk_level'] === 'Medium' ? '#f59e0b' : '#10b981');
            @endphp
            <div style="margin-bottom: 16px;">
                <strong style="color: #374151;">Overall Risk Level:</strong> 
                <span style="color: {{ $riskColor }}; font-weight: 600;">{{ $analysis['risk_assessment']['overall_risk_level'] }}</span>
            </div>
        @endif
        
        @if(isset($analysis['risk_assessment']['risk_factors']) && is_array($analysis['risk_assessment']['risk_factors']))
            <div style="margin-bottom: 16px;">
                <strong style="color: #374151;">Risk Factors:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach($analysis['risk_assessment']['risk_factors'] as $risk)
                        <li style="margin-bottom: 8px; color: #64748b; line-height: 1.6;">
                            @if(is_array($risk))
                                <strong>{{ $risk['risk'] ?? 'Risk' }}</strong>
                                @if(isset($risk['level'])) <span style="color: #ef4444;">({{ $risk['level'] }})</span>@endif
                                @if(isset($risk['description']))<br><span style="font-size: 13px;">{{ $risk['description'] }}</span>@endif
                            @else
                                {{ $risk }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($analysis['risk_assessment']['red_flags']) && is_array($analysis['risk_assessment']['red_flags']))
            <div style="margin-bottom: 16px; padding: 12px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px;">
                <strong style="color: #991b1b;">Red Flags:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach($analysis['risk_assessment']['red_flags'] as $flag)
                        <li style="margin-bottom: 4px; color: #991b1b;">
                            @if(is_string($flag))
                                {{ $flag }}
                            @else
                                {{ json_encode($flag) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($analysis['risk_assessment']['positive_indicators']) && is_array($analysis['risk_assessment']['positive_indicators']))
            <div style="padding: 12px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 6px;">
                <strong style="color: #166534;">Positive Indicators:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach($analysis['risk_assessment']['positive_indicators'] as $indicator)
                        <li style="margin-bottom: 4px; color: #166534;">
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

@if($analysisType === 'professional')
    <!-- Professional Footprint -->
    @if(isset($analysis['professional_footprint']))
        @include('social-media.partials.professional-footprint', ['analysis' => $analysis, 'socialMediaAnalysis' => $socialMediaAnalysis])
    @endif

    <!-- Work Ethic Indicators -->
    @if(isset($analysis['work_ethic_indicators']))
        @include('social-media.partials.work-ethic-indicators', ['analysis' => $analysis])
    @endif

    <!-- Cultural Fit Indicators -->
    @if(isset($analysis['cultural_fit_indicators']))
        @include('social-media.partials.cultural-fit-indicators', ['analysis' => $analysis])
    @endif

    <!-- Professional Growth Signals -->
    @if(isset($analysis['professional_growth_signals']))
        @include('social-media.partials.professional-growth-signals', ['analysis' => $analysis])
    @endif

    <!-- Personality & Communication -->
    @if(isset($analysis['personality_communication']))
        @include('social-media.partials.personality-communication', ['analysis' => $analysis])
    @endif

    <!-- Career Profile -->
    @if(isset($analysis['career_profile']))
        @include('social-media.partials.analysis-section', ['title' => 'Career Profile & Growth Signals', 'data' => $analysis['career_profile']])
    @endif
@elseif($analysisType === 'political')
    <!-- Political Profile -->
    @if(isset($analysis['political_profile']))
        @include('social-media.partials.analysis-section', ['title' => 'Political Profile', 'data' => $analysis['political_profile']])
    @endif

    <!-- Political Engagement Indicators -->
    @if(isset($analysis['political_engagement_indicators']))
        @include('social-media.partials.political-engagement-indicators', ['analysis' => $analysis])
    @endif

    <!-- Political Alignment Indicators -->
    @if(isset($analysis['political_alignment_indicators']))
        @include('social-media.partials.political-alignment-indicators', ['analysis' => $analysis])
    @endif

    <!-- Political Growth Signals -->
    @if(isset($analysis['political_growth_signals']))
        @include('social-media.partials.political-growth-signals', ['analysis' => $analysis])
    @endif

    <!-- Political Communication Style -->
    @if(isset($analysis['political_communication_style']))
        @include('social-media.partials.political-communication-style', ['analysis' => $analysis])
    @endif

    <!-- Political Career Profile -->
    @if(isset($analysis['political_career_profile']))
        @include('social-media.partials.analysis-section', ['title' => 'Political Career Profile', 'data' => $analysis['political_career_profile']])
    @endif
@endif

<!-- Activity Overview (shown for both types) -->
@if(isset($analysis['activity_overview']))
    @include('social-media.partials.activity-overview', ['analysis' => $analysis, 'socialMediaAnalysis' => $socialMediaAnalysis])
@endif

<!-- Overall Assessment -->
@if(isset($analysis['overall_assessment']) && is_string($analysis['overall_assessment']))
    <div class="social-overall-assessment" style="margin-bottom: 32px; padding: 24px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 2px solid #667eea; page-break-before: always; break-before: page;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Overall Assessment</h3>
        <p style="color: #374151; line-height: 1.8; margin: 0; word-wrap: break-word; overflow-wrap: break-word;">{{ $analysis['overall_assessment'] }}</p>
    </div>
@endif

<!-- Recommendations -->
@if(isset($analysis['recommendations']) && is_array($analysis['recommendations']))
    <div style="margin-bottom: 32px; padding: 24px; background: #f0fdf4; border-radius: 12px; border: 1px solid #86efac;">
        <h3 style="font-size: 18px; font-weight: 600; color: #166534; margin-bottom: 16px;">Recommendations</h3>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($analysis['recommendations'] as $rec)
                <li style="margin-bottom: 8px; color: #166534; line-height: 1.6;">
                    @if(is_string($rec))
                        {{ $rec }}
                    @else
                        {{ json_encode($rec) }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Metadata -->
<div style="margin-top: 32px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
    @if(isset($analysis['confidence_level']) && is_string($analysis['confidence_level']))
        <div style="margin-bottom: 4px;"><strong>Confidence Level:</strong> {{ $analysis['confidence_level'] }}</div>
    @endif
    @if(isset($socialMediaAnalysis) && $socialMediaAnalysis->created_at)
        <div style="margin-bottom: 4px;"><strong>Analysis Date:</strong> {{ $socialMediaAnalysis->created_at->format('M d, Y \a\t g:i A') }}</div>
    @endif
    @if(isset($analysis['data_quality']) && is_string($analysis['data_quality']))
        <div style="margin-bottom: 4px;"><strong>Data Quality:</strong> {{ $analysis['data_quality'] }}</div>
    @endif
    @if(isset($analysis['limitations']) && is_string($analysis['limitations']))
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;"><strong>Limitations:</strong> {{ $analysis['limitations'] }}</div>
    @endif
</div>
