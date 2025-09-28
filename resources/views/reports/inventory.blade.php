@extends('layouts.app')

@section('title', 'Reporte de Inventario')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reporte de Inventario</h1>
            <p class="mt-1 text-gray-600">Estado actual del inventario</p>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-xl font-semibold text-gray-900">Estado del Inventario</h3>
        </div>
        <div class="card-body">
            @if($ingredients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ingrediente</th>
                                <th>Proveedor</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Unidad</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingredients as $ingredient)
                            <tr>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $ingredient->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $ingredient->description }}</div>
                                </td>
                                <td class="text-gray-500">{{ $ingredient->supplier->name ?? 'N/A' }}</td>
                                <td class="font-semibold">{{ $ingredient->current_stock }}</td>
                                <td class="font-semibold">{{ $ingredient->minimum_stock }}</td>
                                <td class="text-gray-500">{{ $ingredient->unit }}</td>
                                <td>
                                    @if($ingredient->current_stock <= $ingredient->minimum_stock)
                                        <span class="badge badge-danger">Stock Bajo</span>
                                    @elseif($ingredient->current_stock <= ($ingredient->minimum_stock * 2))
                                        <span class="badge badge-warning">Stock Medio</span>
                                    @else
                                        <span class="badge badge-success">Stock Bueno</span>
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
                    <p class="mt-2">No hay ingredientes registrados</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
