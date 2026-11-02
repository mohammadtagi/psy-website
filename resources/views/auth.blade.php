@php
    $isOtp = ($step ?? 'mobile') === 'otp';
    $codeLength = (int) config('services.otp.length', 6);
@endphp
    <!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isOtp ? 'تأیید شماره موبایل' : 'ورود به حساب کاربری' }} | کریم محمدزاده</title>
    <link rel="stylesheet" href="{{ asset('website/style/care-auth.css') }}">
</head>
<body class="care-auth">
<main class="auth-shell">
    <header class="auth-brand">
        <a href="{{ url('/') }}">کریم محمدزاده</a>
        <p>روان‌شناس بالینی</p>
    </header>
    <section class="auth-card" aria-labelledby="auth-title">
        <p class="auth-step">ورود با شماره موبایل · مرحله {{ $isOtp ? '۲ از ۲' : '۱ از ۲' }}</p>
        <h1 id="auth-title">{{ $isOtp ? 'کد تأیید را وارد کنید' : 'به حساب خود وارد شوید' }}</h1>
        @if ($isOtp)
            <p class="auth-description">کد ارسال‌شده به شماره <bdi dir="ltr">{{ $mobile }}</bdi> را وارد کنید.</p>
            <a class="auth-edit" href="{{ route('auth.login') }}">اصلاح شماره موبایل</a>
        @else
            <p class="auth-description">برای ورود یا ایجاد حساب، شماره موبایل خود را وارد کنید.</p>
        @endif

        @if (session('status'))
            <p class="auth-notice" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="auth-errors" role="alert">
                <p>لطفاً موارد زیر را بررسی کنید:</p>
                <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        @endif

        @if (! $isOtp)
            <form method="POST" action="{{ route('auth.send-otp') }}">
                @csrf
                <label class="auth-label" for="mobile">شماره موبایل</label>
                <input class="auth-input" id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}" autocomplete="tel" inputmode="tel" dir="ltr" required aria-describedby="mobile-hint{{ $errors->has('mobile') ? ' mobile-error' : '' }}" @if($errors->has('mobile')) aria-invalid="true" @endif>
                <p id="mobile-hint" class="auth-hint">مثلاً 09123456789؛ کد ورود با پیامک ارسال می‌شود.</p>
                @error('mobile')<p id="mobile-error" class="auth-field-error">{{ $message }}</p>@enderror
                <label class="auth-remember" for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember', false)) aria-describedby="remember-hint">
                    <span>مرا به خاطر بسپار — ۳۰ روز</span>
                </label>
                <p id="remember-hint" class="auth-hint">در دستگاه‌های عمومی یا مشترک این گزینه را فعال نکنید.</p>
                <button class="auth-button" type="submit">دریافت کد ورود <span aria-hidden="true">←</span></button>
            </form>
        @else
            <form method="POST" action="{{ route('auth.verify-otp') }}">
                @csrf
                <label class="auth-label" for="code">کد تأیید {{ $codeLength }} رقمی</label>
                <input class="auth-input auth-code" id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="{{ $codeLength }}" dir="ltr" required aria-describedby="code-hint{{ $errors->has('code') ? ' code-error' : '' }}" @if($errors->has('code')) aria-invalid="true" @endif>
                <p id="code-hint" class="auth-hint">اعتبار هر کد: {{ (int) config('services.otp.expires_in', 2) }} دقیقه از زمان صدور.</p>
                @error('code')<p id="code-error" class="auth-field-error">{{ $message }}</p>@enderror
                <button class="auth-button" type="submit">تأیید و ورود <span aria-hidden="true">←</span></button>
            </form>
            <div class="auth-resend">
                <p class="auth-hint">کد را دریافت نکردید؟ ارسال مجدد پس از {{ (int) config('services.otp.resend_seconds', 60) }} ثانیه از ارسال قبلی امکان‌پذیر است.</p>
                <form method="POST" action="{{ route('auth.resend-otp') }}">
                    @csrf
                    <button class="auth-button auth-secondary" type="submit">ارسال مجدد کد</button>
                </form>
            </div>
        @endif
    </section>
    <footer class="auth-footer">
        <p>کد ورود خود را در اختیار دیگران قرار ندهید.</p>
        <a href="{{ url('/') }}">بازگشت به سایت</a>
    </footer>
</main>
</body>
</html>
