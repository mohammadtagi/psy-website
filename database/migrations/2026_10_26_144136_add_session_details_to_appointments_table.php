<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            // نوع ارتباط با درمان؛ مستقل از حضوری/آنلاین بودن.
            $table->string('therapy_session_type', 20)
                ->default('free');

            $table->foreignId('treatment_plan_id')
                ->nullable()
                ->constrained('treatment_plans')
                ->restrictOnDelete();

            $table->foreignId('treatment_plan_stage_id')
                ->nullable()
                ->constrained('treatment_plan_stages')
                ->restrictOnDelete();

            // pending / held / absent
            // لغو از appointments.status خوانده می‌شود.
            $table->string('attendance_status', 20)
                ->default('pending');

            // اطلاعات عمومی قابل نمایش برای مراجع همان جلسه.
            $table->string('session_subject')->nullable();
            $table->text('session_discussion')->nullable();
            $table->text('session_recommendations')->nullable();
            $table->text('session_exercises')->nullable();

            $table->index(
                [
                    'treatment_plan_stage_id',
                    'attendance_status',
                    'status',
                ],
                'appointments_stage_attendance_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex(
                'appointments_stage_attendance_status_index'
            );

            $table->dropForeign(['treatment_plan_stage_id']);
            $table->dropForeign(['treatment_plan_id']);

            $table->dropColumn([
                'therapy_session_type',
                'treatment_plan_id',
                'treatment_plan_stage_id',
                'attendance_status',
                'session_subject',
                'session_discussion',
                'session_recommendations',
                'session_exercises',
            ]);
        });
    }
};
