<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
                Schema::table('clinical_notes', function (Blueprint $table) {
                        $table->foreignId('treatment_plan_id')
                            ->nullable()
                            ->after('appointment_id')
                            ->constrained('treatment_plans')
                            ->nullOnDelete();

            $table->foreignId('treatment_plan_stage_id')
                ->nullable()
                            ->after('treatment_plan_id')
                            ->constrained('treatment_plan_stages')
                            ->nullOnDelete();

            $table->string('session_type')
                ->nullable()
                            ->after('treatment_plan_stage_id');

            $table->index(
                    ['treatment_plan_id', 'treatment_plan_stage_id'],
                    'clinical_notes_treatment_plan_stage_index'
                );
        });
            }

    public function down(): void
    {
                Schema::table('clinical_notes', function (Blueprint $table) {
                        $table->dropIndex('clinical_notes_treatment_plan_stage_index');
                        $table->dropForeign(['treatment_plan_stage_id']);
                        $table->dropForeign(['treatment_plan_id']);
                        $table->dropColumn([
                                'treatment_plan_stage_id',
                                'treatment_plan_id',
                                'session_type',
                            ]);
                    });
            }
};
