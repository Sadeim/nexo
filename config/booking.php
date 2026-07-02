<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bookings Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch for the public booking feature. When false, all BOOK NOW
    | buttons are hidden and the booking endpoints reject requests. Toggle via
    | the BOOKINGS_ENABLED variable in .env, then run `php artisan config:cache`.
    |
    */

    'enabled' => env('BOOKINGS_ENABLED', false),
];
