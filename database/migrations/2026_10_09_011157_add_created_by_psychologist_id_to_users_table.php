<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('created_by_psychologist_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(
                ['created_by_psychologist_id', 'role'],
                'users_created_by_psychologist_role_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_created_by_psychologist_role_index');
            $table->dropConstrainedForeignId('created_by_psychologist_id');
        });
    }
};
