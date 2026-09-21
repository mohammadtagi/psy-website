<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Availability>
 */
class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition(): array
    {
        $startsAt = CarbonImmutable::now('UTC')
            ->addDays(2)
            ->setTime(9, 0);

        return [
            'psychologist_id' => User::factory()->psychologist(),
            'series_key' => null,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
            'status' => Availability::STATUS_ACTIVE,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Availability::STATUS_ACTIVE,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Availability::STATUS_DISABLED,
        ]);
    }

    public function forPsychologist(User $psychologist): static
    {
        return $this->state(fn (array $attributes): array => [
            'psychologist_id' => $psychologist->getKey(),
        ]);
    }

    public function from(CarbonImmutable $startsAt): static
    {
        // در فلش‌فانکشن کلمه use ($startsAt) حذف شد چون خودکار ارجاع داده می‌شود
        return $this->state(fn (array $attributes): array => [
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
        ]);
    }
}
