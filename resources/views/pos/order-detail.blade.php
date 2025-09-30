@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT))

@section('content')
<style>
.pos-grid {
    display: grid;
    grid-template-columns: 350px 1fr;
    grid-template-rows: auto 1fr;
    gap: 15px;
    height: calc(100vh - 100px);
    padding: 15px;
}

.pos-header {
    grid-column: 1 / -1;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pos-info {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-y: auto;
}

.pos-menu {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.pos-cart {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    overflow-y: auto;
    flex: 1;
    padding: 10px 0;
}

.product-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
    height: 140px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.cart-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 8px;
    margin-bottom: 8px;
    border: 1px solid #e9ecef;
}

.btn-compact {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-mini {
    width: 24px;
    height: 24px;
    padding: 0;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
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
            <button @click="printBarOrder()" class="btn btn-info btn-compact">
                <i class="fas fa-cocktail"></i> Barra
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
        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
            @if($order->items->count() > 0)
                @foreach($order->items as $item)
                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold" style="font-size: 13px;">{{ $item->product->name }}</h6>
                        <div class="d-flex align-items-center gap-1">
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="btn btn-outline-secondary btn-mini">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="fw-bold mx-2">{{ $item->quantity }}</span>
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="btn btn-outline-secondary btn-mini">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">${{ number_format($item->total_price, 2) }}</div>
                        <button @click="removeItem({{ $item->id }})" class="btn btn-outline-danger btn-mini mt-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
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
        <div class="d-grid gap-2">
            <button @click="updateOrderStatus('preparing')" 
                    :disabled="orderStatus === 'preparing'"
                    :class="orderStatus === 'preparing' ? 'btn btn-outline-primary' : 'btn btn-primary'"
                    class="w-100">
                <i class="fas fa-clock me-1"></i>
                <span x-text="orderStatus === 'preparing' ? '✓ En Preparación' : 'En Preparación'"></span>
            </button>
            <button @click="updateOrderStatus('ready')" 
                    :disabled="orderStatus === 'ready'"
                    :class="orderStatus === 'ready' ? 'btn btn-outline-success' : 'btn btn-success'"
                    class="w-100">
                <i class="fas fa-check me-1"></i>
                <span x-text="orderStatus === 'ready' ? '✓ Listo' : 'Listo'"></span>
            </button>
            <button onclick="openPaymentModal()" 
                    class="btn btn-warning w-100">
                <i class="fas fa-credit-card me-1"></i> Procesar Pago
            </button>
            <button @click="updateOrderStatus('cancelled')" 
                    :disabled="orderStatus === 'cancelled'"
                    class="btn btn-danger w-100"
                    onclick="return confirm('¿Estás seguro de cancelar este pedido?')">
                <i class="fas fa-times me-1"></i> Cancelar Pedido
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
                <div x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}" 
                     class="product-card">
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="bg-light rounded p-2 mb-2">
                                <i class="fas fa-utensils text-muted"></i>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-1" style="font-size: 12px;">{{ Str::limit($product->name, 18) }}</h6>
                        <p class="text-muted mb-2" style="font-size: 10px;">{{ Str::limit($product->description, 25) }}</p>
                        <div class="mb-2">
                            <span class="fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                            @if($product->is_featured)
                            <span class="badge bg-warning text-dark ms-1" style="font-size: 8px;">⭐</span>
                            @endif
                        </div>
                        <button @click="addProduct({{ $product->id }})" 
                                class="btn btn-primary btn-compact w-100">
                            Agregar
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
         style="z-index: 9999;">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4" 
             @click.away="showPaymentModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Procesar Pago</h3>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
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
                <div x-show="payments.length > 0" class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Métodos de Pago Agregados:</h4>
                    <div class="space-y-2">
                        <template x-for="(payment, index) in payments" :key="index">
                            <div class="flex justify-between items-center bg-gray-100 p-3 rounded">
                                <div>
                                    <span class="font-medium" x-text="getPaymentMethodText(payment.method)"></span>
                                    <span x-show="payment.reference" class="text-sm text-gray-600 ml-2">
                                        (Ref: <span x-text="payment.reference"></span>)
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold">$<span x-text="payment.amount.toFixed(2)"></span></span>
                                    <button @click="removePayment(index)" class="text-danger hover:text-danger-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Add New Payment Form -->
                <div x-show="remainingAmount > 0" class="border-t pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Agregar Método de Pago</h4>
                    <form @submit.prevent="addPayment()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Agregar Pago
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Customer Data -->
                <div class="border-t pt-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">Datos del Cliente</h4>
                            
                            <!-- Customer Search/Add Toggle -->
                            <div class="mb-4">
                                <div class="flex space-x-2">
                                    <button type="button" @click="customerMode = 'search'" 
                                            :class="customerMode === 'search' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Buscar Cliente Existente
                                    </button>
                                    <button type="button" @click="customerMode = 'new'" 
                                            :class="customerMode === 'new' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Agregar Cliente Nuevo
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
                            <div x-show="customerMode === 'new' || (customerMode === 'search' && selectedCustomer)" class="space-y-3">
                                <div>
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" x-model="customerData.name" class="form-input" 
                                           placeholder="Nombre del cliente" required>
                                </div>
                                
                                <div>
                                    <label class="form-label">Email</label>
                                    <input type="email" x-model="customerData.email" class="form-input" 
                                           placeholder="email@ejemplo.com">
                                </div>
                                
                                <div>
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" x-model="customerData.phone" class="form-input" 
                                           placeholder="Número de teléfono">
                                </div>
                                
                                <div>
                                    <label class="form-label">Cédula</label>
                                    <input type="text" x-model="customerData.cedula" 
                                           @input="checkCedulaExists()"
                                           class="form-input" 
                                           placeholder="Número de cédula">
                                    <div x-show="cedulaExists" class="text-sm text-orange-600 mt-1">
                                        ⚠️ Esta cédula ya está registrada. Se actualizarán los datos del cliente existente.
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="form-label">Dirección</label>
                                    <textarea x-model="customerData.address" class="form-textarea" 
                                              placeholder="Dirección completa"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showPaymentModal = false" class="btn btn-secondary">
                            Cancelar
                        </button>
                        <button type="button" @click="processPayment()" 
                                :disabled="remainingAmount > 0 || payments.length === 0"
                                class="btn btn-primary">
                            <i class="fas fa-credit-card me-1"></i> Procesar Pago
                        </button>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Procesamiento de Pagos -->
<div id="paymentModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
     style="z-index: 9999; display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Procesar Pago</h3>
                <button onclick="document.getElementById('paymentModal').style.display='none'" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="flex justify-between items-center">
                    <span class="font-medium">Total del Pedido:</span>
                    <span class="text-lg font-bold text-primary">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="font-medium">Pagado:</span>
                    <span class="text-lg font-bold text-success">$<span id="totalPaid">0.00</span></span>
                </div>
                <div class="flex justify-between items-center mt-2 border-t pt-2">
                    <span class="font-bold">Restante:</span>
                    <span class="text-xl font-bold text-danger">$<span id="remainingAmount">{{ number_format($order->total_amount, 2) }}</span></span>
                </div>
            </div>

            <!-- Payment Methods List -->
            <div id="paymentsList" class="mb-4" style="display: none;">
                <h4 class="font-medium text-gray-900 mb-2">Métodos de Pago Agregados:</h4>
                <div id="paymentsContainer" class="space-y-2">
                    <!-- Payments will be added here dynamically -->
                </div>
            </div>

            <!-- Add New Payment Form -->
            <div id="addPaymentForm" class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-3">Agregar Método de Pago</h4>
                <form id="paymentForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Método de Pago</label>
                            <select id="paymentMethod" class="form-select" required>
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
                            <input type="number" id="paymentAmount" step="0.01" min="0.01" 
                                   class="form-input" required>
                        </div>
                        
                        <div id="referenceField" style="display: none;" class="md:col-span-2">
                            <label class="form-label">Referencia</label>
                            <input type="text" id="paymentReference" class="form-input" 
                                   placeholder="Número de referencia">
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Agregar Pago
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Data -->
            <div class="border-t pt-4">
                <h4 class="text-md font-semibold text-gray-900 mb-3">Datos del Cliente</h4>
                
                <!-- Customer Search/Add Toggle -->
                <div class="mb-4">
                    <div class="flex space-x-2">
                        <button type="button" id="searchCustomerBtn" 
                                class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white">
                            Buscar Cliente Existente
                        </button>
                        <button type="button" id="newCustomerBtn" 
                                class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700">
                            Agregar Cliente Nuevo
                        </button>
                    </div>
                </div>

                <!-- Customer Search -->
                <div id="customerSearch" class="space-y-3">
                    <div>
                        <label class="form-label">Buscar por Cédula</label>
                        <input type="text" id="customerSearchInput" class="form-input" 
                               placeholder="Ingrese número de cédula">
                    </div>
                    <div id="searchResults" class="space-y-2" style="display: none;">
                        <!-- Search results will be added here -->
                    </div>
                </div>

                <!-- Customer Form -->
                <div id="customerForm" class="space-y-3" style="display: none;">
                    <div>
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" id="customerName" class="form-input" 
                               placeholder="Nombre del cliente" required>
                    </div>
                    
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" id="customerEmail" class="form-input" 
                               placeholder="email@ejemplo.com">
                    </div>
                    
                    <div>
                        <label class="form-label">Teléfono</label>
                        <input type="tel" id="customerPhone" class="form-input" 
                               placeholder="Número de teléfono">
                    </div>
                    
                    <div>
                        <label class="form-label">Cédula</label>
                        <input type="text" id="customerCedula" class="form-input" 
                               placeholder="Número de cédula" required>
                    </div>
                    
                    <div>
                        <label class="form-label">Dirección</label>
                        <textarea id="customerAddress" class="form-textarea" 
                                  placeholder="Dirección completa"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="document.getElementById('paymentModal').style.display='none'" 
                        class="btn btn-secondary">
                    Cancelar
                </button>
                <button id="processPaymentBtn" 
                        class="btn btn-primary" disabled>
                    <i class="fas fa-credit-card me-1"></i> Procesar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js CDN como respaldo -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Variables globales
let payments = [];
let customerMode = 'search';
let orderTotal = {{ $order->total_amount }};

// Función JavaScript pura para abrir el modal
function openPaymentModal() {
    console.log('Opening payment modal...');
    document.getElementById('paymentModal').style.display = 'block';
    resetPaymentModal();
}

// Resetear el modal
function resetPaymentModal() {
    payments = [];
    customerMode = 'search';
    updatePaymentSummary();
    showCustomerSearch();
    document.getElementById('paymentForm').reset();
    document.getElementById('processPaymentBtn').disabled = true;
}

// Actualizar resumen de pagos
function updatePaymentSummary() {
    const totalPaid = payments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
    const remaining = orderTotal - totalPaid;
    
    document.getElementById('totalPaid').textContent = totalPaid.toFixed(2);
    document.getElementById('remainingAmount').textContent = remaining.toFixed(2);
    
    // Mostrar/ocultar lista de pagos
    const paymentsList = document.getElementById('paymentsList');
    if (payments.length > 0) {
        paymentsList.style.display = 'block';
        updatePaymentsList();
    } else {
        paymentsList.style.display = 'none';
    }
    
    // Habilitar/deshabilitar botón de procesar
    const processBtn = document.getElementById('processPaymentBtn');
    if (remaining <= 0.01 && payments.length > 0) {
        processBtn.disabled = false;
        processBtn.classList.remove('btn-secondary');
        processBtn.classList.add('btn-primary');
    } else {
        processBtn.disabled = true;
        processBtn.classList.remove('btn-primary');
        processBtn.classList.add('btn-secondary');
    }
}

// Actualizar lista de pagos
function updatePaymentsList() {
    const container = document.getElementById('paymentsContainer');
    container.innerHTML = '';
    
    payments.forEach((payment, index) => {
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center bg-gray-100 p-3 rounded';
        div.innerHTML = `
            <div>
                <span class="font-medium">${getPaymentMethodText(payment.method)}</span>
                ${payment.reference ? `<span class="text-sm text-gray-600 ml-2">(Ref: ${payment.reference})</span>` : ''}
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold">$${payment.amount.toFixed(2)}</span>
                <button onclick="removePayment(${index})" class="text-danger hover:text-danger-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
    });
}

// Obtener texto del método de pago
function getPaymentMethodText(method) {
    const methods = {
        'cash': 'Efectivo',
        'mobile_payment': 'Pago Móvil',
        'zelle': 'Zelle',
        'binance': 'Binance',
        'pos': 'Punto de Venta',
        'transfer': 'Transferencia'
    };
    return methods[method] || method;
}

// Agregar pago
function addPayment() {
    const method = document.getElementById('paymentMethod').value;
    const amount = parseFloat(document.getElementById('paymentAmount').value);
    const reference = document.getElementById('paymentReference').value;
    
    if (!method || !amount) {
        alert('Por favor complete todos los campos');
        return;
    }
    
    const remaining = orderTotal - payments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
    if (amount > remaining) {
        alert('El monto no puede ser mayor al restante');
        return;
    }
    
    payments.push({ method, amount, reference });
    document.getElementById('paymentForm').reset();
    updatePaymentSummary();
}

// Remover pago
function removePayment(index) {
    payments.splice(index, 1);
    updatePaymentSummary();
}

// Mostrar búsqueda de cliente
function showCustomerSearch() {
    document.getElementById('customerSearch').style.display = 'block';
    document.getElementById('customerForm').style.display = 'none';
    document.getElementById('searchCustomerBtn').className = 'px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white';
    document.getElementById('newCustomerBtn').className = 'px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700';
    customerMode = 'search';
    
    // Limpiar búsqueda anterior
    document.getElementById('customerSearchInput').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

// Mostrar formulario de cliente
function showCustomerForm() {
    document.getElementById('customerSearch').style.display = 'none';
    document.getElementById('customerForm').style.display = 'block';
    document.getElementById('searchCustomerBtn').className = 'px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700';
    document.getElementById('newCustomerBtn').className = 'px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white';
    customerMode = 'new';
    
    // Limpiar resultados de búsqueda
    document.getElementById('searchResults').style.display = 'none';
}

// Mostrar/ocultar campo de referencia
function toggleReferenceField() {
    const method = document.getElementById('paymentMethod').value;
    const referenceField = document.getElementById('referenceField');
    if (['transfer', 'mobile_payment', 'zelle'].includes(method)) {
        referenceField.style.display = 'block';
    } else {
        referenceField.style.display = 'none';
    }
}

// Buscar cliente por cédula
async function searchCustomerByCedula(query) {
    try {
        // Mostrar indicador de carga
        const searchResults = document.getElementById('searchResults');
        searchResults.innerHTML = `
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                    <span class="text-blue-800">Buscando cliente...</span>
                </div>
            </div>
        `;
        searchResults.style.display = 'block';
        
        const response = await fetch(`/api/customers/search?q=${encodeURIComponent(query)}`);
        const customers = await response.json();
        
        // Filtrar por cédula exacta
        const exactMatch = customers.find(customer => 
            customer.cedula && customer.cedula.toString() === query.toString()
        );
        
        if (exactMatch) {
            // Cliente encontrado - autocompletar datos
            fillCustomerData(exactMatch);
            searchResults.innerHTML = `
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="font-medium text-green-800">✓ Cliente encontrado</div>
                    <div class="text-sm text-green-600">${exactMatch.name} - Cédula: ${exactMatch.cedula}</div>
                    <div class="text-xs text-green-500 mt-1">Los datos se han autocompletado</div>
                </div>
            `;
        } else {
            // No se encontró cliente
            searchResults.innerHTML = `
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="font-medium text-yellow-800">⚠ Cliente no encontrado</div>
                    <div class="text-sm text-yellow-600">Se creará un nuevo cliente con cédula: ${query}</div>
                    <button onclick="createNewCustomerWithCedula('${query}')" 
                            class="mt-2 px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i> Crear Cliente Nuevo
                    </button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error searching customer:', error);
        searchResults.innerHTML = `
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="font-medium text-red-800">Error en la búsqueda</div>
                <div class="text-sm text-red-600">No se pudo conectar con la base de datos</div>
            </div>
        `;
    }
}

// Autocompletar datos del cliente
function fillCustomerData(customer) {
    document.getElementById('customerName').value = customer.name || '';
    document.getElementById('customerEmail').value = customer.email || '';
    document.getElementById('customerPhone').value = customer.phone || '';
    document.getElementById('customerCedula').value = customer.cedula || '';
    document.getElementById('customerAddress').value = customer.address || '';
    
    // Cambiar a modo formulario para mostrar los datos
    showCustomerForm();
}

// Crear nuevo cliente con cédula pre-llenada
function createNewCustomerWithCedula(cedula) {
    document.getElementById('customerCedula').value = cedula;
    showCustomerForm();
    document.getElementById('searchResults').style.display = 'none';
}

// Procesar pago
function processPayment() {
    if (payments.length === 0) {
        alert('Debe agregar al menos un método de pago');
        return;
    }
    
    const remaining = orderTotal - payments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
    if (remaining > 0.01) {
        alert('El monto total no ha sido completado. Restante: $' + remaining.toFixed(2));
        return;
    }
    
    const customerName = document.getElementById('customerName').value;
    const customerCedula = document.getElementById('customerCedula').value;
    
    if (!customerName || !customerCedula) {
        alert('Debe completar los datos del cliente');
        return;
    }
    
    const customerData = {
        name: customerName,
        email: document.getElementById('customerEmail').value,
        phone: document.getElementById('customerPhone').value,
        cedula: customerCedula,
        address: document.getElementById('customerAddress').value
    };
    
    // Enviar datos al servidor
    fetch(`/pos/{{ $order->id }}/payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payments: payments,
            customer_data: customerData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('paymentModal').style.display = 'none';
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar el pago');
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Formulario de pago
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addPayment();
    });
    
    // Cambio de método de pago
    document.getElementById('paymentMethod').addEventListener('change', toggleReferenceField);
    
    // Botones de cliente
    document.getElementById('searchCustomerBtn').addEventListener('click', showCustomerSearch);
    document.getElementById('newCustomerBtn').addEventListener('click', showCustomerForm);
    
    // Botón de procesar pago
    document.getElementById('processPaymentBtn').addEventListener('click', processPayment);
    
    // Búsqueda de cliente
    document.getElementById('customerSearchInput').addEventListener('input', function() {
        const query = this.value;
        if (query.length >= 3) {
            searchCustomerByCedula(query);
        } else {
            document.getElementById('searchResults').style.display = 'none';
        }
    });
});

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        modal.style.display = 'none';
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
            
            this.payments.push({...this.currentPayment});
            this.currentPayment = {
                method: '',
                amount: this.remainingAmount,
                reference: ''
            };
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
        },
        
        get remainingAmount() {
            const totalPaid = {{ $order->payments->sum('amount') }};
            const totalAmount = {{ $order->total_amount }};
            return Math.max(0, totalAmount - totalPaid);
        }
    }
}
</script>
@endsection