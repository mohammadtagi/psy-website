<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\ClinicalRecord;
use App\Models\TreatmentPlan;
use App\Services\TreatmentPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TreatmentPlanController extends Controller
{
    public function store(
        Request              $request,
        ClinicalRecord       $clinicalRecord,
        TreatmentPlanService $service,
    ): RedirectResponse
    {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'main_problem' => ['nullable', 'string', 'max:20000'],
        ]);

        $service->create(
            $clinicalRecord,
            $validated,
            (int)auth()->id(),
        );

        return redirect()
            ->route('psychologist.clinical-records.show', $clinicalRecord->client)
            ->with('status', 'نقشه درمان با موفقیت ایجاد شد.');
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


    public function create(ClinicalRecord $clinicalRecord): View
    {
        $this->ensureRecordBelongsToCurrentPsychologist($clinicalRecord);

        $clinicalRecord->load('client');

        return view('psychologist.treatment-plans.create', [
            'record' => $clinicalRecord,
            'client' => $clinicalRecord->client,
            'plan' => new TreatmentPlan([
                'status' => TreatmentPlan::STATUS_ACTIVE,
                'current_stage' => TreatmentPlan::STAGE_ROOT_CAUSE,
            ]),
        ]);
    }

    public function edit(TreatmentPlan $treatmentPlan): View
    {
        $this->ensurePlanBelongsToPsychologist($treatmentPlan);

        $treatmentPlan->load([
            'client',
            'clinicalRecord',
            'stages',
        ]);

        return view('psychologist.treatment-plans.edit', [
            'plan' => $treatmentPlan,
            'client' => $treatmentPlan->client,
            'record' => $treatmentPlan->clinicalRecord,
        ]);
    }

    private function ensurePlanBelongsToPsychologist(
        TreatmentPlan $treatmentPlan,
    ): void {
        $treatmentPlan->loadMissing('clinicalRecord');

        abort_unless(
            (int) $treatmentPlan->psychologist_id === (int) auth()->id()
            && $treatmentPlan->clinicalRecord !== null
            && (int) $treatmentPlan->client_id
            === (int) $treatmentPlan->clinicalRecord->client_id,
            404,
        );
    }


    public function update(
        Request              $request,
        TreatmentPlan        $treatmentPlan,
        TreatmentPlanService $service,
    ): RedirectResponse
    {
        $this->ensurePlanBelongsToPsychologist($treatmentPlan);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'main_problem' => ['nullable', 'string'],
            'current_stage' => [
                'required',
                'integer',
                Rule::in([
                    TreatmentPlan::STAGE_ROOT_CAUSE,
                    TreatmentPlan::STAGE_SKILLS_AND_ACTION,
                    TreatmentPlan::STAGE_STABILIZATION,
                ]),
            ],
            'stages' => ['required', 'array', 'size:3'],
            'stages.1' => ['required', 'array'],
            'stages.2' => ['required', 'array'],
            'stages.3' => ['required', 'array'],
            'stages.1.goal' => ['nullable', 'string'],
            'stages.2.goal' => ['nullable', 'string'],
            'stages.3.goal' => ['nullable', 'string'],
            'stages.1.estimated_sessions' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'stages.2.estimated_sessions' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'stages.3.estimated_sessions' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
        ]);

        $service->update($treatmentPlan, $validated);

        return redirect()
            ->route(
                'psychologist.clinical-records.show',
                $treatmentPlan->client,
            )
            ->with('status', 'نقشه درمان با موفقیت به‌روزرسانی شد.');
    }

    public function archive(
        Request              $request,
        TreatmentPlan        $treatmentPlan,
        TreatmentPlanService $service,
    ): RedirectResponse
    {
        $this->ensurePlanBelongsToPsychologist($treatmentPlan);

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in([
                    TreatmentPlan::STATUS_COMPLETED,
                    TreatmentPlan::STATUS_DROPPED_OUT,
                    TreatmentPlan::STATUS_REFERRED,
                ]),
            ],
            'status_reason' => ['required', 'string', 'max:20000'],
        ]);

        $service->archive(
            $treatmentPlan,
            $validated['status'],
            $validated['status_reason'] ?? null,
        );

        return redirect()
            ->route(
                'psychologist.clinical-records.show',
                $treatmentPlan->client,
            )
            ->with('status', 'نقشه درمان آرشیو شد.');
    }
}
