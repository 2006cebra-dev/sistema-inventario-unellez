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
            $table->decimal('porcentaje_incremento', 6, 2)->nullable()->after('precio_nuevo');
        });
    }

    public function down(): void
    {
        Schema::table('price_history', function (Blueprint $table) {
            $table->dropColumn('porcentaje_incremento');
        });
    }
};
