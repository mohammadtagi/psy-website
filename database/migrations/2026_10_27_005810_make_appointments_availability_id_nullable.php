<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->unsignedBigInteger('availability_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('appointments')->whereNull('availability_id')->exists()) {
            throw new RuntimeException(
                'Cannot restore NOT NULL while manual appointments exist.'
            );
        }

        Schema::table('appointments', function (Blueprint $table): void {
            $table->unsignedBigInteger('availability_id')
                ->nullable(false)
                ->change();
        });
    }
};
