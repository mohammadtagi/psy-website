<x-layouts.app>
    @section('title', 'داشبورد مراجع')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('website/style/care-client-dashboard.css') }}">
    @endpush
    <main class="ccd">
        <header class="ccd-header">
            <div><p class="ccd-eyebrow">پنل مراجع</p><h1>داشبورد شما</h1><p class="ccd-muted">نوبت‌ها و مسیر درمان خود را در یک نگاه دنبال کنید.</p></div>
            <a class="ccd-button" href="{{ route('booking.index') }}">رزرو جلسه جدید <span aria-hidden="true">←</span></a>
        </header>
        <div class="ccd-stats">
            <section class="ccd-card"><h2>نوبت‌های قطعی آینده</h2><strong class="ccd-number">{{ $activeAppointmentsCount }}</strong><p class="ccd-muted">جلسه تأییدشده پیش رو</p></section>
            <section class="ccd-card"><h2>در انتظار پرداخت</h2><strong class="ccd-number">{{ $pendingPaymentCount }}</strong><a class="ccd-link" href="{{ route('client.appointments.index') }}">بررسی نوبت‌های من ←</a></section>
        </div>
        <section class="ccd-card ccd-next" aria-labelledby="ccd-next-title">
            <div class="ccd-section-head"><h2 id="ccd-next-title">نوبت بعدی</h2><span class="ccd-muted">به وقت تهران</span></div>
            @if ($nextAppointment)
                @php
                    $nextDate = $nextAppointment->starts_at->copy()->setTimezone('Asia/Tehran');
                    $psychologistName = trim(($nextAppointment->psychologist?->first_name ?? '') . ' ' . ($nextAppointment->psychologist?->last_name ?? ''));
                    $pending = $nextAppointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT;
                @endphp
                <div class="ccd-next-body">
                    <div><p class="ccd-date">{{ \App\Support\PersianDate::format($nextDate, 'EEEE، d MMMM yyyy') }}</p><p>ساعت <bdi dir="ltr">{{ $nextDate->format('H:i') }}</bdi></p><p class="ccd-muted">مشاور: {{ $psychologistName !== '' ? $psychologistName : 'نام مشاور ثبت نشده' }}</p></div>
                    <div class="ccd-next-actions"><span @class(['ccd-badge', 'ccd-warning' => $pending])>{{ $pending ? 'در انتظار پرداخت' : 'قطعی' }}</span><a class="ccd-button ccd-secondary" href="{{ route('client.appointments.index') }}">مشاهده نوبت‌ها</a></div>
                </div>
            @else
                <div class="ccd-empty"><h3>نوبت آینده‌ای ندارید</h3><p class="ccd-muted">برای انتخاب زمان جلسه بعدی، زمان‌های قابل رزرو را ببینید.</p><a class="ccd-link" href="{{ route('booking.index') }}">مشاهده زمان‌ها و رزرو جلسه ←</a></div>
            @endif
        </section>
        @if ($nextAppointment)
            <section class="ccd-card ccd-preparation" aria-labelledby="ccd-preparation-title">
                <h2 id="ccd-preparation-title">آمادگی برای جلسه پیش رو</h2>
                <p class="ccd-muted">برای جلسه {{ \App\Support\PersianDate::format($nextDate, 'd MMMM yyyy') }}، ساعت <bdi dir="ltr">{{ $nextDate->format('H:i') }}</bdi> به وقت تهران</p>
                <form data-ccd-note data-success-url="{{ route('client.appointments.session.show', $nextAppointment) }}" method="POST" action="{{ route('client.appointments.session.notes.store', $nextAppointment) }}">
                    @csrf
                    <label for="ccd-note-body">دوست دارید در این جلسه درباره چه چیزی صحبت کنید؟</label>
                    <p id="ccd-note-hint" class="ccd-muted">سؤال‌ها، موضوعات مهم یا چیزهایی که نمی‌خواهید فراموش کنید را بنویسید. حداکثر ۱۰٬۰۰۰ نویسه.</p>
                    <textarea id="ccd-note-body" name="body" rows="3" maxlength="10000" required aria-describedby="ccd-note-hint ccd-note-save-hint{{ $errors->has('body') ? ' ccd-note-error' : '' }}" @if($errors->has('body')) aria-invalid="true" @endif>{{ old('body') }}</textarea>
                    @error('body')<p id="ccd-note-error" class="ccd-note-error" role="alert">{{ $message }}</p>@enderror
                    <p id="ccd-note-save-hint" class="ccd-muted">ذخیره خودکار نیست؛ هر بار ذخیره یک یادداشت جدید می‌سازد. بدون JavaScript، پس از ذخیره صفحه جلسه باز می‌شود.</p>
                    <p class="ccd-note-status" role="status" aria-live="polite" tabindex="-1"></p>
                    <div class="ccd-note-actions"><button class="ccd-button" type="submit">ذخیره یادداشت برای این جلسه</button><a class="ccd-link" href="{{ route('client.appointments.session.show', $nextAppointment) }}">مشاهده یادداشت‌های این جلسه</a></div>
                </form>
            </section>
        @endif
        <section class="ccd-treatment" aria-labelledby="ccd-treatment-title">
            <div class="ccd-section-head"><div><h2 id="ccd-treatment-title">مسیر درمان</h2><p class="ccd-muted">مراحل، هدف‌ها و جلسات برگزارشده</p></div></div>
            @forelse ($treatmentPlans as $plan)
                @php
                    $progress = (int) $plan->progress_percent;
                    $statusLabel = match ($plan->status) {
                        'active' => 'فعال', 'completed' => 'تکمیل‌شده',
                        'dropped_out' => 'انصراف مراجع', 'referred' => 'ارجاع‌شده',
                        default => 'وضعیت نامشخص',
                    };
                @endphp
                <article class="ccd-card ccd-plan">
                    <div class="ccd-section-head"><h3>{{ $plan->title }}</h3><span class="ccd-badge">{{ $statusLabel }}</span></div>
                    <div class="ccd-progress-label"><span>پیشرفت مسیر</span><strong>{{ $progress }}٪</strong></div>
                    <progress class="ccd-progress" value="{{ $progress }}" max="100" aria-label="پیشرفت مسیر {{ $plan->title }}">{{ $progress }}٪</progress>
                    <details class="ccd-plan-details">
                        <summary>مشاهده مراحل و جزئیات</summary>
                        <p class="ccd-small ccd-muted">این درصد بر اساس جلسات برگزارشده نسبت به تعداد جلسات تخمینی است؛ شاخص بهبود بالینی نیست. بدون تخمین، صفر نمایش داده می‌شود.</p>
                        <div class="ccd-stages">
                            @foreach ($plan->stages as $stage)
                                @php
                                    $stageCount = (int) $stage->held_sessions_count;
                                    $isCurrent = (int) $plan->current_stage === (int) $stage->stage_number;
                                @endphp
                                <section @class(['ccd-stage', 'ccd-stage-current' => $isCurrent])>
                                    <div class="ccd-section-head"><h4>مرحله {{ $stage->stage_number }}</h4>@if ($isCurrent)<span class="ccd-badge">{{ $plan->isActive() ? 'مرحله فعلی' : 'آخرین مرحله ثبت‌شده' }}</span>@endif</div>
                                    <p class="ccd-stage-title">{{ $stage->title }}</p>
                                    <h5>هدف مرحله</h5>
                                    @if (filled($stage->goal))
                                        <p class="ccd-goal">{{ $stage->goal }}</p>
                                    @else
                                        <p class="ccd-muted">هدف این مرحله هنوز ثبت نشده است.</p>
                                    @endif
                                    <dl><div><dt>جلسات برگزارشده</dt><dd>{{ $stageCount }}</dd></div><div><dt>تعداد جلسات تخمینی</dt><dd>{{ $stage->estimated_sessions ?? 'بدون تخمین' }}</dd></div></dl>
                                </section>
                            @endforeach
                        </div>
                        <p class="ccd-privacy">یادداشت‌های خصوصی مشاور در این بخش نمایش داده نمی‌شوند.</p>
                    </details>
                </article>
            @empty
                <div class="ccd-card ccd-empty"><h3>مسیر درمان شما هنوز فعال نشده است</h3><p class="ccd-muted">برای شروع، یک جلسه ارزیابی رزرو کنید. مسیر درمان پس از تعیین و فعال‌سازی مشاور در این بخش نمایش داده می‌شود.</p><a class="ccd-button" href="{{ route('booking.index') }}">رزرو جلسه ارزیابی</a></div>
            @endforelse
        </section>
        <nav class="ccd-card ccd-quick" aria-label="دسترسی سریع مراجع"><h2>دسترسی سریع</h2><div><a href="{{ route('booking.index') }}">رزرو نوبت <span aria-hidden="true">←</span></a><a href="{{ route('client.appointments.index') }}">نوبت‌های من <span aria-hidden="true">←</span></a><a href="{{ route('client.profile.show') }}">پروفایل <span aria-hidden="true">←</span></a></div></nav>
    </main>
    <script src="{{ asset('website/js/care-client-dashboard.js') }}" defer></script>
</x-layouts.app>
