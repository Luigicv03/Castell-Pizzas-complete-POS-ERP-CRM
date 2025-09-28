@extends('layouts.app')

@section('title', 'Stock Bajo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-red-700">Ingredientes con Stock Bajo</h1>
            <p class="text-gray-600">Ingredientes que requieren reabastecimiento urgente</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('ingredients.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Ingredientes
            </a>
            <a href="{{ route('inventory-transactions.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Compra
            </a>
        </div>
    </div>

    <!-- Alert -->
    @if($ingredients->count() > 0)
    <div class="card border-l-4 border-red-500 bg-red-50">
        <div class="card-body">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-red-800">¡Atención!</h3>
                    <p class="text-red-700">Tienes {{ $ingredients->count() }} ingredientes con stock bajo que requieren reabastecimiento inmediato.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de Ingredientes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ingredients as $ingredient)
        <div class="card border-l-4 border-red-500">
            <div class="card-body">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $ingredient->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $ingredient->sku }}</p>
                    </div>
                    <span class="badge badge-danger">Stock Bajo</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Stock Actual:</span>
                        <span class="text-sm font-medium text-red-600">{{ $ingredient->current_stock }} {{ $ingredient->unit }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Stock Mínimo:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ingredient->minimum_stock }} {{ $ingredient->unit }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Diferencia:</span>
                        <span class="text-sm font-medium text-red-600">
                            {{ $ingredient->current_stock - $ingredient->minimum_stock }} {{ $ingredient->unit }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Proveedor:</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $ingredient->supplier ? $ingredient->supplier->name : 'Sin proveedor' }}
                        </span>
                    </div>
                    @if($ingredient->location)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ubicación:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ingredient->location }}</span>
                    </div>
                    @endif
                </div>

                <!-- Barra de Progreso -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Stock</span>
                        <span>{{ number_format(($ingredient->current_stock / $ingredient->minimum_stock) * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" 
                             style="width: {{ min(($ingredient->current_stock / $ingredient->minimum_stock) * 100, 100) }}%"></div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('ingredients.show', $ingredient) }}" 
                       class="btn-secondary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver
                    </a>
                    <a href="{{ route('ingredients.edit', $ingredient) }}" 
                       class="btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="card">
                <div class="card-body text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">¡Excelente!</h3>
                    <p class="text-gray-600">No hay ingredientes con stock bajo. Todos los ingredientes tienen stock suficiente.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Resumen -->
    @if($ingredients->count() > 0)
    <div class="card bg-gray-50">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Stock Bajo</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $ingredients->count() }}</div>
                    <div class="text-sm text-gray-600">Ingredientes con stock bajo</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ $ingredients->where('current_stock', '<=', 0)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Sin stock</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">
                        ${{ number_format($ingredients->sum(function($ingredient) {
                            return ($ingredient->minimum_stock - $ingredient->current_stock) * $ingredient->cost_per_unit;
                        }), 2) }}
                    </div>
                    <div class="text-sm text-gray-600">Valor estimado para reabastecer</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
