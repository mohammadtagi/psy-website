<?php

namespace Tests\Feature\Booking;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_client_can_book_a_45_minute_online_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $localDate = '2026-09-24';
        $startsAt = CarbonImmutable::parse(
            "{$localDate} 10:00:00",
            'Asia/Tehran',
        )->utc();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('booking.store', $localDate), [
                'availability_id' => $availability->id,
                'start_time' => '10:00',
                'duration_minutes' => 45,
                'session_type' => Appointment::SESSION_TYPE_ONLINE,
                'first_name' => 'علی',
                'last_name' => 'رضایی',
                'birth_date' => '1375-04-12',
            ]);

        $appointment = Appointment::query()->firstOrFail();

        $response
            ->assertRedirect(route('booking.confirmation', $appointment))
            ->assertSessionHas('status', 'نوبت شما با موفقیت ثبت شد.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'psychologist_id' => $psychologist->id,
            'client_id' => $client->id,
            'availability_id' => $availability->id,
            'duration_minutes' => 45,
            'session_type' => Appointment::SESSION_TYPE_ONLINE,
            'status' => Appointment::STATUS_CONFIRMED,
            'amount' => 760000,
            'hold_expires_at' => null,
        ]);

        $this->assertSame(
            '1375-04-12',
            $client->fresh()->birth_date->format('Y-m-d'),
        );
    }

    public function test_client_can_book_a_60_minute_in_person_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $localDate = '2026-09-24';
        $startsAt = CarbonImmutable::parse(
            "{$localDate} 10:00:00",
            'Asia/Tehran',
        )->utc();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create([
                'ends_at' => $startsAt->addMinutes(60),
            ]);

        $response = $this
            ->actingAs($client)
            ->post(route('booking.store', $localDate), [
                'availability_id' => $availability->id,
                'start_time' => '10:00',
                'duration_minutes' => 60,
                'session_type' => Appointment::SESSION_TYPE_IN_PERSON,
                'first_name' => 'مریم',
                'last_name' => 'احمدی',
                'birth_date' => '1372-08-20',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'psychologist_id' => $psychologist->id,
            'duration_minutes' => 60,
            'session_type' => Appointment::SESSION_TYPE_IN_PERSON,
            'status' => Appointment::STATUS_CONFIRMED,
            'amount' => 950000,
        ]);
    }

    public function test_client_cannot_book_an_occupied_time(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $firstClient = User::factory()->client()->create();
        $secondClient = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $localDate = '2026-09-24';
        $startsAt = CarbonImmutable::parse(
            "{$localDate} 10:00:00",
            'Asia/Tehran',
        )->utc();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        Appointment::factory()
            ->forClient($firstClient)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->create();

        $response = $this
            ->actingAs($secondClient)
            ->post(route('booking.store', $localDate), [
                'availability_id' => $availability->id,
                'start_time' => '10:00',
                'duration_minutes' => 45,
                'session_type' => 'online',
                'first_name' => 'رضا',
                'last_name' => 'کریمی',
                'birth_date' => '1378-01-10',
            ]);

        $response->assertSessionHasErrors('starts_at');

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_client_cannot_book_disabled_availability(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $localDate = '2026-09-24';
        $startsAt = CarbonImmutable::parse(
            "{$localDate} 10:00:00",
            'Asia/Tehran',
        )->utc();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->disabled()
            ->from($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('booking.store', $localDate), [
                'availability_id' => $availability->id,
                'start_time' => '10:00',
                'duration_minutes' => 45,
                'session_type' => 'online',
                'first_name' => 'سارا',
                'last_name' => 'محمدی',
                'birth_date' => '1379-02-15',
            ]);

        $response->assertSessionHasErrors('starts_at');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_client_cannot_book_past_or_immediately_starting_time(): void
    {
        $now = CarbonImmutable::parse('2026-09-24 06:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $psychologist = User::factory()->psychologist()->create();

        $localDate = '2026-09-24';
        $startsAt = CarbonImmutable::parse(
            "{$localDate} 09:00:00",
            'Asia/Tehran',
        )->utc();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        $response = $this
            ->actingAs($client)
            ->post(route('booking.store', $localDate), [
                'availability_id' => $availability->id,
                'start_time' => '09:00',
                'duration_minutes' => 45,
                'session_type' => 'online',
                'first_name' => 'حسن',
                'last_name' => 'اکبری',
                'birth_date' => '1370-03-01',
            ]);

        $response->assertSessionHasErrors('starts_at');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_psychologist_cannot_submit_booking_request(): void
    {
        $psychologist = User::factory()->psychologist()->create();

        $response = $this
            ->actingAs($psychologist)
            ->post(route('booking.store', '2026-09-24'), [
                'availability_id' => 1,
                'start_time' => '10:00',
                'duration_minutes' => 45,
                'session_type' => 'online',
                'first_name' => 'روان',
                'last_name' => 'شناس',
                'birth_date' => '1365-01-01',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('appointments', 0);
    }
}
