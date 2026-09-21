<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * جلوگیری از Hash شدن دوباره رمز در ساخت چند کاربر.
     */
    protected static ?string $password = null;

    /**
     * تعریف وضعیت پیش‌فرض کاربر.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()
                ->dateTimeBetween('-70 years', '-18 years')
                ->format('Y-m-d'),
            'mobile' => fake()->unique()->numerify('09#########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_CLIENT,
        ];
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => User::ROLE_CLIENT,
        ]);
    }

    public function psychologist(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => User::ROLE_PSYCHOLOGIST,
        ]);
    }


    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
