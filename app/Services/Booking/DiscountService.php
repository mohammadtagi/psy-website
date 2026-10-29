<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\DiscountCode;
use App\Models\DiscountRedemption;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DiscountService
{
    public function apply(
        User $client,
        Appointment $appointment,
        string $input,
    ): Appointment {
        $code = DiscountCode::normalizeCode($input);

        if (! preg_match('/\A[A-Z0-9_-]{3,50}\z/', $code)) {
            $this->fail('کد تخفیف معتبر نیست.');
        }

        return DB::transaction(function () use (
            $client,
            $appointment,
            $code,
        ): Appointment {
            $locked = $this->lockOwnedAppointment($client, $appointment);

            $this->ensurePending($locked);
            $this->ensureNoGatewayTransaction($locked);

            $discount = DiscountCode::query()
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if ($discount === null) {
                $this->fail('کد تخفیف معتبر نیست.');
            }

            $this->ensureUsable($discount, $locked);

            // پس از انتظار برای قفل کد، زمان مجدداً بررسی می‌شود.
            $this->ensurePending($locked);

            $locked->forceFill([
                'discount_code_id' => $discount->getKey(),
                'discount_amount' => $discount->discountFor(
                    (int) $locked->amount,
                ),
            ])->save();

            return $locked;
        }, 3);
    }

    public function remove(
        User $client,
        Appointment $appointment,
    ): Appointment {
        return DB::transaction(function () use (
            $client,
            $appointment,
        ): Appointment {
            $locked = $this->lockOwnedAppointment($client, $appointment);

            $this->ensurePending($locked);
            $this->ensureNoGatewayTransaction($locked);

            $locked->forceFill([
                'discount_code_id' => null,
                'discount_amount' => 0,
            ])->save();

            return $locked;
        }, 3);
    }

    public function confirmFree(
        User $client,
        Appointment $appointment,
    ): Appointment {
        return DB::transaction(function () use (
            $client,
            $appointment,
        ): Appointment {
            $locked = $this->lockOwnedAppointment($client, $appointment);

            // درخواست تکراری نباید پرداخت یا مصرف دیگری بسازد.
            $alreadyRedeemed = DiscountRedemption::query()
                ->where('appointment_id', $locked->getKey())
                ->where('client_id', $client->getKey())
                ->where('final_amount', 0)
                ->exists();

            if (
                $alreadyRedeemed
                && $locked->status === Appointment::STATUS_CONFIRMED
            ) {
                return $locked;
            }

            $this->ensurePending($locked);
            $this->ensureNoGatewayTransaction($locked);

            if ($locked->discount_code_id === null) {
                $this->fail('ابتدا کد تخفیف را اعمال کنید.');
            }

            $discount = DiscountCode::query()
                ->whereKey($locked->discount_code_id)
                ->lockForUpdate()
                ->first();

            if ($discount === null) {
                $this->fail('کد تخفیف معتبر نیست.');
            }

            $this->ensureUsable($discount, $locked);
            $this->ensurePending($locked);

            $originalAmount = (int) $locked->amount;
            $discountAmount = $discount->discountFor($originalAmount);
            $finalAmount = max(0, $originalAmount - $discountAmount);

            if ($finalAmount !== 0) {
                $this->fail(
                    'مبلغ نهایی صفر نیست؛ این نوبت به پرداخت نیاز دارد.',
                );
            }

            $now = CarbonImmutable::now('UTC');

            // پرداخت اولیه بدون لینک درگاه، دیگر قابل استفاده نیست.
            Payment::query()
                ->where('appointment_id', $locked->getKey())
                ->whereIn('status', [
                    Payment::STATUS_INITIATED,
                    Payment::STATUS_PENDING,
                ])
                ->update([
                    'status' => Payment::STATUS_CANCELLED,
                    'gateway_message' =>
                        'با تأیید رایگان نوبت جایگزین شد؛ وجهی دریافت نشد.',
                ]);

            // این رکورد تسویه صفر است، نه پرداخت موفق زرین‌پال.
            $payment = Payment::query()->create([
                'appointment_id' => $locked->getKey(),
                'client_id' => $locked->client_id,
                'amount' => 0,
                'gateway' => 'free',
                'status' => Payment::STATUS_PAID,
                'paid_at' => $now,
                'gateway_message' =>
                    'تأیید رایگان با کد تخفیف؛ بدون دریافت وجه.',
            ]);

            DiscountRedemption::query()->create([
                'discount_code_id' => $discount->getKey(),
                'appointment_id' => $locked->getKey(),
                'payment_id' => $payment->getKey(),
                'client_id' => $locked->client_id,
                'code' => $discount->code,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => 0,
            ]);

            $locked->forceFill([
                'discount_amount' => $discountAmount,
                'status' => Appointment::STATUS_CONFIRMED,
                'hold_expires_at' => null,
            ])->save();

            return $locked;
        }, 3);
    }

    private function lockOwnedAppointment(
        User $client,
        Appointment $appointment,
    ): Appointment {
        abort_unless($client->isClient(), 403);

        return Appointment::query()
            ->whereKey($appointment->getKey())
            ->where('client_id', $client->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function ensurePending(Appointment $appointment): void
    {
        $now = CarbonImmutable::now('UTC');

        if (
            $appointment->status
            !== Appointment::STATUS_PENDING_PAYMENT
        ) {
            $this->fail('این نوبت در وضعیت قابل پرداخت نیست.');
        }

        if (
            $appointment->hold_expires_at === null
            || CarbonImmutable::instance(
                $appointment->hold_expires_at,
            )->lte($now)
        ) {
            $this->fail('مهلت پرداخت این نوبت به پایان رسیده است.');
        }

        if (
            CarbonImmutable::instance($appointment->starts_at)->lte($now)
        ) {
            $this->fail('زمان شروع این نوبت فرا رسیده است.');
        }
    }

    private function ensureNoGatewayTransaction(
        Appointment $appointment,
    ): void {
        $payments = Payment::query()
            ->where('appointment_id', $appointment->getKey())
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($payments as $payment) {
            if (
                $payment->status === Payment::STATUS_PAID
                || (
                    $payment->authority !== null
                    && $payment->authority !== ''
                )
            ) {
                $this->fail(
                    'این نوبت تراکنش پرداخت دارد؛ تغییر تخفیف یا '
                    . 'تأیید رایگان تا تعیین تکلیف تراکنش ممکن نیست.',
                );
            }
        }
    }

    private function ensureUsable(
        DiscountCode $discount,
        Appointment $appointment,
    ): void {
        if (
            ! $discount->is_active
            || $discount->expires_at === null
            || $discount->expires_at->lte(CarbonImmutable::now('UTC'))
        ) {
            $this->fail('کد تخفیف غیرفعال است یا اعتبار آن پایان یافته است.');
        }

        if (
            $discount->client_id !== null
            && (int) $discount->client_id
            !== (int) $appointment->client_id
        ) {
            $this->fail('این کد تخفیف برای حساب شما قابل استفاده نیست.');
        }

        if (
            ! in_array($discount->type, [
                DiscountCode::TYPE_PERCENT,
                DiscountCode::TYPE_FIXED,
            ], true)
            || $discount->value < 1
            || (
                $discount->type === DiscountCode::TYPE_PERCENT
                && $discount->value > 100
            )
        ) {
            $this->fail('تنظیمات کد تخفیف معتبر نیست.');
        }

        // خواندن قفل‌دار، وضعیت به‌روز مصرف‌ها را می‌بیند.
        // قفل ردیف discount_codes نیز ثبت مصرف‌های یک کد را سری می‌کند.
        $redemptions = DiscountRedemption::query()
            ->where('discount_code_id', $discount->getKey())
            ->lockForUpdate()
            ->get(['id', 'client_id']);

        if (
            $discount->usage_limit !== null
            && $redemptions->count() >= $discount->usage_limit
        ) {
            $this->fail('ظرفیت استفاده از این کد تخفیف تمام شده است.');
        }

        $clientUsage = $redemptions
            ->filter(
                fn (DiscountRedemption $redemption): bool =>
                    (int) $redemption->client_id
                    === (int) $appointment->client_id,
            )
            ->count();

        if (
            $discount->per_client_limit !== null
            && $clientUsage >= $discount->per_client_limit
        ) {
            $this->fail('سقف استفاده شما از این کد تخفیف تکمیل شده است.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'discount' => $message,
        ]);
    }
}
