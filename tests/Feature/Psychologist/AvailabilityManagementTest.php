<?php

namespace Tests\Feature\Psychologist;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(
            CarbonImmutable::parse('2026-09-22 09:00:00', 'Asia/Tehran'),
        );
    }

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'mode' => 'single',
            'from_date' => '1405/07/02',
            'to_date' => '1405/07/02',
            'start_time' => '10:00',
            'end_time' => '12:00',
        ], $overrides);
    }

    private function availabilityFor(
        User $psychologist,
        array $overrides = [],
    ): Availability {
        return Availability::factory()
            ->forPsychologist($psychologist)
            ->create(array_replace([
                'starts_at' => CarbonImmutable::parse(
                    '2026-09-24 10:00:00',
                    'Asia/Tehran',
                )->utc(),
                'ends_at' => CarbonImmutable::parse(
                    '2026-09-24 12:00:00',
                    'Asia/Tehran',
                )->utc(),
                'status' => Availability::STATUS_ACTIVE,
            ], $overrides));
    }

    private function appointmentFor(
        Availability $availability,
        string $status,
    ): Appointment {
        return Appointment::factory()
            ->forClient(User::factory()->client()->create())
            ->forPsychologist($availability->psychologist)
            ->at(
                CarbonImmutable::parse(
                    '2026-09-24 10:00:00',
                    'Asia/Tehran',
                )->utc(),
            )
            ->create([
                'availability_id' => $availability->getKey(),
                'status' => $status,
                'hold_expires_at' => $status === Appointment::STATUS_PENDING_PAYMENT
                    ? CarbonImmutable::now('UTC')->addMinutes(15)
                    : null,
            ]);
    }

    public function test_guests_cannot_access_availability_endpoints(): void
    {
        $availability = $this->availabilityFor(
            User::factory()->psychologist()->create(),
        );

        $this->get(route('psychologist.availabilities.index'))
            ->assertRedirect(route('auth.login'));

        $this->post(
            route('psychologist.availabilities.store'),
            $this->payload(),
        )->assertRedirect(route('auth.login'));

        $this->delete(
            route('psychologist.availabilities.destroy', $availability),
        )->assertRedirect(route('auth.login'));

        $this->assertDatabaseCount('availabilities', 1);

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->fresh()->status,
        );
    }

    public function test_clients_cannot_access_availability_endpoints(): void
    {
        $availability = $this->availabilityFor(
            User::factory()->psychologist()->create(),
        );

        $this->actingAs(User::factory()->client()->create());

        $this->get(route('psychologist.availabilities.index'))
            ->assertForbidden();

        $this->post(
            route('psychologist.availabilities.store'),
            $this->payload(),
        )->assertForbidden();

        $this->delete(
            route('psychologist.availabilities.destroy', $availability),
        )->assertForbidden();

        $this->assertDatabaseCount('availabilities', 1);

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->fresh()->status,
        );
    }

    public function test_index_shows_only_own_non_past_availabilities(): void
    {
        $psychologist = User::factory()->psychologist()->create();

        $active = $this->availabilityFor($psychologist);

        $disabled = $this->availabilityFor($psychologist, [
            'starts_at' => CarbonImmutable::parse(
                '2026-09-25 10:00:00',
                'Asia/Tehran',
            )->utc(),
            'ends_at' => CarbonImmutable::parse(
                '2026-09-25 12:00:00',
                'Asia/Tehran',
            )->utc(),
            'status' => Availability::STATUS_DISABLED,
        ]);

        $this->availabilityFor(
            User::factory()->psychologist()->create(),
        );

        $this->availabilityFor($psychologist, [
            'starts_at' => CarbonImmutable::parse(
                '2026-09-21 10:00:00',
                'Asia/Tehran',
            )->utc(),
            'ends_at' => CarbonImmutable::parse(
                '2026-09-21 12:00:00',
                'Asia/Tehran',
            )->utc(),
        ]);

        $this->actingAs($psychologist)
            ->get(route('psychologist.availabilities.index'))
            ->assertOk()
            ->assertViewIs('psychologist.availabilities.index')
            ->assertViewHas('timezone', 'Asia/Tehran')
            ->assertViewHas(
                'availabilities',
                fn ($rows): bool => $rows->getCollection()
                    ->modelKeys() === [$active->id, $disabled->id],
            )
            ->assertSee('مدیریت بازه‌های حضور')
            ->assertSee('غیرفعال')
            ->assertSee(
                route('psychologist.availabilities.destroy', $active),
                false,
            )
            ->assertDontSee(
                route('psychologist.availabilities.destroy', $disabled),
                false,
            );
    }

    public function test_psychologist_can_create_a_single_availability(): void
    {
        $psychologist = User::factory()->psychologist()->create();

        $this->actingAs($psychologist)
            ->post(
                route('psychologist.availabilities.store'),
                $this->payload(),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'بازه حضور با موفقیت ایجاد شد.');

        $this->assertDatabaseCount('availabilities', 1);

        $availability = Availability::query()->sole();

        $this->assertSame(
            (int) $psychologist->id,
            (int) $availability->psychologist_id,
        );

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->status,
        );

        $this->assertNull($availability->series_key);

        $this->assertSame(
            '2026-09-24 06:30:00',
            $availability->starts_at->copy()->utc()->format('Y-m-d H:i:s'),
        );

        $this->assertSame(
            '2026-09-24 08:30:00',
            $availability->ends_at->copy()->utc()->format('Y-m-d H:i:s'),
        );
    }

    public function test_psychologist_can_create_recurring_availabilities(): void
    {
        $psychologist = User::factory()->psychologist()->create();

        $this->actingAs($psychologist)
            ->post(
                route('psychologist.availabilities.store'),
                $this->payload([
                    'mode' => 'recurring',
                    'from_date' => '1405/07/02',
                    'to_date' => '1405/07/08',
                    'weekdays' => ['4', '6'],
                ]),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasNoErrors();

        $rows = Availability::query()->orderBy('starts_at')->get();

        $this->assertCount(2, $rows);
        $this->assertCount(1, $rows->pluck('series_key')->unique());
        $this->assertNotNull($rows->first()->series_key);

        $this->assertSame(
            ['2026-09-24', '2026-09-26'],
            $rows->map(
                fn (Availability $row): string => $row->starts_at
                    ->copy()
                    ->setTimezone('Asia/Tehran')
                    ->format('Y-m-d'),
            )->all(),
        );

        foreach ($rows as $row) {
            $this->assertSame(
                (int) $psychologist->id,
                (int) $row->psychologist_id,
            );
        }
    }

    public function test_invalid_date_returns_validation_errors(): void
    {
        $this->actingAs(User::factory()->psychologist()->create())
            ->from(route('psychologist.availabilities.index'))
            ->post(
                route('psychologist.availabilities.store'),
                $this->payload(['from_date' => 'invalid']),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasErrors('from_date');

        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_end_date_before_start_is_rejected(): void
    {
        $this->actingAs(User::factory()->psychologist()->create())
            ->from(route('psychologist.availabilities.index'))
            ->post(
                route('psychologist.availabilities.store'),
                $this->payload([
                    'mode' => 'recurring',
                    'to_date' => '1405/07/01',
                    'weekdays' => ['4'],
                ]),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasErrors('to_date');

        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_overlapping_availability_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();

        $this->availabilityFor($psychologist);

        $this->actingAs($psychologist)
            ->from(route('psychologist.availabilities.index'))
            ->post(
                route('psychologist.availabilities.store'),
                $this->payload(),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('availabilities', 1);
    }

    public function test_psychologist_can_disable_an_empty_availability(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $availability = $this->availabilityFor($psychologist);

        $this->actingAs($psychologist)
            ->delete(
                route('psychologist.availabilities.destroy', $availability),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'بازه حضور غیرفعال شد.');

        $this->assertDatabaseHas('availabilities', [
            'id' => $availability->id,
            'status' => Availability::STATUS_DISABLED,
        ]);
    }

    public function test_psychologist_cannot_disable_another_psychologists_availability(): void
    {
        $availability = $this->availabilityFor(
            User::factory()->psychologist()->create(),
        );

        $this->actingAs(User::factory()->psychologist()->create())
            ->delete(
                route('psychologist.availabilities.destroy', $availability),
            )
            ->assertNotFound();

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->fresh()->status,
        );
    }

    public function test_confirmed_appointment_prevents_disabling(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $availability = $this->availabilityFor($psychologist);

        $appointment = $this->appointmentFor(
            $availability,
            Appointment::STATUS_CONFIRMED,
        );

        $this->actingAs($psychologist)
            ->from(route('psychologist.availabilities.index'))
            ->delete(
                route('psychologist.availabilities.destroy', $availability),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasErrors('availability');

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->fresh()->status,
        );

        $this->assertSame(
            Appointment::STATUS_CONFIRMED,
            $appointment->fresh()->status,
        );
    }

    public function test_unexpired_pending_payment_prevents_disabling(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $availability = $this->availabilityFor($psychologist);

        $this->appointmentFor(
            $availability,
            Appointment::STATUS_PENDING_PAYMENT,
        );

        $this->actingAs($psychologist)
            ->from(route('psychologist.availabilities.index'))
            ->delete(
                route('psychologist.availabilities.destroy', $availability),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasErrors('availability');

        $this->assertSame(
            Availability::STATUS_ACTIVE,
            $availability->fresh()->status,
        );
    }

    public function test_cancelled_appointment_does_not_prevent_disabling(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $availability = $this->availabilityFor($psychologist);

        $appointment = $this->appointmentFor(
            $availability,
            Appointment::STATUS_CANCELLED,
        );

        $this->actingAs($psychologist)
            ->delete(
                route('psychologist.availabilities.destroy', $availability),
            )
            ->assertRedirect(route('psychologist.availabilities.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(
            Availability::STATUS_DISABLED,
            $availability->fresh()->status,
        );

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'availability_id' => $availability->id,
            'status' => Appointment::STATUS_CANCELLED,
        ]);
    }

    public function test_index_counts_active_appointments_and_hides_disable_action(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $availability = $this->availabilityFor($psychologist);

        $this->appointmentFor(
            $availability,
            Appointment::STATUS_CONFIRMED,
        );

        $this->actingAs($psychologist)
            ->get(route('psychologist.availabilities.index'))
            ->assertOk()
            ->assertViewHas('availabilities', function ($rows) use ($availability): bool {
                $row = $rows->getCollection()->firstWhere(
                    'id',
                    $availability->id,
                );

                return $row !== null
                    && (int) $row->active_appointments_count === 1;
            })
            ->assertSee('به دلیل وجود نوبت فعال قابل غیرفعال‌سازی نیست.')
            ->assertDontSee(
                route('psychologist.availabilities.destroy', $availability),
                false,
            );
    }
}
