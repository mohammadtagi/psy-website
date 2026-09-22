@extends('components.layouts.app')

@section('title', 'مدیریت نوبت‌ها')

@section('content')
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                مدیریت نوبت‌ها
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                مشاهده و مدیریت نوبت‌های ثبت‌شده
            </p>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="overflow-x-auto border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-right">
                <thead class="bg-gray-50">
                <tr>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        مراجع
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        تاریخ و ساعت
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        مدت
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        نوع جلسه
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        وضعیت
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        عملیات
                    </th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($appointments as $appointment)
                    @php
                        $startsAt = $appointment->starts_at->setTimezone('Asia/Tehran');
                        $endsAt = $appointment->ends_at->setTimezone('Asia/Tehran');

                        $isActive = in_array($appointment->status, [
                            \App\Models\Appointment::STATUS_CONFIRMED,
                            \App\Models\Appointment::STATUS_PENDING_PAYMENT,
                        ], true);
                    @endphp

                    <tr>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                            {{ $appointment->client->first_name }}
                            {{ $appointment->client->last_name }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                            <br>
                            <span class="text-xs text-gray-500">
                                    {{ $startsAt->format('H:i') }}
                                    تا
                                    {{ $endsAt->format('H:i') }}
                                </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ $appointment->duration_minutes }} دقیقه
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE ? 'آنلاین' : 'حضوری' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
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
                        </td>

                        <td class="whitespace-nowrap px-4 py-4">
                            @if ($isActive)
                                <form
                                    method="POST"
                                    action="{{ route('psychologist.appointments.cancel', $appointment) }}"
                                    onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="text-sm font-medium text-red-700 hover:text-red-900"
                                    >
                                        لغو نوبت
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">
                                        بدون عملیات
                                    </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="6"
                            class="px-4 py-12 text-center text-sm text-gray-500"
                        >
                            هنوز نوبتی ثبت نشده است.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    </main>
@endsection
