@extends('layouts.app')

@section('title', 'Nueva Orden')

@section('content')
<div class="max-w-4xl mx-auto" x-data="orderTypeSelector()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nueva Orden</h1>
        <p class="mt-2 text-gray-600">Selecciona el tipo de orden y configura los detalles</p>
    </div>

    <!-- Order Type Selection -->
    <div class="card mb-8">
        <div class="card-header">
            <h2 class="text-xl font-semibold text-gray-900">Tipo de Orden</h2>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Comer aquí -->
                <button @click="selectOrderType('dine_in')" 
                        :class="selectedType === 'dine_in' ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-6 border border-gray-200 rounded-lg text-center transition-all duration-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Comer aquí</h3>
                    <p class="text-sm text-gray-500 mt-1">Cliente consume en el local</p>
                </button>

                <!-- Para llevar -->
                <button @click="selectOrderType('takeaway')" 
                        :class="selectedType === 'takeaway' ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-6 border border-gray-200 rounded-lg text-center transition-all duration-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-success-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Para llevar</h3>
                    <p class="text-sm text-gray-500 mt-1">Cliente recoge en el local</p>
                </button>

                <!-- Delivery -->
                <button @click="selectOrderType('delivery')" 
                        :class="selectedType === 'delivery' ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-6 border border-gray-200 rounded-lg text-center transition-all duration-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-warning-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Delivery</h3>
                    <p class="text-sm text-gray-500 mt-1">Envío a domicilio</p>
                </button>

                <!-- Pickup -->
                <button @click="selectOrderType('pickup')" 
                        :class="selectedType === 'pickup' ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-6 border border-gray-200 rounded-lg text-center transition-all duration-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-info-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Pickup</h3>
                    <p class="text-sm text-gray-500 mt-1">Recogida rápida</p>
                </button>
            </div>
        </div>
    </div>

    <!-- Configuration Form -->
    <div x-show="selectedType" x-transition class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold text-gray-900">Configuración de la Orden</h2>
        </div>
        <div class="card-body">
            <form @submit.prevent="proceedToOrderBuilder()">
                <!-- Table Selection (only for dine_in) -->
                <div x-show="selectedType === 'dine_in'" class="mb-6">
                    <label class="form-label">Seleccionar Mesa</label>
                    <select x-model="tableId" class="form-select" required>
                        <option value="">Selecciona una mesa</option>
                        @foreach($tables as $table)
                        <option value="{{ $table->id }}" 
                                {{ $table->status === 'occupied' ? 'disabled' : '' }}>
                            Mesa {{ $table->name }} - {{ $table->capacity }} personas
                            @if($table->status === 'occupied')
                                (Ocupada)
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Selection -->
                <div class="mb-6">
                    <label class="form-label">Cliente</label>
                    <div class="space-y-4">
                        <!-- Search existing customer -->
                        <div>
                            <label class="text-sm font-medium text-gray-700">Buscar cliente existente</label>
                            <div class="flex space-x-2">
                                <input type="text" 
                                       x-model="customerSearch" 
                                       @input="searchCustomers()"
                                       placeholder="Buscar por nombre, teléfono o email..."
                                       class="form-input flex-1">
                                <button type="button" @click="searchCustomers()" class="btn-secondary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Customer search results -->
                            <div x-show="customerSearchResults.length > 0" class="mt-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg">
                                <template x-for="customer in customerSearchResults" :key="customer.id">
                                    <button type="button" 
                                            @click="selectCustomer(customer)"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                        <div class="font-medium" x-text="customer.name"></div>
                                        <div class="text-sm text-gray-500" x-text="customer.phone"></div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Or create new customer -->
                        <div class="border-t pt-4">
                            <label class="text-sm font-medium text-gray-700">O crear cliente nuevo</label>
                            <input type="text" 
                                   x-model="newCustomerName" 
                                   placeholder="Nombre del cliente"
                                   class="form-input mt-1">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('pos.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" 
                            :disabled="!canProceed()"
                            class="btn-primary"
                            :class="{ 'opacity-50 cursor-not-allowed': !canProceed() }">
                        Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function orderTypeSelector() {
    return {
        selectedType: null,
        tableId: null,
        customerId: null,
        customerSearch: '',
        customerSearchResults: [],
        newCustomerName: '',

        selectOrderType(type) {
            this.selectedType = type;
            this.tableId = null;
            this.customerId = null;
            this.customerSearch = '';
            this.customerSearchResults = [];
            this.newCustomerName = '';
        },

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
                });
        },

        selectCustomer(customer) {
            this.customerId = customer.id;
            this.customerSearch = customer.name;
            this.customerSearchResults = [];
        },

        canProceed() {
            if (!this.selectedType) return false;
            
            if (this.selectedType === 'dine_in' && !this.tableId) return false;
            
            return this.customerId || this.newCustomerName;
        },

        proceedToOrderBuilder() {
            if (!this.canProceed()) return;

            const formData = new FormData();
            formData.append('order_type', this.selectedType);
            
            if (this.tableId) {
                formData.append('table_id', this.tableId);
            }
            
            if (this.customerId) {
                formData.append('customer_id', this.customerId);
            } else if (this.newCustomerName) {
                formData.append('customer_name', this.newCustomerName);
            }

            // Submit form to build order
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("pos.build-order") }}';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Add form data
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endsection
