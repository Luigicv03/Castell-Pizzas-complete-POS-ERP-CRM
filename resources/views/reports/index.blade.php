@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reportes</h1>
            <p class="mt-1 text-gray-600">Análisis y estadísticas del negocio</p>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <a href="{{ route('reports.sales') }}" class="group">
            <div class="card hover:shadow-lg transition-shadow duration-200">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-700">Reporte de Ventas</h3>
                            <p class="text-sm text-gray-500">Análisis de ventas diarias</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.deliveries') }}" class="group">
            <div class="card hover:shadow-lg transition-shadow duration-200">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-cyan-700">Reporte de Deliverys</h3>
                            <p class="text-sm text-gray-500">Análisis de deliverys diarios</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.products') }}" class="group">
            <div class="card hover:shadow-lg transition-shadow duration-200">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">Reporte de Productos</h3>
                            <p class="text-sm text-gray-500">Productos más vendidos</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.customers') }}" class="group">
            <div class="card hover:shadow-lg transition-shadow duration-200">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-700">Reporte de Clientes</h3>
                            <p class="text-sm text-gray-500">Clientes más activos</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.inventory') }}" class="group">
            <div class="card hover:shadow-lg transition-shadow duration-200">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-700">Reporte de Inventario</h3>
                            <p class="text-sm text-gray-500">Estado del inventario</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection