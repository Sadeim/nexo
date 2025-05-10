<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MessageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

    // Auth::routes();
    // Route::get('verify_otp_form', [RegisterController::class, 'showOtpForm'])->name('verify_otp_form');
    // Route::post('verify_otp', [RegisterController::class, 'verifyOtp'])->name('verify_otp');
    // Route::post('resend_otp', [RegisterController::class, 'resendOtp'])->name('otp.resend');
    // Route::get('password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    // Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    
    // Route::post('login', [LoginController::class, 'login'])->name('login');
    // Route::post('register', [RegisterController::class, 'register'])->name('register');
    // Route::post('send_reset_otp', [ForgotPasswordController::class, 'sendResetOTPCode'])->name('send_reset_otp');
    // Route::post('reset_password', [ForgotPasswordController::class, 'resetPassword'])->name('reset_password');
    
    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::get('about_us', [HomeController::class, 'aboutUs'])->name('about_us');
    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('search', [BlogController::class, 'search'])->name('blog.search');
    Route::get('contact_us', [HomeController::class, 'contactUs'])->name('contact');
    Route::post('contact_us/store', [HomeController::class, 'contactStore'])->name('contact.submit');
    Route::post('consult', [HomeController::class, 'storeConsultation'])->name('consult.store');
    Route::post('subscribe', [HomeController::class, 'storeNewsletter'])->name('newsletter.subscribe');
    Route::post('bookings', [HomeController::class, 'storeBooking'])->name('bookings.store');
