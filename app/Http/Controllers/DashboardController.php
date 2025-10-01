<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas del día
        $today = now()->startOfDay();
        
        // Calcular ventas y ganancias del día
        $dailyOrders = Order::with(['items.product'])
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->get();
        
        $dailySales = $dailyOrders->sum('total_amount');
        $dailyCost = 0;
        $dailyProfit = 0;
        
        foreach ($dailyOrders as $order) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $itemCost = $item->product->cost * $item->quantity;
                    $dailyCost += $itemCost;
                }
            }
        }
        
        $dailyProfit = $dailySales - $dailyCost;
        $profitMargin = $dailySales > 0 ? ($dailyProfit / $dailySales) * 100 : 0;
        
        $stats = [
            'daily_sales' => $dailySales,
            'daily_cost' => $dailyCost,
            'daily_profit' => $dailyProfit,
            'profit_margin' => $profitMargin,
            
            'pending_orders' => Order::whereIn('status', ['pending', 'preparing'])
                ->count(),
            
            'occupied_tables' => Table::where('status', 'occupied')->count(),
            'total_tables' => Table::where('is_active', true)->count(),
            
            'low_stock_ingredients' => Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('is_active', true)
                ->count(),
        ];

        // Órdenes recientes con cálculo de ganancia
        $recent_orders = Order::with(['customer', 'table', 'user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Productos más vendidos del día
        $top_products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.name', 'products.price', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Órdenes del día para el modal de ganancias
        $profitOrders = Order::with(['items.product', 'customer', 'table'])
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                $totalCost = 0;
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $totalCost += $item->product->cost * $item->quantity;
                    }
                }
                $order->total_cost = $totalCost;
                $order->profit = $order->total_amount - $totalCost;
                $order->profit_margin = $order->total_amount > 0 ? ($order->profit / $order->total_amount) * 100 : 0;
                return $order;
            });

        // Asegurar que las colecciones no sean null
        $recent_orders = $recent_orders ?? collect();
        $top_products = $top_products ?? collect();
        $profitOrders = $profitOrders ?? collect();

        return view('dashboard', compact('stats', 'recent_orders', 'top_products', 'profitOrders'));
    }

    public function orderDetails(Order $order)
    {
        // Cargar todas las relaciones necesarias
        $order->load([
            'customer',
            'table', 
            'user',
            'items.product',
            'payments'
        ]);

        return view('orders.details', compact('order'));
    }
}
