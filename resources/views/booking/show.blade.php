@extends('components.layouts.app')

@section('title', 'انتخاب زمان جلسه')

@section('content')
    @php
        $dateObject = \Carbon\CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $date,
            'Asia/Tehran'
        );
    @endphp

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <a
                href="{{ route('booking.index', ['duration' => $durationMinutes]) }}"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
            >
                بازگشت به انتخاب تاریخ
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                انتخاب زمان جلسه
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                {{ $dateObject->locale('fa')->dayName }}
                {{ $dateObject->format('Y/m/d') }}
                -
                جلسه {{ $durationMinutes }} دقیقه‌ای
            </p>
        </header>

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

        @if (! $isAuthenticated)
            <section class="mb-8 rounded-lg border border-amber-200 bg-amber-50 p-5">
                <h2 class="font-semibold text-amber-900">
                    برای ثبت نوبت ابتدا وارد شوید
                </h2>

                <p class="mt-2 text-sm text-amber-800">
                    پس از ورود، زمان انتخاب‌شده را می‌توانید ثبت کنید.
                </p>

                <a
                    href="{{ route('auth.login') }}"
                    class="mt-4 inline-flex min-h-11 items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    ورود به حساب
                </a>
            </section>
        @endif

        <section class="mb-8">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    زمان‌های آزاد
                </h2>

                <span class="text-sm text-gray-500">
                    {{ $durationMinutes }} دقیقه
                </span>
            </div>

            @if (empty($slots))
                <div class="rounded-lg border border-gray-200 bg-white px-6 py-10 text-center shadow-sm">
                    <p class="text-sm text-gray-600">
                        برای این تاریخ و مدت جلسه، زمان آزادی وجود ندارد.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($slots as $index => $slot)
                        <button
                            type="button"
                            class="slot-button min-h-12 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 transition hover:border-indigo-500 hover:bg-indigo-50"
                            data-slot-index="{{ $index }}"
                            data-start-time="{{ $slot['start_time'] }}"
                        >
                            {{ $slot['start_time'] }}
                            <span class="mt-1 block text-xs font-normal text-gray-500">
                                تا {{ $slot['end_time'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($isAuthenticated && ! empty($slots))
            <section
                id="booking-form-container"
                class="hidden border-t border-gray-200 pt-8"
            >
                <h2 class="mb-5 text-lg font-semibold text-gray-900">
                    اطلاعات مراجع
                </h2>

                <form
                    method="POST"
                    action="{{ route('booking.store', ['date' => $date]) }}"
                    class="space-y-6"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="availability_id"
                        id="availability_id"
                    >

                    <input
                        type="hidden"
                        name="start_time"
                        id="start_time"
                    >

                    <input
                        type="hidden"
                        name="duration_minutes"
                        value="{{ $durationMinutes }}"
                    >

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label
                                for="first_name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                نام
                            </label>

                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name', auth()->user()->first_name) }}"
                                required
                                maxlength="100"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label
                                for="last_name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                نام خانوادگی
                            </label>

                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name', auth()->user()->last_name) }}"
                                required
                                maxlength="100"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="birth_date"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            تاریخ تولد
                        </label>

                        <input
                            id="birth_date"
                            name="birth_date"
                            type="date"
                            value="{{ old('birth_date', optional(auth()->user()->birth_date)->format('Y-m-d')) }}"
                            required
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <fieldset>
                        <legend class="mb-3 text-sm font-medium text-gray-700">
                            شیوه برگزاری
                        </legend>

                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($sessionTypes as $sessionType)
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-4 hover:border-indigo-500">
                                    <input
                                        type="radio"
                                        name="session_type"
                                        value="{{ $sessionType['value'] }}"
                                        @checked(old('session_type', 'online') === $sessionType['value'])
                                        required
                                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    >

                                    <span class="text-sm font-medium text-gray-800">
                                        {{ $sessionType['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                        <div class="flex items-center justify-between">
                            <span>زمان انتخاب‌شده</span>
                            <strong id="selected-time">انتخاب نشده</strong>
                        </div>

                        <div class="mt-2 flex items-center justify-between">
                            <span>مبلغ جلسه</span>
                            <strong>
                                {{ $durationMinutes === 45 ? '۷۶۰٬۰۰۰' : '۹۵۰٬۰۰۰' }}
                                تومان
                            </strong>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        ثبت نهایی نوبت
                    </button>
                </form>
            </section>
        @endif
    </main>

    @if ($isAuthenticated && ! empty($slots))
        <script>
            (() => {
                const slots = @json($slots);
                const buttons = document.querySelectorAll('.slot-button');
                const formContainer = document.getElementById(
                    'booking-form-container'
                );
                const availabilityInput = document.getElementById(
                    'availability_id'
                );
                const startTimeInput = document.getElementById('start_time');
                const selectedTime = document.getElementById('selected-time');

                buttons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.dataset.slotIndex);
                        const slot = slots[index];

                        buttons.forEach((item) => {
                            item.classList.remove(
                                'border-indigo-600',
                                'bg-indigo-50',
                                'text-indigo-700'
                            );

                            item.classList.add(
                                'border-gray-300',
                                'bg-white',
                                'text-gray-800'
                            );
                        });

                        button.classList.remove(
                            'border-gray-300',
                            'bg-white',
                            'text-gray-800'
                        );

                        button.classList.add(
                            'border-indigo-600',
                            'bg-indigo-50',
                            'text-indigo-700'
                        );

                        availabilityInput.value = slot.availability_id;
                        startTimeInput.value = slot.start_time;
                        selectedTime.textContent =
                            `${slot.start_time} تا ${slot.end_time}`;

                        formContainer.classList.remove('hidden');
                        formContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                        });
                    });
                });
            })();
        </script>
    @endif
@endsection
