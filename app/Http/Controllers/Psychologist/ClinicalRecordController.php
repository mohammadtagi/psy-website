<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\ClinicalRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Appointment;
use App\Services\Booking\ManualAppointmentService;

class ClinicalRecordController extends Controller
{
    public function show(User $client): View|RedirectResponse
    {
        $this->ensureClient($client);

        $record = $client->clinicalRecord()
            ->with([
                'items',
                'notes' => fn ($query) => $query
                    ->where('psychologist_id', auth()->id())
                    ->latest('created_at')
                    ->latest('id'),
                'attachments' => fn ($query) => $query
                    ->latest('id'),
                'treatmentPlans' => fn ($query) => $query
                    ->where('psychologist_id', auth()->id())
                    ->with('stages')
                    ->latest('id'),

            ])
            ->first();

        if (! $record) {
            return redirect()
                ->route('psychologist.clinical-records.create', $client);
        }

        $appointments = Appointment::query()
            ->where('client_id', $client->id)
            ->where('psychologist_id', auth()->id())
            ->whereIn('status', [
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_NO_SHOW,
            ])
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'appointments_page')
            ->withQueryString();



        $manualDefaultDuration = (int) config(
            'booking.manual_default_duration_minutes',
            45,
        );

        $manualDefaultAmount = app(
            \App\Services\Booking\BookingService::class,
        )->amountForDuration($manualDefaultDuration);

        return view('psychologist.clinical-records.show', [
            'client' => $client,
            'record' => $record,
            'recordAppointments' => $appointments,
            'manualDefaultDuration' => $manualDefaultDuration,
            'manualDefaultAmount' => $manualDefaultAmount,
        ]);

    }

    public function create(User $client): View|RedirectResponse
    {
        $this->ensureClient($client);

        if ($client->clinicalRecord()->exists()) {
            return redirect()
                ->route('psychologist.clinical-records.show', $client);
        }

        return view('psychologist.clinical-records.create', [
            'client' => $client,
            'record' => new ClinicalRecord([
                'status' => ClinicalRecord::STATUS_ACTIVE,
            ]),
        ]);
    }

    public function store(Request $request, User $client): RedirectResponse
    {
        $this->ensureClient($client);

        if ($client->clinicalRecord()->exists()) {
            return redirect()
                ->route('psychologist.clinical-records.show', $client)
                ->with('status', 'پرونده این مراجع قبلاً ایجاد شده است.');
        }

        $validated = $this->validatedData($request);

        $record = DB::transaction(function () use ($client, $validated): ClinicalRecord {
            return ClinicalRecord::query()->create([
                ...$validated,
                'client_id' => $client->id,
            ]);
        });

        return redirect()
            ->route('psychologist.clinical-records.show', $client)
            ->with('status', 'پرونده بالینی با موفقیت ایجاد شد.');
    }

    public function edit(ClinicalRecord $clinicalRecord): View
    {
        $clinicalRecord->load('client');
                $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        return view('psychologist.clinical-records.edit', [
            'client' => $clinicalRecord->client,
            'record' => $clinicalRecord,
        ]);
    }

    public function update(
        Request $request,
        ClinicalRecord $clinicalRecord,
    ): RedirectResponse {
        $clinicalRecord->load('client');
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);
        $clinicalRecord->update(
            $this->validatedData($request),
        );

        return redirect()
            ->route(
                'psychologist.clinical-records.show',
                $clinicalRecord->client,
            )
            ->with('status', 'پرونده بالینی با موفقیت به‌روزرسانی شد.');
    }
    public function storeManualAppointment(
        Request $request,
        User $client,
        ManualAppointmentService $service,
    ): RedirectResponse {
        $this->ensureClient($client);

        $data = $request->only([
            'date',
            'time',
            'duration_minutes',
            'session_type',
            'manual_payment_method',
            'amount',
        ]);

        // فرم جدید تاریخ شمسی می‌فرستد.
        // قرارداد قبلی تاریخ میلادی برای درخواست‌های موجود حفظ می‌شود.
        if ($request->exists('date_jalali')) {
            $validated = $request->validate([
                'date_jalali' => ['required', 'string', 'max:10'],
            ], [
                'date_jalali.required' => 'تاریخ جلسه را وارد کنید.',
                'date_jalali.string' => 'تاریخ جلسه معتبر نیست.',
                'date_jalali.max' => 'تاریخ را به صورت سال/ماه/روز وارد کنید.',
            ]);

            try {
                $data['date'] = \App\Support\PersianDate::toGregorian(
                    $validated['date_jalali'],
                );
            } catch (\InvalidArgumentException $exception) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date_jalali' => 'تاریخ شمسی معتبر وارد کنید؛ مانند ۱۴۰۵/۰۸/۰۵.',
                ]);
            }
        }

        $appointment = $service->create(
            $request->user(),
            $client,
            $data,
        );

        return redirect()
            ->route('psychologist.clinical-records.show', $client)
            ->with(
                'status',
                'جلسه با کد پیگیری '.$appointment->tracking_code.' ثبت شد.',
            );
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'gender' => ['nullable', 'string', 'max:30'],
            'family_status_and_living_conditions' => ['nullable', 'string', 'max:10000'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'employment_status' => ['nullable', 'string', 'max:50'],

            'presenting_problem' => ['nullable', 'string', 'max:10000'],
            'problem_onset' => ['nullable', 'string', 'max:255'],
            'previous_treatment' => ['nullable', 'string', 'max:10000'],
            'important_medical_history' => ['nullable', 'string', 'max:10000'],
            'current_medications' => ['nullable', 'string', 'max:10000'],

            'suicide_risk' => [
                'nullable',
                'string',
                Rule::in(['none', 'low', 'medium', 'high']),
            ],
            'self_harm_risk' => [
                'nullable',
                'string',
                Rule::in(['none', 'low', 'medium', 'high']),
            ],
            'harm_to_others_risk' => [
                'nullable',
                'string',
                Rule::in(['none', 'low', 'medium', 'high']),
            ],
            'overall_risk_level' => [
                'nullable',
                'string',
                Rule::in(['none', 'low', 'medium', 'high']),
            ],
            'safety_actions' => ['nullable', 'string', 'max:10000'],

            'important_life_history' => ['nullable', 'string', 'max:10000'],
            'important_relationships_and_support' => ['nullable', 'string', 'max:10000'],
            'values_and_personal_resources' => ['nullable', 'string', 'max:10000'],

            'clinical_observations' => ['nullable', 'string', 'max:10000'],
            'clinical_formulation' => ['nullable', 'string', 'max:10000'],
            'treatment_approach' => ['nullable', 'string', 'max:10000'],
            'treatment_goals' => ['nullable', 'string', 'max:10000'],
            'treatment_stage' => ['nullable', 'string', 'max:50'],
            'treatment_plan_and_schedule' => ['nullable', 'string', 'max:10000'],

            'status' => [
                'required',
                'string',
                Rule::in([
                    ClinicalRecord::STATUS_ACTIVE,
                    ClinicalRecord::STATUS_CLOSED,
                ]),
            ],
        ]);
    }

    private function ensureClient(User $client): void
    {
        abort_unless(auth()->user()?->isPsychologist(), 404);

        abort_unless(
            $client->isClient()
            && (
                (int) $client->created_by_psychologist_id === (int) auth()->id()
                || $client->clientAppointments()
                    ->where('psychologist_id', auth()->id())
                    ->exists()
            ),
            404,
        );
    }

    private function ensureRecordBelongsToCurrentPsychologist(
        ClinicalRecord $clinicalRecord,
    ): void {

        abort_unless(auth()->user()?->isPsychologist(), 404);

        $client = $clinicalRecord->client;

        abort_unless(
            $client !== null
            && $client->isClient()
            && (
                (int) $client->created_by_psychologist_id === (int) auth()->id()
                || $client->clientAppointments()
                    ->where('psychologist_id', auth()->id())
                    ->exists()
            ),
            404,
        );
    }

}
