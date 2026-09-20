@php
    $preview = $preview ?? true;
@endphp

<x-layouts.auth title="ورود به حساب کاربری">
    <x-slot:description>
        شماره موبایل خود را وارد کنید تا کد ورود برای شما ارسال شود.
        اگر حساب نداشته باشید، پس از تأیید شماره، حساب شما ساخته می‌شود.
    </x-slot:description>

    <div class="space-y-5">
        @if($preview)
            <x-form.status
                type="info"
                message="این صفحه پیش‌نمایش است و هنوز پیامکی ارسال نمی‌کند."
            />
        @endif

        <x-form.status :message="session('status')" />

        <x-form.status
            type="error"
            :message="$errors->first('auth')"
        />

        <form
            method="POST"
            action="{{ $sendOtpUrl ?? '#' }}"
            class="space-y-5"
        >
            @csrf

            <x-form.input
                name="mobile"
                label="شماره موبایل"
                type="tel"
                :value="old('mobile', '')"
                placeholder="09123456789"
                hint="شماره موبایل را با ۰۹ وارد کنید."
                dir="ltr"
                inputmode="tel"
                autocomplete="tel"
                maxlength="11"
                required
                autofocus
            />

            <x-form.button :disabled="$preview">
                دریافت کد ورود
            </x-form.button>
        </form>
    </div>
</x-layouts.auth>
