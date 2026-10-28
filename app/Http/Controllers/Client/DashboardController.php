<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\TreatmentPlan;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $nowTehran = now('Asia/Tehran');
        $nowUtc = $nowTehran->copy()->utc();
        $client = $request->user();
        $bookableStatuses = [
            Appointment::STATUS_CONFIRMED,
            Appointment::STATUS_PENDING_PAYMENT,
        ];

        $appointments = $client->clientAppointments()
            ->where('starts_at', '>=', $nowUtc)
            ->whereIn('status', $bookableStatuses);

        $nextAppointment = (clone $appointments)
            ->with('psychologist')
            ->orderBy('starts_at')
            ->first();

        $activeAppointmentsCount = (clone $appointments)
            ->where('status', Appointment::STATUS_CONFIRMED)
            ->count();

        $pendingPaymentCount = (clone $appointments)
            ->where('status', Appointment::STATUS_PENDING_PAYMENT)
            ->count();


        $treatmentPlans = $client->treatmentPlans()
            ->whereNotNull('activated_at')
            ->whereHas('clinicalRecord', function ($query) use ($client): void {
                $query->where('client_id', $client->id);
            })
            ->select([
                'treatment_plans.id',
                'treatment_plans.psychologist_id',
                'treatment_plans.client_id',
                'treatment_plans.title',
                'treatment_plans.status',
                'treatment_plans.current_stage',
            ])
            ->with([
                'stages' => function ($query): void {
                    $query->select([
                        'id',
                        'treatment_plan_id',
                        'stage_number',
                        'title',
                        'goal',
                        'estimated_sessions',
                    ]);
                },
            ])
            ->latest('id')
            ->get();

// فقط جلسات برگزارشده؛ نه یادداشت‌ها و نه صرفاً جلسات گذشته.
        $heldSessionsByPlan = Appointment::query()
            ->where('client_id', $client->id)
            ->whereIn('treatment_plan_id', $treatmentPlans->modelKeys())
            ->where('status', Appointment::STATUS_CONFIRMED)
            ->where('attendance_status', 'held')
            ->where('therapy_session_type', 'plan')
            ->whereNotNull('treatment_plan_stage_id')
            ->get([
                'id',
                'psychologist_id',
                'treatment_plan_id',
                'treatment_plan_stage_id',
            ])
            ->groupBy('treatment_plan_id');

        foreach ($treatmentPlans as $plan) {
            $stageIds = $plan->stages
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $validSessions = $heldSessionsByPlan
                ->get($plan->id, collect())
                ->filter(
                    fn ($appointment) =>
                        (int) $appointment->psychologist_id === (int) $plan->psychologist_id
                        && in_array(
                            (int) $appointment->treatment_plan_stage_id,
                            $stageIds,
                            true,
                        )
                );

            $countsByStage = $validSessions
                ->countBy('treatment_plan_stage_id');

            foreach ($plan->stages as $stage) {
                $stage->setAttribute(
                    'held_sessions_count',
                    (int) $countsByStage->get($stage->id, 0),
                );
            }

            $completedSessions = $validSessions->count();
            $totalEstimated = (int) $plan->stages->sum('estimated_sessions');

            $plan->setAttribute('held_sessions_count', $completedSessions);

            $plan->setAttribute(
                'progress_percent',
                $totalEstimated > 0
                    ? min(
                    100,
                    (int) round(($completedSessions / $totalEstimated) * 100),
                )
                    : 0,
            );
        }


        return view('client.dashboard', compact(
            'nextAppointment',
            'activeAppointmentsCount',
            'pendingPaymentCount',
            'treatmentPlans',
        ));
    }
}
