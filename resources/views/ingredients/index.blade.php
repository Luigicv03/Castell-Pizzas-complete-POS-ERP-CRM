@extends('layouts.app')

@section('title', 'Gestión de Ingredientes')
@section('subtitle', 'Control de inventario de ingredientes')

@section('content')
<div class="space-y-6">
    <!-- Header con acciones -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ingredientes</h1>
            <p class="text-gray-600">Gestiona el inventario de ingredientes</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('ingredients.low-stock') }}" class="btn-warning btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Stock Bajo
            </a>
            @can('ingredients.create')
            <a href="{{ route('ingredients.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Ingrediente
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nombre, SKU o descripción..." class="form-input">
                </div>
                <div>
                    <label class="form-label">Proveedor</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">Todos los proveedores</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Solo stock bajo</span>
                    </label>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="btn-primary">Filtrar</button>
                    <a href="{{ route('ingredients.index') }}" class="btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de ingredientes -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th>SKU</th>
                            <th>Proveedor</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>Costo Unitario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredients as $ingredient)
                        <tr>
                            <td>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $ingredient->name }}</div>
                                    @if($ingredient->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($ingredient->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="font-mono text-sm">{{ $ingredient->sku }}</td>
                            <td>
                                @if($ingredient->supplier)
                                <span class="text-sm">{{ $ingredient->supplier->name }}</span>
                                @else
                                <span class="text-sm text-gray-400">Sin proveedor</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <span class="font-medium">{{ $ingredient->current_stock }} {{ $ingredient->unit }}</span>
                                    @if($ingredient->current_stock <= $ingredient->minimum_stock)
                                    <svg class="w-4 h-4 ml-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    @endif
                                </div>
                            </td>
                            <td class="text-sm">{{ $ingredient->minimum_stock }} {{ $ingredient->unit }}</td>
                            <td class="font-medium">${{ number_format($ingredient->cost_per_unit, 2) }}</td>
                            <td>
                                @if($ingredient->current_stock <= $ingredient->minimum_stock)
                                <span class="badge badge-danger">Stock Bajo</span>
                                @elseif($ingredient->current_stock <= $ingredient->minimum_stock * 1.5)
                                <span class="badge badge-warning">Stock Medio</span>
                                @else
                                <span class="badge badge-success">Stock OK</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('ingredients.show', $ingredient) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @can('ingredients.edit')
                                    <a href="{{ route('ingredients.edit', $ingredient) }}" 
                                       class="text-gray-600 hover:text-gray-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p>No se encontraron ingredientes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    @if($ingredients->hasPages())
    <div class="flex justify-center">
        {{ $ingredients->links() }}
    </div>
    @endif
</div>
@endsection
