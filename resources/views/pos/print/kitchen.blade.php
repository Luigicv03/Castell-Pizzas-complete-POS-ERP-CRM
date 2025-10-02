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
        <p><strong>Cliente:</strong> {{ $order->customer ? $order->customer->name : ($order->customer_name ?: 'Cliente General') }}</p>
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
                
                @php
                    $productName = strtolower($item->product->name);
                    $is4Estaciones = str_contains($productName, '4 estaciones');
                    $isMulticereal = str_contains($productName, 'multicereal');
                    $baseIngredients = [];
                    
                    // Extraer ingredientes base desde las notas
                    if ($item->notes && str_contains($item->notes, 'Ingredientes base:')) {
                        $notesText = str_replace('Ingredientes base:', '', $item->notes);
                        $baseIngredients = array_map('trim', explode(',', $notesText));
                    }
                @endphp
                
                @if($is4Estaciones && count($baseIngredients) == 4)
                    <!-- Círculo dividido en 4 para Pizza 4 Estaciones -->
                    <div style="margin: 5px 0; display: flex; justify-content: center;">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <!-- Círculo exterior -->
                            <circle cx="50" cy="50" r="48" fill="none" stroke="#000" stroke-width="2"/>
                            <!-- Líneas divisorias -->
                            <line x1="50" y1="2" x2="50" y2="98" stroke="#000" stroke-width="2"/>
                            <line x1="2" y1="50" x2="98" y2="50" stroke="#000" stroke-width="2"/>
                            <!-- Textos en cada cuadrante -->
                            <text x="50" y="25" text-anchor="middle" font-size="8" font-weight="bold">{{ $baseIngredients[0] ?? '' }}</text>
                            <text x="75" y="55" text-anchor="middle" font-size="8" font-weight="bold">{{ $baseIngredients[1] ?? '' }}</text>
                            <text x="50" y="80" text-anchor="middle" font-size="8" font-weight="bold">{{ $baseIngredients[2] ?? '' }}</text>
                            <text x="25" y="55" text-anchor="middle" font-size="8" font-weight="bold">{{ $baseIngredients[3] ?? '' }}</text>
                        </svg>
                    </div>
                @elseif($isMulticereal && count($baseIngredients) == 2)
                    <!-- Lista simple para Multicereal (2 ingredientes) -->
                    <div style="margin-left: 15px; margin-top: 3px;">
                        <div class="item-notes">✓ {{ $baseIngredients[0] ?? '' }}</div>
                        <div class="item-notes">✓ {{ $baseIngredients[1] ?? '' }}</div>
                    </div>
                @endif
                
                @if($item->children && $item->children->count() > 0)
                <div style="margin-left: 15px; margin-top: 2px;">
                    @foreach($item->children as $child)
                        @if($child->product_id && $child->product->price > 0)
                        <div class="item-notes">+ {{ $child->product->name }} ({{ $child->quantity }}x)</div>
                        @endif
                    @endforeach
                </div>
                @endif
                
                {{-- Mostrar notas normales (no de ingredientes base) --}}
                @if($item->notes && !str_contains($item->notes, 'Ingredientes base:'))
                <div style="margin-top: 5px; padding: 5px; background: #fffacd; border: 1px solid #ffd700; border-radius: 3px;">
                    <div style="font-weight: bold; font-size: 11px; margin-bottom: 2px;">📝 NOTA ESPECIAL:</div>
                    <div style="font-size: 12px; font-weight: bold;">{{ $item->notes }}</div>
                </div>
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
