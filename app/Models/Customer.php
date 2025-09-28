<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cedula',
        'address',
        'birth_date',
        'total_orders',
        'total_spent',
        'last_order_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_order_at' => 'datetime',
        'is_active' => 'boolean',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Relación con las órdenes del cliente
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Obtener el historial de compras del cliente
     */
    public function getPurchaseHistory()
    {
        return $this->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener productos favoritos del cliente
     */
    public function getFavoriteProducts()
    {
        return $this->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.*, COUNT(*) as order_count')
            ->groupBy('products.id')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Scope para clientes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para buscar clientes
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
