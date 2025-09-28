<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda Barra - Orden #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .order-info {
            margin-bottom: 15px;
        }
        .item {
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .item-name {
            font-weight: bold;
            font-size: 14px;
        }
        .item-quantity {
            font-size: 16px;
            font-weight: bold;
        }
        .item-notes {
            font-style: italic;
            color: #666;
            margin-top: 2px;
        }
        .item-price {
            font-weight: bold;
            color: #333;
        }
        .totals {
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1> BARRA PIZZERIA </h1>
        <h2>Comanda #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <div class="order-info">
        <p><strong>Tipo:</strong> {{ $order->getTypeText() }}</p>
        @if($order->table)
        <p><strong>Mesa:</strong> {{ $order->table->name }}</p>
        @endif
        <p><strong>Cliente:</strong> {{ $order->customer ? $order->customer->name : 'Cliente General' }}</p>
        <p><strong>Hora:</strong> {{ $order->created_at->format('H:i') }}</p>
        <p><strong>Mesero:</strong> {{ $order->user->name }}</p>
    </div>

    <div class="items">
        <h3>TODA LA ORDEN:</h3>
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-quantity">{{ $item->quantity }}x</div>
            <div class="item-name">{{ $item->product->name }}</div>
            <div class="item-price">${{ number_format($item->total_price, 2) }}</div>
            @if($item->notes)
            <div class="item-notes">Nota: {{ $item->notes }}</div>
            @endif
        </div>
        @endforeach
        
        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="total-line">
                <span>Impuestos (16%):</span>
                <span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="total-line total-final">
                <span>TOTAL:</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Impreso el {{ now()->format('d/m/Y H:i') }}</p>
        <button onclick="window.print()" class="no-print">🖨️ Imprimir</button>
        <button onclick="window.close()" class="no-print">❌ Cerrar</button>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
