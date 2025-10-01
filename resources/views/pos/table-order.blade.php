@extends('layouts.app')

@section('title', 'Orden de Mesa ' . $table->name)

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)] bg-gray-50" x-data="tableOrderSystem()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mesa {{ $table->name }}</h1>
                <p class="text-sm text-gray-500">Capacidad: {{ $table->capacity }} personas</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            @if($activeOrder)
                <span class="badge {{ $activeOrder->status === 'pending' ? 'badge-warning' : ($activeOrder->status === 'preparing' ? 'badge-info' : 'badge-success') }}">
                    {{ ucfirst($activeOrder->status) }}
                </span>
                <span class="text-sm text-gray-500">Orden: {{ $activeOrder->order_number }}</span>
            @else
                <span class="badge badge-secondary">Sin orden activa</span>
            @endif
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Left Side - Product Categories & Products -->
        <div class="flex-1 flex flex-col">
            <!-- Product Categories -->
            <div class="bg-white border-b border-gray-200 p-4 flex-shrink-0">
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <button @click="selectedCategory = null" 
                            :class="selectedCategory === null ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-colors duration-200">
                        Todos
                    </button>
                    @foreach($categories as $category)
                    <button @click="selectedCategory = {{ $category->id }}" 
                            :class="selectedCategory === {{ $category->id }} ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-colors duration-200">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                    <div class="product-card"
                         @click="addProductToOrder({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                         x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}">
                        @if($product->image)
                        <div class="product-card-image">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="product-card-image">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        @endif
                        
                        <h3 class="product-card-title">{{ $product->name }}</h3>
                        <p class="product-card-description">{{ $product->description }}</p>
                        <div class="flex items-center justify-between mt-auto">
                            <span class="product-card-price">${{ number_format($product->price, 2) }}</span>
                            @if($product->is_featured)
                            <span class="badge badge-warning">Destacado</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side - Order Details -->
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col flex-shrink-0">
            @if($activeOrder)
                <!-- Order Header -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">Comanda #{{ str_pad($activeOrder->daily_number, 2, '0', STR_PAD_LEFT) }}</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('pos.print.kitchen', $activeOrder->id) }}" target="_blank" class="btn-sm btn-warning">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cocina
                            </a>
                            <a href="{{ route('pos.print.bar', $activeOrder->id) }}" target="_blank" class="btn-sm btn-info">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Barra
                            </a>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p>Cliente: {{ $activeOrder->customer ? $activeOrder->customer->name : ($activeOrder->customer_name ?: 'Cliente General') }}</p>
                        <p>Mesero: {{ $activeOrder->user->name }}</p>
                        <p>Hora: {{ $activeOrder->created_at->format('H:i') }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="space-y-3">
                        @foreach($activeOrder->items as $item)
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <button @click="removeItem({{ $item->id }})" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                            :disabled="{{ $item->quantity }} <= 1"
                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <span class="w-8 text-center font-medium">{{ $item->quantity }}</span>
                                    <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </div>
                                <span class="font-bold text-primary-600">${{ number_format($item->total_price, 2) }}</span>
                            </div>
                            @if($item->notes)
                            <div class="mt-2 text-xs text-gray-500">
                                <strong>Notas:</strong> {{ $item->notes }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t border-gray-200 p-4 flex-shrink-0">
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span>${{ number_format($activeOrder->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Impuestos (16%):</span>
                            <span>${{ number_format($activeOrder->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                            <span>Total:</span>
                            <span>${{ number_format($activeOrder ? $activeOrder->total_amount : 0, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Status Buttons -->
                        <div class="flex space-x-2">
                            <button @click="updateOrderStatus('preparing')" 
                                    :disabled="'{{ $activeOrder->status }}' === 'preparing'"
                                    class="flex-1 btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                En Preparación
                            </button>
                            <button @click="updateOrderStatus('ready')" 
                                    :disabled="'{{ $activeOrder->status }}' === 'ready'"
                                    class="flex-1 btn-success disabled:opacity-50 disabled:cursor-not-allowed">
                                Listo
                            </button>
                        </div>
                        
                        <!-- Payment and Finalization Buttons -->
                        <div class="flex space-x-2">
                            <button @click="showPaymentModal = true" 
                                    class="flex-1 btn-warning">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Procesar Pago
                            </button>
                            <button @click="finalizeOrder()" 
                                    class="flex-1 btn-danger">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Finalizar Orden
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Active Order -->
                <div class="flex-1 flex items-center justify-center p-8">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin orden activa</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta mesa no tiene una orden en curso.</p>
                        <div class="mt-6">
                            <a href="{{ route('pos.create') }}" class="btn-primary">
                                Crear Nueva Orden
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Modal -->
    @if($activeOrder)
    <div x-show="showPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Procesar Pago</h3>
                <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Order Total -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total a Pagar:</span>
                        <span>${{ number_format($activeOrder->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="form-label">Método de Pago</label>
                    <select x-model="paymentMethod" class="form-select">
                        <option value="">Seleccionar método</option>
                        <option value="cash">Efectivo</option>
                        <option value="mobile_payment">Pago Móvil</option>
                        <option value="zelle">Zelle</option>
                        <option value="binance">Binance</option>
                        <option value="pos">Punto de Venta</option>
                        <option value="transfer">Transferencia</option>
                    </select>
                </div>

                <!-- Payment Reference (for transfers) -->
                <div x-show="paymentMethod === 'mobile_payment' || paymentMethod === 'zelle' || paymentMethod === 'binance' || paymentMethod === 'transfer'">
                    <label class="form-label">Referencia de Pago</label>
                    <input type="text" x-model="paymentReference" class="form-input" placeholder="Número de referencia">
                </div>

                <!-- Amount -->
                <div>
                    <label class="form-label">Monto Recibido</label>
                    <input type="number" x-model="paymentAmount" step="0.01" min="0" class="form-input" placeholder="0.00">
                </div>

                <!-- Customer Search/Data -->
                <div>
                    <label class="form-label">Cliente</label>
                    
                    <!-- Customer Search -->
                    <div class="relative">
                        <input type="text" 
                               x-model="customerSearch" 
                               @input="searchCustomers()"
                               @focus="showCustomerResults = true"
                               class="form-input" 
                               placeholder="Buscar cliente por nombre, teléfono o email...">
                        
                        <!-- Search Results Dropdown -->
                        <div x-show="showCustomerResults && customerSearchResults.length > 0" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="customer in customerSearchResults" :key="customer.id">
                                <div @click="selectCustomer(customer)" 
                                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium text-gray-900" x-text="customer.name"></div>
                                    <div class="text-sm text-gray-500" x-text="customer.phone + ' - ' + customer.email"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Selected Customer Info -->
                    <div x-show="selectedCustomer" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-green-800" x-text="selectedCustomer.name"></div>
                                <div class="text-sm text-green-600" x-text="selectedCustomer.phone + ' - ' + selectedCustomer.email"></div>
                            </div>
                            <button @click="clearSelectedCustomer()" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- New Customer Form (if no customer selected) -->
                    <div x-show="!selectedCustomer" class="mt-4 space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" x-model="createNewCustomer" class="form-checkbox">
                            <label class="text-sm text-gray-700">Crear nuevo cliente</label>
                        </div>
                        
                        <div x-show="createNewCustomer" class="space-y-2">
                            <input type="text" x-model="customerData.name" class="form-input" placeholder="Nombre completo" required>
                            <input type="email" x-model="customerData.email" class="form-input" placeholder="Email">
                            <input type="tel" x-model="customerData.phone" class="form-input" placeholder="Teléfono">
                            <input type="text" x-model="customerData.cedula" class="form-input" placeholder="Cédula">
                            <textarea x-model="customerData.address" class="form-textarea" placeholder="Dirección"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button @click="showPaymentModal = false" class="flex-1 btn-secondary">
                    Cancelar
                </button>
                <button @click="processPayment()" 
                        :disabled="!paymentMethod || !paymentAmount"
                        class="flex-1 btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    Procesar Pago
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function tableOrderSystem() {
    return {
        selectedCategory: null,
        orderId: {{ $activeOrder ? $activeOrder->id : 'null' }},
        showPaymentModal: false,
        paymentMethod: '',
        paymentReference: '',
        paymentAmount: {{ $activeOrder ? $activeOrder->total_amount : 0 }},
        customerComplete: {{ $activeOrder && $activeOrder->customer ? 'true' : 'false' }},
        
        // Customer search functionality
        customerSearch: '',
        customerSearchResults: [],
        showCustomerResults: false,
        selectedCustomer: null,
        createNewCustomer: false,
        customerData: {
            name: '{{ $activeOrder && $activeOrder->customer ? $activeOrder->customer->name : "" }}',
            email: '{{ $activeOrder && $activeOrder->customer ? $activeOrder->customer->email : "" }}',
            phone: '{{ $activeOrder && $activeOrder->customer ? $activeOrder->customer->phone : "" }}',
            cedula: '{{ $activeOrder && $activeOrder->customer ? $activeOrder->customer->cedula : "" }}',
            address: '{{ $activeOrder && $activeOrder->customer ? $activeOrder->customer->address : "" }}'
        },

        addProductToOrder(productId, name, price) {
            if (!this.orderId) {
                alert('No hay orden activa para esta mesa');
                return;
            }

            const quantity = prompt(`¿Cuántas unidades de ${name}?`, '1');
            if (!quantity || quantity < 1) return;

            fetch(`/pos/${this.orderId}/add-product`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
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
                alert('Error al agregar producto');
            });
        },

        removeItem(itemId) {
            if (!confirm('¿Eliminar este item de la orden?')) return;

            fetch(`/pos/${this.orderId}/item/${itemId}`, {
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
                alert('Error al eliminar item');
            });
        },

        updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;

            fetch(`/pos/${this.orderId}/item/${itemId}/quantity`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
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
                alert('Error al actualizar cantidad');
            });
        },

        updateOrderStatus(status) {
            fetch(`/pos/${this.orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
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
                alert('Error al actualizar estado');
            });
        },

        processPayment() {
            if (!this.paymentMethod || !this.paymentAmount) {
                alert('Por favor completa todos los campos requeridos');
                return;
            }

            const paymentData = {
                payment_method: this.paymentMethod,
                amount: parseFloat(this.paymentAmount),
                reference: this.paymentReference,
                customer_data: this.customerData,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            fetch(`/pos/${this.orderId}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': paymentData._token
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pago procesado exitosamente');
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
            if (confirm('¿Estás seguro de que quieres finalizar esta orden? Esta acción no se puede deshacer.')) {
                fetch(`/pos/${this.orderId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: 'delivered'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Orden finalizada exitosamente');
                        location.reload();
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

        // Customer search functions
        searchCustomers() {
            if (this.customerSearch.length < 2) {
                this.customerSearchResults = [];
                return;
            }

            fetch(`/api/customers/search?q=${encodeURIComponent(this.customerSearch)}`)
                .then(response => response.json())
                .then(data => {
                    this.customerSearchResults = data;
                })
                .catch(error => {
                    console.error('Error searching customers:', error);
                    this.customerSearchResults = [];
                });
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.customerSearch = customer.name;
            this.showCustomerResults = false;
            this.createNewCustomer = false;
            
            // Pre-fill customer data
            this.customerData = {
                name: customer.name,
                email: customer.email || '',
                phone: customer.phone || '',
                cedula: '',
                address: ''
            };
        },

        clearSelectedCustomer() {
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.customerSearchResults = [];
            this.showCustomerResults = false;
            this.createNewCustomer = false;
            
            // Clear customer data
            this.customerData = {
                name: '',
                email: '',
                phone: '',
                cedula: '',
                address: ''
            };
        }
    }
}
</script>
@endsection
