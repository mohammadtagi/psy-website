<?php

namespace Tests\Feature\Booking;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use App\Services\Booking\BookingService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AvailableSlotsForDateTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    private User $psychologist;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.timezone' => 'UTC']);

        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-09-22 06:00:00', 'UTC'),
        );

        $this->service = app(BookingService::class);
        $this->psychologist = User::factory()
            ->psychologist()
            ->create();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    #[DataProvider('durationCases')]
    public function test_generates_slots_with_correct_duration_price_and_timezone(
        int $duration,
        int $amount,
        string $firstEnd,
    ): void {
        $availability = $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 12:00:00',
        );

        $slots = $this->slots(duration: $duration);

        $this->assertSame(
            ['10:00', '10:30', '11:00'],
            array_column($slots, 'start_time'),
        );

        $this->assertSame($firstEnd, $slots[0]['end_time']);

        $this->assertSame(
            '2026-09-24 06:30:00',
            $slots[0]['starts_at']->format('Y-m-d H:i:s'),
        );

        foreach ($slots as $slot) {
            $this->assertSame(
                $availability->id,
                $slot['availability_id'],
            );
            $this->assertSame($duration, $slot['duration_minutes']);
            $this->assertSame($amount, $slot['amount']);

            $this->assertInstanceOf(
                CarbonImmutable::class,
                $slot['starts_at'],
            );
            $this->assertInstanceOf(
                CarbonImmutable::class,
                $slot['ends_at'],
            );

            $this->assertSame(0, $slot['starts_at']->getOffset());
            $this->assertSame(0, $slot['ends_at']->getOffset());

            $this->assertTrue(
                $slot['ends_at']->equalTo(
                    $slot['starts_at']->addMinutes($duration),
                ),
            );

            $this->assertTrue(
                $slot['ends_at']->lte($availability->ends_at),
            );
        }
    }

    public static function durationCases(): array
    {
        return [
            '45 minutes' => [45, 760000, '10:45'],
            '60 minutes' => [60, 950000, '11:00'],
        ];
    }

    #[DataProvider('roundingCases')]
    public function test_rounds_start_up_without_leaving_availability(
        string $start,
        array $expected,
    ): void {
        $this->availability(
            "2026-09-24 {$start}",
            '2026-09-24 12:00:00',
        );

        $this->assertSame(
            $expected,
            array_column($this->slots(), 'start_time'),
        );
    }

    public static function roundingCases(): array
    {
        return [
            'aligned' => [
                '10:00:00',
                ['10:00', '10:30', '11:00'],
            ],
            'unaligned minutes' => [
                '10:10:00',
                ['10:30', '11:00'],
            ],
            'one second after hour' => [
                '10:00:01',
                ['10:30', '11:00'],
            ],
            'one second after half hour' => [
                '10:30:01',
                ['11:00'],
            ],
        ];
    }

    #[DataProvider('availabilityLengthCases')]
    public function test_session_must_fit_entirely_inside_availability(
        string $end,
        array $expected,
    ): void {
        $this->availability(
            '2026-09-24 10:00:00',
            "2026-09-24 {$end}",
        );

        $this->assertSame(
            $expected,
            array_column($this->slots(), 'start_time'),
        );
    }

    public static function availabilityLengthCases(): array
    {
        return [
            'exact fit' => ['10:45:00', ['10:00']],
            'one second too short' => ['10:44:59', []],
        ];
    }

    public function test_returns_empty_without_availability(): void
    {
        $this->assertSame([], $this->slots());
    }

    public function test_ignores_disabled_and_other_psychologist_availabilities(): void
    {
        $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 12:00:00',
            status: Availability::STATUS_DISABLED,
        );

        $other = User::factory()->psychologist()->create();

        $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 12:00:00',
            psychologist: $other,
        );

        $this->assertSame([], $this->slots());
    }

    public function test_returns_empty_for_a_past_local_date(): void
    {
        $this->availability(
            '2026-09-21 10:00:00',
            '2026-09-21 12:00:00',
        );

        $this->assertSame([], $this->slots('2026-09-21'));
    }

    public function test_filters_elapsed_slots_on_the_current_day(): void
    {
        $this->freezeLocal('2026-09-24 10:00:00');

        $this->availability(
            '2026-09-24 09:00:00',
            '2026-09-24 12:00:00',
        );

        $this->assertSame(
            ['10:30', '11:00'],
            array_column($this->slots(), 'start_time'),
        );
    }

    #[DataProvider('cutoffCases')]
    public function test_one_minute_booking_boundary(
        string $now,
        array $expected,
    ): void {
        $this->freezeLocal("2026-09-24 {$now}");

        $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 10:45:00',
        );

        $this->assertSame(
            $expected,
            array_column($this->slots(), 'start_time'),
        );
    }

    public static function cutoffCases(): array
    {
        return [
            '61 seconds remaining' => ['09:58:59', ['10:00']],
            'exactly 60 seconds remaining' => ['09:59:00', ['10:00']],
            '59 seconds remaining' => ['09:59:01', []],
            'already starting' => ['10:00:00', []],
        ];
    }

    #[DataProvider('appointmentStatusCases')]
    public function test_appointment_status_and_hold_control_slot_blocking(
        string $status,
        ?int $holdSeconds,
        bool $blocks,
    ): void {
        $availability = $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 13:00:00',
        );

        $this->appointment(
            $availability,
            '2026-09-24 11:00:00',
            status: $status,
            holdExpiresAt: $holdSeconds === null
                ? null
                : CarbonImmutable::now('UTC')->addSeconds($holdSeconds),
        );

        $this->assertSame(
            $blocks
                ? ['10:00', '12:00']
                : ['10:00', '10:30', '11:00', '11:30', '12:00'],
            array_column($this->slots(), 'start_time'),
        );
    }

    public static function appointmentStatusCases(): array
    {
        return [
            'confirmed' => [
                Appointment::STATUS_CONFIRMED, null, true,
            ],
            'completed' => [
                Appointment::STATUS_COMPLETED, null, true,
            ],
            'no show' => [
                Appointment::STATUS_NO_SHOW, null, true,
            ],
            'cancelled' => [
                Appointment::STATUS_CANCELLED, null, false,
            ],
            'active hold' => [
                Appointment::STATUS_PENDING_PAYMENT, 60, true,
            ],
            'expired hold' => [
                Appointment::STATUS_PENDING_PAYMENT, -1, false,
            ],
            'hold expires exactly now' => [
                Appointment::STATUS_PENDING_PAYMENT, 0, false,
            ],
            'hold without expiry' => [
                Appointment::STATUS_PENDING_PAYMENT, null, false,
            ],
        ];
    }

    public function test_touching_appointment_boundaries_are_not_overlaps(): void
    {
        $availability = $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 13:00:00',
        );

        $this->appointment(
            $availability,
            '2026-09-24 11:00:00',
            duration: 60,
        );

        $this->assertSame(
            ['10:00', '12:00'],
            array_column(
                $this->slots(duration: 60),
                'start_time',
            ),
        );
    }

    public function test_other_psychologist_appointment_does_not_block_slots(): void
    {
        $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 11:00:00',
        );

        $other = User::factory()->psychologist()->create();

        $otherAvailability = $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 11:00:00',
            psychologist: $other,
        );

        $this->appointment(
            $otherAvailability,
            '2026-09-24 10:00:00',
            duration: 60,
        );

        $this->assertSame(
            ['10:00'],
            array_column($this->slots(), 'start_time'),
        );
    }

    public function test_overlapping_availabilities_do_not_duplicate_slots(): void
    {
        $this->availability(
            '2026-09-24 10:00:00',
            '2026-09-24 12:00:00',
        );

        $this->availability(
            '2026-09-24 10:30:00',
            '2026-09-24 12:30:00',
        );

        $slots = $this->slots();

        // اسلات‌های یکتا و بدون تکرار:
        $this->assertSame(
            ['10:00', '10:30', '11:00', '11:30'],
            array_values(array_column($slots, 'start_time')),
        );

        foreach ($slots as $slot) {
            $availability = Availability::findOrFail(
                $slot['availability_id'],
            );

            $this->assertTrue(
                $slot['starts_at']->gte($availability->starts_at),
            );
            $this->assertTrue(
                $slot['ends_at']->lte($availability->ends_at),
            );
        }
    }


    public function test_returns_only_starts_on_the_requested_local_date(): void
    {
        $this->availability(
            '2026-09-23 23:00:00',
            '2026-09-24 01:00:00',
        );

        $this->availability(
            '2026-09-24 23:00:00',
            '2026-09-25 01:00:00',
        );

        $slots = $this->slots();

        $this->assertSame(
            ['00:00', '23:00', '23:30'],
            array_column($slots, 'start_time'),
        );

        foreach ($slots as $slot) {
            $this->assertSame(
                '2026-09-24',
                $slot['starts_at']
                    ->setTimezone('Asia/Tehran')
                    ->format('Y-m-d'),
            );
        }

        // Local midnight belongs to the previous UTC date.
        $this->assertSame(
            '2026-09-23 20:30:00',
            $slots[0]['starts_at']->format('Y-m-d H:i:s'),
        );
    }

    public function test_next_day_appointment_blocks_a_cross_midnight_session(): void
    {
        $availability = $this->availability(
            '2026-09-24 23:00:00',
            '2026-09-25 01:00:00',
        );

        $this->appointment(
            $availability,
            '2026-09-25 00:00:00',
        );

        $this->assertSame(
            ['23:00'],
            array_column($this->slots(), 'start_time'),
        );
    }

    public function test_previous_day_appointment_blocks_early_morning_slot(): void
    {
        $availability = $this->availability(
            '2026-09-23 23:00:00',
            '2026-09-24 02:00:00',
        );

        $this->appointment(
            $availability,
            '2026-09-23 23:30:00',
            duration: 60,
        );

        $this->assertSame(
            ['00:30', '01:00'],
            array_column($this->slots(), 'start_time'),
        );
    }

    #[DataProvider('invalidInputCases')]
    public function test_rejects_invalid_input(
        string $date,
        int $duration,
        string $errorKey,
    ): void {
        try {
            $this->slots($date, $duration);
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                $errorKey,
                $exception->errors(),
            );

            return;
        }

        $this->fail('Expected a validation exception.');
    }

    public static function invalidInputCases(): array
    {
        return [
            'invalid duration' => [
                '2026-09-24', 30, 'duration_minutes',
            ],
            'invalid text' => [
                'not-a-date', 45, 'date',
            ],
            'invalid day' => [
                '2026-02-30', 45, 'date',
            ],
            'invalid month' => [
                '2026-13-01', 45, 'date',
            ],
            'noncanonical format' => [
                '2026-9-24', 45, 'date',
            ],
        ];
    }

    private function local(string $dateTime): CarbonImmutable
    {
        return CarbonImmutable::parse(
            $dateTime,
            'Asia/Tehran',
        )->utc();
    }

    private function freezeLocal(string $dateTime): void
    {
        CarbonImmutable::setTestNow($this->local($dateTime));
    }

    private function availability(
        string $start,
        string $end,
        ?User $psychologist = null,
        string $status = Availability::STATUS_ACTIVE,
    ): Availability {
        return Availability::factory()
            ->forPsychologist($psychologist ?? $this->psychologist)
            ->create([
                'starts_at' => $this->local($start),
                'ends_at' => $this->local($end),
                'status' => $status,
            ]);
    }

    private function appointment(
        Availability $availability,
        string $start,
        int $duration = 45,
        string $status = Appointment::STATUS_CONFIRMED,
        ?CarbonImmutable $holdExpiresAt = null,
    ): Appointment {
        return Appointment::factory()
            ->usingAvailability($availability)
            ->create([
                'starts_at' => $this->local($start),
                'ends_at' => $this->local($start)->addMinutes($duration),
                'duration_minutes' => $duration,
                'amount' => $duration === 60 ? 950000 : 760000,
                'status' => $status,
                'hold_expires_at' => $holdExpiresAt,
            ]);
    }

    private function slots(
        string $date = '2026-09-24',
        int $duration = 45,
    ): array {
        return $this->service->availableSlotsForDate(
            $this->psychologist,
            $date,
            $duration,
        );
    }
}
