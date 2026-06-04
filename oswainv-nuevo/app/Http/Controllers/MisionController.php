<?php

namespace App\Http\Controllers;

use App\Models\Mision;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisionController extends Controller
{
    public function index()
    {
        if (Auth::user()->tienePermiso('gestionar_misiones')) {
            $usuarios = User::where('rol', 'empleado')->get();
            $misiones = Mision::with('user')->orderBy('created_at', 'desc')->get();
            return view('inventario.misiones.gestion', compact('misiones', 'usuarios'));
        }

        $misiones = Mision::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $usuarios = collect();
        return view('inventario.misiones.gestion', compact('misiones', 'usuarios'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->tienePermiso('gestionar_misiones')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $mision = Mision::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'user_id' => $request->user_id,
            'estado' => 'pendiente',
        ]);

        Notification::create([
            'user_id' => $request->user_id,
            'type' => 'mission_assigned',
            'title' => '🎯 Nueva misión: ' . $request->titulo,
            'message' => 'Tienes una nueva misión asignada. ¡Complétala y gana XP!',
            'icon' => 'bi-crosshair2',
            'link' => route('misiones.gestion'),
        ]);

        return response()->json(['success' => true, 'message' => 'Misión asignada correctamente.']);
    }

    public function aprobar($id)
    {
        if (!Auth::user()->tienePermiso('gestionar_misiones')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $mision = Mision::findOrFail($id);
        $mision->update(['estado' => 'completada']);

        Notification::create([
            'user_id' => $mision->user_id,
            'type' => 'mission_approved',
            'title' => '✅ Misión aprobada: ' . $mision->titulo,
            'message' => '¡Tu misión fue aprobada! Has ganado XP.',
            'icon' => 'bi-trophy-fill text-warning',
            'link' => route('misiones.gestion'),
        ]);

        return response()->json(['success' => true, 'message' => 'Misión aprobada como completada.']);
    }

    public function rechazar($id)
    {
        if (!Auth::user()->tienePermiso('gestionar_misiones')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $mision = Mision::findOrFail($id);
        $mision->update(['estado' => 'fallida']);

        Notification::create([
            'user_id' => $mision->user_id,
            'type' => 'mission_rejected',
            'title' => '❌ Misión rechazada: ' . $mision->titulo,
            'message' => 'Tu misión no fue aprobada. Revisa los detalles.',
            'icon' => 'bi-x-circle-fill text-danger',
            'link' => route('misiones.gestion'),
        ]);

        return response()->json(['success' => true, 'message' => 'Misión rechazada.']);
    }

    public function revertir($id)
    {
        if (!Auth::user()->tienePermiso('gestionar_misiones')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $mision = Mision::findOrFail($id);
        $mision->update(['estado' => 'pendiente']);

        return response()->json(['success' => true, 'message' => 'Misión reasignada como pendiente.']);
    }

    public function completar($id)
    {
        $mision = Mision::findOrFail($id);

        if ($mision->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Esta misión no te pertenece.'], 403);
        }

        if ($mision->estado === 'completada') {
            return response()->json(['success' => false, 'message' => 'Esta misión ya fue completada.']);
        }

        $mision->update(['estado' => 'completada']);

        $admins = User::where('rol', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'mission_completed',
                'title' => '✅ Misión completada por ' . Auth::user()->display_name,
                'message' => "{$mision->titulo} — Revisa y aprueba",
                'icon' => 'bi-check-circle-fill text-success',
                'link' => route('misiones.gestion'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Misión completada con éxito. ¡+25 XP!']);
    }
}