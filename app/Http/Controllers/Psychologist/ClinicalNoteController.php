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

class ClinicalNoteController extends Controller
{
    public function create(ClinicalRecord $clinicalRecord): View
    {
        $clinicalRecord->load('client');

        $appointments = $this->appointmentsForRecord($clinicalRecord);

        return view('psychologist.clinical-notes.create', [
            'record' => $clinicalRecord,
            'client' => $clinicalRecord->client,
            'appointments' => $appointments,
            'note' => new ClinicalNote([
                'session_at' => now(),
            ]),
        ]);
    }

    public function store(
        Request $request,
        ClinicalRecord $clinicalRecord,
    ): RedirectResponse {
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

    public function edit(ClinicalNote $clinicalNote): View
    {
        $clinicalNote->load([
            'clinicalRecord.client',
            'appointment',
        ]);

        $record = $clinicalNote->clinicalRecord;

        return view('psychologist.clinical-notes.edit', [
            'record' => $record,
            'client' => $record->client,
            'note' => $clinicalNote,
            'appointments' => $this->appointmentsForRecord($record),
        ]);
    }

    public function update(
        Request $request,
        ClinicalNote $clinicalNote,
    ): RedirectResponse {
        $clinicalNote->load('clinicalRecord.client');

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

        $client = $clinicalNote->clinicalRecord->client;

        $clinicalNote->delete();

        return redirect()
            ->route('psychologist.clinical-records.show', $client)
            ->with('status', 'یادداشت بالینی حذف شد.');
    }

    private function appointmentsForRecord(
        ClinicalRecord $clinicalRecord,
    ) {
        return Appointment::query()
            ->where('client_id', $clinicalRecord->client_id)
            ->where('psychologist_id', auth()->id())
            ->whereIn('status', [
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_NO_SHOW,
            ])
            ->with('clinicalNote:id,appointment_id')
            ->orderByDesc('starts_at')
            ->get();
    }

    private function validatedData(
        Request $request,
        ClinicalRecord $clinicalRecord,
        ?ClinicalNote $currentNote = null,
    ): array {

        $sessionAt = trim((string) $request->input('session_at'));

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

        if (
            array_key_exists('appointment_id', $validated)
            && $validated['appointment_id'] !== null
        ) {
            $appointment = Appointment::query()
                ->whereKey($validated['appointment_id'])
                ->where('client_id', $clinicalRecord->client_id)
                ->where('psychologist_id', auth()->id())
                ->first();

            if (! $appointment) {
                throw ValidationException::withMessages([
                    'appointment_id' => 'نوبت انتخاب‌شده متعلق به این مراجع نیست.',
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
}
