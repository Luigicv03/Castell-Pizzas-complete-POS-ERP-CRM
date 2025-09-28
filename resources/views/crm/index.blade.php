@extends('layouts.app')

@section('title', 'CRM - Gestión de Clientes')
@section('subtitle', 'Análisis y gestión de relaciones con clientes')

@section('content')
<div class="space-y-6">
    <!-- Header con filtros -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">CRM Dashboard</h1>
            <p class="text-gray-600">Análisis completo de clientes y comportamiento</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="today" {{ $dateRange['period'] == 'today' ? 'selected' : '' }}>Hoy</option>
                    <option value="this_week" {{ $dateRange['period'] == 'this_week' ? 'selected' : '' }}>Esta Semana</option>
                    <option value="this_month" {{ $dateRange['period'] == 'this_month' ? 'selected' : '' }}>Este Mes</option>
                    <option value="30_days" {{ $dateRange['period'] == '30_days' ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="90_days" {{ $dateRange['period'] == '90_days' ? 'selected' : '' }}>Últimos 90 días</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Estadísticas de clientes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Clientes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($customerStats['total_customers']) }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Clientes Activos</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($customerStats['active_customers']) }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Nuevos Hoy</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($customerStats['new_customers_today']) }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Este Mes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($customerStats['new_customers_month']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segmentación de clientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Segmentación de Clientes</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $customerSegments['vip'] }}</div>
                    <div class="text-sm text-yellow-700">Clientes VIP</div>
                    <div class="text-xs text-yellow-600 mt-1">+$500 en compras</div>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $customerSegments['frequent'] }}</div>
                    <div class="text-sm text-blue-700">Frecuentes</div>
                    <div class="text-xs text-blue-600 mt-1">10+ órdenes</div>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $customerSegments['new'] }}</div>
                    <div class="text-sm text-green-700">Nuevos</div>
                    <div class="text-xs text-green-600 mt-1">Últimos 30 días</div>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $customerSegments['inactive'] }}</div>
                    <div class="text-sm text-red-700">Inactivos</div>
                    <div class="text-xs text-red-600 mt-1">Sin órdenes 90+ días</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes más valiosos y recientes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Clientes más valiosos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Clientes Más Valiosos</h3>
            </div>
            <div class="card-body">
                @if($topCustomers->count() > 0)
                    <div class="space-y-3">
                        @foreach($topCustomers as $customer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr($customer->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-primary-600">${{ number_format($customer->total_spent ?? 0, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->orders_count }} órdenes</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p>No hay clientes registrados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Clientes recientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Clientes Recientes</h3>
            </div>
            <div class="card-body">
                @if($recentCustomers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentCustomers as $customer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-success-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr($customer->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">{{ $customer->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $customer->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p>No hay clientes recientes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Análisis de comportamiento -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Análisis de Comportamiento</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ number_format($behaviorAnalysis['avg_order_frequency'], 1) }}</div>
                    <div class="text-sm text-gray-500">Frecuencia Promedio de Órdenes</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">{{ number_format($behaviorAnalysis['avg_customer_lifetime'], 0) }} días</div>
                    <div class="text-sm text-gray-500">Tiempo de Vida Promedio</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-info-600">{{ number_format($behaviorAnalysis['retention_rate'], 1) }}%</div>
                    <div class="text-sm text-gray-500">Tasa de Retención</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('crm.segmentation') }}" class="group bg-gradient-to-br from-primary-50 to-primary-100 border border-primary-200 rounded-xl p-6 hover:from-primary-100 hover:to-primary-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700">Segmentación</h4>
                            <p class="text-sm text-gray-600">Analizar segmentos de clientes</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('crm.behavior') }}" class="group bg-gradient-to-br from-success-50 to-success-100 border border-success-200 rounded-xl p-6 hover:from-success-100 hover:to-success-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-success-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-success-700">Comportamiento</h4>
                            <p class="text-sm text-gray-600">Analizar patrones de compra</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('crm.campaigns') }}" class="group bg-gradient-to-br from-warning-50 to-warning-100 border border-warning-200 rounded-xl p-6 hover:from-warning-100 hover:to-warning-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-warning-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-warning-700">Campañas</h4>
                            <p class="text-sm text-gray-600">Gestionar campañas de marketing</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('crm.retention') }}" class="group bg-gradient-to-br from-info-50 to-info-100 border border-info-200 rounded-xl p-6 hover:from-info-100 hover:to-info-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-info-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-info-700">Retención</h4>
                            <p class="text-sm text-gray-600">Analizar retención de clientes</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection