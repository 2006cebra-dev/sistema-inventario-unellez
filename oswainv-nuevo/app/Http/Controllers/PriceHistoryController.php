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
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        try {
            $history = PriceHistory::where('producto_id', $productoId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('PriceHistory error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
