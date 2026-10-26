<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_notes', function (Blueprint $table): void {
            $table->foreignId('psychologist_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->index(
                ['clinical_record_id', 'psychologist_id'],
                'clinical_notes_record_author_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('clinical_notes', function (Blueprint $table): void {
            $table->dropIndex('clinical_notes_record_author_index');
            $table->dropConstrainedForeignId('psychologist_id');
        });
    }
};
