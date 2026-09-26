<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Availability;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $nowTehran = now('Asia/Tehran');
        $nowUtc = $nowTehran->copy()->utc();
        $startOfTodayUtc = $nowTehran->copy()->startOfDay()->utc();
        $endOfTodayUtc = $nowTehran->copy()->endOfDay()->utc();

        $psychologistId = $request->user()->getKey();

        $bookableStatuses = [
            Appointment::STATUS_CONFIRMED,
            Appointment::STATUS_PENDING_PAYMENT,
        ];

        $todayAppointments = Appointment::query()
            ->where('psychologist_id', $psychologistId)
            ->whereBetween('starts_at', [$startOfTodayUtc, $endOfTodayUtc])
            ->whereIn('status', $bookableStatuses)
            ->with('client')
            ->orderBy('starts_at')
            ->get();

        $upcomingAppointments = Appointment::query()
            ->where('psychologist_id', $psychologistId)
            ->where('starts_at', '>', $endOfTodayUtc)
            ->whereIn('status', $bookableStatuses)
            ->with('client')
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        $upcomingAvailabilities = Availability::query()
            ->where('psychologist_id', $psychologistId)
            ->where('status', Availability::STATUS_ACTIVE)
            ->where('starts_at', '>=', $nowUtc)
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        return view('psychologist.dashboard', compact(
            'todayAppointments',
            'upcomingAppointments',
            'upcomingAvailabilities',
        ));
    }
}
