<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'ssl_verify' => env('GEMINI_SSL_VERIFY', true),
        'max_retries' => env('GEMINI_MAX_RETRIES', 2),
        'initial_token_limit' => env('GEMINI_INITIAL_TOKEN_LIMIT', 8192),
        'reduced_token_limit' => env('GEMINI_REDUCED_TOKEN_LIMIT', 4096),
        'enable_truncation_detection' => env('GEMINI_ENABLE_TRUNCATION_DETECTION', true),
    ],

        'chatgpt' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o'),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 6000),
            'temperature' => env('OPENAI_TEMPERATURE', 1),
            'timeout' => env('OPENAI_TIMEOUT', 300),
            'ssl_verify' => env('OPENAI_SSL_VERIFY', true),
        ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
 
    'apify' => [
        'api_token' => env('APIFY_API_TOKEN'),
        'timeout' => env('APIFY_TIMEOUT', 300),
        'ssl_verify' => env('APIFY_SSL_VERIFY', false),
        'facebook_actor_id' => env('APIFY_FACEBOOK_ACTOR_ID', 'apify/facebook-posts-scraper'),
        'instagram_actor_id' => env('APIFY_INSTAGRAM_ACTOR_ID', 'apify/instagram-post-scraper'),
        'tiktok_actor_id' => env('APIFY_TIKTOK_ACTOR_ID', 'clockworks/tiktok-profile-scraper'),
        'twitter_actor_id' => env('APIFY_TWITTER_ACTOR_ID', 'apidojo/tweet-scraper'),
    ],

];
