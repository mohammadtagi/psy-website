@php use App\Support\PersianDate; @endphp
@php use App\Models\Appointment; @endphp
@php use App\Models\Payment; @endphp
@extends('components.layouts.app')
@section('title', 'مدیریت نوبت‌ها')
@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-appointments.css') }}">
@endpush
@section('content')
    <main class="ca-page">
        <header class="ca-header">
            <div><p class="ca-eyebrow">پنل مشاور / برنامه جلسات</p><h1>مدیریت نوبت‌ها</h1><p class="ca-muted">پیگیری رزرو، پرداخت و برگزاری؛ هرکدام با وضعیت مستقل.</p></div>
            <a class="ca-button ca-secondary" href="{{ route('psychologist.availabilities.index') }}">تنظیم زمان‌های حضور</a>
        </header>
        @if(session('status'))<div class="ca-alert ca-success" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="ca-alert ca-error" role="alert"><strong>درخواست انجام نشد</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <section class="ca-panel" aria-labelledby="ca-list-title">
            <header class="ca-list-header"><div><h2 id="ca-list-title">فهرست نوبت‌ها <span class="ca-count">{{ number_format($appointments->total()) }}</span></h2><p class="ca-muted">مرتب‌شده از قدیمی‌ترین تاریخ جلسه · ساعت‌ها به وقت تهران</p></div>
                @if($appointments->total())<span class="ca-range">نمایش {{ number_format($appointments->firstItem() ?? 0) }} تا {{ number_format($appointments->lastItem() ?? 0) }} از {{ number_format($appointments->total()) }}</span>@endif
            </header>
            @if($appointments->count())
                <div class="ca-scroll" role="region" aria-label="فهرست نوبت‌ها؛ در نمایشگر متوسط امکان پیمایش افقی وجود دارد" tabindex="0">
                    <table class="ca-table" role="table">
                        <caption class="ca-sr-only">نوبت‌های مشاور، وضعیت‌های مستقل و عملیات هر جلسه</caption>
                        <thead role="rowgroup"><tr role="row">
                            @foreach(['مراجع','زمان و نوع جلسه','وضعیت رزرو','برگزاری جلسه','آخرین تلاش پرداخت','عملیات'] as $heading)<th scope="col" role="columnheader">{{ $heading }}</th>@endforeach
                        </tr></thead>
                        <tbody role="rowgroup">
                        @foreach($appointments as $appointment)
                            @php
                                $startsAt = $appointment->starts_at->copy()->setTimezone('Asia/Tehran');
                                $endsAt = $appointment->ends_at->copy()->setTimezone('Asia/Tehran');
                                $isPendingAndValid = $appointment->status === Appointment::STATUS_PENDING_PAYMENT && $appointment->hold_expires_at?->isFuture();
                                $isActive = $appointment->status === Appointment::STATUS_CONFIRMED || $isPendingAndValid;
                                $canCancel = in_array($appointment->status, [Appointment::STATUS_CONFIRMED, Appointment::STATUS_PENDING_PAYMENT], true) && $appointment->starts_at->isFuture();
                                $latestPayment = $appointment->payments->first();
                                [$bookingLabel, $bookingTone] = match($appointment->status) {
                                    Appointment::STATUS_CONFIRMED => ['تأییدشده', 'green'],
                                    Appointment::STATUS_PENDING_PAYMENT => ['در انتظار پرداخت', 'gold'],
                                    Appointment::STATUS_CANCELLED => ['لغوشده', 'red'],
                                    Appointment::STATUS_COMPLETED => ['تکمیل‌شده (وضعیت قدیمی)', 'neutral'],
                                    Appointment::STATUS_NO_SHOW => ['عدم حضور (وضعیت قدیمی)', 'neutral'],
                                    default => ['نامشخص', 'neutral'],
                                };
                                $sessionLabel = in_array($appointment->status, [Appointment::STATUS_CONFIRMED, Appointment::STATUS_COMPLETED, Appointment::STATUS_NO_SHOW], true) ? $appointment->sessionStatusLabel() : '—';
                                [$paymentLabel, $paymentTone] = match($latestPayment?->status) {
                                    Payment::STATUS_PAID => ['پرداخت‌شده', 'green'],
                                    Payment::STATUS_PENDING => ['در انتظار پرداخت', 'gold'],
                                    Payment::STATUS_INITIATED => ['شروع‌شده', 'gold'],
                                    Payment::STATUS_FAILED => ['ناموفق', 'red'],
                                    Payment::STATUS_CANCELLED => ['لغوشده', 'neutral'],
                                    default => [$latestPayment ? 'نامشخص' : 'بدون سابقه پرداخت', 'neutral'],
                                };
                                $clientName = trim(($appointment->client?->first_name ?? '').' '.($appointment->client?->last_name ?? ''));
                            @endphp
                            <tr role="row">
                                <td role="cell" class="ca-client"><span class="ca-mobile-label" aria-hidden="true">مراجع</span><strong>{{ $clientName !== '' ? $clientName : 'نام ثبت نشده' }}</strong><small>کد پیگیری: <bdi>{{ $appointment->tracking_code }}</bdi></small></td>
                                <td role="cell"><span class="ca-mobile-label" aria-hidden="true">زمان و نوع جلسه</span><strong>{{ PersianDate::format($startsAt, 'yyyy/MM/dd') }}</strong><small><bdi>{{ $startsAt->format('H:i') }} – {{ $endsAt->format('H:i') }}</bdi> · {{ $appointment->duration_minutes }} دقیقه</small><span class="ca-type">{{ match($appointment->session_type) { Appointment::SESSION_TYPE_ONLINE => 'آنلاین', Appointment::SESSION_TYPE_IN_PERSON => 'حضوری', default => 'نوع جلسه نامشخص' } }}</span></td>
                                <td role="cell"><span class="ca-mobile-label" aria-hidden="true">وضعیت رزرو</span><span class="ca-badge ca-{{ $bookingTone }}">{{ $bookingLabel }}</span>@if($appointment->status === Appointment::STATUS_PENDING_PAYMENT && $appointment->hold_expires_at?->isPast())<small>مهلت پرداخت پایان یافته است</small>@endif</td>
                                <td role="cell"><span class="ca-mobile-label" aria-hidden="true">برگزاری جلسه</span><span class="ca-session">{{ $sessionLabel }}</span>@if($sessionLabel === '—')<small>وضعیت برگزاری قابل نمایش نیست</small>@endif</td>
                                <td role="cell"><span class="ca-mobile-label" aria-hidden="true">آخرین تلاش پرداخت</span><span class="ca-badge ca-{{ $paymentTone }}">{{ $paymentLabel }}</span><small class="ca-amount">{{ number_format((int) ($latestPayment ? $latestPayment->amount : $appointment->amount)) }} تومان</small><small>{{ $latestPayment ? 'مبلغ آخرین تلاش پرداخت' : 'مبلغ نوبت' }}</small></td>
                                <td role="cell" class="ca-actions"><span class="ca-mobile-label" aria-hidden="true">عملیات</span>
                                    @if($isActive)<a class="ca-button ca-primary" href="{{ route('psychologist.appointments.session.show', $appointment) }}">مدیریت جلسه<span class="ca-sr-only"> با {{ $clientName }}، {{ PersianDate::format($startsAt, 'yyyy/MM/dd') }} ساعت {{ $startsAt->format('H:i') }}</span></a>@endif
                                    @if($canCancel)
                                        <details class="ca-cancel"><summary>لغو نوبت</summary>
                                            <form method="POST" action="{{ route('psychologist.appointments.cancel', $appointment) }}" onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')">
                                                @csrf
                                                <label for="ca-reason-{{ $appointment->getKey() }}">دلیل لغو <span>(اختیاری)</span></label>
                                                <textarea id="ca-reason-{{ $appointment->getKey() }}" name="cancellation_reason" rows="3" maxlength="255" placeholder="حداکثر ۲۵۵ نویسه"></textarea>
                                                <p>لغو برای این نوبت ثبت می‌شود.</p><button type="submit" class="ca-button ca-danger">تأیید لغو نوبت</button>
                                            </form>
                                        </details>
                                    @endif
                                    @if(!$isActive && !$canCancel)<span class="ca-muted">بدون عملیات</span>@endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="ca-empty"><span aria-hidden="true">◇</span><h3>{{ $appointments->total() ? 'این صفحه نوبتی ندارد' : 'هنوز نوبتی ثبت نشده است' }}</h3><p class="ca-muted">{{ $appointments->total() ? 'برای مشاهده نوبت‌ها به صفحه اول برگردید.' : 'پس از ثبت رزرو، اطلاعات جلسه در این بخش نمایش داده می‌شود.' }}</p>@if($appointments->total())<a class="ca-button ca-secondary" href="{{ $appointments->url(1) }}">صفحه اول</a>@endif</div>
            @endif
        </section>
        <p class="ca-note">وضعیت پرداخت مربوط به آخرین تلاش ثبت‌شده است؛ لغو رزرو به معنی استرداد وجه نیست.</p>
        <div class="ca-pagination">{{ $appointments->links() }}</div>
    </main>
@endsection
