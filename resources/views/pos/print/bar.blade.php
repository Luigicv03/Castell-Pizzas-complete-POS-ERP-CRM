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
            .no-print { display: none !important; }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CAJA - RESUMEN DEL PEDIDO</h1>
        <h2>Orden #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <div class="order-info">
        <p><strong>Tipo:</strong> {{ $order->getTypeText() }}</p>
        @if($order->table)
        <p><strong>Mesa:</strong> {{ $order->table->name }}</p>
        @endif
        <p><strong>Cliente:</strong> {{ $order->customer ? $order->customer->name : 'Cliente General' }}</p>
        @if($order->customer && $order->customer->cedula)
        <p><strong>Cédula:</strong> {{ $order->customer->cedula }}</p>
        @endif
        @if($order->customer && $order->customer->phone)
        <p><strong>Teléfono:</strong> {{ $order->customer->phone }}</p>
        @endif
        <p><strong>Hora:</strong> {{ $order->created_at->format('H:i') }}</p>
        <p><strong>Mesero:</strong> {{ $order->user->name }}</p>
    </div>

    <div class="items">
        <h3>DETALLE DEL PEDIDO:</h3>
        @foreach($order->items as $item)
        <div class="item">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div class="item-quantity">{{ $item->quantity }}x</div>
                    <div class="item-name">{{ $item->product->name }}</div>
                    @if($item->children && $item->children->count() > 0)
                    <div style="margin-left: 20px; margin-top: 5px;">
                        @foreach($item->children as $child)
                        <div class="item-notes">+ {{ $child->product->name }} ({{ $child->quantity }}x) - ${{ number_format($child->total_price, 2) }}</div>
                        @endforeach
                    </div>
                    @endif
                    @if($item->notes)
                    <div class="item-notes">Nota: {{ $item->notes }}</div>
                    @endif
                </div>
                <div class="item-price">${{ number_format($item->total_price, 2) }}</div>
            </div>
        </div>
        @endforeach
        
        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
        </div>
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
