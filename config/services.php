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
    'stripe' => [
        'key' => env('STRIPE_SECRET_KEY'),
        'public_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'webhook_return_secret' => env('STRIPE_WEBHOOK_SECRET_RETURN'),
        'payment_methods' => ['card', 'sepa_debit', 'paypal'],
        'webhooks' => [
            'lunar' => env('LUNAR_STRIPE_WEBHOOK_SECRET'),
        ],
    ],
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

    'branding' => [
        'logo_url' => 'https://piepjack-clothing.de/001-logo-new-gray-removebg.png',
        'logo_url_with_bg' => 'https://piepjack-clothing.de/logo_new_gray_bg_black.jpeg',
    ],

    'frontend_url' => env('APP_URL'),

    'sendcloud' => [
        'public_key' => env('SENDCLOUD_PUBLIC_KEY'),
        'secret_key' => env('SENDCLOUD_SECRET_KEY'),
        'webhook_secret' => env('SENDCLOUD_WEBHOOK_SECRET'),
        'default_return_method_id' => env('SENDCLOUD_DEFAULT_RETURN_METHOD_ID', '0'),
        'request_label' => env('SENDCLOUD_REQUEST_LABEL', true),
    ],

];
