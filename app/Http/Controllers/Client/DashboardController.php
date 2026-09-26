<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

        return view('client.dashboard', compact(
                        'nextAppointment',
                        'activeAppointmentsCount',
                        'pendingPaymentCount',
                    ));
    }
}
