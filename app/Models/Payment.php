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
        'method',
        'amount',
        'amount_usd',
        'amount_bsf',
        'exchange_rate',
        'currency',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'amount_bsf' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    // Métodos de utilidad
    public function getPaymentMethodText(): string
    {
        return match($this->payment_method) {
            'cash' => 'Efectivo USD',
            'mobile_payment' => 'Pago Móvil',
            'zelle' => 'Zelle',
            'binance' => 'Binance',
            'pos' => 'Punto de Venta',
            'transfer' => 'Transferencia',
            'card' => 'Tarjeta',
            default => 'Desconocido',
        };
    }

    public function getFormattedAmountText(): string
    {
        $text = '$' . number_format($this->amount, 2);
        
        // Si el pago fue en bolívares, mostrar también el monto en BsF
        if ($this->amount_bsf && $this->amount_bsf > 0) {
            $text .= ' (' . number_format($this->amount_bsf, 2) . ' BsF)';
        }
        
        return $text;
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

    // Métodos para multimoneda
    public function getAmountInUsd(): float
    {
        if ($this->currency === 'USD') {
            return $this->amount_usd ?? $this->amount ?? 0;
        } elseif ($this->currency === 'BSF' && $this->exchange_rate) {
            return round(($this->amount_bsf ?? $this->amount ?? 0) / $this->exchange_rate, 2);
        }
        return $this->amount ?? 0;
    }

    public function getAmountInBsF(): float
    {
        if ($this->currency === 'BSF') {
            return $this->amount_bsf ?? $this->amount ?? 0;
        } elseif ($this->currency === 'USD' && $this->exchange_rate) {
            return round(($this->amount_usd ?? $this->amount ?? 0) * $this->exchange_rate, 2);
        }
        return $this->amount ?? 0;
    }

    public function setAmountFromUsd(float $usdAmount, float $exchangeRate): void
    {
        $this->amount_usd = $usdAmount;
        $this->amount_bsf = round($usdAmount * $exchangeRate, 2);
        $this->amount = $usdAmount;
        $this->exchange_rate = $exchangeRate;
        $this->currency = 'USD';
    }

    public function setAmountFromBsF(float $bsfAmount, float $exchangeRate): void
    {
        $this->amount_bsf = $bsfAmount;
        $this->amount_usd = round($bsfAmount / $exchangeRate, 2);
        $this->amount = $this->amount_usd;
        $this->exchange_rate = $exchangeRate;
        $this->currency = 'BSF';
    }

    // Relaciones
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}