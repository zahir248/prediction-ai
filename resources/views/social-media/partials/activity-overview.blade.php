@php
    $data = $analysis['activity_overview'] ?? [];
    $confidence = $data['confidence'] ?? $data['confidence_level'] ?? null;
    $overview = $data['overview'] ?? $data['summary'] ?? $data['description'] ?? 'Professional activity timeline highlights consistent engagement and content sharing. Key recent activities include:';
    
    // Extract posts from platform_data
    $allPosts = [];
    $platformData = $socialMediaAnalysis->platform_data ?? [];
    
    // Facebook posts
    $facebookPosts = $platformData['facebook']['data']['posts'] ?? $platformData['facebook']['data']['recent_posts'] ?? [];
    if (is_array($facebookPosts)) {
        foreach ($facebookPosts as $post) {
            if (isset($post['message']) || isset($post['text']) || isset($post['postText'])) {
                $allPosts[] = [
                    'platform' => 'Facebook',
                    'platform_display' => 'Post on Facebook:',
                    'date' => $post['created_time'] ?? $post['time'] ?? $post['timestamp'] ?? null,
                    'content' => $post['message'] ?? $post['text'] ?? $post['postText'] ?? '',
                    'likes' => $post['likes'] ?? $post['likeCount'] ?? $post['reactions'] ?? 0,
                    'comments' => $post['comments'] ?? $post['commentCount'] ?? 0,
                    'url' => $post['url'] ?? $post['postUrl'] ?? null,
                ];
            }
        }
    }
    
    // Instagram posts (recent_media)
    if (isset($platformData['instagram']['data']['recent_media']) && is_array($platformData['instagram']['data']['recent_media'])) {
        foreach ($platformData['instagram']['data']['recent_media'] as $post) {
            if (isset($post['caption']) || isset($post['text'])) {
                $allPosts[] = [
                    'platform' => 'Instagram',
                    'platform_display' => 'Post on Instagram:',
                    'date' => $post['timestamp'] ?? $post['taken_at'] ?? $post['created_time'] ?? null,
                    'content' => $post['caption'] ?? $post['text'] ?? '',
                    'likes' => $post['likesCount'] ?? $post['likes'] ?? $post['like_count'] ?? 0,
                    'comments' => $post['commentsCount'] ?? $post['comments'] ?? $post['comment_count'] ?? 0,
                    'url' => $post['url'] ?? $post['shortCode'] ? "https://www.instagram.com/p/{$post['shortCode']}/" : null,
                ];
            }
        }
    }
    
    // TikTok posts (if available)
    if (isset($platformData['tiktok']['data']['videos']) && is_array($platformData['tiktok']['data']['videos'])) {
        foreach ($platformData['tiktok']['data']['videos'] as $post) {
            if (isset($post['description']) || isset($post['text'])) {
                $allPosts[] = [
                    'platform' => 'TikTok',
                    'platform_display' => 'Post on TikTok:',
                    'date' => $post['createTime'] ?? $post['timestamp'] ?? $post['created_time'] ?? null,
                    'content' => $post['description'] ?? $post['text'] ?? '',
                    'likes' => $post['diggCount'] ?? $post['likes'] ?? $post['like_count'] ?? 0,
                    'comments' => $post['commentCount'] ?? $post['comments'] ?? $post['comment_count'] ?? 0,
                    'url' => $post['url'] ?? $post['webVideoUrl'] ?? null,
                ];
            }
        }
    }
    
    // Sort posts by date (newest first)
    usort($allPosts, function($a, $b) {
        $dateA = $a['date'] ?? '';
        $dateB = $b['date'] ?? '';
        
        // Try to parse dates
        $timestampA = is_numeric($dateA) ? $dateA : strtotime($dateA);
        $timestampB = is_numeric($dateB) ? $dateB : strtotime($dateB);
        
        // If parsing fails, put at end
        if ($timestampA === false) $timestampA = 0;
        if ($timestampB === false) $timestampB = 0;
        
        return $timestampB - $timestampA; // Descending order (newest first)
    });
    
    // Limit to 10 most recent posts
    $allPosts = array_slice($allPosts, 0, 10);
    
    // Format date helper
    if (!function_exists('activityOverview_formatDate')) {
        function activityOverview_formatDate($date) {
            if (empty($date)) return 'Date unknown';
            
            // If it's a timestamp
            if (is_numeric($date)) {
                return date('F j, Y', $date);
            }
            
            // Try to parse as date string
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('F j, Y', $timestamp);
            }
            
            return $date;
        }
    }
@endphp

<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Activity Overview & Behavioral Patterns</h3>
        @if($confidence)
            <span style="background: white; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0;">
                Confidence: {{ is_numeric($confidence) ? $confidence . '%' : $confidence }}
            </span>
        @endif
    </div>
    
    <!-- Overview Text -->
    @if($overview)
        <p style="color: #64748b; line-height: 1.8; font-size: 14px; margin-bottom: 24px;">
            {{ $overview }}
        </p>
    @endif
    
    <!-- Timeline of Posts -->
    @if(count($allPosts) > 0)
        <div style="display: flex; flex-direction: column; gap: 0;">
            @foreach($allPosts as $index => $post)
                <div style="padding: 20px 0; {{ $index < count($allPosts) - 1 ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                    <!-- Date -->
                    <div style="color: #94a3b8; font-size: 13px; font-weight: 500; margin-bottom: 8px;">
                        {{ activityOverview_formatDate($post['date']) }}
                    </div>
                    
                    <!-- Platform -->
                    <div style="color: #64748b; font-size: 13px; margin-bottom: 8px;">
                        {{ $post['platform_display'] }}
                    </div>
                    
                    <!-- Content -->
                    <div style="color: #1e293b; font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
                        {{ Str::limit($post['content'], 200) }}
                    </div>
                    
                    <!-- Engagement Metrics and View Link -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <!-- Likes -->
                            <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #ef4444;">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>{{ number_format($post['likes']) }}</span>
                            </div>
                            
                            <!-- Comments -->
                            <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #64748b;">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>{{ number_format($post['comments']) }}</span>
                            </div>
                        </div>
                        
                        <!-- View Post Link -->
                        @if($post['url'])
                            <a href="{{ $post['url'] }}" target="_blank" style="color: #667eea; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                View Post →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="padding: 40px; text-align: center; color: #94a3b8;">
            <p style="margin: 0; font-size: 14px;">No recent posts found in platform data.</p>
        </div>
    @endif
</div>

