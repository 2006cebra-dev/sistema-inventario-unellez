<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function proveedores()
    {
        if (!Auth::check() || !Auth::user()->tienePermiso('ver_proveedores')) abort(403, 'No autorizado');
        $proveedores = Proveedor::with('productos:id,codigo,nombre,stock,stock_minimo,precio,precio_costo,proveedor_id')->orderBy('created_at', 'desc')->get();
        $productos = Producto::all();
        return view('inventario.proveedores', compact('proveedores'));
    }

    public function storeProveedor(Request $request)
    {
        if (!Auth::user()->tienePermiso('gestionar_proveedores')) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'rif' => $request->rif,
            'contacto' => $request->contacto,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('proveedores', 'public');
        }

        Proveedor::create($data);

        return response()->json(['success' => true]);
    }

    public function updateProveedor(Request $request, $id)
    {
        if (!Auth::user()->tienePermiso('gestionar_proveedores')) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $proveedor = Proveedor::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'required|string|unique:proveedores,rif,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $data = $request->only(['nombre', 'rif', 'contacto', 'telefono', 'direccion']);

        if ($request->hasFile('logo')) {
            if ($proveedor->logo) {
                Storage::disk('public')->delete($proveedor->logo);
            }
            $data['logo'] = $request->file('logo')->store('proveedores', 'public');
        }

        $proveedor->update($data);
        return response()->json(['success' => true]);
    }

    public function destroyProveedor($id)
    {
        if (!Auth::user()->tienePermiso('gestionar_proveedores')) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();
        return response()->json(['success' => true]);
    }

    public function procesarAbastecimiento(Request $request)
    {
        if (!Auth::user()->tienePermiso('gestionar_proveedores')) {
            return response()->json(['success' => false, 'error' => 'No autorizado'], 403);
        }
        $request->validate([ 'producto_id' => 'required', 'cantidad' => 'required|numeric|min:1' ]);

        $producto = Producto::findOrFail($request->producto_id);
        $producto->stock += $request->cantidad;
        $producto->save();

        try {
            $tipoMovimiento = 'Entrada';
            $cantidadMovimiento = $request->cantidad;

            $movimiento = new Movimiento();
            $movimiento->codigo_producto = $producto->codigo;
            $movimiento->tipo = $tipoMovimiento;
            $movimiento->cantidad = $cantidadMovimiento;
            $movimiento->motivo = 'Orden de Abastecimiento';
            $movimiento->usuario_accion = Auth::user()->display_name;
            $movimiento->user_id = Auth::id();
            $movimiento->save();

            $cadena = $movimiento->id . $movimiento->codigo_producto . $movimiento->tipo . $movimiento->cantidad . $movimiento->motivo . $movimiento->usuario_accion;
            $movimiento->firma_hash = hash('sha256', $cadena);
            $movimiento->save();
        } catch (\Exception $e) {
            Log::error('Error creando movimiento de abastecimiento: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
