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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            // --- AGREGA ESTAS 3 LÍNEAS ---
            $table->string('codigo')->unique();   // Para el código de barras
            $table->string('nombre');             // Nombre del producto
            $table->integer('stock')->default(0); // Cantidad disponible
            // -----------------------------
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
