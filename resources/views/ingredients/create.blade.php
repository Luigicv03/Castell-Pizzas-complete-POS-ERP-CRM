@extends('layouts.app')

@section('title', 'Nuevo Ingrediente')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Ingrediente</h1>
            <p class="text-gray-600">Agregar un nuevo ingrediente al inventario</p>
        </div>
        <a href="{{ route('ingredients.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ingredients.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="form-label">Nombre del Ingrediente *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="form-input @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="form-label">SKU *</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" 
                               class="form-input @error('sku') border-red-500 @enderror" required>
                        @error('sku')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label for="supplier_id" class="form-label">Proveedor *</label>
                        <select id="supplier_id" name="supplier_id" 
                                class="form-select @error('supplier_id') border-red-500 @enderror" required>
                            <option value="">Seleccionar proveedor</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label for="unit" class="form-label">Unidad *</label>
                        <select id="unit" name="unit" 
                                class="form-select @error('unit') border-red-500 @enderror" required>
                            <option value="">Seleccionar unidad</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                            <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Gramo (g)</option>
                            <option value="lt" {{ old('unit') == 'lt' ? 'selected' : '' }}>Litro (lt)</option>
                            <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Mililitro (ml)</option>
                            <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Piezas (pcs)</option>
                            <option value="lb" {{ old('unit') == 'lb' ? 'selected' : '' }}>Libra (lb)</option>
                        </select>
                        @error('unit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Costo por Unidad -->
                    <div>
                        <label for="cost_per_unit" class="form-label">Costo por Unidad *</label>
                        <input type="number" id="cost_per_unit" name="cost_per_unit" 
                               value="{{ old('cost_per_unit') }}" step="0.01" min="0"
                               class="form-input @error('cost_per_unit') border-red-500 @enderror" required>
                        @error('cost_per_unit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Mínimo -->
                    <div>
                        <label for="minimum_stock" class="form-label">Stock Mínimo *</label>
                        <input type="number" id="minimum_stock" name="minimum_stock" 
                               value="{{ old('minimum_stock') }}" step="0.001" min="0"
                               class="form-input @error('minimum_stock') border-red-500 @enderror" required>
                        @error('minimum_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Actual -->
                    <div>
                        <label for="current_stock" class="form-label">Stock Actual *</label>
                        <input type="number" id="current_stock" name="current_stock" 
                               value="{{ old('current_stock') }}" step="0.001" min="0"
                               class="form-input @error('current_stock') border-red-500 @enderror" required>
                        @error('current_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" 
                               class="form-input @error('location') border-red-500 @enderror"
                               placeholder="Ej: Almacén A - Estante 1">
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="form-label">Descripción</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-textarea @error('description') border-red-500 @enderror"
                              placeholder="Descripción del ingrediente...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('ingredients.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Crear Ingrediente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
