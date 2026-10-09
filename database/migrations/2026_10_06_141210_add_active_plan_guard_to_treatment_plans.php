<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('treatment_plans', function (Blueprint $table): void {
            $table->unsignedBigInteger('active_plan_guard')
                ->nullable()
                ->after('status');
        });

        $duplicates = DB::table('treatment_plans')
            ->select('psychologist_id', 'client_id')
            ->where('status', 'active')
            ->groupBy('psychologist_id', 'client_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicates) {
            throw new \RuntimeException(
                'برای یک یا چند مراجع، بیش از یک نقشه درمان فعال وجود دارد. '
                . 'ابتدا داده‌ها را اصلاح کنید.'
            );
        }

        DB::statement("
    UPDATE treatment_plans
    SET active_plan_guard = client_id
    WHERE status = 'active'
");

        Schema::table('treatment_plans', function (Blueprint $table): void {
            $table->unique(
                ['psychologist_id', 'active_plan_guard'],
                'treatment_plans_one_active_per_psychologist_client'
            );
        });
    }

    public function down(): void
    {
        Schema::table('treatment_plans', function (Blueprint $table): void {
            $table->dropUnique(
                'treatment_plans_one_active_per_psychologist_client'
            );

            $table->dropColumn('active_plan_guard');
        });
    }
};
