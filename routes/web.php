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
Route::get('/service', [HomeController::class, 'services'])->name('services');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');

Route::get('contact_us', [HomeController::class, 'contactUs'])->name('contact');
Route::post('contact_us/store', [HomeController::class, 'contactStore'])->name('contact.submit');
Route::post('consult', [HomeController::class, 'storeConsultation'])->name('consult.store');
Route::post('subscribe', [HomeController::class, 'storeNewsletter'])->name('newsletter.subscribe');

Route::get('services', [HomeController::class, 'getServices'])->name('services.get');
Route::post('bookings', [HomeController::class, 'storeBooking'])->name('bookings.store');
Route::get('/bookings/available-slots', [HomeController::class, 'getAvailableSlots'])->name('bookings.booked-slots');

