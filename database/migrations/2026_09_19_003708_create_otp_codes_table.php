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

            $table->string('mobile', 11)->index();

            // کد به‌صورت هش‌شده ذخیره می‌شود، نه متن خام
            $table->string('code_hash');

            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();

            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('last_sent_at')->nullable();

            $table->timestamps();

            $table->index([
                'mobile',
                'consumed_at',
                'expires_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
