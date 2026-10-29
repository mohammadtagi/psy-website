<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\Booking\DiscountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingDiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discountService,
    ) {
    }

    public function store(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ], [
            'code.required' => 'کد تخفیف را وارد کنید.',
            'code.string' => 'کد تخفیف معتبر نیست.',
            'code.max' => 'کد تخفیف نباید بیشتر از ۵۰ کاراکتر باشد.',
        ]);

        $this->discountService->apply(
            $request->user(),
            $appointment,
            $validated['code'],
        );

        return redirect()
            ->route('booking.confirmation', $appointment)
            ->with(
                'status',
                'کد تخفیف اعمال شد؛ نوبت هنوز تأیید نهایی نشده است.',
            );
    }

    public function destroy(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);

        $this->discountService->remove(
            $request->user(),
            $appointment,
        );

        return redirect()
            ->route('booking.confirmation', $appointment)
            ->with('status', 'کد تخفیف از فاکتور حذف شد.');
    }

    public function confirmFree(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);

        $this->discountService->confirmFree(
            $request->user(),
            $appointment,
        );

        return redirect()
            ->route('booking.confirmation', $appointment)
            ->with(
                'status',
                'نوبت شما بدون دریافت وجه تأیید شد.',
            );
    }

    private function authorizeAppointment(
        Request $request,
        Appointment $appointment,
    ): void {
        abort_unless(
            $request->user()?->isClient()
            && (int) $appointment->client_id
            === (int) $request->user()->getKey(),
            404,
        );
    }
}
