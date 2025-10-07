<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Ingredient;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Display the POS interface
     */
    public function index(Request $request)
    {
        // Sincronizar estados de mesas antes de cargar
        $this->syncTableStatuses();
        
        $tables = Table::where('is_active', true)->orderBy('zone')->orderBy('name')->get();
        
        // Agrupar mesas por zona
        $tablesByZone = $tables->groupBy('zone');
        
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->with('category')->get();
        $customers = Customer::where('is_active', true)->get();
        
        // Obtener ingredientes desde productos (categorías: Ingredientes Tradicionales e Ingredientes Premium)
        $ingredientCategories = Category::whereIn('name', ['Ingredientes Tradicionales', 'Ingredientes Premium'])
            ->where('is_active', true)
            ->pluck('id');
        
        $ingredients = Product::whereIn('category_id', $ingredientCategories)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Obtener pedidos activos
        $activeOrders = Order::whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['table', 'customer', 'items.product'])
            ->get();

        // Verificar si se preseleccionó una mesa
        $selectedTableId = $request->get('table_id');
        $selectedTable = null;
        if ($selectedTableId) {
            $selectedTable = Table::find($selectedTableId);
        }

        // Obtener tasa de cambio actual
        $exchangeRate = ExchangeRate::getCurrentRate();

        return view('pos.index', compact(
            'tables', 
            'tablesByZone',
            'categories', 
            'products', 
            'customers', 
            'ingredients',
            'activeOrders',
            'selectedTable',
            'exchangeRate'
        ));
    }

    /**
     * Show order creation form with type selection
     */
    public function create()
    {
        $tables = Table::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        
        return view('pos.create', compact('tables', 'customers'));
    }

    /**
     * Show order builder interface
     */
    public function buildOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,delivery,pickup',
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $tables = Table::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->with('category')->get();
        
        // Pasar datos a la vista
        $orderType = $request->order_type;
        $tableId = $request->table_id;
        $customerId = $request->customer_id;
        $customerName = $request->customer_name;
        
        return view('pos.build-order', compact(
            'tables', 
            'customers', 
            'categories', 
            'products',
            'orderType',
            'tableId',
            'customerId',
            'customerName'
        ));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Datos recibidos en store:', $request->all());
        
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,delivery,pickup',
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'delivery_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // NO crear cliente automáticamente, solo guardar el nombre temporal
            // El cliente se creará/asociará al momento del pago
            
            // Crear el pedido
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'daily_number' => $this->generateDailyNumber(),
                'table_id' => $request->table_id,
                'customer_id' => $request->customer_id, // Puede ser null
                'customer_name' => $request->customer_name, // Nombre temporal
                'user_id' => Auth::id(),
                'type' => $request->order_type,
                'status' => Order::STATUS_PENDING,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => 0,
                'delivery_cost' => $request->delivery_cost ?? 0,
                'total_amount' => $request->total_amount,
                'notes' => $request->notes,
                'currency' => 'USD',
                'exchange_rate' => 1,
            ]);

            // Crear los items del pedido
            foreach ($request->items as $item) {
                // Crear el item padre
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'parent_id' => null,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'status' => 'pending',
                    'notes' => $item['notes'] ?? null, // Guardar notas con ingredientes base
                ]);
                
                // Si el item tiene children (ingredientes/cajas), crearlos también
                if (isset($item['children']) && is_array($item['children']) && count($item['children']) > 0) {
                    foreach ($item['children'] as $child) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'parent_id' => $orderItem->id, // ← Relacionar con el padre
                            'product_id' => $child['productId'],
                            'quantity' => $child['quantity'],
                            'unit_price' => $child['price'],
                            'total_price' => $child['quantity'] * $child['price'],
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            // Actualizar estado de la mesa si es dine_in
            if ($request->order_type === Order::TYPE_DINE_IN && $request->table_id) {
                Table::where('id', $request->table_id)->update(['status' => Table::STATUS_OCCUPIED]);
            }

            DB::commit();

            // Auto print orders - solo generar URLs, no ejecutar
            // Las comandas se imprimirán cuando se acceda a las rutas de impresión

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'daily_number' => $order->daily_number,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['table', 'customer', 'items.product', 'payments']);
        
        return view('pos.show', compact('order'));
    }

    public function orderDetail(Order $order)
    {
        $order->load([
            'customer', 
            'table', 
            'items' => function($query) {
                $query->whereNull('parent_id')->with(['product', 'children.product']);
            }, 
            'payments'
        ]);
        
        // Solo cargar categorías que tienen productos activos
        $categories = \App\Models\Category::where('is_active', true)
            ->whereHas('products', function($query) {
                $query->where('is_active', true);
            })
            ->get();
        
        $products = \App\Models\Product::where('is_active', true)->with('category')->get();
        
        // Obtener ingredientes desde productos (categorías: Ingredientes Tradicionales e Ingredientes Premium)
        $ingredientCategories = Category::whereIn('name', ['Ingredientes Tradicionales', 'Ingredientes Premium'])
            ->where('is_active', true)
            ->pluck('id');
        
        $ingredients = Product::whereIn('category_id', $ingredientCategories)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Obtener tasa de cambio actual
        $exchangeRate = ExchangeRate::getCurrentRate();

        return view('pos.order-detail', compact('order', 'categories', 'products', 'ingredients', 'exchangeRate'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del pedido actualizado correctamente'
        ]);
    }

    public function addProductToOrder(Request $request, $orderId)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);

            $order = Order::findOrFail($orderId);
            $product = \App\Models\Product::findOrFail($request->product_id);
            
            // Verificar si el producto es una pizza
            $isPizza = str_contains(strtolower($product->category->name ?? ''), 'pizza');
            
            if ($isPizza) {
                // Para pizzas, SIEMPRE crear un nuevo item (no acumular)
                // Esto permite agregar ingredientes diferentes a cada pizza
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'parent_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $request->quantity,
                    'status' => 'pending',
                    'notes' => $request->notes
                ]);
            } else {
                // Para productos que NO son pizzas, acumular como antes
                $existingItem = $order->items()
                    ->where('product_id', $product->id)
                    ->whereNull('parent_id')
                    ->first();
                
                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $request->quantity,
                        'total_price' => ($existingItem->quantity + $request->quantity) * $existingItem->unit_price
                    ]);
                } else {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'parent_id' => null,
                        'product_id' => $product->id,
                        'quantity' => $request->quantity,
                        'unit_price' => $product->price,
                        'total_price' => $product->price * $request->quantity,
                        'status' => 'pending'
                    ]);
                }
            }

            // Recalcular totales
            $this->recalculateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding product to order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateItemQuantity(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $order = Order::findOrFail($orderId);
        $item = $order->items()->findOrFail($itemId);
        $item->update([
            'quantity' => $request->quantity,
            'total_price' => $item->unit_price * $request->quantity
        ]);

        // Recalcular totales
        $this->recalculateOrderTotals($order);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada correctamente'
        ]);
    }

    /**
     * Update order item notes (kitchen instructions)
     */
    public function updateItemNotes(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $order = Order::findOrFail($orderId);
        $item = $order->items()->findOrFail($itemId);
        
        $item->update([
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nota actualizada correctamente'
        ]);
    }

    /**
     * Obtener la tasa de cambio actual
     */
    public function getCurrentExchangeRate()
    {
        $exchangeRate = ExchangeRate::getCurrentRate();
        
        return response()->json([
            'usd_to_bsf' => $exchangeRate->usd_to_bsf,
            'is_automatic' => $exchangeRate->is_automatic,
            'last_updated_at' => $exchangeRate->last_updated_at,
            'source' => $exchangeRate->source
        ]);
    }

    /**
     * Procesar pedido con pagos multimoneda
     */
    public function processOrder(Request $request)
    {
        try {
            $request->validate([
                'order_type' => 'required|in:dine_in,takeaway,delivery',
                'table_id' => 'nullable|exists:tables,id',
                'customer_name' => 'nullable|string|max:255',
                'customer_id' => 'nullable|exists:customers,id',
                'order_notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.notes' => 'nullable|string',
                'payments' => 'required|array|min:1',
                'payments.*.method' => 'required|in:cash,pago_movil,zelle,binance,card',
                'payments.*.amount' => 'required|numeric|min:0.01',
                'payments.*.currency' => 'required|in:USD,BSF',
                'payments.*.reference' => 'nullable|string',
                'payments.*.notes' => 'nullable|string',
                'exchange_rate' => 'required|numeric|min:0.01'
            ]);

            DB::beginTransaction();

            // Crear el pedido
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'daily_number' => $this->getDailyOrderNumber(),
                'type' => $request->order_type,
                'table_id' => $request->table_id,
                'customer_name' => $request->customer_name,
                'customer_id' => $request->customer_id,
                'status' => 'pending',
                'total_amount' => 0, // Se calculará después
                'notes' => $request->order_notes,
                'user_id' => Auth::id()
            ]);

            $totalAmount = 0;

            // Agregar items al pedido
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $itemTotal = $itemData['unit_price'] * $itemData['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'parent_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemTotal,
                    'status' => 'pending',
                    'notes' => $itemData['notes'] ?? null
                ]);

                $totalAmount += $itemTotal;

                // Agregar children si existen
                if (isset($itemData['children']) && is_array($itemData['children'])) {
                    foreach ($itemData['children'] as $childData) {
                        if (isset($childData['product_id']) && $childData['product_id']) {
                            $childProduct = Product::findOrFail($childData['product_id']);
                            OrderItem::create([
                                'order_id' => $order->id,
                                'parent_id' => null, // Se actualizará después
                                'product_id' => $childProduct->id,
                                'quantity' => $childData['quantity'] ?? 1,
                                'unit_price' => $childData['unit_price'] ?? $childProduct->price,
                                'total_price' => ($childData['unit_price'] ?? $childProduct->price) * ($childData['quantity'] ?? 1),
                                'status' => 'pending'
                            ]);
                        }
                    }
                }
            }

            // Actualizar el total del pedido
            $order->update(['total_amount' => $totalAmount]);

            // Procesar pagos
            foreach ($request->payments as $paymentData) {
                $payment = new Payment();
                
                if ($paymentData['currency'] === 'USD') {
                    $payment->setAmountFromUsd($paymentData['amount'], $request->exchange_rate);
                } else {
                    $payment->setAmountFromBsF($paymentData['amount'], $request->exchange_rate);
                }
                
                $payment->order_id = $order->id;
                $payment->method = $paymentData['method'];
                $payment->reference = $paymentData['reference'] ?? null;
                $payment->notes = $paymentData['notes'] ?? null;
                $payment->user_id = Auth::id();
                
                $payment->save();
            }

            // Crear o actualizar cliente si se proporcionó información completa
            if ($request->customer_name && $request->customer_id) {
                // Actualizar cliente existente
                $customer = Customer::findOrFail($request->customer_id);
                $customer->update(['name' => $request->customer_name]);
                $order->update(['customer_id' => $customer->id]);
            } elseif ($request->customer_name && !$request->customer_id) {
                // Crear nuevo cliente temporal (solo nombre)
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => null,
                    'phone' => null,
                    'address' => null
                ]);
                $order->update(['customer_id' => $customer->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido procesado exitosamente',
                'order' => $order->load(['items.product', 'payments'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeItemFromOrder($orderId, $itemId)
    {
        $order = Order::findOrFail($orderId);
        $item = $order->items()->findOrFail($itemId);
        $item->delete();

        // Recalcular totales
        $this->recalculateOrderTotals($order);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }

    private function recalculateOrderTotals(Order $order)
    {
        $subtotal = $order->items()->sum('total_price');
        $deliveryCost = $order->delivery_cost ?? 0;
        $totalAmount = $subtotal + $deliveryCost;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => 0, // Los precios ya incluyen IVA
            'delivery_cost' => $deliveryCost,
            'total_amount' => $totalAmount
        ]);
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $tables = Table::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->with('category')->get();
        
        $order->load(['items.product']);
        
        return view('pos.edit', compact('order', 'tables', 'customers', 'categories', 'products'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $order->update($request->only(['table_id', 'customer_id', 'status', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Pedido actualizado exitosamente',
        ]);
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un pedido ya entregado',
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Liberar la mesa si es dine-in
            if ($order->table_id && $order->type === 'dine_in') {
                Table::where('id', $order->table_id)->update(['status' => 'free']);
            }

            // Eliminar items y pagos
            $order->items()->delete();
            $order->payments()->delete();
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido eliminado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el pedido: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show order for a specific table
     */
    public function showTableOrder($tableId)
    {
        $table = Table::findOrFail($tableId);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->with('category')->get();
        $customers = Customer::where('is_active', true)->get();
        
        // Buscar orden activa para esta mesa
        $activeOrder = Order::where('table_id', $tableId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['items.product', 'customer', 'user'])
            ->first();

        return view('pos.table-order', compact(
            'table', 
            'activeOrder', 
            'categories', 
            'products', 
            'customers'
        ));
    }




    /**
     * Print kitchen order
     */
    public function printKitchenOrder($orderId)
    {
        $order = Order::with([
            'items' => function($query) {
                $query->whereNull('parent_id')->with(['product.category', 'children.product']);
            },
            'table', 
            'customer', 
            'user'
        ])->findOrFail($orderId);
        
        // Filtrar productos para cocina (todas las categorías excepto bebidas)
        $kitchenItems = $order->items->filter(function($item) {
            $categoryName = $item->product->category->name ?? '';
            // Incluir todo excepto bebidas
            return !str_contains(strtolower($categoryName), 'bebida');
        });

        $exchangeRate = ExchangeRate::getCurrentRate();
        
        return view('pos.print.kitchen', compact('order', 'kitchenItems', 'exchangeRate'));
    }

    /**
     * Print bar order
     */
    public function printBarOrder($orderId)
    {
        $order = Order::with([
            'items' => function($query) {
                $query->whereNull('parent_id')->with(['product.category', 'children.product']);
            },
            'table', 
            'customer', 
            'user'
        ])->findOrFail($orderId);
        
        // Filtrar productos para barra (solo bebidas)
        $barItems = $order->items->filter(function($item) {
            $categoryName = $item->product->category->name ?? '';
            return str_contains(strtolower($categoryName), 'bebida');
        });

        $exchangeRate = ExchangeRate::getCurrentRate();
        
        return view('pos.print.bar', compact('order', 'barItems', 'exchangeRate'));
    }

    /**
     * Show payment form for order
     */
    public function showPayment($orderId)
    {
        $order = Order::with(['items.product', 'customer', 'table'])->findOrFail($orderId);
        
        return view('pos.payment', compact('order'));
    }

    /**
     * Process payment for order
     */
    public function processPayment(Request $request, $orderId)
    {
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,mobile_payment,zelle,binance,pos,transfer',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string|max:255',
            'customer_data' => 'required|array',
            'customer_data.name' => 'required|string|max:255',
            'customer_data.email' => 'nullable|email|max:255',
            'customer_data.phone' => 'nullable|string|max:20',
            'customer_data.address' => 'nullable|string|max:500',
            'customer_data.cedula' => 'required|string|max:20',
        ]);

        $order = Order::findOrFail($orderId);
        
        DB::beginTransaction();
        
        try {
            // Manejar datos del cliente
            if ($request->customer_data) {
                $customer = null;
                
                // Buscar cliente existente por cédula (identificador único)
                if (!empty($request->customer_data['cedula'])) {
                    $existingCustomer = Customer::where('cedula', $request->customer_data['cedula'])->first();
                    if ($existingCustomer) {
                        // Actualizar cliente existente
                        $existingCustomer->update($request->customer_data);
                        $customer = $existingCustomer;
                    }
                }
                
                // Si no se encontró cliente existente, crear uno nuevo
                if (!$customer) {
                    try {
                        $customer = Customer::create([
                            'name' => $request->customer_data['name'],
                            'email' => $request->customer_data['email'] ?? null,
                            'phone' => $request->customer_data['phone'] ?? null,
                            'address' => $request->customer_data['address'] ?? null,
                            'cedula' => $request->customer_data['cedula'] ?? null,
                            'is_active' => true,
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Si hay error de cédula duplicada, buscar el cliente existente
                        if (str_contains($e->getMessage(), 'customers_cedula_unique')) {
                            $customer = Customer::where('cedula', $request->customer_data['cedula'])->first();
                            if ($customer) {
                                // Actualizar cliente existente
                                $customer->update($request->customer_data);
                            }
                        } else {
                            throw $e;
                        }
                    }
                }
                
                // Asociar cliente a la orden
                if ($customer) {
                    $order->update(['customer_id' => $customer->id]);
                }
            }

            // Obtener tasa de cambio actual
            $exchangeRate = ExchangeRate::getCurrentRate();
            
            // Crear múltiples pagos
            $totalPaid = 0;
            foreach ($request->payments as $paymentData) {
                // Determinar si es un método en bolívares
                $isBsfMethod = in_array($paymentData['method'], ['mobile_payment', 'pos', 'transfer']);
                
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $paymentData['amount'], // Monto en USD
                    'amount_usd' => $paymentData['amount'], // Monto en USD
                    'amount_bsf' => $isBsfMethod ? ($paymentData['amountBsf'] ?? ($paymentData['amount'] * $exchangeRate->usd_to_bsf)) : null,
                    'payment_method' => $paymentData['method'],
                    'reference' => $paymentData['reference'] ?? null,
                    'status' => Payment::STATUS_COMPLETED,
                    'user_id' => Auth::id(),
                    'currency' => $paymentData['currency'] ?? 'USD',
                    'exchange_rate' => $isBsfMethod ? $exchangeRate->usd_to_bsf : 1,
                ]);
                $totalPaid += $paymentData['amount'];
            }
            
            // Verificar que el total pagado coincida con el total de la orden
            if (abs($totalPaid - $order->total_amount) > 0.01) {
                throw new \Exception('El monto total pagado no coincide con el total de la orden');
            }

            // Actualizar estado de la orden
            $order->update([
                'status' => Order::STATUS_DELIVERED,
                'delivered_at' => now(),
            ]);

            // Liberar mesa si es dine_in
            if ($order->type === Order::TYPE_DINE_IN && $order->table) {
                $order->table->update(['status' => Table::STATUS_FREE]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'order' => $order->fresh(['items.product', 'customer', 'table', 'payments'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto print kitchen and bar orders
     */
    public function autoPrintOrders($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Imprimir comanda de cocina
        $kitchenUrl = route('pos.print.kitchen', $order->id);
        
        // Imprimir comanda de barra
        $barUrl = route('pos.print.bar', $order->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Comandas enviadas a imprimir',
            'kitchen_url' => $kitchenUrl,
            'bar_url' => $barUrl,
        ]);
    }


    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $prefix = config('pizzeria.pos.order_number_prefix', 'ORD-');
        $date = now()->format('Ymd');
        $sequence = Order::whereDate('created_at', today())->count() + 1;
        
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate daily number for order
     */
    private function generateDailyNumber()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        
        $lastOrder = Order::whereBetween('created_at', [$today, $tomorrow])
            ->orderBy('daily_number', 'desc')
            ->first();
        
        return $lastOrder ? $lastOrder->daily_number + 1 : 1;
    }

    /**
     * Get active orders for status bar
     */
    public function getActiveOrders()
    {
        try {
            $orders = Order::with(['customer', 'table', 'items'])
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'daily_number' => $order->daily_number,
                        'status' => $order->status,
                        'type' => $order->type,
                        'total_amount' => $order->total_amount,
                        'created_at' => $order->created_at->toISOString(),
                        'customer' => $order->customer ? [
                            'name' => $order->customer->name,
                            'phone' => $order->customer->phone,
                            'email' => $order->customer->email,
                        ] : null,
                        'table' => $order->table ? [
                            'id' => $order->table->id,
                            'name' => $order->table->name,
                        ] : null,
                        'items_count' => $order->items->count(),
                    ];
                });

        return response()->json($orders);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error in getActiveOrders: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function getDeliveryCosts()
    {
        try {
            $deliveryCosts = \App\Models\DeliveryCost::getActiveRanges();
            return response()->json($deliveryCosts);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting delivery costs: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function calculateDeliveryCost(Request $request)
    {
        try {
            $request->validate([
                'distance' => 'required|numeric|min:0'
            ]);

            $deliveryCost = \App\Models\DeliveryCost::getCostForDistance($request->distance);
            
            if ($deliveryCost) {
                return response()->json([
                    'success' => true,
                    'cost' => $deliveryCost->cost,
                    'description' => $deliveryCost->description
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró costo de delivery para esa distancia'
                ], 404);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error calculating delivery cost: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener ingredientes por tamaño de pizza
     */
    public function getIngredientsBySize($size)
    {
        try {
            // Normalizar el tamaño
            $sizeMap = [
                'personal' => ['Personal', 'personal', '25cm'],
                'mediana' => ['Mediana', 'mediana', '33cm'],
                'familiar' => ['Familiar', 'familiar', '40cm'],
                'calzone' => ['Calzone', 'calzone'],
            ];
            
            $sizeKey = strtolower($size);
            $searchTerms = $sizeMap[$sizeKey] ?? [$size];
            
            Log::info("Buscando ingredientes para tamaño: {$size}, términos: " . implode(', ', $searchTerms));
            
            // Obtener ingredientes que contienen cualquiera de los términos de búsqueda en su nombre
            $ingredients = Product::with('category')
                ->whereIn('category_id', function($query) {
                    $query->select('id')
                          ->from('categories')
                          ->where('name', 'LIKE', '%Ingrediente%');
                })
                ->where('is_active', true)
                ->where(function($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhere('name', 'like', "%{$term}%");
                    }
                })
                ->orderBy('category_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            
            Log::info("Ingredientes encontrados: " . $ingredients->count());
            
            return response()->json($ingredients);
        } catch (\Exception $e) {
            Log::error('Error getting ingredients by size: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Buscar producto por nombre exacto
     */
    public function searchProductByName(Request $request)
    {
        try {
            $name = $request->input('name');
            
            $product = Product::where('name', $name)
                ->where('is_active', true)
                ->first();
            
            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }
            
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error searching product by name: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Agregar ingrediente a un item de pizza
     */
    public function addIngredientToItem(Request $request, $orderId, $itemId)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $order = Order::findOrFail($orderId);
            $parentItem = OrderItem::findOrFail($itemId);
            $product = Product::findOrFail($request->product_id);
            
            // Si la pizza tiene cantidad > 1, debemos separarla
            if ($parentItem->quantity > 1) {
                // Reducir la cantidad de la pizza original
                $parentItem->update([
                    'quantity' => $parentItem->quantity - 1,
                    'total_price' => $parentItem->unit_price * ($parentItem->quantity - 1)
                ]);
                
                // Crear una nueva pizza con cantidad 1
                $parentItem = OrderItem::create([
                    'order_id' => $order->id,
                    'parent_id' => null,
                    'product_id' => $parentItem->product_id,
                    'quantity' => 1,
                    'unit_price' => $parentItem->unit_price,
                    'total_price' => $parentItem->unit_price,
                    'status' => 'pending'
                ]);
            }
            
            // Crear el ingrediente como hijo del item de pizza
            $ingredientItem = OrderItem::create([
                'order_id' => $order->id,
                'parent_id' => $parentItem->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'total_price' => $product->price * $request->quantity,
                'status' => 'pending'
            ]);

            // Recalcular totales
            $this->recalculateOrderTotals($order);

            return response()->json([
                'success' => true,
                'message' => 'Ingrediente agregado correctamente',
                'ingredient' => $ingredientItem->load('product')
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding ingredient to item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el ingrediente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar estados de mesas con órdenes activas
     */
    private function syncTableStatuses()
    {
        $tables = Table::all();
        
        foreach ($tables as $table) {
            $table->syncStatus();
        }
    }

    /**
     * Buscar clientes para el POS (sin restricciones de roles)
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('cedula', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'phone', 'email', 'cedula', 'address')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

}
