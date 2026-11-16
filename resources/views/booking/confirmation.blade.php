@php use Carbon\CarbonImmutable; @endphp
@php use App\Support\PersianDate; @endphp
@php use App\Models\Appointment; @endphp
@extends('components.layouts.app')

@section('title', 'جزئیات نوبت')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-booking-review.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('website/style/care-booking-followup.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('website/style/care-booking-payment-compact.css') }}?v=2">
@endpush

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

    <main class="cbc mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8" dir="rtl">
        <ol class="cbp-steps" aria-label="مراحل رزرو"><li><span>۱</span>اطلاعات حساب</li><li><span>۲</span>تاریخ و ساعت</li><li aria-current="step"><span>۳</span>بررسی و پرداخت</li></ol>
        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div
                class="border-b px-6 py-8 text-center {{ $isConfirmed
                    ? 'border-green-200 bg-green-50'
                    : 'border-gray-200 bg-gray-50' }}"
            >
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isConfirmed ? 'نوبت شما تأیید شده است' : ($isPendingPayment && $hasActiveHold && ! $hasSessionStarted ? 'بررسی نوبت و پرداخت' : 'جزئیات نوبت') }}
                </h1>

                <p class="mt-2 text-sm text-gray-700">
                    کد پیگیری نوبت: <bdi dir="ltr" class="break-all font-mono">{{ $appointment->tracking_code }}</bdi>
                </p>
            </div>

            @if (session('status') && ! $isPendingPayment)
                <div
                    class="mx-6 mt-6 rounded-lg border px-4 py-3 text-sm {{ $isConfirmed ? 'border-green-200 bg-green-50 text-green-800' : 'border-gray-200 bg-gray-50 text-gray-700' }}"
                    role="status"
                >
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mx-6 mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-7 text-red-800"
                    role="alert"
                >
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
                            <div class="cbp-pending">
                                <p class="font-medium">نوبت شما هنوز تکمیل نشده، برای ادامه پرداخت را انجام دهید.</p>
                                @php($paymentDeadline = $holdExpiresAt->lt($startsAt) ? $holdExpiresAt : $startsAt)
                                <span class="cbp-timer" data-payment-countdown data-remaining="{{ max(0, $paymentDeadline->getTimestamp() - $now->getTimestamp()) }}">
                                    مهلت تکمیل: <bdi data-countdown-value dir="ltr">{{ gmdate('i:s', max(0, $paymentDeadline->getTimestamp() - $now->getTimestamp())) }}</bdi>
                                </span>
                            </div>
                        @endif
                    </div>
                @endif


                <div class="cbc-summary grid gap-5 sm:grid-cols-2">
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
                            ساعت جلسه · به وقت تهران
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

                @if (! $isPendingPayment)
                <section class="cbf-next" aria-labelledby="cbf-next-title">
                    <h2 id="cbf-next-title">قدم بعدی شما</h2>
                    @if ($isConfirmed)
                        <p>نوبت شما تأیید شده است. برای پیگیری جزئیات و امکانات مربوط به جلسه، به «نوبت‌های من» بروید.</p>
                        @if (! $hasSessionStarted)
                            @if ($appointment->session_type === Appointment::SESSION_TYPE_ONLINE)
                                <ul><li>پیش از جلسه، اتصال اینترنت، میکروفن و هدفون خود را بررسی کنید.</li><li>محیطی آرام و خصوصی برای گفت‌وگو انتخاب کنید.</li><li>جزئیات دسترسی به جلسه را در حساب خود بررسی کنید.</li></ul>
                            @elseif ($appointment->session_type === Appointment::SESSION_TYPE_IN_PERSON)
                                <ul><li>پیش از حرکت، نشانی و جزئیات مراجعه را بررسی کنید.</li><li>برای حضور به‌موقع برنامه‌ریزی کنید؛ ساعت نوبت به وقت تهران است.</li></ul>
                            @endif
                        @else
                            <p>زمان شروع این نوبت فرا رسیده است؛ آخرین جزئیات آن را در حساب خود بررسی کنید.</p>
                        @endif
                    @elseif ($isPendingPayment && $hasActiveHold && ! $hasSessionStarted)
                        <p>رزرو هنوز قطعی نیست. برای ادامه، فاکتور و گزینه‌های تسویهٔ همین صفحه را بررسی کنید. داشتن کد پیگیری به‌تنهایی به معنی تأیید نوبت یا پرداخت موفق نیست.</p>
                    @elseif ($isPendingPayment)
                        <p>این نوبت تأیید نشده است و امکان ادامهٔ پرداخت در این صفحه وجود ندارد. برای رزرو جدید، زمان‌های موجود را بررسی کنید.</p>
                    @elseif ($appointment->status === Appointment::STATUS_CANCELLED)
                        <p>این نوبت لغو شده است. وضعیت لغو به‌تنهایی نشان‌دهندهٔ انجام بازپرداخت نیست. برای پیگیری، کد نوبت را نگه دارید.</p>
                    @elseif ($appointment->status === Appointment::STATUS_COMPLETED)
                        <p>این جلسه تکمیل شده است. برای مرور سوابق نوبت‌ها به حساب خود بروید.</p>
                    @elseif ($appointment->status === Appointment::STATUS_NO_SHOW)
                        <p>برای این جلسه وضعیت عدم حضور ثبت شده است. این عنوان به‌تنهایی مشخص نمی‌کند عدم حضور مربوط به کدام طرف بوده یا بازپرداخت انجام شده است.</p>
                    @else
                        <p>برای پیگیری آخرین جزئیات، نوبت‌های خود را در حساب کاربری بررسی کنید.</p>
                    @endif
                    <a class="cbf-account-link" href="{{ route('client.appointments.index') }}">رفتن به نوبت‌های من ←</a>
                    <p class="cbf-privacy">کد پیگیری و جزئیات جلسه را فقط در اختیار افراد مورد اعتماد قرار دهید.</p>
                </section>
                @endif

                @include('booking._invoice')

                @if ($isPendingPayment && $hasActiveHold && ! $hasSessionStarted)
                <section class="cbp-notes" aria-labelledby="cbp-notes-title">
                    <h2 id="cbp-notes-title">ملاحظات پرداخت</h2>
                    <p>هزینهٔ پرداختی شامل مبلغ ویزیت کامل است و هزینه‌ای برای ویزیت در مطب دریافت نمی‌گردد.</p>
                </section>
                <section class="cbp-trust" aria-labelledby="cbp-trust-title">
                    <h2 id="cbp-trust-title"><svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 4 6v6c0 4 5 8 8 9 3-1 8-5 8-9V6Z"/><path d="m8 12 3 3 5-6"/></svg>با اطمینان خاطر نوبت خود را ثبت کنید</h2>
                    <p>بازگشت وجه در صورت عدم حضور مشاور، مطابق شرایط</p>
                    <p>پیگیری نوبت از حساب کاربری</p>
                    <details class="cbp-policy">
                        <summary>امکان لغو نوبت <span>(مشاهده شرایط)</span></summary>
                        <p>در صورتی که لغو تا قبل از ۲۴ ساعت مانده به نوبت انجام شود 100 درصد مبلغ پرداخت شده به حساب شما برگردانده خواهد شد.</p>
                    </details>
                </section>
                @endif

                <div class="cbc-footer flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row">
                    @if (($isPendingPayment || $isConfirmed) && ! $hasSessionStarted)
                    <form method="POST" action="{{ route('client.appointments.cancel', $appointment) }}" class="cbp-cancel" data-cancel-appointment>
                        @csrf
                        <button type="submit">لغو این نوبت</button>
                    </form>
                    @endif

                    <a
                        href="{{ route('client.appointments.index') }}"
                        class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        مشاهده نوبت‌های من
                    </a>
                </div>
            </div>
        </section>
    </main>
@endsection
@push('scripts')
<script src="{{ asset('website/js/care-booking-payment-compact.js') }}?v=2" defer></script>
@endpush
