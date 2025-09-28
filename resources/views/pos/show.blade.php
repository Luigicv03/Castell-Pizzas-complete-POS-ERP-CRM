@extends('layouts.app')

@section('title', 'Detalle del Pedido')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <!-- Header del Pedido -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-sm text-gray-500">Orden: {{ $order->order_number }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->getStatusColorClass() }}">
                {{ $order->getStatusText() }}
            </span>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                {{ $order->getTypeText() }}
            </span>
        </div>
    </div>

    <!-- Información del Pedido -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Información del Cliente -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Información del Cliente</h3>
            @if($order->customer)
                <div class="space-y-2">
                    <p><span class="font-medium">Nombre:</span> {{ $order->customer->name }}</p>
                    <p><span class="font-medium">Teléfono:</span> {{ $order->customer->phone ?? 'N/A' }}</p>
                    <p><span class="font-medium">Email:</span> {{ $order->customer->email ?? 'N/A' }}</p>
                </div>
            @else
                <p class="text-gray-500">Cliente general</p>
            @endif
        </div>

        <!-- Información de la Mesa -->
        @if($order->table)
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Información de la Mesa</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Mesa:</span> {{ $order->table->name }}</p>
                <p><span class="font-medium">Capacidad:</span> {{ $order->table->capacity }} personas</p>
                <p><span class="font-medium">Estado:</span> 
                    <span class="px-2 py-1 rounded text-xs {{ $order->table->status === 'free' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $order->table->getStatusText() }}
                    </span>
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Productos del Pedido -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos del Pedido</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                            @if($item->notes)
                                <div class="text-sm text-gray-500">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen del Pedido -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen del Pedido</h3>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Impuestos (16%):</span>
                <span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="flex justify-between">
                <span>Descuento:</span>
                <span>-${{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-bold border-t pt-2">
                <span>Total:</span>
                <span class="text-primary-600">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Notas del Pedido -->
    @if($order->notes)
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notas del Pedido</h3>
        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Pagos -->
    @if($order->payments->count() > 0)
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagos Realizados</h3>
        <div class="space-y-3">
            @foreach($order->payments as $payment)
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $payment->getPaymentMethodText() }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                        @if($payment->reference)
                            <p class="text-sm text-gray-500">Referencia: {{ $payment->reference }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-green-600">${{ number_format($payment->amount, 2) }}</p>
                        <span class="px-2 py-1 text-xs rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Acciones -->
    <div class="flex items-center justify-between">
        <div class="flex space-x-3">
            <a href="{{ route('pos.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al POS
            </a>
            
            @if($order->table)
            <a href="{{ route('pos.table.order', $order->table->id) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Ver Orden de Mesa
            </a>
            @endif
        </div>

        <div class="flex space-x-3">
            @if($order->status === 'pending' || $order->status === 'preparing')
            <a href="{{ route('pos.print.kitchen', $order->id) }}" target="_blank" class="btn-warning">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir Cocina
            </a>
            
            <a href="{{ route('pos.print.bar', $order->id) }}" target="_blank" class="btn-info">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir Barra
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
