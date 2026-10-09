<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
                Schema::create('treatment_plans', function (Blueprint $table) {
                        $table->id();

                        $table->foreignId('psychologist_id')
                            ->constrained('users')
                            ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                            ->restrictOnDelete();

            $table->foreignId('clinical_record_id')
                ->constrained('clinical_records')
                            ->restrictOnDelete();

            $table->string('title');
            $table->text('main_problem')->nullable();

            $table->string('status')->default('active');
            $table->unsignedTinyInteger('current_stage')->default(1);
            $table->text('status_reason')->nullable();

            $table->timestamps();

            $table->index(
                    ['client_id', 'psychologist_id', 'status'],
                    'treatment_plans_client_psychologist_status_index'
                );

            $table->index(
                    ['clinical_record_id', 'status'],
                    'treatment_plans_record_status_index'
                );
        });
            }

    public function down(): void
    {
                Schema::dropIfExists('treatment_plans');
            }
};
