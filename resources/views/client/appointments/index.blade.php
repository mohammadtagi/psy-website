@extends('components.layouts.app')

@section('title', 'نوبت‌های من')

@section('content')
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                نوبت‌های من
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                فهرست نوبت‌های رزروشده و وضعیت آن‌ها
            </p>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($appointments->isEmpty())
            <section class="rounded-lg border border-gray-200 bg-white px-6 py-12 text-center shadow-sm">
                <p class="text-sm text-gray-600">
                    هنوز نوبتی برای شما ثبت نشده است.
                </p>

                <a
                    href="{{ route('booking.index') }}"
                    class="mt-4 inline-flex min-h-11 items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    رزرو نوبت
                </a>
            </section>
        @else
            <div class="space-y-4">
                @foreach ($appointments as $appointment)
                    @php
                        $startsAt = $appointment->starts_at->setTimezone('Asia/Tehran');
                        $endsAt = $appointment->ends_at->setTimezone('Asia/Tehran');

                        $isActive = in_array($appointment->status, [
                            \App\Models\Appointment::STATUS_CONFIRMED,
                            \App\Models\Appointment::STATUS_PENDING_PAYMENT,
                        ], true);

                        $canCancel = $isActive
                            && $startsAt->greaterThanOrEqualTo(
                                now('Asia/Tehran')->addHours(12)
                            );

                    @endphp

                    <article class="border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="grid flex-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <span class="block text-xs text-gray-500">روان‌شناس</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $appointment->psychologist->first_name }}
                                        {{ $appointment->psychologist->last_name }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">تاریخ</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">ساعت</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        {{ $startsAt->format('H:i') }}
                                        تا
                                        {{ $endsAt->format('H:i') }}
                                    </strong>
                                </div>

                                <div>
                                    <span class="block text-xs text-gray-500">وضعیت</span>
                                    <strong class="mt-1 block text-sm text-gray-900">
                                        @switch($appointment->status)
                                            @case(\App\Models\Appointment::STATUS_CONFIRMED)
                                                تأیید شده
                                                @break
                                            @case(\App\Models\Appointment::STATUS_PENDING_PAYMENT)
                                                در انتظار پرداخت
                                                @break
                                            @case(\App\Models\Appointment::STATUS_CANCELLED)
                                                لغو شده
                                                @break
                                            @case(\App\Models\Appointment::STATUS_COMPLETED)
                                                تکمیل شده
                                                @break
                                            @default
                                                {{ $appointment->status }}
                                        @endswitch
                                    </strong>
                                </div>
                            </div>

                            @if ($canCancel)
                                <form
                                    method="POST"
                                    action="{{ route('client.appointments.cancel', $appointment) }}"
                                    class="shrink-0"
                                    onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="inline-flex min-h-10 items-center rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50"
                                    >
                                        لغو نوبت
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if ($appointment->status === \App\Models\Appointment::STATUS_CANCELLED)
                            @php
                                $cancelledAt = optional($appointment->cancelled_at)
                                    ->setTimezone('Asia/Tehran');
                            @endphp

                            <p class="mt-4 border-t border-gray-100 pt-4 text-xs text-gray-500">
                                این نوبت در
                                {{ $cancelledAt
                                    ? \App\Support\PersianDate::format($cancelledAt, 'yyyy/MM/dd')
                                      . ' ' . $cancelledAt->format('H:i')
                                    : '---'
                                }}
                                لغو شده است.
                            </p>
                        @endif

                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $appointments->links() }}
            </div>
        @endif
    </main>
@endsection
