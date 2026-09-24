<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_attachments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('clinical_record_id')
                ->constrained('clinical_records')
                ->cascadeOnDelete();

            $table->string('description')->nullable();

            $table->string('category', 50)->nullable();

            $table->string('disk', 50)
                ->default('private');

            $table->string('path');

            $table->string('original_name');

            $table->string('mime_type', 150)->nullable();

            $table->unsignedBigInteger('size')->nullable();

            $table->date('document_date')->nullable();

            $table->timestamps();

            $table->index(
                ['clinical_record_id', 'category'],
                'clinical_attachments_record_category_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_attachments');
    }
};
