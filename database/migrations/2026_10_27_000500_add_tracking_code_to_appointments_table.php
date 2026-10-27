<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('tracking_code', 32)
                ->nullable()
                ->unique();
        });

        // برای نوبت‌های قبلی هم کد ایجاد می‌شود.
        // استفاده از Query Builder عمداً باعث عدم تغییر updated_at می‌شود.
        DB::table('appointments')
            ->select('id')
            ->whereNull('tracking_code')
            ->orderBy('id')
            ->chunkById(500, function ($appointments): void {
                foreach ($appointments as $appointment) {
                    do {
                        $code = strtoupper(bin2hex(random_bytes(16)));
                    } while (
                        DB::table('appointments')
                            ->where('tracking_code', $code)
                            ->exists()
                    );

                    DB::table('appointments')
                        ->where('id', $appointment->id)
                        ->update([
                            'tracking_code' => $code,
                        ]);
                }
            });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('tracking_code', 32)
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique(['tracking_code']);
            $table->dropColumn('tracking_code');
        });
    }
};
