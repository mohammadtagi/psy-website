<?php

namespace App\Providers;

use App\Services\Sms\FakeSmsGateway;
use App\Services\Sms\MelipayamakGateway;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // بایندینگ درایور SMS بر اساس config — قابل تعویض با هر پیاده‌سازی دیگر
        $this->app->bind(SmsGatewayInterface::class, function () {
            return match (config('sms.driver')) {
                'melipayamak' => new MelipayamakGateway(config('sms.drivers.melipayamak')),
                default       => new FakeSmsGateway(config('sms.drivers.fake')),
            };
        });
    }

    public function boot(): void
    {
        $this->configureRateLimiters();
    }

    protected function configureRateLimiters(): void
    {
        // محدودیت ارسال OTP: بر اساس شماره موبایل و IP
        RateLimiter::for('otp-send', function (Request $request) {
            $mobile = $request->input('mobile', 'unknown');

            return [
                Limit::perHour(config('sms.otp.max_sends_per_hour'))->by('otp-send:'.$mobile),
                Limit::perMinute(2)->by('otp-send-ip:'.$request->ip()),
            ];
        });

        // محدودیت بررسی OTP: بر اساس IP
        RateLimiter::for('otp-verify', function (Request $request) {
            return Limit::perMinute(10)->by('otp-verify-ip:'.$request->ip());
        });
    }
}
