<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startsAt = CarbonImmutable::now('UTC')
            ->addDays(2)
            ->setTime(9, 0);

        return [
            'psychologist_id' => User::factory()->psychologist(),
            'client_id' => User::factory()->client(),
            'availability_id' => function (array $attributes) use ($startsAt) {
                return Availability::factory()->create([
                    'psychologist_id' => $attributes['psychologist_id'],
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->addHours(2),
                ])->id;
            },

            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes(45),

            'duration_minutes' => 45,
            'session_type' => Appointment::SESSION_TYPE_ONLINE,
            'status' => Appointment::STATUS_CONFIRMED,
            'amount' => 760,

            'hold_expires_at' => null,
            'meeting_url' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ];
    }

    public function pendingPayment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Appointment::STATUS_PENDING_PAYMENT,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Appointment::STATUS_CONFIRMED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_at' => CarbonImmutable::now('UTC'),
            'cancellation_reason' => 'لغو تستی',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Appointment::STATUS_COMPLETED,
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Appointment::STATUS_NO_SHOW,
        ]);
    }

    public function duration(int $minutes, ?int $amount = null): static
    {
        return $this->state(function (array $attributes) use (
            $minutes,
            $amount,
        ): array {
            $startsAt = CarbonImmutable::parse(
                $attributes['starts_at'],
                'UTC',
            );

            return [
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->addMinutes($minutes),
                'duration_minutes' => $minutes,
                'amount' => $amount ?? ($minutes === 60 ? 950 : 760),
            ];
        });
    }

    public function at(CarbonImmutable $startsAt): static
    {
        // کلمه use ($startsAt) در fn حذف شد
        return $this->state(fn (array $attributes): array => [
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes(
                (int) ($attributes['duration_minutes'] ?? 45),
            ),
        ]);
    }

    public function forClient(User $client): static
    {
        return $this->state(fn (array $attributes): array => [
            'client_id' => $client->getKey(),
        ]);
    }

    public function forPsychologist(User $psychologist): static
    {
        return $this->state(fn (array $attributes): array => [
            'psychologist_id' => $psychologist->getKey(),
        ]);
    }

    public function usingAvailability(Availability $availability): static
    {
        return $this->state(fn (array $attributes): array => [
            'availability_id' => $availability->getKey(),
            'psychologist_id' => $availability->psychologist_id,
        ]);
    }
}
