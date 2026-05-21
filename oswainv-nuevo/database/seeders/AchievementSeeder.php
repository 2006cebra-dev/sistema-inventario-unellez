<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['name' => 'Primer Movimiento', 'description' => 'Registra tu primera entrada o salida de inventario', 'icon' => 'bi-box-seam-fill', 'xp_reward' => 50, 'criteria_type' => 'stock_entries', 'criteria_value' => 1],
            ['name' => 'Guardian del Stock', 'description' => 'Realiza 50 movimientos de inventario', 'icon' => 'bi-shield-fill-check', 'xp_reward' => 100, 'criteria_type' => 'stock_entries', 'criteria_value' => 50],
            ['name' => 'Magíster del Inventario', 'description' => 'Realiza 200 movimientos', 'icon' => 'bi-stars', 'xp_reward' => 300, 'criteria_type' => 'stock_entries', 'criteria_value' => 200],
            ['name' => 'Creador de Productos', 'description' => 'Registra 10 productos en el catálogo', 'icon' => 'bi-plus-square-fill', 'xp_reward' => 80, 'criteria_type' => 'products_registered', 'criteria_value' => 10],
            ['name' => 'Proveedor de Contenido', 'description' => 'Registra 50 productos', 'icon' => 'bi-boxes', 'xp_reward' => 200, 'criteria_type' => 'products_registered', 'criteria_value' => 50],
            ['name' => 'Misionero Novato', 'description' => 'Completa tu primera misión', 'icon' => 'bi-crosshair2', 'xp_reward' => 60, 'criteria_type' => 'missions_completed', 'criteria_value' => 1],
            ['name' => 'Élite de Misiones', 'description' => 'Completa 25 misiones', 'icon' => 'bi-trophy-fill', 'xp_reward' => 250, 'criteria_type' => 'missions_completed', 'criteria_value' => 25],
            ['name' => 'Chat Activo', 'description' => 'Envía 50 mensajes en el chat', 'icon' => 'bi-chat-dots-fill', 'xp_reward' => 80, 'criteria_type' => 'chat_messages', 'criteria_value' => 50],
            ['name' => 'Racha de 7 Días', 'description' => 'Inicia sesión 7 días consecutivos', 'icon' => 'bi-fire', 'xp_reward' => 100, 'criteria_type' => 'login_streak', 'criteria_value' => 7],
            ['name' => 'Racha de 30 Días', 'description' => 'Inicia sesión 30 días consecutivos', 'icon' => 'bi-sun-fill', 'xp_reward' => 500, 'criteria_type' => 'login_streak', 'criteria_value' => 30],
            ['name' => 'Logística', 'description' => 'Realiza 10 transferencias', 'icon' => 'bi-truck', 'xp_reward' => 150, 'criteria_type' => 'transfers_made', 'criteria_value' => 10],
            ['name' => 'Solicitante', 'description' => 'Crea 10 requisiciones', 'icon' => 'bi-file-earmark-text-fill', 'xp_reward' => 80, 'criteria_type' => 'requisitions_made', 'criteria_value' => 10],
            ['name' => 'Dios del OSWA', 'description' => 'Alcanza el nivel 50', 'icon' => 'bi-gem', 'xp_reward' => 1000, 'criteria_type' => 'login_streak', 'criteria_value' => 999, 'hidden' => true],
        ];

        foreach ($achievements as $ach) {
            Achievement::create($ach);
        }
    }
}
