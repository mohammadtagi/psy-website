<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Stored in toman.
            $table->unsignedInteger('amount');

            $table->string('gateway', 30)
                ->default('zarinpal');

            $table->string('status', 30)
                ->default('initiated');

            $table->string('authority', 100)
                ->nullable()
                ->unique();

            $table->string('ref_id', 100)
                ->nullable();

            $table->text('gateway_message')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'appointment_id',
                'status',
            ]);

            $table->index([
                'client_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
