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
