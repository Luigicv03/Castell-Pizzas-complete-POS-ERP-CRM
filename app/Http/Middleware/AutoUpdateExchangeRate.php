<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AutoUpdateExchangeRate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Verificar si necesita actualización automática
            $exchangeRate = ExchangeRate::getCurrentRate();
            
            // Si han pasado 4 horas o más desde la última actualización, actualizar
            if ($exchangeRate->needsUpdate()) {
                Log::info('Auto-actualizando tasa de cambio desde BCV (4 horas transcurridas)');
                ExchangeRate::updateFromBCV();
            }
        } catch (\Exception $e) {
            // Si hay un error, solo registrarlo pero no interrumpir la petición
            Log::error('Error en auto-actualización de tasa de cambio: ' . $e->getMessage());
        }

        return $next($request);
    }
}


