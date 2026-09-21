<?php

use App\Http\Controllers\Auth\OtpLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Psychologist\DashboardController as PsychologistDashboardController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        OtpLoginController::class,
        'showMobileForm',
    ])->name('auth.login');

    Route::post('/login', [
        OtpLoginController::class,
        'sendOtp',
    ])->middleware('throttle:5,1')
        ->name('auth.send-otp');

    Route::get('/login/verify', [
        OtpLoginController::class,
        'showOtpForm',
    ])->name('auth.otp.form');

    Route::post('/login/verify', [
        OtpLoginController::class,
        'verifyOtp',
    ])->middleware('throttle:10,1')
        ->name('auth.verify-otp');

    Route::post('/login/resend', [
        OtpLoginController::class,
        'resendOtp',
    ])->middleware('throttle:10,1')
        ->name('auth.resend-otp');
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

Route::get('/', function () {
    return view('home');
})->name('home');


Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
    });

Route::middleware(['auth', 'role:psychologist'])
    ->prefix('psychologist')
    ->name('psychologist.')
    ->group(function (): void {
        Route::get('/', [PsychologistDashboardController::class, 'index'])
            ->name('dashboard');
    });
