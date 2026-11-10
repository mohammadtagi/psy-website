<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Services\Payment\BehpardakhtPaymentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Throwable;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private readonly BehpardakhtPaymentService $behpardakht,
    ) {
    }

    public function pay(
        Request $request,
        Appointment $appointment,
    ): View|RedirectResponse {
        abort_unless(
            (int) $appointment->client_id === (int) $request->user()->getKey(),
            403,
        );

        if (! config('payment.online_enabled', false)) {
            return redirect()
                ->route('booking.confirmation', $appointment)
                ->withErrors([
                    'payment' => 'پرداخت آنلاین فعلاً در دسترس نیست.',
                ]);
        }

        try {
            $paymentData = DB::transaction(function () use (
                $appointment,
            ): array {
                $lockedAppointment = Appointment::query()
                    ->whereKey($appointment->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $now = CarbonImmutable::now('UTC');

                if ($lockedAppointment->status !== Appointment::STATUS_PENDING_PAYMENT) {
                    throw new RuntimeException('این نوبت در وضعیت قابل پرداخت نیست.');
                }

                if (
                    $lockedAppointment->hold_expires_at === null
                    || CarbonImmutable::instance($lockedAppointment->hold_expires_at)->lte($now)
                ) {
                    throw new RuntimeException('مهلت پرداخت این نوبت به پایان رسیده است.');
                }

                if (CarbonImmutable::instance($lockedAppointment->starts_at)->lte($now)) {
                    throw new RuntimeException('زمان شروع این نوبت گذشته است.');
                }

                // محاسبه مبلغ نهایی فاکتور با کسر تخفیف
                $payableAmount = max(0, (int) $lockedAppointment->amount - (int) $lockedAppointment->discount_amount);

                if ($payableAmount === 0) {
                    throw new RuntimeException('مبلغ پرداختی صفر است؛ لطفاً گزینه تأیید رایگان را انتخاب کنید.');
                }

                // ابطال تمام رکوردهای در انتظار قبلی نوبت (طبق اصل شفافیت و ایجاد رکورد جدید در هر بار کلیک)
                Payment::query()
                    ->where('appointment_id', $lockedAppointment->getKey())
                    ->whereIn('status', [Payment::STATUS_INITIATED, Payment::STATUS_PENDING])
                    ->lockForUpdate()
                    ->update([
                        'status' => Payment::STATUS_CANCELLED,
                        'gateway_message' => 'این تلاش پرداخت با درخواست پرداخت مجدد کاربر لغو گردید.',
                    ]);

                // ایجاد فاکتور جدید یکتا
                $saleOrderId = (int) (now()->format('ymdHis') . random_int(10, 99));

                $payment = Payment::query()->create([
                    'appointment_id' => $lockedAppointment->getKey(),
                    'client_id' => $lockedAppointment->client_id,
                    'amount' => $payableAmount,
                    'final_amount' => $payableAmount,
                    'gateway' => Payment::GATEWAY_BEHPARDAKHT,
                    'status' => Payment::STATUS_INITIATED,
                    'sale_order_id' => $saleOrderId,
                ]);

                // دریافت توکن پرداخت RefId از به‌پرداخت ملت
                $callbackUrl = route('payments.behpardakht.callback');
                $refId = $this->behpardakht->request($payment, $callbackUrl);

                $payment->forceFill([
                    'authority' => $refId,
                    'status' => Payment::STATUS_PENDING,
                ])->save();

                return [
                    'refId' => $refId,
                    'startPayUrl' => $this->behpardakht->getStartPayUrl(),
                ];
            }, 3);

            return view('payments.behpardakht_redirect', $paymentData);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'payment' => $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'شروع پرداخت امکان‌پذیر نیست. لطفاً دوباره تلاش کنید.',
            ]);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $refId = (string) $request->input('RefId', '');
        $resCode = (string) $request->input('ResCode', '');
        $saleOrderId = (int) $request->input('SaleOrderId', 0);
        $saleReferenceId = (string) $request->input('SaleReferenceId', '');
        $cardPan = (string) $request->input('CardHolderPan', '');

        if ($refId === '' || $saleOrderId === 0) {
            return redirect()
                ->route('booking.index')
                ->withErrors(['payment' => 'اطلاعات بازگشت از درگاه ناقص است.']);
        }

        $payment = Payment::query()
            ->with('appointment')
            ->where('authority', $refId)
            ->where('sale_order_id', $saleOrderId)
            ->first();

        if ($payment === null) {
            return redirect()
                ->route('booking.index')
                ->withErrors(['payment' => 'تراکنش پرداخت متناظر یافت نشد.']);
        }

        // اگر کاربر فرایند را در صفحه بانک لغو کرده یا ناموفق بوده است
        if ($resCode !== '0') {
            DB::transaction(function () use ($payment, $resCode): void {
                $lockedPayment = Payment::query()->whereKey($payment->getKey())->lockForUpdate()->firstOrFail();
                if (! $lockedPayment->isFinal()) {
                    $lockedPayment->update([
                        'status' => Payment::STATUS_CANCELLED,
                        'gateway_message' => "پرداخت ناموفق بود یا توسط کاربر لغو شد (کد بانک: {$resCode}).",
                    ]);
                }
            }, 3);

            return redirect()
                ->route('booking.confirmation', $payment->appointment)
                ->withErrors(['payment' => 'پرداخت انجام نشد یا در درگاه لغو گردید.']);
        }

        // بررسی اینکه آیا این فاکتور جدیدترین فاکتور معتبر این نوبت است؟
        $latestPaymentId = Payment::query()
            ->where('appointment_id', $payment->appointment_id)
            ->latest('id')
            ->value('id');

        $isSuperseded = ((int) $payment->getKey() !== (int) $latestPaymentId) || ($payment->status === Payment::STATUS_CANCELLED);

        // سناریو: پرداخت روی فاکتور منسوخ یا قدیمی رخ داده است (نیاز به بازگشت وجه فوری Reversal)
        if ($isSuperseded) {
            Log::warning('Payment attempt on superseded invoice. Triggering reversal.', [
                'payment_id' => $payment->id,
                'sale_order_id' => $saleOrderId,
                'sale_reference_id' => $saleReferenceId,
            ]);

            // متد ریورسال ملت طبق پروتکل بعد از Verify صورت می‌گیرد
            $this->behpardakht->verify($saleOrderId, $saleOrderId, $saleReferenceId);
            $reversed = $this->behpardakht->reverse($saleOrderId, $saleOrderId, $saleReferenceId);

            DB::transaction(function () use ($payment, $saleReferenceId, $cardPan, $reversed): void {
                $locked = Payment::query()->whereKey($payment->getKey())->lockForUpdate()->firstOrFail();
                $locked->update([
                    'ref_id' => $saleReferenceId,
                    'card_holder_pan' => $cardPan,
                    'status' => $reversed ? Payment::STATUS_REFUNDED : Payment::STATUS_FAILED,
                    'gateway_message' => $reversed
                        ? 'پرداخت روی فاکتور باطل‌شده انجام گرفت؛ وجه با موفقیت به حساب دارنده کارت بازگردانده شد.'
                        : 'پرداخت روی فاکتور باطل‌شده انجام گرفت؛ ارسال درخواست بازگشت وجه ناموفق بود و باید توسط پشتیبانی پیگیری شود.',
                ]);
            }, 3);

            return redirect()
                ->route('booking.confirmation', $payment->appointment)
                ->withErrors([
                    'payment' => 'این فاکتور قبلاً باطل شده بود. مبلغ کسر شده به صورت خودکار به حساب بانکی شما برگشت داده شد.',
                ]);
        }

        // سناریوی عادی: فاکتور معتبر و جاری است
        try {
            // ۱. تایید تراکنش نزد بانک ملت
            $verified = $this->behpardakht->verify($saleOrderId, $saleOrderId, $saleReferenceId);

            if (! $verified) {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'gateway_message' => 'تأیید تراکنش (Verify) نزد بانک با خطا مواجه شد.',
                ]);

                return redirect()
                    ->route('booking.confirmation', $payment->appointment)
                    ->withErrors(['payment' => 'تأیید پرداخت توسط درگاه بانکی انجام نشد.']);
            }

            // ۲. نهایی‌سازی واریز (Settle)
            $settled = $this->behpardakht->settle($saleOrderId, $saleOrderId, $saleReferenceId);

            if (! $settled) {
                Log::error('Behpardakht bpSettleRequest failed after successful verify', [
                    'payment_id' => $payment->id,
                    'sale_order_id' => $saleOrderId,
                ]);
            }

            // ۳. تکمیل نوبت و ذخیره اطلاعات تراکنش در دیتابیس
            DB::transaction(function () use ($payment, $saleReferenceId, $cardPan): void {
                $lockedPayment = Payment::query()->whereKey($payment->getKey())->lockForUpdate()->firstOrFail();
                $appointment = Appointment::query()->whereKey($lockedPayment->appointment_id)->lockForUpdate()->firstOrFail();

                $now = CarbonImmutable::now('UTC');

                $lockedPayment->update([
                    'status' => Payment::STATUS_PAID,
                    'ref_id' => $saleReferenceId,
                    'card_holder_pan' => $cardPan,
                    'paid_at' => $now,
                    'gateway_message' => 'پرداخت و واریز با موفقیت انجام شد.',
                ]);

                $appointment->update([
                    'status' => Appointment::STATUS_CONFIRMED,
                    'hold_expires_at' => null,
                ]);
            }, 3);
            if (! Auth::check() && $payment->appointment?->client_id) {
                Auth::loginUsingId($payment->appointment->client_id);
            }
            return redirect()
                ->route('booking.confirmation', $payment->appointment)
                ->with('status', 'پرداخت با موفقیت انجام شد و نوبت شما تأیید گردید.');

        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('booking.confirmation', $payment->appointment)
                ->withErrors(['payment' => 'خطایی در ثبت و تأیید نهایی نوبت رخ داد.']);
        }
    }
}
