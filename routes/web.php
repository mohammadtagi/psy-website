<?php

use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Psychologist\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Psychologist\DashboardController as PsychologistDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Psychologist\AvailabilityController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Psychologist\AppointmentController as PsychologistAppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Psychologist\ClinicalRecordController;
use App\Http\Controllers\Psychologist\ClinicalNoteController;

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


Route::get('/booking', [
    BookingController::class,
    'index',
])->name('booking.index');

Route::get('/booking/{date}', [
    BookingController::class,
    'show',
])->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('booking.show');

Route::post('/booking/{date}', [
    BookingController::class,
    'store',
])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->middleware(['auth', 'role:client'])
    ->name('booking.store');




Route::middleware(['auth', 'role:psychologist'])
    ->prefix('psychologist')
    ->name('psychologist.')
    ->group(function (): void {
        Route::get('/', [PsychologistDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/availabilities', [
            AvailabilityController::class,
            'index',
        ])->name('availabilities.index');

        Route::post('/availabilities', [
            AvailabilityController::class,
            'store',
        ])->name('availabilities.store');

        Route::delete('/availabilities/{availability}', [
            AvailabilityController::class,
            'destroy',
        ])->name('availabilities.destroy');
        Route::get('/appointments', [
            PsychologistAppointmentController::class,
            'index',
        ])->name('appointments.index');

        Route::post('/appointments/{appointment}/cancel', [
            PsychologistAppointmentController::class,
            'cancel',
        ])->name('appointments.cancel');

        Route::get('/clients', [ClientController::class, 'index'])
            ->name('clients.index');

        Route::post('/clients', [ClientController::class, 'store'])
            ->name('clients.store');

        Route::get('/clients/{client}/clinical-record', [
            ClinicalRecordController::class,
            'show',
        ])->name('clinical-records.show');

        Route::get('/clients/{client}/clinical-record/create', [
            ClinicalRecordController::class,
            'create',
        ])->name('clinical-records.create');

        Route::post('/clients/{client}/clinical-record', [
            ClinicalRecordController::class,
            'store',
        ])->name('clinical-records.store');

        Route::get('/clinical-records/{clinicalRecord}/edit', [
            ClinicalRecordController::class,
            'edit',
        ])->name('clinical-records.edit');

        Route::put('/clinical-records/{clinicalRecord}', [
            ClinicalRecordController::class,
            'update',
        ])->name('clinical-records.update');
        Route::get('/clinical-records/{clinicalRecord}/notes/create', [
            ClinicalNoteController::class,
            'create',
        ])->name('clinical-notes.create');

        Route::post('/clinical-records/{clinicalRecord}/notes', [
            ClinicalNoteController::class,
            'store',
        ])->name('clinical-notes.store');

        Route::get('/clinical-notes/{clinicalNote}/edit', [
            ClinicalNoteController::class,
            'edit',
        ])->name('clinical-notes.edit');

        Route::put('/clinical-notes/{clinicalNote}', [
            ClinicalNoteController::class,
            'update',
        ])->name('clinical-notes.update');

        Route::delete('/clinical-notes/{clinicalNote}', [
            ClinicalNoteController::class,
            'destroy',
        ])->name('clinical-notes.destroy');

    });


Route::get('/booking/confirmation/{appointment}', [
    BookingController::class,
    'confirmation',
])->middleware('auth')
    ->name('booking.confirmation');


    Route::middleware(['auth', 'role:client'])
        ->get('/profile', [ClientProfileController::class, 'show'])
       ->name('client.profile');

Route::middleware(['auth', 'role:client'])
    ->prefix('appointments')
    ->name('client.appointments.')
    ->group(function (): void {
        Route::get('/', [
            ClientAppointmentController::class,
            'index',
        ])->name('index');

        Route::post('/{appointment}/cancel', [
            ClientAppointmentController::class,
            'cancel',
        ])->name('cancel');
    });

// شروع پرداخت: فقط کاربر لاگین‌شده (کلاینت)
Route::post('/appointments/{appointment}/payment', [
    PaymentController::class,
    'pay',
])->middleware(['auth', 'role:client'])
    ->name('payments.pay');

// callback زرین‌پال: عمومی (بدون auth)
Route::get('/payments/zarinpal/callback', [
    PaymentController::class,
    'callback',
])->name('payments.zarinpal.callback');

