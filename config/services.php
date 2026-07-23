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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // إعدادات فيسبوك
    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT_URI'),
    ],

    // إعدادات جوجل
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    // إعدادات أبل
    'apple' => [
        'client_id'     => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect'      => env('APPLE_REDIRECT_URI'),
    ],

    /*
    | PlutoPay Terminal (in-person card). TEST MODE ONLY in this phase.
    | Base URL is under /api (NOT an api. subdomain) — confirmed from docs.
    | retrieve_path is the one endpoint the public docs don't spell out; it is
    | configurable so we can correct it without a code change.
    */
    'plutopay' => [
        'base_url'        => env('PLUTOPAY_BASE_URL', 'https://plutopayus.com/api'),
        'secret_key'      => env('PLUTOPAY_SECRET_KEY'),
        'publishable_key' => env('PLUTOPAY_PUBLISHABLE_KEY'),
        'webhook_secret'  => env('PLUTOPAY_WEBHOOK_SECRET'),
        'terminal_id'     => env('PLUTOPAY_TERMINAL_ID'),
        'reader_id'       => env('PLUTOPAY_READER_ID'),
        'currency'        => env('PLUTOPAY_CURRENCY', 'usd'),
        // GET single payment (reconciliation). {id} is replaced at runtime.
        'retrieve_path'   => env('PLUTOPAY_RETRIEVE_PATH', 'v1/terminal/payment/{id}'),
        // Card lower bound in cents (docs: minimum 50).
        'min_amount_cents' => 50,
    ],

];
