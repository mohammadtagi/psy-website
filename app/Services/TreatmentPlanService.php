<?php

namespace App\Services;

use App\Models\ClinicalRecord;
use App\Models\TreatmentPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TreatmentPlanService
{

    private const STAGE_NUMBERS = [
        TreatmentPlan::STAGE_ROOT_CAUSE,
        TreatmentPlan::STAGE_SKILLS_AND_ACTION,
        TreatmentPlan::STAGE_STABILIZATION,
    ];

    public function create(
        ClinicalRecord $clinicalRecord,
        array          $data,
        int            $psychologistId,
    ): TreatmentPlan
    {
        return DB::transaction(function () use (
            $clinicalRecord,
            $data,
            $psychologistId,
        ): TreatmentPlan {
            $clinicalRecord = ClinicalRecord::query()
                ->whereKey($clinicalRecord->id)
                ->lockForUpdate()
                ->firstOrFail();

            $activePlanExists = TreatmentPlan::query()
                ->where('client_id', $clinicalRecord->client_id)
                ->where('psychologist_id', $psychologistId)
                ->where('status', TreatmentPlan::STATUS_ACTIVE)
                ->lockForUpdate()
                ->exists();

            if ($activePlanExists) {
                throw ValidationException::withMessages([
                    'treatment_plan' => 'این مراجع در حال حاضر یک نقشه درمان فعال دارد.',
                ]);
            }

            if ((int)$clinicalRecord->client_id === $psychologistId) {
                throw ValidationException::withMessages([
                    'clinical_record' => 'پرونده انتخاب‌شده معتبر نیست.',
                ]);
            }

            $plan = TreatmentPlan::query()->create([
                'psychologist_id' => $psychologistId,
                'client_id' => $clinicalRecord->client_id,
                'clinical_record_id' => $clinicalRecord->id,
                'title' => $data['title'],
                'main_problem' => $data['main_problem'] ?? null,
                'status' => TreatmentPlan::STATUS_ACTIVE,
                'active_plan_guard' => $clinicalRecord->client_id,
                'current_stage' => TreatmentPlan::STAGE_ROOT_CAUSE,
                'status_reason' => null,
            ]);

            $stages = [
                [
                    'stage_number' => TreatmentPlan::STAGE_ROOT_CAUSE,
                    'title' => 'شناخت و ریشه‌یابی',
                ],
                [
                    'stage_number' => TreatmentPlan::STAGE_SKILLS_AND_ACTION,
                    'title' => 'کسب مهارت/تمرین و اقدام برای تغییر',
                ],
                [
                    'stage_number' => TreatmentPlan::STAGE_STABILIZATION,
                    'title' => 'تثبیت حال خوب و استقلال',
                ],
            ];

            foreach ($stages as $stage) {
                $plan->stages()->create([
                    ...$stage,
                    'goal' => null,
                    'estimated_sessions' => null,
                ]);
            }

            return $plan->load('stages');
        });
    }

    public function archive(
        TreatmentPlan $plan,
        string        $status,
        ?string       $statusReason,
    ): TreatmentPlan
    {
        if (!in_array($status, [
            TreatmentPlan::STATUS_COMPLETED,
            TreatmentPlan::STATUS_DROPPED_OUT,
            TreatmentPlan::STATUS_REFERRED,
        ], true)) {
            throw ValidationException::withMessages([
                'status' => 'وضعیت آرشیو معتبر نیست.',
            ]);
        }
        if ($statusReason === null || trim($statusReason) === '') {
            throw ValidationException::withMessages([
                'status_reason' => 'برای آرشیو کردن نقشه، ثبت علت الزامی است.',
            ]);
        }

        $this->ensureActive($plan);

        return DB::transaction(function () use (
            $plan,
            $status,
            $statusReason,
        ): TreatmentPlan {
            $plan = TreatmentPlan::query()
                ->whereKey($plan->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureActive($plan);

            $plan->update([
                'status' => $status,
                'active_plan_guard' => null,
                'status_reason' => $statusReason,
            ]);

            return $plan->fresh('stages');
        });
    }

    private function ensureActive(TreatmentPlan $plan): void
    {
        if (!$plan->isActive()) {
            throw ValidationException::withMessages([
                'treatment_plan' => 'نقشه‌های آرشیوشده قابل ویرایش نیستند.',
            ]);
        }
    }

    public function update(
        TreatmentPlan $plan,
        array         $data,
    ): TreatmentPlan
    {
        $this->ensureActive($plan);

        return DB::transaction(function () use ($plan, $data): TreatmentPlan {
            $plan = TreatmentPlan::query()
                ->whereKey($plan->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureActive($plan);

            $this->validateCurrentStage($data['current_stage'] ?? null);
            $this->validateStages($data['stages'] ?? []);

            $plan->update([
                'title' => $data['title'],
                'main_problem' => $data['main_problem'] ?? null,
                'current_stage' => (int)$data['current_stage'],
            ]);
            foreach (self::STAGE_NUMBERS as $stageNumber) {
                $stageData = $this->getStageData($data['stages'], $stageNumber);

                if ($stageData === null) {
                    throw ValidationException::withMessages([
                        'stages' => 'اطلاعات هر سه مرحله درمان باید ارسال شود.',
                    ]);
                }

                $updatedRows = $plan->stages()
                    ->where('stage_number', $stageNumber)
                    ->update([
                        'goal' => $stageData['goal'] ?? null,
                        'estimated_sessions' => $stageData['estimated_sessions'] ?? null,
                    ]);

                if ($updatedRows !== 1) {
                    throw ValidationException::withMessages([
                        'stages' => 'ساختار مراحل این نقشه درمان ناقص است.',
                    ]);
                }
            }



            return $plan->load('stages');
        });
    }


    private function validateCurrentStage(mixed $currentStage): void
    {
        if (
            filter_var($currentStage, FILTER_VALIDATE_INT) === false
            || !in_array((int) $currentStage, self::STAGE_NUMBERS, true)
        ) {
            throw ValidationException::withMessages([
                'current_stage' => 'مرحله فعلی معتبر نیست.',
            ]);
        }
    }


    private function validateStages(array $stages): void
    {
        $providedStageNumbers = array_map(
            static fn($stageNumber): int => (int)$stageNumber,
            array_keys($stages),
        );

        sort($providedStageNumbers);

        $expectedStageNumbers = self::STAGE_NUMBERS;
        sort($expectedStageNumbers);

        if ($providedStageNumbers !== $expectedStageNumbers) {
            throw ValidationException::withMessages([
                'stages' => 'اطلاعات هر سه مرحله درمان باید ارسال شود.',
            ]);
        }

        foreach (self::STAGE_NUMBERS as $stageNumber) {
            $stageData = $stages[$stageNumber];

            if (!is_array($stageData)) {
                throw ValidationException::withMessages([
                    "stages.{$stageNumber}" => 'اطلاعات مرحله معتبر نیست.',
                ]);
            }

            $estimatedSessions = $stageData['estimated_sessions'] ?? null;

            if (
                array_key_exists('goal', $stageData)
                && $stageData['goal'] !== null
                && !is_string($stageData['goal'])
            ) {
                throw ValidationException::withMessages([
                    "stages.{$stageNumber}.goal" => 'هدف مرحله باید متن باشد.',
                ]);
            }

            if (
                $estimatedSessions !== null
                && (
                    filter_var($estimatedSessions, FILTER_VALIDATE_INT) === false
                    || (int) $estimatedSessions < 1
                )
            ) {

                throw ValidationException::withMessages([
                    "stages.{$stageNumber}.estimated_sessions"
                    => 'تعداد جلسات تخمینی باید حداقل یک باشد.',
                ]);
            }
        }
    }

    private function getStageData(array $stages, int $stageNumber): ?array
    {
        $stageData = $stages[$stageNumber]
            ?? $stages[(string)$stageNumber]
            ?? null;

        return is_array($stageData) ? $stageData : null;
    }


}
