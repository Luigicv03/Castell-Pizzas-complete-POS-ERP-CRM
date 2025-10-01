<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Tipos de orden
    const TYPE_DINE_IN = 'dine_in';
    const TYPE_TAKEAWAY = 'takeaway';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_PICKUP = 'pickup';

    // Estados de orden
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'daily_number',
        'customer_id',
        'customer_name',
        'table_id',
        'user_id',
        'status',
        'type',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'delivery_cost',
        'total_amount',
        'exchange_rate',
        'currency',
        'notes',
        'kitchen_notes',
        'prepared_at',
        'delivered_at',
    ];

    protected $casts = [
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    // Relaciones
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Métodos de utilidad
    public function getTypeText(): string
    {
        return match($this->type) {
            self::TYPE_DINE_IN => 'Comer aquí',
            self::TYPE_TAKEAWAY => 'Para llevar',
            self::TYPE_DELIVERY => 'Delivery',
            self::TYPE_PICKUP => 'Pickup',
            default => 'Desconocido',
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PREPARING => 'Preparando',
            self::STATUS_READY => 'Listo',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getStatusColorClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_PREPARING => 'badge-info',
            self::STATUS_READY => 'badge-success',
            self::STATUS_DELIVERED => 'badge-secondary',
            self::STATUS_CANCELLED => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'preparing', 'ready']);
    }
}
