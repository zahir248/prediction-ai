@extends('layouts.app')

@section('content')
<div class="social-show-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div class="social-show-wrapper" style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="social-show-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $socialMediaAnalysis->username }}</h1>
                        <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                            Analyzed on {{ $socialMediaAnalysis->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="social-header-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('social-media.history') }}" class="social-action-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            ← Back
                        </a>
                        @if($socialMediaAnalysis->status === 'completed')
                            <button onclick="confirmExport({{ $socialMediaAnalysis->id }}, {{ json_encode($socialMediaAnalysis->username) }})" 
                                    class="social-action-btn"
                                    style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);"
                                    onmouseover="this.style.opacity='0.9';"
                                    onmouseout="this.style.opacity='1';">
                                Export PDF
                            </button>
                        @endif
                        @if($socialMediaAnalysis->platform_data)
                            <button onclick="confirmReAnalyze({{ $socialMediaAnalysis->id }}, {{ json_encode($socialMediaAnalysis->username) }})" 
                                    class="social-action-btn"
                                    style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                                    onmouseover="this.style.opacity='0.9';"
                                    onmouseout="this.style.opacity='1';">
                                Re-analyze
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Status Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Analysis Status</h2>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        @php
                            $analysisType = $socialMediaAnalysis->ai_analysis['analysis_type'] ?? 'professional';
                        @endphp
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; 
                            @if($analysisType === 'political') 
                                background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
                            @else 
                                background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
                            @endif">
                            {{ ucfirst($analysisType) }} Analysis
                        </span>
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; 
                            @if($socialMediaAnalysis->status === 'completed') 
                                background: #dcfce7; color: #166534;
                            @elseif($socialMediaAnalysis->status === 'processing') 
                                background: #fef3c7; color: #92400e;
                            @elseif($socialMediaAnalysis->status === 'failed') 
                                background: #fee2e2; color: #991b1b;
                            @else 
                                background: #e2e8f0; color: #475569;
                            @endif">
                            {{ ucfirst($socialMediaAnalysis->status) }}
                        </span>
                        @if($socialMediaAnalysis->platform_count > 0 && is_array($socialMediaAnalysis->found_platforms) && count($socialMediaAnalysis->found_platforms) > 0)
                            <span style="color: #64748b; font-size: 13px;">
                                Platforms: {{ $socialMediaAnalysis->platform_count }} ({{ implode(', ', array_map('ucfirst', $socialMediaAnalysis->found_platforms)) }})
                            </span>
                        @endif
                        @if($socialMediaAnalysis->processing_time)
                            <span style="color: #64748b; font-size: 13px;">
                                Processing Time: {{ number_format($socialMediaAnalysis->processing_time, 2) }}s
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Platform Data Section -->
            @if($socialMediaAnalysis->platform_data && is_array($socialMediaAnalysis->platform_data))
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Platform Data</h2>
                    <div class="social-platform-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                        @php
                            $platforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
                            $platformNames = [
                                'facebook' => 'Facebook',
                                'instagram' => 'Instagram',
                                'tiktok' => 'TikTok',
                                'twitter' => 'X (Twitter)'
                            ];
                        @endphp
                        @foreach($platforms as $platform)
                            @php
                                $platformInfo = $socialMediaAnalysis->platform_data[$platform] ?? null;
                                $isFound = is_array($platformInfo) && isset($platformInfo['found']) && $platformInfo['found'] && isset($platformInfo['data']);
                            @endphp
                            <div @if($isFound) onclick="showPlatformModal('{{ $platform }}')" @endif style="background: {{ $isFound ? '#f0fdf4' : '#fef2f2' }}; padding: 12px; border-radius: 8px; border: 1px solid {{ $isFound ? '#86efac' : '#fecaca' }}; display: flex; align-items: center; gap: 8px; @if($isFound) cursor: pointer; transition: all 0.2s ease; @endif" @if($isFound) onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';" @endif>
                                <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    @if($platform === 'facebook')
                                        <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #1877F2;">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    @elseif($platform === 'instagram')
                                        <svg viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                            <defs>
                                                <linearGradient id="instagram-gradient-show" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" />
                                                    <stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" />
                                                </linearGradient>
                                            </defs>
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-show)"/>
                                        </svg>
                                    @elseif($platform === 'tiktok')
                                        <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;">
                                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                        </svg>
                                    @elseif($platform === 'twitter')
                                        <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $platformNames[$platform] ?? ucfirst($platform) }}</div>
                                    <div class="status-text" style="color: #64748b; font-size: 11px;">
                                        @if($isFound)
                                            Found - Click to view
                                        @else
                                            Not Found
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Store platform data in JavaScript -->
                <script>
                    window.platformDataStore = @json($socialMediaAnalysis->platform_data ?? []);
                </script>
            @endif

            @if($socialMediaAnalysis->status === 'completed' && $socialMediaAnalysis->ai_analysis)
                <!-- AI Analysis Results -->
                @php
                    $analysis = $socialMediaAnalysis->ai_analysis;
                @endphp
                
                <div style="margin-bottom: 32px;">
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

                    @php
                        $analysisType = $socialMediaAnalysis->ai_analysis['analysis_type'] ?? 'professional';
                    @endphp

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
                        <div class="social-overall-assessment" style="margin-bottom: 32px; padding: 24px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 2px solid #667eea;">
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
                        @if(isset($analysis['analysis_date']) && is_string($analysis['analysis_date']))
                            <div style="margin-bottom: 4px;"><strong>Analysis Date:</strong> {{ $analysis['analysis_date'] }}</div>
                        @endif
                        @if(isset($analysis['data_quality']) && is_string($analysis['data_quality']))
                            <div style="margin-bottom: 4px;"><strong>Data Quality:</strong> {{ $analysis['data_quality'] }}</div>
                        @endif
                        @if(isset($analysis['limitations']) && is_string($analysis['limitations']))
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;"><strong>Limitations:</strong> {{ $analysis['limitations'] }}</div>
                        @endif
                    </div>
                </div>
            @elseif($socialMediaAnalysis->status === 'processing')
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 48px; margin-bottom: 16px;">⏳</div>
                    <h2 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Analysis in Progress</h2>
                    <p style="color: #64748b; font-size: 14px;">Your analysis is being processed. Please check back soon.</p>
                </div>
            @elseif($socialMediaAnalysis->status === 'failed')
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 48px; margin-bottom: 16px;">❌</div>
                    <h2 style="font-size: 20px; font-weight: 600; color: #ef4444; margin-bottom: 12px;">Analysis Failed</h2>
                    <p style="color: #64748b; font-size: 14px;">This analysis could not be completed. You can still view the platform data above or try re-analyzing.</p>
                </div>
            @else
                <!-- No AI Analysis Yet - Show message but allow viewing platform data -->
                @if($socialMediaAnalysis->platform_data)
                    <div style="margin-bottom: 32px; padding: 24px; background: #fef3c7; border-radius: 12px; border: 1px solid #fde68a;">
                        <div style="display: flex; align-items: start; gap: 16px;">
                            <div style="font-size: 32px; flex-shrink: 0;">📊</div>
                            <div style="flex: 1;">
                                <h3 style="font-size: 18px; font-weight: 600; color: #92400e; margin-bottom: 8px;">Platform Data Available</h3>
                                <p style="color: #78350f; font-size: 14px; margin-bottom: 16px; line-height: 1.6;">
                                    Platform search has been completed, but AI analysis has not been performed yet. You can view the platform data above or start an analysis using the "Re-analyze" button.
                                </p>
                                @if($socialMediaAnalysis->platform_data)
                                    <button onclick="confirmReAnalyze({{ $socialMediaAnalysis->id }}, {{ json_encode($socialMediaAnalysis->username) }})" 
                                            style="padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                                            onmouseover="this.style.opacity='0.9';"
                                            onmouseout="this.style.opacity='1';">
                                        Start Analysis
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
                        <h2 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">No Data Available</h2>
                        <p style="color: #64748b; font-size: 14px;">No platform data or analysis available for this record.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

 <!-- Export Confirmation Modal -->
 <div id="exportModal" class="social-export-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
     <div class="social-export-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
         <div style="margin-bottom: 24px;">
             <span style="font-size: 48px; color: #10b981;">📄</span>
         </div>
         <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
         <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this social media analysis as a PDF report?</p>
         <p id="exportUsername" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
         <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all analysis details and AI insights.</p>
         
         <div style="display: flex; gap: 16px; justify-content: center;">
             <button onclick="closeExportModal()" 
                     style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                 Cancel
             </button>
             <button id="confirmExportBtn" 
                     style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                 Export PDF
             </button>
         </div>
     </div>
 </div>

 <!-- Platform Modal -->
 <div id="platformModal" class="social-platform-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
     <div onclick="event.stopPropagation();" class="social-platform-modal-content" style="background: white; border-radius: 16px; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3); margin: auto;">
         <div style="padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
             <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">Platform Details</h2>
             <button onclick="closePlatformModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
         </div>
         <div id="platformModalContent" style="flex: 1; overflow-y: auto; padding: 24px;">
             <!-- Content will be loaded here -->
         </div>
     </div>
 </div>

 <!-- Re-analyze Confirmation Modal -->
 <div id="reAnalyzeModal" class="social-reanalyze-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
    <div onclick="event.stopPropagation();" class="social-reanalyze-modal-content" style="background: white; border-radius: 16px; max-width: 600px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3); margin: auto;">
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">Re-analyze Profile</h2>
            <button onclick="closeReAnalyzeModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
        </div>
        <div id="reAnalyzeModalContent" style="flex: 1; overflow-y: auto; padding: 24px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
 </div>

 <script>
 // Export modal functions
 let currentExportId = null;

 function confirmExport(analysisId, username) {
     currentExportId = analysisId;
     document.getElementById('exportUsername').textContent = username;
     document.getElementById('exportModal').style.display = 'flex';
 }

 function closeExportModal() {
     document.getElementById('exportModal').style.display = 'none';
     currentExportId = null;
 }

 function exportAnalysis() {
     if (!currentExportId) return;
     
     // Redirect to the export route
     window.location.href = '{{ url("/social-media") }}/' + currentExportId + '/export';
 }

 // Set up the confirm export button
 document.getElementById('confirmExportBtn').onclick = exportAnalysis;

 // Close export modal when clicking outside
 document.getElementById('exportModal').onclick = function(e) {
     if (e.target === this) {
         closeExportModal();
     }
 };

 // Re-analyze modal functions
 let currentReAnalyzeId = null;

 function confirmReAnalyze(analysisId, username) {
     currentReAnalyzeId = analysisId;
     document.getElementById('reAnalyzeModal').style.display = 'flex';
     document.body.style.overflow = 'hidden';
     
     // Show platform selection UI
     showReAnalyzePlatformSelection();
 }

 function showReAnalyzePlatformSelection() {
     const modalContent = document.getElementById('reAnalyzeModalContent');
     if (!modalContent || !window.platformDataStore) {
         console.error('Modal content or platform data store not found');
         return;
     }
     
     // Get available platforms (only those that were found and have data)
     const availablePlatforms = [];
    const platformNames = {
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'twitter': 'X (Twitter)'
    };
     
     Object.keys(window.platformDataStore).forEach(platform => {
         const platformInfo = window.platformDataStore[platform];
         if (platformInfo && platformInfo.found && platformInfo.data) {
             availablePlatforms.push({
                 key: platform,
                 name: platformNames[platform] || platform.charAt(0).toUpperCase() + platform.slice(1)
             });
         }
     });
     
     // If no platforms available, show error
     if (availablePlatforms.length === 0) {
         modalContent.innerHTML = `
             <div style="text-align: center; padding: 40px 20px;">
                 <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                 <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">No Platforms Available</h3>
                 <p style="color: #64748b; margin-bottom: 24px;">No platform data found to analyze. Please search for platforms first.</p>
                 <button onclick="closeReAnalyzeModal()" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;">Close</button>
             </div>
         `;
         return;
     }
     
     // Initialize selected platforms - all unchecked by default
     window.reAnalyzeSelectedPlatforms = {};
     availablePlatforms.forEach(platform => {
         window.reAnalyzeSelectedPlatforms[platform.key] = false;
     });
     
    // Initialize analysis type (default to professional)
    window.reAnalyzeAnalysisType = 'professional';
    
    // Build platform selection UI
    let html = `
        <div style="padding: 0;">
            <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; text-align: center;">Re-analyze Profile</h3>
            <p style="color: #64748b; margin-bottom: 24px; text-align: center; font-size: 14px;">Select analysis type and platforms to include</p>
            
            <!-- Analysis Type Selection -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 15px;">Analysis Type</label>
                <div style="display: flex; gap: 12px;">
                    <div style="flex: 1; padding: 16px; background: #ffffff; border: 2px solid #667eea; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                         onclick="selectReAnalyzeType('professional')" 
                         id="reAnalyzeTypeProfessional"
                         onmouseover="if(window.reAnalyzeAnalysisType !== 'professional') this.style.borderColor='#9ca3af';" 
                         onmouseout="if(window.reAnalyzeAnalysisType !== 'professional') this.style.borderColor='#e2e8f0';">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                            <input type="radio" name="reAnalyzeAnalysisType" value="professional" checked onchange="selectReAnalyzeType('professional')" 
                                   style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 4px;">Professional</div>
                                <div style="font-size: 13px; color: #64748b;">For recruitment and hiring evaluation</div>
                            </div>
                        </label>
                    </div>
                    <div style="flex: 1; padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                         onclick="selectReAnalyzeType('political')" 
                         id="reAnalyzeTypePolitical"
                         onmouseover="if(window.reAnalyzeAnalysisType !== 'political') this.style.borderColor='#9ca3af';" 
                         onmouseout="if(window.reAnalyzeAnalysisType !== 'political') this.style.borderColor='#e2e8f0';">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                            <input type="radio" name="reAnalyzeAnalysisType" value="political" onchange="selectReAnalyzeType('political')" 
                                   style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 4px;">Political</div>
                                <div style="font-size: 13px; color: #64748b;">For political profile and campaign analysis</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Platform Selection -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 15px;">Select Platforms</label>
                <div style="display: flex; flex-direction: column; gap: 16px;">
    `;
     
     // Add "Select All" option
     html += `
         <div style="padding: 16px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
              onclick="toggleReAnalyzeSelectAll()" 
              onmouseover="this.style.borderColor='#667eea'; this.style.background='#f0f4ff';" 
              onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
             <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                 <input type="checkbox" id="reAnalyzeSelectAllCheckbox" onchange="toggleReAnalyzeSelectAll()" 
                        style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                 <span style="font-weight: 600; color: #1e293b; font-size: 15px;">Select All Platforms</span>
             </label>
         </div>
     `;
     
     // Add individual platform checkboxes
     availablePlatforms.forEach(platform => {
         let platformIconSVG = '';
         if (platform.key === 'facebook') {
             platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
         } else if (platform.key === 'instagram') {
             platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px;"><defs><linearGradient id="instagram-gradient-reanalyze" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-reanalyze)"/></svg>';
         } else if (platform.key === 'tiktok') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>';
        } else if (platform.key === 'twitter') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
        }
         
         html += `
             <div style="padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                  onclick="toggleReAnalyzePlatform('${platform.key}')" 
                  onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.1)';" 
                  onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                 <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                     <input type="checkbox" id="reAnalyze_platform_${platform.key}" onchange="toggleReAnalyzePlatform('${platform.key}')" 
                            style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                     <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">${platformIconSVG}</div>
                     <span style="font-weight: 600; color: #1e293b; font-size: 15px; flex: 1;">${platform.name}</span>
                 </label>
             </div>
         `;
     });
     
    html += `
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px;">
                 <button onclick="closeReAnalyzeModal()" 
                         style="padding: 12px 24px; background: transparent; color: #64748b; border: 2px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                         onmouseover="this.style.borderColor='#9ca3af'; this.style.color='#374151';"
                         onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#64748b';">
                     Cancel
                 </button>
                 <button onclick="proceedWithReAnalyze()" 
                         style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                         onmouseover="this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)';"
                         onmouseout="this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';">
                     Start Re-analysis
                 </button>
             </div>
         </div>
     `;
     
     modalContent.innerHTML = html;
 }

 function toggleReAnalyzeSelectAll() {
     const selectAllCheckbox = document.getElementById('reAnalyzeSelectAllCheckbox');
     const isChecked = selectAllCheckbox.checked;
     
     // Update all platform checkboxes
     Object.keys(window.reAnalyzeSelectedPlatforms || {}).forEach(platform => {
         window.reAnalyzeSelectedPlatforms[platform] = isChecked;
         const checkbox = document.getElementById(`reAnalyze_platform_${platform}`);
         if (checkbox) {
             checkbox.checked = isChecked;
         }
     });
 }

 function toggleReAnalyzePlatform(platform) {
     const checkbox = document.getElementById(`reAnalyze_platform_${platform}`);
     if (checkbox) {
         window.reAnalyzeSelectedPlatforms[platform] = checkbox.checked;
         
         // Update "Select All" checkbox based on individual selections
         const selectAllCheckbox = document.getElementById('reAnalyzeSelectAllCheckbox');
         if (selectAllCheckbox) {
             const allSelected = Object.values(window.reAnalyzeSelectedPlatforms).every(selected => selected);
             selectAllCheckbox.checked = allSelected;
         }
     }
 }

function selectReAnalyzeType(type) {
    window.reAnalyzeAnalysisType = type;
    
    // Update UI
    const professionalDiv = document.getElementById('reAnalyzeTypeProfessional');
    const politicalDiv = document.getElementById('reAnalyzeTypePolitical');
    const professionalRadio = document.querySelector('input[name="reAnalyzeAnalysisType"][value="professional"]');
    const politicalRadio = document.querySelector('input[name="reAnalyzeAnalysisType"][value="political"]');
    
    if (type === 'professional') {
        professionalDiv.style.borderColor = '#667eea';
        professionalDiv.style.background = '#f0f4ff';
        politicalDiv.style.borderColor = '#e2e8f0';
        politicalDiv.style.background = '#ffffff';
        if (professionalRadio) professionalRadio.checked = true;
        if (politicalRadio) politicalRadio.checked = false;
    } else {
        politicalDiv.style.borderColor = '#667eea';
        politicalDiv.style.background = '#f0f4ff';
        professionalDiv.style.borderColor = '#e2e8f0';
        professionalDiv.style.background = '#ffffff';
        if (politicalRadio) politicalRadio.checked = true;
        if (professionalRadio) professionalRadio.checked = false;
    }
}

function proceedWithReAnalyze() {
    // Check if at least one platform is selected
    const hasSelection = Object.values(window.reAnalyzeSelectedPlatforms || {}).some(selected => selected);
    if (!hasSelection) {
        alert('Please select at least one platform to analyze.');
        return;
    }
    
    // Get selected platforms
    const selectedPlatforms = Object.keys(window.reAnalyzeSelectedPlatforms || {}).filter(
        platform => window.reAnalyzeSelectedPlatforms[platform]
    );
    
    // Get analysis type
    const analysisType = window.reAnalyzeAnalysisType || 'professional';
    
    // Start re-analysis with selected platforms and analysis type
    reAnalyze(selectedPlatforms, analysisType);
}

function closeReAnalyzeModal() {
    document.getElementById('reAnalyzeModal').style.display = 'none';
    document.body.style.overflow = '';
    currentReAnalyzeId = null;
    window.reAnalyzeSelectedPlatforms = {};
    window.reAnalyzeAnalysisType = 'professional';
}

async function reAnalyze(selectedPlatforms = null, analysisType = 'professional') {
    if (!currentReAnalyzeId) return;
    
    // Update modal content to show loading
    const modalContent = document.getElementById('reAnalyzeModalContent');
    if (modalContent) {
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔄</div>
                <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Re-analyzing...</h3>
                <p style="color: #64748b; margin-bottom: 24px;">Please wait while we analyze the selected platforms.</p>
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
        `;
    }

    try {
        const response = await fetch(`{{ url('social-media') }}/${currentReAnalyzeId}/re-analyze`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                selected_platforms: selectedPlatforms || null,
                analysis_type: analysisType || 'professional'
            })
        });

        const result = await response.json();

        if (result.success && result.analysis_id) {
            // Close modal
            closeReAnalyzeModal();
            // Show success message
            alert('Re-analysis completed successfully! Redirecting to the new analysis...');
            // Redirect to the new analysis
            window.location.href = `{{ url('social-media') }}/${result.analysis_id}`;
        } else {
            alert('Re-analysis failed: ' + (result.error || 'Unknown error'));
            // Re-enable button
            const btn = document.getElementById('confirmReAnalyzeBtn');
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Start Re-analysis';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }
    } catch (error) {
        console.error('Re-analysis error:', error);
        alert('An error occurred during re-analysis. Please try again.');
        // Re-enable button
        const btn = document.getElementById('confirmReAnalyzeBtn');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Start Re-analysis';
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    }
}

// Close modal when clicking outside
document.getElementById('reAnalyzeModal').onclick = function(e) {
    if (e.target === this) {
        closeReAnalyzeModal();
    }
};

// Close platform modal when clicking outside
document.getElementById('platformModal').onclick = function(e) {
    if (e.target === this) {
        closePlatformModal();
    }
};

 // Close modals with Escape key
 document.addEventListener('keydown', function(e) {
     if (e.key === 'Escape') {
         closeExportModal();
         closeReAnalyzeModal();
         closePlatformModal();
     }
 });

 // Platform Modal Functions
 function showPlatformModal(platform) {
     // Get data from the global store
     if (!window.platformDataStore) {
         console.error('Platform data store not found');
         alert('Platform data not available.');
         return;
     }
     
     const platformInfo = window.platformDataStore[platform];
     if (!platformInfo || !platformInfo.found || !platformInfo.data) {
         console.error('Platform data not found for:', platform);
         alert('No data available for this platform.');
         return;
     }
     
     const data = platformInfo.data;
     if (!data) {
         alert('No data available for this platform.');
         return;
     }
     
     const modal = document.getElementById('platformModal');
     const modalContent = document.getElementById('platformModalContent');
     if (!modal || !modalContent) {
         console.error('Modal elements not found');
         return;
     }
     
    const platformNames = {
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'twitter': 'X (Twitter)'
    };
     
     const platformName = platformNames[platform] || platform.charAt(0).toUpperCase() + platform.slice(1);
     
     // Generate platform card HTML
     let html = generatePlatformCardHTML(platformName, platform, data);
     
     modalContent.innerHTML = html;
     modal.style.display = 'flex';
     document.body.style.overflow = 'hidden';
     
     // Initialize tooltips after content is loaded
     setTimeout(initTooltips, 100);
 }

 function closePlatformModal() {
     const modal = document.getElementById('platformModal');
     if (modal) {
         modal.style.display = 'none';
         document.body.style.overflow = '';
     }
 }

 function generatePlatformCardHTML(platformName, platformType, data) {
     let html = `<div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">`;
     html += `<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">`;
     html += `<div style="display: flex; align-items: center; justify-content: center;">${getPlatformIconSVG(platformType)}</div>`;
     html += `<div style="flex: 1;"><h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">${platformName}</h2>`;
     if (data.username) html += `<p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">@${data.username}</p>`;
     html += `</div></div>`;
     
     // Profile URL
     const profileUrl = data.profile_url || data.link || (data.username ? 
         (platformType === 'facebook' ? `https://www.facebook.com/${data.username}` :
          platformType === 'instagram' ? `https://www.instagram.com/${data.username}/` :
          platformType === 'tiktok' ? `https://www.tiktok.com/@${data.username}` :
         platformType === 'twitter' ? `https://twitter.com/${data.username}` : null) : null);
     
     if (profileUrl) {
         html += `<div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">`;
         html += `<a href="${profileUrl}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none; font-size: 14px; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">`;
         html += `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>`;
         html += `<span>${profileUrl}</span>`;
         html += `</a></div>`;
     }
     
     // Engagement Metrics
     if (data.engagement) {
         html += `<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 24px; margin-bottom: 20px; color: white;">`;
         html += `<h3 style="font-size: 18px; font-weight: 700; margin: 0 0 20px 0; color: white;">Engagement Metrics</h3>`;
         html += `<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px;">`;
         
         if (data.engagement.engagement_rate !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Engagement Rate: The percentage of followers who interact with posts (likes + comments + shares) relative to total followers. Higher rates indicate better audience engagement."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Engagement Rate</div><div style="font-size: 24px; font-weight: 700;">${parseFloat(data.engagement.engagement_rate).toFixed(2)}%</div></div></span>`;
         }
         if (data.engagement.average_engagement_per_post !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Average Engagement per Post: The average number of total interactions (likes + comments + shares) each post receives. Shows how engaging your content is on average."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Engagement/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_engagement_per_post)}</div></div></span>`;
         }
         if (data.engagement.total_engagement !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Total Engagement: The sum of all interactions (likes + comments + shares) across all analyzed posts. Represents overall audience activity."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Total Engagement</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.total_engagement)}</div></div></span>`;
         }
         if (data.engagement.average_likes !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Average Likes per Post: The average number of likes each post receives. Indicates how much your audience appreciates your content."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Likes/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_likes)}</div></div></span>`;
         }
         if (data.engagement.average_comments !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Average Comments per Post: The average number of comments each post receives. Higher values indicate more active discussions and deeper engagement."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Comments/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_comments)}</div></div></span>`;
         }
         if (data.engagement.average_shares !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Average Shares per Post: The average number of times each post is shared. Shares indicate high-value content that audiences want to share with others."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Shares/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_shares)}</div></div></span>`;
         }
         if (data.engagement.total_posts_analyzed !== undefined) {
             html += `<span class="info-tooltip" data-tooltip="Recent Posts Analyzed: The total number of recent posts included in this analysis. More posts provide a more accurate representation of engagement patterns."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Recent Posts Analyzed</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.total_posts_analyzed)}</div></div></span>`;
         }
         
         // Add Posts count card to engagement metrics
         const postsCount = data.stats?.total_media || data.stats?.total_videos || data.stats?.total_tweets || data.stats?.recent_posts_count || 0;
         if (postsCount > 0) {
             const postsLabel = data.stats?.total_media ? 'Recent Posts' : (data.stats?.total_videos ? 'Recent Videos' : (data.stats?.recent_posts_count ? 'Recent Posts' : 'Recent Tweets'));
             html += `<span class="info-tooltip" data-tooltip="Recent ${postsLabel.replace('Recent ', '')}: The number of recent ${postsLabel.toLowerCase().replace('recent ', '')} fetched from this account. This represents the recent content volume."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">${postsLabel}</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(postsCount)}</div></div></span>`;
         }
         
         html += `</div>`;
         
         // Add posts inside engagement metrics section with pagination
         const posts = data.recent_posts || data.recent_media || data.recent_videos || [];
         if (posts.length > 0) {
             const postsPerPage = 5;
             const totalPages = Math.ceil(posts.length / postsPerPage);
             const uniqueId = 'posts-modal-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
             
             html += `<div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.2);">`;
             html += `<h4 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0; color: white;">Recent Posts (${posts.length} total)</h4>`;
             html += `<div id="${uniqueId}-container" style="display: grid; gap: 12px;">`;
             
             // Render all posts but hide them initially (we'll show first page)
             posts.forEach((post, index) => {
                 const isVisible = index < postsPerPage ? '' : 'display: none;';
                 html += `<div class="post-item-${uniqueId}" style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); ${isVisible}">`;
                 
                 if (post.message || post.text || post.caption || post.description) {
                     const content = post.message || post.text || post.caption || post.description;
                     html += `<p style="color: white; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0; opacity: 0.95;">${content.substring(0, 200)}${content.length > 200 ? '...' : ''}</p>`;
                 }
                 
                 if (post.created_time || post.timestamp) {
                     const date = new Date(post.created_time || post.timestamp);
                     html += `<div style="display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.8); flex-wrap: wrap; margin-bottom: 8px;">`;
                     html += `<span>📅 ${date.toLocaleDateString()}</span>`;
                 } else {
                     html += `<div style="display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.8); flex-wrap: wrap; margin-bottom: 8px;">`;
                 }
                 
                 if (post.likes || post.like_count) {
                     html += `<span>👍 ${formatNumber(post.likes || post.like_count || 0)}</span>`;
                 }
                 if (post.comments || post.comments_count) {
                     html += `<span>💬 ${formatNumber(post.comments || post.comments_count || 0)}</span>`;
                 }
                 if (post.shares || post.share_count) {
                     html += `<span>📤 ${formatNumber(post.shares || post.share_count || 0)}</span>`;
                 }
                 if (post.views || post.view_count) {
                     html += `<span>👁️ ${formatNumber(post.views || post.view_count || 0)}</span>`;
                 }
                 if (post.total_engagement) {
                     html += `<span style="font-weight: 600; opacity: 1;">Total: ${formatNumber(post.total_engagement)}</span>`;
                 }
                 html += `</div>`;
                 
                 if (post.url || post.permalink) {
                     html += `<a href="${post.url || post.permalink}" target="_blank" style="display: inline-block; margin-top: 8px; color: rgba(255,255,255,0.9); text-decoration: underline; font-size: 12px; font-weight: 500;">View Post →</a>`;
                 }
                 
                 html += `</div>`;
             });
             
             html += `</div>`;
             
             // Pagination controls
             if (totalPages > 1) {
                 html += `<div id="${uniqueId}-pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap;">`;
                 html += `<button onclick="changePostsPage('${uniqueId}', 0, ${postsPerPage}, ${posts.length})" id="${uniqueId}-prev" style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s;" disabled onmouseover="this.style.background='rgba(255,255,255,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.2)';">Previous</button>`;
                 html += `<span style="color: white; font-size: 14px; padding: 0 12px;">Page <span id="${uniqueId}-current">1</span> of ${totalPages}</span>`;
                 html += `<button onclick="changePostsPage('${uniqueId}', 1, ${postsPerPage}, ${posts.length})" id="${uniqueId}-next" style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s;" ${totalPages === 1 ? 'disabled' : ''} onmouseover="if(!this.disabled) this.style.background='rgba(255,255,255,0.3)';" onmouseout="if(!this.disabled) this.style.background='rgba(255,255,255,0.2)';">Next</button>`;
                 html += `</div>`;
                 
                 // Store pagination state
                 window[`${uniqueId}_page`] = 1;
             }
             
             html += `</div>`;
         }
         
         html += `</div>`;
     }
     
     html += `</div>`;
     return html;
 }

 function getPlatformIconSVG(platformType) {
     if (platformType === 'facebook') {
         return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`;
     } else if (platformType === 'instagram') {
         return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px;"><defs><linearGradient id="instagram-gradient-modal" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-modal)"/></svg>`;
     } else if (platformType === 'tiktok') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>`;
    } else if (platformType === 'twitter') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`;
    }
    return '';
}

 function formatNumber(num) {
     if (num >= 1000000) {
         return (num / 1000000).toFixed(1) + 'M';
     } else if (num >= 1000) {
         return (num / 1000).toFixed(1) + 'K';
     }
     return num.toString();
 }

 function changePostsPage(uniqueId, direction, postsPerPage, totalPosts) {
     const currentPage = window[`${uniqueId}_page`] || 1;
     const totalPages = Math.ceil(totalPosts / postsPerPage);
     
     let newPage = currentPage;
     if (direction === 0) { // Previous
         newPage = Math.max(1, currentPage - 1);
     } else { // Next
         newPage = Math.min(totalPages, currentPage + 1);
     }
     
     if (newPage === currentPage) return;
     
     window[`${uniqueId}_page`] = newPage;
     
     // Hide all posts
     const posts = document.querySelectorAll(`.post-item-${uniqueId}`);
     posts.forEach(post => {
         post.style.display = 'none';
     });
     
     // Show posts for current page
     const startIndex = (newPage - 1) * postsPerPage;
     const endIndex = startIndex + postsPerPage;
     for (let i = startIndex; i < endIndex && i < posts.length; i++) {
         posts[i].style.display = 'block';
     }
     
     // Update pagination controls
     const currentSpan = document.getElementById(`${uniqueId}-current`);
     if (currentSpan) {
         currentSpan.textContent = newPage;
     }
     
     const prevBtn = document.getElementById(`${uniqueId}-prev`);
     const nextBtn = document.getElementById(`${uniqueId}-next`);
     if (prevBtn) {
         prevBtn.disabled = newPage === 1;
     }
     if (nextBtn) {
         nextBtn.disabled = newPage === totalPages;
     }
 }

 function initTooltips() {
     const tooltips = document.querySelectorAll('.info-tooltip');
     tooltips.forEach(tooltip => {
         const tooltipText = tooltip.getAttribute('data-tooltip');
         if (tooltipText) {
             tooltip.addEventListener('mouseenter', function(e) {
                 const tooltipEl = document.createElement('div');
                 tooltipEl.id = 'dynamic-tooltip';
                 tooltipEl.textContent = tooltipText;
                 tooltipEl.style.cssText = 'position: absolute; background: #1f2937; color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; z-index: 10000; max-width: 300px; pointer-events: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
                 document.body.appendChild(tooltipEl);
                 
                 const rect = tooltip.getBoundingClientRect();
                 tooltipEl.style.left = (rect.left + rect.width / 2 - tooltipEl.offsetWidth / 2) + 'px';
                 tooltipEl.style.top = (rect.top - tooltipEl.offsetHeight - 8) + 'px';
             });
             
             tooltip.addEventListener('mouseleave', function() {
                 const tooltipEl = document.getElementById('dynamic-tooltip');
                 if (tooltipEl) {
                     tooltipEl.remove();
                 }
             });
         }
     });
 }
 </script>

<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        /* Container and card padding */
        .social-show-container {
            padding: 16px 8px !important;
        }
        
        .social-show-wrapper {
            padding: 0 !important;
        }
        
        .social-show-main-card {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Header section */
        h1 {
            font-size: 18px !important;
            line-height: 1.4 !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        
        p[style*="color: #64748b; font-size: 13px"] {
            font-size: 12px !important;
        }
        
        /* Header actions - keep in one row on mobile */
        .social-header-actions {
            flex-direction: row !important;
            width: 100% !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
        }
        
        .social-action-btn {
            flex: 1 !important;
            min-width: 0 !important;
            padding: 10px 8px !important;
            font-size: 11px !important;
            min-height: 44px !important;
        }
        
        /* Status section */
        div[style*="display: flex; align-items: center; gap: 16px"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        /* Platform grid */
        .social-platform-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        .social-platform-grid > div {
            padding: 10px !important;
        }
        
        /* Executive summary */
        .social-executive-summary {
            padding: 16px !important;
        }
        
        .social-executive-summary h3 {
            font-size: 16px !important;
        }
        
        .social-executive-summary p {
            font-size: 13px !important;
        }
        
        /* Risk assessment */
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] {
            padding: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] h3 {
            font-size: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] ul {
            padding-left: 18px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] li {
            font-size: 13px !important;
            margin-bottom: 6px !important;
        }
        
        /* Overall assessment */
        .social-overall-assessment {
            padding: 16px !important;
        }
        
        .social-overall-assessment h3 {
            font-size: 16px !important;
        }
        
        .social-overall-assessment p {
            font-size: 13px !important;
        }
        
        /* Recommendations */
        div[style*="margin-bottom: 32px; padding: 24px; background: #f0fdf4"] {
            padding: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f0fdf4"] h3 {
            font-size: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f0fdf4"] li {
            font-size: 13px !important;
        }
        
        /* Section headings */
        h2 {
            font-size: 16px !important;
        }
        
        h3 {
            font-size: 16px !important;
        }
        
        /* Text content */
        p, li {
            font-size: 13px !important;
        }
        
        /* Platform data warning message */
        div[style*="margin-bottom: 32px; padding: 24px; background: #fef3c7"] {
            padding: 16px !important;
            flex-direction: column !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #fef3c7"] > div:first-child {
            font-size: 24px !important;
            margin-bottom: 12px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #fef3c7"] h3 {
            font-size: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #fef3c7"] p {
            font-size: 13px !important;
        }
        
        /* Metadata section */
        div[style*="margin-top: 32px; padding: 16px; background: #f8fafc"] {
            padding: 12px !important;
            font-size: 11px !important;
        }
        
        /* Empty state */
        div[style*="text-align: center; padding: 60px 20px"] {
            padding: 40px 16px !important;
        }
        
        div[style*="text-align: center; padding: 60px 20px"] > div[style*="font-size: 48px"] {
            font-size: 36px !important;
        }
        
        div[style*="text-align: center; padding: 60px 20px"] h2 {
            font-size: 18px !important;
        }
        
        /* Modal improvements */
        .social-export-modal-overlay {
            padding: 16px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .social-export-modal-content {
            padding: 24px 20px !important;
            max-width: 100% !important;
        }
        
        .social-export-modal-content h3 {
            font-size: 18px !important;
        }
        
        .social-export-modal-content p {
            font-size: 14px !important;
        }
        
        .social-export-modal-content button {
            padding: 12px 20px !important;
            font-size: 14px !important;
            min-height: 44px !important;
        }
        
        .social-export-modal-content div[style*="display: flex; gap: 16px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .social-export-modal-content div[style*="display: flex; gap: 16px"] button {
            width: 100% !important;
        }
        
        /* Platform modal */
        .social-platform-modal-overlay {
            padding: 12px !important;
        }
        
        .social-platform-modal-content {
            max-height: 95vh !important;
        }
        
        .social-platform-modal-content > div:first-child {
            padding: 16px !important;
        }
        
        .social-platform-modal-content > div:first-child h2 {
            font-size: 18px !important;
        }
        
        #platformModalContent {
            padding: 16px !important;
        }
        
        /* Re-analyze modal */
        .social-reanalyze-modal-overlay {
            padding: 12px !important;
        }
        
        .social-reanalyze-modal-content {
            max-height: 95vh !important;
        }
        
        .social-reanalyze-modal-content > div:first-child {
            padding: 16px !important;
        }
        
        .social-reanalyze-modal-content > div:first-child h2 {
            font-size: 18px !important;
        }
        
        #reAnalyzeModalContent {
            padding: 16px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Very small screens */
        .social-show-container {
            padding: 12px 4px !important;
        }
        
        .social-show-main-card {
            padding: 16px 12px !important;
            border-radius: 10px !important;
        }
        
        h1 {
            font-size: 16px !important;
        }
        
        /* Header actions */
        .social-header-actions {
            gap: 6px !important;
        }
        
        .social-action-btn {
            padding: 10px 6px !important;
            font-size: 10px !important;
            min-height: 42px !important;
        }
        
        /* Platform grid */
        .social-platform-grid {
            gap: 8px !important;
        }
        
        .social-platform-grid > div {
            padding: 8px !important;
        }
        
        /* Executive summary */
        .social-executive-summary {
            padding: 12px !important;
        }
        
        .social-executive-summary h3 {
            font-size: 14px !important;
        }
        
        .social-executive-summary p {
            font-size: 12px !important;
        }
        
        /* Risk assessment */
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] {
            padding: 12px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f8fafc"] h3 {
            font-size: 14px !important;
        }
        
        /* Overall assessment */
        .social-overall-assessment {
            padding: 12px !important;
        }
        
        .social-overall-assessment h3 {
            font-size: 14px !important;
        }
        
        .social-overall-assessment p {
            font-size: 12px !important;
        }
        
        /* Recommendations */
        div[style*="margin-bottom: 32px; padding: 24px; background: #f0fdf4"] {
            padding: 12px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: #f0fdf4"] h3 {
            font-size: 14px !important;
        }
        
        /* Section headings */
        h2 {
            font-size: 14px !important;
        }
        
        h3 {
            font-size: 14px !important;
        }
        
        /* Text content */
        p, li {
            font-size: 12px !important;
        }
        
        /* Modal improvements */
        .social-export-modal-overlay {
            padding: 12px !important;
            padding-top: 15vh !important;
        }
        
        .social-export-modal-content {
            padding: 20px 16px !important;
        }
        
        .social-export-modal-content h3 {
            font-size: 16px !important;
        }
        
        .social-export-modal-content p {
            font-size: 13px !important;
        }
        
        .social-export-modal-content button {
            padding: 10px 16px !important;
            font-size: 13px !important;
            min-height: 42px !important;
        }
        
        /* Platform modal */
        .social-platform-modal-overlay {
            padding: 8px !important;
        }
        
        .social-platform-modal-content > div:first-child {
            padding: 12px !important;
        }
        
        .social-platform-modal-content > div:first-child h2 {
            font-size: 16px !important;
        }
        
        #platformModalContent {
            padding: 12px !important;
        }
        
        /* Re-analyze modal */
        .social-reanalyze-modal-overlay {
            padding: 8px !important;
        }
        
        .social-reanalyze-modal-content > div:first-child {
            padding: 12px !important;
        }
        
        .social-reanalyze-modal-content > div:first-child h2 {
            font-size: 16px !important;
        }
        
        #reAnalyzeModalContent {
            padding: 12px !important;
        }
    }
    
    /* Ensure all text wraps properly */
    * {
        box-sizing: border-box;
    }
    
    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }
    
    /* Improve platform modal content on mobile */
    @media (max-width: 768px) {
        #platformModalContent div[style*="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr))"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        #platformModalContent div[style*="background: rgba(255,255,255,0.15); padding: 16px"] {
            padding: 12px !important;
        }
        
        #platformModalContent div[style*="font-size: 24px; font-weight: 700"] {
            font-size: 18px !important;
        }
        
        #platformModalContent div[style*="font-size: 11px"] {
            font-size: 10px !important;
        }
    }
    
    @media (max-width: 480px) {
        #platformModalContent div[style*="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr))"] {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
    }
    
    /* Partial views mobile improvements */
    @media (max-width: 768px) {
        /* Professional footprint */
        div[style*="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px"] {
            padding: 16px !important;
        }
        
        div[style*="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px"] h3 {
            font-size: 16px !important;
        }
        
        div[style*="display: flex; justify-content: space-between; align-items: center"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        div[style*="position: relative; width: 140px; height: 140px"] {
            width: 120px !important;
            height: 120px !important;
        }
        
        svg[width="140"] {
            width: 120px !important;
            height: 120px !important;
        }
        
        div[style*="font-size: 36px; font-weight: 700"] {
            font-size: 28px !important;
        }
        
        /* Activity overview */
        div[style*="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        div[style*="display: flex; align-items: center; gap: 16px"] {
            flex-wrap: wrap !important;
            gap: 12px !important;
        }
        
        /* Text content in partials */
        div[style*="color: #374151; line-height: 1.8; font-size: 14px"] {
            font-size: 13px !important;
        }
        
        p[style*="color: #64748b; line-height: 1.8; font-size: 14px"] {
            font-size: 13px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Professional footprint - smaller gauge */
        div[style*="position: relative; width: 140px; height: 140px"] {
            width: 100px !important;
            height: 100px !important;
        }
        
        svg[width="140"] {
            width: 100px !important;
            height: 100px !important;
        }
        
        div[style*="font-size: 36px; font-weight: 700"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 12px; color: #64748b"] {
            font-size: 10px !important;
        }
        
        /* Text content */
        div[style*="color: #374151; line-height: 1.8; font-size: 14px"] {
            font-size: 12px !important;
        }
        
        p[style*="color: #64748b; line-height: 1.8; font-size: 14px"] {
            font-size: 12px !important;
        }
    }
</style>
@endsection

