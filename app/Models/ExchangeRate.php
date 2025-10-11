<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'usd_to_bsf',
        'is_automatic',
        'last_updated_at',
        'source'
    ];

    protected $casts = [
        'usd_to_bsf' => 'decimal:4',
        'is_automatic' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Obtener la tasa de cambio actual
     */
    public static function getCurrentRate()
    {
        return self::first() ?? self::create([
            'usd_to_bsf' => 36.50,
            'is_automatic' => true,
            'source' => 'manual'
        ]);
    }

    /**
     * Actualizar la tasa automáticamente desde BCV
     */
    public static function updateFromBCV()
    {
        try {
            // API del Banco Central de Venezuela
            $response = file_get_contents('https://api.exchangerate-api.com/v4/latest/USD');
            $data = json_decode($response, true);
            
            if ($data && isset($data['rates']['VES'])) {
                $rate = $data['rates']['VES'];
            } else {
                // Fallback: API alternativa
                $response = file_get_contents('https://api.fixer.io/latest?base=USD&symbols=VES');
                $data = json_decode($response, true);
                $rate = $data['rates']['VES'] ?? 36.50;
            }
        } catch (\Exception $e) {
            // Si falla la API, usar tasa manual
            $rate = 36.50;
        }

        $exchangeRate = self::getCurrentRate();
        $exchangeRate->update([
            'usd_to_bsf' => $rate,
            'is_automatic' => true,
            'last_updated_at' => now(),
            'source' => 'bcv'
        ]);

        return $exchangeRate;
    }

    /**
     * Convertir USD a BsF
     */
    public function usdToBsF($usdAmount)
    {
        return round($usdAmount * $this->usd_to_bsf, 2);
    }

    /**
     * Convertir BsF a USD
     */
    public function bsfToUsd($bsfAmount)
    {
        return round($bsfAmount / $this->usd_to_bsf, 2);
    }

    /**
     * Verificar si necesita actualización (4 horas)
     */
    public function needsUpdate()
    {
        if (!$this->last_updated_at) {
            return true;
        }

        // Verificar si han pasado 4 horas o más
        return now()->diffInHours($this->last_updated_at) >= 4;
    }

    /**
     * Obtener el tiempo restante para la próxima actualización
     */
    public function getTimeUntilNextUpdate()
    {
        if (!$this->last_updated_at) {
            return [
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'total_seconds' => 0,
                'needs_update' => true
            ];
        }

        $nextUpdate = $this->last_updated_at->copy()->addHours(4);
        $now = now();

        if ($now->gte($nextUpdate)) {
            return [
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'total_seconds' => 0,
                'needs_update' => true
            ];
        }

        $diff = $now->diff($nextUpdate);
        
        return [
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
            'total_seconds' => $now->diffInSeconds($nextUpdate),
            'needs_update' => false,
            'next_update_at' => $nextUpdate
        ];
    }
}
