<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\ClinicalRecord;
use App\Models\Payment;
use App\Models\User;
use App\Services\Booking\BookingService;
use App\Services\Booking\ManualAppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ManualAppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User $psychologist;
    private User $client;

    /**
     * این hook قبل از اجرای migrationهای RefreshDatabase اجرا می‌شود.
     * در صورت تنظیم دیتابیس غیرحافظه‌ای، تست متوقف می‌شود.
     */
    protected function beforeRefreshingDatabase(): void
    {
        if (
            ! app()->environment('testing')
            || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:'
            || ! empty(config('database.connections.sqlite.url'))
        ) {
            throw new \RuntimeException(
                'Tests require testing environment and SQLite :memory:.'
            );
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'app.timezone' => 'UTC',
            'payment.hold_minutes' => 15,
            'booking.manual_default_duration_minutes' => 45,
        ]);

        $this->travelTo(
            CarbonImmutable::parse('2030-01-10 06:00:00', 'UTC')
        );

        $this->psychologist = User::factory()->psychologist()->create();

        $this->client = User::factory()->client()->create([
            'created_by_psychologist_id' => $this->psychologist->id,
        ]);

        ClinicalRecord::query()->create([
            'client_id' => $this->client->id,
            'status' => ClinicalRecord::STATUS_ACTIVE,
        ]);
    }

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'date' => '2030-01-11',
            'time' => '12:00',
            'duration_minutes' => 60,
            'session_type' => 'online',
            'manual_payment_method' => 'paid_outside',
            'amount' => 950000,
        ], $overrides);
    }

    private function storeUrl(?User $client = null): string
    {
        return route(
            'psychologist.clinical-records.manual-appointments.store',
            $client ?? $this->client,
        );
    }

    private function recordUrl(array $query = []): string
    {
        return route('psychologist.clinical-records.show', [
            'client' => $this->client,
            ...$query,
        ]);
    }

    private function createManual(array $overrides = []): Appointment
    {
        return app(ManualAppointmentService::class)->create(
            $this->psychologist,
            $this->client,
            $this->payload($overrides),
        );
    }

    /**
     * ساخت داده زمینه، نه اجرای منطق ثبت دستی.
     */
    private function existingAppointment(array $overrides = []): Appointment
    {
        $appointment = new Appointment();

        $appointment->forceFill(array_replace([
            'psychologist_id' => $this->psychologist->id,
            'client_id' => $this->client->id,
            'availability_id' => null,
            // برابر با ۱۲ ظهر تهران
            'starts_at' => '2030-01-11 08:30:00',
            'ends_at' => '2030-01-11 09:30:00',
            'duration_minutes' => 60,
            'session_type' => 'online',
            'status' => Appointment::STATUS_CONFIRMED,
            'attendance_status' => Appointment::ATTENDANCE_PENDING,
            'amount' => 950000,
            'booking_source' => Appointment::BOOKING_SOURCE_WEBSITE,
            'therapy_session_type' => Appointment::THERAPY_TYPE_FREE,
        ], $overrides));

        $appointment->save();

        return $appointment;
    }

    private function availability(): Availability
    {
        return Availability::query()->create([
            'psychologist_id' => $this->psychologist->id,
            'starts_at' => '2030-01-11 06:30:00',
            'ends_at' => '2030-01-11 12:30:00',
            'status' => Availability::STATUS_ACTIVE,
        ]);
    }

    private function websitePayload(
        Availability $availability,
        string $time = '12:00',
    ): array {
        return [
            'availability_id' => $availability->id,
            'starts_at' => CarbonImmutable::parse(
                '2030-01-11 '.$time,
                'Asia/Tehran',
            )->toIso8601String(),
            'duration_minutes' => 60,
            'session_type' => 'online',
        ];
    }
    public function test_manual_form_accepts_jalali_date(): void
    {
        $payload = $this->payload();
        unset($payload['date']);

        $payload['date_jalali'] = \App\Support\PersianDate::format(
            CarbonImmutable::parse('2030-01-11 12:00:00', 'Asia/Tehran'),
            'yyyy/MM/dd',
        );

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect($this->recordUrl());

        $appointment = Appointment::query()
            ->where('client_id', $this->client->id)
            ->sole();

        $this->assertSame(
            '2030-01-11 08:30:00',
            $appointment->starts_at->copy()->utc()->format('Y-m-d H:i:s'),
        );
    }

    public function test_invalid_jalali_date_does_not_create_appointment(): void
    {
        $payload = $this->payload();

        // حتی با وجود تاریخ میلادی معتبر، ورودی شمسی نامعتبر نباید نادیده گرفته شود.
        $payload['date_jalali'] = '۱۴۰۵/۰۷/۳۱';

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $payload)
            ->assertSessionHasErrors('date_jalali');

        $this->assertDatabaseCount('appointments', 0);
    }

    public static function validSessions(): array
    {
        return [
            'past external online' => [
                '2030-01-09', 'paid_outside', 'online', 75, 123456,
                Appointment::ATTENDANCE_HELD,
            ],
            'future free in person' => [
                '2030-01-11', 'free', 'in_person', 30, 0,
                Appointment::ATTENDANCE_PENDING,
            ],
        ];
    }

    public static function manualAttendanceBoundaries(): array
    {
        // زمان ثابت تست: 2030-01-10، ساعت 09:30 تهران.
        return [
            'ended one minute ago' => [
                '08:29', Appointment::ATTENDANCE_HELD,
            ],
            'ends exactly now' => [
                '08:30', Appointment::ATTENDANCE_HELD,
            ],
            'still in progress' => [
                '09:00', Appointment::ATTENDANCE_PENDING,
            ],
            'starts exactly now' => [
                '09:30', Appointment::ATTENDANCE_PENDING,
            ],
            'future session' => [
                '10:00', Appointment::ATTENDANCE_PENDING,
            ],
        ];
    }

    #[DataProvider('manualAttendanceBoundaries')]
    public function test_manual_attendance_depends_on_end_time(
        string $time,
        string $expectedAttendance,
    ): void {
        $appointment = $this->createManual([
            'date' => '2030-01-10',
            'time' => $time,
            'duration_minutes' => 60,

            // ورودی دستکاری‌شده نباید تصمیم سرویس را تغییر دهد.
            'attendance_status' => $expectedAttendance
            === Appointment::ATTENDANCE_HELD
                ? Appointment::ATTENDANCE_PENDING
                : Appointment::ATTENDANCE_HELD,
        ]);

        $this->assertSame(
            $expectedAttendance,
            $appointment->fresh()->attendance_status,
        );

        $this->assertSame(
            Appointment::STATUS_CONFIRMED,
            $appointment->fresh()->status,
        );
    }

    public function test_future_manual_session_does_not_automatically_become_held(): void
    {
        $appointment = $this->createManual([
            'date' => '2030-01-11',
            'time' => '12:00',
            'duration_minutes' => 60,
        ]);

        $this->travelTo(
            CarbonImmutable::parse('2030-01-12 06:00:00', 'UTC'),
        );

        $this->assertSame(
            Appointment::ATTENDANCE_PENDING,
            $appointment->fresh()->attendance_status,
        );
    }

    #[DataProvider('validSessions')]
    public function test_manual_session_is_saved_with_safe_defaults(
        string $date,
        string $method,
        string $type,
        int $duration,
        int $expectedAmount,
    ): void {
        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload([
                'date' => $date,
                'manual_payment_method' => $method,
                'session_type' => $type,
                'duration_minutes' => $duration,
                'amount' => 123456,
            ]))
            ->assertRedirect($this->recordUrl())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('treatment_plans', 0);

        $appointment = Appointment::query()->sole();

        $this->assertSame($this->psychologist->id, $appointment->psychologist->id);
        $this->assertSame($this->client->id, $appointment->client->id);
        $this->assertSame('manual', $appointment->booking_source);
        $this->assertSame($method, $appointment->manual_payment_method);
        $this->assertSame($type, $appointment->session_type);
        $this->assertSame($duration, $appointment->duration_minutes);
        $this->assertSame($expectedAmount, $appointment->amount);
        $this->assertSame('confirmed', $appointment->status);
        $this->assertSame('pending', $appointment->attendance_status);
        $this->assertSame('free', $appointment->therapy_session_type);
        $this->assertNull($appointment->availability_id);
        $this->assertNull($appointment->hold_expires_at);
        $this->assertNull($appointment->treatment_plan_id);
        $this->assertNull($appointment->treatment_plan_stage_id);

        $expectedStart = CarbonImmutable::parse(
            $date.' 12:00',
            'Asia/Tehran',
        )->utc();

        $this->assertSame(
            $expectedStart->format('Y-m-d H:i:s'),
            $appointment->starts_at->format('Y-m-d H:i:s'),
        );
        $this->assertSame(
            $expectedStart->addMinutes($duration)->format('Y-m-d H:i:s'),
            $appointment->ends_at->format('Y-m-d H:i:s'),
        );

        $this->assertMatchesRegularExpression(
            '/^[1-9][0-9]{6}$/',
            $appointment->tracking_code,
        );
        $this->assertSame(
            $appointment->tracking_code,
            $appointment->getRouteKey(),
        );
        $this->assertFalse(
            Appointment::query()->countableForTreatment()->exists()
        );
    }

    public static function invalidInputs(): array
    {
        return [
            'invalid date' => ['date', '2030-02-30'],
            'missing date' => ['date', null],
            'invalid time' => ['time', '25:00'],
            'zero duration' => ['duration_minutes', 0],
            'too long' => ['duration_minutes', 481],
            'fractional duration' => ['duration_minutes', 45.5],
            'invalid type' => ['session_type', 'phone'],
            'invalid method' => ['manual_payment_method', 'gateway'],
            'negative amount' => ['amount', -1],
            'external zero' => ['amount', 0],
            'too large amount' => ['amount', 4294967296],
        ];
    }

    #[DataProvider('invalidInputs')]
    public function test_invalid_input_creates_nothing(
        string $field,
        mixed $value,
    ): void {
        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload([$field => $value]))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('appointments', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_protected_fields_cannot_be_injected(): void
    {
        $other = User::factory()->psychologist()->create();

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload([
                'psychologist_id' => $other->id,
                'client_id' => $other->id,
                'availability_id' => 999999,
                'status' => 'completed',
                'attendance_status' => 'held',
                'booking_source' => 'website',
                'therapy_session_type' => 'plan',
                'treatment_plan_id' => 999999,
                'treatment_plan_stage_id' => 999999,
                'tracking_code' => 'INJECTED',
            ]))
            ->assertSessionHasNoErrors();

        $appointment = Appointment::query()->sole();

        $this->assertSame($this->psychologist->id, $appointment->psychologist->id);
        $this->assertSame($this->client->id, $appointment->client->id);
        $this->assertSame('confirmed', $appointment->status);
        $this->assertSame('pending', $appointment->attendance_status);
        $this->assertSame('manual', $appointment->booking_source);
        $this->assertSame('free', $appointment->therapy_session_type);
        $this->assertNull($appointment->availability_id);
        $this->assertNull($appointment->treatment_plan_id);
        $this->assertNull($appointment->treatment_plan_stage_id);
        $this->assertNotSame('INJECTED', $appointment->tracking_code);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post($this->storeUrl(), $this->payload())
            ->assertRedirect(route('auth.login'));

        $this->assertDatabaseCount('appointments', 0);
    }

    public static function forbiddenRoles(): array
    {
        return [
            'client' => [User::ROLE_CLIENT],
            'admin' => [User::ROLE_ADMIN],
        ];
    }

    #[DataProvider('forbiddenRoles')]
    public function test_other_roles_cannot_register(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->post($this->storeUrl(), $this->payload())
            ->assertForbidden();

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_unrelated_psychologist_cannot_register_or_view(): void
    {
        $other = User::factory()->psychologist()->create();

        $this->actingAs($other)
            ->post($this->storeUrl(), $this->payload())
            ->assertNotFound();

        $this->get($this->recordUrl())->assertNotFound();

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_record_is_required(): void
    {
        $this->client->clinicalRecord()->delete();

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload())
            ->assertNotFound();

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_previous_appointment_also_grants_client_access(): void
    {
        $this->client->update(['created_by_psychologist_id' => null]);

        $this->existingAppointment([
            'starts_at' => '2030-01-08 08:30:00',
            'ends_at' => '2030-01-08 09:30:00',
        ]);

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload())
            ->assertRedirect($this->recordUrl())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('appointments', 2);
    }

    public static function overlapTimes(): array
    {
        // جلسه قبلی: ۱۲ تا ۱۳ تهران
        return [
            'same period' => ['12:00', 60],
            'overlap beginning' => ['11:30', 60],
            'overlap end' => ['12:30', 60],
            'inside existing' => ['12:15', 15],
            'contains existing' => ['11:30', 120],
        ];
    }

    #[DataProvider('overlapTimes')]
    public function test_all_overlap_shapes_are_rejected(
        string $time,
        int $duration,
    ): void {
        $this->existingAppointment();

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload([
                'time' => $time,
                'duration_minutes' => $duration,
            ]))
            ->assertSessionHasErrors('time');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('payments', 0);
    }

    public static function blockingStatuses(): array
    {
        return [
            'confirmed' => ['confirmed', null, true],
            'completed' => ['completed', null, true],
            'no show' => ['no_show', null, true],
            'active hold' => ['pending_payment', 1, true],
            'expired hold' => ['pending_payment', -1, false],
            'exact expiry' => ['pending_payment', 0, false],
            'null hold' => ['pending_payment', null, false],
            'cancelled' => ['cancelled', null, false],
        ];
    }

    #[DataProvider('blockingStatuses')]
    public function test_status_and_hold_determine_conflicts(
        string $status,
        ?int $holdOffset,
        bool $blocked,
    ): void {
        $this->existingAppointment([
            'status' => $status,
            'hold_expires_at' => $holdOffset === null
                ? null
                : CarbonImmutable::now('UTC')->addMinutes($holdOffset),
        ]);

        $response = $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload());

        if ($blocked) {
            $response->assertSessionHasErrors('time');
        } else {
            $response->assertSessionHasNoErrors()
                ->assertRedirect($this->recordUrl());
        }

        $this->assertDatabaseCount('appointments', $blocked ? 1 : 2);
    }

    public static function adjacentTimes(): array
    {
        return [
            'before' => ['11:00'],
            'after' => ['13:00'],
        ];
    }

    #[DataProvider('adjacentTimes')]
    public function test_adjacent_sessions_are_allowed(string $time): void
    {
        $this->existingAppointment();

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload(['time' => $time]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('appointments', 2);
    }

    public function test_another_psychologists_time_does_not_block(): void
    {
        $other = User::factory()->psychologist()->create();

        $this->existingAppointment(['psychologist_id' => $other->id]);

        $this->createManual();

        $this->assertDatabaseCount('appointments', 2);
    }

    public function test_double_submission_does_not_create_duplicate(): void
    {
        $this->actingAs($this->psychologist);

        $this->post($this->storeUrl(), $this->payload())
            ->assertSessionHasNoErrors();

        $this->post($this->storeUrl(), $this->payload())
            ->assertSessionHasErrors('time');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_manual_booking_blocks_website_and_slot_listing(): void
    {
        $availability = $this->availability();
        $this->createManual();

        $booking = app(BookingService::class);

        $times = array_column(
            $booking->availableSlotsForDate(
                $this->psychologist,
                '2030-01-11',
                60,
            ),
            'start_time',
        );

        $this->assertNotContains('11:30', $times);
        $this->assertNotContains('12:00', $times);
        $this->assertNotContains('12:30', $times);
        $this->assertContains('11:00', $times);
        $this->assertContains('13:00', $times);

        try {
            $booking->book(
                $this->client,
                $this->psychologist,
                $this->websitePayload($availability),
            );

            $this->fail('Website booking must reject overlapping manual session.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('starts_at', $exception->errors());
        }

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_website_booking_creates_payment_and_blocks_manual(): void
    {
        $availability = $this->availability();

        $appointment = app(BookingService::class)->book(
            $this->client,
            $this->psychologist,
            $this->websitePayload($availability),
        )->refresh();

        $this->assertSame('website', $appointment->booking_source);
        $this->assertSame('pending_payment', $appointment->status);
        $this->assertNull($appointment->manual_payment_method);
        $this->assertSame(950000, $appointment->amount);
        $this->assertTrue(
            $appointment->hold_expires_at->equalTo(now()->addMinutes(15))
        );

        $payment = $appointment->payments()->sole();

        $this->assertSame(Payment::STATUS_INITIATED, $payment->status);
        $this->assertSame(Payment::GATEWAY_ZARINPAL, $payment->gateway);
        $this->assertSame($appointment->amount, $payment->amount);

        $this->actingAs($this->psychologist)
            ->post($this->storeUrl(), $this->payload())
            ->assertSessionHasErrors('time');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_website_booking_accepts_adjacent_manual_session(): void
    {
        $availability = $this->availability();
        $this->createManual();

        $appointment = app(BookingService::class)->book(
            $this->client,
            $this->psychologist,
            $this->websitePayload($availability, '13:00'),
        );

        $this->assertSame('pending_payment', $appointment->status);
        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_tracking_code_is_unique_in_sample_and_stable_on_update(): void
    {
        $first = $this->createManual();
        $second = $this->createManual(['time' => '13:00']);

        $id = $first->id;
        $code = $first->tracking_code;

        $this->assertNotSame($code, $second->tracking_code);

        $first->meeting_url = 'https://example.com/meeting';
        $first->save();
        $first->refresh();

        $this->assertSame($id, $first->id);
        $this->assertSame($code, $first->tracking_code);
    }

    public function test_record_filters_and_paginates_sessions(): void
    {
        $visible = collect();

        for ($i = 0; $i < 12; $i++) {
            $visible->push($this->existingAppointment([
                'status' => [
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_COMPLETED,
                    Appointment::STATUS_NO_SHOW,
                ][$i % 3],
            ]));
        }

        $hidden = collect([
            $this->existingAppointment(['status' => 'cancelled']),
            $this->existingAppointment([
                'status' => 'pending_payment',
                'hold_expires_at' => now()->addMinutes(15),
            ]),
            $this->existingAppointment([
                'psychologist_id' => User::factory()->psychologist()->create()->id,
            ]),
            $this->existingAppointment([
                'client_id' => User::factory()->client()->create()->id,
            ]),
        ]);

        // زمان‌ها برابرند؛ ترتیب id نزولی باید پایدار باشد.
        $expectedIds = $visible->pluck('id')->sortDesc()->values();

        $firstPage = $this->actingAs($this->psychologist)
            ->get($this->recordUrl())
            ->assertOk()
            ->assertViewHas('manualDefaultDuration', 45)
            ->assertViewHas('manualDefaultAmount', 760000)
            ->assertViewHas(
                'recordAppointments',
                fn ($page) => $page instanceof LengthAwarePaginator
                    && $page->total() === 12
                    && $page->count() === 10
                    && $page->getCollection()->modelKeys()
                    === $expectedIds->take(10)->all(),
            );

        foreach ($hidden as $appointment) {
            $firstPage->assertDontSeeText($appointment->tracking_code);
        }

        $secondPage = $this->get($this->recordUrl([
            'appointments_page' => 2,
        ]))
            ->assertOk()
            ->assertViewHas(
                'recordAppointments',
                fn ($page) => $page->currentPage() === 2
                    && $page->count() === 2
                    && $page->getCollection()->modelKeys()
                    === $expectedIds->slice(10)->values()->all(),
            );

        foreach ($visible->whereIn('id', $expectedIds->slice(10)->all()) as $appointment) {
            $secondPage->assertSeeText($appointment->tracking_code);
        }
    }

    public function test_manual_session_is_visible_in_record(): void
    {
        $appointment = $this->createManual();

        $this->actingAs($this->psychologist)
            ->get($this->recordUrl())
            ->assertOk()
            ->assertSeeText($appointment->tracking_code)
            ->assertSeeText('ثبت دستی مشاور')
            ->assertSeeText('پرداخت‌شده خارج از سایت')
            ->assertSee(
                route('psychologist.appointments.session.show', $appointment),
                false,
            );
    }
}
