<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema Pizzería') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Alpine.js x-cloak CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false, userMenuOpen: false }">
    <div id="app" class="h-full">
        @auth
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 lg:hidden"
             @click="sidebarOpen = false">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 shadow-xl transform transition-transform duration-300 ease-in-out"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
             x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Logo Section -->
            <div class="flex items-center justify-between h-16 bg-primary-600 border-b border-primary-700 px-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                        <span class="text-xl">🍕</span>
                    </div>
                    <h1 class="text-xl font-bold text-white">Pizzería</h1>
                </div>
                <!-- Close button -->
                <button @click="sidebarOpen = false" 
                        class="p-1 rounded-md text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <!-- Dashboard -->
                               <a href="{{ route('dashboard') }}" 
                                  class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                                   </svg>
                                   Dashboard
                               </a>

                    @can('pos.view')
                    <!-- POS -->
                    <a href="{{ route('pos.index') }}" 
                       class="sidebar-link {{ request()->routeIs('pos.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        Sistema POS
                    </a>
                    @endcan

                    @can('tables.view')
                    <!-- Mesas -->
                    <a href="{{ route('tables.index') }}" 
                       class="sidebar-link {{ request()->routeIs('tables.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Gestión de Mesas
                    </a>
                    @endcan

                    @can('products.view')
                    <!-- Productos -->
                    <a href="{{ route('products.index') }}" 
                       class="sidebar-link {{ request()->routeIs('products.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Productos
                    </a>
                    @endcan

                    @can('customers.view')
                    <!-- Clientes -->
                    <a href="{{ route('customers.index') }}" 
                       class="sidebar-link {{ request()->routeIs('customers.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Clientes
                    </a>
                    @endcan

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-3"></div>

                    @can('ingredients.view')
                    <!-- Inventario -->
                    <a href="{{ route('ingredients.index') }}" 
                       class="sidebar-link {{ request()->routeIs('ingredients.*') || request()->routeIs('suppliers.*') || request()->routeIs('recipes.*') || request()->routeIs('inventory-transactions.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Inventario
                    </a>
                    @endcan

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-3"></div>

                    @can('reports.view')
                    <!-- Reportes -->
                    <a href="{{ route('reports.index') }}" 
                       class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reportes
                    </a>
                    @endcan

                    @can('crm.view')
                    <!-- CRM -->
                    <a href="{{ route('crm.index') }}" 
                       class="sidebar-link {{ request()->routeIs('crm.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                   <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a3 3 0 015.356-1.857M17 20v-9a2 2 0 00-2-2H7a2 2 0 00-2 2v9m4-11h2m-2 11h2m-1-9v8m-1-8v4m-1-4h4m-1-4v4m-1-4h4"></path>
                        </svg>
                        CRM
                    </a>
                    @endcan
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64 flex flex-col h-full">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <!-- Sidebar toggle button -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition-colors duration-200"
                                :class="sidebarOpen ? 'bg-primary-100 text-primary-600' : ''">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <!-- Page title -->
                        <div class="ml-4 lg:ml-0">
                            <h1 class="text-xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                            <p class="text-sm text-gray-500 hidden sm:block">@yield('subtitle', 'Sistema de gestión de pizzería')</p>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828z"></path>
                            </svg>
                        </button>

                           <!-- User menu -->
                           <div class="relative" x-data="{ open: false }">
                               <button @click="open = !open" 
                                       class="flex items-center space-x-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                   <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                       <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                   </div>
                                   <div class="hidden sm:block text-left">
                                       <div class="text-sm font-medium text-gray-900 truncate max-w-32">{{ auth()->user()->name }}</div>
                                   </div>
                                   <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                   </svg>
                               </button>

                               <div x-show="open" 
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-200">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Mi Perfil
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Configuración
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-4 sm:py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-6">
                                <div class="alert alert-success">
                                    <div class="flex">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-6">
                                <div class="alert alert-danger">
                                    <div class="flex">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="mb-6">
                                <div class="alert alert-warning">
                                    <div class="flex">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        {{ session('warning') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-6">
                                <div class="alert alert-info">
                                    <div class="flex">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ session('info') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
        @else
        <!-- Public Layout -->
        <div class="min-h-full">
            <!-- Public Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                                    <span class="text-xl text-white">🍕</span>
                                </div>
                                <h1 class="text-xl font-bold text-gray-900">Sistema Pizzería</h1>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">Iniciar Sesión</a>
                            <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Public Content -->
            <main class="min-h-screen">
                @yield('content')
            </main>
        </div>
        @endauth
    </div>

    @livewireScripts
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // FORZAR TAMAÑO DE ICONOS DESPUÉS DE CARGAR
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarIcons = document.querySelectorAll('.sidebar-icon, .sidebar-link svg');
            sidebarIcons.forEach(icon => {
                icon.style.width = '14px';
                icon.style.height = '14px';
                icon.style.maxWidth = '14px';
                icon.style.maxHeight = '14px';
                icon.style.minWidth = '14px';
                icon.style.minHeight = '14px';
            });
        });

        // También forzar después de Alpine.js
        document.addEventListener('alpine:init', function() {
            setTimeout(() => {
                const sidebarIcons = document.querySelectorAll('.sidebar-icon, .sidebar-link svg');
                sidebarIcons.forEach(icon => {
                    icon.style.width = '14px';
                    icon.style.height = '14px';
                    icon.style.maxWidth = '14px';
                    icon.style.maxHeight = '14px';
                    icon.style.minWidth = '14px';
                    icon.style.minHeight = '14px';
                });
            }, 100);
        });
    </script>
</body>
</html>