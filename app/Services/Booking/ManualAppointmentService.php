<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManualAppointmentService
{
    public function create(
        User $psychologist,
        User $client,
        array $input,
    ): Appointment {
        abort_unless($psychologist->isPsychologist(), 403);

        $data = Validator::make($input, [
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'session_type' => [
                'required',
                Rule::in(['online', 'in_person']),
            ],
            'manual_payment_method' => [
                'required',
                Rule::in([
                    Appointment::MANUAL_PAYMENT_EXTERNAL,
                    Appointment::MANUAL_PAYMENT_FREE,
                ]),
            ],
            'amount' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],
        ])->validate();

        if (
            $data['manual_payment_method'] === Appointment::MANUAL_PAYMENT_EXTERNAL
            && (int) $data['amount'] === 0
        ) {
            throw ValidationException::withMessages([
                'amount' => 'برای پرداخت خارج از سایت، مبلغ باید بیشتر از صفر باشد.',
            ]);
        }

        $startsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $data['date'].' '.$data['time'],
            'Asia/Tehran',
        )->utc();

        $endsAt = $startsAt->addMinutes((int) $data['duration_minutes']);

        return DB::transaction(function () use (
            $psychologist,
            $client,
            $data,
            $startsAt,
            $endsAt,
        ): Appointment {
            // همان قفل مشترک سرویس رزرو سایت.
            $lockedPsychologist = User::query()
                ->whereKey($psychologist->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedPsychologist->isPsychologist(), 403);

            $currentClient = User::query()
                ->whereKey($client->getKey())
                ->firstOrFail();

            // همان قاعده دسترسی پرونده بالینی.
            abort_unless(
                $currentClient->isClient()
                && (
                    (int) $currentClient->created_by_psychologist_id
                    === (int) $lockedPsychologist->getKey()
                    || $currentClient->clientAppointments()
                        ->where(
                            'psychologist_id',
                            $lockedPsychologist->getKey(),
                        )
                        ->exists()
                ),
                404,
            );

            abort_unless(
                $currentClient->clinicalRecord()->exists(),
                404,
            );

            $now = CarbonImmutable::now('UTC');

            $conflict = Appointment::query()
                ->where(
                    'psychologist_id',
                    $lockedPsychologist->getKey(),
                )
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->where(function ($query) use ($now): void {
                    $query->whereIn('status', [
                        Appointment::STATUS_CONFIRMED,
                        Appointment::STATUS_COMPLETED,
                        Appointment::STATUS_NO_SHOW,
                    ])->orWhere(function ($pending) use ($now): void {
                        $pending
                            ->where(
                                'status',
                                Appointment::STATUS_PENDING_PAYMENT,
                            )
                            ->where('hold_expires_at', '>', $now);
                    });
                })
                ->lockForUpdate()
                ->first(['id']);

            if ($conflict !== null) {
                throw ValidationException::withMessages([
                    'time' => 'این بازه با نوبت دیگری تداخل دارد.',
                ]);
            }

            $appointment = new Appointment();

            // هیچ شناسه، وضعیت یا ارتباط درمانی از فرم پذیرفته نمی‌شود.
            $appointment->psychologist_id = $lockedPsychologist->getKey();
            $appointment->client_id = $currentClient->getKey();
            $appointment->availability_id = null;

            $appointment->starts_at = $startsAt;
            $appointment->ends_at = $endsAt;
            $appointment->duration_minutes = (int) $data['duration_minutes'];
            $appointment->session_type = $data['session_type'];

            $appointment->status = Appointment::STATUS_CONFIRMED;
            $appointment->attendance_status = Appointment::ATTENDANCE_PENDING;
            $appointment->hold_expires_at = null;

            $appointment->booking_source = Appointment::BOOKING_SOURCE_MANUAL;
            $appointment->manual_payment_method = $data['manual_payment_method'];

            $appointment->amount =
                $data['manual_payment_method'] === Appointment::MANUAL_PAYMENT_FREE
                    ? 0
                    : (int) $data['amount'];

            // «خارج از مسیر درمان»، نه رایگان از نظر مالی.
            $appointment->therapy_session_type = Appointment::THERAPY_TYPE_FREE;
            $appointment->treatment_plan_id = null;
            $appointment->treatment_plan_stage_id = null;

            // کد پیگیری توسط رویداد فعلی مدل تولید می‌شود.
            $appointment->save();

            return $appointment;
        }, 3);
    }
}
