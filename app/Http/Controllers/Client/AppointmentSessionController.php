<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ClientSessionNote;

class AppointmentSessionController extends Controller
{
    public function show(
        Request $request,
        Appointment $appointment,
    ): View {
        abort_unless(
            $request->user()?->isClient()
            && (int) $appointment->client_id
            === (int) $request->user()->id,
            404,
        );

        $appointment->load('psychologist');

        $notes = ClientSessionNote::query()
            ->where('appointment_id', $appointment->id)
            ->where('client_id', $request->user()->id)
            ->latest('id')
            ->paginate(10);

        return view('client.appointments.session', [
            'appointment' => $appointment,
            'notes' => $notes,
        ]);

    }
}
