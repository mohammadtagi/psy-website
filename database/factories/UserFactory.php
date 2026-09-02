<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'mobile'        => '+9891'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
            'name'          => $this->faker->name(),
            'gender'        => $this->faker->randomElement(['male', 'female']),
            'birth_date'    => $this->faker->optional()->date(),
            'role'          => 'client',
            'status'        => 'active',
            'last_login_at' => now(),
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['status' => 'blocked']);
    }
}
