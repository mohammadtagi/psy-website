<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClientSessionNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientSessionNoteController extends Controller
{
    public function store(
        Request $request,
        Appointment $appointment,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note = new ClientSessionNote();
        $note->body = $data['body'];
        $note->client_id = $request->user()->id;
        $note->appointment_id = $appointment->id;
        $note->save();

        return redirect()
            ->route('client.appointments.session.show', $appointment)
            ->with('status', 'یادداشت خصوصی ثبت شد.');
    }

    public function update(
        Request $request,
        Appointment $appointment,
        ClientSessionNote $note,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);
        $this->authorizeNote($request, $appointment, $note);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note->body = $data['body'];
        $note->save();

        return redirect()
            ->route('client.appointments.session.show', $appointment)
            ->with('status', 'یادداشت خصوصی ویرایش شد.');
    }

    public function destroy(
        Request $request,
        Appointment $appointment,
        ClientSessionNote $note,
    ): RedirectResponse {
        $this->authorizeAppointment($request, $appointment);
        $this->authorizeNote($request, $appointment, $note);

        $note->delete();

        return redirect()
            ->route('client.appointments.session.show', $appointment)
            ->with('status', 'یادداشت خصوصی حذف شد.');
    }

    private function authorizeAppointment(
        Request $request,
        Appointment $appointment,
    ): void {
        abort_unless(
            $request->user()?->isClient()
            && (int) $appointment->client_id
            === (int) $request->user()->id,
            404,
        );
    }

    private function authorizeNote(
        Request $request,
        Appointment $appointment,
        ClientSessionNote $note,
    ): void {
        abort_unless(
            (int) $note->client_id === (int) $request->user()->id
            && (int) $note->appointment_id === (int) $appointment->id,
            404,
        );
    }
}
