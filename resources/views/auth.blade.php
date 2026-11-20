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
        <h1 id="auth-title">{{ $isOtp ? 'تأیید شماره موبایل' : 'ورود یا ثبت‌نام' }}</h1>
        @if ($isOtp)
            <p class="auth-description">کد به <bdi dir="ltr">{{ $mobile }}</bdi> پیامک شد.</p>
            <a class="auth-edit" href="{{ route('auth.login') }}">اصلاح شماره موبایل</a>
        @endif

        @if (session('status'))
            <p class="auth-notice" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="auth-errors" role="alert">
                <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        @endif

        @if (! $isOtp)
            <form method="POST" action="{{ route('auth.send-otp') }}">
                @csrf
                <label class="auth-label" for="mobile">شماره موبایل</label>
                <input class="auth-input" id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}" autocomplete="tel" inputmode="tel" dir="ltr" required aria-describedby="mobile-hint{{ $errors->has('mobile') ? ' mobile-error' : '' }}" @if($errors->has('mobile')) aria-invalid="true" @endif>
                <p id="mobile-hint" class="auth-hint">مثلاً <bdi dir="ltr">09123456789</bdi></p>
                @error('mobile')<p id="mobile-error" class="auth-field-error">{{ $message }}</p>@enderror
                <label class="auth-remember" for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember', false)) aria-describedby="remember-hint">
                    <span> مرا به خاطر بسپار</span>
                </label>
                <button class="auth-button" type="submit">دریافت کد پیامکی <span aria-hidden="true">←</span></button>
            </form>
        @else
            <form method="POST" action="{{ route('auth.verify-otp') }}">
                @csrf
                <label class="auth-label" for="code">کد تأیید {{ $codeLength }} رقمی</label>
                <input class="auth-input auth-code" id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="{{ $codeLength }}" dir="ltr" required aria-describedby="code-hint{{ $errors->has('code') ? ' code-error' : '' }}" @if($errors->has('code')) aria-invalid="true" @endif>
                <p id="code-hint" class="auth-hint">اعتبار کد: {{ (int) config('services.otp.expires_in', 2) }} دقیقه</p>
                @error('code')<p id="code-error" class="auth-field-error">{{ $message }}</p>@enderror
                <button class="auth-button" type="submit">تأیید و ورود <span aria-hidden="true">←</span></button>
            </form>
            <div class="auth-resend">
                <p class="auth-hint">ارسال دوباره پس از {{ (int) config('services.otp.resend_seconds', 60) }} ثانیه</p>
                <form method="POST" action="{{ route('auth.resend-otp') }}">
                    @csrf
                    <button class="auth-button auth-secondary" type="submit">ارسال مجدد کد</button>
                </form>
            </div>
        @endif
    </section>
    <footer class="auth-footer">
        @if ($isOtp)<p>کد ورود را به کسی ندهید.</p>@endif
        <a href="{{ url('/') }}">بازگشت به سایت</a>
    </footer>
</main>
</body>
</html>
