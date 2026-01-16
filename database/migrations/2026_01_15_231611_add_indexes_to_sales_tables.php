<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Para acelerar consultas de items por venta (tickets/recibos)
            $table->index('sale_id', 'idx_sale_items_sale_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            // Para reportes por día/cierre de caja
            $table->index('sale_date', 'idx_sales_sale_date');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('idx_sale_items_sale_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_sale_date');
        });
    }
};
