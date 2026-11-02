<x-layouts.psychologist-preview title="داشبورد مشاور">
    @php
        $fa = static fn ($value) => strtr((string) $value, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']);
        $confirmedToday = $todayAppointments->where('status', \App\Models\Appointment::STATUS_CONFIRMED)->count();
        $pendingToday = $todayAppointments->where('status', \App\Models\Appointment::STATUS_PENDING_PAYMENT)->count();
    @endphp
    <section class="welcome" aria-labelledby="page-heading">
        <div><span class="eyebrow">نمای کلی فعالیت‌ها</span><h1 id="page-heading">برنامه امروز، در یک نگاه</h1><p>نوبت‌ها را مرور کنید و به اطلاعات هر جلسه دسترسی داشته باشید.</p></div>
        <a class="button button-primary" href="{{ route('psychologist.availabilities.index') }}"><svg class="icon" aria-hidden="true"><use href="#calendar"/></svg>مدیریت زمان‌های حضور</a>
    </section>
    <div class="stats">
        <section class="stat stat-featured"><div class="stat-heading"><h2>نوبت‌های امروز</h2><svg class="icon" aria-hidden="true"><use href="#calendar"/></svg></div><strong>{{ $fa($todayAppointments->count()) }}</strong><p>{{ $fa($confirmedToday) }} تأییدشده <span aria-hidden="true">·</span> {{ $fa($pendingToday) }} در انتظار پرداخت</p></section>
        <section class="stat"><div class="stat-heading"><h2>نوبت‌های روزهای آینده</h2><svg class="icon" aria-hidden="true"><use href="#users"/></svg></div><strong>{{ $fa($upcomingAppointmentsCount) }}</strong><p>از فردا؛ تأییدشده و در انتظار پرداخت</p></section>
        <section class="stat"><div class="stat-heading"><h2>بازه‌های حضور آینده</h2><svg class="icon" aria-hidden="true"><use href="#clock"/></svg></div><strong>{{ $fa($upcomingAvailabilitiesCount) }}</strong><p>بازه‌های فعال؛ نه تعداد نوبت‌های آزاد</p></section>
    </div>
    <div class="dashboard-grid">
        <section class="panel today-panel" aria-labelledby="today-heading">
            <div class="panel-header"><div><span class="eyebrow">برنامه روزانه</span><h2 id="today-heading">نوبت‌های امروز <span class="count">{{ $fa($todayAppointments->count()) }}</span></h2></div><a class="text-link" href="{{ route('psychologist.appointments.index') }}">همه نوبت‌ها <svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a></div>
            <p class="section-description">به ترتیب ساعت شروع؛ وضعیت برگزاری مستقل از تأیید رزرو است.</p>
            <ul class="appointment-list">
                @forelse ($todayAppointments as $appointment)
                    @include('psychologist.partials.dashboard-appointment', ['showDate' => false])
                @empty
                    <li class="empty-state"><svg class="icon" aria-hidden="true"><use href="#calendar"/></svg><h3>امروز نوبتی در این فهرست ندارید</h3><p>نوبت‌های تأییدشده و در انتظار پرداخت امروز اینجا نمایش داده می‌شوند.</p><a class="button button-secondary" href="{{ route('psychologist.appointments.index') }}">مشاهده همه نوبت‌ها</a></li>
                @endforelse
            </ul>
        </section>
        <aside class="side-panels">
            <section class="panel quick-actions"><span class="eyebrow">کارهای روزمره</span><h2>از کجا شروع کنیم؟</h2><a href="{{ route('psychologist.clients.index') }}"><span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#users"/></svg></span><span><strong>مراجعان و پرونده‌ها</strong><small>پرونده، مسیر درمان و ثبت دستی جلسه</small></span><svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a><a href="{{ route('psychologist.availabilities.index') }}"><span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#clock"/></svg></span><span><strong>تنظیم زمان‌های حضور</strong><small>مدیریت برنامه قابل رزرو</small></span><svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a></section>
            <section class="panel availability-panel"><div class="panel-header"><h2>زمان‌های حضور پیش‌رو</h2></div><p class="section-description">حداکثر ۸ بازه نزدیک؛ ممکن است بخشی از هر بازه رزرو شده باشد.</p><ul class="availability-list">
                    @forelse ($upcomingAvailabilities as $availability)
                        <li><span>{{ \App\Support\PersianDate::format($availability->starts_at, 'EEE، d MMM yyyy') }}</span><strong><bdi>{{ \App\Support\PersianDate::format($availability->starts_at, 'HH:mm') }}</bdi><span class="muted"> تا </span><bdi>{{ \App\Support\PersianDate::format($availability->ends_at, $availability->starts_at->copy()->setTimezone('Asia/Tehran')->isSameDay($availability->ends_at->copy()->setTimezone('Asia/Tehran')) ? 'HH:mm' : 'yyyy/MM/dd HH:mm') }}</bdi></strong></li>
                    @empty
                        <li class="compact-empty">بازه حضور آینده‌ای ثبت نشده است.</li>
                    @endforelse
                </ul><a class="text-link" href="{{ route('psychologist.availabilities.index') }}">مدیریت همه بازه‌ها <svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a></section>
        </aside>
        <section class="panel upcoming-panel" aria-labelledby="upcoming-heading"><div class="panel-header"><div><span class="eyebrow">نگاهی به روزهای بعد</span><h2 id="upcoming-heading">نوبت‌های پیش‌رو</h2></div><a class="text-link" href="{{ route('psychologist.appointments.index') }}">همه نوبت‌ها <svg class="icon" aria-hidden="true"><use href="#arrow"/></svg></a></div><p class="section-description">۸ نوبت نزدیک از فردا؛ بدون نوبت‌های لغوشده.</p><ul class="appointment-list">
                @forelse ($upcomingAppointments as $appointment)
                    @include('psychologist.partials.dashboard-appointment', ['showDate' => true])
                @empty
                    <li class="empty-state"><h3>نوبت آینده‌ای در این فهرست نیست</h3><p>نوبت‌های جدید روزهای بعد در این بخش قرار می‌گیرند.</p></li>
                @endforelse
            </ul></section>
    </div>
</x-layouts.psychologist-preview>
