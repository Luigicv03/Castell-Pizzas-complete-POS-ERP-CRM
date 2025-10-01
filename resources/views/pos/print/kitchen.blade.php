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
            line-height: 1.2;
            margin: 0;
            padding: 8px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h1 {
            margin: 0 0 3px 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .order-info {
            margin-bottom: 8px;
        }
        .order-info p {
            margin: 2px 0;
        }
        .items h3 {
            margin: 8px 0 5px 0;
            font-size: 13px;
        }
        .item {
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #333;
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
            font-weight: bold;
            color: #000;
            margin-top: 2px;
            font-size: 12px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COCINA</h1>
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
                @if($item->children && $item->children->count() > 0)
                <div style="margin-left: 15px; margin-top: 2px;">
                    @foreach($item->children as $child)
                    <div class="item-notes">+ {{ $child->product->name }} ({{ $child->quantity }}x)</div>
                    @endforeach
                </div>
                @endif
                @if($item->notes)
                <div class="item-notes">Nota: {{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        @else
            <p>No hay productos para cocina en esta orden.</p>
        @endif
    </div>

    <div class="footer no-print">
        <button onclick="window.print()">🖨️ Imprimir</button>
        <button onclick="window.close()">❌ Cerrar</button>
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
