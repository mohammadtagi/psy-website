@php
    $preview = $preview ?? true;
    $mobile = $mobile ?? null;
@endphp

<x-layouts.auth title="تأیید شماره موبایل">
    <x-slot:description>
        @if($mobile)
            کد ۶ رقمی ارسال‌شده به شماره
            <bdi class="font-semibold text-slate-900">{{ $mobile }}</bdi>
            را وارد کنید.
        @else
            در نسخه نهایی، شماره مقصد ارسال پیامک اینجا نمایش داده می‌شود.
        @endif
    </x-slot:description>

    <div class="space-y-5">
        @if($preview)
            <x-form.status
                type="info"
                message="این صفحه پیش‌نمایش است؛ تأیید کد و ارسال مجدد هنوز فعال نیست."
            />
        @endif

        <x-form.status :message="session('status')" />

        <x-form.status
            type="error"
            :message="$errors->first('auth')"
        />

        <form
            method="POST"
            action="{{ $verifyOtpUrl ?? '#' }}"
            class="space-y-5"
        >
            @csrf

            <x-form.input
                name="code"
                label="کد ورود"
                hint="کد ورود از زمان صدور، ۲ دقیقه اعتبار دارد."
                dir="ltr"
                inputmode="numeric"
                autocomplete="one-time-code"
                minlength="6"
                maxlength="6"
                class="text-center text-2xl tracking-widest"
                required
                autofocus
            />

            <x-form.button :disabled="$preview">
                تأیید و ورود
            </x-form.button>
        </form>

        <div class="border-t border-slate-100 pt-5">
            <form
                method="POST"
                action="{{ $resendOtpUrl ?? '#' }}"
            >
                @csrf

                <x-form.button
                    variant="secondary"
                    :disabled="$preview"
                >
                    ارسال مجدد کد
                </x-form.button>
            </form>

            <p class="mt-3 text-center text-xs leading-6 text-slate-500">
                ارسال مجدد پس از گذشت ۶۰ ثانیه امکان‌پذیر است.
            </p>

            <div class="mt-4 text-center">
                <a
                    href="{{ $changeMobileUrl ?? url('/preview/auth/login') }}"
                    class="text-sm font-medium text-teal-700 underline-offset-4 hover:underline"
                >
                    اصلاح شماره موبایل
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth>
