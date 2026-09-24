<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_records', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('client_id')
                ->unique()
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * مشخصات مراجع
             */
            $table->string('gender', 30)->nullable();
            $table->text('family_status_and_living_conditions')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employment_status', 50)->nullable();

            /*
             * مسئله و وضعیت فعلی
             */
            $table->text('presenting_problem')->nullable();
            $table->string('problem_onset')->nullable();
            $table->text('previous_treatment')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('important_medical_history')->nullable();

            /*
             * ایمنی و خطر
             */
            $table->string('suicide_risk', 30)->nullable();
            $table->string('self_harm_risk', 30)->nullable();
            $table->string('harm_to_others_risk', 30)->nullable();
            $table->string('overall_risk_level', 30)->nullable();
            $table->text('safety_actions')->nullable();

            /*
             * زمینه زندگی و روابط
             */
            $table->text('important_life_history')->nullable();
            $table->text('important_relationships_and_support')->nullable();
            $table->text('values_and_personal_resources')->nullable();

            /*
             * ارزیابی و برنامه درمان
             */
            $table->text('clinical_observations')->nullable();
            $table->text('clinical_formulation')->nullable();
            $table->text('treatment_approach')->nullable();
            $table->text('treatment_goals')->nullable();
            $table->string('treatment_stage', 50)->nullable();
            $table->text('treatment_plan_and_schedule')->nullable();

            /*
             * وضعیت پرونده
             */
            $table->string('status', 30)
                ->default('active')
                ->index();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(
                ['status', 'created_at'],
                'clinical_records_status_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};
