<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicalNote;
use App\Models\ClinicalRecord;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TreatmentPlanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_psychologist_can_create_a_treatment_plan(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.treatment-plans.store', [
                'clinicalRecord' => $record,
            ]),
            [
                'title' => 'نقشه درمان اضطراب',
                'main_problem' => 'اضطراب و نگرانی مداوم',
            ],
        );

        $response
            ->assertRedirect(
                route('psychologist.clinical-records.show', $client),
            )
            ->assertSessionHas('status');

        $plan = TreatmentPlan::query()->sole();

        $this->assertSame($psychologist->id, $plan->psychologist_id);
        $this->assertSame($client->id, $plan->client_id);
        $this->assertSame($record->id, $plan->clinical_record_id);
        $this->assertSame('نقشه درمان اضطراب', $plan->title);
        $this->assertSame(TreatmentPlan::STATUS_ACTIVE, $plan->status);
        $this->assertSame($client->id, $plan->active_plan_guard);

        $this->assertSame(
            [1, 2, 3],
            TreatmentPlanStage::query()
                ->where('treatment_plan_id', $plan->id)
                ->orderBy('stage_number')
                ->pluck('stage_number')
                ->map(static fn ($value): int => (int) $value)
                ->all(),
        );
    }

    public function test_second_active_plan_for_same_psychologist_and_client_is_rejected(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();

        $firstPlan = $this->createPlan(
            $psychologist,
            $client,
            $record,
        );

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.treatment-plans.store', [
                'clinicalRecord' => $record,
            ]),
            [
                'title' => 'نقشه درمان دوم',
                'main_problem' => 'تلاش برای ایجاد نقشه موازی',
            ],
        );

        $response->assertSessionHasErrors();

        $this->assertDatabaseCount('treatment_plans', 1);
        $this->assertDatabaseHas('treatment_plans', [
            'id' => $firstPlan->id,
            'status' => TreatmentPlan::STATUS_ACTIVE,
        ]);
    }

    public function test_database_guard_rejects_duplicate_active_plan(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();

        $this->createPlan($psychologist, $client, $record);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('treatment_plans')->insert([
            'psychologist_id' => $psychologist->id,
            'client_id' => $client->id,
            'clinical_record_id' => $record->id,
            'title' => 'نقشه تکراری',
            'main_problem' => null,
            'status' => TreatmentPlan::STATUS_ACTIVE,
            'active_plan_guard' => $client->id,
            'current_stage' => TreatmentPlan::STAGE_ROOT_CAUSE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_treatment_plan_update_requires_three_stages(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);

        $response = $this->actingAs($psychologist)->put(
            route('psychologist.treatment-plans.update', [
                'treatmentPlan' => $plan,
            ]),
            [
                'title' => 'نقشه به‌روزشده',
                'main_problem' => 'مسئله به‌روزشده',
                'current_stage' => TreatmentPlan::STAGE_SKILLS_AND_ACTION,
                'stages' => [
                    1 => [
                        'goal' => 'هدف مرحله اول',
                        'estimated_sessions' => 4,
                    ],
                    2 => [
                        'goal' => 'هدف مرحله دوم',
                        'estimated_sessions' => 6,
                    ],
                ],
            ],
        );

        $response
            ->assertSessionHasErrors([
                'stages',
                'stages.3',
            ]);
    }

    public function test_psychologist_can_update_plan_and_all_three_stages(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);
        $this->createStages($plan);

        $response = $this->actingAs($psychologist)->put(
            route('psychologist.treatment-plans.update', [
                'treatmentPlan' => $plan,
            ]),
            $this->planUpdatePayload(),
        );

        $response
            ->assertRedirect(
                route('psychologist.clinical-records.show', $client),
            )
            ->assertSessionHas('status');

        $this->assertDatabaseHas('treatment_plans', [
            'id' => $plan->id,
            'title' => 'عنوان به‌روزشده',
            'main_problem' => 'مسئله به‌روزشده',
            'current_stage' => TreatmentPlan::STAGE_STABILIZATION,
        ]);

        foreach ([1, 2, 3] as $stageNumber) {
            $this->assertDatabaseHas('treatment_plan_stages', [
                'treatment_plan_id' => $plan->id,
                'stage_number' => $stageNumber,
                'goal' => "هدف به‌روزشده مرحله {$stageNumber}",
                'estimated_sessions' => $stageNumber + 3,
            ]);
        }
    }

    public function test_archive_requires_a_valid_status_and_reason(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);

        $response = $this->actingAs($psychologist)->patch(
            route('psychologist.treatment-plans.archive', [
                'treatmentPlan' => $plan,
            ]),
            [
                'status' => 'invalid-status',
                'status_reason' => '',
            ],
        );

        $response->assertSessionHasErrors([
            'status',
            'status_reason',
        ]);

        $this->assertSame(
            TreatmentPlan::STATUS_ACTIVE,
            $plan->fresh()->status,
        );
    }

    public function test_archiving_plan_releases_active_plan_guard(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);

        $response = $this->actingAs($psychologist)->patch(
            route('psychologist.treatment-plans.archive', [
                'treatmentPlan' => $plan,
            ]),
            [
                'status' => TreatmentPlan::STATUS_COMPLETED,
                'status_reason' => 'اهداف درمان تکمیل شد.',
            ],
        );

        $response
            ->assertRedirect(
                route('psychologist.clinical-records.show', $client),
            )
            ->assertSessionHas('status');

        $this->assertDatabaseHas('treatment_plans', [
            'id' => $plan->id,
            'status' => TreatmentPlan::STATUS_COMPLETED,
            'status_reason' => 'اهداف درمان تکمیل شد.',
            'active_plan_guard' => null,
        ]);
    }

    public function test_another_psychologist_cannot_edit_the_plan(): void
    {
        [$owner, $client, $record] = $this->clinicalContext();
        $otherPsychologist = User::factory()->psychologist()->create();
        $plan = $this->createPlan($owner, $client, $record);

        $response = $this->actingAs($otherPsychologist)->get(
            route('psychologist.treatment-plans.edit', [
                'treatmentPlan' => $plan,
            ]),
        );

        $response->assertNotFound();
    }

    public function test_psychologist_cannot_edit_a_plan_with_mismatched_record_client(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();

        $this->createCompletedAppointment($psychologist, $client);
        $record = $this->createClinicalRecord($otherClient);

        $plan = TreatmentPlan::withoutEvents(function () use (
            $psychologist,
            $client,
            $record,
        ): TreatmentPlan {
            return TreatmentPlan::query()->forceCreate([
                'psychologist_id' => $psychologist->id,
                'client_id' => $client->id,
                'clinical_record_id' => $record->id,
                'title' => 'نقشه نامعتبر',
                'status' => TreatmentPlan::STATUS_ACTIVE,
                'active_plan_guard' => $client->id,
                'current_stage' => TreatmentPlan::STAGE_ROOT_CAUSE,
            ]);
        });

        $response = $this->actingAs($psychologist)->get(
            route('psychologist.treatment-plans.edit', [
                'treatmentPlan' => $plan,
            ]),
        );

        $response->assertNotFound();
    }


    public function test_free_session_clears_plan_and_stage_ids(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);
        $stage = $this->createStages($plan)[0];

        $appointment = $this->createSessionAppointment(
            $psychologist,
            $client,
        );

        $appointment->forceFill([
            'therapy_session_type' => Appointment::THERAPY_TYPE_PLAN,
            'treatment_plan_id' => $plan->id,
            'treatment_plan_stage_id' => $stage->id,
        ])->save();

        $response = $this->actingAs($psychologist)->put(
            route('psychologist.appointments.session.update', $appointment),
            [
                'therapy_session_type' => Appointment::THERAPY_TYPE_FREE,
                'attendance_status' => Appointment::ATTENDANCE_PENDING,
                'treatment_plan_id' => $plan->id,
                'treatment_plan_stage_id' => $stage->id,
            ],
        );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('psychologist.appointments.session.show', $appointment),
            )
            ->assertSessionHas('status');

        $appointment->refresh();

        $this->assertNull($appointment->treatment_plan_id);
        $this->assertNull($appointment->treatment_plan_stage_id);
        $this->assertSame(
            Appointment::THERAPY_TYPE_FREE,
            $appointment->therapy_session_type,
        );

        $this->assertDatabaseCount('clinical_notes', 0);
    }

    public function test_plan_session_requires_a_plan_and_stage(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $appointment = $this->createSessionAppointment(
            $psychologist,
            $client,
        );

        $url = route(
            'psychologist.appointments.session.update',
            $appointment,
        );

        $withoutPlan = $this->actingAs($psychologist)->put($url, [
            'therapy_session_type' => Appointment::THERAPY_TYPE_PLAN,
            'attendance_status' => Appointment::ATTENDANCE_PENDING,
        ]);

        $withoutPlan->assertSessionHasErrors([
            'treatment_plan_id',
            'treatment_plan_stage_id',
        ]);

        $plan = $this->createPlan($psychologist, $client, $record);

        $withoutStage = $this->actingAs($psychologist)->put($url, [
            'therapy_session_type' => Appointment::THERAPY_TYPE_PLAN,
            'attendance_status' => Appointment::ATTENDANCE_PENDING,
            'treatment_plan_id' => $plan->id,
        ]);

        $withoutStage->assertSessionHasErrors('treatment_plan_stage_id');

        $this->assertSessionRemainsUnassigned($appointment);
    }

    public function test_plan_session_rejects_stage_belonging_to_another_plan(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $firstPlan = $this->createPlan($psychologist, $client, $record);
        $secondPlan = $this->createPlanForAnotherClient();
        $foreignStage = $this->createStages($secondPlan)[0];

        $appointment = $this->createSessionAppointment(
            $psychologist,
            $client,
        );

        $response = $this->actingAs($psychologist)->put(
            route('psychologist.appointments.session.update', $appointment),
            [
                'therapy_session_type' => Appointment::THERAPY_TYPE_PLAN,
                'attendance_status' => Appointment::ATTENDANCE_PENDING,
                'treatment_plan_id' => $firstPlan->id,
                'treatment_plan_stage_id' => $foreignStage->id,
            ],
        );

        $response->assertSessionHasErrors('treatment_plan_stage_id');

        $this->assertSessionRemainsUnassigned($appointment);
    }

    public function test_plan_session_cannot_be_added_to_an_archived_plan(): void
    {
        [$psychologist, $client, $record] = $this->clinicalContext();
        $plan = $this->createPlan($psychologist, $client, $record);
        $stage = $this->createStages($plan)[0];

        $plan->update([
            'status' => TreatmentPlan::STATUS_COMPLETED,
            'status_reason' => 'تکمیل‌شده',
            'active_plan_guard' => null,
        ]);

        $appointment = $this->createSessionAppointment(
            $psychologist,
            $client,
        );

        $response = $this->actingAs($psychologist)->put(
            route('psychologist.appointments.session.update', $appointment),
            [
                'therapy_session_type' => Appointment::THERAPY_TYPE_PLAN,
                'attendance_status' => Appointment::ATTENDANCE_PENDING,
                'treatment_plan_id' => $plan->id,
                'treatment_plan_stage_id' => $stage->id,
            ],
        );

        $response->assertSessionHasErrors('treatment_plan_id');

        $this->assertSessionRemainsUnassigned($appointment);
    }

    public function test_unrelated_psychologist_cannot_create_note_for_the_record(): void
    {
        [$owner, $client, $record] = $this->clinicalContext();
        $otherPsychologist = User::factory()->psychologist()->create();

        $response = $this->actingAs($otherPsychologist)->post(
            route('psychologist.clinical-notes.store', [
                'clinicalRecord' => $record,
            ]),
            [
                'private_note' => 'دسترسی غیرمجاز',
            ],

        );

        $response->assertNotFound();
        $this->assertDatabaseCount('clinical_notes', 0);
    }
    private function createSessionAppointment(
        User $psychologist,
        User $client,
    ): Appointment {
        $appointment = Appointment::query()
            ->where('psychologist_id', $psychologist->id)
            ->where('client_id', $client->id)
            ->sole();

        $appointment->forceFill([
            'status' => Appointment::STATUS_CONFIRMED,
            'therapy_session_type' => Appointment::THERAPY_TYPE_FREE,
            'attendance_status' => Appointment::ATTENDANCE_PENDING,
            'treatment_plan_id' => null,
            'treatment_plan_stage_id' => null,
        ])->save();

        return $appointment->refresh();
    }

    private function assertSessionRemainsUnassigned(
        Appointment $appointment,
    ): void {
        $appointment->refresh();

        $this->assertSame(
            Appointment::THERAPY_TYPE_FREE,
            $appointment->therapy_session_type,
        );
        $this->assertSame(
            Appointment::ATTENDANCE_PENDING,
            $appointment->attendance_status,
        );
        $this->assertNull($appointment->treatment_plan_id);
        $this->assertNull($appointment->treatment_plan_stage_id);
    }

    private function clinicalContext(): array
    {
        $psychologist = User::factory()->psychologist()->create();
        $client = User::factory()->client()->create();

        $this->createCompletedAppointment($psychologist, $client);
        $record = $this->createClinicalRecord($client);

        return [$psychologist, $client, $record];
    }

    private function createClinicalRecord(User $client): ClinicalRecord
    {
        return ClinicalRecord::query()->create([
            'client_id' => $client->id,
        ]);
    }

    private function createCompletedAppointment(
        User $psychologist,
        User $client,
    ): Appointment {
        return Appointment::factory()
            ->forPsychologist($psychologist)
            ->forClient($client)
            ->completed()
            ->create();
    }

    private function createPlan(
        User $psychologist,
        User $client,
        ClinicalRecord $record,
    ): TreatmentPlan {
        return TreatmentPlan::query()->create([
            'psychologist_id' => $psychologist->id,
            'client_id' => $client->id,
            'clinical_record_id' => $record->id,
            'title' => 'نقشه درمان',
            'main_problem' => 'مسئله اصلی',
            'status' => TreatmentPlan::STATUS_ACTIVE,
            'active_plan_guard' => $client->id,
            'current_stage' => TreatmentPlan::STAGE_ROOT_CAUSE,
        ]);
    }

    private function createPlanForAnotherClient(): TreatmentPlan
    {
        $psychologist = User::factory()->psychologist()->create();
        $client = User::factory()->client()->create();

        $this->createCompletedAppointment($psychologist, $client);
        $record = $this->createClinicalRecord($client);

        return $this->createPlan($psychologist, $client, $record);
    }

    private function createStages(TreatmentPlan $plan): array
    {
        return collect([1, 2, 3])->map(
            static fn (int $stageNumber): TreatmentPlanStage =>
            TreatmentPlanStage::query()->create([
                'treatment_plan_id' => $plan->id,
                'stage_number' => $stageNumber,
                'title' => "مرحله {$stageNumber}",
                'goal' => "هدف مرحله {$stageNumber}",
                'estimated_sessions' => 4,
            ]),
        )->all();
    }

    private function planUpdatePayload(): array
    {
        return [
            'title' => 'عنوان به‌روزشده',
            'main_problem' => 'مسئله به‌روزشده',
            'current_stage' => TreatmentPlan::STAGE_STABILIZATION,
            'stages' => [
                1 => [
                    'goal' => 'هدف به‌روزشده مرحله 1',
                    'estimated_sessions' => 4,
                ],
                2 => [
                    'goal' => 'هدف به‌روزشده مرحله 2',
                    'estimated_sessions' => 5,
                ],
                3 => [
                    'goal' => 'هدف به‌روزشده مرحله 3',
                    'estimated_sessions' => 6,
                ],
            ],
        ];
    }
}
