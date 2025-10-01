@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT))

@section('content')
<style>
.pos-grid {
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
    gap: 10px;
    height: 100vh;
    padding: 10px;
    overflow: hidden;
}

@media (min-width: 768px) {
    .pos-grid {
        grid-template-columns: 320px 1fr;
        height: calc(100vh - 20px);
    }
}

.pos-header {
    grid-column: 1 / -1;
    background: white;
    border-radius: 6px;
    padding: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.pos-info {
    background: white;
    border-radius: 6px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 120px);
}

.pos-menu {
    background: white;
    border-radius: 6px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.pos-cart {
    background: white;
    border-radius: 6px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
    overflow-y: auto;
    flex: 1;
    padding: 8px 0;
}

.product-card {
    background: white;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
    height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #dee2e6;
    background: #f8f9fa;
}

.product-card:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.cart-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
    border: 1px solid #dee2e6;
}

.btn-compact {
    padding: 6px 12px;
    font-size: 13px;
    white-space: nowrap;
}

.btn-mini {
    width: 24px;
    height: 24px;
    padding: 0;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-weight: 600;
    line-height: 1;
}

@media (max-width: 767px) {
    .pos-grid {
        grid-template-rows: auto auto auto;
    }
    
    .pos-info {
        max-height: 50vh;
    }
}
</style>

<div class="pos-grid" x-data="orderDetailSystem()">
    <!-- Header -->
    <div class="pos-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <div>
                <h5 class="mb-0 fw-bold">Pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</h5>
                <small class="text-muted">
                    @if($order->table)
                        Mesa {{ $order->table->name }} • {{ $order->customer ? $order->customer->name : 'Cliente General' }}
                    @else
                        {{ $order->getTypeText() }} • {{ $order->customer ? $order->customer->name : 'Cliente General' }}
                    @endif
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ $order->getStatusText() }}</span>
            <button @click="printKitchenOrder()" class="btn btn-warning btn-compact">
                <i class="fas fa-print"></i> Cocina
            </button>
            <button @click="printBarOrder()" class="btn btn-success btn-compact">
                <i class="fas fa-print"></i> Caja
            </button>
        </div>
    </div>

    <!-- Pedido Actual -->
    <div class="pos-info">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Pedido Actual</h6>
            <small class="text-muted">
                @if($order->table)
                    Mesa: {{ $order->table->name }}
                @else
                    {{ $order->getTypeText() }}
                @endif
            </small>
        </div>
        
        <!-- Lista de Productos del Pedido -->
        <div class="mb-3" style="max-height: 350px; overflow-y: auto;">
            @if($order->items->count() > 0)
                @foreach($order->items as $item)
                <div style="background: white; border: 1px solid #e9ecef; border-radius: 8px; padding: 12px; margin-bottom: 10px;">
                    <!-- Header: Nombre del producto y botón eliminar -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold" style="font-size: 14px;">{{ $item->product->name }}</h6>
                        <button @click="removeItem({{ $item->id }})" 
                                class="btn btn-mini"
                                style="background: transparent; border: none; color: #dc3545; font-size: 20px; padding: 0; width: auto; height: auto;">
                            ×
                        </button>
                    </div>
                    
                    <!-- Controles: Cantidad, Botón Pizza (si aplica), Precio - TODO EN UNA LÍNEA -->
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <!-- Controles de cantidad -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="btn btn-mini"
                                    style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057;">
                                −
                            </button>
                            <span class="fw-bold" style="min-width: 30px; text-align: center; font-size: 15px;">{{ $item->quantity }}</span>
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="btn btn-mini"
                                    style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057;">
                                +
                            </button>
                            
                            <!-- Botón de Ingredientes (para pizzas y calzones, excepto Caja de Pizza) -->
                            @php
                                $categoryName = strtolower($item->product->category->name ?? '');
                                $productName = strtolower($item->product->name ?? '');
                                $showIngredientsBtn = (str_contains($categoryName, 'pizza') || str_contains($categoryName, 'calzone') || str_contains($productName, 'calzone')) 
                                                      && !str_contains($productName, 'caja');
                            @endphp
                            @if($showIngredientsBtn)
                            <button onclick="openIngredientsModal({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn btn-mini"
                                    style="background: #ffc107; border: 1px solid #ffc107; color: white; border-radius: 50%; width: 28px; height: 28px; margin-left: 5px;"
                                    title="Agregar ingredientes extras">
                                🍕
                            </button>
                            <button onclick="addBoxToPizza({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn btn-mini"
                                    style="background: #795548; border: 1px solid #795548; color: white; border-radius: 50%; width: 28px; height: 28px; margin-left: 5px;"
                                    title="Agregar caja">
                                📦
                            </button>
                            @endif
                        </div>
                        
                        <!-- Precio a la derecha -->
                        <span class="fw-bold" style="color: #0d6efd; font-size: 16px;">${{ number_format($item->total_price, 2) }}</span>
                    </div>
                    
                    @if($item->children && $item->children->count() > 0)
                    <!-- Ingredientes extras -->
                    <div class="mt-2 ps-3 border-start border-2 border-warning">
                        @foreach($item->children as $child)
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <small class="text-muted">+ {{ $child->product->name }}</small>
                            <div class="d-flex align-items-center gap-1">
                                <small class="fw-bold text-success">${{ number_format($child->total_price, 2) }}</small>
                                <button @click="removeItem({{ $child->id }})" 
                                        class="btn btn-mini"
                                        style="background: transparent; border: none; color: #dc3545; font-size: 14px; padding: 0; width: auto; height: auto;">
                                    ×
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <p>Sin productos</p>
                </div>
            @endif
        </div>

        <!-- Tipo de Pedido -->
        <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                <span class="fw-bold">{{ $order->getTypeText() }}</span>
                <span class="ms-auto text-muted small">Tipo de Pedido</span>
            </div>
        </div>

        <!-- Cliente -->
        <div class="mb-3">
            <label class="form-label small fw-bold">Cliente</label>
            <div class="bg-light p-2 rounded">
                {{ $order->customer ? $order->customer->name : 'Cliente general' }}
            </div>
        </div>

        <!-- Resumen -->
        <div class="border-top pt-3 mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal:</span>
                <span class="fw-bold">${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->type === 'delivery' && $order->delivery_cost > 0)
            <div class="d-flex justify-content-between mb-1">
                <span>Delivery:</span>
                <span class="fw-bold text-info">${{ number_format($order->delivery_cost, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between border-top pt-2">
                <span class="h6 fw-bold">Total:</span>
                <span class="h5 fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex flex-column gap-2 mt-auto">
            <button @click="updateOrderStatus('preparing')" 
                    :disabled="orderStatus === 'preparing'"
                    :class="orderStatus === 'preparing' ? 'btn-outline-primary' : 'btn-primary'"
                    class="btn w-100 py-2">
                <i class="fas fa-clock me-2"></i>
                <span x-text="orderStatus === 'preparing' ? '✓ En Preparación' : 'En Preparación'"></span>
            </button>
            <button @click="updateOrderStatus('ready')" 
                    :disabled="orderStatus === 'ready'"
                    :class="orderStatus === 'ready' ? 'btn-outline-success' : 'btn-success'"
                    class="btn w-100 py-2">
                <i class="fas fa-check me-2"></i>
                <span x-text="orderStatus === 'ready' ? '✓ Listo' : 'Listo'"></span>
            </button>
            <button @click="openPaymentModal()" 
                    class="btn btn-warning w-100 py-2">
                <i class="fas fa-credit-card me-2"></i> Procesar Pago
            </button>
            <button @click="updateOrderStatus('cancelled')" 
                    :disabled="orderStatus === 'cancelled'"
                    class="btn btn-danger w-100 py-2"
                    onclick="return confirm('¿Estás seguro de cancelar este pedido?')">
                <i class="fas fa-times me-2"></i> Cancelar Pedido
            </button>
        </div>
    </div>

    <!-- Productos -->
    <div class="pos-menu">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Productos</h6>
            <small class="text-muted">Selecciona productos para agregar al pedido</small>
        </div>
        
        <!-- Filtro de Categoría -->
        <div class="mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label small fw-bold mb-0">Categoría:</label>
                <select class="form-select form-select-sm" style="width: auto;" @change="selectedCategory = $event.target.value === 'all' ? null : parseInt($event.target.value)">
                    <option value="all">Todas</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Categorías como botones -->
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-1">
                <button @click="selectedCategory = null" 
                        :class="selectedCategory === null ? 'btn btn-primary btn-compact' : 'btn btn-outline-secondary btn-compact'">
                    Todas
                </button>
                @foreach($categories as $category)
                <button @click="selectedCategory = {{ $category->id }}" 
                        :class="selectedCategory === {{ $category->id }} ? 'btn btn-primary btn-compact' : 'btn btn-outline-secondary btn-compact'">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Grid de Productos Limitado -->
        <div style="height: 400px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 8px; padding: 10px;">
            <div class="products-grid" style="height: auto;">
                @foreach($products as $product)
                <button @click="addProduct({{ $product->id }})" 
                        x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}" 
                        class="product-card"
                        style="background: white; border: 1px solid #e9ecef; cursor: pointer; transition: all 0.2s; width: 100%;">
                    <div class="text-center">
                        <h6 class="fw-bold mb-2" style="font-size: 13px; color: #212529;">{{ Str::limit($product->name, 20) }}</h6>
                        <p class="text-muted mb-2" style="font-size: 10px;">{{ Str::limit($product->description, 30) }}</p>
                        <div>
                            <span class="fw-bold" style="color: #0d6efd; font-size: 16px;">${{ number_format($product->price, 2) }}</span>
                            @if($product->is_featured)
                            <span class="ms-1" style="font-size: 10px;">⭐</span>
                            @endif
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Payment Modal (dentro del scope de Alpine) -->
    <div x-show="showPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
         style="z-index: 9999; padding: 20px; overflow-y: auto;">
        <div class="bg-white rounded-lg shadow-xl w-full mx-auto" 
             @click.away="showPaymentModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <div class="p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Procesar Pago</h3>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Total del Pedido:</span>
                        <span class="text-lg font-bold text-primary">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="font-medium">Pagado:</span>
                        <span class="text-lg font-bold text-success">$<span x-text="totalPaid.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="font-bold">Restante:</span>
                        <span class="text-xl font-bold" :class="remainingAmount > 0 ? 'text-danger' : 'text-success'">
                            $<span x-text="remainingAmount.toFixed(2)"></span>
                        </span>
                    </div>
                </div>

                <!-- Payment Methods List -->
                <div x-show="payments.length > 0" class="mb-3">
                    <h4 class="font-medium text-gray-900 mb-2 text-sm">Métodos de Pago Agregados:</h4>
                    <div class="space-y-2">
                        <template x-for="(payment, index) in payments" :key="index">
                            <div class="flex justify-between items-center bg-gray-100 p-2 rounded text-sm">
                                <div>
                                    <span class="font-medium" x-text="getPaymentMethodText(payment.method)"></span>
                                    <span x-show="payment.reference" class="text-xs text-gray-600 ml-2">
                                        (Ref: <span x-text="payment.reference"></span>)
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold">$<span x-text="payment.amount.toFixed(2)"></span></span>
                                    <button @click="removePayment(index)" class="text-danger hover:text-danger-700 text-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Add New Payment Form -->
                <div x-show="remainingAmount > 0" class="border-t pt-3">
                    <h4 class="font-medium text-gray-900 mb-2 text-sm">Agregar Método de Pago</h4>
                    <form @submit.prevent="addPayment()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Método de Pago</label>
                                <select x-model="currentPayment.method" class="form-select" required>
                                    <option value="">Seleccionar método</option>
                                    <option value="cash">Efectivo</option>
                                    <option value="mobile_payment">Pago Móvil</option>
                                    <option value="zelle">Zelle</option>
                                    <option value="binance">Binance</option>
                                    <option value="pos">Punto de Venta</option>
                                    <option value="transfer">Transferencia</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="form-label">Monto</label>
                                <input type="number" x-model="currentPayment.amount" step="0.01" min="0.01" 
                                       :max="remainingAmount" class="form-input" required>
                            </div>
                            
                            <div x-show="currentPayment.method === 'transfer' || currentPayment.method === 'mobile_payment' || currentPayment.method === 'zelle'" 
                                 class="md:col-span-2">
                                <label class="form-label">Referencia</label>
                                <input type="text" x-model="currentPayment.reference" class="form-input" 
                                       placeholder="Número de referencia">
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Agregar Pago
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Customer Data -->
                <div class="border-t pt-3">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Datos del Cliente</h4>
                            
                            <!-- Customer Search/Add Toggle -->
                            <div class="mb-3">
                                <div class="flex gap-2">
                                    <button type="button" @click="customerMode = 'search'" 
                                            :class="customerMode === 'search' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1.5 rounded text-xs font-medium transition-colors flex-1">
                                        Buscar Existente
                                    </button>
                                    <button type="button" @click="customerMode = 'new'" 
                                            :class="customerMode === 'new' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1.5 rounded text-xs font-medium transition-colors flex-1">
                                        Cliente Nuevo
                                    </button>
                                </div>
                            </div>

                            <!-- Customer Search -->
                            <div x-show="customerMode === 'search'" class="space-y-3">
                                <div>
                                    <label class="form-label">Buscar por Cédula</label>
                                    <div class="relative">
                                        <input type="text" x-model="customerSearch" 
                                               @input="searchCustomerByCedula()"
                                               class="form-input" 
                                               placeholder="Ingrese número de cédula">
                                        <div x-show="customerSearchLoading" class="absolute right-3 top-3">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Search Results -->
                                <div x-show="customerSearchResults.length > 0" class="space-y-2">
                                    <div class="text-sm font-medium text-gray-700">Clientes encontrados:</div>
                                    <div class="space-y-2">
                                        <template x-for="customer in customerSearchResults" :key="customer.id">
                                            <div class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                                                 @click="selectExistingCustomer(customer)">
                                                <div class="font-medium" x-text="customer.name"></div>
                                                <div class="text-sm text-gray-600" x-text="'Cédula: ' + customer.cedula"></div>
                                                <div class="text-sm text-gray-600" x-text="'Teléfono: ' + (customer.phone || 'N/A')"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- No Results -->
                                <div x-show="customerSearch && customerSearchResults.length === 0 && !customerSearchLoading" 
                                     class="text-sm text-gray-500 p-3 bg-gray-50 rounded-lg">
                                    No se encontraron clientes con esa cédula.
                                </div>
                            </div>

                            <!-- Customer Form -->
                            <div x-show="customerMode === 'new' || (customerMode === 'search' && selectedCustomer)" class="space-y-2">
                                <div>
                                    <label class="form-label text-xs">Nombre Completo</label>
                                    <input type="text" x-model="customerData.name" class="form-input form-input-sm" 
                                           placeholder="Nombre del cliente" required>
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Email</label>
                                    <input type="email" x-model="customerData.email" class="form-input form-input-sm" 
                                           placeholder="email@ejemplo.com">
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Teléfono</label>
                                    <input type="tel" x-model="customerData.phone" class="form-input form-input-sm" 
                                           placeholder="Número de teléfono">
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Cédula</label>
                                    <input type="text" x-model="customerData.cedula" 
                                           @input="checkCedulaExists()"
                                           class="form-input form-input-sm" 
                                           placeholder="Número de cédula">
                                    <div x-show="cedulaExists" class="text-xs text-orange-600 mt-1">
                                        ⚠️ Esta cédula ya está registrada.
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Dirección</label>
                                    <textarea x-model="customerData.address" class="form-textarea form-input-sm" rows="2"
                                              placeholder="Dirección completa"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                        <button type="button" @click="showPaymentModal = false" class="btn btn-secondary btn-sm">
                            Cancelar
                        </button>
                        <button type="button" @click="processPayment()" 
                                :disabled="remainingAmount > 0 || payments.length === 0"
                                class="btn btn-primary btn-sm">
                            <i class="fas fa-credit-card me-1"></i> Procesar Pago
                        </button>
                    </div>
            </div>
        </div>
    </div>

</div><!-- Fin del componente Alpine orderDetailSystem -->

<!-- Modal de Ingredientes -->
<div id="ingredientsModal" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; margin: auto;">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #212529; margin: 0;">🍕 Agregar Ingredientes</h3>
                <button onclick="closeIngredientsModal()" 
                        style="background: transparent; border: none; color: #dc3545; font-size: 28px; line-height: 1; cursor: pointer; padding: 0; width: 30px; height: 30px;">
                    ×
                </button>
            </div>
            
            <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #495057; font-weight: 500;" id="pizzaNameDisplay"></p>
            </div>
            
            <div id="ingredientsList" style="display: flex; flex-direction: column; gap: 10px;">
                <!-- Ingredients will be loaded here -->
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <button onclick="closeIngredientsModal()" class="btn btn-secondary">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js CDN como respaldo -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Variables globales para modal de ingredientes
let currentPizzaItemId = null;
let currentPizzaName = '';
let availableIngredients = [];

// Abrir modal de ingredientes
async function openIngredientsModal(itemId, pizzaName) {
    currentPizzaItemId = itemId;
    currentPizzaName = pizzaName;
    
    document.getElementById('pizzaNameDisplay').textContent = `Ingredientes para: ${pizzaName}`;
    
    // Determinar el tamaño basado en el nombre del producto
    let size = 'Personal';
    
    // Si es Calzone, usar "Calzone" como tamaño
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
        const response = await fetch(`/api/ingredients/by-size/${size}`);
        availableIngredients = await response.json();
        displayIngredients();
        const modal = document.getElementById('ingredientsModal');
        modal.style.display = 'flex'; // Cambiar a flex para que funcione el centrado
    } catch (error) {
        console.error('Error loading ingredients:', error);
        alert('Error al cargar los ingredientes');
    }
}

// Cerrar modal de ingredientes
function closeIngredientsModal() {
    document.getElementById('ingredientsModal').style.display = 'none';
    currentPizzaItemId = null;
    currentPizzaName = '';
    availableIngredients = [];
}

// Agregar caja a la pizza
async function addBoxToPizza(itemId, pizzaName) {
    // Determinar el tamaño de la caja basado en el nombre de la pizza
    let boxName = 'Caja Personal';
    
    if (pizzaName.toLowerCase().includes('personal') || pizzaName.includes('25cm')) {
        boxName = 'Caja Personal';
    } else if (pizzaName.toLowerCase().includes('mediana') || pizzaName.includes('33cm')) {
        boxName = 'Caja Mediana';
    } else if (pizzaName.toLowerCase().includes('familiar') || pizzaName.includes('40cm')) {
        boxName = 'Caja Familiar';
    }
    
    // Buscar el producto de la caja
    try {
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: boxName })
        });
        
        const box = await response.json();
        
        if (!box || !box.id) {
            alert('No se encontró la caja correspondiente');
            return;
        }
        
        // Agregar la caja como ingrediente
        const addResponse = await fetch(`/pos/{{ $order->id }}/item/${itemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: box.id,
                quantity: 1
            })
        });
        
        const result = await addResponse.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Error al agregar la caja');
        }
    } catch (error) {
        console.error('Error adding box:', error);
        alert('Error al agregar la caja');
    }
}

// Mostrar ingredientes en el modal
function displayIngredients() {
    const container = document.getElementById('ingredientsList');
    container.innerHTML = '';
    
    if (availableIngredients.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">No hay ingredientes disponibles para este tamaño</p>';
        return;
    }
    
    // Agrupar por categoría
    const grouped = {};
    availableIngredients.forEach(ingredient => {
        const categoryName = ingredient.category?.name || 'Otros';
        if (!grouped[categoryName]) {
            grouped[categoryName] = [];
        }
        grouped[categoryName].push(ingredient);
    });
    
    // Renderizar por categoría
    Object.keys(grouped).forEach(categoryName => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'mb-3';
        categoryDiv.innerHTML = `<h6 class="fw-bold text-muted mb-2" style="font-size: 12px;">${categoryName}</h6>`;
        
        grouped[categoryName].forEach(ingredient => {
            const ingredientDiv = document.createElement('div');
            ingredientDiv.className = 'd-flex justify-content-between align-items-center p-2 border rounded mb-2';
            ingredientDiv.style.cursor = 'pointer';
            ingredientDiv.style.transition = 'all 0.2s';
            ingredientDiv.innerHTML = `
                <div>
                    <div class="fw-bold" style="font-size: 13px;">${ingredient.name}</div>
                    <div class="text-muted" style="font-size: 11px;">${ingredient.description || ''}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-success">$${parseFloat(ingredient.price).toFixed(2)}</span>
                    <button onclick="addIngredientToPizza(${ingredient.id})" 
                            class="btn btn-sm btn-primary">
                        +
                    </button>
                </div>
            `;
            categoryDiv.appendChild(ingredientDiv);
        });
        
        container.appendChild(categoryDiv);
    });
}

// Agregar ingrediente a la pizza
async function addIngredientToPizza(ingredientId) {
    try {
        const response = await fetch(`/pos/{{ $order->id }}/item/${currentPizzaItemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: ingredientId,
                quantity: 1
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeIngredientsModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error adding ingredient:', error);
        alert('Error al agregar el ingrediente');
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const paymentModal = document.getElementById('paymentModal');
    const ingredientsModal = document.getElementById('ingredientsModal');
    
    if (event.target === paymentModal) {
        paymentModal.style.display = 'none';
    }
    
    if (event.target === ingredientsModal) {
        ingredientsModal.style.display = 'none';
    }
}
</script>

<script>
function orderDetailSystem() {
    return {
        selectedCategory: null,
        orderStatus: '{{ $order->status }}',
        showPaymentModal: false,
        payments: [],
        currentPayment: {
            method: '',
            amount: 0,
            reference: ''
        },
        customerMode: 'search', // 'search' or 'new'
        customerSearch: '',
        customerSearchResults: [],
        customerSearchLoading: false,
        selectedCustomer: null,
        cedulaExists: false,
        customerData: {
            name: '',
            email: '',
            phone: '',
            cedula: '',
            address: ''
        },
        
        get totalPaid() {
            return this.payments.reduce((total, payment) => total + parseFloat(payment.amount), 0);
        },
        
        get remainingAmount() {
            return {{ $order->total_amount }} - this.totalPaid;
        },
        
        init() {
            this.currentPayment.amount = {{ $order->total_amount }};
        },
        
        openPaymentModal() {
            console.log('Opening payment modal...');
            this.showPaymentModal = true;
            this.payments = [];
            this.currentPayment = {
                method: '',
                amount: {{ $order->total_amount }},
                reference: ''
            };
            console.log('Payment modal should be visible:', this.showPaymentModal);
        },
        
        addProduct(productId) {
            fetch(`/pos/{{ $order->id }}/add-product`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al agregar el producto');
            });
        },
        
        updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;
            
            fetch(`/pos/{{ $order->id }}/item/${itemId}/quantity`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la cantidad');
            });
        },
        
        removeItem(itemId) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                fetch(`/pos/{{ $order->id }}/item/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                });
            }
        },
        
        updateOrderStatus(status) {
            fetch(`/pos/{{ $order->id }}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.orderStatus = status;
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado');
            });
        },
        
        printKitchenOrder() {
            window.open(`/pos/{{ $order->id }}/print/kitchen`, '_blank');
        },
        
        printBarOrder() {
            window.open(`/pos/{{ $order->id }}/print/bar`, '_blank');
        },
        
        addPayment() {
            if (!this.currentPayment.method || !this.currentPayment.amount) {
                alert('Por favor complete todos los campos');
                return;
            }
            
            if (this.currentPayment.amount > this.remainingAmount) {
                alert('El monto no puede ser mayor al restante');
                return;
            }
            
            // Agregar el pago al array
            this.payments.push({
                method: this.currentPayment.method,
                amount: parseFloat(this.currentPayment.amount),
                reference: this.currentPayment.reference
            });
            
            // Resetear el formulario con el nuevo restante
            this.$nextTick(() => {
                this.currentPayment = {
                    method: '',
                    amount: this.remainingAmount > 0 ? this.remainingAmount : 0,
                    reference: ''
                };
            });
        },
        
        removePayment(index) {
            this.payments.splice(index, 1);
        },
        
        getPaymentMethodText(method) {
            const methods = {
                'cash': 'Efectivo',
                'mobile_payment': 'Pago Móvil',
                'zelle': 'Zelle',
                'binance': 'Binance',
                'pos': 'Punto de Venta',
                'transfer': 'Transferencia'
            };
            return methods[method] || method;
        },
        
        processPayment() {
            if (this.payments.length === 0) {
                alert('Debe agregar al menos un método de pago');
                return;
            }
            
            if (this.remainingAmount > 0) {
                alert('El monto total no ha sido completado. Restante: $' + this.remainingAmount.toFixed(2));
                return;
            }
            
            if (!this.customerData.name || !this.customerData.cedula) {
                alert('Debe completar los datos del cliente');
                return;
            }
            
            fetch(`/pos/{{ $order->id }}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payments: this.payments,
                    customer_data: this.customerData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showPaymentModal = false;
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el pago');
            });
        },
        
        finalizeOrder() {
            if (confirm('¿Estás seguro de que quieres finalizar esta orden?')) {
                fetch(`/pos/{{ $order->id }}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: 'delivered' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Orden finalizada correctamente');
                        window.location.href = '/pos';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al finalizar la orden');
                });
            }
        },
        
        // Customer search methods
        async searchCustomerByCedula() {
            if (this.customerSearch.length < 3) {
                this.customerSearchResults = [];
                return;
            }
            
            this.customerSearchLoading = true;
            
            try {
                const response = await fetch(`/api/customers/search?q=${encodeURIComponent(this.customerSearch)}`);
                const customers = await response.json();
                
                // Filter by cedula
                this.customerSearchResults = customers.filter(customer => 
                    customer.cedula && customer.cedula.includes(this.customerSearch)
                );
            } catch (error) {
                console.error('Error searching customers:', error);
                this.customerSearchResults = [];
            } finally {
                this.customerSearchLoading = false;
            }
        },
        
        selectExistingCustomer(customer) {
            this.selectedCustomer = customer;
            this.customerData = {
                name: customer.name,
                email: customer.email || '',
                phone: customer.phone || '',
                cedula: customer.cedula || '',
                address: customer.address || ''
            };
            this.customerSearchResults = [];
            this.customerSearch = '';
        },
        
        async checkCedulaExists() {
            if (this.customerData.cedula.length < 3) {
                this.cedulaExists = false;
                return;
            }
            
            try {
                const response = await fetch(`/api/customers/search?q=${encodeURIComponent(this.customerData.cedula)}`);
                const customers = await response.json();
                
                const existingCustomer = customers.find(customer => 
                    customer.cedula === this.customerData.cedula
                );
                
                this.cedulaExists = !!existingCustomer;
                
                if (existingCustomer && !this.selectedCustomer) {
                    // Auto-fill data if cedula exists
                    this.customerData = {
                        name: existingCustomer.name,
                        email: existingCustomer.email || '',
                        phone: existingCustomer.phone || '',
                        cedula: existingCustomer.cedula,
                        address: existingCustomer.address || ''
                    };
                }
            } catch (error) {
                console.error('Error checking cedula:', error);
                this.cedulaExists = false;
            }
        }
    }
}
</script>
@endsection