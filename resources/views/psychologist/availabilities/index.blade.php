@extends('components.layouts.app')

@section('title', 'مدیریت بازه‌های حضور')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                مدیریت بازه‌های حضور
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                زمان‌هایی را که برای برگزاری جلسات در دسترس هستید ثبت کنید.
            </p>
        </div>

        @if (session('status'))
            <div
                class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                role="status"
            >
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                role="alert"
            >
                <ul class="list-disc space-y-1 pr-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    ایجاد بازه حضور
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    تاریخ‌ها و ساعت‌ها بر اساس منطقه زمانی تهران ثبت می‌شوند.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('psychologist.availabilities.store') }}"
                id="availability-form"
                class="space-y-6"
            >
                @csrf

                <div>
                    <span class="mb-3 block text-sm font-medium text-gray-700">
                        نوع ایجاد بازه
                    </span>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-300 p-4 transition hover:border-indigo-500"
                        >
                            <input
                                type="radio"
                                name="mode"
                                value="single"
                                class="mt-1"
                                {{ old('mode', 'single') === 'single' ? 'checked' : '' }}
                            >

                            <span>
                                <span class="block font-medium text-gray-900">
                                    یک روز
                                </span>

                                <span class="mt-1 block text-sm text-gray-500">
                                    ایجاد یک بازه برای یک تاریخ مشخص
                                </span>
                            </span>
                        </label>

                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-300 p-4 transition hover:border-indigo-500"
                        >
                            <input
                                type="radio"
                                name="mode"
                                value="recurring"
                                class="mt-1"
                                {{ old('mode') === 'recurring' ? 'checked' : '' }}
                            >

                            <span>
                                <span class="block font-medium text-gray-900">
                                    تکرارشونده
                                </span>

                                <span class="mt-1 block text-sm text-gray-500">
                                    ایجاد بازه در چند تاریخ و روز مشخص هفته
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            for="from_date"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            تاریخ شروع
                        </label>

                        <input
                            id="from_date"
                            name="from_date"
                            type="text"
                            inputmode="numeric"
                            dir="ltr"
                            value="{{ old('from_date') }}"
                            placeholder="۱۴۰۵/۰۷/۰۱"
                            maxlength="10"
                            pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}"
                            required
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div id="to-date-field">
                        <label
                            for="to_date"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            تاریخ پایان
                        </label>

                        <input
                            id="to_date"
                            name="to_date"
                            type="text"
                            inputmode="numeric"
                            dir="ltr"
                            value="{{ old('to_date') }}"
                            placeholder="۱۴۰۵/۰۷/۳۰"
                            maxlength="10"
                            pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div
                    id="weekdays-field"
                    class="hidden"
                >
                    <span class="mb-3 block text-sm font-medium text-gray-700">
                        روزهای هفته
                    </span>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
                        @php
                            $weekdays = [
                                0 => 'یکشنبه',
                                1 => 'دوشنبه',
                                2 => 'سه‌شنبه',
                                3 => 'چهارشنبه',
                                4 => 'پنجشنبه',
                                5 => 'جمعه',
                                6 => 'شنبه',
                            ];
                        @endphp

                        @foreach ($weekdays as $value => $label)
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm hover:border-indigo-400"
                            >
                                <input
                                    type="checkbox"
                                    name="weekdays[]"
                                    value="{{ $value }}"
                                    @checked(in_array($value, old('weekdays', []), true))
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >

                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            for="start_time"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            ساعت شروع
                        </label>

                        <input
                            id="start_time"
                            name="start_time"
                            type="time"
                            value="{{ old('start_time') }}"
                            required
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label
                            for="end_time"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            ساعت پایان
                        </label>

                        <input
                            id="end_time"
                            name="end_time"
                            type="time"
                            value="{{ old('end_time') }}"
                            required
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        ایجاد بازه حضور
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    بازه‌های آینده
                </h2>
            </div>

            @if ($availabilities->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-500">
                        هنوز بازه حضوری برای آینده ثبت نشده است.
                    </p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach ($availabilities as $availability)
                        @php
                            $startsAt = $availability->starts_at
                                ->setTimezone($timezone);

                            $endsAt = $availability->ends_at
                                ->setTimezone($timezone);

                            $hasActiveAppointments =
                                $availability->active_appointments_count > 0;
                        @endphp

                        <div class="flex flex-col gap-4 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-medium text-gray-900">
                                        {{ \App\Support\PersianDate::format($startsAt) }}
                                    </h3>

                                    <span class="text-sm text-gray-500">
                                        {{ \App\Support\PersianDate::format($startsAt, 'EEEE') }}
                                    </span>

                                    <span class="text-gray-400">|</span>

                                    <span class="text-sm text-gray-700">
                                        {{ $startsAt->format('H:i') }}
                                        تا
                                        {{ $endsAt->format('H:i') }}
                                    </span>

                                    @if ($availability->status === \App\Models\Availability::STATUS_ACTIVE)
                                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                            فعال
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                            غیرفعال
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-sm text-gray-500">
                                    نوبت‌های فعال:
                                    {{ $availability->active_appointments_count }}
                                </p>
                            </div>

                            @if (
                                $availability->status === \App\Models\Availability::STATUS_ACTIVE
                                && ! $hasActiveAppointments
                            )
                                <form
                                    method="POST"
                                    action="{{ route('psychologist.availabilities.destroy', $availability) }}"
                                    onsubmit="return confirm('آیا از غیرفعال‌کردن این بازه مطمئن هستید؟')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    >
                                        غیرفعال‌کردن
                                    </button>
                                </form>
                            @elseif ($hasActiveAppointments)
                                <span class="text-sm text-gray-500">
                                    به دلیل وجود نوبت فعال قابل غیرفعال‌سازی نیست.
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $availabilities->links() }}
                </div>
            @endif
        </section>
    </div>

    <script>
        (() => {
            const form = document.getElementById('availability-form');
            const modeInputs = form.querySelectorAll('input[name="mode"]');
            const fromDateInput = document.getElementById('from_date');
            const toDateField = document.getElementById('to-date-field');
            const toDateInput = document.getElementById('to_date');
            const weekdaysField = document.getElementById('weekdays-field');
            const weekdayInputs = weekdaysField.querySelectorAll(
                'input[name="weekdays[]"]'
            );

            const updateMode = () => {
                const mode = form.querySelector(
                    'input[name="mode"]:checked'
                )?.value;

                const recurring = mode === 'recurring';

                toDateField.classList.toggle('hidden', !recurring);
                weekdaysField.classList.toggle('hidden', !recurring);

                toDateInput.required = recurring;

                if (!recurring) {
                    toDateInput.value = fromDateInput.value;
                }

                weekdayInputs.forEach((input) => {
                    input.required = false;
                });
            };

            fromDateInput.addEventListener('input', () => {
                const mode = form.querySelector(
                    'input[name="mode"]:checked'
                )?.value;

                if (mode !== 'recurring') {
                    toDateInput.value = fromDateInput.value;
                }
            });

            modeInputs.forEach((input) => {
                input.addEventListener('change', updateMode);
            });

            updateMode();
        })();
    </script>
@endsection
