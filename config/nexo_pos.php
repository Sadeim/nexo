<?php

/*
|--------------------------------------------------------------------------
| Nexo POS (Flutter mobile app) OTA configuration
|--------------------------------------------------------------------------
| These values back the /api/pos/v1/app/version endpoint that the tablet
| app hits on launch. Bump `version_code` (and set `apk_url`) after
| uploading a new APK to the public downloads folder, then run
| `php artisan config:cache` on the server.
*/

return [
    'version_code'  => (int) env('NEXO_POS_VERSION_CODE', 1),
    'version_name'  => env('NEXO_POS_VERSION_NAME', '1.0.0'),
    'apk_url'       => env('NEXO_POS_APK_URL'),
    'mandatory'     => (bool) env('NEXO_POS_MANDATORY', false),
    'release_notes' => env('NEXO_POS_RELEASE_NOTES', 'Initial release'),

    /*
    | PlutoPay Terminal — dedicated to the Flutter POS. Isolated from the
    | Web POS's `services.plutopay` block so both flows can coexist without
    | stepping on each other. TEST MODE ONLY in this phase.
    */
    'plutopay' => [
        'base_url'         => env('NEXO_POS_PLUTOPAY_BASE_URL', 'https://plutopayus.com/api'),
        'secret_key'       => env('NEXO_POS_PLUTOPAY_SECRET_KEY'),
        'publishable_key'  => env('NEXO_POS_PLUTOPAY_PUBLISHABLE_KEY'),
        'webhook_secret'   => env('NEXO_POS_PLUTOPAY_WEBHOOK_SECRET'),
        'terminal_id'      => env('NEXO_POS_PLUTOPAY_TERMINAL_ID'),
        'reader_id'        => env('NEXO_POS_PLUTOPAY_READER_ID'),
        'currency'         => env('NEXO_POS_PLUTOPAY_CURRENCY', 'usd'),
        'min_amount_cents' => (int) env('NEXO_POS_PLUTOPAY_MIN_CENTS', 50),
        // Set to false to hard-disable live keys even if one is in .env.
        'allow_live'       => (bool) env('NEXO_POS_PLUTOPAY_ALLOW_LIVE', true),
    ],
];
