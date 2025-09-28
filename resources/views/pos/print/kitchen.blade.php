<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda Cocina - Orden #{{ $order->order_number }}</title>
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
        <h1>🍕 COCINA 🍕</h1>
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
        <h3>PRODUCTOS PARA COCINA:</h3>
        @if($kitchenItems->count() > 0)
            @foreach($kitchenItems as $item)
            <div class="item">
                <div class="item-quantity">{{ $item->quantity }}x</div>
                <div class="item-name">{{ $item->product->name }}</div>
                @if($item->notes)
                <div class="item-notes">Nota: {{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        @else
            <p>No hay productos para cocina en esta orden.</p>
        @endif
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
