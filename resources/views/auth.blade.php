@if (($step ?? 'mobile') === 'mobile')
    <form method="POST" action="{{ route('auth.send-otp') }}">
        @csrf

        <input
            type="tel"
            name="mobile"
            value="{{ old('mobile') }}"
            autocomplete="tel"
            inputmode="tel"
            required
        >

        @error('mobile')
        <p>{{ $message }}</p>
        @enderror

        <button type="submit">
            دریافت کد ورود
        </button>
    </form>
@elseif (($step ?? null) === 'otp')
    <p>
        کد ارسال‌شده به شماره {{ $mobile }} را وارد کنید.
    </p>

    <form method="POST" action="{{ route('auth.verify-otp') }}">
        @csrf

        <input
            type="text"
            name="code"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="{{ config('services.otp.length', 6) }}"
            required
        >

        @error('code')
        <p>{{ $message }}</p>
        @enderror

        <button type="submit">
            تأیید و ورود
        </button>
    </form>

    <form method="POST" action="{{ route('auth.resend-otp') }}">
        @csrf

        <button type="submit">
            ارسال مجدد کد
        </button>
    </form>
@endif

@if (session('status'))
    <p>{{ session('status') }}</p>
@endif
