<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('psychologist_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->uuid('series_key')
                ->nullable()
                ->index();

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->timestamps();

            $table->unique(
                ['psychologist_id', 'starts_at', 'ends_at'],
                'availabilities_psychologist_time_unique'
            );

            $table->index(
                ['psychologist_id', 'starts_at', 'ends_at'],
                'availabilities_psychologist_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
