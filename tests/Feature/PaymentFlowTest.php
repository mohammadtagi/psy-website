<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'services.zarinpal.merchant_id' => 'TEST-MERCHANT',
            'services.zarinpal.request_url' =>
                'https://example.test/payment/request',
            'services.zarinpal.verify_url' =>
                'https://example.test/payment/verify',
            'services.zarinpal.start_url' =>
                'https://example.test/StartPay',
            'services.zarinpal.callback_url' =>
                'https://example.test/payments/zarinpal/callback',
        ]);
    }

    public function test_client_can_start_payment_for_an_active_pending_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $appointment = $this->createPendingAppointment(
            client: $client,
            startsAt: $now->addHours(2),
            holdExpiresAt: $now->addMinutes(15),
        );

        $payment = Payment::factory()
            ->forAppointment($appointment)
            ->create();

        Http::fake([
            'https://example.test/payment/request' => Http::response([
                'data' => [
                    'code' => 100,
                    'authority' => 'AUTH-START-001',
                ],
            ]),
        ]);

        $response = $this
            ->actingAs($client)
            ->post(route('payments.pay', $appointment));

        $response->assertRedirect(
            'https://example.test/StartPay/AUTH-START-001'
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'authority' => 'AUTH-START-001',
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function test_existing_authority_is_reused_without_requesting_a_new_payment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $appointment = $this->createPendingAppointment(
            client: $client,
            startsAt: $now->addHours(2),
            holdExpiresAt: $now->addMinutes(15),
        );

        Payment::factory()
            ->forAppointment($appointment)
            ->pending('AUTH-EXISTING-001')
            ->create();

        Http::fake();

        $response = $this
            ->actingAs($client)
            ->post(route('payments.pay', $appointment));

        $response->assertRedirect(
            'https://example.test/StartPay/AUTH-EXISTING-001'
        );

        Http::assertNothingSent();
    }

    public function test_client_cannot_start_payment_after_hold_expiration(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $appointment = $this->createPendingAppointment(
            client: $client,
            startsAt: $now->addHours(2),
            holdExpiresAt: $now->subMinute(),
        );

        Payment::factory()
            ->forAppointment($appointment)
            ->create();

        Http::fake();

        $response = $this
            ->actingAs($client)
            ->post(route('payments.pay', $appointment));

        $response
            ->assertRedirect()
            ->assertSessionHasErrors([
                'payment' => 'مهلت پرداخت این نوبت به پایان رسیده است.',
            ]);

        Http::assertNothingSent();
    }

    public function test_client_cannot_start_payment_for_another_clients_appointment(): void
    {
        $owner = User::factory()->client()->create();
        $anotherClient = User::factory()->client()->create();

        $appointment = $this->createPendingAppointment(
            client: $owner,
            startsAt: CarbonImmutable::now('UTC')->addHours(2),
            holdExpiresAt: CarbonImmutable::now('UTC')->addMinutes(15),
        );

        Payment::factory()
            ->forAppointment($appointment)
            ->create();

        $response = $this
            ->actingAs($anotherClient)
            ->post(route('payments.pay', $appointment));

        $response->assertForbidden();
    }

    public function test_successful_callback_pays_payment_and_confirms_appointment(): void
    {
        $now = CarbonImmutable::parse('2026-09-22 09:00:00', 'UTC');
        CarbonImmutable::setTestNow($now);

        $client = User::factory()->client()->create();
        $appointment = $this->createPendingAppointment(
            client: $client,
            startsAt: $now->addHours(2),
            holdExpiresAt: $now->addMinutes(15),
        );

        Payment::factory()
            ->forAppointment($appointment)
            ->pending('AUTH-SUCCESS-001')
            ->create();

        Http::fake([
            'https://example.test/payment/verify' => Http::response([
                'data' => [
                    'code' => 100,
                    'ref_id' => 'REF-001',
                ],
            ]),
        ]);

        $response = $this->get(route('payments.zarinpal.callback', [
            'Authority' => 'AUTH-SUCCESS-001',
            'Status' => 'OK',
        ]));

        $response
            ->assertRedirect(route(
                'booking.confirmation',
                $appointment,
            ))
            ->assertSessionHas(
                'status',
                'پرداخت با موفقیت انجام شد و نوبت تأیید شد.',
            );

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'status' => Payment::STATUS_PAID,
            'authority' => 'AUTH-SUCCESS-001',
            'ref_id' => 'REF-001',
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
            'hold_expires_at' => null,
        ]);
    }

    public function test_cancelled_gateway_payment_is_stored_as_cancelled(): void
    {
        $client = User::factory()->client()->create();
        $appointment = $this->createPendingAppointment(
            client: $client,
            startsAt: CarbonImmutable::now('UTC')->addHours(2),
            holdExpiresAt: CarbonImmutable::now('UTC')->addMinutes(15),
        );

        Payment::factory()
            ->forAppointment($appointment)
            ->pending('AUTH-CANCELLED-001')
            ->create();

        Http::fake();

        $response = $this->get(route('payments.zarinpal.callback', [
            'Authority' => 'AUTH-CANCELLED-001',
            'Status' => 'Cancel',
        ]));

        $response
            ->assertRedirect(route(
                'booking.confirmation',
                $appointment,
            ))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'status' => Payment::STATUS_CANCELLED,
            'gateway_message' => 'پرداخت توسط کاربر لغو شد.',
        ]);

        Http::assertNothingSent();
    }

    public function test_paid_payment_is_not_verified_again(): void
    {
        $client = User::factory()->client()->create();
        $appointment = Appointment::factory()
            ->forClient($client)
            ->confirmed()
            ->create();

        Payment::factory()
            ->forAppointment($appointment)
            ->paid('AUTH-PAID-001')
            ->create();

        Http::fake();

        $response = $this->get(route('payments.zarinpal.callback', [
            'Authority' => 'AUTH-PAID-001',
            'Status' => 'OK',
        ]));

        $response
            ->assertRedirect(route(
                'booking.confirmation',
                $appointment,
            ))
            ->assertSessionHas(
                'status',
                'این پرداخت قبلاً با موفقیت ثبت شده است.',
            );

        Http::assertNothingSent();
    }

    public function test_failed_payment_is_not_verified_again(): void
    {
        $client = User::factory()->client()->create();
        $appointment = Appointment::factory()
            ->forClient($client)
            ->pendingPayment()
            ->create();

        Payment::factory()
            ->forAppointment($appointment)
            ->failed('AUTH-FAILED-001')
            ->create();

        Http::fake();

        $response = $this->get(route('payments.zarinpal.callback', [
            'Authority' => 'AUTH-FAILED-001',
            'Status' => 'OK',
        ]));

        $response
            ->assertRedirect(route(
                'booking.confirmation',
                $appointment,
            ))
            ->assertSessionHasErrors([
                'payment' => 'این تراکنش قبلاً تعیین تکلیف شده است.',
            ]);

        Http::assertNothingSent();
    }

    public function test_callback_without_authority_is_rejected(): void
    {
        $response = $this->get(route('payments.zarinpal.callback'));

        $response
            ->assertRedirect(route('booking.index'))
            ->assertSessionHasErrors([
                'payment' => 'اطلاعات بازگشت از درگاه ناقص است.',
            ]);
    }

    public function test_callback_with_unknown_authority_is_rejected(): void
    {
        $response = $this->get(route('payments.zarinpal.callback', [
            'Authority' => 'UNKNOWN-AUTHORITY',
            'Status' => 'OK',
        ]));

        $response
            ->assertRedirect(route('booking.index'))
            ->assertSessionHasErrors([
                'payment' => 'رکورد پرداخت پیدا نشد.',
            ]);
    }

    private function createPendingAppointment(
        User $client,
        CarbonImmutable $startsAt,
        CarbonImmutable $holdExpiresAt,
    ): Appointment {
        $psychologist = User::factory()->psychologist()->create();

        $availability = Availability::factory()
            ->forPsychologist($psychologist)
            ->from($startsAt)
            ->create();

        return Appointment::factory()
            ->forClient($client)
            ->forPsychologist($psychologist)
            ->usingAvailability($availability)
            ->at($startsAt)
            ->pendingPayment()
            ->state([
                'hold_expires_at' => $holdExpiresAt,
            ])
            ->create();
    }
}
