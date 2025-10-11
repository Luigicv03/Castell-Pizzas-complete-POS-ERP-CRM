<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExchangeRate;

class ExchangeRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:exchange-rates.view')->only(['index']);
        $this->middleware('can:exchange-rates.update')->only(['update', 'updateFromBCV']);
    }

    /**
     * Mostrar la vista de tasa de cambio
     */
    public function index()
    {
        $exchangeRate = ExchangeRate::getCurrentRate();
        $timeUntilUpdate = $exchangeRate->getTimeUntilNextUpdate();
        
        return view('exchange-rates.index', compact('exchangeRate', 'timeUntilUpdate'));
    }

    /**
     * Actualizar la tasa manualmente
     */
    public function update(Request $request)
    {
        $request->validate([
            'usd_to_bsf' => 'required|numeric|min:0.01',
            'is_automatic' => 'boolean'
        ]);

        $exchangeRate = ExchangeRate::getCurrentRate();
        $exchangeRate->update([
            'usd_to_bsf' => $request->usd_to_bsf,
            'is_automatic' => $request->is_automatic ?? false,
            'source' => 'manual',
            'last_updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tasa de cambio actualizada correctamente',
            'rate' => $exchangeRate
        ]);
    }

    /**
     * Actualizar desde BCV
     */
    public function updateFromBCV()
    {
        try {
            $exchangeRate = ExchangeRate::updateFromBCV();
            
            return response()->json([
                'success' => true,
                'message' => 'Tasa actualizada desde BCV correctamente',
                'rate' => $exchangeRate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar desde BCV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la tasa actual (API)
     */
    public function getCurrentRate()
    {
        $exchangeRate = ExchangeRate::getCurrentRate();
        
        return response()->json([
            'usd_to_bsf' => $exchangeRate->usd_to_bsf,
            'is_automatic' => $exchangeRate->is_automatic,
            'last_updated_at' => $exchangeRate->last_updated_at,
            'source' => $exchangeRate->source
        ]);
    }
}
