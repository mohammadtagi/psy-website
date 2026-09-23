<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use App\Services\Booking\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
        use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
                parent::setUp();

                $this->service = app(AvailabilityService::class);
            }

    protected function tearDown(): void
    {
                CarbonImmutable::setTestNow();

                parent::tearDown();
            }

    public function test_it_creates_a_single_availability_for_a_date(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();

                $created = $this->service->createForDate(
                        psychologist: $psychologist,
            date: '2026-09-24',
            startTime: '10:00',
            endTime: '12:00',
        );

        $this->assertCount(1, $created);

        $availability = $created->first();

        $this->assertSame(
                $psychologist->id,
                $availability->psychologist_id,
            );
        $this->assertSame(
                Availability::STATUS_ACTIVE,
                $availability->status,
            );
        $this->assertNull($availability->series_key);
        $this->assertSame(
                '2026-09-24 06:30:00',
                $availability->starts_at->utc()->format('Y-m-d H:i:s'),
            );
        $this->assertSame(
                '2026-09-24 08:30:00',
                $availability->ends_at->utc()->format('Y-m-d H:i:s'),
            );
    }

    public function test_it_creates_only_matching_weekdays_in_a_date_range(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-20 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();

                $created = $this->service->createForDateRange(
                        psychologist: $psychologist,
            fromDate: '2026-09-21',
            toDate: '2026-09-27',
            weekdays: [1, 3, 5],
            startTime: '09:00',
            endTime: '11:00',
        );

        $this->assertCount(3, $created);
        $this->assertCount(1, $created->pluck('series_key')->unique());
        $this->assertNotNull($created->first()->series_key);

        $this->assertSame(
                [
                        '2026-09-21',
                        '2026-09-23',
                        '2026-09-25',
                    ],
                $created
                    ->map(fn (Availability $availability): string => $availability
                        ->starts_at
                    ->setTimezone('Asia/Tehran')
                            ->format('Y-m-d'))
                ->values()
                        ->all(),
        );
    }

    public function test_it_rejects_a_start_date_before_today(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDate(
                        User::factory()->psychologist()->create(),
                        '2026-09-21',
                        '10:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_a_date_range_longer_than_366_days(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDateRange(
                        User::factory()->psychologist()->create(),
                        '2026-09-22',
                        '2027-09-23',
                        [1],
                        '10:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_an_end_time_before_or_equal_to_start_time(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDate(
                        User::factory()->psychologist()->create(),
                        '2026-09-24',
                        '12:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_a_period_shorter_than_a_valid_45_minute_slot(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDate(
                        User::factory()->psychologist()->create(),
                        '2026-09-24',
                        '10:01',
                        '10:45',
                    );
            }

    public function test_it_allows_availability_boundaries_on_30_minute_intervals(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
        );

        $created = $this->service->createForDate(
            User::factory()->psychologist()->create(),
            '2026-09-24',
            '10:00',
            '11:30',
        );

        $this->assertCount(1, $created);
    }


    public function test_it_rejects_an_invalid_weekday(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-20 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDateRange(
                        User::factory()->psychologist()->create(),
                        '2026-09-21',
                        '2026-09-27',
                        [7],
                        '10:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_duplicate_weekdays(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-20 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDateRange(
                        User::factory()->psychologist()->create(),
                        '2026-09-21',
                        '2026-09-27',
                        [1, 1],
                        '10:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_a_recurring_range_with_no_matching_weekday(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-20 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDateRange(
                        User::factory()->psychologist()->create(),
                        '2026-09-21',
                        '2026-09-21',
                        [0],
                        '10:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_a_period_that_has_already_started(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-24 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDate(
                        User::factory()->psychologist()->create(),
                        '2026-09-24',
                        '09:00',
                        '12:00',
                    );
            }

    public function test_it_rejects_an_overlapping_availability_even_when_existing_row_is_disabled(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();

                Availability::factory()
                    ->forPsychologist($psychologist)
                    ->from(
                            CarbonImmutable::parse(
                                    '2026-09-24 10:00:00',
                                    'Asia/Tehran',
                                )->utc(),
                        )
                    ->disabled()
                    ->create([
                            'ends_at' => CarbonImmutable::parse(
                                    '2026-09-24 12:00:00',
                                    'Asia/Tehran',
                                )->utc(),
                        ]);

        $this->expectException(ValidationException::class);

        $this->service->createForDate(
                $psychologist,
                '2026-09-24',
                '11:00',
                '13:00',
            );
    }

    public function test_it_rejects_overlap_with_a_confirmed_appointment(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();
                $client = User::factory()->client()->create();

                Appointment::factory()
                    ->forClient($client)
                    ->forPsychologist($psychologist)
                    ->at(
                            CarbonImmutable::parse(
                                    '2026-09-24 10:00:00',
                                    'Asia/Tehran',
                                )->utc(),
                        )
                    ->create([
                            'status' => Appointment::STATUS_CONFIRMED,
                        ]);

        $this->expectException(ValidationException::class);

        $this->service->createForDate(
                $psychologist,
                '2026-09-24',
                '10:30',
                '12:00',
            );
    }

    public function test_it_rejects_overlap_with_an_unexpired_pending_payment(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();
                $client = User::factory()->client()->create();

                $startsAt = CarbonImmutable::parse(
                        '2026-09-24 10:00:00',
                        'Asia/Tehran',
                    )->utc();

                Appointment::factory()
                    ->forClient($client)
                    ->forPsychologist($psychologist)
                    ->at($startsAt)
                    ->create([
                            'status' => Appointment::STATUS_PENDING_PAYMENT,
                            'hold_expires_at' => CarbonImmutable::now('UTC')->addMinutes(15),
                        ]);

        $this->expectException(ValidationException::class);

        $this->service->createForDate(
                $psychologist,
                '2026-09-24',
                '10:00',
                '12:00',
            );
    }

    public function test_it_ignores_an_expired_pending_payment_when_checking_conflicts(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();
                $client = User::factory()->client()->create();

                $startsAt = CarbonImmutable::parse(
                        '2026-09-24 10:00:00',
                        'Asia/Tehran',
                    )->utc();

                Appointment::factory()
                    ->forClient($client)
                    ->forPsychologist($psychologist)
                    ->at($startsAt)
                    ->create([
                            'status' => Appointment::STATUS_PENDING_PAYMENT,
                            'hold_expires_at' => CarbonImmutable::now('UTC')->subMinute(),
                        ]);

        $created = $this->service->createForDate(
                $psychologist,
                '2026-09-24',
                '10:00',
                '12:00',
            );

        $this->assertCount(1, $created);
    }

    public function test_it_ignores_cancelled_appointments_when_checking_conflicts(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $psychologist = User::factory()->psychologist()->create();
                $client = User::factory()->client()->create();

                Appointment::factory()
                    ->forClient($client)
                    ->forPsychologist($psychologist)
                    ->cancelled()
                    ->at(
                            CarbonImmutable::parse(
                                    '2026-09-24 10:00:00',
                                    'Asia/Tehran',
                                )->utc(),
                        )
                    ->create();

        $created = $this->service->createForDate(
                $psychologist,
                '2026-09-24',
                '10:00',
                '12:00',
            );

        $this->assertCount(1, $created);
    }

    public function test_it_rejects_creation_for_a_non_psychologist_user(): void
    {
                CarbonImmutable::setTestNow(
                        CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
                    );

                $this->expectException(ValidationException::class);

                $this->service->createForDate(
                        User::factory()->client()->create(),
                        '2026-09-24',
                        '10:00',
                        '12:00',
                    );
            }
}
