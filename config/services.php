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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'test_key' => env('RAZORPAY_TEST_KEY'),
        'test_secret' => env('RAZORPAY_TEST_SECRET'),
    ],

    'shiprocket' => [
        'email' => env('SHIPROCKET_EMAIL'),
        'password' => env('SHIPROCKET_PASSWORD'),
        'base_url' => env('SHIPROCKET_BASE_URL', 'https://apiv2.shiprocket.in/v1/external'),
        'pickup_pincode' => '125001'
    ],

    'instagram' => [
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        'user_id' => env('INSTAGRAM_USER_ID'),
        'graph_version' => env('INSTAGRAM_GRAPH_VERSION', 'v21.0'),
        'cache_ttl' => env('INSTAGRAM_CACHE_TTL', 3600),
        'post_limit' => env('INSTAGRAM_POST_LIMIT', 20),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
];
