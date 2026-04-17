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
        Schema::table('productos', function (Blueprint $table) {
            // Verificamos si la columna 'marca' NO existe antes de crearla
            if (!Schema::hasColumn('productos', 'marca')) {
                $table->string('marca')->nullable()->after('nombre');
            }
            
            // Verificamos si la columna 'descripcion' NO existe antes de crearla
            if (!Schema::hasColumn('productos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('marca');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['marca', 'descripcion']);
        });
    }
};