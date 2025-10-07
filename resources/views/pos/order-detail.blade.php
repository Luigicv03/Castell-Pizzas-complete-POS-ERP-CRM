@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT))

@section('content')
<style>
/* ============================================
   LAYOUT PRINCIPAL - Responsivo y Optimizado
   ============================================ */
.pos-grid {
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
    gap: 12px;
    min-height: 100vh;
    padding: 12px;
    background: #f8f9fa;
}

/* Tablet y Desktop: Sidebar + Contenido */
@media (min-width: 768px) {
    .pos-grid {
        grid-template-columns: 380px 1fr;
        padding: 16px;
        gap: 16px;
    }
}

@media (min-width: 1200px) {
    .pos-grid {
        grid-template-columns: 420px 1fr;
        padding: 20px;
        gap: 20px;
    }
}

/* ============================================
   HEADER - Compacto y Adaptable
   ============================================ */
.pos-header {
    grid-column: 1 / -1;
    background: white;
    border-radius: 10px;
    padding: 12px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    border: 1px solid #e9ecef;
}

.pos-header h5 {
    font-size: 16px;
    margin: 0;
}

.pos-header small {
    font-size: 12px;
}

@media (min-width: 768px) {
    .pos-header {
        padding: 16px 20px;
    }
    
    .pos-header h5 {
        font-size: 18px;
    }
    
    .pos-header small {
        font-size: 13px;
    }
}

/* ============================================
   COLUMNA DE PEDIDO ACTUAL - Sidebar
   ============================================ */
.pos-info {
    background: white;
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: calc(100vh - 140px);
    min-height: 0;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

@media (min-width: 768px) {
    .pos-info {
        padding: 16px;
        gap: 14px;
        height: calc(100vh - 140px); /* Altura fija en desktop */
    }
}

@media (min-width: 1200px) {
    .pos-info {
        padding: 14px;
        gap: 10px; /* Gaps más pequeños en desktop */
    }
}

@media (max-width: 767px) {
    .pos-info {
        max-height: 500px;
        order: 2;
        padding: 14px;
        gap: 14px;
    }
}

/* ============================================
   COLUMNA DE PRODUCTOS - Contenido Principal
   ============================================ */
.pos-menu {
    background: white;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 16px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    max-height: calc(100vh - 140px);
}

@media (min-width: 768px) {
    .pos-menu {
        padding: 20px;
        gap: 20px;
    }
}

@media (max-width: 767px) {
    .pos-menu {
        order: 1;
        max-height: calc(100vh - 600px);
        min-height: 400px;
    }
}

/* ============================================
   GRID DE PRODUCTOS - Adaptable por Pantalla
   ============================================ */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 12px;
    overflow-y: auto;
    flex: 1;
    min-height: 0; /* Importante para que funcione el flex con overflow */
}

/* Más columnas en tablets */
@media (min-width: 768px) and (max-width: 1199px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }
}

/* Más columnas y cards más grandes en desktop */
@media (min-width: 1200px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
    }
}

/* ============================================
   TARJETAS DE PRODUCTOS - Más Atractivas
   ============================================ */
.product-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 10px;
    padding: 14px;
    text-align: center;
    border: 2px solid #e9ecef;
    transition: all 0.25s ease;
    min-height: 130px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.03) 0%, rgba(13, 110, 253, 0) 100%);
    opacity: 0;
    transition: opacity 0.25s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    border-color: #0d6efd;
}

.product-card:hover::before {
    opacity: 1;
}

.product-card:active {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

@media (min-width: 768px) {
    .product-card {
        min-height: 140px;
        padding: 16px;
    }
}

/* ============================================
   ITEMS DEL PEDIDO - Organizados y Limpios
   ============================================ */
.cart-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 8px 10px;
    margin-bottom: 8px;
    border: 1px solid #dee2e6;
    transition: box-shadow 0.2s;
}

.cart-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

@media (min-width: 768px) {
    .cart-item {
        padding: 10px 12px;
        margin-bottom: 10px;
    }
}

@media (min-width: 1200px) {
    .cart-item {
        padding: 8px 10px; /* Más compacto en desktop */
        margin-bottom: 6px;
    }
}

/* ============================================
   BOTONES - Compactos y Adaptables
   ============================================ */
.btn-compact {
    padding: 5px 8px;
    font-size: 11px;
    white-space: nowrap;
    border-radius: 5px;
    font-weight: 500;
}

@media (min-width: 768px) {
    .btn-compact {
        padding: 6px 10px;
        font-size: 12px;
    }
}

@media (min-width: 1200px) {
    .btn-compact {
        padding: 5px 9px;
        font-size: 11px;
    }
}

.btn-mini {
    width: 24px;
    height: 24px;
    padding: 0;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    font-weight: 600;
    line-height: 1;
    transition: all 0.2s;
}

.btn-mini:hover {
    transform: scale(1.08);
}

@media (min-width: 1200px) {
    .btn-mini {
        width: 22px;
        height: 22px;
        font-size: 12px;
    }
}

/* Botones de acción especiales (emoji) */
.btn-emoji {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid;
    transition: all 0.2s;
    flex-shrink: 0;
}

.btn-emoji:hover {
    transform: scale(1.12) rotate(5deg);
}

@media (min-width: 1200px) {
    .btn-emoji {
        width: 26px;
        height: 26px;
        font-size: 13px;
        border-width: 1.5px;
    }
}

/* ============================================
   SCROLL PERSONALIZADO
   ============================================ */
.pos-info::-webkit-scrollbar,
.pos-menu::-webkit-scrollbar,
.products-grid::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.pos-info::-webkit-scrollbar-track,
.pos-menu::-webkit-scrollbar-track,
.products-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.pos-info::-webkit-scrollbar-thumb,
.pos-menu::-webkit-scrollbar-thumb,
.products-grid::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.pos-info::-webkit-scrollbar-thumb:hover,
.pos-menu::-webkit-scrollbar-thumb:hover,
.products-grid::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* ============================================
   BOTONES DE CATEGORÍA - Más Compactos
   ============================================ */
.category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

@media (min-width: 768px) {
    .category-filters {
        gap: 8px;
    }
}

/* ============================================
   SECCIÓN DE CONTROLES DE ITEM
   ============================================ */
.item-controls {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

@media (min-width: 768px) {
    .item-controls {
        gap: 8px;
        flex-wrap: nowrap;
    }
}

/* ============================================
   BOTONES DE ACCIÓN DEL PEDIDO
   ============================================ */
.action-buttons {
    display: grid;
    gap: 10px;
}

@media (min-width: 768px) {
    .action-buttons {
        gap: 12px;
    }
}

/* ============================================
   UTILIDADES RESPONSIVAS
   ============================================ */
.text-responsive {
    font-size: 12px;
}

@media (min-width: 768px) {
    .text-responsive {
        font-size: 13px;
    }
}

@media (min-width: 1200px) {
    .text-responsive {
        font-size: 12px; /* Más pequeño en desktop para ahorrar espacio */
    }
}

.heading-responsive {
    font-size: 13px;
}

@media (min-width: 768px) {
    .heading-responsive {
        font-size: 15px;
    }
}

@media (min-width: 1200px) {
    .heading-responsive {
        font-size: 14px; /* Más compacto en desktop */
    }
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
                        Mesa {{ $order->table->name }} • {{ $order->customer ? $order->customer->name : ($order->customer_name ?: 'Cliente General') }}
                    @else
                        {{ $order->getTypeText() }} • {{ $order->customer ? $order->customer->name : ($order->customer_name ?: 'Cliente General') }}
                    @endif
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ $order->getStatusText() }}</span>
            <button @click="printKitchenOrder()" class="btn btn-warning btn-compact">
                <i class="fas fa-print"></i> Cocina
            </button>
            <button @click="printBarOrder()" class="btn btn-success btn-compact">
                <i class="fas fa-print"></i> Caja
            </button>
        </div>
    </div>

    <!-- Pedido Actual -->
    <div class="pos-info">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 heading-responsive">Pedido Actual</h6>
            <small class="text-muted text-responsive">
                @if($order->table)
                    Mesa: {{ $order->table->name }}
                @else
                    {{ $order->getTypeText() }}
                @endif
            </small>
        </div>
        
        <!-- Lista de Productos del Pedido -->
        <div style="flex: 1; overflow-y: auto; min-height: 0; overflow-x: hidden;">
            @if($order->items->count() > 0)
                @foreach($order->items->where('parent_id', null) as $item)
                <div class="cart-item">
                    <!-- Header: Nombre del producto y botones en la misma línea horizontal -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 6px;">
                        <h6 style="margin: 0; font-weight: bold; font-size: 12px; line-height: 1.2; flex: 1; min-width: 0;">{{ $item->product->name }}</h6>
                        <!-- Botones agrupados a la derecha en línea horizontal -->
                        <div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
                            <!-- Botón de nota -->
                            <button @click="openItemNotesModal({{ $item->id }}, '{{ addslashes($item->product->name) }}', '{{ addslashes($item->notes ?? '') }}')" 
                                    :style="'background: ' + (itemNotes[{{ $item->id }}] ? '#3b82f6' : '#d1d5db') + '; color: white; border: none; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer;'"
                                    title="Agregar nota para cocina">
                                ?
                            </button>
                            <!-- Botón eliminar -->
                            <button @click="removeItem({{ $item->id }})" 
                                    style="background: transparent; border: none; color: #dc3545; font-size: 18px; padding: 0; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; line-height: 1; cursor: pointer;">
                                ×
                            </button>
                        </div>
                    </div>
                    
                    <!-- Mostrar nota si existe -->
                    @if($item->notes && !str_contains($item->notes, 'Ingredientes base:'))
                    <div class="mt-1 p-2" style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 4px;">
                        <p class="mb-0" style="font-size: 11px; color: #92400e;">
                            <strong>📝 Nota:</strong> {{ $item->notes }}
                        </p>
                    </div>
                    @endif
                    
                    <!-- Controles: Cantidad, Botones de Acción, Precio -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                        <!-- Controles de cantidad -->
                        <div class="item-controls">
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="btn btn-mini btn-light border">
                                −
                            </button>
                            <span class="fw-bold text-responsive" style="min-width: 28px; text-align: center;">{{ $item->quantity }}</span>
                            <button @click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="btn btn-mini btn-light border">
                                +
                            </button>
                            
                            <!-- Botones de acción especiales -->
                            @php
                                $categoryName = strtolower($item->product->category->name ?? '');
                                $productName = strtolower($item->product->name ?? '');
                                $showIngredientsBtn = (str_contains($categoryName, 'pizza') || str_contains($categoryName, 'calzone') || str_contains($productName, 'calzone')) 
                                                      && !str_contains($productName, 'caja');
                                $showTeaContainerBtn = (str_contains($categoryName, 'té') || str_contains($categoryName, 'te naturales'))
                                                      && !str_contains($productName, 'envase');
                                $showCoffeeContainerBtn = str_contains($categoryName, 'café') && !str_contains($productName, 'envase');
                            @endphp
                            @if($showIngredientsBtn)
                            <button onclick="openIngredientsModal({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn-emoji"
                                    style="background: #ffc107; border-color: #ffc107; color: white;"
                                    title="Agregar ingredientes extras">
                                🍕
                            </button>
                            <button onclick="addBoxToPizza({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn-emoji"
                                    style="background: #795548; border-color: #795548; color: white;"
                                    title="Agregar caja">
                                📦
                            </button>
                            @endif
                            
                            @if($showTeaContainerBtn)
                            <button onclick="addContainerToTea({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn-emoji"
                                    style="background: #4CAF50; border-color: #4CAF50; color: white;"
                                    title="Agregar envase para té">
                                🥤
                            </button>
                            @endif
                            
                            @if($showCoffeeContainerBtn)
                            <button onclick="addContainerToCoffee({{ $item->id }}, '{{ $item->product->name }}')" 
                                    class="btn-emoji"
                                    style="background: #6F4E37; border-color: #6F4E37; color: white;"
                                    title="Agregar envase para café">
                                ☕
                            </button>
                            @endif
                        </div>
                        
                        <!-- Precio a la derecha -->
                        <span class="fw-bold text-primary" style="font-size: 15px;">${{ number_format($item->total_price, 2) }}</span>
                    </div>
                    
                    @php
                        $productName = strtolower($item->product->name);
                        $is4Estaciones = str_contains($productName, '4 estaciones');
                        $isMulticereal = str_contains($productName, 'multicereal');
                        $baseIngredients = [];
                        
                        // Extraer ingredientes base desde las notas
                        if ($item->notes && str_contains($item->notes, 'Ingredientes base:')) {
                            $notesText = str_replace('Ingredientes base:', '', $item->notes);
                            $baseIngredients = array_map('trim', explode(',', $notesText));
                        }
                    @endphp
                    
                    @if(count($baseIngredients) > 0)
                    <!-- Ingredientes base incluidos -->
                    <div class="mt-2 ps-3 border-start border-2 border-primary">
                        <small class="text-primary fw-bold">Ingredientes incluidos:</small>
                        @foreach($baseIngredients as $ingredient)
                        <div class="py-1">
                            <small class="text-muted">✓ {{ $ingredient }}</small>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    @if($item->children && $item->children->count() > 0)
                    <!-- Ingredientes extras -->
                    <div class="mt-2 ps-3 border-start border-2 border-warning">
                        @foreach($item->children as $child)
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <small class="text-muted">+ {{ $child->product->name }}</small>
                            <div class="d-flex align-items-center gap-1">
                                <small class="fw-bold text-success">${{ number_format($child->total_price, 2) }}</small>
                                <button @click="removeItem({{ $child->id }})" 
                                        class="btn btn-mini"
                                        style="background: transparent; border: none; color: #dc3545; font-size: 14px; padding: 0; width: auto; height: auto;">
                                    ×
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <p>Sin productos</p>
                </div>
            @endif
        </div>

        <!-- Tipo de Pedido y Cliente - Compacto -->
        <div style="background: #f8f9fa; padding: 8px 10px; border-radius: 6px; border: 1px solid #e9ecef;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-utensils text-primary" style="font-size: 12px;"></i>
                    <span class="fw-bold text-responsive">{{ $order->getTypeText() }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-user text-muted" style="font-size: 11px;"></i>
                <span class="text-responsive text-muted" style="font-size: 11px;">
                    {{ Str::limit($order->customer ? $order->customer->name : ($order->customer_name ?: 'Cliente general'), 25) }}
                    @if(!$order->customer && $order->customer_name)
                        <small class="text-warning">(Temp)</small>
                    @endif
                </span>
            </div>
        </div>

        <!-- Resumen de Totales -->
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 10px 12px; border-radius: 8px; border: 2px solid #dee2e6;">
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Subtotal:</span>
                <span class="fw-bold">${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->type === 'delivery' && $order->delivery_cost > 0)
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Delivery:</span>
                <span class="fw-bold text-info">${{ number_format($order->delivery_cost, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between pt-1 border-top">
                <span class="fw-bold" style="font-size: 13px;">Total:</span>
                <span class="fw-bold text-primary" style="font-size: 18px;">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="action-buttons" style="gap: 6px;">
            <button @click="updateOrderStatus('preparing')" 
                    :disabled="orderStatus === 'preparing'"
                    :class="orderStatus === 'preparing' ? 'btn-outline-primary' : 'btn-primary'"
                    class="btn w-100"
                    style="padding: 8px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                <i class="fas fa-clock me-1" style="font-size: 11px;"></i>
                <span x-text="orderStatus === 'preparing' ? '✓ En Preparación' : 'En Preparación'"></span>
            </button>
            <button @click="updateOrderStatus('ready')" 
                    :disabled="orderStatus === 'ready'"
                    :class="orderStatus === 'ready' ? 'btn-outline-success' : 'btn-success'"
                    class="btn w-100"
                    style="padding: 8px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                <i class="fas fa-check me-1" style="font-size: 11px;"></i>
                <span x-text="orderStatus === 'ready' ? '✓ Listo' : 'Listo'"></span>
            </button>
            <button @click="openPaymentModal()" 
                    class="btn btn-warning w-100"
                    style="padding: 8px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                <i class="fas fa-credit-card me-1" style="font-size: 11px;"></i> Procesar Pago
            </button>
            <button @click="updateOrderStatus('cancelled')" 
                    :disabled="orderStatus === 'cancelled'"
                    class="btn btn-danger w-100"
                    style="padding: 8px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;"
                    onclick="return confirm('¿Estás seguro de cancelar este pedido?')">
                <i class="fas fa-times me-1" style="font-size: 11px;"></i> Cancelar Pedido
            </button>
        </div>
    </div>

    <!-- Productos -->
    <div class="pos-menu">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0 heading-responsive">Productos</h6>
            <small class="text-muted text-responsive d-none d-md-block">Selecciona para agregar</small>
        </div>
        
        <!-- Categorías como botones - Filtro rápido -->
        <div class="category-filters">
            <button @click="selectedCategory = null" 
                    :class="selectedCategory === null ? 'btn btn-primary btn-compact' : 'btn btn-outline-secondary btn-compact'"
                    style="flex-shrink: 0;">
                Todas
            </button>
            @foreach($categories as $category)
            <button @click="selectedCategory = {{ $category->id }}" 
                    :class="selectedCategory === {{ $category->id }} ? 'btn btn-primary btn-compact' : 'btn btn-outline-secondary btn-compact'"
                    style="flex-shrink: 0;">
                {{ Str::limit($category->name, 15) }}
            </button>
            @endforeach
        </div>

        <!-- Grid de Productos con altura limitada -->
        <div style="flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div class="products-grid" style="border: 1px solid #e9ecef; border-radius: 10px; padding: 12px; background: #fafbfc;">
                @foreach($products as $product)
            <button @click="addProduct({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})" 
                    x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}" 
                    class="product-card">
                <div style="position: relative; z-index: 1;">
                    <h6 class="fw-bold mb-2 text-responsive text-dark">
                        {{ Str::limit($product->name, 25) }}
                    </h6>
                    @if($product->description)
                    <p class="text-muted mb-2" style="font-size: 11px; line-height: 1.3;">
                        {{ Str::limit($product->description, 35) }}
                    </p>
                    @endif
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <span class="fw-bold text-primary" style="font-size: 17px;">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        @if($product->is_featured)
                        <span style="font-size: 12px;">⭐</span>
                        @endif
                    </div>
                </div>
            </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Notas para Items -->
    <div x-show="showItemNotesModal" 
         @click.away="closeItemNotesModal()"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        <div @click.stop 
             style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; margin: auto;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100">
            <div style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                    <div>
                        <h3 style="font-size: 20px; font-weight: bold; color: #212529; margin: 0 0 5px 0;">📝 Nota para Cocina</h3>
                        <p style="margin: 0; font-size: 14px; color: #6c757d;" x-text="currentItemName"></p>
                    </div>
                    <button @click="closeItemNotesModal()" 
                            style="background: transparent; border: none; color: #dc3545; font-size: 28px; line-height: 1; cursor: pointer; padding: 0; width: 30px; height: 30px;">
                        ×
                    </button>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #495057; margin-bottom: 8px;">
                        Instrucciones especiales:
                    </label>
                    <textarea x-model="currentItemNotes" 
                              rows="4" 
                              placeholder="Ej: Poca salsa, sin cebolla, bien cocida, etc..."
                              style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; resize: vertical;"
                              class="form-textarea"></textarea>
                    <p style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                        Esta nota aparecerá en la comanda de cocina
                    </p>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button @click="closeItemNotesModal()" class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button @click="saveItemNotes()" class="btn btn-primary">
                        Guardar Nota
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal (dentro del scope de Alpine) -->
    <div x-show="showPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" 
         style="z-index: 9999; padding: 20px; overflow-y: auto;">
        <div class="bg-white rounded-lg shadow-xl w-full mx-auto" 
             @click.away="showPaymentModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <div class="p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Procesar Pago</h3>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Total del Pedido:</span>
                        <span class="text-lg font-bold text-primary">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="font-medium">Pagado:</span>
                        <span class="text-lg font-bold text-success">$<span x-text="totalPaid.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center mt-2 border-t pt-2">
                        <span class="font-bold">Restante:</span>
                        <div class="text-right">
                            <span class="text-xl font-bold" :class="remainingAmount > 0 ? 'text-danger' : 'text-success'">
                                $<span x-text="remainingAmount.toFixed(2)"></span>
                            </span>
                            <div class="text-sm text-gray-600" x-show="remainingAmount > 0">
                                (<span x-text="parseFloat((remainingAmount * {{ $exchangeRate->usd_to_bsf }}).toFixed(2))"></span> BsF)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods List -->
                <div x-show="payments.length > 0" class="mb-3">
                    <h4 class="font-medium text-gray-900 mb-2 text-sm">Métodos de Pago Agregados:</h4>
                    <div class="space-y-2">
                        <template x-for="(payment, index) in payments" :key="index">
                            <div class="flex justify-between items-center bg-gray-100 p-2 rounded text-sm">
                                <div>
                                    <span class="font-medium" x-text="getPaymentMethodText(payment.method)"></span>
                                    <span x-show="payment.reference" class="text-xs text-gray-600 ml-2">
                                        (Ref: <span x-text="payment.reference"></span>)
                                    </span>
                                    <div class="text-xs text-gray-600" x-show="payment.currency === 'BsF'">
                                        <span x-text="payment.amountBsf.toFixed(2)"></span> BsF
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-right">
                                        <div class="font-bold">$<span x-text="payment.amount.toFixed(2)"></span></div>
                                        <div class="text-xs text-gray-600" x-show="payment.currency === 'USD'">USD</div>
                                    </div>
                                    <button @click="removePayment(index)" class="text-danger hover:text-danger-700 text-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Add New Payment Form -->
                <div x-show="remainingAmount > 0" class="border-t pt-3">
                    <h4 class="font-medium text-gray-900 mb-2 text-sm">Agregar Método de Pago</h4>
                    <form @submit.prevent="addPayment()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Método de Pago</label>
                                <select x-model="currentPayment.method" class="form-select" required>
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
                                <label class="form-label">
                                    Monto 
                                    <span x-show="['mobile_payment', 'pos', 'transfer'].includes(currentPayment.method)">
                                        (BsF)
                                    </span>
                                    <span x-show="!['mobile_payment', 'pos', 'transfer'].includes(currentPayment.method) || currentPayment.method === ''">
                                        (USD)
                                    </span>
                                </label>
                                <input type="number" x-model="currentPayment.amount" step="0.01" min="0.01" 
                                       :placeholder="['mobile_payment', 'pos', 'transfer'].includes(currentPayment.method) ? 
                                                     'Monto en Bolívares' : 'Monto en Dólares'"
                                       class="form-input" required>
                                <small class="text-xs text-gray-600" 
                                       x-show="['mobile_payment', 'pos', 'transfer'].includes(currentPayment.method) && currentPayment.amount > 0">
                                    ≈ $<span x-text="(currentPayment.amount / {{ $exchangeRate->usd_to_bsf }}).toFixed(2)"></span> USD
                                </small>
                            </div>
                            
                            <div x-show="currentPayment.method === 'transfer' || currentPayment.method === 'mobile_payment' || currentPayment.method === 'zelle'" 
                                 class="md:col-span-2">
                                <label class="form-label">Referencia</label>
                                <input type="text" x-model="currentPayment.reference" class="form-input" 
                                       placeholder="Número de referencia">
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Agregar Pago
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Customer Data -->
                <div class="border-t pt-3">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Datos del Cliente</h4>
                            
                            <!-- Customer Search/Add Toggle -->
                            <div class="mb-3">
                                <div class="flex gap-2">
                                    <button type="button" @click="customerMode = 'search'" 
                                            :class="customerMode === 'search' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1.5 rounded text-xs font-medium transition-colors flex-1">
                                        Buscar Existente
                                    </button>
                                    <button type="button" @click="customerMode = 'new'" 
                                            :class="customerMode === 'new' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1.5 rounded text-xs font-medium transition-colors flex-1">
                                        Cliente Nuevo
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
                            <div x-show="customerMode === 'new' || (customerMode === 'search' && selectedCustomer)" class="space-y-2">
                                <div>
                                    <label class="form-label text-xs">Nombre Completo</label>
                                    <input type="text" x-model="customerData.name" class="form-input form-input-sm" 
                                           placeholder="Nombre del cliente" required>
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Email</label>
                                    <input type="email" x-model="customerData.email" class="form-input form-input-sm" 
                                           placeholder="email@ejemplo.com">
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Teléfono</label>
                                    <input type="tel" x-model="customerData.phone" class="form-input form-input-sm" 
                                           placeholder="Número de teléfono">
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Cédula</label>
                                    <input type="text" x-model="customerData.cedula" 
                                           @input="checkCedulaExists()"
                                           class="form-input form-input-sm" 
                                           placeholder="Número de cédula">
                                    <div x-show="cedulaExists" class="text-xs text-orange-600 mt-1">
                                        ⚠️ Esta cédula ya está registrada.
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="form-label text-xs">Dirección</label>
                                    <textarea x-model="customerData.address" class="form-textarea form-input-sm" rows="2"
                                              placeholder="Dirección completa"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                        <button type="button" @click="showPaymentModal = false" class="btn btn-secondary btn-sm">
                            Cancelar
                        </button>
                        <button type="button" @click="processPayment()" 
                                :disabled="remainingAmount > 0 || payments.length === 0"
                                class="btn btn-primary btn-sm">
                            <i class="fas fa-credit-card me-1"></i> Procesar Pago
                        </button>
                    </div>
            </div>
        </div>
    </div>

</div><!-- Fin del componente Alpine orderDetailSystem -->

<!-- Modal de Ingredientes -->
<div id="ingredientsModal" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; margin: auto;">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #212529; margin: 0;">🍕 Agregar Ingredientes</h3>
                <button onclick="closeIngredientsModal()" 
                        style="background: transparent; border: none; color: #dc3545; font-size: 28px; line-height: 1; cursor: pointer; padding: 0; width: 30px; height: 30px;">
                    ×
                </button>
            </div>
            
            <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #495057; font-weight: 500;" id="pizzaNameDisplay"></p>
            </div>
            
            <div id="ingredientsList" style="display: flex; flex-direction: column; gap: 10px;">
                <!-- Ingredients will be loaded here -->
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <button onclick="closeIngredientsModal()" class="btn btn-secondary">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js CDN como respaldo -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Variables globales para modal de ingredientes
let currentPizzaItemId = null;
let currentPizzaName = '';
let availableIngredients = [];

// Abrir modal de ingredientes
async function openIngredientsModal(itemId, pizzaName) {
    currentPizzaItemId = itemId;
    currentPizzaName = pizzaName;
    
    document.getElementById('pizzaNameDisplay').textContent = `Ingredientes para: ${pizzaName}`;
    
    // Determinar el tamaño basado en el nombre del producto
    let size = 'Personal';
    
    // Si es Calzone, usar "Calzone" como tamaño
    if (pizzaName.toLowerCase().includes('calzone')) {
        size = 'Calzone';
    } else if (pizzaName.includes('Personal') || pizzaName.includes('25cm')) {
        size = 'Personal';
    } else if (pizzaName.includes('Mediana') || pizzaName.includes('33cm')) {
        size = 'Mediana';
    } else if (pizzaName.includes('Familiar') || pizzaName.includes('40cm')) {
        size = 'Familiar';
    }
    
    // Cargar ingredientes
    try {
        const response = await fetch(`/api/ingredients/by-size/${size}`);
        availableIngredients = await response.json();
        displayIngredients();
        const modal = document.getElementById('ingredientsModal');
        modal.style.display = 'flex'; // Cambiar a flex para que funcione el centrado
    } catch (error) {
        console.error('Error loading ingredients:', error);
        alert('Error al cargar los ingredientes');
    }
}

// Cerrar modal de ingredientes
function closeIngredientsModal() {
    document.getElementById('ingredientsModal').style.display = 'none';
    currentPizzaItemId = null;
    currentPizzaName = '';
    availableIngredients = [];
}

// Agregar caja a la pizza
async function addBoxToPizza(itemId, pizzaName) {
    // Determinar el tamaño de la caja basado en el nombre de la pizza
    let boxName = 'Caja Personal';
    
    if (pizzaName.toLowerCase().includes('personal') || pizzaName.includes('25cm')) {
        boxName = 'Caja Personal';
    } else if (pizzaName.toLowerCase().includes('mediana') || pizzaName.includes('33cm')) {
        boxName = 'Caja Mediana';
    } else if (pizzaName.toLowerCase().includes('familiar') || pizzaName.includes('40cm')) {
        boxName = 'Caja Familiar';
    }
    
    // Buscar el producto de la caja
    try {
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: boxName })
        });
        
        const box = await response.json();
        
        if (!box || !box.id) {
            alert('No se encontró la caja correspondiente');
            return;
        }
        
        // Agregar la caja como ingrediente
        const addResponse = await fetch(`/pos/{{ $order->id }}/item/${itemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: box.id,
                quantity: 1
            })
        });
        
        const result = await addResponse.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Error al agregar la caja');
        }
    } catch (error) {
        console.error('Error adding box:', error);
        alert('Error al agregar la caja');
    }
}

// Agregar envase para té
async function addContainerToTea(itemId, teaName) {
    const containerName = 'Envase para Té';
    
    // Buscar el producto del envase
    try {
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: containerName })
        });
        
        const container = await response.json();
        
        if (!container || !container.id) {
            alert('No se encontró el envase para té');
            return;
        }
        
        // Agregar el envase como ingrediente adicional
        const addResponse = await fetch(`/pos/{{ $order->id }}/item/${itemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: container.id,
                quantity: 1
            })
        });
        
        const result = await addResponse.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Error al agregar el envase');
        }
    } catch (error) {
        console.error('Error adding container:', error);
        alert('Error al agregar el envase');
    }
}

// Agregar envase para café
async function addContainerToCoffee(itemId, coffeeName) {
    const containerName = 'Envase para Café';
    
    // Buscar el producto del envase
    try {
        const response = await fetch('/api/products/search-by-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: containerName })
        });
        
        const container = await response.json();
        
        if (!container || !container.id) {
            alert('No se encontró el envase para café');
            return;
        }
        
        // Agregar el envase como ingrediente adicional
        const addResponse = await fetch(`/pos/{{ $order->id }}/item/${itemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: container.id,
                quantity: 1
            })
        });
        
        const result = await addResponse.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Error al agregar el envase');
        }
    } catch (error) {
        console.error('Error adding container:', error);
        alert('Error al agregar el envase');
    }
}

// Mostrar ingredientes en el modal
function displayIngredients() {
    const container = document.getElementById('ingredientsList');
    container.innerHTML = '';
    
    if (availableIngredients.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">No hay ingredientes disponibles para este tamaño</p>';
        return;
    }
    
    // Filtrar solo ingredientes simples (sin "Doble" en el nombre)
    const simpleIngredients = availableIngredients.filter(ing => !ing.name.includes('Doble'));
    
    // Agrupar por categoría
    const grouped = {};
    simpleIngredients.forEach(ingredient => {
        const categoryName = ingredient.category?.name || 'Otros';
        if (!grouped[categoryName]) {
            grouped[categoryName] = [];
        }
        grouped[categoryName].push(ingredient);
    });
    
    // Renderizar por categoría
    Object.keys(grouped).forEach(categoryName => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'mb-3';
        categoryDiv.innerHTML = `<h6 class="fw-bold text-muted mb-2" style="font-size: 12px;">${categoryName}</h6>`;
        
        grouped[categoryName].forEach(ingredient => {
            // Buscar la versión doble del ingrediente
            const doubleVersion = availableIngredients.find(ing => 
                ing.name === ingredient.name.replace(/\s+(Personal|Mediana|Familiar|Calzone)/, ' $1 Doble')
            );
            
            const ingredientDiv = document.createElement('div');
            ingredientDiv.className = 'd-flex justify-content-between align-items-center p-2 border rounded mb-2';
            ingredientDiv.style.transition = 'all 0.2s';
            ingredientDiv.innerHTML = `
                <div style="flex: 1;">
                    <div class="fw-bold" style="font-size: 13px;">${ingredient.name}</div>
                    <div class="text-muted" style="font-size: 11px;">Simple: $${parseFloat(ingredient.price).toFixed(2)}${doubleVersion ? ` | Doble: $${parseFloat(doubleVersion.price).toFixed(2)}` : ''}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button onclick="addIngredientToPizza(${ingredient.id}, false)" 
                            class="btn btn-sm btn-success" 
                            style="min-width: 45px; font-weight: bold;"
                            title="Agregar porción simple">
                        +
                    </button>
                    ${doubleVersion ? `
                    <button onclick="addIngredientToPizza(${doubleVersion.id}, true)" 
                            class="btn btn-sm btn-primary" 
                            style="min-width: 45px; font-weight: bold;"
                            title="Agregar porción doble">
                        ++
                    </button>
                    ` : ''}
                </div>
            `;
            categoryDiv.appendChild(ingredientDiv);
        });
        
        container.appendChild(categoryDiv);
    });
}

// Agregar ingrediente a la pizza
async function addIngredientToPizza(ingredientId) {
    try {
        const response = await fetch(`/pos/{{ $order->id }}/item/${currentPizzaItemId}/add-ingredient`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: ingredientId,
                quantity: 1
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeIngredientsModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error adding ingredient:', error);
        alert('Error al agregar el ingrediente');
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const paymentModal = document.getElementById('paymentModal');
    const ingredientsModal = document.getElementById('ingredientsModal');
    
    if (event.target === paymentModal) {
        paymentModal.style.display = 'none';
    }
    
    if (event.target === ingredientsModal) {
        ingredientsModal.style.display = 'none';
    }
}
</script>

<script>
function orderDetailSystem() {
    return {
        selectedCategory: null,
        orderStatus: '{{ $order->status }}',
        showPaymentModal: false,
        payments: [],
        currentPayment: {
            method: '',
            amount: 0,
            reference: ''
        },
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
        
        // Variables para notas de items
        showItemNotesModal: false,
        currentItemId: null,
        currentItemName: '',
        currentItemNotes: '',
        itemNotes: {},
        
        get totalPaid() {
            return this.payments.reduce((total, payment) => total + parseFloat(payment.amount), 0);
        },
        
        get remainingAmount() {
            return {{ $order->total_amount }} - this.totalPaid;
        },
        
        init() {
            this.currentPayment.amount = {{ $order->total_amount }};
            
            // Cargar notas existentes de los items
            @foreach($order->items->where('parent_id', null) as $item)
                @if($item->notes && !str_contains($item->notes, 'Ingredientes base:'))
                    this.itemNotes[{{ $item->id }}] = '{{ addslashes($item->notes) }}';
                @endif
            @endforeach
        },
        
        openItemNotesModal(itemId, itemName, itemNotes) {
            this.currentItemId = itemId;
            this.currentItemName = itemName;
            this.currentItemNotes = itemNotes || '';
            this.showItemNotesModal = true;
        },
        
        saveItemNotes() {
            if (!this.currentItemId) return;
            
            // Guardar la nota en el servidor
            fetch(`/pos/{{ $order->id }}/item/${this.currentItemId}/notes`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    notes: this.currentItemNotes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el objeto de notas localmente
                    if (this.currentItemNotes && this.currentItemNotes.trim() !== '') {
                        this.itemNotes[this.currentItemId] = this.currentItemNotes;
                    } else {
                        delete this.itemNotes[this.currentItemId];
                    }
                    this.closeItemNotesModal();
                    location.reload(); // Recargar para mostrar la nota
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la nota');
            });
        },
        
        closeItemNotesModal() {
            this.showItemNotesModal = false;
            this.currentItemId = null;
            this.currentItemName = '';
            this.currentItemNotes = '';
        },
        
        openPaymentModal() {
            console.log('Opening payment modal...');
            this.showPaymentModal = true;
            this.payments = [];
            this.currentPayment = {
                method: '',
                amount: {{ $order->total_amount }},
                reference: ''
            };
            console.log('Payment modal should be visible:', this.showPaymentModal);
        },
        
        addProduct(productId, productName, productPrice) {
            const nameLower = productName.toLowerCase();
            
            // Detectar pizzas especiales que requieren selección de ingredientes
            const is4Estaciones = nameLower.includes('4 estaciones');
            const isMulticereal = nameLower.includes('multicereal');
            
            // Si es pizza 4 Estaciones o Multicereal, abrir modal de ingredientes
            if (is4Estaciones) {
                openCustomPizzaModalDetail(productId, productName, productPrice, 4); // 4 ingredientes
                return;
            }
            
            if (isMulticereal) {
                openCustomPizzaModalDetail(productId, productName, productPrice, 2); // 2 ingredientes
                return;
            }
            
            // Para productos normales, agregar directamente
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
        
        addPayment() {
            if (!this.currentPayment.method || !this.currentPayment.amount) {
                alert('Por favor complete todos los campos');
                return;
            }
            
            // Determinar si el método de pago es en bolívares
            const isBsfMethod = ['mobile_payment', 'pos', 'transfer'].includes(this.currentPayment.method);
            
            // Convertir el monto a USD si es un método en bolívares
            const exchangeRate = {{ $exchangeRate->usd_to_bsf }};
            let amountInUsd = parseFloat(this.currentPayment.amount);
            let amountInBsf = null;
            
            if (isBsfMethod) {
                // El monto ingresado está en BsF, convertir a USD
                amountInBsf = parseFloat(this.currentPayment.amount);
                amountInUsd = parseFloat((amountInBsf / exchangeRate).toFixed(2)); // Redondear a 2 decimales
            }
            
            // Validar que el monto no sea mayor al restante (con margen de 0.01 por redondeo)
            if (amountInUsd > (this.remainingAmount + 0.01)) {
                alert('El monto no puede ser mayor al restante');
                return;
            }
            
            // Si el monto es casi igual al restante (diferencia menor a 0.01), ajustar al restante exacto
            if (Math.abs(amountInUsd - this.remainingAmount) < 0.01) {
                amountInUsd = this.remainingAmount;
            }
            
            // Agregar el pago al array
            this.payments.push({
                method: this.currentPayment.method,
                amount: amountInUsd, // Siempre guardar en USD
                amountBsf: amountInBsf, // Guardar el monto en BsF si aplica
                reference: this.currentPayment.reference,
                currency: isBsfMethod ? 'BsF' : 'USD'
            });
            
            // Resetear el formulario con el nuevo restante
            this.$nextTick(() => {
                this.currentPayment = {
                    method: '',
                    amount: this.remainingAmount > 0 ? this.remainingAmount : 0,
                    reference: ''
                };
            });
        },
        
        removePayment(index) {
            this.payments.splice(index, 1);
        },
        
        getPaymentMethodText(method) {
            const methods = {
                'cash': 'Efectivo',
                'mobile_payment': 'Pago Móvil',
                'zelle': 'Zelle',
                'binance': 'Binance',
                'pos': 'Punto de Venta',
                'transfer': 'Transferencia'
            };
            return methods[method] || method;
        },
        
        processPayment() {
            if (this.payments.length === 0) {
                alert('Debe agregar al menos un método de pago');
                return;
            }
            
            if (this.remainingAmount > 0) {
                alert('El monto total no ha sido completado. Restante: $' + this.remainingAmount.toFixed(2));
                return;
            }
            
            if (!this.customerData.name || !this.customerData.cedula) {
                alert('Debe completar los datos del cliente');
                return;
            }
            
            fetch(`/pos/{{ $order->id }}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payments: this.payments,
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
                const response = await fetch(`/api/customers/pos-search?q=${encodeURIComponent(this.customerSearch)}`);
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
                const response = await fetch(`/api/customers/pos-search?q=${encodeURIComponent(this.customerData.cedula)}`);
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
        }
    }
}

// Variables globales para modal de pizza personalizable en detalle
let customPizzaDetailData = {
    productId: null,
    name: '',
    price: 0,
    requiredIngredients: 0,
    selectedIngredients: []
};

// Abrir modal de pizza personalizable en vista detalle
function openCustomPizzaModalDetail(productId, name, price, requiredCount) {
    customPizzaDetailData = {
        productId: productId,
        name: name,
        price: price,
        requiredIngredients: requiredCount,
        selectedIngredients: []
    };
    
    document.getElementById('customPizzaTitleDetail').textContent = name;
    document.getElementById('customPizzaSubtitleDetail').textContent = `Selecciona ${requiredCount} ingredientes incluidos en el precio`;
    document.getElementById('selectedCountDisplayDetail').textContent = `0/${requiredCount}`;
    
    loadIngredientsForCustomPizzaDetail(name);
    document.getElementById('customPizzaModalDetail').style.display = 'flex';
}

// Cargar ingredientes filtrados por tamaño
function loadIngredientsForCustomPizzaDetail(pizzaName) {
    const allIngredients = @json($ingredients ?? []);
    console.log('Todos los ingredientes:', allIngredients);
    
    // Determinar tamaño de la pizza
    let sizeKeyword = '';
    if (pizzaName.toLowerCase().includes('personal') || pizzaName.toLowerCase().includes('25cm')) {
        sizeKeyword = 'Personal';
    } else if (pizzaName.toLowerCase().includes('mediana') || pizzaName.toLowerCase().includes('33cm')) {
        sizeKeyword = 'Mediana';
    } else if (pizzaName.toLowerCase().includes('familiar') || pizzaName.toLowerCase().includes('40cm')) {
        sizeKeyword = 'Familiar';
    }
    
    console.log('Tamaño detectado:', sizeKeyword);
    
    // Filtrar ingredientes por tamaño y excluir "Doble"
    const filteredIngredients = allIngredients.filter(ingredient => {
        const nameLower = ingredient.name.toLowerCase();
        return !nameLower.includes('doble') && (sizeKeyword ? nameLower.includes(sizeKeyword.toLowerCase()) : true);
    });
    
    const container = document.getElementById('customPizzaIngredientsListDetail');
    container.innerHTML = '';
    
    if (!filteredIngredients || filteredIngredients.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">No se encontraron ingredientes disponibles.</p>';
        return;
    }
    
    // Agrupar por nombre base (sin sufijos de tamaño)
    const ingredientsMap = new Map();
    filteredIngredients.forEach(ingredient => {
        let baseName = ingredient.name
            .replace(/\s+(Personal|Mediana|Familiar|25cm|33cm|40cm)/gi, '')
            .trim();
        
        if (!ingredientsMap.has(baseName)) {
            ingredientsMap.set(baseName, {
                id: ingredient.id,
                name: baseName,
                fullName: ingredient.name
            });
        }
    });
    
    const uniqueIngredients = Array.from(ingredientsMap.values());
    console.log('Ingredientes únicos:', uniqueIngredients);
    
    uniqueIngredients.forEach(ingredient => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'custom-ingredient-btn';
        button.innerHTML = `
            <span class="ingredient-icon">🍕</span>
            <span class="ingredient-name">${ingredient.name}</span>
            <span class="ingredient-check">✓</span>
        `;
        button.onclick = () => toggleCustomIngredientDetail(ingredient.id, ingredient.name, button);
        container.appendChild(button);
    });
}

// Alternar selección de ingrediente en detalle
function toggleCustomIngredientDetail(ingredientId, ingredientName, buttonElement) {
    const index = customPizzaDetailData.selectedIngredients.findIndex(i => i.id === ingredientId);
    
    if (index > -1) {
        customPizzaDetailData.selectedIngredients.splice(index, 1);
        buttonElement.classList.remove('selected');
    } else {
        if (customPizzaDetailData.selectedIngredients.length >= customPizzaDetailData.requiredIngredients) {
            alert(`Solo puedes seleccionar ${customPizzaDetailData.requiredIngredients} ingredientes`);
            return;
        }
        customPizzaDetailData.selectedIngredients.push({ id: ingredientId, name: ingredientName });
        buttonElement.classList.add('selected');
    }
    
    const count = customPizzaDetailData.selectedIngredients.length;
    document.getElementById('selectedCountDisplayDetail').textContent = `${count}/${customPizzaDetailData.requiredIngredients}`;
    
    const confirmBtn = document.getElementById('confirmCustomPizzaBtnDetail');
    if (count === customPizzaDetailData.requiredIngredients) {
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = '1';
        confirmBtn.style.cursor = 'pointer';
    } else {
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.5';
        confirmBtn.style.cursor = 'not-allowed';
    }
}

// Confirmar pizza personalizable en detalle
function confirmCustomPizzaDetail() {
    if (customPizzaDetailData.selectedIngredients.length !== customPizzaDetailData.requiredIngredients) {
        alert(`Debes seleccionar exactamente ${customPizzaDetailData.requiredIngredients} ingredientes`);
        return;
    }
    
    // Crear string con los ingredientes
    const ingredientsString = customPizzaDetailData.selectedIngredients.map(ing => ing.name).join(', ');
    const notes = `Ingredientes base: ${ingredientsString}`;
    
    // Agregar producto a la orden con las notas
    fetch(`/pos/{{ $order->id }}/add-product`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            product_id: customPizzaDetailData.productId, 
            quantity: 1,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCustomPizzaModalDetail();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar el producto');
    });
}

// Cerrar modal de pizza personalizable en detalle
function closeCustomPizzaModalDetail() {
    document.getElementById('customPizzaModalDetail').style.display = 'none';
    customPizzaDetailData = {
        productId: null,
        name: '',
        price: 0,
        requiredIngredients: 0,
        selectedIngredients: []
    };
}

</script>

<!-- Modal de Pizza Personalizable para Vista Detalle -->
<div id="customPizzaModalDetail" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                <div>
                    <h3 id="customPizzaTitleDetail" style="font-size: 22px; font-weight: bold; color: #212529; margin: 0 0 5px 0;">Pizza Personalizable</h3>
                    <p id="customPizzaSubtitleDetail" style="margin: 0; font-size: 14px; color: #6c757d;">Selecciona los ingredientes</p>
                </div>
                <button onclick="closeCustomPizzaModalDetail()" 
                        style="background: transparent; border: none; color: #dc3545; font-size: 28px; line-height: 1; cursor: pointer; padding: 0; width: 30px; height: 30px;">
                    ×
                </button>
            </div>
            
            <div style="margin-bottom: 20px; padding: 12px; background: #e3f2fd; border-radius: 8px; text-align: center;">
                <span style="font-size: 16px; font-weight: 600; color: #1976d2;">
                    Ingredientes seleccionados: <span id="selectedCountDisplayDetail">0/4</span>
                </span>
            </div>
            
            <div id="customPizzaIngredientsListDetail" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 20px;">
                <!-- Se llenarán con JavaScript -->
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <button onclick="closeCustomPizzaModalDetail()" class="btn btn-secondary">
                    Cancelar
                </button>
                <button id="confirmCustomPizzaBtnDetail" onclick="confirmCustomPizzaDetail()" class="btn btn-primary" disabled style="opacity: 0.5;">
                    Agregar al Pedido
                </button>
            </div>
        </div>
    </div>
</div>

@endsection