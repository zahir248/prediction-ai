@php
    $platformData = $socialMediaAnalysis->platform_data ?? [];
    $hasEngagementData = false;
    $allEngagementMetrics = [];
    
    // Collect engagement metrics from all platforms
    $platforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
    $platformNames = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'twitter' => 'X (Twitter)'
    ];
    
    foreach ($platforms as $platform) {
        if (isset($platformData[$platform]['data']['engagement']) && is_array($platformData[$platform]['data']['engagement'])) {
            $hasEngagementData = true;
            $platformInfo = $platformData[$platform]['data'];
            $engagement = $platformInfo['engagement'];
            
            $allEngagementMetrics[] = [
                'platform' => $platformNames[$platform] ?? ucfirst($platform),
                'platform_key' => $platform,
                'engagement' => $engagement,
                'stats' => $platformInfo['stats'] ?? [],
                'posts' => $platformInfo['recent_posts'] ?? 
                          $platformInfo['recent_media'] ?? 
                          $platformInfo['recent_videos'] ?? 
                          $platformInfo['recent_tweets'] ?? [],
                'username' => $platformInfo['username'] ?? null,
                'profile_url' => $platformInfo['profile_url'] ?? $platformInfo['link'] ?? null,
                'data' => $platformInfo
            ];
        }
    }
@endphp

@if($hasEngagementData)
    <div class="engagement-metrics-wrapper" style="max-width: 100%; margin: 0; padding: 0;">
        @foreach($allEngagementMetrics as $platformMetrics)
            @php
                $engagement = $platformMetrics['engagement'];
                $stats = $platformMetrics['stats'];
                $posts = $platformMetrics['posts'];
                $platformName = $platformMetrics['platform'];
                $platformKey = $platformMetrics['platform_key'];
                $username = $platformMetrics['username'];
                $profileUrl = $platformMetrics['profile_url'];
                $data = $platformMetrics['data'];
                
                // Generate profile URL if not provided
                if (!$profileUrl && $username) {
                    if ($platformKey === 'facebook') {
                        $profileUrl = 'https://www.facebook.com/' . $username;
                    } elseif ($platformKey === 'instagram') {
                        $profileUrl = 'https://www.instagram.com/' . $username . '/';
                    } elseif ($platformKey === 'tiktok') {
                        $profileUrl = 'https://www.tiktok.com/@' . $username;
                    } elseif ($platformKey === 'twitter') {
                        $profileUrl = 'https://twitter.com/' . $username;
                    }
                }
            @endphp
            
            <div style="padding: 0; margin-bottom: 20px;">
                <!-- Platform Header -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; justify-content: center;">
                        @if($platformKey === 'facebook')
                            <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #1877F2;">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        @elseif($platformKey === 'instagram')
                            <svg viewBox="0 0 24 24" style="width: 32px; height: 32px;">
                                <defs>
                                    <linearGradient id="instagram-gradient-engagement-{{ $platformKey }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-engagement-{{ $platformKey }})"/>
                            </svg>
                        @elseif($platformKey === 'tiktok')
                            <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                        @elseif($platformKey === 'twitter')
                            <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">{{ $platformName }}</h2>
                        @if($username)
                            <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">{!! '@' . $username !!}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Profile URL -->
                @if($profileUrl)
                    <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                        <a href="{{ $profileUrl }}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none; font-size: 14px; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            <span>{{ $profileUrl }}</span>
                        </a>
                    </div>
                @endif
                
                <!-- Engagement Metrics -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 24px; margin-bottom: 20px; color: white;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 20px 0; color: white;">Engagement Metrics</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px;">
                        
                        @if(isset($engagement['engagement_rate']))
                            <span class="info-tooltip" data-tooltip="Engagement Rate: The percentage of followers who interact with posts (likes + comments + shares) relative to total followers. Higher rates indicate better audience engagement.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Engagement Rate</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['engagement_rate'], 2) }}%</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['average_engagement_per_post']))
                            <span class="info-tooltip" data-tooltip="Average Engagement per Post: The average number of total interactions (likes + comments + shares) each post receives. Shows how engaging your content is on average.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Engagement/Post</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['average_engagement_per_post']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['total_engagement']))
                            <span class="info-tooltip" data-tooltip="Total Engagement: The sum of all interactions (likes + comments + shares) across all analyzed posts. Represents overall audience activity.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Total Engagement</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['total_engagement']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['average_likes']))
                            <span class="info-tooltip" data-tooltip="Average Likes per Post: The average number of likes each post receives. Indicates how much your audience appreciates your content.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Likes/Post</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['average_likes']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['average_comments']))
                            <span class="info-tooltip" data-tooltip="Average Comments per Post: The average number of comments each post receives. Higher values indicate more active discussions and deeper engagement.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Comments/Post</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['average_comments']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['average_shares']))
                            <span class="info-tooltip" data-tooltip="Average Shares per Post: The average number of times each post is shared. Shares indicate high-value content that audiences want to share with others.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Shares/Post</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['average_shares']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @if(isset($engagement['total_posts_analyzed']))
                            <span class="info-tooltip" data-tooltip="Recent Posts Analyzed: The total number of recent posts included in this analysis. More posts provide a more accurate representation of engagement patterns.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Recent Posts Analyzed</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($engagement['total_posts_analyzed']) }}</div>
                                </div>
                            </span>
                        @endif
                        
                        @php
                            $postsCount = $stats['total_media'] ?? $stats['total_videos'] ?? $stats['total_tweets'] ?? $stats['recent_posts_count'] ?? 0;
                            $postsLabel = isset($stats['total_media']) ? 'Recent Posts' : (isset($stats['total_videos']) ? 'Recent Videos' : (isset($stats['recent_posts_count']) ? 'Recent Posts' : 'Recent Tweets'));
                        @endphp
                        @if($postsCount > 0)
                            <span class="info-tooltip" data-tooltip="Recent {{ str_replace('Recent ', '', $postsLabel) }}: The number of recent {{ strtolower(str_replace('Recent ', '', $postsLabel)) }} fetched from this account. This represents the recent content volume.">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">{{ $postsLabel }}</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($postsCount) }}</div>
                                </div>
                            </span>
                        @endif
                    </div>
                    
                    <!-- Recent Posts -->
                    @if(count($posts) > 0)
                        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.2);">
                            <h4 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0; color: white;">Recent Posts ({{ count($posts) }} total)</h4>
                            <div style="display: grid; gap: 12px; max-height: 600px; overflow-y: auto; padding-right: 8px;">
                                @foreach($posts as $post)
                                    <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                        @if(isset($post['message']) || isset($post['text']) || isset($post['caption']) || isset($post['description']))
                                            @php
                                                $content = $post['message'] ?? $post['text'] ?? $post['caption'] ?? $post['description'] ?? '';
                                                $truncated = Str::limit($content, 200);
                                            @endphp
                                            <p style="color: white; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0; opacity: 0.95;">{{ $truncated }}</p>
                                        @endif
                                        
                                        <div style="display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.8); flex-wrap: wrap; margin-bottom: 8px;">
                                            @if(isset($post['created_time']) || isset($post['timestamp']))
                                                @php
                                                    $date = isset($post['created_time']) ? \Carbon\Carbon::parse($post['created_time']) : \Carbon\Carbon::parse($post['timestamp']);
                                                @endphp
                                                <span>📅 {{ $date->format('M d, Y') }}</span>
                                            @endif
                                            
                                            @if(isset($post['likes']) || isset($post['like_count']))
                                                <span>👍 {{ number_format($post['likes'] ?? $post['like_count'] ?? 0) }}</span>
                                            @endif
                                            
                                            @if(isset($post['comments']) || isset($post['comments_count']))
                                                <span>💬 {{ number_format($post['comments'] ?? $post['comments_count'] ?? 0) }}</span>
                                            @endif
                                            
                                            @if(isset($post['shares']) || isset($post['share_count']))
                                                <span>📤 {{ number_format($post['shares'] ?? $post['share_count'] ?? 0) }}</span>
                                            @endif
                                            
                                            @if(isset($post['views']) || isset($post['view_count']))
                                                <span>👁️ {{ number_format($post['views'] ?? $post['view_count'] ?? 0) }}</span>
                                            @endif
                                            
                                            @if(isset($post['total_engagement']))
                                                <span style="font-weight: 600; opacity: 1;">Total: {{ number_format($post['total_engagement']) }}</span>
                                            @endif
                                        </div>
                                        
                                        @if(isset($post['url']) || isset($post['permalink']))
                                            <a href="{{ $post['url'] ?? $post['permalink'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-top: 8px; color: rgba(255,255,255,0.9); text-decoration: underline; font-size: 12px; font-weight: 500;">View Post →</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div style="text-align: center; padding: 60px 20px; color: #64748b;">
        <i class="bi bi-bar-chart" style="font-size: 48px; display: block; margin-bottom: 16px; opacity: 0.5;"></i>
        <h3 style="font-size: 18px; font-weight: 600; color: #64748b; margin-bottom: 12px;">No Engagement Metrics Available</h3>
        <p style="color: #9ca3af; line-height: 1.6; margin: 0; font-size: 14px;">Engagement metrics are not available for this analysis.</p>
    </div>
@endif

<script>
// Initialize tooltips
function initEngagementTooltips() {
    const tooltips = document.querySelectorAll('.info-tooltip');
    tooltips.forEach(tooltip => {
        const tooltipText = tooltip.getAttribute('data-tooltip');
        if (tooltipText) {
            tooltip.addEventListener('mouseenter', function(e) {
                const tooltipEl = document.createElement('div');
                tooltipEl.id = 'dynamic-tooltip-engagement';
                tooltipEl.textContent = tooltipText;
                tooltipEl.style.cssText = 'position: absolute; background: #1f2937; color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; z-index: 10000; max-width: 300px; pointer-events: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
                document.body.appendChild(tooltipEl);
                
                const rect = tooltip.getBoundingClientRect();
                tooltipEl.style.left = (rect.left + rect.width / 2 - tooltipEl.offsetWidth / 2) + 'px';
                tooltipEl.style.top = (rect.top - tooltipEl.offsetHeight - 8) + 'px';
            });
            
            tooltip.addEventListener('mouseleave', function() {
                const tooltipEl = document.getElementById('dynamic-tooltip-engagement');
                if (tooltipEl) {
                    tooltipEl.remove();
                }
            });
        }
    });
}

// Initialize tooltips when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEngagementTooltips);
} else {
    initEngagementTooltips();
}
</script>
