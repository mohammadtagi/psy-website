<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('website/style/care-clinical-record.css') }}?v=phase5-ux2">
    @endpush
        <script src="{{ asset('website/js/care-record-navigation.js') }}?v=phase5-ux2" defer></script>
    <div class="care-record" dir="rtl">
        @error('treatment_plan')
        <div role="alert" class="mb-6 border-r-4 border-red-600 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror

        @php
            $riskLabels = ['none' => 'بدون خطر گزارش‌شده', 'low' => 'کم', 'medium' => 'متوسط', 'high' => 'زیاد'];
            $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
        @endphp

        @if (session('status'))
            <div role="status" class="mb-6 border-r-4 border-green-600 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <a class="cr-back" href="{{ route('psychologist.clients.index', ['q' => $client->mobile]) }}">بازگشت به فهرست مراجع</a>
        <div class="cr-hero">
            <div>
                <p class="cr-page-label">پرونده بالینی</p>
                <h1>{{ $clientName ?: 'نام ثبت نشده' }}</h1>
                <span class="cr-badge">{{ $record->isActive() ? 'پرونده فعال' : ($record->isClosed() ? 'پرونده بسته' : 'وضعیت نامشخص') }}</span>

                <p class="mt-2 text-sm text-gray-600">
                    <span dir="ltr">{{ $client->mobile }}</span>
                </p>
            </div>

            <a
                href="{{ route('psychologist.clinical-records.edit', $record) }}"
                class="border border-indigo-700 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
            >
                ویرایش پرونده
            </a>
        </div>

        <nav class="cr-nav" aria-label="بخش‌های پرونده">
            <a href="#record-overview">خلاصه بالینی</a>
            <a href="#record-plans">نقشه‌های درمان</a>
            <a href="#client-sessions">جلسات</a>
            <a href="#manual-session">ثبت دستی جلسه</a>
            <a href="#record-notes">یادداشت‌های من</a>
            <a href="#record-attachments">ضمائم</a>
        </nav>
        <div class="cr-grid">
            <section id="record-overview" class="cr-panel cr-wide">
                <h2 class="text-lg font-semibold text-gray-900">مسئله اصلی</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->presenting_problem ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section class="cr-panel">
                <h2 class="text-lg font-semibold text-gray-900">سطح خطر</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودکشی</dt>
                        <dd class="cr-risk" data-risk="{{ $record->suicide_risk }}">{{ $riskLabels[$record->suicide_risk] ?? 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودآسیب‌رسانی</dt>
                        <dd class="cr-risk" data-risk="{{ $record->self_harm_risk }}">{{ $riskLabels[$record->self_harm_risk] ?? 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">آسیب به دیگران</dt>
                        <dd class="cr-risk" data-risk="{{ $record->harm_to_others_risk }}">{{ $riskLabels[$record->harm_to_others_risk] ?? 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خطر کلی</dt>
                        <dd class="cr-risk" data-risk="{{ $record->overall_risk_level }}">{{ $riskLabels[$record->overall_risk_level] ?? 'ثبت نشده' }}</dd>
                    </div>
                </dl>
                @if ($record->safety_actions)
                    <div class="cr-safety">
                        <h3>اقدامات ایمنی ثبت‌شده</h3>
                        <p class="cr-text">{{ $record->safety_actions }}</p>
                    </div>
                @endif
            </section>

            <section class="cr-panel">
                <h2 class="text-lg font-semibold text-gray-900">برنامه درمان</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->treatment_plan_and_schedule ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section id="record-plans" class="cr-panel cr-wide">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            نقشه‌های درمان
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            مدیریت مراحل، اهداف و پیشرفت درمان
                        </p>
                    </div>

                    @if (!$record->treatmentPlans->contains(fn ($plan) => $plan->isActive()))
                        <a
                            href="{{ route('psychologist.treatment-plans.create', $record) }}"
                            class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white"
                        >
                            ایجاد نقشه درمان
                        </a>
                    @endif
                </div>

                @forelse ($record->treatmentPlans as $plan)
                    @php
                        $stageIds = $plan->stages->modelKeys();

                        $heldSessions = \App\Models\Appointment::query()
                            ->where('psychologist_id', auth()->id())
                            ->where('client_id', $client->id)
                            ->where('treatment_plan_id', $plan->id)
                            ->whereIn('treatment_plan_stage_id', $stageIds)
                            ->where('therapy_session_type', 'plan')
                            ->where('status', \App\Models\Appointment::STATUS_CONFIRMED)
                            ->where('attendance_status', 'held')
                            ->get(['id', 'treatment_plan_stage_id']);

                        $totalEstimated = (int) $plan->stages->sum('estimated_sessions');
                        $completedSessions = $heldSessions->count();

                        $progress = $totalEstimated > 0
                            ? min(100, (int) round(($completedSessions / $totalEstimated) * 100))
                            : 0;
                    @endphp


                    <article class="cr-plan">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ $plan->title }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-600">
                                    وضعیت:
                                    @switch($plan->status)
                                        @case('active') فعال @break
                                        @case('completed') تکمیل‌شده @break
                                        @case('dropped_out') انصراف مراجع @break
                                        @case('referred') ارجاع‌شده @break
                                    @endswitch
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <a
                                    href="{{ route('psychologist.treatment-plans.edit', $plan) }}"
                                    class="text-sm text-indigo-700 hover:underline"
                                >
                                    {{ $plan->isActive() ? 'ویرایش' : 'مشاهده' }}
                                </a>

                                @if ($plan->isActive())
                                    <form
                                        method="POST"
                                        action="{{ route('psychologist.treatment-plans.destroy', $plan) }}"
                                        onsubmit="return confirm('مسیر درمان و مراحل آن برای همیشه حذف شوند؟ جلسات، پرداخت‌ها و یادداشت‌ها باقی می‌مانند، اما ارتباط آن‌ها با این مسیر پاک می‌شود.')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-sm text-red-700 hover:underline">
                                            حذف مسیر
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>پیشرفت مسیر</span>
                                <span>{{ $progress }}٪</span>
                            </div>

                            <div class="cr-progress" role="progressbar" aria-label="پیشرفت مسیر {{ $plan->title }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}">
                                <div
                                    class="cr-progress-fill"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                {{ $completedSessions }} جلسه برگزارشده از {{ $totalEstimated ?: 'بدون تخمین' }}
                            </p>
                        </div>

                        <div class="cr-stages">
                            @foreach ($plan->stages as $stage)
                                @php
                                    $stageCount = $heldSessions
                                        ->where('treatment_plan_stage_id', $stage->id)
                                        ->count();
                                @endphp


                                <div class="cr-stage">
                                    <p class="text-sm font-medium text-gray-900">
                                        مرحله {{ $stage->stage_number }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $stage->title }}</p>
                                    <p class="mt-2 text-xs text-gray-500">
                                        جلسات: {{ $stageCount }}
                                        /
                                        {{ $stage->estimated_sessions ?? 'بدون تخمین' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <p class="mt-5 text-sm text-gray-500">
                        هنوز نقشه درمانی برای این پرونده ایجاد نشده است.
                    </p>
                @endforelse
            </section>

            @include('psychologist.clinical-records.partials.appointments', [
    'recordAppointments' => $recordAppointments,
])


            @include('psychologist.clinical-records.partials.manual-appointment')


            <section id="record-notes" class="cr-panel cr-wide">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            یادداشت‌های محرمانه من
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            فقط یادداشت‌های خودتان نمایش داده می‌شوند.
                            تعداد: {{ $record->notes->count() }}
                        </p>
                    </div>

                    <a
                        href="{{ route('psychologist.clinical-notes.create', [
                'clinicalRecord' => $record,
            ]) }}"
                        class="bg-indigo-700 px-4 py-2 text-sm text-white"
                    >
                        ثبت یادداشت محرمانه
                    </a>
                </div>

                <div class="mt-5 space-y-4">
                @forelse ($record->notes as $note)
                        <article class="cr-note">
                            <div class="space-y-1 text-xs text-gray-500">
                                <p>
                                    ثبت:
                                    {{ \App\Support\PersianDate::format(
                                        $note->created_at->copy()->setTimezone('Asia/Tehran'),
                                        'yyyy/MM/dd HH:mm'
                                    ) }}
                                </p>

                                <p>
                                    آخرین ویرایش:
                                    {{ \App\Support\PersianDate::format(
                                        $note->updated_at->copy()->setTimezone('Asia/Tehran'),
                                        'yyyy/MM/dd HH:mm'
                                    ) }}
                                </p>
                            </div>

                            <p class="whitespace-pre-wrap break-words text-sm leading-7 text-gray-700">{{ $note->private_note }}</p>

                            <div class="flex flex-wrap gap-4">
                                <a
                                    href="{{ route('psychologist.clinical-notes.edit', [
                            'clinicalNote' => $note,
                        ]) }}"
                                    class="text-sm text-indigo-700"
                                >
                                    ویرایش
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('psychologist.clinical-notes.destroy', [
                            'clinicalNote' => $note,
                        ]) }}"
                                    onsubmit="return confirm('این یادداشت محرمانه حذف شود؟')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-sm text-red-700">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-gray-500">
                            هنوز یادداشت محرمانه‌ای ثبت نکرده‌اید.
                        </p>
                    @endforelse
                </div>
            </section>



            <section id="record-attachments" class="cr-panel cr-wide">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            مدارک و ضمائم
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            تعداد ضمائم:
                            {{ $record->attachments->count() }}
                        </p>
                    </div>

                    <a
                        href="{{ route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ]) }}"
                        class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                    >
                        مدیریت ضمائم
                    </a>
                </div>

                @if ($record->attachments->isEmpty())
                    <p class="mt-5 text-sm text-gray-500">
                        هنوز ضمیمه‌ای برای این پرونده ثبت نشده است.
                    </p>
                @else
                    <div class="mt-5 space-y-3">
                        @foreach ($record->attachments->take(3) as $attachment)
                            <div class="flex flex-wrap items-center justify-between gap-3 border border-gray-200 p-3">
                                <div class="min-w-0">
                                    <p class="break-all text-sm font-medium text-gray-900">
                                        {{ $attachment->original_name }}
                                    </p>

                                    @if ($attachment->document_date)
                                        <p class="mt-1 text-xs text-gray-500">
                                            تاریخ مدرک:
                                            {{ \App\Support\PersianDate::format($attachment->document_date, 'yyyy/MM/dd') }}
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('psychologist.clinical-attachments.download', [
                            'clinicalAttachment' => $attachment,
                        ]) }}"
                                    class="text-sm text-indigo-700 hover:underline"
                                >
                                    دریافت فایل
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($record->attachments->count() > 3)
                        <p class="mt-4 text-sm text-gray-500">
                            {{ $record->attachments->count() - 3 }}
                            ضمیمهٔ دیگر نیز وجود دارد.
                        </p>
                    @endif
                @endif
            </section>

        </div>
    </div>
</x-layouts.app>
