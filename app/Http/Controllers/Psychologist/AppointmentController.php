<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Services\Booking\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
    ) {
    }

    public function index(Request $request): View
    {
        /** @var User $psychologist */
        $psychologist = $request->user();

        $appointments = Appointment::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->with([
                'client:id,first_name,last_name,mobile,birth_date',
            ])
            ->orderBy('starts_at')
            ->paginate(30)
            ->withQueryString();

        return view('psychologist.appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    public function cancel(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        /** @var User $psychologist */
        $psychologist = $request->user();

        if (
            (int) $appointment->psychologist_id
            !== (int) $psychologist->getKey()
        ) {
            abort(404);
        }


        $validated = $request->validate([
            'cancellation_reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $this->appointmentService->cancel(
            appointment: $appointment,
            actor: $psychologist,
            reason: $validated['cancellation_reason'] ?? null,
            bypassClientTimeLimit: true,
        );

        return redirect()
            ->route('psychologist.appointments.index')
            ->with('status', 'نوبت با موفقیت لغو شد.');
    }
}
