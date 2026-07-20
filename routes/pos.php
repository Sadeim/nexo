<?php

use App\Http\Controllers\Pos\AuthController;
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
    });
});
