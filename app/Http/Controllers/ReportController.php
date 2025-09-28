<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Ingredient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Reporte de ventas diarias
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as period'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('period', 'asc')
            ->get();

        return view('reports.sales', compact('dailySales', 'dateRange'));
    }

    public function products(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Productos más vendidos
        $topProducts = Product::select('products.*')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($dateRange) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->groupBy('products.id', 'products.name', 'products.description', 'products.category_id', 'products.price', 'products.is_featured', 'products.is_active', 'products.image', 'products.created_at', 'products.updated_at')
            ->orderBy(DB::raw('COUNT(order_items.id)'), 'desc')
            ->limit(10)
            ->get();

        return view('reports.products', compact('topProducts', 'dateRange'));
    }

    public function customers(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Clientes más activos
        $topCustomers = Customer::select('customers.*')
            ->selectRaw('(SELECT COUNT(*) FROM orders WHERE customers.id = orders.customer_id AND orders.created_at BETWEEN ? AND ?) as total_orders', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('(SELECT COALESCE(SUM(orders.total_amount), 0) FROM orders WHERE customers.id = orders.customer_id AND orders.created_at BETWEEN ? AND ? AND orders.status = ?) as total_spent', [$dateRange['start'], $dateRange['end'], 'delivered'])
            ->orderByRaw('(SELECT COUNT(*) FROM orders WHERE customers.id = orders.customer_id AND orders.created_at BETWEEN ? AND ?) DESC', [$dateRange['start'], $dateRange['end']])
            ->limit(20)
            ->get();

        return view('reports.customers', compact('topCustomers', 'dateRange'));
    }

    public function inventory()
    {
        // Reporte de inventario
        $ingredients = Ingredient::with('supplier')
            ->orderBy('current_stock', 'asc')
            ->get();

        return view('reports.inventory', compact('ingredients'));
    }

    private function getDateRange($request)
    {
        $start = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        return [
            'start' => Carbon::parse($start)->startOfDay(),
            'end' => Carbon::parse($end)->endOfDay(),
        ];
    }
}