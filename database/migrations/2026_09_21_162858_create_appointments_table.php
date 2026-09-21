<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('psychologist_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('availability_id')
                ->constrained('availabilities')
                ->restrictOnDelete();

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->unsignedSmallInteger('duration_minutes');

            $table->string('session_type', 20);
            $table->string('status', 30)
                ->default('confirmed')
                ->index();

            // Stored as an integer in tomans.
            $table->unsignedInteger('amount');

            // Reserved for the future temporary payment flow.
            $table->timestamp('hold_expires_at')->nullable();

            $table->text('meeting_url')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(
                ['psychologist_id', 'status', 'starts_at'],
                'appointments_psychologist_booking_index'
            );

            $table->index(
                ['client_id', 'status', 'starts_at'],
                'appointments_client_booking_index'
            );

            $table->index(
                ['availability_id', 'status'],
                'appointments_availability_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
