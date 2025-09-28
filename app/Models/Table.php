<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'position_x',
        'position_y',
        'status',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
    ];

    const STATUS_FREE = 'free';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_PENDING_PAYMENT = 'pending_payment';

    /**
     * Relación con las órdenes de la mesa
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Obtener la orden activa de la mesa
     */
    public function activeOrder()
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->latest()
            ->first();
    }

    /**
     * Sincronizar el estado de la mesa con las órdenes activas
     */
    public function syncStatus()
    {
        $activeOrder = $this->activeOrder();
        
        if ($activeOrder) {
            // Si hay una orden activa, la mesa debe estar ocupada
            if ($this->status !== self::STATUS_OCCUPIED) {
                $this->update(['status' => self::STATUS_OCCUPIED]);
            }
        } else {
            // Si no hay órdenes activas, la mesa debe estar libre
            if ($this->status !== self::STATUS_FREE) {
                $this->update(['status' => self::STATUS_FREE]);
            }
        }
        
        return $this->fresh();
    }

    /**
     * Verificar si la mesa está libre
     */
    public function isFree(): bool
    {
        return $this->status === self::STATUS_FREE;
    }

    /**
     * Verificar si la mesa está ocupada
     */
    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    /**
     * Verificar si la mesa está reservada
     */
    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Verificar si la mesa tiene pago pendiente
     */
    public function hasPendingPayment(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT;
    }

    /**
     * Obtener el color de la mesa según su estado
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_FREE => 'green',
            self::STATUS_OCCUPIED => 'red',
            self::STATUS_RESERVED => 'yellow',
            self::STATUS_PENDING_PAYMENT => 'orange',
            default => 'gray',
        };
    }

    /**
     * Obtener las clases CSS para el color del estado
     */
    public function getStatusColorClass(): string
    {
        return match($this->status) {
            self::STATUS_FREE => 'bg-green-100 border-green-400 text-green-700',
            self::STATUS_OCCUPIED => 'bg-red-100 border-red-400 text-red-700',
            self::STATUS_RESERVED => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            self::STATUS_PENDING_PAYMENT => 'bg-orange-100 border-orange-400 text-orange-700',
            default => 'bg-gray-100 border-gray-400 text-gray-700',
        };
    }

    /**
     * Obtener el texto del estado
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_FREE => 'Libre',
            self::STATUS_OCCUPIED => 'Ocupada',
            self::STATUS_RESERVED => 'Reservada',
            self::STATUS_PENDING_PAYMENT => 'Pago Pendiente',
            default => 'Desconocido',
        };
    }

    /**
     * Scope para mesas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para mesas por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para mesas libres
     */
    public function scopeFree($query)
    {
        return $query->where('status', self::STATUS_FREE);
    }

    /**
     * Scope para mesas ocupadas
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }
}
