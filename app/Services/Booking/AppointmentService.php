<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    private const TIMEZONE = 'Asia/Tehran';

    public function cancel(
        Appointment $appointment,
        User $actor,
        ?string $reason = null,
        bool $bypassClientTimeLimit = false,
    ): Appointment {
        return \DB::transaction(function () use (
            $appointment,
            $actor,
            $reason,
            $bypassClientTimeLimit,
        ): Appointment {
            $lockedAppointment = Appointment::query()
                ->whereKey($appointment->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCanCancel(
                appointment: $lockedAppointment,
                actor: $actor,
                bypassClientTimeLimit: $bypassClientTimeLimit,
            );

            $lockedAppointment->update([
                'status' => Appointment::STATUS_CANCELLED,
                'cancelled_at' => CarbonImmutable::now('UTC'),
                'cancelled_by' => $actor->getKey(),
                'cancellation_reason' => $reason,
                'hold_expires_at' => null,
            ]);

            return $lockedAppointment->refresh();
        }, 3);
    }

    private function ensureCanCancel(
        Appointment $appointment,
        User $actor,
        bool $bypassClientTimeLimit,
    ): void {
        if (
            ! in_array($appointment->status, [
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_PENDING_PAYMENT,
            ], true)
        ) {
            throw ValidationException::withMessages([
                'appointment' => 'این نوبت قابل لغو نیست.',
            ]);
        }

        if ($appointment->starts_at->lessThanOrEqualTo(
            CarbonImmutable::now('UTC')
        )) {
            throw ValidationException::withMessages([
                'appointment' => 'نوبت‌های گذشته قابل لغو نیستند.',
            ]);
        }

        $isClient = (int) $appointment->client_id === (int) $actor->getKey();
        $isPsychologist = (
            (int) $appointment->psychologist_id
            === (int) $actor->getKey()
        );

        if (! $isClient && ! $isPsychologist) {
            abort(403);
        }

        if (
            $isClient
            && ! $bypassClientTimeLimit
            && $appointment->starts_at->lt(
                CarbonImmutable::now('UTC')->addHours(12)
            )
        ) {
            throw ValidationException::withMessages([
                'appointment' => 'لغو نوبت فقط تا ۱۲ ساعت پیش از شروع امکان‌پذیر است.',
            ]);
        }

    }
}
