@extends('components.layouts.app')
@section('title', 'مدیریت بازه‌های حضور')
@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-availability.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/care-datetime-picker.css') }}">
@endpush
@section('content')
    <main class="cv-page">
        <header class="cv-header"><div><p class="cv-muted">پنل مشاور / برنامه حضور</p><h1>مدیریت زمان‌های حضور</h1><p class="cv-muted">زمان‌های در دسترس خود را برای برگزاری جلسات مشخص کنید.</p></div><a class="cv-button cv-secondary" href="{{ route('psychologist.appointments.index') }}">مشاهده نوبت‌ها</a></header>
        @if(session('status'))<div class="cv-alert cv-success" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="cv-alert cv-error" role="alert"><strong>ثبت یا تغییر بازه انجام نشد</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <section class="cv-panel" aria-labelledby="cv-create-title">
            <header class="cv-panel-head"><h2 id="cv-create-title">ایجاد بازه حضور</h2><p class="cv-muted">تاریخ شمسی · ساعت تهران</p></header>
            <form method="POST" action="{{ route('psychologist.availabilities.store') }}" id="cv-form" class="cv-form">
                @csrf
                <fieldset><legend>۱. شیوه ثبت</legend><div class="cv-grid">
                        <label class="cv-option"><input type="radio" name="mode" value="single" @checked(old('mode', 'single') === 'single')><span><strong>یک روز</strong><small>یک بازه برای یک تاریخ مشخص</small></span></label>
                        <label class="cv-option"><input type="radio" name="mode" value="recurring" @checked(old('mode') === 'recurring')><span><strong>تکرارشونده</strong><small>ساعت ثابت در روزهای انتخابی هفته</small></span></label>
                    </div></fieldset>

                <fieldset><legend>۲. تاریخ و روزهای حضور</legend><p id="cv-date-help" class="cv-muted">تاریخ را مانند ۱۴۰۵/۰۷/۰۱ وارد کنید. در حالت تکرارشونده، محدوده شامل تاریخ شروع و پایان است؛ حداکثر ۳۶۶ روز.</p>
                    <div class="cv-grid">
                        <div><label for="cv-from">تاریخ شروع</label><div class="cv-date-input"><input id="cv-from" name="from_date" type="text" dir="ltr" value="{{ old('from_date') }}" placeholder="۱۴۰۵/۰۷/۰۱" maxlength="10" pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}" required aria-describedby="cv-date-help" aria-invalid="{{ $errors->has('from_date') ? 'true' : 'false' }}"><button type="button" class="cv-calendar-icon" data-date-target="cv-from" aria-label="انتخاب تاریخ شروع از تقویم" aria-haspopup="dialog" hidden><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h3M14 15h3"/></svg></button></div></div>
                        <div id="cv-to-field"><label for="cv-to">تاریخ پایان</label><div class="cv-date-input"><input id="cv-to" name="to_date" type="text" dir="ltr" value="{{ old('to_date') }}" placeholder="۱۴۰۵/۰۷/۳۰" maxlength="10" pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}" required aria-describedby="cv-date-help" aria-invalid="{{ $errors->has('to_date') ? 'true' : 'false' }}"><button type="button" class="cv-calendar-icon" data-date-target="cv-to" aria-label="انتخاب تاریخ پایان از تقویم" aria-haspopup="dialog" hidden><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h3M14 15h3"/></svg></button></div></div>
                    </div>
                    <fieldset id="cv-weekdays"><legend>روزهای هفته</legend><p class="cv-muted">برای ثبت تکرارشونده، حداقل یک روز مطابق محدوده تاریخ انتخاب کنید.</p><div class="cv-days">
                            @php
                                $selectedDays = array_map('strval', (array) old('weekdays', []));
                                $weekdays = [6 => 'شنبه', 0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه‌شنبه', 3 => 'چهارشنبه', 4 => 'پنجشنبه', 5 => 'جمعه'];
                            @endphp
                            @foreach($weekdays as $value => $label)<label class="cv-day"><input type="checkbox" name="weekdays[]" value="{{ $value }}" @checked(in_array((string) $value, $selectedDays, true))><span>{{ $label }}</span></label>@endforeach
                        </div></fieldset></fieldset>
                <fieldset><legend>۳. ساعت حضور</legend><div class="cv-grid">
                        <div><label for="cv-start">ساعت شروع</label><input id="cv-start" type="time" name="start_time" dir="ltr" value="{{ old('start_time') }}" required aria-describedby="cv-time-help" aria-invalid="{{ $errors->has('start_time') ? 'true' : 'false' }}"></div>
                        <div><label for="cv-end">ساعت پایان</label><input id="cv-end" type="time" name="end_time" dir="ltr" value="{{ old('end_time') }}" required aria-describedby="cv-time-help" aria-invalid="{{ $errors->has('end_time') ? 'true' : 'false' }}"></div>
                    </div><p id="cv-time-help" class="cv-muted">پایان باید بعد از شروع و در همان روز باشد. بازه باید برای حداقل یک جلسه ۴۵ دقیقه‌ای با شروع روی ساعت یا نیم‌ساعت کافی باشد؛ مثلاً ۹:۱۵ تا ۱۰:۱۵.</p></fieldset>
                <aside class="cv-guide"><strong>پیش از ثبت</strong><ul><li>شروع تمام بازه‌ها باید در آینده باشد.</li><li>تداخل با برنامه موجود هنگام ثبت بررسی می‌شود؛ در صورت خطا هیچ‌یک از بازه‌های این درخواست ثبت نمی‌شود.</li></ul></aside>
                <noscript><p class="cv-alert">جاوااسکریپت غیرفعال است؛ در حالت یک‌روز، تاریخ پایان را برابر تاریخ شروع وارد کنید. روزهای هفته فقط برای حالت تکرارشونده استفاده می‌شوند.</p></noscript>
                <div class="cv-submit"><span class="cv-muted">ثبت بازه به معنی رزرو نوبت نیست.</span><button class="cv-button cv-primary" type="submit">ثبت بازه حضور</button></div>
            </form></section>
        <section class="cv-panel" aria-labelledby="cv-list-title"><header class="cv-panel-head cv-header"><div><h2 id="cv-list-title">بازه‌های امروز و آینده <span class="cv-count">{{ number_format($availabilities->total()) }}</span></h2><p class="cv-muted">تفکیک بر اساس وضعیت ثبت‌شده؛ گذشته‌بودن زمان، وضعیت بازه را تغییر نمی‌دهد. گروه‌ها و شمارنده‌های زیر فقط مربوط به همین صفحه‌اند.</p></div></header>
            @if($availabilities->isEmpty())
                <div class="cv-empty"><h3>{{ $availabilities->total() ? 'این صفحه بازه‌ای ندارد' : 'هنوز بازه‌ای برای امروز و آینده ثبت نشده است' }}</h3><p class="cv-muted">{{ $availabilities->total() ? 'برای مشاهده بازه‌ها به صفحه اول برگردید.' : 'از فرم بالا، اولین زمان حضور خود را ثبت کنید.' }}</p>@if($availabilities->total())<a class="cv-button cv-secondary" href="{{ $availabilities->url(1) }}">صفحه اول</a>@endif</div>
            @else
                @php
                    $pageGroups = $availabilities->getCollection()->sortBy('starts_at')->groupBy(fn ($item) => $item->status === \App\Models\Availability::STATUS_ACTIVE ? 'active' : 'inactive');
                @endphp
                @foreach(['active' => 'بازه‌های فعال', 'inactive' => 'بازه‌های غیرفعال'] as $groupKey => $groupLabel)
                    @php $groupItems = $pageGroups->get($groupKey, collect()); @endphp
                    <details class="cv-status-group" @if($groupKey === 'active' || !$pageGroups->has('active')) open @endif>
                        <summary>{{ $groupLabel }} <span class="cv-count">{{ number_format($groupItems->count()) }}</span><span class="cv-muted">در این صفحه</span></summary>
                        @forelse($groupItems->groupBy(fn ($item) => $item->starts_at->copy()->setTimezone($timezone)->format('Y-m-d')) as $dateItems)
                            <section class="cv-date-group">
                                <h3 class="cv-date-heading">{{ \App\Support\PersianDate::format($dateItems->first()->starts_at->copy()->setTimezone($timezone), 'EEEE yyyy/MM/dd') }}</h3>
                                <ul class="cv-list">
                                    @foreach($dateItems as $availability)
                                        @php
                                            $startsAt = $availability->starts_at->copy()->setTimezone($timezone);
                                            $endsAt = $availability->ends_at->copy()->setTimezone($timezone);
                                            $hasActiveAppointments = $availability->active_appointments_count > 0;
                                            $isActive = $availability->status === \App\Models\Availability::STATUS_ACTIVE;
                                        @endphp
                                        <li class="cv-row"><div class="cv-date"><span class="cv-muted">ساعت حضور</span><span class="cv-time"><bdi>{{ $startsAt->format('H:i') }} – {{ $endsAt->format('H:i') }}</bdi></span></div>
                                            <div class="cv-state"><span class="cv-badge {{ $isActive ? 'cv-active' : '' }}">{{ $isActive ? 'فعال' : 'غیرفعال' }}</span>@if($endsAt->isPast())<span class="cv-muted">زمان بازه پایان یافته</span>@endif<p>نوبت‌های فعال: <strong>{{ number_format($availability->active_appointments_count) }}</strong></p></div>
                                            <div class="cv-action">
                                                @if($isActive && !$hasActiveAppointments)
                                                    <form method="POST" action="{{ route('psychologist.availabilities.destroy', $availability) }}" onsubmit="return confirm('آیا از غیرفعال‌کردن این بازه مطمئن هستید؟')">@csrf @method('DELETE')<button class="cv-button cv-danger" type="submit" aria-label="غیرفعال‌کردن بازه {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }} ساعت {{ $startsAt->format('H:i') }}">غیرفعال‌کردن</button></form>
                                                @elseif($hasActiveAppointments)<p class="cv-muted">به دلیل وجود نوبت فعال، قابل غیرفعال‌سازی نیست.</p>
                                                @else<p class="cv-muted">این بازه غیرفعال شده است.</p>@endif
                                            </div></li>
                                    @endforeach
                                </ul>
                            </section>
                        @empty
                            <p class="cv-group-empty">در این صفحه {{ $groupLabel }} وجود ندارد.</p>
                        @endforelse
                    </details>
                @endforeach
            @endif
        </section><p class="cv-footnote">نوبت‌های فعال شامل رزروهای تأییدشده و در انتظار پرداخت است؛ این عدد ظرفیت باقی‌مانده نیست.</p><div class="cv-pagination">{{ $availabilities->links() }}</div>
    </main>
@endsection
@push('scripts')
    <script src="{{ asset('website/js/care-availability.js') }}" defer></script>
    <script src="{{ asset('website/js/care-availability-date-picker.js') }}" defer></script>
@endpush
