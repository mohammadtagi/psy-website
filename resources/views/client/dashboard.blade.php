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
