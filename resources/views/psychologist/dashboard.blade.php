<x-layouts.app>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">داشبورد روان‌شناس</h1>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <section class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="text-sm text-gray-600">نوبت‌های امروز</h2>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $todayAppointments->count() }}</p>
            </section>
            <section class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="text-sm text-gray-600">نوبت‌های آینده</h2>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $upcomingAppointments->count() }}</p>
            </section>
            <section class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="text-sm text-gray-600">بازه‌های حضور آینده</h2>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $upcomingAvailabilities->count() }}</p>
            </section>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="border-t border-gray-200 pt-5">
                <h2 class="font-semibold text-gray-900">نوبت‌های امروز</h2>

                <ul class="mt-3 divide-y divide-gray-100">
                    @forelse ($todayAppointments as $appointment)
                        <li class="flex flex-wrap justify-between gap-2 py-3 text-sm">
                <span>
                    {{ $appointment->starts_at->copy()->setTimezone('Asia/Tehran')->format('Y/m/d H:i') }}
                </span>
                            <span>
                    {{ trim(($appointment->client?->first_name ?? '') . ' ' . ($appointment->client?->last_name ?? '')) ?: $appointment->client?->mobile }}
                </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">نوبتی برای امروز ثبت نشده است.</li>
                    @endforelse
                </ul>
            </section>

            <section class="border-t border-gray-200 pt-5">
                <h2 class="font-semibold text-gray-900">نوبت‌های آینده</h2>
                <ul class="mt-3 divide-y divide-gray-100">
                    @forelse ($upcomingAppointments as $appointment)
                        <li class="flex flex-wrap justify-between gap-2 py-3 text-sm">
                            <span>{{ $appointment->starts_at->copy()->setTimezone('Asia/Tehran')->format('Y/m/d H:i') }}</span>
                            <span>{{ trim(($appointment->client?->first_name ?? '') . ' ' . ($appointment->client?->last_name ?? '')) ?: $appointment->client?->mobile }}</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">نوبت آینده‌ای ثبت نشده است.</li>
                    @endforelse
                </ul>
            </section>

            <section class="border-t border-gray-200 pt-5">
                <h2 class="font-semibold text-gray-900">بازه‌های حضور آینده</h2>
                <ul class="mt-3 divide-y divide-gray-100">
                    @forelse ($upcomingAvailabilities as $availability)
                        <li class="py-3 text-sm">
                            {{ $availability->starts_at->copy()->setTimezone('Asia/Tehran')->format('Y/m/d H:i') }}
                            تا
                            {{ $availability->ends_at->copy()->setTimezone('Asia/Tehran')->format('H:i') }}
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">بازهٔ حضور آینده‌ای ثبت نشده است.</li>
                    @endforelse
                </ul>
            </section>

            <nav aria-label="میان‌برهای روان‌شناس" class="border-t border-gray-200 pt-5">
                <h2 class="font-semibold text-gray-900">دسترسی سریع</h2>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-3 text-sm">
                    <a class="text-indigo-700 hover:underline" href="{{ route('psychologist.availabilities.index') }}">مدیریت
                        بازه‌های حضور</a>
                    <a class="text-indigo-700 hover:underline" href="{{ route('psychologist.appointments.index') }}">مشاهدهٔ
                        نوبت‌ها</a>
                    <a class="text-indigo-700 hover:underline" href="{{ route('psychologist.clients.index') }}">مراجعان
                        و پرونده‌ها</a>
                </div>
            </nav>
        </div>
    </div>
</x-layouts.app>
