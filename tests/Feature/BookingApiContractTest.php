<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\Booking\BookingService;
use App\Services\MelipayamakService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;

/** HTTP contract tests, not payment/concurrency tests. Never runs project migrations. */
class BookingApiContractTest extends TestCase
{
    public function createApplication()
    {
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->afterBootstrapping(LoadConfiguration::class, function ($app): void {
            // Override before providers boot; never use the deployment database/cache.
            $app['config']->set([
                'app.env' => 'testing',
                'app.key' => 'base64:'.base64_encode(str_repeat('x', 32)),
                'database.default' => 'booking_api_test',
                'database.connections' => ['booking_api_test' => [
                    'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
                    'foreign_key_constraints' => true,
                ]],
                'cache.default' => 'array',
                'cache.stores.array' => ['driver' => 'array', 'serialize' => false],
                'session.driver' => 'array',
                'queue.default' => 'sync',
                'services.otp.length' => 6,
                'services.otp.resend_seconds' => 60,
                'services.otp.expires_in' => 2,
                'services.otp.max_attempts' => 5,
            ]);
            $app->detectEnvironment(fn () => 'testing');
        });
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        $this->assertSame(':memory:', DB::connection()->getConfig('database'));
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            foreach (['name', 'first_name', 'last_name', 'mobile', 'email', 'password', 'role', 'remember_token'] as $column) {
                $table->string($column)->nullable();
            }
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });
        Schema::create('otp_codes', function (Blueprint $table): void {
            $table->id();
            $table->string('mobile');
            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
        });
        Schema::create('availabilities', fn (Blueprint $table) => $table->id());
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id');
        });
        // Unexpected SMS calls fail the test rather than contacting the provider.
        $this->instance(MelipayamakService::class, Mockery::mock(MelipayamakService::class));
    }

    private function client(bool $complete = true): User
    {
        return User::query()->create([
            'mobile' => '09123456789', 'role' => User::ROLE_CLIENT,
            'first_name' => $complete ? 'علی' : null,
            'last_name' => $complete ? 'رضایی' : null,
            'birth_date' => $complete ? '1996-09-05' : null,
        ]);
    }

    private function personal(): array
    {
        return ['first_name' => 'سارا', 'last_name' => 'احمدی', 'birth_date_jalali' => '۱۳۷۵/۰۶/۱۵'];
    }

    private function booking(): array
    {
        DB::table('availabilities')->insert(['id' => 1]);
        User::query()->create(['mobile' => '09120000000', 'role' => User::ROLE_PSYCHOLOGIST]);

        return ['availability_id' => 1, 'start_time' => '09:15', 'duration_minutes' => 45, 'session_type' => 'online'];
    }

    public function test_guest_status_and_update_authentication(): void
    {
        $this->getJson('/booking/account')->assertOk()->assertExactJson([
            'authenticated' => false, 'user' => null, 'profile_complete' => false,
        ])->assertHeader('Cache-Control', 'no-store, private');
        $this->putJson('/booking/account', $this->personal())->assertUnauthorized();
    }

    public function test_account_update_allowlist_and_persian_date(): void
    {
        $user = $this->client(false);
        $this->actingAs($user)->putJson('/booking/account', $this->personal() + [
            'role' => 'admin', 'mobile' => '09129999999', 'password' => 'malicious',
        ])->assertOk()->assertJsonPath('profile_complete', true)
            ->assertJsonPath('user.role', 'client')->assertJsonMissingPath('user.password');
        $user->refresh();
        $this->assertSame('09123456789', $user->mobile);
        $this->assertNull($user->password);
        $this->assertSame('1996-09-05', $user->birth_date->format('Y-m-d'));
        $data = $this->personal();
        $data['birth_date_jalali'] = '1375/06/15';
        $this->putJson('/booking/account', $data)->assertOk();
    }

    public function test_non_client_cannot_update_account(): void
    {
        $user = $this->client();
        $user->update(['role' => User::ROLE_PSYCHOLOGIST]);
        $this->actingAs($user)->putJson('/booking/account', $this->personal())->assertForbidden();
    }

    public function test_invalid_and_future_dates_do_not_write_profile(): void
    {
        $user = $this->client();
        $this->actingAs($user);
        foreach (['1400/12/30', '1500/01/01', '1375/13/01'] as $date) {
            $data = $this->personal();
            $data['birth_date_jalali'] = $date;
            $this->putJson('/booking/account', $data)->assertUnprocessable()
                ->assertJsonValidationErrors('birth_date_jalali');
        }
        $this->assertSame('علی', $user->fresh()->first_name);
        $this->putJson('/booking/account', ['first_name' => ' '])->assertUnprocessable();
    }

    public function test_booking_uses_saved_account_and_returns_201(): void
    {
        $user = $this->client();
        $data = $this->booking();
        $appointment = new Appointment();
        $appointment->forceFill(['id' => 77]);
        $this->mock(BookingService::class)->shouldReceive('book')->once()
            ->withArgs(fn ($client, $psychologist, $data) => $client->id === $user->id
                && $client->first_name === 'علی' && $data['duration_minutes'] === 45)
            ->andReturn($appointment);
        $this->actingAs($user)->postJson('/booking/2030-01-01', $data)
            ->assertCreated()->assertJsonPath('appointment_id', 77)
            ->assertJsonPath('confirmation_url', route('booking.confirmation', 77));
    }

    public function test_legacy_form_saves_personal_fields_and_redirects_even_with_xhr_header(): void
    {
        $user = $this->client(false);
        $data = $this->booking() + $this->personal();
        $appointment = new Appointment();
        $appointment->forceFill(['id' => 77]);
        $this->mock(BookingService::class)->shouldReceive('book')->once()->andReturn($appointment);
        $this->actingAs($user)->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post('/booking/2030-01-01', $data)->assertRedirect(route('booking.confirmation', 77));
        $this->assertSame('سارا', $user->fresh()->first_name);
    }

    public function test_incomplete_profile_and_partial_personal_fields_are_rejected(): void
    {
        $user = $this->client(false);
        $data = $this->booking();
        $this->mock(BookingService::class)->shouldNotReceive('book');
        $this->actingAs($user)->postJson('/booking/2030-01-01', $data)
            ->assertUnprocessable()->assertJsonValidationErrors('profile');
        $this->postJson('/booking/2030-01-01', $data + ['first_name' => 'علی'])
            ->assertUnprocessable()->assertJsonValidationErrors(['last_name', 'birth_date_jalali']);
        $this->postJson('/booking/2030-01-01', array_replace($data, ['duration_minutes' => 60]))
            ->assertUnprocessable()->assertJsonValidationErrors('duration_minutes');
    }

    public function test_pending_booking_returns_422_and_legacy_redirect(): void
    {
        $user = $this->client();
        $data = $this->booking();
        DB::table('appointments')->insert(['id' => 88, 'client_id' => $user->id]);
        $this->mock(BookingService::class)->shouldReceive('book')->twice()
            ->andThrow(ValidationException::withMessages(['pending_payment_appointment_id' => '88']));
        $this->actingAs($user)->postJson('/booking/2030-01-01', $data)
            ->assertUnprocessable()->assertJsonPath('appointment_id', 88)
            ->assertJsonPath('confirmation_url', route('booking.confirmation', 88));
        $this->post('/booking/2030-01-01', $data)->assertRedirect(route('booking.confirmation', 88));
    }

    public function test_otp_send_masks_mobile_and_enforces_resend_delay(): void
    {
        $sentCode = null;
        $this->mock(MelipayamakService::class)->shouldReceive('sendOtp')->once()
            ->withArgs(function ($mobile, $code) use (&$sentCode): bool {
                $sentCode = $code;
                return $mobile === '09123456789' && strlen($code) === 6;
            });
        $response = $this->postJson('/login', ['mobile' => '۰۹۱۲۳۴۵۶۷۸۹'])->assertOk()
            ->assertJsonPath('mobile_masked', '0912***6789')
            ->assertJsonPath('otp_length', 6)->assertJsonMissingPath('code');
        $this->assertSame(['mobile_masked', 'otp_length', 'resend_after', 'expires_in'], array_keys($response->json()));
        $this->assertGreaterThan(0, $response->json('expires_in'));
        $this->assertTrue(Hash::check($sentCode, OtpCode::query()->firstOrFail()->code_hash));
        $this->postJson('/login/resend')->assertStatus(429)->assertHeader('Retry-After');
    }

    public function test_otp_verification_logs_in_without_exposing_model_secrets(): void
    {
        $otp = OtpCode::query()->create([
            'mobile' => '09123456789', 'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(2), 'attempts' => 0,
        ]);
        $this->withSession(['auth.otp.mobile' => '09123456789'])
            ->postJson('/login/verify', ['code' => '۱۲۳۴۵۶'])->assertOk()
            ->assertJsonPath('authenticated', true)->assertJsonPath('profile_complete', false)
            ->assertJsonMissingPath('user.password')->assertJsonStructure(['dashboard_url', 'csrf_token']);
        $this->assertNotNull($otp->fresh()->consumed_at);
        $this->assertAuthenticated();
    }

    public function test_missing_otp_session_is_422_for_json_but_redirect_for_form(): void
    {
        $this->postJson('/login/verify', ['code' => '123456'])->assertUnprocessable();
        $this->post('/login/verify', ['code' => '123456'])->assertRedirect(route('auth.login'));
    }

    public function test_wrong_and_expired_otp_cannot_authenticate(): void
    {
        $otp = OtpCode::query()->create([
            'mobile' => '09123456789', 'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(2), 'attempts' => 0,
        ]);
        $this->withSession(['auth.otp.mobile' => '09123456789'])
            ->postJson('/login/verify', ['code' => '999999'])->assertUnprocessable();
        $this->assertSame(1, $otp->fresh()->attempts);
        $this->assertGuest();
        $otp->update(['expires_at' => now()->subMinute()]);
        $this->postJson('/login/verify', ['code' => '123456'])->assertUnprocessable();
        $this->assertGuest();
    }

    public function test_legacy_otp_send_still_redirects_without_accept_json(): void
    {
        $this->mock(MelipayamakService::class)->shouldReceive('sendOtp')->once();
        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post('/login', ['mobile' => '09123456789'])
            ->assertRedirect(route('auth.otp.form'));
    }

    public function test_throttle_errors_are_json_with_retry_after(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', ['mobile' => 'invalid'])->assertUnprocessable();
        }
        $this->postJson('/login', ['mobile' => 'invalid'])->assertStatus(429)->assertHeader('Retry-After');
    }

    public function test_csrf_exception_is_rendered_as_json(): void
    {
        // Tests rendering only. Real CSRF enforcement requires a browser/staging test.
        Route::post('/booking/contract-csrf', fn () => throw new TokenMismatchException());
        $this->postJson('/booking/contract-csrf')->assertStatus(419)
            ->assertHeader('Content-Type', 'application/json');
    }
}
