<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_history', function (Blueprint $table) {
            $table->decimal('tasa_dolar', 10, 2)->nullable()->after('porcentaje_incremento');
        });
    }

    public function down(): void
    {
        Schema::table('price_history', function (Blueprint $table) {
            $table->dropColumn('tasa_dolar');
        });
    }
};
