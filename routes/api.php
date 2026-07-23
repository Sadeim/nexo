<?php

use App\Http\Controllers\Api\Pos\V1\CardPaymentController;
use App\Http\Controllers\Api\Pos\V1\PosApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1', 'as' => 'api.v1.'], function () {
    // legacy placeholder
});

/*
|--------------------------------------------------------------------------
| Flutter POS API v1
|--------------------------------------------------------------------------
| Base URL: https://nexobarbers.com/api/pos/v1
| Auth: Bearer token from /login  →  Authorization: Bearer {token}
|
| NOTE: This is the mobile-app API. The browser-based Web POS lives at
|       /pos and uses session auth via routes/pos.php.
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/pos/v1', 'as' => 'pos.api.'], function () {
    // Public
    Route::get('health', [PosApiController::class, 'health'])->name('health');
    Route::get('app/version', [PosApiController::class, 'appVersion'])->name('app.version');
    Route::post('login', [PosApiController::class, 'login'])->name('login');

    // Authenticated (bearer token)
    Route::middleware('pos.api')->group(function () {
        Route::get('me', [PosApiController::class, 'me'])->name('me');
        Route::post('logout', [PosApiController::class, 'logout'])->name('logout');

        Route::get('employees', [PosApiController::class, 'employees'])->name('employees');
        Route::get('services', [PosApiController::class, 'services'])->name('services');

        Route::post('orders', [PosApiController::class, 'storeOrder'])->name('orders.store');
        Route::get('orders/{id}', [PosApiController::class, 'showOrder'])->name('orders.show');
        Route::post('orders/{id}/email-receipt', [PosApiController::class, 'emailReceipt'])->name('orders.email-receipt');

        // Card (PlutoPay Terminal) — server-driven flow, sandbox only for now.
        Route::get('card/readers', [CardPaymentController::class, 'readers'])->name('card.readers');
        Route::post('card/start', [CardPaymentController::class, 'start'])->name('card.start');
        Route::get('card/status/{id}', [CardPaymentController::class, 'status'])->name('card.status');
        Route::post('card/simulate/{id}', [CardPaymentController::class, 'simulate'])->name('card.simulate');
    });
});
