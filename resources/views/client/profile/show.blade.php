@extends('components.layouts.app')
@section('title', 'پروفایل من')
@push('styles')
<link rel="stylesheet" href="{{ asset('website/style/care-client-profile.css') }}?v=1">
@endpush
@section('content')
<main class="cpf" dir="rtl">
    <header><h1>پروفایل من</h1><p>اطلاعات فردی و سوابق جلسات تکمیل‌شده</p></header>
    @if (session('success'))
        <div class="cpf-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="cpf-error" role="alert"><ul>
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul></div>
    @endif
    <section class="cpf-card" aria-labelledby="cpf-info">
        <h2 id="cpf-info">اطلاعات فردی</h2>
        <dl class="cpf-grid cpf-summary">
            <div><dt>نام و نام خانوادگی</dt><dd>{{ trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->name ?: 'ثبت نشده') }}</dd></div>
            <div><dt>شماره موبایل</dt><dd><bdi>{{ $client->mobile ?: 'ثبت نشده' }}</bdi></dd></div>
            <div><dt>تاریخ تولد شمسی</dt><dd>{{ $client->birth_date ? \App\Support\PersianDate::format(\Illuminate\Support\Carbon::parse($client->birth_date)) : 'ثبت نشده' }}</dd></div>
        </dl>
        <details class="cpf-edit" @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('birth_date')) open @endif>
            <summary>ویرایش اطلاعات فردی</summary>
            <form method="POST" action="{{ route('client.profile.update') }}">
                @csrf
                @method('PUT')
                <div class="cpf-grid cpf-fields">
                    <div>
                        <label for="first_name">نام</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $client->first_name) }}" maxlength="100" required autocomplete="given-name" @error('first_name') aria-invalid="true" aria-describedby="cpf-first-error" @enderror>
                        @error('first_name')<p id="cpf-first-error" class="cpf-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name">نام خانوادگی</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $client->last_name) }}" maxlength="100" required autocomplete="family-name" @error('last_name') aria-invalid="true" aria-describedby="cpf-last-error" @enderror>
                        @error('last_name')<p id="cpf-last-error" class="cpf-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="birth_date">تاریخ تولد شمسی</label>
                        <div class="cpf-birth-field">
                            <input id="birth_date" name="birth_date" type="text" dir="ltr" inputmode="numeric" placeholder="۱۳۷۵/۰۶/۱۵" value="{{ old('birth_date', $client->birth_date ? \App\Support\PersianDate::format($client->birth_date, 'yyyy/MM/dd') : '') }}" maxlength="10" pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}" required autocomplete="bday" aria-describedby="cpf-birth-help @error('birth_date') cpf-birth-error @enderror" @error('birth_date') aria-invalid="true" @enderror>
                            <button type="button" id="cpf-birth-open" class="cpf-birth-open" hidden aria-label="باز کردن تقویم تاریخ تولد شمسی" aria-haspopup="dialog" title="انتخاب تاریخ تولد">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h2m3 0h2M7 18h2"/></svg>
                            </button>
                        </div>
                        <p id="cpf-birth-help" class="cpf-help">از تقویم انتخاب کنید یا به ترتیب سال/ماه/روز با اعداد فارسی یا انگلیسی بنویسید.</p>
                        @error('birth_date')<p id="cpf-birth-error" class="cpf-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div><span class="cpf-label">شماره موبایل</span><p class="cpf-readonly"><bdi>{{ $client->mobile ?: 'ثبت نشده' }}</bdi></p><p class="cpf-help">شماره موبایل قابل ویرایش نیست.</p></div>
                </div>
                <button type="submit" class="cpf-save">ذخیره تغییرات</button>
            </form>
        </details>
    </section>
    <section aria-labelledby="cpf-history">
        <div class="cpf-section-heading"><h2 id="cpf-history">جلسات تکمیل‌شده</h2><a href="{{ route('client.appointments.index') }}">مشاهده همه نوبت‌ها</a></div>
        <p class="cpf-help">ساعت جلسات به وقت تهران نمایش داده می‌شود.</p>
        @if ($completedAppointments->isEmpty())
            <p class="cpf-card cpf-empty">هنوز جلسه تکمیل‌شده‌ای ثبت نشده است.</p>
        @else
            <div class="cpf-history">
            @foreach ($completedAppointments as $appointment)
                @php
                    $startsAt = $appointment->starts_at->copy()->setTimezone('Asia/Tehran');
                    $latestPayment = $appointment->payments->first();
                    $paymentStatusLabel = $latestPayment
                        ? match ($latestPayment->status) {
                            \App\Models\Payment::STATUS_INITIATED => 'شروع نشده',
                            \App\Models\Payment::STATUS_PENDING => 'در حال پرداخت',
                            \App\Models\Payment::STATUS_PAID => 'پرداخت موفق',
                            \App\Models\Payment::STATUS_FAILED => 'ناموفق',
                            \App\Models\Payment::STATUS_CANCELLED => 'لغو شده',
                            default => $latestPayment->status,
                        }
                        : 'ثبت نشده';
                @endphp
                <article class="cpf-card">
                    <dl class="cpf-grid">
                        <div><dt>روان‌شناس</dt><dd>{{ trim(($appointment->psychologist?->first_name ?? '') . ' ' . ($appointment->psychologist?->last_name ?? '')) ?: 'ثبت نشده' }}</dd></div>
                        <div><dt>تاریخ و ساعت جلسه</dt><dd>{{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }} · <bdi>{{ $startsAt->format('H:i') }}</bdi></dd></div>
                    </dl>
                    <details class="cpf-payment"><summary>جزئیات پرداخت</summary><dl class="cpf-grid">
                        <div><dt>وضعیت آخرین پرداخت</dt><dd>{{ $paymentStatusLabel }}</dd></div>
                        <div><dt>مبلغ آخرین پرداخت</dt><dd>@if ($latestPayment) {{ number_format($latestPayment->amount) }} تومان @else ثبت نشده @endif</dd></div>
                    </dl></details>
                </article>
            @endforeach
            </div>
            <div class="cpf-pagination">{{ $completedAppointments->links() }}</div>
        @endif
    </section>
</main>
<script src="{{ asset('website/js/care-profile-birth-picker.js') }}?v=1" defer></script>
@endsection
