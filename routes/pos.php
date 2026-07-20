<?php

use App\Http\Controllers\Pos\AuthController;
use App\Http\Controllers\Pos\CardPaymentController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\Pos\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| POS Routes (prefix: /pos)
|--------------------------------------------------------------------------
| Authentication is on the dedicated `pos` guard. The `pos` middleware alias
| (PosAuthenticate) requires an authenticated cashier with `pos.access`.
*/

Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {

    // Auth (public)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Protected POS terminal
    Route::group(['middleware' => 'pos'], function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');

        // Card (PlutoPay Terminal). start() drives the reader; status() is polled.
        Route::post('card/start', [CardPaymentController::class, 'start'])->name('card.start');
        Route::get('card/{transaction}/status', [CardPaymentController::class, 'status'])->name('card.status');
    });
});
