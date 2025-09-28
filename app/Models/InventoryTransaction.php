<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'ingredient_id',
        'type',
        'quantity',
        'unit',
        'cost_per_unit',
        'total_cost',
        'supplier_id',
        'order_id',
        'user_id',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Relación con el ingrediente
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Relación con el proveedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relación con la orden
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para transacciones de entrada
     */
    public function scopeInbound($query)
    {
        return $query->whereIn('type', ['initial', 'purchase', 'adjustment_in', 'return']);
    }

    /**
     * Scope para transacciones de salida
     */
    public function scopeOutbound($query)
    {
        return $query->whereIn('type', ['sale', 'adjustment_out', 'consumption', 'waste']);
    }
}
