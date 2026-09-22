<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'client_id' => fn (array $attributes): int =>
            Appointment::query()
                ->findOrFail($attributes['appointment_id'])
                ->client_id,
            'amount' => 760000,
            'gateway' => Payment::GATEWAY_ZARINPAL,
            'status' => Payment::STATUS_INITIATED,
            'authority' => null,
            'ref_id' => null,
            'gateway_message' => null,
            'paid_at' => null,
        ];
    }

    public function pending(string $authority = 'TEST-AUTHORITY'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Payment::STATUS_PENDING,
            'authority' => $authority,
        ]);
    }

    public function paid(string $authority = 'TEST-AUTHORITY'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Payment::STATUS_PAID,
            'authority' => $authority,
            'ref_id' => 'TEST-REF-ID',
            'paid_at' => now(),
        ]);
    }

    public function failed(string $authority = 'TEST-AUTHORITY'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Payment::STATUS_FAILED,
            'authority' => $authority,
            'gateway_message' => 'خطای تستی در تأیید پرداخت.',
        ]);
    }

    public function cancelled(string $authority = 'TEST-AUTHORITY'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Payment::STATUS_CANCELLED,
            'authority' => $authority,
            'gateway_message' => 'پرداخت توسط کاربر لغو شد.',
        ]);
    }

    public function forAppointment(
        Appointment $appointment,
        ?int $amount = null,
    ): static {
        return $this->state([
            'appointment_id' => $appointment->getKey(),
            'client_id' => $appointment->client_id,
            'amount' => $amount ?? $appointment->amount,
        ]);
    }

}
