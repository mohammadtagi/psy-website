<section
    id="client-sessions"
    class="border border-gray-200 bg-white p-5 md:col-span-2"
>
    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            جلسات این مراجع
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            همه نوبت‌های این مراجع با شما؛ مدیریت جلسه برای نوبت‌های تأییدشده در دسترس است.
        </p>
    </header>

    @if ($recordAppointments->isEmpty())
        <p class="mt-5 text-sm text-gray-500">
            نوبتی برای این مراجع با شما ثبت نشده است.
        </p>
    @else
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-gray-200 text-gray-600">
                <tr>
                    <th scope="col" class="whitespace-nowrap px-3 py-3">
                        تاریخ و ساعت
                    </th>
                    <th scope="col" class="whitespace-nowrap px-3 py-3">
                        نوع جلسه
                    </th>
                    <th scope="col" class="whitespace-nowrap px-3 py-3">
                        وضعیت رزرو
                    </th>
                    <th scope="col" class="whitespace-nowrap px-3 py-3">
                        وضعیت حضور
                    </th>
                    <th scope="col" class="whitespace-nowrap px-3 py-3">
                        عملیات
                    </th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                @foreach ($recordAppointments as $sessionAppointment)
                    @php
                        $startsAt = $sessionAppointment->starts_at
                            ?->copy()
                            ->setTimezone('Asia/Tehran');

                        $bookingStatus = match ($sessionAppointment->status) {
                            \App\Models\Appointment::STATUS_CONFIRMED => 'تأییدشده',
                            \App\Models\Appointment::STATUS_PENDING_PAYMENT => 'در انتظار پرداخت',
                            \App\Models\Appointment::STATUS_CANCELLED => 'لغوشده',
                            'expired' => 'منقضی‌شده',
                            'completed' => 'تکمیل‌شده',
                            'no_show' => 'عدم حضور',
                            default => 'سایر',
                        };

                        $attendanceStatus = match ($sessionAppointment->attendance_status) {
                            'held' => 'برگزارشده',
                            'absent' => 'عدم حضور',
                            default => 'ثبت نشده',
                        };

                        $sessionType = match ($sessionAppointment->session_type) {
                            'online' => 'آنلاین',
                            'in_person' => 'حضوری',
                            default => '—',
                        };

                        $canManage = $sessionAppointment->status
                            === \App\Models\Appointment::STATUS_CONFIRMED;
                    @endphp

                    <tr>
                        <td class="whitespace-nowrap px-3 py-4">
                            @if ($startsAt)
                                {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
                                <span class="mr-2 text-gray-500">
                                        {{ $startsAt->format('H:i') }}
                                    </span>
                            @else
                                —
                            @endif

                            <div class="mt-2 text-xs text-gray-500">
                                کد پیگیری:
                                <span dir="ltr" class="inline-block font-mono">
        {{ $sessionAppointment->tracking_code }}
    </span>
                            </div>
                        </td>


                        <td class="whitespace-nowrap px-3 py-4">
                            <div>{{ $sessionType }}</div>

                            @if ($sessionAppointment->isManuallyBooked())
                                <div class="mt-1 text-xs text-amber-700 bg-amber-50 rounded px-1.5 py-0.5 inline-block">
                                    {{ $sessionAppointment->bookingSourceLabel() }}
                                    —
                                    {{ $sessionAppointment->manualPaymentMethodLabel() }}
                                </div>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-3 py-4">
                            {{ $bookingStatus }}
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                            {{ $attendanceStatus }}
                        </td>

                        <td class="whitespace-nowrap px-3 py-4">
                            @if ($canManage)
                                <a
                                    href="{{ route(
                                            'psychologist.appointments.session.show',
                                            $sessionAppointment
                                        ) }}"
                                    class="inline-flex min-h-11 items-center text-indigo-700 hover:underline"
                                >
                                    مدیریت جلسه
                                </a>
                            @else
                                <span class="text-xs text-gray-500">
                                        مدیریت جلسه در دسترس نیست
                                    </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if ($recordAppointments->hasPages())
            <div class="mt-5">
                {{ $recordAppointments->fragment('client-sessions')->links() }}
            </div>
        @endif
    @endif
</section>
