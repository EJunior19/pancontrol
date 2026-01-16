<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sale_unit', 10)
                ->default('unit')
                ->after('type');

            $table->decimal('price_per_kg', 12, 2)
                ->nullable()
                ->after('price');

            $table->decimal('stock_qty', 12, 3)
                ->default(0)
                ->after('price_per_kg');

            $table->boolean('allow_decimal')
                ->default(false)
                ->after('sale_unit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sale_unit',
                'price_per_kg',
                'stock_qty',
                'allow_decimal',
            ]);
        });
    }
};
