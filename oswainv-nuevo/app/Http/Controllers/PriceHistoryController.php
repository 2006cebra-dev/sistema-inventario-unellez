<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PriceHistoryController extends Controller
{
    public function index($productoId)
    {
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
