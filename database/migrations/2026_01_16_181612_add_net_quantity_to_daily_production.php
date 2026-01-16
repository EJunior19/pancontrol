<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('daily_production', function (Blueprint $table) {
            $table->decimal('net_quantity', 12, 3)->after('waste_quantity');
        });
    }

    public function down()
    {
        Schema::table('daily_production', function (Blueprint $table) {
            $table->dropColumn('net_quantity');
        });
    }

};
