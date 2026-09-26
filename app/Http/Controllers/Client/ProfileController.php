<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var User $client */
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

    public function update(Request $request): RedirectResponse
    {
        /** @var User $client */
        $client = $request->user();

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'birth_date' => [
                'required',
                'string',
                'max:10',
            ],
        ], [
            'first_name.required' => 'وارد کردن نام الزامی است.',
            'first_name.string' => 'نام واردشده معتبر نیست.',
            'first_name.max' => 'نام نباید بیشتر از ۱۰۰ کاراکتر باشد.',

            'last_name.required' => 'وارد کردن نام خانوادگی الزامی است.',
            'last_name.string' => 'نام خانوادگی واردشده معتبر نیست.',
            'last_name.max' => 'نام خانوادگی نباید بیشتر از ۱۰۰ کاراکتر باشد.',

            'birth_date.required' => 'وارد کردن تاریخ تولد الزامی است.',
            'birth_date.string' => 'تاریخ تولد واردشده معتبر نیست.',
            'birth_date.max' => 'تاریخ تولد واردشده معتبر نیست.',
        ]);

        try {
            $birthDate = \App\Support\PersianDate::birthDateToGregorian(
                $validated['birth_date']
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'birth_date' => 'تاریخ تولد شمسی واردشده معتبر نیست.',
            ]);
        }

        $client->update([
            'first_name' => trim($validated['first_name']),
            'last_name' => trim($validated['last_name']),
            'birth_date' => $birthDate,
        ]);

        return to_route('client.profile.show')
            ->with('success', 'اطلاعات پروفایل با موفقیت ذخیره شد.');
    }


}
