@extends('layouts.app')

@section('title', 'Sistema POS')
@section('subtitle', 'Punto de Venta - Gestión de pedidos')

@section('content')
<div class="space-y-6" x-data="posSystem()" x-init="initOrderStatusBar()">
    <!-- POS Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Sistema POS</h1>
                    <p class="text-gray-600">Usuario: {{ auth()->user()->name }} • {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="startNewOrder('dine_in')" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Pedido
                </button>
                <button class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Historial
                </button>
            </div>
        </div>
    </div>

    <!-- Barra de Estado de Pedidos en Tiempo Real -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pedidos Activos</h3>
                    <p class="text-sm text-gray-500">Estado en tiempo real de todos los pedidos</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1 text-xs text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>Actualizado</span>
                </div>
                <button @click="refreshActiveOrders()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Filtros de Tipo de Pedido -->
        <div class="flex space-x-2 mb-4">
            <button @click="selectedOrderType = null" 
                    :class="selectedOrderType === null ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Todos
            </button>
            <button @click="selectedOrderType = 'dine_in'" 
                    :class="selectedOrderType === 'dine_in' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Comer Aquí
            </button>
            <button @click="selectedOrderType = 'takeaway'" 
                    :class="selectedOrderType === 'takeaway' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Para Llevar
            </button>
            <button @click="selectedOrderType = 'delivery'" 
                    :class="selectedOrderType === 'delivery' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Delivery
            </button>
            <button @click="selectedOrderType = 'pickup'" 
                    :class="selectedOrderType === 'pickup' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Pickup
            </button>
        </div>
        
        <!-- Lista de Pedidos Activos -->
        <div class="overflow-x-auto">
            <div class="flex space-x-4 pb-2" style="min-width: max-content;">
                <template x-for="order in filteredActiveOrders" :key="order.id">
                    <div @click="openActiveOrder(order)" 
                         class="flex-shrink-0 w-64 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                         :class="getActiveOrderCardClass(order.status)">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900" x-text="'#' + order.daily_number"></span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                          :class="getActiveOrderStatusBadgeClass(order.status)"
                                          x-text="getActiveOrderStatusText(order.status)"></span>
                                </div>
                                <div class="text-xs text-gray-500" x-text="formatActiveOrderTime(order.created_at)"></div>
                            </div>
                            
                            <div class="space-y-1 mb-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600" x-text="order.customer ? order.customer.name : 'Cliente General'"></span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600" x-text="getActiveOrderTypeText(order.type)"></span>
                                    <span x-show="order.table" class="text-sm text-gray-600" x-text="order.table ? ' - ' + order.table.name : ''"></span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900" x-text="'$' + parseFloat(order.total_amount).toFixed(2)"></span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500" x-text="order.items_count + ' productos'"></div>
                                <div class="flex space-x-1">
                                    <button @click.stop="updateActiveOrderStatus(order.id, 'preparing')" 
                                            x-show="order.status === 'pending'"
                                            class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200 transition-colors">
                                        Preparar
                                    </button>
                                    <button @click.stop="updateActiveOrderStatus(order.id, 'ready')" 
                                            x-show="order.status === 'preparing'"
                                            class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200 transition-colors">
                                        Listo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Mensaje cuando no hay pedidos -->
                <div x-show="filteredActiveOrders.length === 0" class="flex-shrink-0 w-64 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center p-8">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No hay pedidos activos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main POS Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Tables Section -->
        <div class="lg:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">Estado de Mesas</h2>
                    <p class="text-sm text-gray-500">Selecciona una mesa para el pedido</p>
                    
                    <!-- Legend -->
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 rounded-full bg-green-200 border border-green-500"></div>
                            <span class="text-gray-600">Libre</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 rounded-full bg-red-200 border border-red-500"></div>
                            <span class="text-gray-600">Ocupada</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 rounded-full bg-yellow-200 border border-yellow-500"></div>
                            <span class="text-gray-600">Reservada</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 rounded-full bg-orange-200 border border-orange-500"></div>
                            <span class="text-gray-600">Pendiente</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 p-4">
                        @foreach($tables as $table)
                        <div class="table-card {{ $table->status === 'free' ? 'free' : ($table->status === 'occupied' ? 'occupied' : ($table->status === 'reserved' ? 'reserved' : 'pending_payment')) }}"
                             @click="selectTable({{ $table->id }})"
                             :class="{ 'ring-2 ring-primary-500 ring-offset-2': selectedTable === {{ $table->id }} }">
                            <div class="text-center">
                                <div class="text-lg font-bold">{{ $table->name }}</div>
                                <div class="text-xs mt-1">{{ $table->capacity }} personas</div>
                                <div class="text-xs mt-1 font-medium">{{ $table->getStatusText() }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Productos</h2>
                            <p class="text-sm text-gray-500">Selecciona productos para agregar al pedido</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Categoría:</span>
                            <select x-model="selectedCategory" class="form-select w-40">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Category Filter Buttons -->
                    <div class="flex space-x-2 mb-4 overflow-x-auto pb-2">
                        <button @click="selectedCategory = null" 
                                :class="selectedCategory === null ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-colors duration-200">
                            Todas
                        </button>
                        @foreach($categories as $category)
                        <button @click="selectedCategory = {{ $category->id }}" 
                                :class="selectedCategory === {{ $category->id }} ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-colors duration-200">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto scrollbar-thin">
                        @foreach($products as $product)
                        <div class="product-card"
                             @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                             x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}">
                            <div class="product-image">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <p class="product-description">{{ Str::limit($product->description, 50) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="product-price">${{ number_format($product->price, 2) }}</span>
                                @if($product->is_featured)
                                <span class="badge badge-warning">Destacado</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">Pedido Actual</h2>
                    <p class="text-sm text-gray-500">Mesa: <span x-text="selectedTableName || 'Sin mesa'"></span></p>
                </div>
                <div class="card-body">
                    <!-- Empty Cart State -->
                    <div x-show="cart.length === 0" class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Carrito vacío</h4>
                        <p class="text-gray-500">Agrega productos para comenzar el pedido</p>
                    </div>

                    <!-- Cart Items -->
                    <div x-show="cart.length > 0" class="space-y-3 mb-4">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <!-- Item Principal -->
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 text-sm" x-text="item.name"></h4>
                                    <button @click="removeFromCart(index)" class="text-danger-500 hover:text-danger-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <button @click="updateQuantity(index, item.quantity - 1)" 
                                                :disabled="item.quantity <= 1"
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 hover:bg-gray-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium text-sm" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(index, item.quantity + 1)" 
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Botones de Ingredientes y Caja (solo para pizzas/calzones) -->
                                        <template x-if="isPizzaOrCalzone(item) && !item.name.toLowerCase().includes('caja')">
                                            <div class="flex items-center space-x-1 ml-2">
                                                <button @click="openIngredientsModalPOS(index, item.name)" 
                                                        class="w-6 h-6 rounded-full flex items-center justify-center text-sm"
                                                        style="background: #ffc107; border: 1px solid #ffc107; color: white;"
                                                        title="Agregar ingredientes">
                                                    🍕
                                                </button>
                                                <button @click="addBoxToCartItem(index, item.name)" 
                                                        class="w-6 h-6 rounded-full flex items-center justify-center text-sm"
                                                        style="background: #795548; border: 1px solid #795548; color: white;"
                                                        title="Agregar caja">
                                                    📦
                                                </button>
                                            </div>
                                        </template>
                                        
                                        <!-- Botón de Envase (solo para tés) -->
                                        <template x-if="isTea(item) && !item.name.toLowerCase().includes('envase')">
                                            <div class="flex items-center space-x-1 ml-2">
                                                <button @click="addContainerToCartItem(index, item.name, 'Envase para Té')" 
                                                        class="w-6 h-6 rounded-full flex items-center justify-center text-sm"
                                                        style="background: #4CAF50; border: 1px solid #4CAF50; color: white;"
                                                        title="Agregar envase para té">
                                                    🥤
                                                </button>
                                            </div>
                                        </template>
                                        
                                        <!-- Botón de Envase (solo para café) -->
                                        <template x-if="isCoffee(item) && !item.name.toLowerCase().includes('envase')">
                                            <div class="flex items-center space-x-1 ml-2">
                                                <button @click="addContainerToCartItem(index, item.name, 'Envase para Café')" 
                                                        class="w-6 h-6 rounded-full flex items-center justify-center text-sm"
                                                        style="background: #6F4E37; border: 1px solid #6F4E37; color: white;"
                                                        title="Agregar envase para café">
                                                    ☕
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <span class="font-bold text-primary-600 text-sm" x-text="'$' + getItemTotal(item).toFixed(2)"></span>
                                </div>
                                
                                <!-- Ingredientes/Extras (children) -->
                                <template x-if="item.children && item.children.length > 0">
                                    <div class="mt-2 pl-4 border-l-2 border-yellow-400 space-y-1">
                                        <template x-for="(child, childIndex) in item.children" :key="childIndex">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600" x-text="`+ ${child.name} (${child.quantity}x)`"></span>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-green-600 font-medium" x-text="'$' + (child.price * child.quantity).toFixed(2)"></span>
                                                    <button @click="removeChild(index, childIndex)" class="text-red-500 hover:text-red-700">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Order Summary -->
                <div x-show="cart.length > 0" class="border-t border-gray-200 p-4">
                    <!-- Indicador del Tipo de Pedido Actual -->
                    <div class="mb-4 p-3 rounded-lg border-2" 
                         :class="getOrderTypeIndicatorClass(orderType)">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full" 
                                     :class="getOrderTypeDotClass(orderType)"></div>
                                <span class="font-medium" x-text="getOrderTypeText(orderType)"></span>
                            </div>
                            <span class="text-xs opacity-75">Tipo de Pedido</span>
                        </div>
                    </div>

                    <!-- Botones de Tipo de Pedido -->
                    <div class="mb-4">
                        <label class="form-label">Cambiar Tipo de Pedido</label>
                        <div class="flex space-x-2">
                            <button @click="startOrder('dine_in')" 
                                    :class="orderType === 'dine_in' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-medium transition-colors">
                                <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Comer Aquí
                            </button>
                            <button @click="startOrder('takeaway')" 
                                    :class="orderType === 'takeaway' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-medium transition-colors">
                                <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Para Llevar
                            </button>
                            <button @click="startOrder('delivery')" 
                                    :class="orderType === 'delivery' ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-medium transition-colors">
                                <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Delivery
                            </button>
                            <button @click="startOrder('pickup')" 
                                    :class="orderType === 'pickup' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-medium transition-colors">
                                <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Pickup
                            </button>
                        </div>
                    </div>

                    <!-- Información del Pedido -->
                    <div class="mb-4 space-y-3">

                        <div x-show="orderType === 'dine_in'">
                            <label class="form-label">Mesa</label>
                            <select x-model="selectedTable" class="form-select">
                                <option value="">Seleccionar mesa</option>
                                @foreach($tables as $table)
                                <option value="{{ $table->id }}" {{ $table->status !== 'free' ? 'disabled' : '' }}>
                                    {{ $table->name }} ({{ $table->capacity }} personas) - {{ $table->getStatusText() }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Cliente</label>
                            <div class="flex space-x-2">
                                <select x-model="selectedCustomer" class="form-select flex-1">
                                    <option value="">Cliente general</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                    @endforeach
                                </select>
                                <button @click="showCustomerModal = true" class="btn-secondary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div x-show="!selectedCustomer">
                            <label class="form-label">Nombre del Cliente</label>
                            <input type="text" x-model="customerName" class="form-input" placeholder="Nombre del cliente">
                        </div>

                        <!-- Campo de Delivery -->
                        <div x-show="orderType === 'delivery'">
                            <label class="form-label">Distancia de Delivery (km)</label>
                            <div class="flex space-x-2">
                                <input type="number" x-model="deliveryDistance" @input="calculateDeliveryCost()" 
                                       step="0.1" min="0" max="10" class="form-input flex-1" 
                                       placeholder="Ej: 2.5">
                                <button type="button" @click="showDeliveryCosts = !showDeliveryCosts" 
                                        class="btn-secondary px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Mostrar costo de delivery -->
                            <div x-show="deliveryCost > 0" class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-green-800">Costo de Delivery:</span>
                                    <span class="text-sm font-bold text-green-900">$<span x-text="deliveryCost.toFixed(2)"></span></span>
                                </div>
                                <div x-show="deliveryCostDescription" class="text-xs text-green-600 mt-1" x-text="deliveryCostDescription"></div>
                            </div>

                            <!-- Tabla de costos de delivery -->
                            <div x-show="showDeliveryCosts" class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Tarifas de Delivery</h4>
                                <div class="space-y-1 text-xs">
                                    <template x-for="cost in deliveryCosts" :key="cost.id">
                                        <div class="flex justify-between">
                                            <span x-text="cost.min_distance + ' - ' + cost.max_distance + ' km'"></span>
                                            <span class="font-medium">$<span x-text="cost.cost"></span></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Notas del Pedido (Opcional)</label>
                            <textarea x-model="orderNotes" class="form-textarea" rows="2" placeholder="Notas especiales para el pedido"></textarea>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div x-show="orderType === 'delivery' && deliveryCost > 0" class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery:</span>
                            <span class="font-medium" x-text="'$' + deliveryCost.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span class="text-primary-600" x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>
                    
                    <button @click="processOrder()" 
                            :disabled="cart.length === 0"
                            class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Procesar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
function posSystem() {
    return {
        selectedTable: null,
        selectedTableName: null,
        selectedCategory: null,
        cart: [],
        orderType: 'dine_in',
        selectedCustomer: null,
        customerName: '',
        orderNotes: '',
        showCustomerModal: false,
        
        // Variables para delivery
        deliveryDistance: 0,
        deliveryCost: 0,
        deliveryCostDescription: '',
        showDeliveryCosts: false,
        deliveryCosts: [],
        
        // Propiedades para la barra de pedidos activos
        activeOrders: [],
        selectedOrderType: null,
        orderRefreshInterval: null,
        
        // Inicializar event listeners
        init() {
            const self = this;
            
            // Listener para agregar ingredientes
            window.addEventListener('add-ingredient-to-cart', (e) => {
                const { cartIndex, ingredientId, ingredientName, ingredientPrice } = e.detail;
                self.addIngredientToItem(cartIndex, ingredientId, ingredientName, ingredientPrice);
            });
            
            // Listener para agregar cajas
            window.addEventListener('add-box-to-cart', (e) => {
                const { cartIndex, boxId, boxName, boxPrice } = e.detail;
                self.addBoxToItem(cartIndex, boxId, boxName, boxPrice);
            });
            
            console.log('Alpine POS System initialized with event listeners');
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => {
                const itemTotal = this.getItemTotal(item);
                return sum + itemTotal;
            }, 0);
        },

        get tax() {
            return 0; // Los precios ya incluyen IVA
        },

        get total() {
            return this.subtotal + this.deliveryCost;
        },

        selectTable(tableId) {
            this.selectedTable = tableId;
            const table = @json($tables->toArray()).find(t => t.id === tableId);
            this.selectedTableName = table ? table.name : null;
        },

        addToCart(productId, name, price) {
            // Detectar si es pizza o calzone
            const isPizza = name.toLowerCase().includes('pizza') && !name.toLowerCase().includes('caja');
            const isCalzone = name.toLowerCase().includes('calzone');
            
            // Si es pizza o calzone, SIEMPRE crear un nuevo item (no acumular)
            if (isPizza || isCalzone) {
                this.cart.push({
                    productId: productId,
                    name: name,
                    price: price,
                    quantity: 1,
                    children: [] // Array para ingredientes/extras
                });
            } else {
                // Para otros productos, acumular como antes
                const existingItem = this.cart.find(item => item.productId === productId);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    this.cart.push({
                        productId: productId,
                        name: name,
                        price: price,
                        quantity: 1,
                        children: []
                    });
                }
            }
        },
        
        // Calcular el total de un item incluyendo sus children
        getItemTotal(item) {
            let total = item.price * item.quantity;
            if (item.children && item.children.length > 0) {
                item.children.forEach(child => {
                    total += child.price * child.quantity;
                });
            }
            return total;
        },
        
        // Verificar si un item es pizza o calzone
        isPizzaOrCalzone(item) {
            const name = item.name.toLowerCase();
            return (name.includes('pizza') || name.includes('calzone')) && !name.includes('caja');
        },
        
        // Verificar si un item es té
        isTea(item) {
            const name = item.name.toLowerCase();
            return (name.includes('té') || name.includes('te ') || name.includes('matcha') || name.includes('jamaica')) && !name.includes('envase');
        },
        
        // Verificar si un item es café
        isCoffee(item) {
            const name = item.name.toLowerCase();
            // Lista de nombres de cafés para detectar
            const coffeeNames = ['árabe', 'americano', 'cappuccino', 'espresso', 'latte', 'macchiato', 
                               'doppio', 'irlandés', 'caravel', 'viena', 'breve', 'lungo', 'affogato',
                               'bombón', 'caribeño', 'amaretto', 'ristretto', 'hawaiano', 'cubano', 
                               'panna', 'vainilla', 'pistacho', 'café'];
            return coffeeNames.some(coffee => name.includes(coffee)) && !name.includes('envase');
        },
        
        // Remover un child (ingrediente/caja)
        removeChild(parentIndex, childIndex) {
            this.cart[parentIndex].children.splice(childIndex, 1);
        },
        
        // Agregar ingrediente a un item del carrito (llamado desde función global)
        addIngredientToItem(cartIndex, ingredientId, ingredientName, ingredientPrice) {
            console.log('Alpine: Adding ingredient to index', cartIndex);
            
            if (cartIndex < 0 || cartIndex >= this.cart.length) {
                console.error('Invalid cart index:', cartIndex, 'Cart length:', this.cart.length);
                alert('Error: Item no encontrado en el carrito');
                return;
            }
            
            const item = this.cart[cartIndex];
            
            if (!item.children) {
                item.children = [];
            }
            
            const existing = item.children.find(c => c.productId === ingredientId);
            if (existing) {
                existing.quantity += 1;
            } else {
                item.children.push({
                    productId: ingredientId,
                    name: ingredientName,
                    price: ingredientPrice,
                    quantity: 1
                });
            }
            
            // Forzar actualización
            this.cart = [...this.cart];
            
            alert(`✓ ${ingredientName} agregado correctamente`);
        },
        
        // Agregar caja a un item del carrito (llamado desde función global)
        async addBoxToItem(cartIndex, boxId, boxName, boxPrice) {
            console.log('Alpine: Adding box to index', cartIndex);
            
            if (cartIndex < 0 || cartIndex >= this.cart.length) {
                alert('Error: Índice de carrito inválido');
                return;
            }
            
            const item = this.cart[cartIndex];
            
            if (!item.children) {
                item.children = [];
            }
            
            const hasBox = item.children.some(c => c.name.toLowerCase().includes('caja'));
            if (hasBox) {
                alert('Esta pizza ya tiene una caja agregada');
                return;
            }
            
            item.children.push({
                productId: boxId,
                name: boxName,
                price: boxPrice,
                quantity: 1
            });
            
            // Forzar actualización
            this.cart = [...this.cart];
            
            alert(`✓ ${boxName} agregada correctamente`);
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
            } else {
                this.cart[index].quantity = newQuantity;
            }
        },

        startOrder(type) {
            this.orderType = type;
            
            // Solo limpiar mesa si no es dine_in, pero NO borrar el carrito
            if (this.orderType !== 'dine_in') {
                this.selectedTable = null;
                this.selectedTableName = null;
            }
            
            // Si es delivery, inicializar el cálculo
            if (this.orderType === 'delivery' && this.deliveryDistance > 0) {
                this.calculateDeliveryCost();
            }
            
            console.log('Cambiando tipo de pedido a:', type);
        },

        startNewOrder(type) {
            this.orderType = type;
            this.cart = [];
            this.customerName = '';
            this.orderNotes = '';
            this.selectedCustomer = null;
            this.deliveryDistance = 0;
            this.deliveryCost = 0;
            this.deliveryCostDescription = '';
            
            // Limpiar mesa si no es dine_in
            if (this.orderType !== 'dine_in') {
                this.selectedTable = null;
                this.selectedTableName = null;
            }
            
            console.log('Iniciando nuevo pedido tipo:', type);
        },

        // Métodos para delivery
        async loadDeliveryCosts() {
            try {
                const response = await fetch('/api/delivery/costs');
                this.deliveryCosts = await response.json();
            } catch (error) {
                console.error('Error cargando costos de delivery:', error);
            }
        },

        async calculateDeliveryCost() {
            console.log('Calculando costo de delivery para distancia:', this.deliveryDistance);
            
            if (this.deliveryDistance <= 0) {
                this.deliveryCost = 0;
                this.deliveryCostDescription = '';
                console.log('Distancia <= 0, costo = 0');
                return;
            }

            try {
                const response = await fetch(`/api/delivery/calculate?distance=${this.deliveryDistance}`);
                console.log('Respuesta del servidor:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Datos recibidos:', data);
                
                if (data.success) {
                    this.deliveryCost = parseFloat(data.cost);
                    this.deliveryCostDescription = data.description;
                    console.log('Costo calculado:', this.deliveryCost);
                } else {
                    this.deliveryCost = 0;
                    this.deliveryCostDescription = '';
                    console.warn('No se encontró costo para la distancia:', this.deliveryDistance);
                }
            } catch (error) {
                console.error('Error calculando costo de delivery:', error);
                this.deliveryCost = 0;
                this.deliveryCostDescription = '';
            }
        },

        changeOrderType() {
            // Mostrar modal para cambiar tipo de pedido
            const newType = prompt('Selecciona el tipo de pedido:\n1. Comer Aquí\n2. Para Llevar\n3. Delivery\n4. Pickup\n\nIngresa el número (1-4):');
            
            const typeMap = {
                '1': 'dine_in',
                '2': 'takeaway', 
                '3': 'delivery',
                '4': 'pickup'
            };
            
            if (typeMap[newType]) {
                this.startOrder(typeMap[newType]);
            }
        },

        getOrderTypeText(type) {
            const typeMap = {
                'dine_in': 'Comer Aquí',
                'takeaway': 'Para Llevar',
                'delivery': 'Delivery',
                'pickup': 'Pickup'
            };
            return typeMap[type] || type;
        },

        getOrderTypeBadgeClass(type) {
            const classMap = {
                'dine_in': 'bg-blue-100 text-blue-800',
                'takeaway': 'bg-yellow-100 text-yellow-800',
                'delivery': 'bg-cyan-100 text-cyan-800',
                'pickup': 'bg-green-100 text-green-800'
            };
            return classMap[type] || 'bg-gray-100 text-gray-800';
        },

        getOrderTypeIndicatorClass(type) {
            const classMap = {
                'dine_in': 'bg-blue-50 border-blue-200',
                'takeaway': 'bg-yellow-50 border-yellow-200',
                'delivery': 'bg-cyan-50 border-cyan-200',
                'pickup': 'bg-green-50 border-green-200'
            };
            return classMap[type] || 'bg-gray-50 border-gray-200';
        },

        getOrderTypeDotClass(type) {
            const classMap = {
                'dine_in': 'bg-blue-500',
                'takeaway': 'bg-yellow-500',
                'delivery': 'bg-cyan-500',
                'pickup': 'bg-green-500'
            };
            return classMap[type] || 'bg-gray-500';
        },

        processOrder() {
            if (this.cart.length === 0) return;

            const orderData = {
                order_type: this.orderType,
                table_id: this.orderType === 'dine_in' ? this.selectedTable : null,
                customer_id: this.selectedCustomer,
                customer_name: this.customerName,
                items: this.cart.map(item => ({
                    product_id: item.productId,
                    quantity: item.quantity,
                    unit_price: item.price,
                    children: item.children && item.children.length > 0 ? item.children : []
                })),
                subtotal: this.subtotal,
                tax_amount: 0, // Los precios ya incluyen IVA
                delivery_cost: this.orderType === 'delivery' ? this.deliveryCost : 0,
                total_amount: this.total,
                notes: this.orderNotes,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            console.log('Enviando pedido con children:', orderData);
            console.log('Items en el carrito:', this.cart);

            fetch('/pos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': orderData._token
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor:', data);
                if (data.success) {
                    alert('Pedido creado exitosamente: Comanda #' + data.daily_number);
                    this.cart = [];
                    this.selectedTable = null;
                    this.selectedTableName = null;
                    this.customerName = '';
                    this.orderNotes = '';
                    // Recargar pedidos activos en lugar de recargar toda la página
                    console.log('Recargando pedidos activos...');
                    this.loadActiveOrders();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el pedido: ' + error.message);
            });
        },

        // Métodos para la barra de pedidos activos
        initOrderStatusBar() {
            console.log('Inicializando barra de pedidos activos...');
            this.loadActiveOrders();
            this.startOrderRefresh();
            this.loadDeliveryCosts();
            this.initDeliveryCost();
            this.initSelectedTable();
        },

        // Inicializar delivery cost si ya hay distancia
        initDeliveryCost() {
            if (this.orderType === 'delivery' && this.deliveryDistance > 0) {
                console.log('Inicializando costo de delivery para distancia:', this.deliveryDistance);
                this.calculateDeliveryCost();
            }
        },

        // Inicializar mesa preseleccionada
        initSelectedTable() {
            @if(isset($selectedTable) && $selectedTable)
            console.log('Mesa preseleccionada:', {{ $selectedTable->id }}, '{{ $selectedTable->name }}');
            this.selectedTable = {{ $selectedTable->id }};
            this.selectedTableName = '{{ $selectedTable->name }}';
            this.orderType = 'dine_in';
            // Automáticamente iniciar nuevo pedido para la mesa preseleccionada
            this.startNewOrder('dine_in');
            @endif
        },

        get filteredActiveOrders() {
            console.log('🔍 Filtrando pedidos activos. Total:', this.activeOrders.length, 'Tipo seleccionado:', this.selectedOrderType);
            if (this.selectedOrderType === null) {
                console.log('📋 Mostrando todos los pedidos activos:', this.activeOrders);
                return this.activeOrders;
            }
            const filtered = this.activeOrders.filter(order => order.type === this.selectedOrderType);
            console.log('🎯 Pedidos activos filtrados por tipo', this.selectedOrderType, ':', filtered);
            return filtered;
        },

        loadActiveOrders() {
            console.log('🔄 Cargando pedidos activos...');
            fetch('/api/orders/active')
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Pedidos activos cargados:', data);
                    console.log('📊 Total pedidos activos:', data.length);
                    
                    // Log de tipos de pedidos
                    const types = data.map(order => order.type);
                    console.log('🏷️ Tipos de pedidos activos encontrados:', [...new Set(types)]);
                    
                    this.activeOrders = data;
                    console.log('💾 activeOrders actualizado:', this.activeOrders);
                })
                .catch(error => {
                    console.error('❌ Error cargando pedidos activos:', error);
                    this.activeOrders = [];
                });
        },

        refreshActiveOrders() {
            this.loadActiveOrders();
        },

        startOrderRefresh() {
            this.orderRefreshInterval = setInterval(() => {
                this.loadActiveOrders();
            }, 10000); // Actualizar cada 10 segundos
        },

        stopOrderRefresh() {
            if (this.orderRefreshInterval) {
                clearInterval(this.orderRefreshInterval);
            }
        },

        openActiveOrder(order) {
            // Ir a la vista detallada de la orden
            window.location.href = `/pos/${order.id}/detail`;
        },

        updateActiveOrderStatus(orderId, newStatus) {
            fetch(`/pos/${orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.loadActiveOrders(); // Recargar pedidos
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado');
            });
        },

        getActiveOrderStatusText(status) {
            const statusMap = {
                'pending': 'Pendiente',
                'preparing': 'Preparando',
                'ready': 'Listo',
                'delivered': 'Entregado',
                'cancelled': 'Cancelado'
            };
            return statusMap[status] || status;
        },

        getActiveOrderTypeText(type) {
            const typeMap = {
                'dine_in': 'Comer Aquí',
                'takeaway': 'Para Llevar',
                'delivery': 'Delivery',
                'pickup': 'Pickup'
            };
            return typeMap[type] || type;
        },

        getActiveOrderStatusBadgeClass(status) {
            const classMap = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'preparing': 'bg-blue-100 text-blue-800',
                'ready': 'bg-green-100 text-green-800',
                'delivered': 'bg-gray-100 text-gray-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classMap[status] || 'bg-gray-100 text-gray-800';
        },

        getActiveOrderCardClass(status) {
            const classMap = {
                'pending': 'border-l-4 border-l-yellow-400',
                'preparing': 'border-l-4 border-l-blue-400',
                'ready': 'border-l-4 border-l-green-400',
                'delivered': 'border-l-4 border-l-gray-400',
                'cancelled': 'border-l-4 border-l-red-400'
            };
            return classMap[status] || 'border-l-4 border-l-gray-400';
        },

        formatActiveOrderTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

    }
}

// Variables globales para el modal de ingredientes en POS
let currentCartIndexPOS = null;
let currentPizzaNamePOS = '';
let availableIngredientsPOS = [];

// Abrir modal de ingredientes en POS
async function openIngredientsModalPOS(cartIndex, pizzaName) {
    currentCartIndexPOS = cartIndex;
    currentPizzaNamePOS = pizzaName;
    
    document.getElementById('pizzaNameDisplayPOS').textContent = `Ingredientes para: ${pizzaName}`;
    
    // Determinar el tamaño basado en el nombre del producto
    let size = 'Personal';
    
    if (pizzaName.toLowerCase().includes('calzone')) {
        size = 'Calzone';
    } else if (pizzaName.includes('Personal') || pizzaName.includes('25cm')) {
        size = 'Personal';
    } else if (pizzaName.includes('Mediana') || pizzaName.includes('33cm')) {
        size = 'Mediana';
    } else if (pizzaName.includes('Familiar') || pizzaName.includes('40cm')) {
        size = 'Familiar';
    }
    
    // Cargar ingredientes
    try {
        console.log('Fetching ingredients for size:', size);
        const response = await fetch(`/api/ingredients/by-size/${size}`);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        availableIngredientsPOS = await response.json();
        console.log('Ingredients loaded:', availableIngredientsPOS.length);
        console.log('Sample ingredient:', availableIngredientsPOS[0]);
        
        displayIngredientsPOS();
        document.getElementById('ingredientsModalPOS').style.display = 'flex';
    } catch (error) {
        console.error('Error loading ingredients:', error);
        alert('Error al cargar los ingredientes: ' + error.message);
    }
}

// Cerrar modal de ingredientes
function closeIngredientsModalPOS() {
    document.getElementById('ingredientsModalPOS').style.display = 'none';
    currentCartIndexPOS = null;
    currentPizzaNamePOS = '';
    availableIngredientsPOS = [];
}

// Mostrar ingredientes en el modal
function displayIngredientsPOS() {
    const container = document.getElementById('ingredientsListPOS');
    container.innerHTML = '';
    
    if (availableIngredientsPOS.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6c757d;">No hay ingredientes disponibles para este tamaño</p>';
        return;
    }
    
    // Filtrar solo ingredientes simples (sin "Doble" en el nombre)
    const simpleIngredients = availableIngredientsPOS.filter(ing => !ing.name.includes('Doble'));
    
    // Agrupar por categoría
    const grouped = {};
    simpleIngredients.forEach(ing => {
        const catName = ing.category ? ing.category.name : 'Sin categoría';
        if (!grouped[catName]) {
            grouped[catName] = [];
        }
        grouped[catName].push(ing);
    });
    
    // Mostrar por categoría
    Object.keys(grouped).forEach(categoryName => {
        const categoryDiv = document.createElement('div');
        categoryDiv.style.marginBottom = '15px';
        
        const categoryTitle = document.createElement('h4');
        categoryTitle.textContent = categoryName;
        categoryTitle.style.fontSize = '14px';
        categoryTitle.style.fontWeight = 'bold';
        categoryTitle.style.color = '#495057';
        categoryTitle.style.marginBottom = '8px';
        categoryDiv.appendChild(categoryTitle);
        
        grouped[categoryName].forEach(ingredient => {
            // Buscar la versión doble del ingrediente
            const doubleVersion = availableIngredientsPOS.find(ing => 
                ing.name === ingredient.name.replace(/\s+(Personal|Mediana|Familiar|Calzone)/, ' $1 Doble')
            );
            
            const ingredientRow = document.createElement('div');
            ingredientRow.style.display = 'flex';
            ingredientRow.style.justifyContent = 'space-between';
            ingredientRow.style.alignItems = 'center';
            ingredientRow.style.padding = '8px';
            ingredientRow.style.border = '1px solid #dee2e6';
            ingredientRow.style.borderRadius = '6px';
            ingredientRow.style.marginBottom = '8px';
            ingredientRow.style.backgroundColor = '#fff';
            
            // Columna izquierda: nombre y precios
            const infoDiv = document.createElement('div');
            infoDiv.style.flex = '1';
            
            const nameDiv = document.createElement('div');
            nameDiv.textContent = ingredient.name;
            nameDiv.style.fontWeight = 'bold';
            nameDiv.style.fontSize = '13px';
            nameDiv.style.marginBottom = '3px';
            
            const priceDiv = document.createElement('div');
            priceDiv.style.fontSize = '11px';
            priceDiv.style.color = '#6c757d';
            priceDiv.textContent = `Simple: $${parseFloat(ingredient.price).toFixed(2)}${doubleVersion ? ` | Doble: $${parseFloat(doubleVersion.price).toFixed(2)}` : ''}`;
            
            infoDiv.appendChild(nameDiv);
            infoDiv.appendChild(priceDiv);
            
            // Columna derecha: botones
            const buttonsDiv = document.createElement('div');
            buttonsDiv.style.display = 'flex';
            buttonsDiv.style.gap = '8px';
            
            // Botón Simple (+)
            const simpleBtn = document.createElement('button');
            simpleBtn.className = 'btn btn-success btn-sm';
            simpleBtn.textContent = '+';
            simpleBtn.style.minWidth = '45px';
            simpleBtn.style.fontWeight = 'bold';
            simpleBtn.title = 'Agregar porción simple';
            simpleBtn.onclick = () => addIngredientToCartItem(ingredient.id, ingredient.name, parseFloat(ingredient.price));
            buttonsDiv.appendChild(simpleBtn);
            
            // Botón Doble (++) - solo si existe
            if (doubleVersion) {
                const doubleBtn = document.createElement('button');
                doubleBtn.className = 'btn btn-primary btn-sm';
                doubleBtn.textContent = '++';
                doubleBtn.style.minWidth = '45px';
                doubleBtn.style.fontWeight = 'bold';
                doubleBtn.title = 'Agregar porción doble';
                doubleBtn.onclick = () => addIngredientToCartItem(doubleVersion.id, doubleVersion.name, parseFloat(doubleVersion.price));
                buttonsDiv.appendChild(doubleBtn);
            }
            
            ingredientRow.appendChild(infoDiv);
            ingredientRow.appendChild(buttonsDiv);
            categoryDiv.appendChild(ingredientRow);
        });
        
        container.appendChild(categoryDiv);
    });
}

// Agregar ingrediente al item del carrito
function addIngredientToCartItem(ingredientId, ingredientName, ingredientPrice) {
    console.log('Global: Adding ingredient', ingredientName, 'to cart index', currentCartIndexPOS);
    
    // Disparar evento personalizado para que Alpine lo maneje
    window.dispatchEvent(new CustomEvent('add-ingredient-to-cart', {
        detail: {
            cartIndex: currentCartIndexPOS,
            ingredientId: ingredientId,
            ingredientName: ingredientName,
            ingredientPrice: ingredientPrice
        }
    }));
}

// Agregar caja al item del carrito
async function addBoxToCartItem(cartIndex, pizzaName) {
    console.log('Global: Adding box to cart index', cartIndex, 'Pizza:', pizzaName);
    
    // Determinar el tamaño de la caja
    let boxName = 'Caja Personal';
    if (pizzaName.toLowerCase().includes('personal') || pizzaName.includes('25cm')) {
        boxName = 'Caja Personal';
    } else if (pizzaName.toLowerCase().includes('mediana') || pizzaName.includes('33cm')) {
        boxName = 'Caja Mediana';
    } else if (pizzaName.toLowerCase().includes('familiar') || pizzaName.includes('40cm')) {
        boxName = 'Caja Familiar';
    }
    
    try {
        // Buscar el producto de la caja
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: boxName })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const box = await response.json();
        
        if (!box || !box.id) {
            alert('No se encontró la caja: ' + boxName);
            return;
        }
        
        // Disparar evento para que Alpine lo maneje
        window.dispatchEvent(new CustomEvent('add-box-to-cart', {
            detail: {
                cartIndex: cartIndex,
                boxId: box.id,
                boxName: box.name,
                boxPrice: parseFloat(box.price)
            }
        }));
        
    } catch (error) {
        console.error('Error fetching box:', error);
        alert('Error al agregar la caja: ' + error.message);
    }
}

// Agregar envase al item del carrito (genérico para tés y cafés)
async function addContainerToCartItem(cartIndex, itemName, containerName = 'Envase para Té') {
    console.log('Global: Adding container to cart index', cartIndex, 'Item:', itemName, 'Container:', containerName);
    
    try {
        // Buscar el producto del envase
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: containerName })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const container = await response.json();
        
        if (!container || !container.id) {
            alert('No se encontró el envase: ' + containerName);
            return;
        }
        
        // Disparar evento para que Alpine lo maneje
        window.dispatchEvent(new CustomEvent('add-box-to-cart', {
            detail: {
                cartIndex: cartIndex,
                boxId: container.id,
                boxName: container.name,
                boxPrice: parseFloat(container.price)
            }
        }));
        
    } catch (error) {
        console.error('Error fetching container:', error);
        alert('Error al agregar el envase: ' + error.message);
    }
}

</script>

<!-- Modal de Ingredientes para POS -->
<div id="ingredientsModalPOS" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; margin: auto;">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #212529; margin: 0;">🍕 Agregar Ingredientes</h3>
                <button onclick="closeIngredientsModalPOS()" 
                        style="background: transparent; border: none; color: #dc3545; font-size: 28px; line-height: 1; cursor: pointer; padding: 0; width: 30px; height: 30px;">
                    ×
                </button>
            </div>
            
            <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #495057; font-weight: 500;" id="pizzaNameDisplayPOS"></p>
            </div>
            
            <div id="ingredientsListPOS" style="display: flex; flex-direction: column; gap: 10px;">
                <!-- Ingredients will be loaded here -->
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <button onclick="closeIngredientsModalPOS()" class="btn btn-secondary">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection