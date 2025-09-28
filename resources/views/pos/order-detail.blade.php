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
            <button @click="showPaymentModal = true" 
                    class="btn btn-warning w-100">
                <i class="fas fa-credit-card me-1"></i> Procesar Pedido
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
    <div x-show="showPaymentModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" @click.away="showPaymentModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Procesar Pago</h3>
                
                <form @submit.prevent="processPayment()">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Método de Pago</label>
                            <select x-model="paymentMethod" class="form-select" required>
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
                            <input type="number" x-model="paymentAmount" step="0.01" min="0.01" 
                                   :max="remainingAmount" class="form-input" required>
                            <p class="text-sm text-gray-500 mt-1">
                                Restante: $<span x-text="remainingAmount.toFixed(2)"></span>
                            </p>
                        </div>
                        
                        <div x-show="paymentMethod === 'transfer' || paymentMethod === 'mobile_payment' || paymentMethod === 'zelle'">
                            <label class="form-label">Referencia</label>
                            <input type="text" x-model="paymentReference" class="form-input" 
                                   placeholder="Número de referencia">
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
                        <button type="button" @click="showPaymentModal = false" class="btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            Procesar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function orderDetailSystem() {
    return {
        selectedCategory: null,
        orderStatus: '{{ $order->status }}',
        showPaymentModal: false,
        paymentMethod: '',
        paymentAmount: 0,
        paymentReference: '',
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
        
        processPayment() {
            fetch(`/pos/{{ $order->id }}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_method: this.paymentMethod,
                    amount: this.paymentAmount,
                    reference: this.paymentReference,
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