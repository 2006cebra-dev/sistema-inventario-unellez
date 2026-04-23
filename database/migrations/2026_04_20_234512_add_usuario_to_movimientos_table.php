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
        Schema::table('movimientos', function (Blueprint $table) {
            // Creamos la columna para guardar el nombre del usuario
            // La ponemos después de 'motivo' para que la tabla sea ordenada
            $table->string('usuario_accion')->nullable()->after('motivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Si echamos para atrás la migración, borramos la columna
            $table->dropColumn('usuario_accion');
        });
    }
};