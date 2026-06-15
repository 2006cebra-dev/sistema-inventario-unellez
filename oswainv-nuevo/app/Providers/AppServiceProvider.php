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

        if (!$this->app->runningInConsole()) {
            $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);
            $defaultPermisos = [
                'admin' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','aprobar_requisiciones','ver_auditoria','gestionar_misiones','exportar_pdf','chat'],
                'empleado' => ['ver_dashboard','ver_catalogo','ver_proveedores','chat'],
                'desarrollador' => ['ver_dashboard','ver_catalogo','ver_proveedores','gestionar_productos','gestionar_proveedores','aprobar_requisiciones','gestionar_usuarios','ver_auditoria','gestionar_misiones','gestionar_precios','exportar_pdf','respaldar_bd','chat'],
            ];
            $changed = false;
            foreach ($defaultPermisos as $r => $perms) {
                if (!isset($rolesPermisos[$r])) {
                    $rolesPermisos[$r] = $perms;
                    $changed = true;
                }
            }
            if ($changed) {
                \Illuminate\Support\Facades\Cache::forever('roles_permisos', $rolesPermisos);
            }
        }
    }
}
