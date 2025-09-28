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
        
        $stats = [
            'daily_sales' => Order::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            
            'pending_orders' => Order::whereIn('status', ['pending', 'preparing'])
                ->count(),
            
            'occupied_tables' => Table::where('status', 'occupied')->count(),
            'total_tables' => Table::where('is_active', true)->count(),
            
            'low_stock_ingredients' => Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('is_active', true)
                ->count(),
        ];

        // Órdenes recientes
        $recent_orders = Order::with(['customer', 'table', 'user'])
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

        // Asegurar que las colecciones no sean null
        $recent_orders = $recent_orders ?? collect();
        $top_products = $top_products ?? collect();

        return view('dashboard', compact('stats', 'recent_orders', 'top_products'));
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
