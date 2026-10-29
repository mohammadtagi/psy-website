<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('type', 20);
            $table->unsignedInteger('value');

            // null یعنی کد عمومی است.
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);

            // null یعنی بدون محدودیت تعداد.
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_client_limit')->nullable();

            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('discount_code_id')
                ->nullable()
                ->constrained('discount_codes')
                ->restrictOnDelete();

            $table->unsignedInteger('discount_amount')->default(0);
        });

        Schema::create('discount_redemptions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('discount_code_id')
                ->constrained('discount_codes')
                ->restrictOnDelete();

            // هر نوبت حداکثر یک مصرف موفق دارد.
            $table->foreignId('appointment_id')
                ->unique()
                ->constrained('appointments')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->unique()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                ->restrictOnDelete();

            // snapshot برای حفظ سابقه.
            $table->string('code', 50);
            $table->unsignedInteger('original_amount');
            $table->unsignedInteger('discount_amount');
            $table->unsignedInteger('final_amount');

            $table->timestamps();

            $table->index(
                ['discount_code_id', 'client_id'],
                'discount_redemptions_code_client_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_redemptions');

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('discount_code_id');
            $table->dropColumn('discount_amount');
        });

        Schema::dropIfExists('discount_codes');
    }
};
