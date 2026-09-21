<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AppointmentCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }
    public function test_client_can_cancel_exactly_at_the_twelve_hour_boundary(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(12);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('client.appointments.cancel', $appointment));

        $response
            ->assertRedirect(route('client.appointments.index'))
            ->assertSessionHas('status', 'نوبت با موفقیت لغو شد.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_by' => $client->id,
        ]);
    }

    public function test_client_can_cancel_appointment_more_than_twelve_hours_before_start(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(13);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('client.appointments.cancel', $appointment), [
                'cancellation_reason' => 'تغییر برنامه',
            ]);

        $response
            ->assertRedirect(route('client.appointments.index'))
            ->assertSessionHas('status', 'نوبت با موفقیت لغو شد.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_by' => $client->id,
            'cancellation_reason' => 'تغییر برنامه',
            'hold_expires_at' => null,
        ]);

        $this->assertNotNull($appointment->fresh()->cancelled_at);
    }

    public function test_client_cannot_cancel_appointment_less_than_twelve_hours_before_start(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(11);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('client.appointments.cancel', $appointment));

        $response->assertSessionHasErrors('appointment');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ]);
    }

    public function test_psychologist_can_cancel_appointment_inside_client_twelve_hour_limit(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(2);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($psychologist)
            ->post(route('psychologist.appointments.cancel', $appointment), [
                'cancellation_reason' => 'لغو توسط روان‌شناس',
            ]);

        $response
            ->assertSessionHas('status', 'نوبت با موفقیت لغو شد.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_by' => $psychologist->id,
            'cancellation_reason' => 'لغو توسط روان‌شناس',
        ]);
    }

    public function test_client_cannot_cancel_another_clients_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $owner = User::factory()->client()->create();
        $anotherClient = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(13);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($owner)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($anotherClient)
            ->post(route('client.appointments.cancel', $appointment));

        $response->assertNotFound();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
            'cancelled_at' => null,
        ]);
    }

    public function test_psychologist_cannot_cancel_another_psychologists_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $ownerPsychologist = User::factory()->psychologist()->create();
        $anotherPsychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(13);

        $availability = Availability::factory()
            ->forPsychologist($ownerPsychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($ownerPsychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($anotherPsychologist)
            ->post(route('psychologist.appointments.cancel', $appointment));

        $response->assertNotFound();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
            'cancelled_at' => null,
        ]);
    }

    public function test_past_appointment_cannot_be_cancelled(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->subHour();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('client.appointments.cancel', $appointment));

        $response->assertSessionHasErrors('appointment');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
            'cancelled_at' => null,
        ]);
    }

    public function test_cancelled_appointment_cannot_be_cancelled_again(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $startsAt = $now->addHours(13);

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $appointment = Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->cancelled()
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('client.appointments.cancel', $appointment));

        $response->assertSessionHasErrors('appointment');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CANCELLED,
        ]);
    }
}
