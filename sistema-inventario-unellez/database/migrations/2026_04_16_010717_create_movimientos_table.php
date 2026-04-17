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
    Schema::create('movimientos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_producto'); // Se conecta con el código del producto
        $table->string('tipo');            // "Entrada" o "Salida"
        $table->integer('cantidad');
        $table->string('motivo')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
