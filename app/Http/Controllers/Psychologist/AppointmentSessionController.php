<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TreatmentPlan;
use App\Services\AppointmentSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentSessionController extends Controller
{
    public function show(
        Request $request,
        Appointment $appointment,
    ): View {
        $this->authorizeOwner($request, $appointment);

        $appointment->load([
            'client',
            'treatmentPlan',
            'treatmentPlanStage',
        ]);

        $plans = TreatmentPlan::query()
            ->where('psychologist_id', $request->user()->id)
            ->where('client_id', $appointment->client_id)
            ->with('stages')
            ->latest('id')
            ->get();

        return view('psychologist.appointments.session', [
            'appointment' => $appointment,
            'plans' => $plans,
        ]);
    }

    public function update(
        Request $request,
        Appointment $appointment,
        AppointmentSessionService $service,
    ): RedirectResponse {
        $this->authorizeOwner($request, $appointment);

        $service->update(
            $request->user(),
            $appointment,
            $request->only([
                'therapy_session_type',
                'treatment_plan_id',
                'treatment_plan_stage_id',
                'attendance_status',
                'meeting_url',
                'session_subject',
                'session_discussion',
                'session_recommendations',
                'session_exercises',
            ]),
        );

        return redirect()
            ->route('psychologist.appointments.session.show', $appointment)
            ->with('status', 'اطلاعات جلسه ذخیره شد.');
    }

    private function authorizeOwner(
        Request $request,
        Appointment $appointment,
    ): void {
        abort_unless(
            $request->user()?->isPsychologist()
            && (int) $appointment->psychologist_id
            === (int) $request->user()->id,
            404,
        );
    }
}
