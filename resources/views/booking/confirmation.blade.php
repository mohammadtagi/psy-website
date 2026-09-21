@extends('components.layouts.app')

@section('title', 'تأیید ثبت نوبت')

@section('content')
    @php
        $timezone = 'Asia/Tehran';

        $startsAt = \Carbon\CarbonImmutable::instance(
            $appointment->starts_at
        )->setTimezone($timezone);

        $endsAt = \Carbon\CarbonImmutable::instance(
            $appointment->ends_at
        )->setTimezone($timezone);

        $sessionType = match ($appointment->session_type) {
            \App\Models\Appointment::SESSION_TYPE_ONLINE => 'آنلاین',
            \App\Models\Appointment::SESSION_TYPE_IN_PERSON => 'حضوری',
            default => 'نامشخص',
        };

        $statusLabel = match ($appointment->status) {
            \App\Models\Appointment::STATUS_CONFIRMED => 'تأیید شده',
            \App\Models\Appointment::STATUS_PENDING_PAYMENT => 'در انتظار پرداخت',
            \App\Models\Appointment::STATUS_CANCELLED => 'لغو شده',
            \App\Models\Appointment::STATUS_COMPLETED => 'تکمیل شده',
            \App\Models\Appointment::STATUS_NO_SHOW => 'عدم حضور',
            default => $appointment->status,
        };
    @endphp

    <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-green-200 bg-green-50 px-6 py-8 text-center">
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-xl font-bold text-green-700"
                    aria-hidden="true"
                >
                    ✓
                </div>

                <h1 class="mt-4 text-2xl font-bold text-green-900">
                    نوبت شما با موفقیت ثبت شد
                </h1>

                <p class="mt-2 text-sm text-green-800">
                    اطلاعات جلسه را در این صفحه مشاهده کنید.
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

            <div class="space-y-6 p-6">
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

                        <span class="mt-1 inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500">
                            تاریخ جلسه
                        </span>

                        <strong class="mt-1 block text-gray-900">
                            {{ $startsAt->format('Y/m/d') }}
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

                    @if ($appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE)
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
                        href="{{ route('home') }}"
                        class="inline-flex min-h-11 flex-1 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                    >
                        بازگشت به صفحه اصلی
                    </a>
                </div>
            </div>
        </section>
    </main>
@endsection
