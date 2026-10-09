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
                            $completedSessions = $plan->clinicalNotes->count();
                            $totalEstimated = (int) $plan->stages->sum('estimated_sessions');
                            $progress = $totalEstimated > 0
                                ? min(100, (int) round(($completedSessions / $totalEstimated) * 100))
                                : 0;
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
                                        $stageCount = $plan->clinicalNotes
                                            ->where('treatment_plan_stage_id', $stage->id)
                                            ->count();
                                    @endphp

                                    <div class="border border-gray-200 p-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            مرحله {{ $stage->stage_number }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $stage->title }}
                                        </p>

                                        <p class="mt-2 text-xs text-gray-500">
                                            جلسات تکمیل‌شده:
                                            {{ $stageCount }}
                                            /
                                            {{ $stage->estimated_sessions ?? 'بدون تخمین' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-4 text-xs text-gray-500">
                                یادداشت‌های خصوصی مشاور در این بخش نمایش داده نمی‌شوند.
                            </p>
                        </article>
                    @endforeach
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
