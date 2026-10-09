<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicalNote;
use App\Models\ClinicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Support\PersianDate;
use InvalidArgumentException;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanStage;
use Illuminate\Validation\Rule;

class ClinicalNoteController extends Controller
{
    public function store(
        Request        $request,
        ClinicalRecord $clinicalRecord,
    ): RedirectResponse
    {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        $clinicalRecord->load('client');

        $validated = $this->validatedData(
            $request,
            $clinicalRecord,
        );

        DB::transaction(function () use ($clinicalRecord, $validated): ClinicalNote {
            return ClinicalNote::query()->create([
                ...$validated,
                'clinical_record_id' => $clinicalRecord->id,
            ]);
        });

        return redirect()
            ->route('psychologist.clinical-records.show', $clinicalRecord->client)
            ->with('status', 'یادداشت بالینی با موفقیت ثبت شد.');
    }

    private function ensureRecordBelongsToCurrentPsychologist(
        ClinicalRecord $clinicalRecord,
    ): void
    {
        abort_unless(
            $clinicalRecord->client()
                ->where('role', 'client')
                ->whereHas('clientAppointments', function ($query): void {
                    $query->where('psychologist_id', auth()->id());
                })
                ->exists(),
            404,
        );
    }

    private function validatedData(
        Request        $request,
        ClinicalRecord $clinicalRecord,
        ?ClinicalNote  $currentNote = null,
    ): array
    {

        $sessionAt = trim((string)$request->input('session_at'));

        if ($sessionAt === '') {
            $request->merge([
                'session_at' => null,
            ]);
        } else {
            try {
                $request->merge([
                    'session_at' => PersianDate::toGregorianDateTime($sessionAt),
                ]);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'session_at' => $exception->getMessage(),
                ]);
            }
        }


        $validated = $request->validate([
            'appointment_id' => [
                'nullable',
                'integer',
            ],

            'session_type' => [
                'required',
                'string',
                Rule::in([
                    ClinicalNote::SESSION_TYPE_PLAN,
                    ClinicalNote::SESSION_TYPE_FREE,
                ]),
            ],
            'treatment_plan_id' => [
                'nullable',
                'integer',
                'exists:treatment_plans,id',
            ],
            'treatment_plan_stage_id' => [
                'nullable',
                'integer',
                'exists:treatment_plan_stages,id',
            ],

            'session_at' => [
                'nullable',
                'date',
            ],
            'summary' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'client_condition' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'interventions' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'homework_and_next_plan' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'private_note' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ]);


        $sessionType = $validated['session_type'];
        $planId = $validated['treatment_plan_id'] ?? null;
        $stageId = $validated['treatment_plan_stage_id'] ?? null;

        if ($sessionType === ClinicalNote::SESSION_TYPE_PLAN && !$planId) {
            throw ValidationException::withMessages([
                'treatment_plan_id' => 'برای جلسه مسیر درمان، انتخاب نقشه درمان الزامی است.',
            ]);
        }

        if ($sessionType === ClinicalNote::SESSION_TYPE_FREE) {
            $validated['treatment_plan_stage_id'] = null;
        }

        if (
            $sessionType === ClinicalNote::SESSION_TYPE_PLAN
            && $planId
        ) {
            $plan = TreatmentPlan::query()
                ->whereKey($planId)
                ->where('clinical_record_id', $clinicalRecord->id)
                ->where('client_id', $clinicalRecord->client_id)
                ->where('psychologist_id', auth()->id())
                ->first();

            if (!$plan) {
                throw ValidationException::withMessages([
                    'treatment_plan_id' => 'نقشه درمان انتخاب‌شده معتبر نیست.',
                ]);
            }

            if ($sessionType === ClinicalNote::SESSION_TYPE_PLAN && !$plan->isActive()) {
                throw ValidationException::withMessages([
                    'treatment_plan_id' => 'ثبت جلسه جدید فقط برای نقشه درمان فعال امکان‌پذیر است.',
                ]);
            }

            if ($stageId) {
                $stageExists = TreatmentPlanStage::query()
                    ->whereKey($stageId)
                    ->where('treatment_plan_id', $plan->id)
                    ->exists();

                if (!$stageExists) {
                    throw ValidationException::withMessages([
                        'treatment_plan_stage_id' => 'مرحله انتخاب‌شده متعلق به این نقشه درمان نیست.',
                    ]);
                }
            }

            if (
                $sessionType === ClinicalNote::SESSION_TYPE_PLAN
                && !$stageId
            ) {
                throw ValidationException::withMessages([
                    'treatment_plan_stage_id' => 'برای جلسه مسیر درمان، انتخاب مرحله الزامی است.',
                ]);
            }
        }
        if ($sessionType === ClinicalNote::SESSION_TYPE_FREE) {
            $validated['treatment_plan_id'] = null;
            $validated['treatment_plan_stage_id'] = null;
        }

        if ($sessionType === ClinicalNote::SESSION_TYPE_PLAN && !$stageId) {
            $validated['treatment_plan_stage_id'] = null;
        }


        if (
            array_key_exists('appointment_id', $validated)
            && $validated['appointment_id'] !== null
        ) {
            $appointment = Appointment::query()
                ->whereKey($validated['appointment_id'])
                ->where('client_id', $clinicalRecord->client_id)
                ->where('psychologist_id', auth()->id())
                ->where('status', Appointment::STATUS_COMPLETED)
                ->first();

            if (!$appointment) {
                throw ValidationException::withMessages([
                    'appointment_id' => 'فقط نوبت‌های تکمیل‌شده قابل اتصال به یادداشت بالینی هستند.',
                ]);
            }


            $existingNoteQuery = ClinicalNote::query()
                ->where('appointment_id', $appointment->id);

            if ($currentNote) {
                $existingNoteQuery->where('id', '!=', $currentNote->id);
            }

            if ($existingNoteQuery->exists()) {
                throw ValidationException::withMessages([
                    'appointment_id' => 'برای این نوبت قبلاً یادداشت ثبت شده است.',
                ]);
            }
        }


        return $validated;
    }

    public function create(ClinicalRecord $clinicalRecord): View
    {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        $clinicalRecord->load('client');

        $appointments = $this->appointmentsForRecord($clinicalRecord);
        $plans = $clinicalRecord->treatmentPlans()
            ->with('stages')
            ->latest('id')
            ->get();

        return view('psychologist.clinical-notes.create', [
            'record' => $clinicalRecord,
            'client' => $clinicalRecord->client,
            'appointments' => $appointments,
            'plans' => $plans,

            'note' => new ClinicalNote([
                'session_at' => now(),
                'session_type' => ClinicalNote::SESSION_TYPE_FREE,
            ]),
        ]);
    }

    private function appointmentsForRecord(
        ClinicalRecord $clinicalRecord,
        ?ClinicalNote  $currentNote = null,
    )
    {
        $query = Appointment::query()
            ->where('client_id', $clinicalRecord->client_id)
            ->where('psychologist_id', auth()->id())
            ->where('status', Appointment::STATUS_COMPLETED)
            ->orderByDesc('starts_at')
            ->orderByDesc('id');

        $query->where(function ($query) use ($currentNote): void {
            $query->whereDoesntHave('clinicalNote');

            if ($currentNote?->appointment_id) {
                $query->orWhereKey($currentNote->appointment_id);
            }
        });

        return $query->get();
    }

    public function edit(ClinicalNote $clinicalNote): View
    {
        $clinicalNote->load([
            'clinicalRecord.client',
            'appointment',
        ]);

        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);

        $record = $clinicalNote->clinicalRecord;
        $plans = $record->treatmentPlans()
            ->with('stages')
            ->latest('id')
            ->get();
        return view('psychologist.clinical-notes.edit', [
            'record' => $record,
            'client' => $record->client,
            'note' => $clinicalNote,
            'appointments' => $this->appointmentsForRecord($record, $clinicalNote),
            'plans' => $plans,
        ]);
    }

    private function ensureNoteBelongsToCurrentPsychologist(
        ClinicalNote $clinicalNote,
    ): void
    {
        abort_unless(
            $clinicalNote->clinicalRecord
                ?->client
                ?->clientAppointments()
                ->where('psychologist_id', auth()->id())
                ->exists()
            && $clinicalNote->clinicalRecord->client_id,
            404,
        );
    }

    public function update(
        Request      $request,
        ClinicalNote $clinicalNote,
    ): RedirectResponse
    {

        $clinicalNote->load('clinicalRecord.client');
        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);
        $clinicalNote->update(
            $this->validatedData(
                $request,
                $clinicalNote->clinicalRecord,
                $clinicalNote,
            ),
        );

        return redirect()
            ->route(
                'psychologist.clinical-records.show',
                $clinicalNote->clinicalRecord->client,
            )
            ->with('status', 'یادداشت بالینی با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(ClinicalNote $clinicalNote): RedirectResponse
    {
        $clinicalNote->load('clinicalRecord.client');
        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);
        $client = $clinicalNote->clinicalRecord->client;

        $clinicalNote->delete();

        return redirect()
            ->route('psychologist.clinical-records.show', $client)
            ->with('status', 'یادداشت بالینی حذف شد.');
    }
}
