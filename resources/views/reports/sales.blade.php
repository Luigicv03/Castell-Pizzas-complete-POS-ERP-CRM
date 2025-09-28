@extends('layouts.app')

@section('title', 'Reporte de Ventas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Ventas</h1>
            <p class="mt-1 text-gray-600">Análisis de ventas diarias</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-48">
                    <label for="start_date" class="form-label">Fecha Inicio</label>
                    <input type="date" id="start_date" name="start_date" 
                           value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}" 
                           class="form-input">
                </div>
                <div class="flex-1 min-w-48">
                    <label for="end_date" class="form-label">Fecha Fin</label>
                    <input type="date" id="end_date" name="end_date" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                           class="form-input">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Ventas Diarias</h3>
        </div>
        <div class="card-body">
            @if($dailySales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total Ventas</th>
                                <th>Número de Órdenes</th>
                                <th>Valor Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailySales as $sale)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($sale->period)->format('d/m/Y') }}</td>
                                <td class="font-semibold text-green-600">${{ number_format($sale->total_sales, 2) }}</td>
                                <td>{{ $sale->total_orders }}</td>
                                <td>${{ number_format($sale->avg_order_value, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="mt-2">No hay datos de ventas para el período seleccionado</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection