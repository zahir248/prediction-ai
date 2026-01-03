@php
    $isFallbackResponse = false;
    if ($socialMediaAnalysis->status === 'completed' && $socialMediaAnalysis->ai_analysis) {
        $analysis = $socialMediaAnalysis->ai_analysis;
        // Check if this is a fallback/error response
        if (isset($analysis['title']) && is_string($analysis['title'])) {
            $isFallbackResponse = stripos($analysis['title'], 'Analysis Failed') !== false || 
                                 stripos($analysis['title'], 'Fallback Response') !== false;
        }
        if (!$isFallbackResponse && isset($analysis['executive_summary']) && is_string($analysis['executive_summary'])) {
            $isFallbackResponse = stripos($analysis['executive_summary'], 'Due to technical difficulties') !== false ||
                                 stripos($analysis['executive_summary'], 'comprehensive analysis could not be generated') !== false ||
                                 stripos($analysis['executive_summary'], 'Fallback Response') !== false;
        }
    }
@endphp

@if($isFallbackResponse)
    <!-- Fallback/Error Response - Show Simple Error Message (Centered) -->
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100%; width: 100%; text-align: center; padding: 60px 20px; box-sizing: border-box;">
        <div style="font-size: 48px; margin-bottom: 16px;">❌</div>
        <h2 style="font-size: 20px; font-weight: 600; color: #ef4444; margin-bottom: 12px;">Analysis Failed</h2>
        <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto;">This analysis could not be completed. You can still view the platform data above or try re-analyzing.</p>
    </div>
@else
<div class="social-content-wrapper" style="width: 100%; margin: 0; padding: 0;">
    <!-- Main Content (No Card) -->
    <div class="social-main-card" style="background: transparent; padding: 0; width: 100%;">
        @if($socialMediaAnalysis->status === 'completed' && $socialMediaAnalysis->ai_analysis)
            @php
                $analysis = $socialMediaAnalysis->ai_analysis;
            @endphp
            
            <!-- NUJUM Analysis Results -->
            <div style="width: 100%;">
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
                    @if($socialMediaAnalysis->created_at)
                        <div style="margin-bottom: 4px;"><strong>Analysis Date:</strong> {{ $socialMediaAnalysis->created_at->format('M d, Y \a\t g:i A') }}</div>
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
            <!-- No NUJUM Analysis Yet - Show message but allow viewing platform data -->
            @if($socialMediaAnalysis->platform_data)
                <div style="margin-bottom: 32px; padding: 24px; background: #fef3c7; border-radius: 12px; border: 1px solid #fde68a;">
                    <div style="display: flex; align-items: start; gap: 16px;">
                        <div style="font-size: 32px; flex-shrink: 0;">📊</div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #92400e; margin-bottom: 8px;">Platform Data Available</h3>
                            <p style="color: #78350f; font-size: 14px; margin-bottom: 16px; line-height: 1.6;">
                                Platform search has been completed, but NUJUM analysis has not been performed yet. You can view the platform data above or start an analysis using the "Re-analyze" button.
                            </p>
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
@endif

<!-- Platform Modal (same as show page) -->
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

<script>
// Platform Modal Functions (same as show page)
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

// Close platform modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const platformModal = document.getElementById('platformModal');
    if (platformModal) {
        platformModal.onclick = function(e) {
            if (e.target === this) {
                closePlatformModal();
            }
        };
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePlatformModal();
    }
});
</script>
