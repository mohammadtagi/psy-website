@extends('components.layouts.app')
@section('title', 'نوبت‌های من')
@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-client-appointments.css') }}?v=2">
@endpush
@section('content')
    <main class="cca" dir="rtl">
        <header class="cca-header"><div><h1>نوبت‌های من</h1><p class="cca-muted">زمان جلسه و اقدام بعدی، در یک نگاه · ساعت‌ها به وقت تهران</p></div><a class="cca-button" href="{{ route('booking.index') }}">رزرو جلسه جدید</a></header>
        @if(session('status'))<p class="cca-feedback" role="status">{{ session('status') }}</p>@endif
        @error('appointment')<p class="cca-error" role="alert">{{ $message }}</p>@enderror
        @if($appointments->isEmpty())
            <section class="cca-card cca-empty"><h2>نوبتی برای نمایش وجود ندارد</h2><p class="cca-muted">برای انتخاب زمان جلسه، زمان‌های قابل رزرو را ببینید.</p><a href="{{ route('booking.index') }}">مشاهده زمان‌ها و رزرو</a></section>
        @else
            <div class="cca-filters" hidden data-filters role="group" aria-label="فیلتر نوبت‌های همین صفحه">
                <span class="cca-muted">در همین صفحه:</span>
                <button type="button" data-filter="all" aria-pressed="true">همه</button>
                <button type="button" data-filter="upcoming" aria-pressed="false">جلسات پیش رو</button>
                <button type="button" data-filter="past" aria-pressed="false">جلسات گذشته</button>
                <button type="button" data-filter="cancelled" aria-pressed="false">لغو شده</button>
            </div>
            <p class="cca-filter-status cca-muted" role="status" aria-live="polite"></p>
            <div class="cca-list">
                @foreach($appointments as $appointment)
                    @php
                        $startsAtUtc = $appointment->starts_at->toImmutable()->utc();
                        $endsAtUtc = $appointment->ends_at->toImmutable()->utc();

                        $startsAt = $startsAtUtc->setTimezone('Asia/Tehran');
                        $endsAt = $endsAtUtc->setTimezone('Asia/Tehran');

                        $now = \Carbon\CarbonImmutable::now('UTC');

                                                                        $isPendingPayment =
                                                                            $appointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT;

$holdExpired = $isPendingPayment
    && (
        $appointment->hold_expires_at === null
        || $appointment->hold_expires_at
            ->toImmutable()
            ->lessThanOrEqualTo($now)
    );


                                                                        $isActive = in_array($appointment->status, [
                                                                            \App\Models\Appointment::STATUS_CONFIRMED,
                                                                            \App\Models\Appointment::STATUS_PENDING_PAYMENT,
                                                                        ], true);

                        $cancellationBeforeMinutes = (int) config(
                            'booking.cancellation_before_minutes',
                            720,
                        );

                        $newBookingGraceMinutes = (int) config(
                            'booking.new_booking_grace_minutes',
                            60,
                        );

                        $createdAtUtc = \Carbon\CarbonImmutable::parse(
                            $appointment->created_at
                        )->utc();

                        $isBeforeAppointmentStart = $startsAtUtc->greaterThan($now);

                        $isWithinNewBookingGracePeriod = $now->lt(
                            $createdAtUtc->addMinutes($newBookingGraceMinutes)
                        );

                        $isBeforeCancellationDeadline = $now->lt(
                            $startsAtUtc->subMinutes($cancellationBeforeMinutes)
                        );

$canCancel = $isActive
    && ! $holdExpired
    && $isBeforeAppointmentStart
    && (
        $isWithinNewBookingGracePeriod
        || $isBeforeCancellationDeadline
    );


                                                                        $latestPayment = $appointment->payments->first();

                                                                        $canPay = $isPendingPayment
                                                                            && ! $holdExpired
                                                                            && $latestPayment
                                                                            && $latestPayment->isPending();

                                                                        $statusLabel = match ($appointment->status) {
                        \App\Models\Appointment::STATUS_CONFIRMED => $appointment->sessionStatusLabel(),
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
                    @php
                        // Presentation grouping only; payment/cancellation eligibility above is unchanged.
                        $upcoming = $isActive && ! $holdExpired && $endsAtUtc->greaterThan($now);
                    @endphp
                    <article class="cca-card" data-appointment data-group="{{ $appointment->status === \App\Models\Appointment::STATUS_CANCELLED ? 'cancelled' : ($upcoming ? 'upcoming' : 'past') }}">
                        <div class="cca-card-top">
                            <div><h2>{{ $appointment->psychologist->first_name }} {{ $appointment->psychologist->last_name }}</h2><p class="cca-date">{{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }} <span aria-hidden="true"> · </span><bdi dir="ltr">{{ $startsAt->format('H:i') }} – {{ $endsAt->format('H:i') }}</bdi></p></div>
                            <span @class(['cca-badge', 'cca-warning' => $isPendingPayment, 'cca-cancelled' => $appointment->status === \App\Models\Appointment::STATUS_CANCELLED])>{{ $statusLabel }}</span>
                        </div>
                        <div class="cca-actions">
                            @if($canPay)
                                <form method="POST" action="{{ route('payments.pay', $appointment) }}">@csrf<button type="submit" class="cca-button">پرداخت {{ number_format($appointment->amount) }} تومان</button></form>
                            @endif
                            @if($appointment->status === \App\Models\Appointment::STATUS_CONFIRMED && filled($appointment->meeting_url))
                                <a class="cca-button" href="{{ $appointment->meeting_url }}" target="_blank" rel="noopener noreferrer">ورود به جلسه <span class="cca-small">(زبانه جدید)</span></a>
                            @endif
                            <a class="cca-secondary" href="{{ route('client.appointments.session.show', $appointment) }}">جلسه و یادداشت‌ها</a>
                        </div>
                        <details class="cca-details"><summary>جزئیات پرداخت و پیگیری</summary>
                            <dl><div><dt>مبلغ</dt><dd>{{ number_format($appointment->amount) }} تومان</dd></div><div><dt>وضعیت پرداخت</dt><dd>{{ $paymentStatusLabel }}</dd></div><div><dt>کد پیگیری</dt><dd><bdi dir="ltr">{{ $appointment->tracking_code }}</bdi></dd></div></dl>
                        </details>
                        @if($canCancel)
                            <details class="cca-cancel"><summary>لغو نوبت</summary><p class="cca-muted">آیا از لغو این نوبت مطمئن هستید؟ برای ادامه، دکمه تأیید را بزنید.</p>
                                <form method="POST" action="{{ route('client.appointments.cancel', $appointment) }}" onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')">@csrf<button type="submit" class="cca-danger">تأیید لغو نوبت</button></form>
                            </details>
                        @endif
                        <div class="cca-cancellation-info">
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

                        </div>
                    </article>
                @endforeach
            </div>
            <p class="cca-filter-empty cca-card" hidden>در این صفحه نوبتی با این فیلتر وجود ندارد. «همه» یا صفحات دیگر را بررسی کنید.</p>
            <div class="cca-pagination">{{ $appointments->links() }}</div>
        @endif
    </main>
    <script src="{{ asset('website/js/care-client-appointments.js') }}" defer></script>
@endsection
