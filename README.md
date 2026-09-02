
اسکلت فاز ۱ سایت مشاوره (Laravel 13 style) — فقط ورود/ثبت‌نام با OTP موبایل، تکمیل پروفایل و داشبورد.

## نصب
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## متغیرهای env
```
SMS_DRIVER=fake            # fake | melipayamak
SMS_FROM=10008000
OTP_TTL_MINUTES=5
OTP_LENGTH=6
OTP_MAX_ATTEMPTS=5
MEELIPAYAMAK_USERNAME=
MEELIPAYAMAK_PASSWORD=
MEELIPAYAMAK_ORIGIN=
```

## ساختار کلیدی
- `app/Services/MobileNormalizationService.php` — نرمال‌سازی شماره موبایل ایران به +989xxxxxxxxx
- `app/Services/Sms/` — قرارداد `SmsGatewayInterface` + پیاده‌سازی Fake و Melipayamak
- `app/Models/{User,OtpCode}.php`
- `app/Http/Controllers/Auth/OtpController.php`, `ProfileController.php`, `DashboardController.php`
- `app/Http/Middleware/EnsureUserIsActive.php`
- `app/Http/Requests/Auth/*`
- `database/migrations/*`, `database/factories/UserFactory.php`
- `app/Providers/AppServiceProvider.php` — بایندینگ‌ها و Rate Limiters
- `config/sms.php`
- `resources/views/**` — ویوهای RTL فارسی
- `tests/Unit/MobileNormalizationTest.php`, `tests/Feature/OtpFlowTest.php`

## جریان
1. `GET /login` → ارسال شماره → `POST /otp/send`
2. `GET /verify` → `POST /otp/verify` (کد هش‌شده، تک‌مصرف، منقضی‌شونده؛ OTPهای فعال قبلی برای همان شماره/هدف لغو می‌شوند)
3. تکمیل پروفایل → داشبورد
   """)
