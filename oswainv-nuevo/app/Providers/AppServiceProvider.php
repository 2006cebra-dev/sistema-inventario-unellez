<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\RegistrarBitacoraAcceso;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('es');
        Event::listen(Login::class, RegistrarBitacoraAcceso::class);
    }
}
