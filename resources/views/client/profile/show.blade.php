@extends('components.layouts.app')

@section('title', 'پروفایل من')

@section('content')
        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <header class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900">پروفایل من</h1>
                    </header>

                <section class="border border-gray-200 bg-white p-5">
                        <h2 class="text-lg font-semibold text-gray-900">اطلاعات فردی</h2>

                        <dl class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                        <dt class="text-xs text-gray-500">نام و نام خانوادگی</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                                {{ trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->name ?: 'ثبت نشده') }}
                                            </dd>
                                    </div>

                                <div>
                                        <dt class="text-xs text-gray-500">شماره موبایل</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                                {{ $client->mobile ?: 'ثبت نشده' }}
                                            </dd>
                                    </div>

                            <div>
                                <dt class="text-gray-500">تاریخ تولد</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ $client->birth_date
                                        ? \App\Support\PersianDate::format(\Illuminate\Support\Carbon::parse($client->birth_date))
                                        : 'ثبت نشده' }}
                                </dd>
                            </div>

                            </dl>
                    </section>
            @if (session('success'))
                <div class="mt-5 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="mt-10 border border-gray-200 bg-white p-5">
                <details
                    @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('birth_date'))
                        open
                    @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
            <span class="text-lg font-semibold text-gray-900">
                ویرایش اطلاعات فردی
            </span>

                        <span class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                ویرایش اطلاعات
            </span>
                    </summary>

                    <form
                        method="POST"
                        action="{{ route('client.profile.update') }}"
                        class="mt-5 space-y-5"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label
                                    for="first_name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    نام
                                </label>

                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    value="{{ old('first_name', $client->first_name) }}"
                                    maxlength="100"
                                    required
                                    autocomplete="given-name"
                                    class="mt-1 block w-full border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label
                                    for="last_name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    نام خانوادگی
                                </label>

                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    value="{{ old('last_name', $client->last_name) }}"
                                    maxlength="100"
                                    required
                                    autocomplete="family-name"
                                    class="mt-1 block w-full border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label
                                    for="birth_date"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    تاریخ تولد
                                </label>

                                <input
                                    id="birth_date"
                                    name="birth_date"
                                    type="text"
                                    dir="ltr"
                                    inputmode="numeric"
                                    placeholder="۱۴۰۰/۰۵/۱۲"
                                    value="{{ old(
                            'birth_date',
                            $client->birth_date
                                ? \App\Support\PersianDate::format($client->birth_date, 'yyyy/MM/dd')
                                : ''
                        ) }}"
                                    maxlength="10"
                                    pattern="[۰-۹0-9]{4}/[۰-۹0-9]{1,2}/[۰-۹0-9]{1,2}"
                                    required
                                    autocomplete="bday"
                                    class="mt-1 block w-full border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    تاریخ را به‌صورت شمسی وارد کنید؛ برای نمونه: ۱۴۰۰/۰۵/۱۲
                                </p>
                            </div>

                            <div>
                    <span class="block text-sm font-medium text-gray-700">
                        شماره موبایل
                    </span>

                                <p class="mt-1 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                                    {{ $client->mobile ?: 'ثبت نشده' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    شماره موبایل قابل ویرایش نیست.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-start">
                            <button
                                type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </details>
            </section>

                <section class="mt-10">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <h2 class="text-lg font-semibold text-gray-900">سوابق جلسات تکمیل‌شده</h2>
                                <a
                                        href="{{ route('client.appointments.index') }}"
                                        class="text-sm font-medium text-indigo-700 hover:text-indigo-900"
                                    >
                                        مشاهده همه نوبت‌ها
                                    </a>
                            </div>

                        @if ($completedAppointments->isEmpty())
                                <p class="border border-gray-200 bg-white px-5 py-8 text-center text-sm text-gray-600">
                                        هنوز جلسه تکمیل‌شده‌ای ثبت نشده است.
                                    </p>
                            @else
                                <div class="divide-y divide-gray-200 border border-gray-200 bg-white">
                                        @foreach ($completedAppointments as $appointment)
                                                @php
                                                        $startsAt = $appointment->starts_at->copy()->setTimezone('Asia/Tehran');
                                                        $latestPayment = $appointment->payments->first();

                                                        $paymentStatusLabel = $latestPayment
                                                            ? match ($latestPayment->status) {
                                                                \App\Models\Payment::STATUS_INITIATED => 'شروع نشده',
                                                                \App\Models\Payment::STATUS_PENDING => 'در حال پرداخت',
                                                                \App\Models\Payment::STATUS_PAID => 'پرداخت موفق',
                                                                \App\Models\Payment::STATUS_FAILED => 'ناموفق',
                                                                \App\Models\Payment::STATUS_CANCELLED => 'لغو شده',
                                                                default => $latestPayment->status,
                                                            }
                                                            : 'ثبت نشده';
                                                    @endphp

                                                <article class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                                                        <div>
                                                                <span class="block text-xs text-gray-500">روان‌شناس</span>
                                                                <strong class="mt-1 block text-sm text-gray-900">
                                                                        {{ trim(($appointment->psychologist?->first_name ?? '') . ' ' . ($appointment->psychologist?->last_name ?? '')) ?: 'ثبت نشده' }}
                                                                    </strong>
                                                            </div>

                                                        <div>
                                                                <span class="block text-xs text-gray-500">تاریخ جلسه</span>
                                                                <strong class="mt-1 block text-sm text-gray-900">
                                                                        {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                                                                       ، {{ $startsAt->format('H:i') }}
                                                                    </strong>
                                                            </div>

                                                        <div>
                                                                <span class="block text-xs text-gray-500">وضعیت پرداخت</span>
                                                                <strong class="mt-1 block text-sm text-gray-900">
                                                                        {{ $paymentStatusLabel }}
                                                                    </strong>
                                                            </div>

                                                        <div>
                                                                <span class="block text-xs text-gray-500">مبلغ پرداخت</span>
                                                                <strong class="mt-1 block text-sm text-gray-900">
                                                                        @if ($latestPayment)
                                                                                {{ number_format($latestPayment->amount) }} تومان
                                                                            @else
                                                                                ثبت نشده
                                                                            @endif
                                                                    </strong>
                                                            </div>
                                                    </article>
                                            @endforeach
                                    </div>

                                <div class="mt-6">
                                        {{ $completedAppointments->links() }}
                                    </div>
                            @endif
                    </section>
            </main>
    @endsection
