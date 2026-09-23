@extends('components.layouts.app')

@section('title', 'نوبت‌های من')

@section('content')
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                نوبت‌های من
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                فهرست نوبت‌های رزروشده و وضعیت آن‌ها
            </p>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($appointments->isEmpty())
            <section class="rounded-lg border border-gray-200 bg-white px-6 py-12 text-center shadow-sm">
                <p class="text-sm text-gray-600">
                    هنوز نوبتی برای شما ثبت نشده است.
                </p>

                <a
                    href="{{ route('booking.index') }}"
                    class="mt-4 inline-flex min-h-11 items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    رزرو نوبت
                </a>
            </section>
        @else
            <div class="space-y-4">
                @foreach ($appointments as $appointment)
                    @php
                        $startsAt = $appointment->starts_at->setTimezone('Asia/Tehran');
                        $endsAt = $appointment->ends_at->setTimezone('Asia/Tehran');
                        $now = now('Asia/Tehran');

                        $isPendingPayment =
                            $appointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT;

                        $holdExpired = $isPendingPayment
                            && (
                                $appointment->hold_expires_at === null
                                || $appointment->hold_expires_at
                                    ->setTimezone('Asia/Tehran')
                                    ->lessThanOrEqualTo($now)
                            );

                        $isActive = in_array($appointment->status, [
                            \App\Models\Appointment::STATUS_CONFIRMED,
                            \App\Models\Appointment::STATUS_PENDING_PAYMENT,
                        ], true);

                        $canCancel = $isActive
                            && $startsAt->greaterThanOrEqualTo($now->copy()->addHours(12));

                        $latestPayment = $appointment->payments->first();

                        $canPay = $isPendingPayment
                            && ! $holdExpired
                            && $latestPayment
                            && $latestPayment->isPending();

                        $statusLabel = match ($appointment->status) {
                            \App\Models\Appointment::STATUS_CONFIRMED => 'تأیید شده',
                            \App\Models\Appointment::STATUS_PENDING_PAYMENT => $holdExpired
                                ? 'مهلت پرداخت تمام شده'
                                : 'در انتظار پرداخت',
                            \App\Models\Appointment::STATUS_CANCELLED => 'لغو شده',
                            \App\Models\Appointment::STATUS_COMPLETED => 'تکمیل شده',
                            \App\Models\Appointment::STATUS_NO_SHOW => 'عدم حضور',
                            default => $appointment->status,
                        };

                        $paymentStatusLabel = $latestPayment?->status
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

                    <article class="border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="grid flex-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                                <div>
                                    <span class="block text-xs text-gray-500">روان‌شناس</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $appointment->psychologist->first_name }}
                                        {{ $appointment->psychologist->last_name }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">تاریخ</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">ساعت</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $startsAt->format('H:i') }}
                                        تا
                                        {{ $endsAt->format('H:i') }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">وضعیت نوبت</span>

                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $statusLabel }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">وضعیت پرداخت</span>

                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $paymentStatusLabel }}
                                    </strong>
                                </div>


                                <div>
                                    <span class="block text-xs text-gray-500">مبلغ</span>

                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ number_format($appointment->amount) }}
                                        تومان
                                    </strong>
                                </div>

                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2">

                                @if ($canPay)
                                    <form
                                        method="POST"
                                        action="{{ route('payments.pay', $appointment) }}"
                                        class="shrink-0"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex min-h-10 items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                        >
                                            پرداخت نوبت
                                        </button>
                                    </form>
                                @endif

                                @if (
                                    $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED
                                    && filled($appointment->meeting_url)
                                )
                                    <a
                                        href="{{ $appointment->meeting_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex min-h-10 items-center rounded-lg border border-indigo-300 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
                                    >
                                        ورود به جلسه
                                    </a>
                                @endif

                                @if ($canCancel)
                                    <form
                                        method="POST"
                                        action="{{ route('client.appointments.cancel', $appointment) }}"
                                        class="shrink-0"
                                        onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex min-h-10 items-center rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50"
                                        >
                                            لغو نوبت
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>

                        @if ($appointment->status === \App\Models\Appointment::STATUS_CANCELLED)
                            @php
                                $cancelledAt = $appointment->cancelled_at
                                    ? $appointment->cancelled_at->copy()->setTimezone('Asia/Tehran')
                                    : null;

                                $cancelledByPsychologist = $appointment->cancelled_by !== null
                                    && (int) $appointment->cancelled_by === (int) $appointment->psychologist_id;
                            @endphp

                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <p class="text-xs text-gray-500">
                                    این نوبت
                                    @if ($cancelledAt)
                                        در
                                        {{ \App\Support\PersianDate::format($cancelledAt, 'yyyy/MM/dd') }}
                                        ساعت
                                        {{ $cancelledAt->format('H:i') }}
                                    @endif

                                    @if ($cancelledByPsychologist)
                                        توسط روان‌شناس
                                    @endif

                                    لغو شده است.
                                </p>

                                @if (filled($appointment->cancellation_reason))
                                    <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                                        <p class="text-sm font-semibold text-red-900">
                                            دلیل لغو نوبت
                                        </p>

                                        <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-7 text-red-800">{{ $appointment->cancellation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $appointments->links() }}
            </div>
        @endif
    </main>
@endsection
