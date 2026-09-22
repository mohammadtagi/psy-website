<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class BookingService
{
    private const LOCAL_TIMEZONE = 'Asia/Tehran';

    private const SLOT_INTERVAL_MINUTES = 30;

    private const MINUTE_BEFORE_START_LIMIT = 1;

    private const PRICE_45_MINUTES = 760000;

    private const PRICE_60_MINUTES = 950000;

    /**
     * @return array<int, array{
     *     availability_id: int,
     *     starts_at: CarbonImmutable,
     *     ends_at: CarbonImmutable,
     *     start_time: string,
     *     end_time: string,
     *     duration_minutes: int,
     *     amount: int
     * }>
     */

    public function availableSlotsForDate(
        User $psychologist,
        string $date,
        int $durationMinutes,
    ): array {
        $this->validateDuration($durationMinutes);

        $localDate = $this->parseLocalDate($date);

        if ($localDate->isBefore(
            CarbonImmutable::now(self::LOCAL_TIMEZONE)->startOfDay()
        )) {
            return [];
        }

        $dayStart = $localDate->startOfDay()->utc();
        $dayEnd = $localDate->addDay()->startOfDay()->utc();

        $availabilities = Availability::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where('status', Availability::STATUS_ACTIVE)
            ->where('starts_at', '<', $dayEnd)
            ->where('ends_at', '>', $dayStart)
            ->orderBy('starts_at')
            ->get();

        if ($availabilities->isEmpty()) {
            return [];
        }

        $now = CarbonImmutable::now('UTC');

        $appointments = Appointment::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where(
                'starts_at',
                '<',
                $dayEnd->addMinutes($durationMinutes),
            )
            ->where('ends_at', '>', $dayStart)
            ->where(function ($query) use ($now): void {
                $query
                    ->whereIn('status', [
                        Appointment::STATUS_CONFIRMED,
                        Appointment::STATUS_COMPLETED,
                        Appointment::STATUS_NO_SHOW,
                    ])
                    ->orWhere(function ($pending) use ($now): void {
                        $pending
                            ->where(
                                'status',
                                Appointment::STATUS_PENDING_PAYMENT,
                            )
                            ->where('hold_expires_at', '>', $now);
                    });
            })
            ->get(['starts_at', 'ends_at', 'status', 'hold_expires_at']);

        $slots = [];

        foreach ($availabilities as $availability) {
            $availabilityStart = CarbonImmutable::instance(
                $availability->starts_at
            );

            $availabilityEnd = CarbonImmutable::instance(
                $availability->ends_at
            );

            $firstStart = $this->roundUpToHalfHour(
                $availabilityStart->lt($dayStart)
                    ? $dayStart
                    : $availabilityStart,
            );

            for (
                $slotStart = $firstStart;
                $slotStart->lt($dayEnd)
                && $slotStart->addMinutes($durationMinutes)->lte($availabilityEnd);
                $slotStart = $slotStart->addMinutes(self::SLOT_INTERVAL_MINUTES)
            ) {
                $slotEnd = $slotStart->addMinutes($durationMinutes);

                if (
                    $slotStart->lt(
                        $now->addMinutes(self::MINUTE_BEFORE_START_LIMIT)
                    )
                ) {
                    continue;
                }

                // Continue with the existing overlap check and slot construction.


                if ($this->overlapsBookableAppointment(
                    $slotStart,
                    $slotEnd,
                    $appointments,
                    $now,
                )) {
                    continue;
                }

                $localStart = $slotStart->setTimezone(self::LOCAL_TIMEZONE);
                $localEnd = $slotEnd->setTimezone(self::LOCAL_TIMEZONE);

                $slots[] = [
                    'availability_id' => $availability->getKey(),
                    'starts_at' => $slotStart,
                    'ends_at' => $slotEnd,
                    'start_time' => $localStart->format('H:i'),
                    'end_time' => $localEnd->format('H:i'),
                    'duration_minutes' => $durationMinutes,
                    'amount' => $this->amountForDuration($durationMinutes),
                ];

            }
        }

        return $this->uniqueSlots($slots);
    }

    /**
     * @param array<string, mixed> $data
     */
    /**
     * @param array<string, mixed> $data
     */
    public function book(
        User $client,
        User $psychologist,
        array $data,
    ): Appointment {
        $durationMinutes = (int) ($data['duration_minutes'] ?? 0);
        $this->validateDuration($durationMinutes);

        $availabilityId = (int) ($data['availability_id'] ?? 0);
        $startsAtInput = (string) ($data['starts_at'] ?? '');

        $startsAt = CarbonImmutable::parse($startsAtInput)->utc();
        $endsAt = $startsAt->addMinutes($durationMinutes);

        if ($startsAt->minute % 30 !== 0 || $startsAt->second !== 0) {
            throw ValidationException::withMessages([
                'starts_at' => 'زمان شروع باید روی ساعت یا نیم‌ساعت باشد.',
            ]);
        }

        $now = CarbonImmutable::now('UTC');

        if (
            $startsAt->lt(
                $now->addMinutes(self::MINUTE_BEFORE_START_LIMIT)
            )
        ) {
            throw ValidationException::withMessages([
                'starts_at' => 'رزرو این زمان امکان‌پذیر نیست.',
            ]);
        }

        return DB::transaction(function () use (
            $client,
            $psychologist,
            $availabilityId,
            $durationMinutes,
            $startsAt,
            $endsAt,
            $data,
        ): Appointment {
            $lockedPsychologist = User::query()
                ->whereKey($psychologist->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedPsychologist->isPsychologist()) {
                throw ValidationException::withMessages([
                    'psychologist_id' => 'کاربر انتخاب‌شده روان‌شناس نیست.',
                ]);
            }

            $availability = Availability::query()
                ->whereKey($availabilityId)
                ->where('psychologist_id', $lockedPsychologist->getKey())
                ->where('status', Availability::STATUS_ACTIVE)
                ->lockForUpdate()
                ->first();

            if ($availability === null) {
                throw ValidationException::withMessages([
                    'starts_at' => 'بازه حضور انتخاب‌شده معتبر نیست.',
                ]);
            }

            // زمان پس از دریافت قفل‌ها دوباره بررسی می‌شود.
            $now = CarbonImmutable::now('UTC');

            if (
                $startsAt->lt(
                    $now->addMinutes(self::MINUTE_BEFORE_START_LIMIT)
                )
            ) {
                throw ValidationException::withMessages([
                    'starts_at' => 'رزرو این زمان امکان‌پذیر نیست.',
                ]);
            }

            $availabilityStart = CarbonImmutable::instance(
                $availability->starts_at
            );

            $availabilityEnd = CarbonImmutable::instance(
                $availability->ends_at
            );

            if (
                $startsAt->lt($availabilityStart)
                || $endsAt->gt($availabilityEnd)
            ) {
                throw ValidationException::withMessages([
                    'starts_at' => 'جلسه انتخاب‌شده داخل بازه حضور قرار ندارد.',
                ]);
            }

            $conflict = Appointment::query()
                ->where('psychologist_id', $lockedPsychologist->getKey())
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->where(function ($query) use ($now): void {
                    $query
                        ->whereIn('status', [
                            Appointment::STATUS_CONFIRMED,
                        ])
                        ->orWhere(function ($pending) use ($now): void {
                            $pending
                                ->where(
                                    'status',
                                    Appointment::STATUS_PENDING_PAYMENT,
                                )
                                ->where('hold_expires_at', '>', $now);
                        });
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'starts_at' => 'این زمان قبلاً رزرو شده است.',
                ]);
            }

            $holdMinutes = (int) config('payment.hold_minutes', 15);

            if ($holdMinutes < 1) {
                throw new \LogicException(
                    'payment.hold_minutes must be a positive integer.'
                );
            }

            $appointment = Appointment::query()->create([
                'psychologist_id' => $lockedPsychologist->getKey(),
                'client_id' => $client->getKey(),
                'availability_id' => $availability->getKey(),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'duration_minutes' => $durationMinutes,
                'session_type' => $data['session_type'] ?? 'online',
                'status' => Appointment::STATUS_PENDING_PAYMENT,
                'amount' => $this->amountForDuration($durationMinutes),
                'hold_expires_at' => $now->addMinutes($holdMinutes),
            ]);

            $appointment->payments()->create([
                'client_id' => $client->getKey(),
                'amount' => $appointment->amount,
                'gateway' => Payment::GATEWAY_ZARINPAL,
                'status' => Payment::STATUS_INITIATED,
            ]);

            return $appointment;
        }, 3);
    }

    private function validateDuration(int $durationMinutes): void
    {
        if (! in_array($durationMinutes, [45, 60], true)) {
            throw ValidationException::withMessages([
                'duration_minutes' => 'مدت جلسه باید ۴۵ یا ۶۰ دقیقه باشد.',
            ]);
        }
    }

    private function amountForDuration(int $durationMinutes): int
    {
        return match ($durationMinutes) {
            45 => self::PRICE_45_MINUTES,
            60 => self::PRICE_60_MINUTES,
            default => throw new \InvalidArgumentException(
                'Unsupported appointment duration.'
            ),
        };
    }

    private function parseLocalDate(string $date): CarbonImmutable
    {
        try {
            $parsed = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
                self::LOCAL_TIMEZONE,
            );
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'date' => 'تاریخ انتخاب‌شده معتبر نیست.',
            ]);
        }

        if (
            $parsed === false
            || $parsed->format('Y-m-d') !== $date
        ) {
            throw ValidationException::withMessages([
                'date' => 'تاریخ انتخاب‌شده معتبر نیست.',
            ]);
        }

        return $parsed;
    }


    private function roundUpToHalfHour(
        CarbonImmutable $dateTime,
    ): CarbonImmutable {
        $local = $dateTime->setTimezone(self::LOCAL_TIMEZONE);

        $rounded = $local->startOfHour()->addMinutes(
            $local->minute >= 30 ? 30 : 0,
        );

        if ($rounded->lt($local)) {
            $rounded = $rounded->addMinutes(self::SLOT_INTERVAL_MINUTES);
        }

        return $rounded->utc();
    }


    /**
     * @param \Illuminate\Support\Collection<int, Appointment> $appointments
     */
    private function overlapsBookableAppointment(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
                        $appointments,
        CarbonImmutable $now,
    ): bool {
        foreach ($appointments as $appointment) {
            if (
                $appointment->status
                === Appointment::STATUS_PENDING_PAYMENT
                && $appointment->hold_expires_at?->lte($now)
            ) {
                continue;
            }

            $appointmentStart = CarbonImmutable::instance(
                $appointment->starts_at
            );

            $appointmentEnd = CarbonImmutable::instance(
                $appointment->ends_at
            );

            if (
                $appointmentStart->lt($endsAt)
                && $appointmentEnd->gt($startsAt)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, array<string, mixed>> $slots
     * @return array<int, array<string, mixed>>
     */
    private function uniqueSlots(array $slots): array
    {
        $unique = [];

        foreach ($slots as $slot) {
            $key = $slot['starts_at']->toIso8601String()
                . '|'
                . $slot['ends_at']->toIso8601String();

            $unique[$key] = $slot;
        }

        return array_values($unique);
    }
}
