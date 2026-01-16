<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_currency', 3)->default('PYG');
            $table->decimal('exchange_rate', 14, 4)->default(1);
            $table->decimal('paid_amount', 14, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'payment_currency',
                'exchange_rate',
                'paid_amount'
            ]);
        });
    }
};
