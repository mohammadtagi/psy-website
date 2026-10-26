<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\ClinicalNote;
use App\Models\ClinicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalNoteController extends Controller
{
    public function create(ClinicalRecord $clinicalRecord): View
    {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        return view('psychologist.clinical-notes.create', [
            'record' => $clinicalRecord,
            'client' => $clinicalRecord->client,
            'note' => new ClinicalNote(),
        ]);
    }

    public function store(
        Request $request,
        ClinicalRecord $clinicalRecord,
    ): RedirectResponse {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        $data = $this->validatedData($request);

        $note = new ClinicalNote();
        $note->clinical_record_id = $clinicalRecord->id;
        $note->psychologist_id = $request->user()->id;
        $note->private_note = $data['private_note'];

        $this->clearLegacySessionFields($note);

        $note->save();

        return redirect()
            ->route('psychologist.clinical-records.show', $clinicalRecord->client)
            ->with('status', 'یادداشت محرمانه ثبت شد.');
    }

    public function edit(ClinicalNote $clinicalNote): View
    {
        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);

        $record = $clinicalNote->clinicalRecord;

        return view('psychologist.clinical-notes.edit', [
            'record' => $record,
            'client' => $record->client,
            'note' => $clinicalNote,
        ]);
    }

    public function update(
        Request $request,
        ClinicalNote $clinicalNote,
    ): RedirectResponse {
        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);

        $data = $this->validatedData($request);

        $clinicalNote->private_note = $data['private_note'];
        $this->clearLegacySessionFields($clinicalNote);
        $clinicalNote->save();

        return redirect()
            ->route(
                'psychologist.clinical-records.show',
                $clinicalNote->clinicalRecord->client,
            )
            ->with('status', 'یادداشت محرمانه ویرایش شد.');
    }

    public function destroy(ClinicalNote $clinicalNote): RedirectResponse
    {
        $this->ensureNoteBelongsToCurrentPsychologist($clinicalNote);

        $client = $clinicalNote->clinicalRecord->client;

        $clinicalNote->delete();

        return redirect()
            ->route('psychologist.clinical-records.show', $client)
            ->with('status', 'یادداشت محرمانه حذف شد.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'private_note' => ['required', 'string', 'max:20000'],
        ]);
    }

    private function clearLegacySessionFields(ClinicalNote $note): void
    {
        $note->appointment_id = null;
        $note->treatment_plan_id = null;
        $note->treatment_plan_stage_id = null;
        $note->session_at = null;

        // فقط برای سازگاری با ستون قدیمی؛ در رابط کاربری کاربردی ندارد.
        $note->session_type = ClinicalNote::SESSION_TYPE_FREE;
    }

    private function ensureRecordBelongsToCurrentPsychologist(
        ClinicalRecord $clinicalRecord,
    ): void {
        $user = auth()->user();

        abort_unless($user?->isPsychologist(), 404);

        $client = $clinicalRecord->client;

        abort_unless(
            $client !== null
            && $client->isClient()
            && (
                (int) $client->created_by_psychologist_id === (int) $user->id
                || $client->clientAppointments()
                    ->where('psychologist_id', $user->id)
                    ->exists()
            ),
            404,
        );
    }

    private function ensureNoteBelongsToCurrentPsychologist(
        ClinicalNote $clinicalNote,
    ): void {
        $user = auth()->user();

        abort_unless(
            $user?->isPsychologist()
            && (int) $clinicalNote->psychologist_id === (int) $user->id,
            404,
        );

        $record = $clinicalNote->clinicalRecord;

        abort_unless($record !== null, 404);

        $this->ensureRecordBelongsToCurrentPsychologist($record);
    }
}
