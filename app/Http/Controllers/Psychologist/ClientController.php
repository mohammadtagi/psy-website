<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PersianDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;

class ClientController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = trim((string) ($validated['q'] ?? ''));

        $clients = collect();

        if ($query !== '') {
            $clientsQuery = User::query()
                ->where('role', User::ROLE_CLIENT);

            if ($this->looksLikeMobileSearch($query)) {
                $clientsQuery->where('mobile', 'like', '%' . $query . '%');
            } else {
                $clientsQuery->where(function ($nameQuery) use ($query): void {
                    $nameQuery
                        ->where('first_name', 'like', '%' . $query . '%')
                        ->orWhere('last_name', 'like', '%' . $query . '%')
                        ->orWhereRaw(
                            "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?",
                            ['%' . $query . '%'],
                        );
                });
            }

            $clients = $clientsQuery
                ->with('clinicalRecord:id,client_id,status')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->orderBy('mobile')
                ->limit(50)
                ->get();

        }

        $canCreateClient = $this->isCompleteMobileNumber($query);

        return view('psychologist.clients.index', [
            'query' => $query,
            'clients' => $clients,
            'canCreateClient' => $canCreateClient,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'mobile' => [
                'required',
                'string',
                'regex:/^09\d{9}$/',
                'unique:users,mobile',
            ],
            'birth_date' => [
                'nullable',
                'string',
                'regex:/^\d{4}\/\d{1,2}\/\d{1,2}$/',
            ],
        ], [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود.',
            'mobile.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'birth_date.regex' => 'تاریخ تولد را به شکل ۱۴۰۰/۰۱/۰۱ وارد کنید.',
        ]);

        $birthDate = null;

        if (! empty($validated['birth_date'])) {
            try {
                $birthDate = PersianDate::birthDateToGregorian(
                    $validated['birth_date'],
                );
            } catch (InvalidArgumentException) {
                return redirect()
                    ->route('psychologist.clients.index', [
                        'q' => $validated['mobile'],
                    ])
                    ->withErrors([
                        'birth_date' => 'تاریخ تولد واردشده معتبر نیست.',
                    ])
                    ->withInput();
            }
        }

        $client = User::query()->create([
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'mobile' => $validated['mobile'],
            'birth_date' => $birthDate,
            'role' => User::ROLE_CLIENT,
            'password' => Hash::make(Str::random(32)),
            ]);

        return redirect()
            ->route('psychologist.clients.index', [
                'q' => $client->mobile,
            ])
            ->with('status', 'مراجع با موفقیت ثبت شد.');
    }

    private function looksLikeMobileSearch(string $query): bool
    {
        return (bool) preg_match('/^[0-9۰-۹]+$/u', $query);
    }

    private function isCompleteMobileNumber(string $query): bool
    {
        return (bool) preg_match('/^09\d{9}$/', $query);
    }
}
