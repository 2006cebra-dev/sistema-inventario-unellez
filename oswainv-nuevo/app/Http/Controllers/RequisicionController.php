<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Requisicion;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequisicionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function crearRequisicion()
    {
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre', 'asc')->get();
        return view('inventario.requisiciones.crear', compact('productos'));
    }

    public function solicitarRequisicion(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Debes iniciar sesión para hacer una solicitud.');
        }

        try {
            DB::beginTransaction();

            foreach ($request->productos as $producto_id => $cantidad) {
                Requisicion::create([
                    'user_id' => Auth::id(),
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'estado' => 'Pendiente'
                ]);
            }

            DB::commit();

            $admins = User::where('rol', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'requisition_created',
                    'title' => '📋 Nueva requisición de ' . Auth::user()->name,
                    'message' => count($request->productos) . ' producto(s) solicitado(s)',
                    'icon' => 'bi-file-earmark-text-fill',
                    'link' => route('inventario'),
                ]);
            }

            return redirect()->route('catalogo')->with('success', '¡Solicitud enviada al Administrador exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    public function aprobarRequisicion($id)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        try {
            DB::beginTransaction();

            $requisicion = Requisicion::with('producto')->findOrFail($id);

            if ($requisicion->estado !== 'Pendiente') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'La requisición ya fue procesada.']);
            }

            $producto = $requisicion->producto;
            if (!$producto) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Producto no encontrado.']);
            }

            if ($producto->stock < $requisicion->cantidad) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Stock insuficiente para aprobar esta solicitud.']);
            }

            $producto->decrement('stock', $requisicion->cantidad);

            $mov = Movimiento::create([
                'codigo_producto' => $producto->codigo,
                'tipo' => 'Salida',
                'cantidad' => $requisicion->cantidad,
                'motivo' => 'Requisición Aprobada #' . $requisicion->id,
                'usuario_accion' => Auth::user()->name,
            ]);
            
            $mov->firma_hash = $mov->generarFirma();
            $mov->save();

            $requisicion->estado = 'Aprobada';
            $requisicion->save();

            DB::commit();

            Notification::create([
                'user_id' => $requisicion->user_id,
                'type' => 'requisition_approved',
                'title' => '✅ Requisición aprobada',
                'message' => "Tu solicitud de {$requisicion->producto->nombre} fue aprobada",
                'icon' => 'bi-check-circle-fill text-success',
                'link' => route('catalogo'),
            ]);

            return response()->json(['success' => true, 'message' => 'Requisición aprobada y stock actualizado.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar: ' . $e->getMessage()]);
        }
    }

    public function rechazarRequisicion($id)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        $requisicion = Requisicion::findOrFail($id);
        $requisicion->estado = 'Rechazada';
        $requisicion->save();

        Notification::create([
            'user_id' => $requisicion->user_id,
            'type' => 'requisition_rejected',
            'title' => '❌ Requisición rechazada',
            'message' => "Tu solicitud de {$requisicion->producto->nombre} fue rechazada",
            'icon' => 'bi-x-circle-fill text-danger',
            'link' => route('catalogo'),
        ]);

        return response()->json(['success' => true, 'message' => 'Requisición rechazada.']);
    }
}
