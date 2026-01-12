<?php

/**
 * Script to run AI analysis on Twitter/X data
 * Usage: php run_twitter_analysis.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SocialMediaService;
use App\Services\AIServiceFactory;
use App\Services\AnalyticsService;
use App\Models\SocialMediaAnalysis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Twitter data provided by user (new format with user profile)
$twitterDataRaw = json_decode(file_get_contents('php://stdin'), true);

// If no stdin data, use the provided data
if (empty($twitterDataRaw)) {
    // You can paste the JSON data here or pass via stdin
    die("Please provide Twitter data via stdin: echo 'JSON_DATA' | php run_twitter_analysis.php\n");
}

$username = 'hannahyeoh';
$analysisType = 'professional'; // or 'political'

echo "Formatting Twitter data for username: {$username}\n";

// Extract user profile from first tweet
$userProfile = null;
if (!empty($twitterDataRaw[0]['user'])) {
    $user = $twitterDataRaw[0]['user'];
    $userProfile = [
        'id' => $user['user_id_str'] ?? null,
        'username' => $username,
        'name' => $user['name'] ?? 'Hannah Yeoh',
        'bio' => $user['description'] ?? null,
        'followers_count' => $user['followers_count'] ?? 0,
        'following_count' => $user['friends_count'] ?? 0,
        'tweets_count' => $user['statuses_count'] ?? 0,
        'profile_url' => $user['url'] ?? "https://twitter.com/{$username}",
    ];
}

// Format tweets to match expected structure
$formattedTweets = [];
$totalLikes = 0;
$totalRetweets = 0;
$totalReplies = 0;
$totalQuotes = 0;
$totalEngagement = 0;

foreach ($twitterDataRaw as $tweet) {
    // Handle different field names
    $likes = max(0, (int)($tweet['favorite_count'] ?? $tweet['likeCount'] ?? 0));
    $retweets = max(0, (int)($tweet['retweet_count'] ?? $tweet['retweetCount'] ?? 0));
    $replies = max(0, (int)($tweet['reply_count'] ?? $tweet['replyCount'] ?? 0));
    $quotes = max(0, (int)($tweet['quote_count'] ?? $tweet['quoteCount'] ?? 0));
    
    $totalEngagement += ($likes + $retweets + $replies + $quotes);
    
    $totalLikes += $likes;
    $totalRetweets += $retweets;
    $totalReplies += $replies;
    $totalQuotes += $quotes;
    
    // Get tweet text
    $text = $tweet['full_text'] ?? $tweet['text'] ?? '';
    
    // Get tweet URL
    $tweetUrl = null;
    if (isset($tweet['id_str'])) {
        $tweetUrl = "https://twitter.com/{$username}/status/{$tweet['id_str']}";
    } elseif (isset($tweet['id'])) {
        $tweetUrl = "https://twitter.com/{$username}/status/{$tweet['id']}";
    }
    
    $formattedTweets[] = [
        'id' => $tweet['id_str'] ?? $tweet['id'] ?? null,
        'text' => $text,
        'created_time' => $tweet['created_at'] ?? $tweet['createdAt'] ?? null,
        'likes' => $likes,
        'like_count' => $likes,
        'retweets' => $retweets,
        'retweet_count' => $retweets,
        'replies' => $replies,
        'reply_count' => $replies,
        'quotes' => $quotes,
        'quote_count' => $quotes,
        'total_engagement' => $likes + $retweets + $replies + $quotes,
        'url' => $tweetUrl,
        'permalink' => $tweetUrl,
    ];
}

$tweetCount = count($formattedTweets);
$followersCount = $userProfile['followers_count'] ?? 0;
$averageLikes = $tweetCount > 0 ? round($totalLikes / $tweetCount, 2) : 0;
$averageRetweets = $tweetCount > 0 ? round($totalRetweets / $tweetCount, 2) : 0;
$averageReplies = $tweetCount > 0 ? round($totalReplies / $tweetCount, 2) : 0;
$averageQuotes = $tweetCount > 0 ? round($totalQuotes / $tweetCount, 2) : 0;
$averageEngagement = $tweetCount > 0 ? round($totalEngagement / $tweetCount, 2) : 0;

// Calculate engagement rate
$engagementRate = ($followersCount > 0 && $averageEngagement > 0) 
    ? round(($averageEngagement / $followersCount) * 100, 2) 
    : 0;

// Format platform data structure
$platformData = [
    'twitter' => [
        'data' => [
            'username' => $username,
            'name' => $userProfile['name'] ?? 'Hannah Yeoh',
            'bio' => $userProfile['bio'] ?? null,
            'followers_count' => $followersCount,
            'following_count' => $userProfile['following_count'] ?? 0,
            'tweets_count' => $userProfile['tweets_count'] ?? $tweetCount,
            'profile_url' => $userProfile['profile_url'] ?? "https://twitter.com/{$username}",
            'recent_tweets' => $formattedTweets,
            'engagement' => [
                'total_engagement' => $totalEngagement,
                'average_engagement_per_post' => $averageEngagement,
                'engagement_rate' => $engagementRate,
                'average_likes' => $averageLikes,
                'average_retweets' => $averageRetweets,
                'average_replies' => $averageReplies,
                'average_quotes' => $averageQuotes,
                'total_posts_analyzed' => $tweetCount,
                'total_likes' => $totalLikes,
                'total_retweets' => $totalRetweets,
                'total_replies' => $totalReplies,
                'total_quotes' => $totalQuotes,
            ],
            'stats' => [
                'total_followers' => $followersCount,
                'total_following' => $userProfile['following_count'] ?? 0,
                'total_tweets' => $userProfile['tweets_count'] ?? $tweetCount,
                'recent_tweets_count' => $tweetCount,
            ],
        ]
    ]
];

echo "Formatted {$tweetCount} tweets\n";
echo "Followers: " . number_format($followersCount) . "\n";
echo "Total engagement: {$totalEngagement}\n";
echo "Average engagement per tweet: {$averageEngagement}\n";
echo "Engagement rate: {$engagementRate}%\n\n";

// Get the first user (you may want to specify a user ID)
$user = \App\Models\User::first();
if (!$user) {
    die("Error: No users found in database. Please create a user first.\n");
}

// Authenticate as the user
Auth::login($user);

echo "Authenticated as user: {$user->email} (ID: {$user->id})\n\n";

// Prepare analysis text
$controller = new \App\Http\Controllers\SocialMediaController(
    app(\App\Services\SocialMediaService::class),
    app(\App\Services\AnalyticsService::class)
);

$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('prepareAnalysisText');
$method->setAccessible(true);

$analysisText = $method->invoke($controller, $platformData, $analysisType);

echo "Analysis text prepared (length: " . strlen($analysisText) . " characters)\n\n";
echo "Starting AI analysis...\n";
echo "Analysis type: {$analysisType}\n\n";

// Start analytics tracking
$analyticsService = app(\App\Services\AnalyticsService::class);
$analytics = $analyticsService->startAnalysisWithoutPrediction($user->id, [
    'text' => $analysisText,
    'analysis_type' => 'social-media-analysis',
]);

// Get AI service
$aiService = AIServiceFactory::create();

$startTime = microtime(true);

// Perform AI analysis
try {
    $result = $aiService->analyzeText(
        $analysisText,
        'social-media-analysis',
        null,
        null,
        $analytics,
        null
    );
    
    $processingTime = round(microtime(true) - $startTime, 3);
    
    // Complete analytics tracking
    if ($analytics) {
        $analyticsService->completeAnalysis($analytics, [
            'total_processing_time' => $processingTime,
            'api_error_message' => isset($result['title']) ? null : 'Analysis failed'
        ]);
    }
    
    // Ensure analysis_type is set in the result
    if (is_array($result)) {
        $result['analysis_type'] = $analysisType;
    }
    
    // Create or update analysis record
    $analysis = SocialMediaAnalysis::updateOrCreate(
        [
            'username' => $username,
            'user_id' => $user->id,
        ],
        [
            'platform_data' => $platformData,
            'ai_analysis' => $result,
            'status' => isset($result['title']) ? SocialMediaAnalysis::STATUS_COMPLETED : SocialMediaAnalysis::STATUS_FAILED,
            'processing_time' => $processingTime,
            'model_used' => AIServiceFactory::getCurrentProvider()
        ]
    );
    
    echo "Analysis completed!\n";
    echo "Processing time: {$processingTime} seconds\n";
    echo "Analysis ID: {$analysis->id}\n";
    echo "User ID: {$user->id}\n";
    echo "Username: {$user->email}\n\n";
    
    if (isset($result['title'])) {
        echo "Title: {$result['title']}\n";
        echo "Status: {$analysis->status}\n";
        echo "Analysis saved successfully!\n\n";
        
        // Get the base URL
        $baseUrl = config('app.url', 'http://localhost');
        $viewUrl = "{$baseUrl}/social-media/{$analysis->id}";
        
        echo "==========================================\n";
        echo "VIEW THE ANALYSIS:\n";
        echo "==========================================\n";
        echo "URL: {$viewUrl}\n";
        echo "Route: /social-media/{$analysis->id}\n";
        echo "\n";
        echo "IMPORTANT: You must be logged in as user ID {$user->id} ({$user->email})\n";
        echo "to view this analysis.\n";
        echo "\n";
        echo "To view in your browser:\n";
        echo "1. Log in to your application\n";
        echo "2. Navigate to: {$viewUrl}\n";
        echo "   OR go to Social Media > History and find '{$username}'\n";
        echo "\n";
        echo "Analysis Summary:\n";
        if (isset($result['executive_summary'])) {
            echo "Executive Summary: " . substr($result['executive_summary'], 0, 200) . "...\n";
        }
        if (isset($result['political_profile'])) {
            echo "Political Profile section: Yes\n";
        }
        if (isset($result['risk_assessment'])) {
            echo "Risk Assessment section: Yes\n";
        }
    } else {
        echo "Error: Analysis failed\n";
        if (isset($result['error'])) {
            echo "Error message: {$result['error']}\n";
        }
        print_r($result);
    }
    
} catch (\Exception $e) {
    $processingTime = round(microtime(true) - $startTime, 3);
    
    // Complete analytics tracking with error
    if ($analytics) {
        $analyticsService->completeAnalysis($analytics, [
            'total_processing_time' => $processingTime,
            'api_error_message' => 'Analysis failed: ' . $e->getMessage()
        ]);
    }
    
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
