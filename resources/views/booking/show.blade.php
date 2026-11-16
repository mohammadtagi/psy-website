@extends('components.layouts.app')
@section('title', 'انتخاب ساعت جلسه')
@push('styles')
<link rel="stylesheet" href="{{ asset('website/style/care-booking-time.css') }}?v=1">
@endpush
@section('content')
@php
    $dateObject = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date, 'Asia/Tehran');
    $client = auth()->user();
    $canBook = $client && $client->isClient() && \App\Support\BookingAccount::complete($client);
@endphp
<main class="bkt" dir="rtl" data-booking-time>
    <ol class="bkt-steps" aria-label="مراحل رزرو"><li>۱. اطلاعات حساب</li><li aria-current="step">۲. تاریخ و ساعت</li><li>۳. بررسی و پرداخت</li></ol>
    <header><a href="{{ route('booking.index', ['duration' => $durationMinutes]) }}">← بازگشت به اطلاعات حساب و انتخاب تاریخ</a><h1>ساعت مناسب خود را انتخاب کنید</h1><p>{{ $dateObject->locale('fa')->dayName }}، {{ \App\Support\PersianDate::format($dateObject) }} · جلسهٔ {{ $durationMinutes }} دقیقه‌ای · به وقت تهران</p></header>
    @if(session('status'))<p class="bkt-notice" role="status">{{ session('status') }}</p>@endif
    @if($errors->any())
        <div class="bkt-errors" role="alert"><strong>درخواست ثبت نشد؛ لطفاً موارد زیر را بررسی کنید.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if(!$canBook)
        <section class="bkt-card"><h2>{{ !$client ? 'ابتدا وارد حساب شوید' : ($client->isClient() ? 'اطلاعات حساب را تکمیل کنید' : 'رزرو با حساب مراجع') }}</h2><p>برای ادامهٔ رزرو، با حساب مراجع وارد شوید و نام، نام خانوادگی و تاریخ تولد خود را تأیید کنید.</p><a class="bkt-link" href="{{ route('booking.index', ['duration' => $durationMinutes]) }}">بازگشت به مرحلهٔ اطلاعات حساب</a></section>
    @endif
    <div class="bkt-grid">
        <div>
            <section class="bkt-card"><div class="bkt-section-head"><h2>ساعت‌های آزاد</h2><span>مدت جلسه: {{ $durationMinutes }} دقیقه</span></div><p>برای انتخاب، روی یکی از ساعت‌ها بزنید. زمان‌ها به وقت تهران هستند.</p>
                @if(empty($slots))
                    <p class="bkt-notice">برای این روز ساعت آزادی وجود ندارد؛ لطفاً تاریخ دیگری انتخاب کنید.</p><a class="bkt-link" href="{{ route('booking.index', ['duration' => $durationMinutes]) }}">انتخاب تاریخ دیگر</a>
                @else
                    <div class="bkt-slots" role="group" aria-label="انتخاب ساعت جلسه">
                        @foreach($slots as $slot)
                            <button type="button" class="bkt-slot" data-slot data-availability="{{ $slot['availability_id'] }}" data-start="{{ $slot['start_time'] }}" data-end="{{ $slot['end_time'] }}" aria-pressed="false" @disabled(!$canBook)>
                                <strong dir="ltr">{{ $slot['start_time'] }}</strong><span>تا {{ $slot['end_time'] }}</span><small>{{ $loop->first ? 'اولین ساعت آزاد' : 'قابل رزرو' }}</small>
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>
            @if($canBook && !empty($slots))
                <section class="bkt-card"><h2>شیوهٔ برگزاری جلسه</h2>
                    <form method="POST" action="{{ route('booking.store', ['date' => $date]) }}" data-booking-form>
                        @csrf
                        <input type="hidden" name="availability_id" value="{{ old('availability_id') }}">
                        <input type="hidden" name="start_time" value="{{ old('start_time') }}">
                        <input type="hidden" name="duration_minutes" value="{{ $durationMinutes }}">
                        <fieldset><legend class="bkt-sr">شیوهٔ برگزاری را انتخاب کنید</legend><div class="bkt-types">
                            @foreach($sessionTypes as $sessionType)
                                <label><input type="radio" name="session_type" value="{{ $sessionType['value'] }}" @checked(old('session_type', 'online') === $sessionType['value']) required><span>{{ $sessionType['label'] }}</span></label>
                            @endforeach
                        </div></fieldset>
                        <p class="bkt-selection" data-selection role="status">ابتدا یک ساعت از فهرست بالا انتخاب کنید.</p>
                        <p class="bkt-notice">با ادامه، نوبت به‌صورت موقت ثبت می‌شود. تأیید نهایی پس از پرداخت موفق یا تأیید نوبت رایگان انجام خواهد شد.</p>
                        <button type="submit" class="bkt-primary" data-submit disabled>ادامه به بررسی و پرداخت ←</button>
                        <noscript><p class="bkt-errors">برای انتخاب ساعت و ادامه، جاوااسکریپت مرورگر را فعال کنید.</p></noscript>
                    </form>
                </section>
            @endif
        </div>
        <aside class="bkt-card bkt-summary"><h2>خلاصهٔ جلسه</h2><dl><div><dt>تاریخ</dt><dd>{{ \App\Support\PersianDate::format($dateObject) }}</dd></div><div><dt>ساعت</dt><dd data-summary-time aria-live="polite">انتخاب نشده</dd></div><div><dt>مدت</dt><dd>{{ $durationMinutes }} دقیقه</dd></div><div><dt>شیوهٔ برگزاری</dt><dd data-summary-type>انتخاب نشده</dd></div></dl><p>مبلغ نهایی در مرحلهٔ بررسی و پرداخت نمایش داده می‌شود.</p>
            @if($canBook)
                <div class="bkt-account"><h3>اطلاعات حساب</h3><dl><div><dt>مراجع</dt><dd>{{ $client->first_name }} {{ $client->last_name }}</dd></div><div><dt>تاریخ تولد</dt><dd>{{ \App\Support\PersianDate::format($client->birth_date) }}</dd></div></dl><p>رزرو با اطلاعات ذخیره‌شدهٔ حساب شما ثبت می‌شود.</p><a href="{{ route('booking.index', ['duration' => $durationMinutes]) }}">ویرایش اطلاعات حساب</a></div>
            @endif
        </aside>
    </div>
</main>
@endsection
@push('scripts')
<script src="{{ asset('website/js/care-booking-time.js') }}?v=1" defer></script>
@endpush
