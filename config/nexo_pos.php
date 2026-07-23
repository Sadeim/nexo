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
];
