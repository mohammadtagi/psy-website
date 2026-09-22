<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Services\Payment\ZarinpalPaymentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly ZarinpalPaymentService $zarinpal,
    ) {
    }

    public function pay(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        abort_unless(
            $appointment->client_id === $request->user()->getKey(),
            403,
        );

        try {
            $authority = DB::transaction(function () use (
                $appointment,
            ): string {
                $lockedAppointment = Appointment::query()
                    ->whereKey($appointment->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $now = CarbonImmutable::now('UTC');

                if (
                    $lockedAppointment->status
                    !== Appointment::STATUS_PENDING_PAYMENT
                ) {
                    throw new RuntimeException(
                        'این نوبت در وضعیت قابل پرداخت نیست.'
                    );
                }

                if (
                    $lockedAppointment->hold_expires_at === null
                    || CarbonImmutable::instance(
                        $lockedAppointment->hold_expires_at
                    )->lte($now)
                ) {
                    throw new RuntimeException(
                        'مهلت پرداخت این نوبت به پایان رسیده است.'
                    );
                }

                if (
                    CarbonImmutable::instance(
                        $lockedAppointment->starts_at
                    )->lte($now)
                ) {
                    throw new RuntimeException(
                        'زمان شروع این نوبت گذشته است.'
                    );
                }

                $payment = Payment::query()
                    ->where('appointment_id', $lockedAppointment->getKey())
                    ->where('client_id', $lockedAppointment->client_id)
                    ->whereIn('status', [
                        Payment::STATUS_INITIATED,
                        Payment::STATUS_PENDING,
                    ])
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if ($payment === null) {
                    throw new RuntimeException(
                        'رکورد پرداخت این نوبت پیدا نشد.'
                    );
                }

                if (
                    $payment->authority !== null
                    && $payment->authority !== ''
                ) {
                    return $payment->authority;
                }

                $authority = $this->zarinpal->request($payment);

                $payment->authority = $authority;
                $payment->status = Payment::STATUS_PENDING;
                $payment->save();

                return $authority;
            }, 3);

            return redirect()->away(
                $this->zarinpal->startUrl($authority)
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'payment' => $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'شروع پرداخت امکان‌پذیر نیست. دوباره تلاش کنید.',
            ]);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $authority = (string) $request->query('Authority', '');
        $gatewayStatus = strtoupper(
            (string) $request->query('Status', '')
        );

        if ($authority === '') {
            return redirect()
                ->route('booking.index')
                ->withErrors([
                    'payment' => 'اطلاعات بازگشت از درگاه ناقص است.',
                ]);
        }

        $payment = Payment::query()
            ->with('appointment')
            ->where('authority', $authority)
            ->first();

        if ($payment === null) {
            return redirect()
                ->route('booking.index')
                ->withErrors([
                    'payment' => 'رکورد پرداخت پیدا نشد.',
                ]);
        }

        if ($payment->status === Payment::STATUS_PAID) {
            return redirect()
                ->route(
                    'booking.confirmation',
                    $payment->appointment,
                )
                ->with('status', 'این پرداخت قبلاً با موفقیت ثبت شده است.');
        }

        if ($gatewayStatus !== 'OK') {
            $payment->status = Payment::STATUS_FAILED;
            $payment->gateway_message = 'پرداخت توسط کاربر لغو شد.';
            $payment->save();

            return redirect()
                ->route(
                    'booking.confirmation',
                    $payment->appointment,
                )
                ->withErrors([
                    'payment' => 'پرداخت لغو شد یا توسط درگاه تأیید نشد.',
                ]);
        }

        try {
            $result = $this->zarinpal->verify(
                $payment,
                $authority,
            );

            if (! $result['success']) {
                $payment->status = Payment::STATUS_FAILED;
                $payment->gateway_message = $result['message'];
                $payment->save();

                return redirect()
                    ->route(
                        'booking.confirmation',
                        $payment->appointment,
                    )
                    ->withErrors([
                        'payment' => $result['message']
                            ?? 'پرداخت تأیید نشد.',
                    ]);
            }

            DB::transaction(function () use (
                $payment,
                $authority,
                $result,
            ): void {
                $lockedPayment = Payment::query()
                    ->whereKey($payment->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedPayment->status === Payment::STATUS_PAID) {
                    return;
                }

                $appointment = Appointment::query()
                    ->whereKey($lockedPayment->appointment_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $now = CarbonImmutable::now('UTC');

                if (
                    $appointment->status
                    !== Appointment::STATUS_PENDING_PAYMENT
                    || $appointment->hold_expires_at === null
                    || CarbonImmutable::instance(
                        $appointment->hold_expires_at
                    )->lte($now)
                    || CarbonImmutable::instance(
                        $appointment->starts_at
                    )->lte($now)
                ) {
                    $lockedPayment->status = Payment::STATUS_FAILED;
                    $lockedPayment->gateway_message =
                        'رزرو در زمان تأیید پرداخت دیگر معتبر نبود.';
                    $lockedPayment->save();

                    throw new RuntimeException(
                        'این نوبت دیگر قابل تأیید نیست.'
                    );
                }

                $lockedPayment->status = Payment::STATUS_PAID;
                $lockedPayment->ref_id = $result['ref_id'];
                $lockedPayment->paid_at = $now;
                $lockedPayment->gateway_message = null;
                $lockedPayment->save();

                $appointment->status = Appointment::STATUS_CONFIRMED;
                $appointment->hold_expires_at = null;
                $appointment->save();
            }, 3);

            return redirect()
                ->route(
                    'booking.confirmation',
                    $payment->appointment,
                )
                ->with('status', 'پرداخت با موفقیت انجام شد و نوبت تأیید شد.');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route(
                    'booking.confirmation',
                    $payment->appointment,
                )
                ->withErrors([
                    'payment' => $exception instanceof RuntimeException
                        ? $exception->getMessage()
                        : 'پرداخت انجام شد، اما تأیید نهایی نوبت ممکن نشد.',
                ]);
        }
    }
}
