<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'supplier_id',
        'unit',
        'cost_per_unit',
        'minimum_stock',
        'current_stock',
        'location',
        'barcode',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'minimum_stock' => 'decimal:3',
        'current_stock' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el proveedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relación con las transacciones de inventario
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Relación con los ingredientes de recetas
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Verificar si el stock está bajo
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Obtener el valor total del stock
     */
    public function getTotalStockValue(): float
    {
        return $this->current_stock * $this->cost_per_unit;
    }

    /**
     * Scope para ingredientes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ingredientes con stock bajo
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }
}
