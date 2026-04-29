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
        // 1. TABLA DE PRODUCTOS
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();   // Código de barras
            $table->string('nombre');             // Nombre del artículo
            $table->integer('stock')->default(0); // Cantidad disponible
            $table->string('marca')->nullable();
            $table->string('categoria')->default('General');
            $table->decimal('precio', 10, 2)->default(0); 
            $table->string('descripcion')->nullable(); // Aquí guardaremos la ruta de la foto
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();
        });

        // 2. TABLA DE MOVIMIENTOS (EL KARDEX / AUDITORÍA)
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_producto');    // Referencia al código del producto
            $table->string('tipo');               // 'Entrada' o 'Salida'
            $table->integer('cantidad');          // Cuánto se movió
            $table->string('motivo')->nullable(); // Ej: "Ajuste rápido", "Venta", etc.
            $table->string('usuario_accion');     // Quién lo hizo (ej: Carlos Armando)
            $table->text('firma_digital')->nullable(); // Para seguridad SHA-256
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
        Schema::dropIfExists('productos');
    }
};