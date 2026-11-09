<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\PortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Employee portal (prefix: /me)
|--------------------------------------------------------------------------
| Barbers only, on the `employee` guard, with its own session cookie (see
| UseEmployeeSession). Nothing in here is reachable with an admin session,
| and nothing here grants one.
|
| Read-only in this phase: no route changes a booking.
*/

Route::group(['prefix' => 'me', 'as' => 'portal.'], function () {

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');

    // Five attempts a minute per IP. Login is the only unauthenticated POST
    // in the portal and the only place a password can be guessed.
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Unauthenticated: a service worker that redirects to login never installs.
    // It carries no data — see the handler and sw.js itself.
    Route::get('sw.js', [PortalController::class, 'serviceWorker'])->name('sw');

    Route::middleware('employee')->group(function () {

        // Reachable while must_change_password is still set — this is the one
        // screen the forced-change gate deliberately does not cover.
        Route::get('password', [AuthController::class, 'editPassword'])->name('password.edit');
        Route::post('password', [AuthController::class, 'updatePassword'])->name('password.update');

        Route::middleware('employee.password')->group(function () {
            Route::get('/', [PortalController::class, 'today'])->name('today');
            Route::get('bookings', [PortalController::class, 'bookings'])->name('bookings');
            Route::get('earnings', [PortalController::class, 'earnings'])->name('earnings');
            Route::get('profile', [PortalController::class, 'profile'])->name('profile');
        });
    });
});
