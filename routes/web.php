<?php

use Illuminate\Support\Facades\Route;

// Authentication Controllers
use App\Http\Controllers\Auth\OtpLoginController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Psychologist Controllers
use App\Http\Controllers\Psychologist\DashboardController as PsychologistDashboardController;
use App\Http\Controllers\Psychologist\ClientController;
use App\Http\Controllers\Psychologist\AvailabilityController;
use App\Http\Controllers\Psychologist\AppointmentController as PsychologistAppointmentController;
use App\Http\Controllers\Psychologist\AppointmentSessionController as PsychologistAppointmentSessionController;
use App\Http\Controllers\Psychologist\ClinicalRecordController;
use App\Http\Controllers\Psychologist\ClinicalNoteController;
use App\Http\Controllers\Psychologist\ClinicalAttachmentController;
use App\Http\Controllers\Psychologist\TreatmentPlanController;
use App\Http\Controllers\Psychologist\DiscountCodeController;

// Client Controllers
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\AppointmentSessionController as ClientAppointmentSessionController;
use App\Http\Controllers\Client\ClientSessionNoteController;

// General / Booking / Payment Controllers
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingDiscountController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Public & Static Website Pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'website.pages.home')->name('home');
Route::view('/contact-us', 'website.pages.contact')->name('website.contact');
Route::view('/about-us', 'website.pages.about')->name('website.about');
Route::view('/services', 'website.pages.services')->name('website.services');
Route::view('/faq', 'website.pages.faq')->name('website.faq');

/*
|--------------------------------------------------------------------------
| Local Environment Previews
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::view('/preview/auth/login', 'auth.login', ['preview' => true])->name('preview.auth.login');
    Route::view('/preview/auth/verify', 'auth.verify', ['preview' => true])->name('preview.auth.verify');
}

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest / Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpLoginController::class, 'showMobileForm'])->name('auth.login');
    Route::post('/login', [OtpLoginController::class, 'sendOtp'])->middleware('throttle:5,1')->name('auth.send-otp');
    Route::get('/login/verify', [OtpLoginController::class, 'showOtpForm'])->name('auth.otp.form');
    Route::post('/login/verify', [OtpLoginController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('auth.verify-otp');
    Route::post('/login/resend', [OtpLoginController::class, 'resendOtp'])->middleware('throttle:10,1')->name('auth.resend-otp');
});

Route::post('/logout', [OtpLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Public & Shared Booking & Payment Routes
|--------------------------------------------------------------------------
*/
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

Route::get('/booking/{date}', [BookingController::class, 'show'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('booking.show');

Route::post('/booking/{date}', [BookingController::class, 'store'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->middleware(['auth', 'role:client'])
    ->name('booking.store');

Route::get('/booking/confirmation/{appointment}', [BookingController::class, 'confirmation'])
    ->middleware('auth')
    ->name('booking.confirmation');

// درگاه پرداخت - کال‌بک عمومی
Route::get('/payments/zarinpal/callback', [PaymentController::class, 'callback'])
    ->name('payments.zarinpal.callback');

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Client Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function (): void {

    // Dashboard
    Route::get('/client', [ClientDashboardController::class, 'index'])->name('client.dashboard');

    // Profile
    Route::prefix('profile')->name('client.profile.')->group(function (): void {
        Route::get('/', [ClientProfileController::class, 'show'])->name('show');
        Route::put('/', [ClientProfileController::class, 'update'])->name('update');
    });

    // Appointments Management
    Route::prefix('appointments')->name('client.appointments.')->group(function (): void {
        Route::get('/', [ClientAppointmentController::class, 'index'])->name('index');
        Route::post('/{appointment}/cancel', [ClientAppointmentController::class, 'cancel'])->name('cancel');
    });

    // Booking Discounts & Confirmations
    Route::prefix('booking/appointments/{appointment}')
        ->where(['appointment' => '[0-9]+'])
        ->name('booking.')
        ->group(function (): void {
            Route::post('/discount', [BookingDiscountController::class, 'store'])->name('discount.store');
            Route::delete('/discount', [BookingDiscountController::class, 'destroy'])->name('discount.destroy');
            Route::post('/confirm-free', [BookingDiscountController::class, 'confirmFree'])->name('confirm-free');
        });

    // Payment Initiation
    Route::post('/appointments/{appointment}/payment', [PaymentController::class, 'pay'])->name('payments.pay');

    // Client Appointment Sessions & Notes
    Route::prefix('client/appointments/{appointment}/session')->name('client.appointments.session.')->group(function (): void {
        Route::get('/', [ClientAppointmentSessionController::class, 'show'])->name('show');
        Route::post('/notes', [ClientSessionNoteController::class, 'store'])->name('notes.store');
        Route::put('/notes/{note}', [ClientSessionNoteController::class, 'update'])->name('notes.update');
        Route::delete('/notes/{note}', [ClientSessionNoteController::class, 'destroy'])->name('notes.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Psychologist Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:psychologist'])
    ->prefix('psychologist')
    ->name('psychologist.')
    ->group(function (): void {

        // Dashboard
        Route::get('/', [PsychologistDashboardController::class, 'index'])->name('dashboard');

        // Availabilities
        Route::get('/availabilities', [AvailabilityController::class, 'index'])->name('availabilities.index');
        Route::post('/availabilities', [AvailabilityController::class, 'store'])->name('availabilities.store');
        Route::delete('/availabilities/{availability}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');

        // Appointments
        Route::get('/appointments', [PsychologistAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments/{appointment}/cancel', [PsychologistAppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Appointment Sessions
        Route::get('/appointments/{appointment}/session', [PsychologistAppointmentSessionController::class, 'show'])->name('appointments.session.show');
        Route::put('/appointments/{appointment}/session', [PsychologistAppointmentSessionController::class, 'update'])->name('appointments.session.update');

        // Clients
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

        // Clinical Records & Manual Appointments
        Route::post('/clients/{client}/manual-appointments', [ClinicalRecordController::class, 'storeManualAppointment'])->name('clinical-records.manual-appointments.store');
        Route::get('/clients/{client}/clinical-record', [ClinicalRecordController::class, 'show'])->name('clinical-records.show');
        Route::get('/clients/{client}/clinical-record/create', [ClinicalRecordController::class, 'create'])->name('clinical-records.create');
        Route::post('/clients/{client}/clinical-record', [ClinicalRecordController::class, 'store'])->name('clinical-records.store');
        Route::get('/clinical-records/{clinicalRecord}/edit', [ClinicalRecordController::class, 'edit'])->name('clinical-records.edit');
        Route::put('/clinical-records/{clinicalRecord}', [ClinicalRecordController::class, 'update'])->name('clinical-records.update');

        // Treatment Plans
        Route::get('/clinical-records/{clinicalRecord}/treatment-plan/create', [TreatmentPlanController::class, 'create'])->name('treatment-plans.create');
        Route::post('/clinical-records/{clinicalRecord}/treatment-plan', [TreatmentPlanController::class, 'store'])->name('treatment-plans.store');
        Route::get('/treatment-plans/{treatmentPlan}/edit', [TreatmentPlanController::class, 'edit'])->name('treatment-plans.edit');
        Route::put('/treatment-plans/{treatmentPlan}', [TreatmentPlanController::class, 'update'])->name('treatment-plans.update');
        Route::delete('/treatment-plans/{treatmentPlan}', [TreatmentPlanController::class, 'destroy'])->name('treatment-plans.destroy');
        Route::patch('/treatment-plans/{treatmentPlan}/archive', [TreatmentPlanController::class, 'archive'])->name('treatment-plans.archive');
        Route::post('/treatment-plans/{treatmentPlan}/activate', [TreatmentPlanController::class, 'activate'])->name('treatment-plans.activate');

        // Clinical Notes
        Route::get('/clinical-records/{clinicalRecord}/notes/create', [ClinicalNoteController::class, 'create'])->name('clinical-notes.create');
        Route::post('/clinical-records/{clinicalRecord}/notes', [ClinicalNoteController::class, 'store'])->name('clinical-notes.store');
        Route::get('/clinical-notes/{clinicalNote}/edit', [ClinicalNoteController::class, 'edit'])->name('clinical-notes.edit');
        Route::put('/clinical-notes/{clinicalNote}', [ClinicalNoteController::class, 'update'])->name('clinical-notes.update');
        Route::delete('/clinical-notes/{clinicalNote}', [ClinicalNoteController::class, 'destroy'])->name('clinical-notes.destroy');

        // Clinical Attachments
        Route::get('/clinical-records/{clinicalRecord}/attachments', [ClinicalAttachmentController::class, 'index'])->name('clinical-attachments.index');
        Route::post('/clinical-records/{clinicalRecord}/attachments', [ClinicalAttachmentController::class, 'store'])->name('clinical-attachments.store');
        Route::get('/clinical-attachments/{clinicalAttachment}/download', [ClinicalAttachmentController::class, 'download'])->name('clinical-attachments.download');
        Route::delete('/clinical-attachments/{clinicalAttachment}', [ClinicalAttachmentController::class, 'destroy'])->name('clinical-attachments.destroy');

        // Discounts Management
        Route::prefix('discounts')->name('discounts.')->group(function (): void {
            Route::get('/', [DiscountCodeController::class, 'index'])->name('index');
            Route::get('/create', [DiscountCodeController::class, 'create'])->name('create');
            Route::post('/', [DiscountCodeController::class, 'store'])->name('store');
            Route::get('/{discount}', [DiscountCodeController::class, 'show'])->whereNumber('discount')->name('show');
            Route::patch('/{discount}/status', [DiscountCodeController::class, 'updateStatus'])->whereNumber('discount')->name('status');
        });
    });
