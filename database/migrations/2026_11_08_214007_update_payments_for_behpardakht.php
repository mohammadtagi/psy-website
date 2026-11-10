<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            // فقط پیش‌فرض رکوردهای آینده عوض می‌شود؛
            // gateway پرداخت‌های قدیمی زرین‌پال حفظ می‌شود.
            $table->string('gateway', 30)
                ->default('behpardakht')
                ->change();

            $table->unsignedBigInteger('sale_order_id')
                ->nullable()
                ->unique();

            // مبلغ نهایی تأییدشده، به تومان؛ برای تلاش‌های ناموفق NULL.
            $table->unsignedInteger('final_amount')
                ->nullable();

            // فقط چهار رقم آخر یا مقدار ماسک‌شده، هرگز PAN کامل.
            $table->string('card_holder_pan', 32)
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique('payments_sale_order_id_unique');

            $table->dropColumn([
                'sale_order_id',
                'final_amount',
                'card_holder_pan',
            ]);

            $table->string('gateway', 30)
                ->default('zarinpal')
                ->change();
        });
    }
};
