<?php

use App\Http\Controllers\Auth\OtpLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        OtpLoginController::class,
        'showLoginForm',
    ])->name('login');

    Route::post('/login/send-otp', [
        OtpLoginController::class,
        'sendOtp',
    ])->middleware('throttle:otp-send')
        ->name('login.send-otp');

    Route::get('/login/verify', [
        OtpLoginController::class,
        'showVerifyForm',
    ])->name('login.verify');

    Route::post('/login/verify', [
        OtpLoginController::class,
        'verifyOtp',
    ])->middleware('throttle:otp-verify')
        ->name('login.verify.submit');
});

Route::post('/logout', [
    OtpLoginController::class,
    'logout',
])->middleware('auth')->name('logout');


if (app()->environment('local')) {
    Route::view('/preview/auth/login', 'auth.login', [
        'preview' => true,
    ])->name('preview.auth.login');

    Route::view('/preview/auth/verify', 'auth.verify', [
        'preview' => true,
    ])->name('preview.auth.verify');
}
