<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'product_id',
        'servings',
        'prep_time',
        'cook_time',
        'instructions',
    ];

    protected $casts = [
        'servings' => 'integer',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
    ];

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con los ingredientes de la receta
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Obtener el tiempo total de preparación
     */
    public function getTotalTimeAttribute(): int
    {
        return $this->prep_time + $this->cook_time;
    }

    /**
     * Calcular el costo total de la receta
     */
    public function calculateTotalCost(): float
    {
        return $this->recipeIngredients->sum(function($recipeIngredient) {
            return $recipeIngredient->quantity * $recipeIngredient->ingredient->cost_per_unit;
        });
    }

    /**
     * Verificar disponibilidad de ingredientes
     */
    public function checkIngredientAvailability(): array
    {
        $unavailable = [];
        
        foreach ($this->recipeIngredients as $recipeIngredient) {
            if ($recipeIngredient->ingredient->current_stock < $recipeIngredient->quantity) {
                $unavailable[] = [
                    'ingredient' => $recipeIngredient->ingredient->name,
                    'required' => $recipeIngredient->quantity,
                    'available' => $recipeIngredient->ingredient->current_stock,
                    'missing' => $recipeIngredient->quantity - $recipeIngredient->ingredient->current_stock
                ];
            }
        }
        
        return $unavailable;
    }
}
