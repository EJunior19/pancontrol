<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('barcode', 50)->unique();

            $table->enum('sale_unit', ['kg', 'unit']);
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('price_per_kg', 12, 2)->nullable();

            $table->decimal('stock_qty', 12, 3)->default(0);
            $table->boolean('allow_decimal')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
