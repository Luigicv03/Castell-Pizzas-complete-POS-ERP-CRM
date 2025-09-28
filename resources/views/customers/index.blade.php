@extends('layouts.app')

@section('title', 'Gestión de Clientes')
@section('subtitle', 'Administra la base de datos de clientes')

@section('content')
<div class="space-y-6">
    <!-- Header con acciones -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Clientes</h1>
            <p class="text-gray-600">Administra la información de tus clientes</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <button class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Cliente
            </button>
        </div>
    </div>

    <!-- Estadísticas de clientes -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                            <dd class="text-2xl font-bold text-gray-900">{{ $customers->count() }}</dd>
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
                            <dd class="text-2xl font-bold text-gray-900">{{ $customers->where('created_at', '>=', now()->subDays(30))->count() }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Clientes VIP</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $customers->where('total_spent', '>=', 500)->count() }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Con Órdenes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $customers->where('orders_count', '>', 0)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Buscar cliente</label>
                    <input type="text" class="form-input" placeholder="Nombre, email o teléfono...">
                </div>
                <div>
                    <label class="form-label">Tipo de cliente</label>
                    <select class="form-select">
                        <option value="">Todos</option>
                        <option value="vip">VIP</option>
                        <option value="frequent">Frecuentes</option>
                        <option value="new">Nuevos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Rango de fechas</label>
                    <select class="form-select">
                        <option value="">Todos</option>
                        <option value="today">Hoy</option>
                        <option value="week">Esta semana</option>
                        <option value="month">Este mes</option>
                        <option value="year">Este año</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select">
                        <option value="name">Nombre</option>
                        <option value="created_at">Fecha de registro</option>
                        <option value="total_spent">Total gastado</option>
                        <option value="orders_count">Número de órdenes</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Lista de Clientes</h3>
        </div>
        <div class="card-body">
            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Total Gastado</th>
                                <th>Órdenes</th>
                                <th>Última Visita</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr($customer->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $customer->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->phone ?? 'Sin teléfono' }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-900">${{ number_format($customer->total_spent ?? 0, 2) }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $customer->orders_count ?? 0 }} órdenes</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $customer->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    @if(($customer->total_spent ?? 0) >= 500)
                                        <span class="badge badge-warning">VIP</span>
                                    @elseif(($customer->orders_count ?? 0) >= 10)
                                        <span class="badge badge-info">Frecuente</span>
                                    @elseif($customer->created_at->isAfter(now()->subDays(30)))
                                        <span class="badge badge-success">Nuevo</span>
                                    @else
                                        <span class="badge badge-secondary">Regular</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center space-x-2">
                                        <button class="btn-sm btn-secondary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn-sm btn-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn-sm btn-danger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay clientes</h3>
                    <p class="text-gray-500 mb-4">Comienza agregando clientes a tu base de datos</p>
                    <button class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar Primer Cliente
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
