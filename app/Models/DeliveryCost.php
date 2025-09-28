<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_distance',
        'max_distance',
        'cost',
        'description',
        'is_active',
    ];

    protected $casts = [
        'min_distance' => 'decimal:2',
        'max_distance' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Obtener el costo de delivery para una distancia específica
     */
    public static function getCostForDistance($distance)
    {
        return self::where('is_active', true)
            ->where('min_distance', '<=', $distance)
            ->where('max_distance', '>=', $distance)
            ->first();
    }

    /**
     * Obtener todos los rangos de costo activos
     */
    public static function getActiveRanges()
    {
        return self::where('is_active', true)
            ->orderBy('min_distance')
            ->get();
    }
}