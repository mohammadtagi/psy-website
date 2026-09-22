@php use Carbon\CarbonImmutable; @endphp
@php use App\Support\PersianDate; @endphp
@php use App\Models\Appointment; @endphp
@extends('components.layouts.app')

@section('title', 'جزئیات نوبت')

@section('content')
    @php
        $timezone = 'Asia/Tehran';

        $startsAt = CarbonImmutable::instance(
            $appointment->starts_at
        )->setTimezone($timezone);

        $endsAt = CarbonImmutable::instance(
            $appointment->ends_at
        )->setTimezone($timezone);

        $sessionType = match ($appointment->session_type) {
            Appointment::SESSION_TYPE_ONLINE => 'آنلاین',
            Appointment::SESSION_TYPE_IN_PERSON => 'حضوری',
            default => 'نامشخص',
        };

        $now = CarbonImmutable::now('UTC');

        $isPendingPayment = $appointment->status
            === Appointment::STATUS_PENDING_PAYMENT;

        $holdExpiresAt = $appointment->hold_expires_at
            ? CarbonImmutable::instance(
                $appointment->hold_expires_at
            )->setTimezone($timezone)
            : null;

        $hasActiveHold = $isPendingPayment
            && $holdExpiresAt !== null
            && $holdExpiresAt->gt($now);

        $hasExpiredHold = $isPendingPayment && ! $hasActiveHold;

        $hasSessionStarted = $startsAt->lte($now);


        $statusLabel = match ($appointment->status) {
            Appointment::STATUS_CONFIRMED => 'تأیید شده',
Appointment::STATUS_PENDING_PAYMENT =>
    $hasExpiredHold
        ? 'مهلت رزرو موقت پایان یافته'
        : 'در انتظار پرداخت',

            Appointment::STATUS_CANCELLED => 'لغو شده',
            Appointment::STATUS_COMPLETED => 'تکمیل شده',
            Appointment::STATUS_NO_SHOW => 'عدم حضور',
            default => $appointment->status,
        };

        $isConfirmed = $appointment->status
            === Appointment::STATUS_CONFIRMED;

        $statusClasses = match ($appointment->status) {
            Appointment::STATUS_CONFIRMED =>
                'bg-green-100 text-green-800',

            Appointment::STATUS_PENDING_PAYMENT =>
                'bg-amber-100 text-amber-800',

            Appointment::STATUS_CANCELLED =>
                'bg-red-100 text-red-800',

            default => 'bg-gray-100 text-gray-800',
        };

        if ($hasExpiredHold) {
    $statusClasses = 'bg-gray-100 text-gray-800';
}

    @endphp

    <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div
                class="border-b px-6 py-8 text-center {{ $isConfirmed
                    ? 'border-green-200 bg-green-50'
                    : 'border-gray-200 bg-gray-50' }}"
            >
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isConfirmed ? 'نوبت شما تأیید شده است' : 'جزئیات نوبت' }}
                </h1>

                <p class="mt-2 text-sm text-gray-700">
                    وضعیت فعلی نوبت: {{ $statusLabel }}
                </p>
            </div>

            @if (session('status'))
                <div
                    class="mx-6 mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                    role="status"
                >
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('payment'))
                <div
                    class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-7 text-red-800"
                    role="alert"
                >
                    {{ $errors->first('payment') }}
                </div>
            @endif

            <div class="space-y-6 p-6">
                @if ($isPendingPayment)
                    <div
                        class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-7 text-amber-900"
                        role="status"
                    >
                        @if ($hasExpiredHold)
                            <p class="font-medium">
                                مهلت رزرو موقت این نوبت پایان یافته است.
                            </p>

                            <p class="mt-1">
                                این نوبت تأیید نشده و دیگر زمان جلسه را برای شما نگه نمی‌دارد.
                                برای رزرو مجدد، زمان‌های موجود را بررسی کنید.
                            </p>
                        @elseif ($hasSessionStarted)
                            <p class="font-medium">
                                زمان شروع جلسه فرا رسیده و این نوبت تأیید نشده است.
                            </p>

                            <p class="mt-1">
                                برای ثبت نوبت جدید، زمان‌های موجود را بررسی کنید.
                            </p>
                        @else
                            <p class="font-medium">
                                این نوبت به‌صورت موقت ثبت شده و هنوز تأیید نهایی نشده است.
                            </p>

                            <p class="mt-1">
                                پایان مهلت نگهداری موقت:
                                <strong>
                                    {{ \App\Support\PersianDate::format($holdExpiresAt) }}
                                    ساعت
                                    {{ $holdExpiresAt->format('H:i:s') }}
                                </strong>
                                به وقت تهران.
                            </p>

                            <p class="mt-1">
                                تأیید نهایی به پرداخت موفق و معتبر بودن زمان نوبت وابسته است.
                                مهلت نگهداری موقت، امکان پرداخت پس از شروع جلسه را تضمین نمی‌کند.
                            </p>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <span class="text-sm text-gray-500">
                        کد نوبت
                    </span>

                    <strong class="font-mono text-sm text-gray-900">
                        #{{ $appointment->getKey() }}
                    </strong>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <span class="block text-sm text-gray-500">
                            روان‌شناس
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ $appointment->psychologist->first_name }}
                            {{ $appointment->psychologist->last_name }}
                        </strong>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            وضعیت
                        </span>

                        <span
                            class="mt-1 inline-flex rounded-full px-3 py-1 text-sm font-medium {{ $statusClasses }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            تاریخ جلسه
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ PersianDate::format($startsAt) }}
                        </strong>

                        <span class="mt-1 block text-xs text-gray-500">
                            {{ $startsAt->locale('fa')->dayName }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            ساعت جلسه
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ $startsAt->format('H:i') }}
                            تا
                            {{ $endsAt->format('H:i') }}
                        </strong>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            مدت جلسه
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ $appointment->duration_minutes }} دقیقه
                        </strong>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            شیوه برگزاری
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ $sessionType }}
                        </strong>
                    </div>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">
                            مبلغ جلسه
                        </span>

                        <strong class="text-gray-900">
                            {{ number_format((int) $appointment->amount) }}
                            تومان
                        </strong>
                    </div>

                    @if ($isPendingPayment && $hasActiveHold && ! $hasSessionStarted)
                        <p class="mt-3 text-xs leading-6 text-gray-600">
                            با انتخاب گزینه پرداخت، به درگاه زرین‌پال منتقل می‌شوید.
                            تأیید نهایی نوبت پس از پرداخت موفق و تأیید سمت سرور انجام می‌شود.
                        </p>
                    @elseif (! $isConfirmed)
                        <p class="mt-3 text-xs leading-6 text-gray-600">
                            نمایش مبلغ یا وضعیت نوبت، به‌تنهایی رسید پرداخت محسوب نمی‌شود.
                        </p>
                    @endif




                @if ($isPendingPayment && $hasActiveHold && ! $hasSessionStarted)
                        <form
                            method="POST"
                            action="{{ route('payments.pay', $appointment) }}"
                            class="mt-4"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="inline-flex min-h-11 items-center justify-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                پرداخت و تأیید نوبت
                            </button>
                        </form>
                    @endif

                    @if ($appointment->session_type === Appointment::SESSION_TYPE_ONLINE)
                        <p class="mt-3 text-xs leading-6 text-gray-500">
                            لینک جلسه آنلاین، در صورت فعال‌شدن، در اطلاعات نوبت نمایش داده خواهد شد.
                        </p>
                    @endif
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row">
                    <a
                        href="{{ route('booking.index') }}"
                        class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        رزرو نوبت دیگر
                    </a>

                    <a
                        href="{{ route('client.appointments.index') }}"
                        class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                    >
                        مشاهده نوبت‌های من
                    </a>
                </div>
            </div>
        </section>
    </main>
@endsection
