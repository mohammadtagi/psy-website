<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'client_session_notes',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('appointment_id')
                    ->constrained('appointments')
                    ->cascadeOnDelete();

                $table->foreignId('client_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->text('body');

                $table->timestamps();

                $table->index(
                    ['client_id', 'appointment_id', 'created_at'],
                    'client_session_notes_owner_appointment_created_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('client_session_notes');
    }
};
