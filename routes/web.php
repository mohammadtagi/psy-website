<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpController::class, 'showLogin'])->name('login');
    Route::post('/otp/send', [OtpController::class, 'send'])
        ->middleware('throttle:otp-send')->name('otp.send');
    Route::get('/verify', [OtpController::class, 'showVerify'])->name('verify');
    Route::post('/otp/verify', [OtpController::class, 'verify'])
        ->middleware('throttle:otp-verify')->name('otp.verify');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [OtpController::class, 'logout'])->name('logout');
});
