@php
    $startsAt = $appointment->starts_at->copy()->setTimezone('Asia/Tehran');
    $endsAt = $appointment->ends_at->copy()->setTimezone('Asia/Tehran');
    $meetingUrl = $appointment->meeting_url;
    $safeMeetingUrl = is_string($meetingUrl)
        && filter_var($meetingUrl, FILTER_VALIDATE_URL)
        && in_array(strtolower((string) parse_url($meetingUrl, PHP_URL_SCHEME)), ['http', 'https'], true);
    $canShowMeetingLink = $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED
        && $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE
        && $safeMeetingUrl;
@endphp
<section class="ccs-card" aria-labelledby="ccs-info-title">
    <div class="ccs-heading"><h2 id="ccs-info-title">اطلاعات جلسه</h2><span class="ccs-badge">{{ $appointment->sessionStatusLabel() }}</span></div>
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
</section>
<details class="ccs-card ccs-session-details">
    <summary>موضوع، توصیه‌ها و تمرین‌های جلسه</summary>
    <div class="ccs-session-grid">
    @foreach (['session_subject' => 'موضوع جلسه', 'session_discussion' => 'موارد مطرح‌شده', 'session_recommendations' => 'توصیه‌ها', 'session_exercises' => 'تمرین‌ها'] as $field => $label)
        <section><h3>{{ $label }}</h3><p class="ccs-text">{{ filled($appointment->{$field}) ? $appointment->{$field} : 'هنوز ثبت نشده است.' }}</p></section>
    @endforeach
    </div>
</details>
