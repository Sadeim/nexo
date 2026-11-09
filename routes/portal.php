<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Support\Portal;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Employee portal
|--------------------------------------------------------------------------
| Barbers only, on the `employee` guard. Declared once and mounted twice:
|
|   /me     phones  — bottom tab bar, installable, service worker
|   /work   desks   — sidebar, timeline, tables
|
| Same controllers, same middleware, same guard and the SAME session cookie
| (see UseEmployeeSession), so signing in on one signs you in on the other.
| The route group hands each mount a `portal_layout` attribute, which is all
| the views need to pick their chrome — see App\Support\Portal.
|
| Read-only in this phase: no route changes a booking — the one POST that
| changes anything changes the signed-in barber's own password.
*/

$screens = function (string $variant) {
    return function () use ($variant) {

        Route::get('login', [AuthController::class, 'showLogin'])->name('login');

        /*
         * Five attempts a minute per IP, across BOTH mounts.
         *
         * A bare `throttle:5,1` keys on the request fingerprint, which
         * includes the URI — so /me/login and /work/login would get a bucket
         * each and the real limit would be ten. The named limiter in
         * AppServiceProvider keys on the IP alone.
         */
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:portal-login')
            ->name('login.post');

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Phones only. /work is not installable and has no worker, so there is
        // nothing at /work/sw.js to find.
        if ($variant === Portal::MOBILE) {
            Route::get('sw.js', [PortalController::class, 'serviceWorker'])->name('sw');
        }

        Route::middleware('employee')->group(function () {

            Route::get('/', [PortalController::class, 'today'])->name('today');
            Route::get('bookings', [PortalController::class, 'bookings'])->name('bookings');
            Route::get('earnings', [PortalController::class, 'earnings'])->name('earnings');
            Route::get('profile', [PortalController::class, 'profile'])->name('profile');

            /*
             * Changing a password. There is no screen of its own: the form
             * lives on Profile, which is the only place it is offered from.
             *
             * Nothing gates the screens above on having changed it. A barber
             * who has only ever had the password the shop typed still lands
             * on Today — being made to choose a new one before seeing a
             * single booking cost one barber his first afternoon with the
             * portal on 9 Nov 2026, and the gate bought nothing the owner
             * could not already see in the dashboard.
             */
            Route::post('password', [AuthController::class, 'updatePassword'])->name('password.update');
        });
    };
};

foreach (Portal::VERSIONS as $variant => $version) {
    Route::group([
        'prefix' => $version['path'],
        'as' => $version['name'],
        // Underscored: Route::getAction() reads its argument with dot
        // notation, so a 'portal.layout' key would never be found.
        'portal_layout' => $variant,
    ], $screens($variant));
}
