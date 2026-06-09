<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->enum('estado', ['en_camino', 'llegado'])->default('en_camino')->after('motivo');
            $table->timestamp('fecha_llegada')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn(['estado', 'fecha_llegada']);
        });
    }
};
