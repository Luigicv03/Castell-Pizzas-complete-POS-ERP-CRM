@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general del sistema')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">¡Bienvenido, {{ Auth::user()->name }}!</h2>
                <p class="text-primary-100">Aquí tienes un resumen completo de tu pizzería hoy</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-primary-200">{{ now()->format('l, d \d\e F \d\e Y') }}</div>
                <div class="text-lg font-semibold">{{ now()->format('H:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Ventas del Día -->
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ventas del Día</dt>
                            <dd class="text-2xl font-bold text-gray-900">${{ number_format($stats['daily_sales'], 2) }}</dd>
                            <dd class="text-xs text-gray-500 mt-1">
                                Ingresos totales
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ganancias del Día -->
        <div class="stats-card cursor-pointer hover:shadow-lg transition-shadow" onclick="openProfitModal()">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon" style="background-color: #10b981; color: white;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ganancia del Día</dt>
                            <dd class="text-2xl font-bold {{ $stats['daily_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($stats['daily_profit'], 2) }}
                            </dd>
                            <dd class="text-xs flex items-center mt-1 {{ $stats['profit_margin'] >= 50 ? 'text-green-600' : ($stats['profit_margin'] >= 30 ? 'text-yellow-600' : 'text-red-600') }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                {{ number_format($stats['profit_margin'], 1) }}% margen
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Órdenes Pendientes -->
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-warning">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Órdenes Pendientes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['pending_orders'] }}</dd>
                            <dd class="text-xs text-warning-600 flex items-center mt-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Requieren atención
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mesas Ocupadas -->
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-danger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Mesas Ocupadas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['occupied_tables'] }} / {{ $stats['total_tables'] }}</dd>
                            <dd class="text-xs text-gray-500 mt-1">
                                {{ $stats['total_tables'] > 0 ? round(($stats['occupied_tables'] / $stats['total_tables']) * 100, 1) : 0 }}% ocupación
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="stats-card">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-card-icon stats-card-icon-info">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Stock Bajo</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['low_stock_ingredients'] }}</dd>
                            <dd class="text-xs text-info-600 flex items-center mt-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Revisar inventario
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
            <p class="text-sm text-gray-500 mt-1">Accede rápidamente a las funciones más utilizadas</p>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @can('pos.view')
                <a href="{{ route('pos.index') }}" class="group bg-gradient-to-br from-primary-50 to-primary-100 border border-primary-200 rounded-xl p-6 hover:from-primary-100 hover:to-primary-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700">Sistema POS</h4>
                            <p class="text-sm text-gray-600">Gestionar pedidos y ventas</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('tables.view')
                <a href="{{ route('tables.index') }}" class="group bg-gradient-to-br from-success-50 to-success-100 border border-success-200 rounded-xl p-6 hover:from-success-100 hover:to-success-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-success-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-success-700">Gestión de Mesas</h4>
                            <p class="text-sm text-gray-600">Controlar estado de mesas</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('customers.create')
                <a href="{{ route('customers.create') }}" class="group bg-gradient-to-br from-info-50 to-info-100 border border-info-200 rounded-xl p-6 hover:from-info-100 hover:to-info-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-info-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 group-hover:text-info-700">Nuevo Cliente</h4>
                            <p class="text-sm text-gray-600">Registrar nuevo cliente</p>
                        </div>
                    </div>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Pedidos Recientes</h3>
                        <p class="text-sm text-gray-500 mt-1">Últimos pedidos del día</p>
                    </div>
                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todos</a>
                </div>
            </div>
            <div class="card-body">
                @if($recent_orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_orders as $order)
                        <a href="{{ route('orders.details', $order->id) }}" class="block">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150 cursor-pointer group">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                        <span class="text-sm font-bold text-primary-600">#{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-primary-700">
                                            Pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}
                                            @if($order->table)
                                                - Mesa {{ $order->table->name }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $order->created_at->format('d/m/Y H:i') }} • 
                                            <span class="badge badge-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'preparing' ? 'info' : ($order->status === 'ready' ? 'success' : 'secondary')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            @if($order->customer)
                                                • {{ $order->customer->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->items->count() }} items</p>
                                    <div class="flex items-center text-xs text-primary-600 mt-1">
                                        <span>Ver detalles</span>
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No hay pedidos recientes</h4>
                        <p class="text-gray-500 mb-4">Los pedidos del día aparecerán aquí</p>
                        @can('pos.view')
                        <a href="{{ route('pos.index') }}" class="btn-primary btn-sm">Crear primer pedido</a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Productos Más Vendidos</h3>
                        <p class="text-sm text-gray-500 mt-1">Ranking del día</p>
                    </div>
                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver reporte</a>
                </div>
            </div>
            <div class="card-body">
                @if($top_products->count() > 0)
                    <div class="space-y-4">
                        @foreach($top_products as $product)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-primary-600">#{{ $loop->iteration }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Sin categoría' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ $product->total_sold }} vendidos</p>
                                <p class="text-xs text-gray-500">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No hay datos de ventas</h4>
                        <p class="text-gray-500 mb-4">Los productos más vendidos aparecerán aquí</p>
                        @can('products.view')
                        <a href="{{ route('products.index') }}" class="btn-primary btn-sm">Ver productos</a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Desglose de Ganancias -->
<div id="profitModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 50; display: flex; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 12px; max-width: 1200px; width: 90%; max-height: 90vh; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 24px; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px;">📊 Análisis de Ganancias</h2>
                    <p style="font-size: 14px; opacity: 0.9;">Desglose detallado de cada ticket del día</p>
                </div>
                <button onclick="closeProfitModal()" style="background: rgba(255, 255, 255, 0.2); border: none; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Resumen General -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 20px;">
                <div style="background: rgba(255, 255, 255, 0.15); border-radius: 8px; padding: 16px;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Ventas Totales</div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($stats['daily_sales'], 2) }}</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.15); border-radius: 8px; padding: 16px;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Costos Totales</div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($stats['daily_cost'], 2) }}</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.15); border-radius: 8px; padding: 16px;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Ganancia Neta</div>
                    <div style="font-size: 24px; font-weight: bold;">${{ number_format($stats['daily_profit'], 2) }}</div>
                    <div style="font-size: 12px; margin-top: 4px;">{{ number_format($stats['profit_margin'], 1) }}% margen</div>
                </div>
            </div>
        </div>
        
        <!-- Body -->
        <div style="padding: 24px; overflow-y: auto; max-height: calc(90vh - 250px);">
            @if($profitOrders->count() > 0)
                <div style="display: grid; gap: 16px;">
                    @foreach($profitOrders as $order)
                    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.boxShadow='none'" onclick="toggleOrderDetails({{ $order->id }})">
                        <!-- Cabecera del Ticket -->
                        <div style="background: #f9fafb; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="background: #3b82f6; color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">
                                    #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 16px; color: #111827;">
                                        Pedido #{{ str_pad($order->daily_number, 2, '0', STR_PAD_LEFT) }}
                                        @if($order->table)
                                            - Mesa {{ $order->table->name }}
                                        @endif
                                    </div>
                                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                        @if($order->customer)
                                            • {{ $order->customer->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div style="text-align: right;">
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; text-align: center;">
                                    <div>
                                        <div style="font-size: 11px; color: #6b7280; margin-bottom: 2px;">Venta</div>
                                        <div style="font-weight: 700; font-size: 16px; color: #111827;">${{ number_format($order->total_amount, 2) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 11px; color: #6b7280; margin-bottom: 2px;">Costo</div>
                                        <div style="font-weight: 700; font-size: 16px; color: #ef4444;">${{ number_format($order->total_cost, 2) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 11px; color: #6b7280; margin-bottom: 2px;">Ganancia</div>
                                        <div style="font-weight: 700; font-size: 16px; color: {{ $order->profit >= 0 ? '#10b981' : '#ef4444' }};">
                                            ${{ number_format($order->profit, 2) }}
                                        </div>
                                        <div style="font-size: 10px; color: {{ $order->profit_margin >= 50 ? '#10b981' : ($order->profit_margin >= 30 ? '#f59e0b' : '#ef4444') }}; margin-top: 2px;">
                                            {{ number_format($order->profit_margin, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detalle de Items (colapsable) -->
                        <div id="order-details-{{ $order->id }}" style="display: none; padding: 16px; background: white;">
                            <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 12px;">Productos del pedido:</div>
                            <table style="width: 100%; font-size: 12px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e5e7eb;">
                                        <th style="text-align: left; padding: 8px; color: #6b7280; font-weight: 600;">Producto</th>
                                        <th style="text-align: center; padding: 8px; color: #6b7280; font-weight: 600;">Cant.</th>
                                        <th style="text-align: right; padding: 8px; color: #6b7280; font-weight: 600;">P. Unit.</th>
                                        <th style="text-align: right; padding: 8px; color: #6b7280; font-weight: 600;">Costo Unit.</th>
                                        <th style="text-align: right; padding: 8px; color: #6b7280; font-weight: 600;">Subtotal</th>
                                        <th style="text-align: right; padding: 8px; color: #6b7280; font-weight: 600;">Ganancia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items->whereNull('parent_id') as $item)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px 8px; font-weight: 500;">
                                            {{ $item->product->name ?? 'Producto eliminado' }}
                                            @if($item->children->count() > 0)
                                                <div style="margin-top: 4px; font-size: 11px; color: #6b7280;">
                                                    @foreach($item->children as $child)
                                                        <div style="margin-left: 16px;">+ {{ $child->product->name ?? 'N/A' }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 8px; text-align: center;">{{ $item->quantity }}</td>
                                        <td style="padding: 12px 8px; text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                                        <td style="padding: 12px 8px; text-align: right; color: #ef4444;">${{ number_format($item->product->cost ?? 0, 2) }}</td>
                                        <td style="padding: 12px 8px; text-align: right; font-weight: 600;">
                                            ${{ number_format($item->total_price + $item->children->sum('total_price'), 2) }}
                                        </td>
                                        <td style="padding: 12px 8px; text-align: right; font-weight: 600; color: #10b981;">
                                            @php
                                                $itemRevenue = $item->total_price + $item->children->sum('total_price');
                                                $itemCost = ($item->product->cost ?? 0) * $item->quantity;
                                                foreach ($item->children as $child) {
                                                    $itemCost += ($child->product->cost ?? 0) * $child->quantity;
                                                }
                                                $itemProfit = $itemRevenue - $itemCost;
                                            @endphp
                                            ${{ number_format($itemProfit, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 48px; color: #6b7280;">
                    <svg style="width: 64px; height: 64px; margin: 0 auto 16px; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h4 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px;">No hay ventas hoy</h4>
                    <p style="color: #6b7280;">Los tickets del día aparecerán aquí con su análisis de ganancia</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function openProfitModal() {
    document.getElementById('profitModal').style.display = 'flex';
}

function closeProfitModal() {
    document.getElementById('profitModal').style.display = 'none';
}

function toggleOrderDetails(orderId) {
    const detailsEl = document.getElementById('order-details-' + orderId);
    if (detailsEl.style.display === 'none') {
        detailsEl.style.display = 'block';
    } else {
        detailsEl.style.display = 'none';
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('profitModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeProfitModal();
    }
});
</script>
@endsection