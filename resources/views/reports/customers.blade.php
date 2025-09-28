@extends('layouts.app')

@section('title', 'Reporte de Clientes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Clientes</h1>
            <p class="mt-1 text-gray-600">Clientes más activos</p>
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

    <!-- Tabla de Clientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Clientes Más Activos</h3>
        </div>
        <div class="card-body">
            @if($topCustomers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Total Órdenes</th>
                                <th>Total Gastado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $customer)
                            <tr>
                                <td class="font-bold text-primary-600">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                </td>
                                <td class="text-gray-500">{{ $customer->email }}</td>
                                <td class="text-gray-500">{{ $customer->phone }}</td>
                                <td class="font-semibold">{{ $customer->total_orders }}</td>
                                <td class="font-semibold text-green-600">${{ number_format($customer->total_spent, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <p class="mt-2">No hay datos de clientes para el período seleccionado</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
