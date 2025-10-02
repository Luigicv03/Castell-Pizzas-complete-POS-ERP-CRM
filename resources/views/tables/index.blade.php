@extends('layouts.app')

@section('title', 'Gestión de Mesas')
@section('subtitle', 'Control y administración de mesas del restaurante')

@section('content')
<div class="space-y-6">
    <!-- Header con acciones -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Mesas</h1>
            <p class="text-gray-600">Controla el estado y disponibilidad de las mesas</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <button onclick="syncTableStatuses()" class="btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Sincronizar Estados
            </button>
            <button class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Mesa
            </button>
        </div>
    </div>

    <!-- Barra de Estado de Pedidos en Tiempo Real -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-data="orderStatusBar()">
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
                <button @click="refreshOrders()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Filtros de Tipo de Pedido -->
        <div class="flex space-x-2 mb-4">
            <button @click="selectedType = null" 
                    :class="selectedType === null ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Todos
            </button>
            <button @click="selectedType = 'dine_in'" 
                    :class="selectedType === 'dine_in' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Comer Aquí
            </button>
            <button @click="selectedType = 'takeaway'" 
                    :class="selectedType === 'takeaway' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Para Llevar
            </button>
            <button @click="selectedType = 'delivery'" 
                    :class="selectedType === 'delivery' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Delivery
            </button>
            <button @click="selectedType = 'pickup'" 
                    :class="selectedType === 'pickup' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors">
                Pickup
            </button>
        </div>
        
        <!-- Lista de Pedidos Activos -->
        <div class="overflow-x-auto">
            <div class="flex space-x-4 pb-2" style="min-width: max-content;">
                <template x-for="order in filteredOrders" :key="order.id">
                    <div @click="openOrder(order)" 
                         class="flex-shrink-0 w-64 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                         :class="getOrderCardClass(order.status)">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900" x-text="'#' + order.daily_number"></span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                          :class="getStatusBadgeClass(order.status)"
                                          x-text="getStatusText(order.status)"></span>
                                </div>
                                <div class="text-xs text-gray-500" x-text="formatTime(order.created_at)"></div>
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
                                    <span class="text-sm text-gray-600" x-text="getTypeText(order.type)"></span>
                                    <span x-show="order.table" class="text-sm text-gray-600" x-text="' - ' + order.table.name"></span>
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
                                    <button @click.stop="updateOrderStatus(order.id, 'preparing')" 
                                            x-show="order.status === 'pending'"
                                            class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200 transition-colors">
                                        Preparar
                                    </button>
                                    <button @click.stop="updateOrderStatus(order.id, 'ready')" 
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
                <div x-show="filteredOrders.length === 0" class="flex-shrink-0 w-64 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center p-8">
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

    <!-- Estadísticas de mesas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-success">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Mesas Libres</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $tables->where('status', 'free')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-danger">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Mesas Ocupadas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $tables->where('status', 'occupied')->count() }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Reservadas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $tables->where('status', 'reserved')->count() }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Mesas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $tables->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Visual de Mesas -->
    @include('tables._layout')

    <!-- Leyenda de estados -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Leyenda de Estados</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 bg-green-100 border-2 border-green-400 rounded"></div>
                    <span class="text-sm text-gray-700">Libre - Disponible para uso</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 bg-red-100 border-2 border-red-400 rounded"></div>
                    <span class="text-sm text-gray-700">Ocupada - En uso actual</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 bg-yellow-100 border-2 border-yellow-400 rounded"></div>
                    <span class="text-sm text-gray-700">Reservada - Reservada para más tarde</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 bg-orange-100 border-2 border-orange-400 rounded"></div>
                    <span class="text-sm text-gray-700">Pago Pendiente - Esperando pago</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function orderStatusBar() {
    return {
        orders: [],
        selectedType: null,
        refreshInterval: null,
        
        init() {
            console.log('Inicializando orderStatusBar...');
            this.loadOrders();
            this.startAutoRefresh();
        },
        
        get filteredOrders() {
            console.log('Filtrando pedidos. Total:', this.orders.length, 'Tipo seleccionado:', this.selectedType);
            if (this.selectedType === null) {
                console.log('Mostrando todos los pedidos:', this.orders);
                return this.orders;
            }
            const filtered = this.orders.filter(order => order.type === this.selectedType);
            console.log('Pedidos filtrados por tipo', this.selectedType, ':', filtered);
            return filtered;
        },
        
        loadOrders() {
            console.log('Loading orders...');
            fetch('/api/orders/active')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Orders loaded:', data);
                    console.log('Total orders:', data.length);
                    
                    // Log de tipos de pedidos
                    const types = data.map(order => order.type);
                    console.log('Tipos de pedidos encontrados:', [...new Set(types)]);
                    
                    this.orders = data;
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    this.orders = [];
                });
        },
        
        refreshOrders() {
            this.loadOrders();
        },
        
        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                this.loadOrders();
            }, 10000); // Actualizar cada 10 segundos
        },
        
        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        },
        
        openOrder(order) {
            // Siempre ir a la vista de detalle de orden (unificada)
            window.location.href = `/pos/${order.id}/detail`;
        },
        
        updateOrderStatus(orderId, newStatus) {
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
                    this.loadOrders(); // Recargar pedidos
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado');
            });
        },
        
        getStatusText(status) {
            const statusMap = {
                'pending': 'Pendiente',
                'preparing': 'Preparando',
                'ready': 'Listo',
                'delivered': 'Entregado',
                'cancelled': 'Cancelado'
            };
            return statusMap[status] || status;
        },
        
        getTypeText(type) {
            const typeMap = {
                'dine_in': 'Comer Aquí',
                'takeaway': 'Para Llevar',
                'delivery': 'Delivery',
                'pickup': 'Pickup'
            };
            return typeMap[type] || type;
        },
        
        getStatusBadgeClass(status) {
            const classMap = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'preparing': 'bg-blue-100 text-blue-800',
                'ready': 'bg-green-100 text-green-800',
                'delivered': 'bg-gray-100 text-gray-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classMap[status] || 'bg-gray-100 text-gray-800';
        },
        
        getOrderCardClass(status) {
            const classMap = {
                'pending': 'border-l-4 border-l-yellow-400',
                'preparing': 'border-l-4 border-l-blue-400',
                'ready': 'border-l-4 border-l-green-400',
                'delivered': 'border-l-4 border-l-gray-400',
                'cancelled': 'border-l-4 border-l-red-400'
            };
            return classMap[status] || 'border-l-4 border-l-gray-400';
        },
        
        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
    }
}

function openTableOrder(tableId, currentStatus) {
    console.log('🔍 CLIC EN MESA:', tableId, 'Estado:', currentStatus);
    
    // Buscar órdenes activas para encontrar la orden de esta mesa
    fetch('/api/orders/active')
        .then(response => {
            console.log('📡 Respuesta de API:', response.status);
            return response.json();
        })
        .then(orders => {
            console.log('📋 Órdenes activas encontradas:', orders);
            console.log('🔍 Buscando orden para mesa:', tableId);
            
            // Buscar orden activa para esta mesa específica
            const activeOrder = orders.find(order => {
                const orderTableId = order.table ? order.table.id : null;
                console.log('🔍 Comparando orden:', order.id, 'con mesa:', orderTableId, 'vs', tableId);
                return orderTableId == tableId;
            });
            console.log('✅ Orden activa para mesa', tableId, ':', activeOrder);
            
            if (activeOrder) {
                // Si hay orden activa, ir DIRECTAMENTE a la vista de detalle unificada
                console.log('🚀 Redirigiendo a vista de detalle:', `/pos/${activeOrder.id}/detail`);
                window.location.href = `/pos/${activeOrder.id}/detail`;
            } else {
                // Si no hay orden activa, pero la mesa está marcada como ocupada,
                // liberar la mesa y ir al POS
                if (currentStatus === 'occupied') {
                    console.log('⚠️ Mesa marcada como ocupada pero sin orden activa. Liberando mesa...');
                    // Liberar la mesa
                    fetch(`/tables/${tableId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'free'
                        })
                    })
                    .then(() => {
                        console.log('✅ Mesa liberada. Redirigiendo al POS...');
                        window.location.href = `/pos?table_id=${tableId}`;
                    })
                    .catch(error => {
                        console.error('❌ Error liberando mesa:', error);
                        window.location.href = `/pos?table_id=${tableId}`;
                    });
                } else {
                    // Si la mesa está libre, ir al POS
                    console.log('🆓 Mesa libre. Redirigiendo al POS...');
                    window.location.href = `/pos?table_id=${tableId}`;
                }
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            // En caso de error, ir al POS
            window.location.href = `/pos?table_id=${tableId}`;
        });
}

function syncTableStatuses() {
    if (confirm('¿Estás seguro de que quieres sincronizar los estados de las mesas?')) {
        fetch('/tables/sync-statuses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Recargar la página para mostrar los cambios
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al sincronizar los estados');
        });
    }
}
</script>
@endsection
