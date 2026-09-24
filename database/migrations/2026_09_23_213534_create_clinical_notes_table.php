<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_notes', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('clinical_record_id')
                ->constrained('clinical_records')
                ->cascadeOnDelete();

            /*
             * اختیاری است:
             * یادداشت می‌تواند به نوبت وصل باشد یا مستقل ثبت شود.
             */
            $table->foreignId('appointment_id')
                ->nullable()
                ->unique()
                ->constrained('appointments')
                ->nullOnDelete();

            $table->dateTime('session_at')->nullable();

            $table->text('summary')->nullable();

            $table->text('client_condition')->nullable();

            $table->text('interventions')->nullable();

            $table->text('homework_and_next_plan')->nullable();

            $table->text('private_note')->nullable();

            $table->timestamps();

            $table->index(
                ['clinical_record_id', 'session_at'],
                'clinical_notes_record_session_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
