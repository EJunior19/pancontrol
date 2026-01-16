<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_production', function (Blueprint $table) {
            $table->id();
            $table->date('production_date');

            $table->foreignId('product_id')
                  ->constrained('products');

            $table->integer('produced_quantity');
            $table->integer('sold_quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->integer('waste_quantity')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_production');
    }
};
