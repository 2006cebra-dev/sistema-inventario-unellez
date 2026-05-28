<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_costo', 10, 2)->nullable()->after('precio');
            $table->integer('stock_minimo')->default(5)->after('stock');
            $table->integer('stock_maximo')->nullable()->after('stock_minimo');
            $table->string('unidad_medida', 20)->default('unidad')->after('stock_maximo');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_costo', 'stock_minimo', 'stock_maximo', 'unidad_medida']);
        });
    }
};
