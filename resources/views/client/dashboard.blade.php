<x-layouts.app>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900">داشبورد مراجع</h1>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <section class="border-t border-gray-200 py-4">
                                <h2 class="text-sm text-gray-600">نوبت‌های قطعی آینده</h2>
                                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $activeAppointmentsCount }}</p>
                            </section>
                        <section class="border-t border-gray-200 py-4">
                                <h2 class="text-sm text-gray-600">در انتظار پرداخت</h2>
                                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $pendingPaymentCount }}</p>
                            </section>
                    </div>

                <section class="mt-6 border-t border-gray-200 py-5">
                        <h2 class="font-semibold text-gray-900">نوبت بعدی</h2>
                        @if ($nextAppointment)
                                <p class="mt-3 text-sm text-gray-700">
                                        {{ $nextAppointment->starts_at->copy()->setTimezone('Asia/Tehran')->format('Y/m/d H:i') }}
                                        <span class="mx-1">|</span>
                                        {{ trim(($nextAppointment->psychologist?->first_name ?? '') . ' ' . ($nextAppointment->psychologist?->last_name ?? '')) }}
                                        <span class="mx-1">|</span>
                                        {{ $nextAppointment->status === \App\Models\Appointment::STATUS_PENDING_PAYMENT ? 'در انتظار پرداخت' : 'قطعی' }}
                                    </p>
                            @else
                                <p class="mt-3 text-sm text-gray-600">نوبت آینده‌ای ندارید.</p>
                            @endif
                    </section>

            @if ($treatmentPlans->isNotEmpty())
                <section class="mt-6 border-t border-gray-200 py-5">
                    <h2 class="font-semibold text-gray-900">مسیر درمان</h2>

                    @foreach ($treatmentPlans as $plan)
                        @php
                            $progress = (int) $plan->progress_percent;
                        @endphp


                        <article class="mt-4 border border-gray-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $plan->title }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-600">
                                        @switch($plan->status)
                                            @case('active') فعال @break
                                            @case('completed') تکمیل‌شده @break
                                            @case('dropped_out') انصراف مراجع @break
                                            @case('referred') ارجاع‌شده @break
                                        @endswitch
                                    </p>
                                </div>

                                <span class="text-sm text-gray-600">
                        {{ $progress }}٪ پیشرفت
                    </span>
                            </div>

                            <div class="mt-3 h-2 bg-gray-200">
                                <div
                                    class="h-2 bg-indigo-700"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                @foreach ($plan->stages as $stage)
                                    @php
                                        $stageCount = (int) $stage->held_sessions_count;
                                    @endphp


                                    <div
                                        @class([
                                            'rounded-lg border p-4',
                                            'border-indigo-300 bg-indigo-50' =>
                                                (int) $plan->current_stage === (int) $stage->stage_number,
                                            'border-gray-200' =>
                                                (int) $plan->current_stage !== (int) $stage->stage_number,
                                        ])
                                    >
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <h4 class="text-sm font-medium text-gray-900">
                                                مرحله {{ $stage->stage_number }}
                                            </h4>

                                            @if ((int) $plan->current_stage === (int) $stage->stage_number)
                                                <span class="rounded bg-indigo-100 px-2 py-1 text-xs text-indigo-800">
                                                    {{ $plan->isActive() ? 'مرحله فعلی' : 'آخرین مرحله ثبت‌شده' }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-sm font-medium text-gray-700">
                                            {{ $stage->title }}
                                        </p>

                                        <h5 class="mt-4 text-xs font-semibold text-gray-700">
                                            هدف مرحله
                                        </h5>

                                        @if (filled($stage->goal))
                                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-7 text-gray-600">{{ $stage->goal }}</p>
                                        @else
                                            <p class="mt-2 text-sm text-gray-500">
                                                هدف این مرحله هنوز ثبت نشده است.
                                            </p>
                                        @endif

                                        <dl class="mt-4 space-y-2 text-xs text-gray-600">
                                            <div class="flex justify-between gap-3">
                                                <dt>جلسات برگزارشده</dt>
                                                <dd>{{ $stageCount }}</dd>
                                            </div>

                                            <div class="flex justify-between gap-3">
                                                <dt>تعداد جلسات تخمینی</dt>
                                                <dd>{{ $stage->estimated_sessions ?? 'بدون تخمین' }}</dd>
                                            </div>
                                        </dl>
                                    </div>

                                @endforeach
                            </div>

                            <p class="mt-4 text-xs text-gray-500">
                                یادداشت‌های خصوصی مشاور در این بخش نمایش داده نمی‌شوند.
                            </p>
                        </article>
                    @endforeach
                </section>
            @else
                <section class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="font-semibold text-gray-900">
                        مسیر درمان
                    </h2>

                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        هنوز مسیر درمانی برای شما فعال نشده است.
                        برای شروع، یک جلسه ارزیابی رزرو کنید.
                        مسیر درمان پس از تعیین و فعال‌سازی مشاور در این بخش نمایش داده می‌شود.
                    </p>

                    <a
                        href="{{ route('booking.index') }}"
                        class="mt-4 inline-flex min-h-11 items-center justify-center rounded-lg bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                    >
                        رزرو جلسه ارزیابی
                    </a>
                </section>

            @endif


            <nav aria-label="دسترسی سریع مراجع" class="mt-2 border-t border-gray-200 py-5">
                        <h2 class="font-semibold text-gray-900">دسترسی سریع</h2>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-3 text-sm">
                                <a class="text-indigo-700 hover:underline" href="{{ route('booking.index') }}">رزرو نوبت</a>
                                <a class="text-indigo-700 hover:underline" href="{{ route('client.appointments.index') }}">نوبت‌های من</a>
                                <a class="text-indigo-700 hover:underline" href="{{ route('client.profile.show') }}">پروفایل</a>
                            </div>
                    </nav>
            </main>
    </x-layouts.app>
