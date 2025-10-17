@extends('layouts.app')

@section('title', 'Detalle de Deliverys - ' . $selectedDate->format('d/m/Y'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle de Deliverys</h1>
            <p class="mt-1 text-gray-600">{{ $selectedDate->format('l, d \d\e F \d\e Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.deliveries.day.export', $selectedDate->format('Y-m-d')) }}" 
               class="btn btn-success" 
               title="Exportar a CSV">
                📊 Exportar CSV
            </a>
            <a href="{{ route('reports.deliveries') }}" class="btn btn-outline-secondary">
                ← Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Resumen del día -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-blue-600">${{ number_format($dayTotals['total_delivery_cost'], 2) }}</div>
                <div class="text-sm text-gray-600">Total Costo Delivery</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-green-600">{{ $dayTotals['total_delivery_orders'] }}</div>
                <div class="text-sm text-gray-600">Total Deliverys</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-purple-600">${{ number_format($dayTotals['avg_delivery_cost'], 2) }}</div>
                <div class="text-sm text-gray-600">Costo Promedio Delivery</div>
            </div>
        </div>
    </div>

    <!-- Detalle de Deliverys del Día -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Deliverys del {{ $selectedDate->format('d/m/Y') }}</h3>
            <p class="text-sm text-gray-600">Haz clic en una orden para ver el detalle completo</p>
        </div>
        <div class="card-body">
            @if($dayDeliveries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Dirección</th>
                                <th>Costo Delivery</th>
                                <th>Total Orden</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dayDeliveries as $order)
                            <tr class="hover:bg-gray-50">
                                <td>
                                    <div class="font-semibold">
                                        {{ $order->custom_title ?: 'Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="text-xs text-gray-500">ID: {{ $order->id }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $order->created_at->format('H:i') }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('A') }}</div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <div class="font-medium">{{ $order->customer->name }}</div>
                                        @if($order->customer->phone)
                                            <div class="text-xs text-gray-500">{{ $order->customer->phone }}</div>
                                        @endif
                                    @elseif($order->customer_name)
                                        <div class="font-medium">{{ $order->customer_name }}</div>
                                    @else
                                        <span class="text-gray-500">Cliente General</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-sm">
                                        {{ $order->delivery_address ?? 'No especificada' }}
                                    </div>
                                </td>
                                <td class="font-semibold text-blue-600">${{ number_format($order->delivery_cost, 2) }}</td>
                                <td class="font-semibold text-green-600">${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'ready') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $order->getStatusText() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.details', $order->id) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Ver detalle de la orden">
                                        👁️ Ver
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-2">No hay deliverys para el {{ $selectedDate->format('d/m/Y') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
