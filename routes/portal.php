<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Portal\ShortLinkController;
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

        /*
         * Phones only. /work is not installable and has no worker, so there
         * is nothing at /work/sw.js or /work/manifest.webmanifest to find.
         *
         * Both are outside the `employee` group below on purpose. A worker
         * that 302s to the login page is a worker that never registers, and a
         * manifest behind a login is one the browser fetches, is redirected
         * from, and silently ignores — leaving the portal uninstallable for
         * anyone not already signed in. Neither file contains any data.
         */
        if ($variant === Portal::MOBILE) {
            Route::get('sw.js', [PortalController::class, 'serviceWorker'])->name('sw');

            /*
             * Served here rather than as a static file so the icon URLs can
             * carry their own fingerprints — see PortalController::manifest.
             * Living under /me/ also puts it inside the scope it declares,
             * which is one less thing for a browser to be fussy about.
             */
            Route::get('manifest.webmanifest', [PortalController::class, 'manifest'])->name('manifest');
        }

        Route::middleware('employee')->group(function () {

            Route::get('/', [PortalController::class, 'today'])->name('today');
            Route::get('bookings', [PortalController::class, 'bookings'])->name('bookings');
            Route::get('earnings', [PortalController::class, 'earnings'])->name('earnings');
            Route::get('profile', [PortalController::class, 'profile'])->name('profile');

            /*
             * All Bookings — the whole shop's day, for a manager.
             *
             * The only portal screen that reads beyond the signed-in
             * employee's own work, and the only one with a gate in front of
             * it. Mounted in both versions like everything else, so the
             * desk and the phone cannot drift apart.
             */
            Route::get('team', [PortalController::class, 'team'])
                ->middleware('employee.manager')
                ->name('team');

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

/*
 * The short link. Outside the loop because it belongs to neither mount: it is
 * one URL that lands on the phone version, whoever follows it.
 *
 * The pattern is deliberately narrow rather than '.*'. Every portal path is
 * letters, digits and the odd dot or dash — /app/bookings, /app/sw.js — so
 * anything else is not a portal URL that lost its prefix, and a 404 is a
 * better answer than a redirect built out of it.
 */
Route::get('app/{path?}', ShortLinkController::class)
    ->where('path', '[A-Za-z0-9/_.-]*')
    ->name('app.shortlink');
