@extends('layouts.app')

@section('title', 'Detalles del Pedido')
@section('subtitle', 'Información completa del pedido')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalles del Pedido</h1>
            <p class="text-gray-600 mt-1">Información completa del pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
            @if($order->table_id)
            <a href="{{ route('pos.table.order', $order->table_id) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Ver Orden de Mesa
            </a>
            @endif
        </div>
    </div>

    <!-- Order Status Banner -->
    <div class="bg-gradient-to-r {{ $order->status === 'pending' ? 'from-yellow-500 to-yellow-600' : ($order->status === 'preparing' ? 'from-blue-500 to-blue-600' : ($order->status === 'ready' ? 'from-green-500 to-green-600' : 'from-gray-500 to-gray-600')) }} rounded-xl text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-sm opacity-90">
                    {{ $order->getTypeText() }} • 
                    {{ $order->getStatusText() }} • 
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">${{ number_format($order->total_amount, 2) }}</div>
                <div class="text-sm opacity-90">{{ $order->items->count() }} productos</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Información del Cliente</h3>
                </div>
                <div class="card-body">
                    @if($order->customer)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                                <p class="text-gray-900 font-medium">{{ $order->customer->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Cédula</label>
                                <p class="text-gray-900">{{ $order->customer->cedula ?? 'No registrada' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Teléfono</label>
                                <p class="text-gray-900">{{ $order->customer->phone ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-gray-900">{{ $order->customer->email ?? 'No registrado' }}</p>
                            </div>
                            @if($order->customer->address)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-500">Dirección</label>
                                <p class="text-gray-900">{{ $order->customer->address }}</p>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Cliente General</h4>
                            <p class="text-gray-500">No se registró información del cliente</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Productos del Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->category->name ?? 'Sin categoría' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes || $order->kitchen_notes)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Notas del Pedido</h3>
                </div>
                <div class="card-body">
                    @if($order->notes)
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-500">Notas Generales</label>
                        <p class="text-gray-900 mt-1">{{ $order->notes }}</p>
                    </div>
                    @endif
                    @if($order->kitchen_notes)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Notas de Cocina</label>
                        <p class="text-gray-900 mt-1">{{ $order->kitchen_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Resumen del Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->type === 'delivery' && $order->delivery_cost > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery:</span>
                            <span class="font-medium">${{ number_format($order->delivery_cost, 2) }}</span>
                        </div>
                        @endif
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Descuento:</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Detalles del Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Número de Orden</label>
                            <p class="text-gray-900 font-mono">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Número Diario</label>
                            <p class="text-gray-900 font-mono">#{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tipo de Pedido</label>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-900">{{ $order->getTypeText() }}</span>
                                @if($order->type === 'delivery')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Delivery
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Estado</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                {{ $order->getStatusText() }}
                            </span>
                        </div>
                        @if($order->table)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mesa</label>
                            <p class="text-gray-900">{{ $order->table->name }} (Capacidad: {{ $order->table->capacity }})</p>
                        </div>
                        @endif
                        @if($order->type === 'delivery' && $order->delivery_cost > 0)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Costo de Delivery</label>
                            <p class="text-gray-900 font-semibold">${{ number_format($order->delivery_cost, 2) }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mesero</label>
                            <p class="text-gray-900">{{ $order->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha y Hora</label>
                            <p class="text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($order->prepared_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Preparado</label>
                            <p class="text-gray-900">{{ $order->prepared_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($order->delivered_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Entregado</label>
                            <p class="text-gray-900">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payments->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Información de Pago</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        @foreach($order->payments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $payment->getPaymentMethodText() }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">
                                        ${{ number_format($payment->amount, 2) }}
                                        @if($payment->amount_bsf && $payment->amount_bsf > 0)
                                            <span class="text-sm text-gray-600 font-normal">
                                                ({{ number_format($payment->amount_bsf, 2) }} BsF)
                                            </span>
                                        @endif
                                    </p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $payment->getStatusText() }}
                                    </span>
                                </div>
                            </div>
                            @if($payment->reference)
                            <div class="mt-2">
                                <label class="text-xs font-medium text-gray-500">Referencia:</label>
                                <p class="text-sm text-gray-700 font-mono">{{ $payment->reference }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Pagado:</span>
                                <span>${{ number_format($order->payments->sum('amount'), 2) }}</span>
                            </div>
                            @if($order->payments->sum('amount') < $order->total_amount)
                            <div class="flex justify-between text-sm text-red-600 mt-1">
                                <span>Pendiente:</span>
                                <span>${{ number_format($order->total_amount - $order->payments->sum('amount'), 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Acciones</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <a href="{{ route('pos.print.kitchen', $order->id) }}" target="_blank" class="w-full btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir Comanda Cocina
                        </a>
                        <a href="{{ route('pos.print.bar', $order->id) }}" target="_blank" class="w-full btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir Comanda Barra
                        </a>
                        @if($order->table_id)
                        <a href="{{ route('pos.table.order', $order->table_id) }}" class="w-full btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Gestionar Orden de Mesa
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
