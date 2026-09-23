<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
        public function show(Request $request): View
    {
                /** @var User Appointmentclient */
                $client = $request->user();

                $completedAppointments = Appointment::query()
                        ->where('client_id', $client->getKey())
                    ->where('status', Appointment::STATUS_COMPLETED)
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
                                                'paid_at',
                                            ])
                                        ->latest('id');
                },
                        ])
                    ->orderByDesc('starts_at')
                    ->paginate(10)
                    ->withQueryString();

        return view('client.profile.show', [
                        'client' => $client,
                        'completedAppointments' => $completedAppointments,
                    ]);
    }
}
