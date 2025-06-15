<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

// Show registration form (GET)
Route::get('/', function () {
    return view('register');
})->name('register.form');

// Handle registration form submission (POST)
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Show OTP verification form (GET)
Route::get('/otp-verify', function () {
    return view('otp');
})->name('otp.verify');

// Handle OTP verification form submission (POST)
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('verify.otp');

// Resend OTP (POST)
Route::post('/resend-otp', [RegisterController::class, 'sendotp'])->name('resend.otp');