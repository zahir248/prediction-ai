@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="margin-bottom: 24px;">
            <a href="{{ route('social-media.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: #667eea; text-decoration: none; font-weight: 500; font-size: 14px; margin-bottom: 16px;">
                ← Back to Analysis
            </a>
            <h1 style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">Social Media Analysis Results</h1>
            <p style="color: #64748b; font-size: 14px; margin: 8px 0 0 0;">
                Username: <strong>{{ $identifier }}</strong> | Platform: <strong>{{ ucfirst($platform) }}</strong>
                @if(isset($result['search_method']))
                    | Search Method: <strong>{{ ucfirst($result['search_method']) }}</strong>
                @endif
            </p>
        </div>

        @if(isset($result['error']))
            <!-- Error Display -->
            <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-bottom: 24px;">
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 20px; border-radius: 8px;">
                    <strong style="display: block; margin-bottom: 12px; font-size: 16px;">❌ Analysis Failed</strong>
                    <p style="margin: 0 0 16px 0; line-height: 1.6;">{{ $result['error'] }}</p>
                    
                    @if(isset($result['token_type_issue']) && $result['token_type_issue'])
                        <div style="background: #fef3c7; border: 2px solid #fbbf24; color: #92400e; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                            <strong style="display: block; margin-bottom: 8px; font-size: 15px;">⚠️ Token Type Limitation</strong>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                                You are using a <strong>System User Token</strong>, which can only access Facebook Pages and Instagram Business Accounts. 
                                Personal profiles require a <strong>User Access Token</strong> (generated via Facebook Login).
                            </p>
                        </div>
                    @endif
                    
                    @if(isset($result['error_code']))
                        <div style="background: rgba(255,255,255,0.5); padding: 8px 12px; border-radius: 4px; margin-bottom: 12px; font-size: 13px;">
                            <strong>Error Code:</strong> {{ $result['error_code'] }}
                            @if($result['error_code'] == 190)
                                <br><span style="color: #dc2626; font-size: 12px;">This usually means the access token is invalid or expired.</span>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($result['token_guide_url']))
                        <div style="background: rgba(255,255,255,0.5); padding: 12px; border-radius: 4px; margin-top: 12px;">
                            <a href="{{ $result['token_guide_url'] }}" target="_blank" style="color: #0369a1; text-decoration: underline; font-size: 13px; font-weight: 600;">
                                📖 Learn How to Get User Access Tokens for Personal Profiles →
                            </a>
                        </div>
                    @endif
                    
                    @if(isset($result['help_url']) && !isset($result['token_guide_url']))
                        <div style="background: rgba(255,255,255,0.5); padding: 12px; border-radius: 4px; margin-top: 12px;">
                            <a href="{{ $result['help_url'] }}" target="_blank" style="color: #0369a1; text-decoration: underline; font-size: 13px;">
                                📖 Get Help: How to Generate Facebook Access Token →
                            </a>
                        </div>
                    @endif
                    
                    @if(isset($result['suggestions']) && is_array($result['suggestions']))
                        <div style="background: rgba(255,255,255,0.5); padding: 12px; border-radius: 4px; margin-top: 12px;">
                            <strong style="display: block; margin-bottom: 8px; font-size: 14px;">💡 Suggestions:</strong>
                            <ul style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.8;">
                                @foreach($result['suggestions'] as $suggestion)
                                    <li>{{ $suggestion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if(isset($result['details']))
                        <div style="background: rgba(255,255,255,0.5); padding: 8px 12px; border-radius: 4px; margin-top: 12px; font-size: 13px;">
                            {{ $result['details'] }}
                        </div>
                    @endif
                </div>
                
                <div style="margin-top: 24px; text-align: center;">
                    <a href="{{ route('social-media.index') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                        Try Again
                    </a>
                </div>
            </div>
        @else
            <!-- Facebook Personal Profile Results -->
            @if(isset($result['facebook']) && $result['facebook'] && isset($result['facebook']['is_personal_profile']) && $result['facebook']['is_personal_profile'])
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">
                        <div style="width: 48px; height: 48px; background: #1877f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">👤</div>
                        <div>
                            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">Facebook Personal Profile</h2>
                            <p style="color: #64748b; font-size: 13px; margin: 0;">{{ $result['facebook']['name'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                        @if(isset($result['facebook']['profile_picture']) && $result['facebook']['profile_picture'])
                            <div>
                                <img src="{{ $result['facebook']['profile_picture'] }}" alt="Profile Picture" style="width: 100%; max-width: 200px; border-radius: 50%; border: 1px solid #e2e8f0;">
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">{{ $result['facebook']['name'] ?? 'N/A' }}</h3>
                            @if((isset($result['facebook']['first_name']) && $result['facebook']['first_name']) || (isset($result['facebook']['last_name']) && $result['facebook']['last_name']))
                                <p style="color: #64748b; font-size: 14px; margin: 0 0 12px 0;">
                                    {{ $result['facebook']['first_name'] ?? '' }} {{ $result['facebook']['last_name'] ?? '' }}
                                </p>
                            @endif
                            @if(isset($result['facebook']['about']) && $result['facebook']['about'])
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ $result['facebook']['about'] }}</p>
                            @endif
                            @if(isset($result['facebook']['bio']) && $result['facebook']['bio'])
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ $result['facebook']['bio'] }}</p>
                            @endif
                            @if(isset($result['facebook']['link']) && $result['facebook']['link'])
                                <a href="{{ $result['facebook']['link'] }}" target="_blank" style="display: inline-block; color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                                    View Profile →
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    @if(isset($result['facebook']['recent_posts']) && count($result['facebook']['recent_posts']) > 0)
                        <div>
                            <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 16px 0;">Recent Posts</h4>
                            <div style="display: grid; gap: 16px;">
                                @foreach($result['facebook']['recent_posts'] as $post)
                                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        @if($post['message'])
                                            <p style="color: #1e293b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ Str::limit($post['message'], 200) }}</p>
                                        @endif
                                        <div style="display: flex; gap: 16px; font-size: 12px; color: #64748b;">
                                            @if($post['created_time'])
                                                <span>📅 {{ \Carbon\Carbon::parse($post['created_time'])->format('M d, Y') }}</span>
                                            @endif
                                            <span>👍 {{ number_format($post['likes'] ?? 0) }}</span>
                                            <span>💬 {{ number_format($post['comments'] ?? 0) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Facebook Page Results -->
            @if(isset($result['facebook']) && $result['facebook'] && (!isset($result['facebook']['is_personal_profile']) || !$result['facebook']['is_personal_profile']))
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">
                        <div style="width: 48px; height: 48px; background: #1877f2; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">f</div>
                        <div>
                            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">Facebook Page</h2>
                            <p style="color: #64748b; font-size: 13px; margin: 0;">{{ $result['facebook']['username'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                        @if($result['facebook']['profile_picture'])
                            <div>
                                <img src="{{ $result['facebook']['profile_picture'] }}" alt="Profile Picture" style="width: 100%; max-width: 200px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">{{ $result['facebook']['name'] ?? 'N/A' }}</h3>
                            @if($result['facebook']['about'])
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ $result['facebook']['about'] }}</p>
                            @endif
                            @if($result['facebook']['description'])
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ $result['facebook']['description'] }}</p>
                            @endif
                            @if($result['facebook']['link'])
                                <a href="{{ $result['facebook']['link'] }}" target="_blank" style="display: inline-block; color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                                    View Page →
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Fans</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ number_format($result['facebook']['stats']['total_fans'] ?? 0) }}</div>
                        </div>
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Followers</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ number_format($result['facebook']['stats']['total_followers'] ?? 0) }}</div>
                        </div>
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Recent Posts</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $result['facebook']['stats']['recent_posts_count'] ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Analysis & Valuation -->
                    @if(isset($result['facebook']['analysis']) && $result['facebook']['analysis'])
                        @php $analysis = $result['facebook']['analysis']; @endphp
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: white;">
                            <h4 style="font-size: 18px; font-weight: 700; margin: 0 0 20px 0; color: white;">📊 Account Analysis & Valuation</h4>
                            
                            <!-- Valuation Score -->
                            <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 8px; margin-bottom: 20px; backdrop-filter: blur(10px);">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                    <div>
                                        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Valuation Score</div>
                                        <div style="font-size: 32px; font-weight: 700;">{{ $analysis['valuation_score'] ?? 0 }}/100</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Tier</div>
                                        <div style="font-size: 20px; font-weight: 700;">{{ $analysis['valuation_tier'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div style="background: rgba(255,255,255,0.3); height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background: white; height: 100%; width: {{ $analysis['valuation_score'] ?? 0 }}%; transition: width 0.3s;"></div>
                                </div>
                            </div>

                            <!-- Engagement Metrics -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 20px;">
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Engagement Rate</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($analysis['engagement_rate'] ?? 0, 2) }}%</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Engagement</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($analysis['average_engagement_per_post'] ?? 0) }}</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Total Engagement</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($analysis['total_engagement'] ?? 0) }}</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Posts Analyzed</div>
                                    <div style="font-size: 24px; font-weight: 700;">{{ $analysis['total_posts_analyzed'] ?? 0 }}</div>
                                </div>
                            </div>

                            <!-- Engagement Breakdown -->
                            <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; margin-bottom: 20px; backdrop-filter: blur(10px);">
                                <div style="font-size: 13px; font-weight: 600; margin-bottom: 12px;">Engagement Breakdown</div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; font-size: 12px;">
                                    <div>
                                        <div style="opacity: 0.9; margin-bottom: 4px;">👍 Likes</div>
                                        <div style="font-size: 18px; font-weight: 700;">{{ number_format($analysis['average_likes'] ?? 0) }}</div>
                                        @if(isset($analysis['engagement_breakdown']['likes_percentage']))
                                            <div style="font-size: 10px; opacity: 0.8;">{{ number_format($analysis['engagement_breakdown']['likes_percentage'], 1) }}%</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="opacity: 0.9; margin-bottom: 4px;">💬 Comments</div>
                                        <div style="font-size: 18px; font-weight: 700;">{{ number_format($analysis['average_comments'] ?? 0) }}</div>
                                        @if(isset($analysis['engagement_breakdown']['comments_percentage']))
                                            <div style="font-size: 10px; opacity: 0.8;">{{ number_format($analysis['engagement_breakdown']['comments_percentage'], 1) }}%</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="opacity: 0.9; margin-bottom: 4px;">📤 Shares</div>
                                        <div style="font-size: 18px; font-weight: 700;">{{ number_format($analysis['average_shares'] ?? 0) }}</div>
                                        @if(isset($analysis['engagement_breakdown']['shares_percentage']))
                                            <div style="font-size: 10px; opacity: 0.8;">{{ number_format($analysis['engagement_breakdown']['shares_percentage'], 1) }}%</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Post Frequency -->
                            @if(isset($analysis['post_frequency']) && $analysis['post_frequency'])
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; margin-bottom: 20px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">Post Frequency</div>
                                    <div style="display: flex; gap: 20px; font-size: 12px;">
                                        <div>
                                            <span style="opacity: 0.9;">Per Day:</span>
                                            <span style="font-weight: 700; font-size: 16px;">{{ number_format($analysis['post_frequency']['posts_per_day'] ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <span style="opacity: 0.9;">Per Week:</span>
                                            <span style="font-weight: 700; font-size: 16px;">{{ number_format($analysis['post_frequency']['posts_per_week'] ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <span style="opacity: 0.9;">Days Analyzed:</span>
                                            <span style="font-weight: 700; font-size: 16px;">{{ number_format($analysis['post_frequency']['days_analyzed'] ?? 0, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Best Post -->
                            @if(isset($analysis['best_post']) && $analysis['best_post'])
                                <div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 13px; font-weight: 600; margin-bottom: 12px;">🏆 Best Performing Post</div>
                                    @if($analysis['best_post']['message'])
                                        <p style="font-size: 12px; line-height: 1.5; margin: 0 0 12px 0; opacity: 0.95;">{{ Str::limit($analysis['best_post']['message'], 150) }}</p>
                                    @endif
                                    <div style="display: flex; gap: 16px; font-size: 11px; opacity: 0.9;">
                                        <span>👍 {{ number_format($analysis['best_post']['likes'] ?? 0) }}</span>
                                        <span>💬 {{ number_format($analysis['best_post']['comments'] ?? 0) }}</span>
                                        <span>📤 {{ number_format($analysis['best_post']['shares'] ?? 0) }}</span>
                                        <span style="font-weight: 700;">Total: {{ number_format($analysis['best_post']['total_engagement'] ?? 0) }}</span>
                                    </div>
                                    @if(isset($analysis['best_post']['url']) && $analysis['best_post']['url'])
                                        <a href="{{ $analysis['best_post']['url'] }}" target="_blank" style="display: inline-block; margin-top: 12px; color: white; text-decoration: underline; font-size: 11px; font-weight: 500;">
                                            View Post →
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Additional Info -->
                    @if($result['facebook']['category'] || $result['facebook']['phone'] || $result['facebook']['website'])
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                            <h4 style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Additional Information</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 13px;">
                                @if($result['facebook']['category'])
                                    <div>
                                        <span style="color: #64748b;">Category:</span>
                                        <span style="color: #1e293b; font-weight: 500;">{{ $result['facebook']['category'] }}</span>
                                    </div>
                                @endif
                                @if($result['facebook']['phone'])
                                    <div>
                                        <span style="color: #64748b;">Phone:</span>
                                        <span style="color: #1e293b; font-weight: 500;">{{ $result['facebook']['phone'] }}</span>
                                    </div>
                                @endif
                                @if($result['facebook']['website'])
                                    <div>
                                        <span style="color: #64748b;">Website:</span>
                                        <a href="{{ $result['facebook']['website'] }}" target="_blank" style="color: #667eea; text-decoration: none;">{{ $result['facebook']['website'] }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Recent Posts -->
                    @if(isset($result['facebook']['recent_posts']) && count($result['facebook']['recent_posts']) > 0)
                        <div>
                            <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 16px 0;">Recent Posts</h4>
                            <div style="display: grid; gap: 16px;">
                                @foreach($result['facebook']['recent_posts'] as $post)
                                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        @if($post['message'])
                                            <p style="color: #1e293b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ Str::limit($post['message'], 200) }}</p>
                                        @endif
                                        <div style="display: flex; gap: 16px; font-size: 12px; color: #64748b; flex-wrap: wrap;">
                                            @if($post['created_time'])
                                                <span>📅 {{ \Carbon\Carbon::parse($post['created_time'])->format('M d, Y') }}</span>
                                            @endif
                                            <span>👍 {{ number_format($post['likes'] ?? 0) }}</span>
                                            <span>💬 {{ number_format($post['comments'] ?? 0) }}</span>
                                            <span>📤 {{ number_format($post['shares'] ?? 0) }}</span>
                                            @if(isset($post['total_engagement']))
                                                <span style="font-weight: 600; color: #667eea;">Total: {{ number_format($post['total_engagement']) }}</span>
                                            @endif
                                        </div>
                                        @if(isset($post['url']) && $post['url'])
                                            <a href="{{ $post['url'] }}" target="_blank" style="display: inline-block; margin-top: 8px; color: #667eea; text-decoration: none; font-size: 12px; font-weight: 500;">
                                                View Post →
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Instagram Results -->
            @if(isset($result['instagram']) && $result['instagram'])
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">
                        <div style="width: 48px; height: 48px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">📷</div>
                        <div>
                            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">Instagram Business Account</h2>
                            <p style="color: #64748b; font-size: 13px; margin: 0;">@{{ $result['instagram']['username'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                        @if($result['instagram']['profile_picture'])
                            <div>
                                <img src="{{ $result['instagram']['profile_picture'] }}" alt="Profile Picture" style="width: 100%; max-width: 200px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">{{ $result['instagram']['name'] ?? $result['instagram']['username'] ?? 'N/A' }}</h3>
                            @if($result['instagram']['biography'])
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">{{ $result['instagram']['biography'] }}</p>
                            @endif
                            @if($result['instagram']['website'])
                                <a href="{{ $result['instagram']['website'] }}" target="_blank" style="display: inline-block; color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                                    Visit Website →
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Followers</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ number_format($result['instagram']['stats']['total_followers'] ?? 0) }}</div>
                        </div>
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Total Media</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ number_format($result['instagram']['stats']['total_media'] ?? 0) }}</div>
                        </div>
                        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Recent Media</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $result['instagram']['stats']['recent_media_count'] ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Recent Media -->
                    @if(isset($result['instagram']['recent_media']) && count($result['instagram']['recent_media']) > 0)
                        <div>
                            <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 16px 0;">Recent Media</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                                @foreach($result['instagram']['recent_media'] as $media)
                                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        @if($media['media_url'])
                                            <img src="{{ $media['media_url'] }}" alt="Media" style="width: 100%; border-radius: 8px; margin-bottom: 12px; aspect-ratio: 1; object-fit: cover;">
                                        @endif
                                        @if($media['caption'])
                                            <p style="color: #1e293b; font-size: 13px; line-height: 1.5; margin: 0 0 12px 0;">{{ Str::limit($media['caption'], 100) }}</p>
                                        @endif
                                        <div style="display: flex; gap: 12px; font-size: 12px; color: #64748b;">
                                            <span>❤️ {{ number_format($media['like_count'] ?? 0) }}</span>
                                            <span>💬 {{ number_format($media['comments_count'] ?? 0) }}</span>
                                        </div>
                                        @if($media['permalink'])
                                            <a href="{{ $media['permalink'] }}" target="_blank" style="display: inline-block; margin-top: 8px; color: #667eea; text-decoration: none; font-size: 12px; font-weight: 500;">
                                                View Post →
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- No Results -->
            @if((!isset($result['facebook']) || !$result['facebook']) && (!isset($result['instagram']) || !$result['instagram']))
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">No Results Found</h3>
                    <p style="color: #64748b; font-size: 14px; margin: 0 0 24px 0;">
                        We couldn't find any social media accounts with the provided identifier.
                    </p>
                    <a href="{{ route('social-media.index') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                        Try Again
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

