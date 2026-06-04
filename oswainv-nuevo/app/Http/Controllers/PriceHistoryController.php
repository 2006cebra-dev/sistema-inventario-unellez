<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PriceHistoryController extends Controller
{
    public function index($productoId)
    {
        if (!Auth::user()->tienePermiso('gestionar_precios')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        try {
            $history = PriceHistory::where('producto_id', $productoId)
                ->with('user:id,name,nick,email')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->user_name = $item->user?->display_name ?? 'Sistema';
                    $item->incremento_label = $item->porcentaje_incremento !== null
                        ? ($item->porcentaje_incremento >= 0 ? '+' : '') . $item->porcentaje_incremento . '%'
                        : '—';
                    // precio_anterior/precio_nuevo ya están en USD
                    $item->precio_usd_anterior = (float)$item->precio_anterior;
                    $item->precio_usd_nuevo = (float)$item->precio_nuevo;
                    $item->precio_bs_anterior = $item->tasa_dolar > 0
                        ? round($item->precio_anterior * $item->tasa_dolar, 2) : null;
                    $item->precio_bs_nuevo = $item->tasa_dolar > 0
                        ? round($item->precio_nuevo * $item->tasa_dolar, 2) : null;
                    unset($item->user);
                    return $item;
                });

            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('PriceHistory error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
