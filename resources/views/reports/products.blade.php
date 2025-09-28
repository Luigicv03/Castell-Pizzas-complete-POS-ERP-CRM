@extends('layouts.app')

@section('title', 'Reporte de Productos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Productos</h1>
            <p class="mt-1 text-gray-600">Productos más vendidos</p>
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

    <!-- Tabla de Productos -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Productos Más Vendidos</h3>
        </div>
        <div class="card-body">
            @if($topProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Destacado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $product)
                            <tr>
                                <td class="font-bold text-primary-600">{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->description }}</div>
                                    </div>
                                </td>
                                <td class="font-semibold">${{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_featured)
                                        <span class="badge badge-warning">Destacado</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="mt-2">No hay datos de productos para el período seleccionado</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
