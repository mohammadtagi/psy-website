<?php

namespace App\Http\Controllers\Client;

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
        /** @var User $client */
        $client = $request->user();

        $appointments = Appointment::query()
            ->where('client_id', $client->getKey())
            ->with([
                'psychologist:id,first_name,last_name',
                'payments' => function ($query): void {
                    $query
                        ->select([
                            'id',
                            'appointment_id',
                            'client_id',
                            'amount',
                            'status',
                            'gateway_message',
                            'paid_at',
                        ])
                        ->latest('id');
                },
            ])
            ->orderByRaw(
                'CASE
        WHEN status IN (?, ?) THEN 0
        WHEN status IN (?, ?) THEN 1
        WHEN status = ? THEN 2
        ELSE 3
    END',
                [
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_PENDING_PAYMENT,
                    Appointment::STATUS_COMPLETED,
                    Appointment::STATUS_NO_SHOW,
                    Appointment::STATUS_CANCELLED,
                ],
            )
            ->orderByDesc('starts_at')
            ->orderByDesc('id')

            ->paginate(20)
            ->withQueryString();

        return view('client.appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    public function cancel(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        /** @var User $client */
        $client = $request->user();

        if ((int) $appointment->client_id !== (int) $client->getKey()) {
            abort(404);
        }

        $validated = $request->validate([
            'cancellation_reason' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $this->appointmentService->cancel(
            appointment: $appointment,
            actor: $client,
            reason: $validated['cancellation_reason'] ?? null,
        );

        return redirect()
            ->route('client.appointments.index')
            ->with('status', 'نوبت با موفقیت لغو شد.');
    }
}
