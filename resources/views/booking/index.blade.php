@extends('components.layouts.app')
@section('title', 'رزرو جلسه مشاوره')
@push('styles')
<link rel="stylesheet" href="{{ asset('website/style/care-booking-account.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('website/style/care-booking-calendar.css') }}?v=1">
<style>
@media (max-width:760px) {
    .bka .bka-summary, .bka .bks-summary { order:0; }
    .bka .bka-summary[data-mobile-summary],
    .bka .bks-summary[data-mobile-summary] { margin-block:16px; width:100%; }
}
</style>
@endpush
@section('content')
<main class="bka" dir="rtl" data-booking-stage="1" data-booking-account data-account="{{ route('booking.account.show') }}" data-save="{{ route('booking.account.update') }}" data-send="{{ route('auth.send-otp') }}" data-verify="{{ route('auth.verify-otp') }}" data-resend="{{ route('auth.resend-otp') }}" data-login="{{ route('auth.login') }}" data-client-dashboard="{{ route('client.dashboard') }}" data-psychologist-dashboard="{{ route('psychologist.dashboard') }}" data-admin-dashboard="{{ route('admin.dashboard') }}">
    <ol class="bka-steps" aria-label="مراحل رزرو"><li aria-current="step"><span>۱</span>اطلاعات حساب</li><li><span>۲</span>تاریخ و ساعت</li><li><span>۳</span>بررسی و پرداخت</li></ol>
    <header class="bka-heading"><p>یک قدم برای حال بهتر</p><h1 tabindex="-1">رزرو جلسه مشاوره</h1><p>ابتدا اطلاعات خود را تأیید کنید؛ سپس زمان مناسب‌تان را انتخاب کنید.</p></header>
    <div class="bka-grid">
        <div>
            <p class="bka-notice" data-status role="status">در حال دریافت اطلاعات حساب…</p>
            <button class="bka-secondary" type="button" data-retry hidden>تلاش دوباره</button>
            <section class="bka-card" data-guest hidden>
                <span class="bka-badge">شروع رزرو</span><h2>با شمارهٔ موبایل وارد شوید</h2>
                <p>برای رزرو و پیگیری نوبت، شمارهٔ موبایل خود را با کد پیامکی تأیید کنید.</p>
                <button class="bka-primary" type="button" data-open-login>ورود یا ایجاد حساب</button>
                <p class="bka-hint">اگر قبلاً حساب دارید، اطلاعات شما پس از ورود نمایش داده می‌شود.</p>
            </section>
            <section class="bka-card" data-profile hidden>
                <span class="bka-badge">مرحلهٔ اول</span><h2>اطلاعات حساب شما</h2><p>لطفاً اطلاعات زیر را بررسی و در صورت نیاز تکمیل کنید.</p>
                <form data-profile-form>
                    <label>شمارهٔ موبایل تأییدشده<input name="mobile" type="tel" disabled dir="ltr" autocomplete="tel"><small>این شماره تأیید شده و قابل ویرایش نیست.</small></label>
                    <div class="bka-fields"><label>نام<input name="first_name" autocomplete="given-name" maxlength="100" required><small data-error="first_name"></small></label><label>نام خانوادگی<input name="last_name" autocomplete="family-name" maxlength="100" required><small data-error="last_name"></small></label></div>
                    <div class="bka-birth-field"><label for="bka-birth-date">تاریخ تولد</label><div class="bka-birth-control"><input id="bka-birth-date" name="birth_date_jalali" dir="ltr" inputmode="numeric" placeholder="۱۳۷۵/۰۶/۱۵" maxlength="10" required aria-describedby="bka-birth-help"><button id="bka-birth-open" type="button" aria-label="باز کردن تقویم شمسی تاریخ تولد" aria-haspopup="dialog" hidden><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h2M11 15h2M15 15h2M7 18h2M11 18h2"/></svg></button></div><small id="bka-birth-help">از تقویم شمسی انتخاب کنید یا به ترتیب سال / ماه / روز بنویسید.</small><small data-error="birth_date_jalali"></small></div>
                    <p class="bka-form-message" data-profile-message role="alert"></p>
                    <button class="bka-primary" type="submit">تایید اطلاعات و ادامه فرایند رزرو <span aria-hidden="true">←</span></button>
                </form>
            </section>
            <section class="bka-card" data-dates hidden>
                <div class="bka-section-head"><div><span class="bka-badge">مرحلهٔ دوم</span><h2 tabindex="-1">انتخاب تاریخ جلسه</h2></div><button class="bka-secondary" type="button" data-back>ویرایش اطلاعات</button></div>
                <p>یک تاریخ را انتخاب کنید تا ساعت‌های آزاد آن روز نمایش داده شوند.</p>
                @if($dates->isEmpty())
                    <p class="bka-notice">در حال حاضر نوبت آزادی وجود ندارد. لطفاً بعداً دوباره بررسی کنید.</p>
                @else
                    <div data-booking-calendar hidden></div>
                    <div class="bka-dates" data-calendar-fallback>
                    @foreach($dates as $date)
                        @php($dateObject = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date, 'Asia/Tehran'))
                        <a data-calendar-date="{{ $date }}" href="{{ route('booking.show', ['date'=>$date, 'duration'=>$durationMinutes]) }}"><small>{{ $dateObject->locale('fa')->dayName }}</small><strong>{{ \App\Support\PersianDate::format($dateObject) }}</strong><span>مشاهدهٔ ساعت‌ها ←</span></a>
                    @endforeach
                    </div>
                @endif
            </section>
            <noscript><p class="bka-notice">برای استفاده از مراحل رزرو، جاوااسکریپت مرورگر را فعال کنید. <a href="{{ route('auth.login') }}">صفحهٔ ورود</a></p></noscript>
        </div>
        <aside class="bka-summary"><span class="bka-badge">همراه شما در مسیر تغییر</span><h2>جلسهٔ مشاوره</h2><dl><div><dt>مدت جلسه</dt><dd>{{ $durationMinutes }} دقیقه</dd></div><div><dt>زمان‌بندی</dt><dd>به وقت تهران</dd></div><div><dt>تأیید نهایی نوبت</dt><dd>پس از تکمیل پرداخت</dd></div></dl><p>انتخاب تاریخ و ساعت به‌تنهایی به معنی ثبت قطعی نوبت نیست.</p><a href="{{ route('home') }}">بازگشت به وب‌سایت</a></aside>
    </div>
    <dialog class="bka-dialog" aria-labelledby="bka-dialog-title">
        <div class="bka-dialog-top"><span class="bka-badge">ورود امن با پیامک</span><button type="button" data-close aria-label="بستن پنجرهٔ ورود">×</button></div>
        <h2 id="bka-dialog-title">تأیید شمارهٔ موبایل</h2><p data-otp-description>کد ورود به شمارهٔ موبایل شما ارسال می‌شود.</p>
        <form data-mobile-form><label>شمارهٔ موبایل<input name="mobile" type="tel" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="۰۹۱۲۳۴۵۶۷۸۹" required maxlength="30"><small data-error="mobile"></small></label><button class="bka-primary" type="submit">دریافت کد تأیید</button></form>
        <form data-code-form hidden><label>کد تأیید<input name="code" inputmode="numeric" autocomplete="one-time-code" dir="ltr" required><small data-error="code"></small></label><button class="bka-primary" type="submit">تأیید و ادامه</button><div class="bka-otp-actions"><button class="bka-secondary" type="button" data-resend-code>ارسال مجدد کد</button><button class="bka-secondary" type="button" data-change-mobile>اصلاح شماره</button></div><p class="bka-hint" data-expiry></p></form>
        <p class="bka-form-message" data-login-message role="alert"></p>
    </dialog>
</main>
@endsection
@push('scripts')
<script src="{{ asset('website/js/care-booking-account.js') }}?v=booking-shell-2" defer></script>
<script src="{{ asset('website/js/care-booking-birth-picker.js') }}?v=1" defer></script>
<script src="{{ asset('website/js/care-booking-calendar.js') }}?v=1" defer></script>
<script src="{{ asset('website/js/care-booking-summary-position.js') }}?v=mobile-order-1" defer></script>
@endpush
