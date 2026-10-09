<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
                Schema::create('treatment_plan_stages', function (Blueprint $table) {
                        $table->id();

                        $table->foreignId('treatment_plan_id')
                            ->constrained('treatment_plans')
                            ->cascadeOnDelete();

            $table->unsignedTinyInteger('stage_number');
            $table->string('title');
            $table->text('goal')->nullable();
            $table->unsignedSmallInteger('estimated_sessions')->nullable();

            $table->timestamps();

            $table->unique(
                    ['treatment_plan_id', 'stage_number'],
                    'treatment_plan_stages_plan_number_unique'
                );
        });
            }

    public function down(): void
    {
                Schema::dropIfExists('treatment_plan_stages');
            }
};
