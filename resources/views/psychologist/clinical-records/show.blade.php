<x-layouts.app>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @error('treatment_plan')
        <div role="alert" class="mb-6 border-r-4 border-red-600 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
        @enderror

        @php
            $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
        @endphp

        @if (session('status'))
            <div class="mb-6 border-r-4 border-green-600 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <a
                    href="{{ route('psychologist.clients.index', ['q' => $client->mobile]) }}"
                    class="text-sm text-indigo-700 hover:underline"
                >
                    بازگشت به فهرست مراجع
                </a>

                <h1 class="mt-4 text-2xl font-bold text-gray-900">
                    پرونده بالینی
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    {{ $clientName ?: 'نام ثبت نشده' }}
                    -
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

        <div class="grid gap-5 md:grid-cols-2">
            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900">مسئله اصلی</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->presenting_problem ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section class="border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-gray-900">سطح خطر</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودکشی</dt>
                        <dd>{{ $record->suicide_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودآسیب‌رسانی</dt>
                        <dd>{{ $record->self_harm_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">آسیب به دیگران</dt>
                        <dd>{{ $record->harm_to_others_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خطر کلی</dt>
                        <dd>{{ $record->overall_risk_level ?: 'ثبت نشده' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-gray-900">برنامه درمان</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->treatment_plan_and_schedule ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
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


                    <article class="mt-5 border border-gray-200 p-4">
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

                            <div class="mt-2 h-2 bg-gray-200">
                                <div
                                    class="h-2 bg-indigo-700"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                {{ $completedSessions }} جلسه تکمیل‌شده از {{ $totalEstimated ?: 'بدون تخمین' }}
                            </p>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-3">
                            @foreach ($plan->stages as $stage)
                                @php
                                    $stageCount = $heldSessions
                                        ->where('treatment_plan_stage_id', $stage->id)
                                        ->count();
                                @endphp


                                <div class="border border-gray-200 p-3">
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


            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
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
                        <article class="space-y-4 border border-gray-200 p-4">
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



            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
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
