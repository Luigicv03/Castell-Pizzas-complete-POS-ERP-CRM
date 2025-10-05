@extends('layouts.app')

@section('title', 'Gestión de Productos')
@section('subtitle', 'Administra el catálogo de productos y menú')

@section('content')
<div class="space-y-6" x-data="productsManagement()">
    <!-- Header con acciones -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Productos</h1>
            <p class="text-gray-600">Administra el catálogo de productos del menú</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <button @click="openCategoryModal('create')" class="btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Nueva Categoría
            </button>
            <button @click="openModal('create')" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Producto
            </button>
        </div>
    </div>

    <!-- Estadísticas de productos -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Productos</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="allProducts.length"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-success">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Disponibles</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="allProducts.filter(p => p.is_active).length"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-warning">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Destacados</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="allProducts.filter(p => p.is_featured).length"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-info">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Categorías</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="allCategories.length"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de Categorías -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Gestión de Categorías</h3>
            <p class="text-sm text-gray-600">Administra las categorías de productos</p>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="category in allCategories" :key="category.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900" x-text="category.name"></h4>
                            <div class="flex items-center space-x-2">
                                <button @click="toggleCategoryStatus(category)" 
                                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs"
                                        :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                        :title="category.is_active ? 'Desactivar' : 'Activar'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="category.is_active" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        <path x-show="!category.is_active" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div class="relative">
                                    <button @click="openCategoryDropdown(category.id)" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                        </svg>
                                    </button>
                                    <div x-show="activeCategoryDropdown === category.id" 
                                         @click.away="activeCategoryDropdown = null"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                        <div class="py-1">
                                            <button @click="openCategoryModal('edit', category)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button @click="deleteCategory(category)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-2" x-text="category.description || 'Sin descripción'"></p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span x-text="category.products_count + ' productos'"></span>
                            <span x-show="category.sort_order" x-text="'Orden: ' + category.sort_order"></span>
                        </div>
                        <div class="mt-2">
                            <span class="badge" :class="category.is_active ? 'badge-success' : 'badge-secondary'" 
                                  x-text="category.is_active ? 'Activa' : 'Inactiva'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Buscar producto</label>
                    <input type="text" x-model="filters.search" class="form-input" placeholder="Nombre del producto...">
                </div>
                <div>
                    <label class="form-label">Categoría</label>
                    <select x-model="filters.category" class="form-select">
                        <option value="">Todas las categorías</option>
                        <template x-for="category in allCategories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select x-model="filters.status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Destacados</label>
                    <select x-model="filters.featured" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Solo destacados</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de productos -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Catálogo de Productos (<span x-text="filteredProducts.length"></span>)</h3>
        </div>
        <div class="card-body">
            <template x-if="filteredProducts.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div class="product-card">
                            <template x-if="product.image">
                                <div class="product-card-image">
                                    <img :src="'/storage/' + product.image" :alt="product.name" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <template x-if="!product.image">
                                <div class="product-card-image">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </template>
                            
                            <h3 class="product-card-title" x-text="product.name"></h3>
                            <p class="product-card-description" x-text="product.description"></p>
                            
                            <div class="mt-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Precio:</span>
                                    <span class="font-bold text-primary-600" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Costo:</span>
                                    <span class="font-semibold text-gray-700" x-text="'$' + parseFloat(product.cost || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between border-t pt-1 mt-1">
                                    <span>Ganancia:</span>
                                    <span class="font-bold text-success-600" x-text="'$' + (parseFloat(product.price) - parseFloat(product.cost || 0)).toFixed(2)"></span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex space-x-1">
                                    <template x-if="product.is_featured">
                                        <span class="badge badge-warning">⭐</span>
                                    </template>
                                    <span class="badge" :class="product.is_active ? 'badge-success' : 'badge-danger'" x-text="product.is_active ? 'Disponible' : 'No disponible'"></span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-3 gap-2">
                                <button @click="openModal('edit', product)" class="btn-sm btn-secondary flex-1">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button @click="deleteProduct(product.id)" class="btn-sm btn-danger flex-1">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <template x-if="filteredProducts.length === 0">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay productos</h3>
                    <p class="text-gray-500 mb-4">No se encontraron productos con los filtros seleccionados</p>
                    <button @click="openModal('create')" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar Primer Producto
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal para Crear/Editar Producto -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
         style="z-index: 9999; padding: 20px; overflow-y: auto;">
        <div class="bg-white rounded-lg shadow-xl w-full mx-auto" 
             @click.away="showModal = false"
             style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900" x-text="modalMode === 'create' ? 'Crear Nuevo Producto' : 'Editar Producto'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form @submit.prevent="saveProduct()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="form-label">Nombre del Producto <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.name" class="form-input" required>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="form-label">Descripción</label>
                            <textarea x-model="formData.description" class="form-textarea" rows="3"></textarea>
                        </div>
                        
                        <div>
                            <label class="form-label">Categoría <span class="text-red-500">*</span></label>
                            <select x-model="formData.category_id" class="form-select" required>
                                <option value="">Seleccionar categoría</option>
                                <template x-for="category in allCategories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label">SKU</label>
                            <input type="text" x-model="formData.sku" class="form-input">
                        </div>
                        
                        <div>
                            <label class="form-label">Precio de Venta <span class="text-red-500">*</span></label>
                            <input type="number" x-model="formData.price" step="0.01" min="0" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Costo del Producto <span class="text-red-500">*</span></label>
                            <input type="number" x-model="formData.cost" step="0.01" min="0" class="form-input" required>
                        </div>
                        
                        <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-blue-800">Ganancia por unidad:</span>
                                <span class="text-lg font-bold text-blue-900" x-text="'$' + calculateProfit().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-blue-600">Margen de ganancia:</span>
                                <span class="text-sm font-semibold text-blue-700" x-text="calculateProfitMargin() + '%'"></span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label">Tiempo de Preparación (min)</label>
                            <input type="number" x-model="formData.preparation_time" min="0" class="form-input">
                        </div>
                        
                        <div>
                            <label class="form-label">Orden de Visualización</label>
                            <input type="number" x-model="formData.sort_order" min="0" class="form-input">
                        </div>
                        
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="formData.is_active" class="form-checkbox">
                                <span class="ml-2 text-sm font-medium text-gray-700">Disponible</span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="formData.is_featured" class="form-checkbox">
                                <span class="ml-2 text-sm font-medium text-gray-700">Destacado</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" @click="showModal = false" class="btn btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            <span x-text="modalMode === 'create' ? 'Crear Producto' : 'Guardar Cambios'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Categoría -->
    <div x-show="showCategoryModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
         style="z-index: 9999; padding: 20px; overflow-y: auto;">
        <div class="bg-white rounded-lg shadow-xl w-full mx-auto" 
             @click.away="showCategoryModal = false"
             style="max-width: 500px; max-height: 90vh; overflow-y: auto;">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900" x-text="categoryModalMode === 'create' ? 'Crear Nueva Categoría' : 'Editar Categoría'"></h3>
                    <button @click="showCategoryModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form @submit.prevent="saveCategory()">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Nombre de la Categoría <span class="text-red-500">*</span></label>
                            <input type="text" x-model="categoryFormData.name" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Descripción</label>
                            <textarea x-model="categoryFormData.description" class="form-textarea" rows="3"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Color</label>
                                <input type="color" x-model="categoryFormData.color" class="form-input h-10">
                            </div>
                            
                            <div>
                                <label class="form-label">Orden de Visualización</label>
                                <input type="number" x-model="categoryFormData.sort_order" min="0" class="form-input">
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="categoryFormData.is_active" class="form-checkbox">
                                <span class="ml-2 text-sm font-medium text-gray-700">Categoría activa</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" @click="showCategoryModal = false" class="btn btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            <span x-text="categoryModalMode === 'create' ? 'Crear Categoría' : 'Guardar Cambios'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function productsManagement() {
    return {
        allProducts: @json($products),
        allCategories: @json($categories),
        showModal: false,
        showCategoryModal: false,
        modalMode: 'create', // 'create' or 'edit'
        categoryModalMode: 'create', // 'create' or 'edit'
        activeCategoryDropdown: null,
        formData: {
            id: null,
            name: '',
            description: '',
            price: 0,
            cost: 0,
            category_id: '',
            sku: '',
            preparation_time: 0,
            sort_order: 0,
            is_active: true,
            is_featured: false
        },
        categoryFormData: {
            id: null,
            name: '',
            description: '',
            color: '#3B82F6',
            sort_order: 0,
            is_active: true
        },
        filters: {
            search: '',
            category: '',
            status: '',
            featured: ''
        },
        
        get filteredProducts() {
            return this.allProducts.filter(product => {
                // Búsqueda por nombre
                if (this.filters.search && !product.name.toLowerCase().includes(this.filters.search.toLowerCase())) {
                    return false;
                }
                
                // Filtro por categoría
                if (this.filters.category && product.category_id != this.filters.category) {
                    return false;
                }
                
                // Filtro por estado
                if (this.filters.status !== '' && product.is_active != this.filters.status) {
                    return false;
                }
                
                // Filtro por destacado
                if (this.filters.featured && !product.is_featured) {
                    return false;
                }
                
                return true;
            });
        },
        
        openModal(mode, product = null) {
            this.modalMode = mode;
            
            if (mode === 'edit' && product) {
                this.formData = {
                    id: product.id,
                    name: product.name,
                    description: product.description || '',
                    price: product.price,
                    cost: product.cost || 0,
                    category_id: product.category_id,
                    sku: product.sku || '',
                    preparation_time: product.preparation_time || 0,
                    sort_order: product.sort_order || 0,
                    is_active: product.is_active,
                    is_featured: product.is_featured
                };
            } else {
                this.formData = {
                    id: null,
                    name: '',
                    description: '',
                    price: 0,
                    cost: 0,
                    category_id: '',
                    sku: '',
                    preparation_time: 0,
                    sort_order: 0,
                    is_active: true,
                    is_featured: false
                };
            }
            
            this.showModal = true;
        },
        
        calculateProfit() {
            const price = parseFloat(this.formData.price) || 0;
            const cost = parseFloat(this.formData.cost) || 0;
            return price - cost;
        },
        
        calculateProfitMargin() {
            const price = parseFloat(this.formData.price) || 0;
            const cost = parseFloat(this.formData.cost) || 0;
            if (price === 0) return 0;
            return ((price - cost) / price * 100).toFixed(2);
        },
        
        async saveProduct() {
            try {
                const url = this.modalMode === 'create' ? '/products' : `/products/${this.formData.id}`;
                const method = this.modalMode === 'create' ? 'POST' : 'PUT';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.showModal = false;
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar el producto'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar el producto');
            }
        },
        
        async deleteProduct(productId) {
            if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                return;
            }
            
            try {
                const response = await fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo eliminar el producto'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el producto');
            }
        },

        // Funciones para categorías
        openCategoryModal(mode, category = null) {
            this.categoryModalMode = mode;
            this.activeCategoryDropdown = null;
            
            if (mode === 'edit' && category) {
                this.categoryFormData = {
                    id: category.id,
                    name: category.name,
                    description: category.description || '',
                    color: category.color || '#3B82F6',
                    sort_order: category.sort_order || 0,
                    is_active: category.is_active
                };
            } else {
                this.categoryFormData = {
                    id: null,
                    name: '',
                    description: '',
                    color: '#3B82F6',
                    sort_order: 0,
                    is_active: true
                };
            }
            
            this.showCategoryModal = true;
        },

        async saveCategory() {
            try {
                const url = this.categoryModalMode === 'create' ? '/api/categories' : `/api/categories/${this.categoryFormData.id}`;
                const method = this.categoryModalMode === 'create' ? 'POST' : 'PUT';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.categoryFormData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.showCategoryModal = false;
                    await this.loadCategories();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar la categoría'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar la categoría');
            }
        },

        async deleteCategory(category) {
            if (category.products_count > 0) {
                alert('No se puede eliminar la categoría porque tiene productos asociados.');
                return;
            }

            if (!confirm(`¿Estás seguro de que deseas eliminar la categoría "${category.name}"?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/api/categories/${category.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await this.loadCategories();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo eliminar la categoría'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar la categoría');
            }
        },

        async toggleCategoryStatus(category) {
            try {
                const response = await fetch(`/api/categories/${category.id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    category.is_active = data.is_active;
                } else {
                    alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cambiar el estado de la categoría');
            }
        },

        openCategoryDropdown(categoryId) {
            this.activeCategoryDropdown = this.activeCategoryDropdown === categoryId ? null : categoryId;
        },

        async loadCategories() {
            try {
                const response = await fetch('/api/categories');
                const categories = await response.json();
                this.allCategories = categories;
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
    }
}
</script>
@endsection
