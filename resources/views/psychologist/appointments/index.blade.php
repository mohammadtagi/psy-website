@php use App\Support\PersianDate; @endphp
@php use App\Models\Appointment; @endphp
@php use App\Models\Payment; @endphp
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
                        وضعیت نوبت
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        وضعیت پرداخت
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-gray-600">
                        مبلغ
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
                            Appointment::STATUS_CONFIRMED,
                            Appointment::STATUS_PENDING_PAYMENT,
                        ], true);

                        $latestPayment = $appointment->payments->first();
                    @endphp

                    <tr>
                        {{-- ۱. مراجع --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                            {{ $appointment->client->first_name }}
                            {{ $appointment->client->last_name }}
                        </td>

                        {{-- ۲. تاریخ و ساعت --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                            <br>
                            <span class="text-xs text-gray-500">
                                {{ $startsAt->format('H:i') }}
                                تا
                                {{ $endsAt->format('H:i') }}
                            </span>
                        </td>

                        {{-- ۳. مدت --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ $appointment->duration_minutes }} دقیقه
                        </td>

                        {{-- ۴. نوع جلسه --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            {{ $appointment->session_type === Appointment::SESSION_TYPE_ONLINE ? 'آنلاین' : 'حضوری' }}
                        </td>

                        {{-- ۵. وضعیت نوبت --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            @switch($appointment->status)
                                @case(Appointment::STATUS_PENDING_PAYMENT)
                                    در انتظار پرداخت
                                    @break

                                @case(Appointment::STATUS_CONFIRMED)
                                    تأییدشده
                                    @break

                                @case(Appointment::STATUS_CANCELLED)
                                    لغوشده
                                    @break

                                @case(Appointment::STATUS_COMPLETED)
                                    تکمیل‌شده
                                    @break

                                @case(Appointment::STATUS_NO_SHOW)
                                    عدم حضور
                                    @break

                                @default
                                    {{ $appointment->status ?: 'نامشخص' }}
                            @endswitch
                        </td>

                        {{-- ۶. وضعیت پرداخت --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            @if (! $latestPayment)
                                بدون پرداخت
                            @elseif ($latestPayment->status === Payment::STATUS_PAID)
                                پرداخت‌شده
                            @elseif ($latestPayment->status === Payment::STATUS_PENDING)
                                در انتظار پرداخت
                            @elseif ($latestPayment->status === Payment::STATUS_INITIATED)
                                شروع‌شده
                            @elseif ($latestPayment->status === Payment::STATUS_FAILED)
                                ناموفق
                            @elseif ($latestPayment->status === Payment::STATUS_CANCELLED)
                                لغوشده
                            @else
                                {{ $latestPayment->status }}
                            @endif
                        </td>

                        {{-- ۷. مبلغ --}}
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                            @if ($latestPayment)
                                {{ number_format((int) $latestPayment->amount) }} تومان
                            @else
                                {{ number_format((int) $appointment->amount) }} تومان
                            @endif
                        </td>

                        {{-- ۸. عملیات --}}
                        <td class="min-w-52 px-4 py-4">
                            @if ($isActive)
                                <form
                                    method="POST"
                                    action="{{ route('psychologist.appointments.cancel', $appointment) }}"
                                    class="space-y-2"
                                    onsubmit="return confirm('آیا از لغو این نوبت مطمئن هستید؟')"
                                >
                                    @csrf

                                    <textarea
                                        name="cancellation_reason"
                                        rows="2"
                                        maxlength="500"
                                        placeholder="دلیل لغو، اختیاری"
                                        class="w-full border-gray-300 text-xs"
                                    ></textarea>

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
                            colspan="8"
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
