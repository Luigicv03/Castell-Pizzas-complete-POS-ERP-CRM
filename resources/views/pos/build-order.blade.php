@extends('layouts.app')

@section('title', 'Construir Orden')

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)] bg-gray-50" x-data="orderBuilder()">
    <!-- Order Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold text-gray-900">Construir Orden</h1>
            <span class="badge badge-primary" x-text="orderTypeText"></span>
            <span x-show="selectedTable" class="badge badge-info" x-text="'Mesa: ' + selectedTable"></span>
            <span x-show="selectedCustomer" class="badge badge-success" x-text="'Cliente: ' + selectedCustomer"></span>
        </div>
        <div class="flex items-center space-x-4">
            <button @click="showOrderSummary = !showOrderSummary" class="btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Resumen
            </button>
            <span class="text-sm text-gray-500 font-medium">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <!-- Main Content -->
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
                         @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                         x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}">
                        @if($product->image)
                        <div class="product-card-image">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="product-card-image">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
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

        <!-- Right Side - Order Summary -->
        <div x-show="showOrderSummary" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-96 bg-white border-l border-gray-200 flex flex-col flex-shrink-0">
            
            <!-- Order Header -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Resumen de la Orden</h3>
                <p class="text-sm text-gray-500">Agrega productos a la orden</p>
            </div>

            <!-- Order Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div x-show="cartItems.length === 0" class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="mt-2">No hay productos en la orden</p>
                </div>

                <div x-show="cartItems.length > 0" class="space-y-3">
                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900" x-text="item.name"></h4>
                                <p class="text-sm text-gray-500">$<span x-text="item.price.toFixed(2)"></span> c/u</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="updateQuantity(index, item.quantity - 1)" 
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span class="w-8 text-center font-medium" x-text="item.quantity"></span>
                                <button @click="updateQuantity(index, item.quantity + 1)" 
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                                <button @click="removeItem(index)" 
                                        class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Order Totals -->
            <div class="border-t border-gray-200 p-4">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">$<span x-text="subtotal.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Impuestos (16%):</span>
                        <span class="font-medium">$<span x-text="taxAmount.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span>$<span x-text="totalAmount.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-3">
                    <button @click="confirmOrder()" 
                            :disabled="cartItems.length === 0"
                            class="w-full btn-primary"
                            :class="{ 'opacity-50 cursor-not-allowed': cartItems.length === 0 }">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirmar Orden
                    </button>
                    
                    <button @click="showOrderSummary = false" class="w-full btn-secondary">
                        Continuar Agregando
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div x-show="showConfirmationModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¡Orden Confirmada!</h3>
                <p class="text-gray-600 mb-4">La orden ha sido enviada a la cocina y barra. Las comandas se están imprimiendo.</p>
                <div class="flex space-x-3">
                    <button @click="goToOrders()" class="flex-1 btn-primary">
                        Ver Órdenes
                    </button>
                    <button @click="createNewOrder()" class="flex-1 btn-secondary">
                        Nueva Orden
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function orderBuilder() {
    return {
        selectedCategory: null,
        showOrderSummary: true,
        showConfirmationModal: false,
        cartItems: [],
        orderTypeText: '{{ $orderType ?? "Comer aquí" }}',
        selectedTable: '{{ $selectedTable ?? "" }}',
        selectedCustomer: '{{ $selectedCustomer ?? "" }}',

        get subtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get taxAmount() {
            return this.subtotal * 0.16;
        },

        get totalAmount() {
            return this.subtotal + this.taxAmount;
        },

        addToCart(productId, name, price) {
            const existingItem = this.cartItems.find(item => item.productId === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                this.cartItems.push({
                    productId: productId,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeItem(index);
            } else {
                this.cartItems[index].quantity = newQuantity;
            }
        },

        removeItem(index) {
            this.cartItems.splice(index, 1);
        },

        async confirmOrder() {
            if (this.cartItems.length === 0) return;

            try {
                const response = await fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        order_type: '{{ $orderType ?? "dine_in" }}',
                        table_id: '{{ $tableId ?? null }}',
                        customer_id: '{{ $customerId ?? null }}',
                        customer_name: '{{ $customerName ?? null }}',
                        items: this.cartItems.map(item => ({
                            product_id: item.productId,
                            quantity: item.quantity,
                            unit_price: item.price
                        })),
                        subtotal: this.subtotal,
                        tax_amount: this.taxAmount,
                        total_amount: this.totalAmount
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showConfirmationModal = true;
                } else {
                    alert('Error al crear la orden: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear la orden');
            }
        },

        goToOrders() {
            window.location.href = '{{ route("pos.index") }}';
        },

        createNewOrder() {
            window.location.href = '{{ route("pos.create") }}';
        }
    }
}
</script>
@endsection
