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
            ->with([
                'stages',
                'clinicalNotes' => fn($query) => $query
                    ->where('session_type', \App\Models\ClinicalNote::SESSION_TYPE_PLAN)
                    ->whereHas('appointment', fn($appointmentQuery) => $appointmentQuery->where(
                        'status',
                        Appointment::STATUS_COMPLETED,
                    )
                    )
                    ->with('appointment')
                    ->latest('session_at'),
            ])
            ->latest('id')
            ->get();


        return view('client.dashboard', compact(
            'nextAppointment',
            'activeAppointmentsCount',
            'pendingPaymentCount',
            'treatmentPlans',
        ));
    }
}
