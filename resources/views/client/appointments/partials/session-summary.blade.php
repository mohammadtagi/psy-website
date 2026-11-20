@php
    $isCancelledSession = $appointment->status === \App\Models\Appointment::STATUS_CANCELLED;
    $isExpiredSession = $appointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT
        && $appointment->hold_expires_at && $appointment->hold_expires_at->lte(now());
    $startsAt = $appointment->starts_at->copy()->setTimezone('Asia/Tehran');
    $endsAt = $appointment->ends_at->copy()->setTimezone('Asia/Tehran');
    $meetingUrl = $appointment->meeting_url;
    $safeMeetingUrl = is_string($meetingUrl)
        && filter_var($meetingUrl, FILTER_VALIDATE_URL)
        && in_array(strtolower((string) parse_url($meetingUrl, PHP_URL_SCHEME)), ['http', 'https'], true);
    $canShowMeetingLink = $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED
        && $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE
        && $safeMeetingUrl;
    // Prefer a settled transaction over a subsequent unsuccessful attempt.
    $sessionPayments = $appointment->payments()->latest('id')->get();
    $sessionPayment = $sessionPayments->first(fn ($payment) => in_array($payment->status, ['paid', 'refunded'], true)) ?? $sessionPayments->first();
    $sessionPaymentLabel = match ($sessionPayment?->status) {
        'paid' => $sessionPayment->gateway === 'free' ? 'تأیید بدون پرداخت' : 'پرداخت موفق',
        'refunded' => 'بازپرداخت ثبت شده',
        'initiated' => 'در انتظار پرداخت',
        'pending' => 'در حال پرداخت',
        'failed' => 'پرداخت ناموفق',
        'cancelled' => 'پرداخت لغو شده',
        default => 'پرداخت ثبت نشده',
    };
    $sessionGatewayLabel = match ($sessionPayment?->gateway) {
        'behpardakht' => 'به‌پرداخت ملت',
        'zarinpal' => 'زرین‌پال',
        'free' => 'بدون دریافت وجه',
        default => 'مشخص نشده',
    };
@endphp
<section class="ccs-card" aria-labelledby="ccs-info-title">
    <div class="ccs-heading"><h2 id="ccs-info-title">اطلاعات جلسه</h2><span @class(['ccs-badge', 'ccs-badge-danger' => $isCancelledSession || $isExpiredSession])>{{ $isExpiredSession ? 'مهلت پرداخت گذشته' : $appointment->sessionStatusLabel() }}</span></div>
    <dl class="ccs-info">
        <div><dt>تاریخ</dt><dd>{{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}</dd></div>
        <div><dt>ساعت · به وقت تهران</dt><dd><bdi dir="ltr">{{ $startsAt->format('H:i') }} – {{ $endsAt->format('H:i') }}</bdi></dd></div>
        <div><dt>مدت</dt><dd>{{ $appointment->duration_minutes }} دقیقه</dd></div>
        <div><dt>شیوه برگزاری</dt><dd>{{ $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE ? 'آنلاین' : 'حضوری' }}</dd></div>
    </dl>
    <p class="ccs-tracking">کد پیگیری: <bdi dir="ltr">{{ $appointment->tracking_code }}</bdi></p>
    @if ($canShowMeetingLink)
        <a class="ccs-button" href="{{ $meetingUrl }}" target="_blank" rel="noopener noreferrer">ورود به جلسه آنلاین <span class="ccs-small">(زبانه جدید)</span></a>
    @endif
    <section class="ccs-payment" aria-labelledby="ccs-payment-title">
        <div class="ccs-heading"><h3 id="ccs-payment-title">اطلاعات پرداخت</h3><span class="ccs-payment-status">{{ $sessionPaymentLabel }}</span></div>
        @if ($sessionPayment)
            <dl class="ccs-info ccs-payment-info">
                <div><dt>{{ $sessionPayment->status === 'paid' && $sessionPayment->gateway !== 'free' ? 'مبلغ پرداخت‌شده' : 'مبلغ تراکنش' }}</dt><dd>{{ number_format($sessionPayment->final_amount ?? $sessionPayment->amount) }} <span class="ccs-small">تومان</span></dd></div>
                <div><dt>روش پرداخت</dt><dd>{{ $sessionGatewayLabel }}</dd></div>
                @if ($sessionPayment->paid_at)
                    <div><dt>تاریخ پرداخت · تهران</dt><dd><bdi dir="ltr">{{ \App\Support\PersianDate::format($sessionPayment->paid_at->copy()->setTimezone('Asia/Tehran'), 'yyyy/MM/dd HH:mm') }}</bdi></dd></div>
                @endif
                @if (filled($sessionPayment->ref_id))
                    <div><dt>شمارهٔ مرجع پرداخت</dt><dd><bdi dir="ltr">{{ $sessionPayment->ref_id }}</bdi></dd></div>
                @endif
            </dl>
        @endif
    </section>
</section>
<section class="ccs-card ccs-about" aria-labelledby="ccs-about-title">
    <div class="ccs-heading"><h2 id="ccs-about-title">درباره جلسه</h2></div>
    <div class="ccs-session-grid">
    @foreach (['session_subject' => 'موضوع جلسه', 'session_discussion' => 'موارد مطرح‌شده', 'session_recommendations' => 'توصیه‌ها', 'session_exercises' => 'تمرین‌ها'] as $field => $label)
        <section><h3>{{ $label }}</h3><p class="ccs-text">{{ filled($appointment->{$field}) ? $appointment->{$field} : 'هنوز ثبت نشده است.' }}</p></section>
    @endforeach
    </div>
</section>
