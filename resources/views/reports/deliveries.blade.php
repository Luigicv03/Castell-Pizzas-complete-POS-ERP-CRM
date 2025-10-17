@extends('layouts.app')

@section('title', 'Reporte de Deliverys')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Deliverys</h1>
            <p class="mt-1 text-gray-600">Análisis de deliverys diarios</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.deliveries.export', request()->query()) }}" 
               class="btn btn-success" 
               title="Exportar a CSV">
                📊 Exportar CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                ← Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <form method="GET" class="space-y-4">
                <!-- Filtros de período predefinidos -->
                <div>
                    <label class="form-label">Período</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="period" value="1_week" 
                                class="btn {{ $period === '1_week' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            1 Semana
                        </button>
                        <button type="submit" name="period" value="2_weeks" 
                                class="btn {{ $period === '2_weeks' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            2 Semanas
                        </button>
                        <button type="submit" name="period" value="1_month" 
                                class="btn {{ $period === '1_month' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            1 Mes
                        </button>
                        <button type="submit" name="period" value="3_months" 
                                class="btn {{ $period === '3_months' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            3 Meses
                        </button>
                        <button type="submit" name="period" value="6_months" 
                                class="btn {{ $period === '6_months' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            6 Meses
                        </button>
                        <button type="submit" name="period" value="1_year" 
                                class="btn {{ $period === '1_year' ? 'btn-primary' : 'btn-outline' }} btn-sm">
                            1 Año
                        </button>
                    </div>
                </div>

                <!-- Filtros de fecha personalizada -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="form-label">Fecha Inicio</label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ $dateRange['start']->format('Y-m-d') }}"
                               class="form-control">
                    </div>
                    <div>
                        <label for="end_date" class="form-label">Fecha Fin</label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ $dateRange['end']->format('Y-m-d') }}"
                               class="form-control">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" name="period" value="custom" class="btn btn-primary">
                        🔍 Filtrar
                    </button>
                    <a href="{{ route('reports.deliveries') }}" class="btn btn-outline-secondary">
                        🔄 Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen del período -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-blue-600">${{ number_format($periodTotals['total_delivery_cost'], 2) }}</div>
                <div class="text-sm text-gray-600">Total Costo Delivery</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-green-600">{{ $periodTotals['total_delivery_orders'] }}</div>
                <div class="text-sm text-gray-600">Total Deliverys</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-purple-600">${{ number_format($periodTotals['avg_delivery_cost'], 2) }}</div>
                <div class="text-sm text-gray-600">Costo Promedio Delivery</div>
            </div>
        </div>
    </div>

    <!-- Reporte de Deliverys Diarios -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Deliverys Diarios</h3>
            <p class="text-sm text-gray-600">Haz clic en "Ver Detalle" para ver las órdenes de delivery de cada día</p>
        </div>
        <div class="card-body">
            @if($dailyDeliveries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total Costo Delivery</th>
                                <th>Número de Deliverys</th>
                                <th>Costo Promedio Delivery</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyDeliveries as $delivery)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($delivery->period)->format('d/m/Y') }}</td>
                                <td class="font-semibold text-blue-600">${{ number_format($delivery->total_delivery_cost, 2) }}</td>
                                <td>{{ $delivery->total_delivery_orders }}</td>
                                <td>${{ number_format($delivery->avg_delivery_cost, 2) }}</td>
                                <td>
                                    <a href="{{ route('reports.deliveries.day', $delivery->period) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Ver detalle del día">
                                        📋 Ver Detalle
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
                    <p class="mt-2">No hay deliverys para el período seleccionado</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
