@extends('components.layouts.app')
@section('title', 'نوبت‌های من')
@push('styles')
<link rel="stylesheet" href="{{ asset('website/style/care-client-appointments-v7.css') }}?v=1">
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
@php
    $isCancelled = $appointment->status === \App\Models\Appointment::STATUS_CANCELLED;
    $canEnter = $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED
        && $endsAtUtc->greaterThan($now) && filled($appointment->meeting_url);
@endphp
<div class="cca-card-header">
    <div class="cca-identity">
        <div><h2>{{ $appointment->psychologist->first_name }} {{ $appointment->psychologist->last_name }}</h2><p class="cca-muted">جلسهٔ مشاوره</p></div>
    </div>
    <a class="cca-detail-link" href="{{ route('client.appointments.session.show', $appointment) }}">مشاهدهٔ جلسه <svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="m14 6-6 6 6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
</div>
<dl class="cca-facts">
    <div class="cca-date"><dt>زمان جلسه</dt><dd>{{ \App\Support\PersianDate::format($startsAt, 'd MMMM yyyy') }}<span class="cca-hours"><bdi dir="ltr">{{ $startsAt->format('H:i') }} – {{ $endsAt->format('H:i') }}</bdi></span></dd></div>
    <div><dt>وضعیت نوبت</dt><dd><span @class(['cca-badge', 'cca-warning' => $isPendingPayment, 'cca-cancelled' => $isCancelled])>{{ $statusLabel }}</span></dd></div>
    <div><dt>نوع مشاوره</dt><dd>{{ match ($appointment->session_type) { \App\Models\Appointment::SESSION_TYPE_ONLINE => 'آنلاین', \App\Models\Appointment::SESSION_TYPE_IN_PERSON => 'حضوری', default => 'مشخص نشده' } }}</dd></div>
    <div><dt>کد پیگیری</dt><dd class="cca-tracking"><bdi dir="ltr">{{ $appointment->tracking_code ?: '—' }}</bdi></dd></div>
    <div><dt>مبلغ جلسه</dt><dd>{{ number_format($appointment->amount) }} <span class="cca-unit">تومان</span>@if($latestPayment?->status !== \App\Models\Payment::STATUS_PAID)<span class="cca-payment-state">{{ $paymentStatusLabel }}</span>@endif</dd></div>
</dl>
<div class="cca-cancellation-info">
                        @if ($appointment->status === \App\Models\Appointment::STATUS_CANCELLED)
                            @php
                                $cancelledAt = $appointment->cancelled_at
                                    ? $appointment->cancelled_at->copy()->setTimezone('Asia/Tehran')
                                    : null;

                                $cancelledByPsychologist = $appointment->cancelled_by !== null
                                    && (int) $appointment->cancelled_by === (int) $appointment->psychologist_id;
                            @endphp

                            <div class="cca-cancel-context">
                                <p class="cca-cancel-caption">
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
                                    <div class="cca-cancel-reason">
                                        <p class="cca-reason-title">
                                            دلیل لغو نوبت
                                        </p>

                                        <p class="cca-reason-text">{{ $appointment->cancellation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

</div>
@if(! $isCancelled)
<footer class="cca-card-footer">
    <div class="cca-primary-area">
        @if($canPay)
            <form method="POST" action="{{ route('payments.pay', $appointment) }}">@csrf<button type="submit" class="cca-button">تکمیل پرداخت</button></form>
            @if($appointment->hold_expires_at)
                <p class="cca-footnote">مهلت پرداخت: {{ \App\Support\PersianDate::format($appointment->hold_expires_at, 'MM/dd، HH:mm') }}</p>
            @endif
        @elseif($canEnter)
            <a class="cca-button" href="{{ $appointment->meeting_url }}" target="_blank" rel="noopener noreferrer">ورود به جلسه <span aria-hidden="true">↗</span><span class="cca-sr-only">در زبانهٔ جدید</span></a>
        @elseif($upcoming && ! $isPendingPayment)
            <p class="cca-footnote">لینک ورود هنوز ثبت نشده است.</p>
        @elseif($holdExpired)
            <p class="cca-footnote">برای رزرو دوباره، زمان‌های موجود را ببینید.</p>
        @endif
    </div>
    <div class="cca-secondary-area">
        @if($canCancel)
            <form method="POST" action="{{ route('client.appointments.cancel', $appointment) }}" data-cancel-form>@csrf<button type="submit" class="cca-cancel-button" disabled>لغو نوبت</button><noscript><span class="cca-footnote">برای تأیید لغو، جاوااسکریپت را فعال کنید.</span></noscript></form>
        @endif
        @if(! $upcoming)
            <a class="cca-secondary" href="{{ route('booking.index') }}">رزرو جلسه جدید</a>
        @endif
    </div>
</footer>
@endif
</article>
@endforeach
</div>
<p class="cca-filter-empty cca-card" hidden>در این صفحه نوبتی با این فیلتر وجود ندارد. «همه» یا صفحات دیگر را بررسی کنید.</p>
<div class="cca-pagination">{{ $appointments->links() }}</div>
@endif
</main>
<script src="{{ asset('website/js/care-client-appointments-v5.js') }}" defer></script>
@endsection
