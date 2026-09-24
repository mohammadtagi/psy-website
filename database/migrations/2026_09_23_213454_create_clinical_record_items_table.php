<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_record_items', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('clinical_record_id')
                ->constrained('clinical_records')
                ->cascadeOnDelete();

            $table->string('type', 40)->index();

            $table->string('title')->nullable();

            $table->text('content')->nullable();

            $table->date('occurred_on')->nullable();

            $table->json('metadata')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['clinical_record_id', 'type', 'sort_order'],
                'clinical_record_items_record_type_order_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_record_items');
    }
};
