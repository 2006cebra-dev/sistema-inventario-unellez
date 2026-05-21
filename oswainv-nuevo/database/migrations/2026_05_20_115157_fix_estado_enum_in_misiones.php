<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE misiones SET estado = LOWER(estado)");
        DB::statement("ALTER TABLE misiones MODIFY COLUMN estado ENUM('pendiente', 'completada', 'fallida') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE misiones MODIFY COLUMN estado ENUM('Pendiente', 'Completada') NOT NULL DEFAULT 'Pendiente'");
        DB::statement("UPDATE misiones SET estado = CONCAT(UCASE(LEFT(estado, 1)), LCASE(SUBSTRING(estado, 2)))");
    }
};