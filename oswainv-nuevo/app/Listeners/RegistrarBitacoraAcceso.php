<?php

namespace App\Listeners;

use App\Models\BitacoraAcceso;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegistrarBitacoraAcceso
{
    public function __construct()
    {
    }

    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = request();
        
        BitacoraAcceso::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'login_at' => now(),
        ]);
    }
}
