<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 16)->index();        // +989XXXXXXXXX
            $table->string('code_hash');                  // کد هش‌شده — هرگز متن آشکار ذخیره نمی‌شود
            $table->string('purpose')->default('login');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable(); // تک‌مصرف
            $table->timestamp('revoked_at')->nullable();  // لغو کدهای قبلی
            $table->timestamps();

            $table->index(['mobile', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
