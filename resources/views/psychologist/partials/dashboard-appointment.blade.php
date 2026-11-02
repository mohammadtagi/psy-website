@php
    $clientName = trim(($appointment->client?->first_name ?? '') . ' ' . ($appointment->client?->last_name ?? ''));
    $pending = $appointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT;
    $sessionType = match ($appointment->session_type) {
        \App\Models\Appointment::SESSION_TYPE_ONLINE => 'آنلاین',
        \App\Models\Appointment::SESSION_TYPE_IN_PERSON => 'حضوری',
        default => 'نوع جلسه نامشخص',
    };
    $attendance = match ($appointment->attendance_status) {
        \App\Models\Appointment::ATTENDANCE_HELD => 'برگزارشده',
        \App\Models\Appointment::ATTENDANCE_ABSENT => 'عدم حضور مراجع',
        default => 'ثبت نشده',
    };
@endphp
<li class="appointment">
    <div class="appointment-time"><time datetime="{{ $appointment->starts_at->toIso8601String() }}">{{ \App\Support\PersianDate::format($appointment->starts_at, 'HH:mm') }}</time>@if($showDate)<small>{{ \App\Support\PersianDate::format($appointment->starts_at, 'EEE، d MMM yyyy') }}</small>@else<small>{{ $fa($appointment->duration_minutes) }} دقیقه</small>@endif</div>
    <div class="appointment-body"><h3>@if($clientName !== ''){{ $clientName }}@else<bdi>{{ $appointment->client?->mobile ?? 'مراجع نامشخص' }}</bdi>@endif</h3><p>{{ $sessionType }}@if($showDate) · {{ $fa($appointment->duration_minutes) }} دقیقه@endif</p><div class="appointment-status"><span class="badge {{ $pending ? 'badge-pending' : 'badge-confirmed' }}">رزرو: {{ $pending ? 'در انتظار پرداخت' : 'تأییدشده' }}</span>@unless($pending)<span class="attendance">برگزاری: {{ $attendance }}</span>@endunless</div></div>
    <a class="session-link" href="{{ route('psychologist.appointments.session.show', $appointment) }}" aria-label="جزئیات جلسه {{ $clientName ?: 'مراجع' }}، {{ \App\Support\PersianDate::format($appointment->starts_at, 'yyyy/MM/dd HH:mm') }}">جزئیات جلسه <svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a>
</li>
