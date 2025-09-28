<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // Métodos de pago
    const METHOD_CASH = 'cash';
    const METHOD_MOBILE_PAYMENT = 'mobile_payment';
    const METHOD_ZELLE = 'zelle';
    const METHOD_BINANCE = 'binance';
    const METHOD_POS = 'pos';
    const METHOD_TRANSFER = 'transfer';

    // Estados de pago
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'reference',
        'status',
        'notes',
        'user_id',
        'currency',
        'exchange_rate',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Métodos de utilidad
    public function getPaymentMethodText(): string
    {
        return match($this->payment_method) {
            self::METHOD_CASH => 'Efectivo',
            self::METHOD_MOBILE_PAYMENT => 'Pago Móvil',
            self::METHOD_ZELLE => 'Zelle',
            self::METHOD_BINANCE => 'Binance',
            self::METHOD_POS => 'Punto de Venta',
            self::METHOD_TRANSFER => 'Transferencia',
            default => 'Desconocido',
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_FAILED => 'Fallido',
            self::STATUS_REFUNDED => 'Reembolsado',
            default => 'Desconocido',
        };
    }

    public function getStatusColorClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_FAILED => 'badge-danger',
            self::STATUS_REFUNDED => 'badge-info',
            default => 'badge-secondary',
        };
    }

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}