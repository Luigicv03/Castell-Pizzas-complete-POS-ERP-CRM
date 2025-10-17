<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Ingredient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $period = $request->get('period', 'custom');
        
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

        // Calcular totales del período
        $periodTotals = [
            'total_sales' => $dailySales->sum('total_sales'),
            'total_orders' => $dailySales->sum('total_orders'),
            'avg_order_value' => $dailySales->sum('total_orders') > 0 ? $dailySales->sum('total_sales') / $dailySales->sum('total_orders') : 0
        ];

        return view('reports.sales', compact('dailySales', 'periodTotals', 'dateRange', 'period'));
    }

    public function salesDayDetail(Request $request, $date)
    {
        $selectedDate = Carbon::parse($date);
        
        // Obtener órdenes del día específico
        $dayOrders = Order::with(['table', 'customer', 'items.product'])
            ->where('status', 'delivered')
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular totales del día
        $dayTotals = [
            'total_sales' => $dayOrders->sum('total_amount'),
            'total_orders' => $dayOrders->count(),
            'avg_order_value' => $dayOrders->count() > 0 ? $dayOrders->sum('total_amount') / $dayOrders->count() : 0
        ];

        return view('reports.sales-day-detail', compact('dayOrders', 'dayTotals', 'selectedDate'));
    }

    public function deliveries(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $period = $request->get('period', 'custom');
        
        // Reporte de deliverys diarios
        $dailyDeliveries = Order::select(
                DB::raw('DATE(created_at) as period'),
                DB::raw('SUM(delivery_cost) as total_delivery_cost'),
                DB::raw('COUNT(*) as total_delivery_orders'),
                DB::raw('AVG(delivery_cost) as avg_delivery_cost')
            )
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('period', 'asc')
            ->get();

        // Calcular totales del período
        $periodTotals = [
            'total_delivery_cost' => $dailyDeliveries->sum('total_delivery_cost'),
            'total_delivery_orders' => $dailyDeliveries->sum('total_delivery_orders'),
            'avg_delivery_cost' => $dailyDeliveries->sum('total_delivery_orders') > 0 ? $dailyDeliveries->sum('total_delivery_cost') / $dailyDeliveries->sum('total_delivery_orders') : 0
        ];

        return view('reports.deliveries', compact('dailyDeliveries', 'periodTotals', 'dateRange', 'period'));
    }

    public function deliveriesDayDetail(Request $request, $date)
    {
        $selectedDate = Carbon::parse($date);
        
        // Obtener órdenes de delivery del día específico
        $dayDeliveries = Order::with(['table', 'customer', 'items.product'])
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular totales del día
        $dayTotals = [
            'total_delivery_cost' => $dayDeliveries->sum('delivery_cost'),
            'total_delivery_orders' => $dayDeliveries->count(),
            'avg_delivery_cost' => $dayDeliveries->count() > 0 ? $dayDeliveries->sum('delivery_cost') / $dayDeliveries->count() : 0
        ];

        return view('reports.deliveries-day-detail', compact('dayDeliveries', 'dayTotals', 'selectedDate'));
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
        $period = $request->get('period', 'custom');
        
        // Si se selecciona un período predefinido, calcular las fechas automáticamente
        switch ($period) {
            case '1_week':
                $start = Carbon::now()->subWeek()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case '2_weeks':
                $start = Carbon::now()->subWeeks(2)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case '1_month':
                $start = Carbon::now()->subMonth()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case '3_months':
                $start = Carbon::now()->subMonths(3)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case '6_months':
                $start = Carbon::now()->subMonths(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case '1_year':
                $start = Carbon::now()->subYear()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case 'custom':
            default:
                $start = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
                $end = $request->get('end_date', Carbon::now()->format('Y-m-d'));
                $start = Carbon::parse($start)->startOfDay();
                $end = Carbon::parse($end)->endOfDay();
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    public function exportSales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $period = $request->get('period', 'custom');
        
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

        // Calcular totales del período
        $periodTotals = [
            'total_sales' => $dailySales->sum('total_sales'),
            'total_orders' => $dailySales->sum('total_orders'),
            'avg_order_value' => $dailySales->sum('total_orders') > 0 ? $dailySales->sum('total_sales') / $dailySales->sum('total_orders') : 0
        ];

        $periodText = $this->getPeriodText($period, $dateRange);
        $filename = "Reporte_Ventas_{$periodText}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        // Crear contenido CSV
        $csvContent = "Fecha,Total Ventas ($),Número de Órdenes,Valor Promedio ($)\n";
        
        foreach ($dailySales as $sale) {
            $csvContent .= Carbon::parse($sale->period)->format('d/m/Y') . ",";
            $csvContent .= number_format($sale->total_sales, 2, '.', '') . ",";
            $csvContent .= $sale->total_orders . ",";
            $csvContent .= number_format($sale->avg_order_value, 2, '.', '') . "\n";
        }

        // Agregar totales al final
        $csvContent .= "\n";
        $csvContent .= "TOTALES DEL PERÍODO\n";
        $csvContent .= "Total Facturado: $" . number_format($periodTotals['total_sales'], 2, '.', '') . "\n";
        $csvContent .= "Total Órdenes: " . $periodTotals['total_orders'] . "\n";
        $csvContent .= "Valor Promedio: $" . number_format($periodTotals['avg_order_value'], 2, '.', '') . "\n";

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportSalesDayDetail(Request $request, $date)
    {
        $selectedDate = Carbon::parse($date);
        
        // Obtener órdenes del día específico
        $dayOrders = Order::with(['table', 'customer', 'items.product'])
            ->where('status', 'delivered')
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular totales del día
        $dayTotals = [
            'total_sales' => $dayOrders->sum('total_amount'),
            'total_orders' => $dayOrders->count(),
            'avg_order_value' => $dayOrders->count() > 0 ? $dayOrders->sum('total_amount') / $dayOrders->count() : 0
        ];

        $filename = "Detalle_Ventas_" . $selectedDate->format('d-m-Y') . "_" . now()->format('H-i-s') . '.csv';

        // Crear contenido CSV
        $csvContent = "Orden,Hora,Cliente,Mesa/Tipo,Total ($),Estado,Notas\n";
        
        foreach ($dayOrders as $order) {
            $customerInfo = '';
            if ($order->customer) {
                $customerInfo = $order->customer->name;
                if ($order->customer->phone) {
                    $customerInfo .= " ({$order->customer->phone})";
                }
            } elseif ($order->customer_name) {
                $customerInfo = $order->customer_name;
            } else {
                $customerInfo = 'Cliente General';
            }

            $tableInfo = '';
            if ($order->table) {
                $tableInfo = "Mesa {$order->table->name}";
            } else {
                $tableInfo = $order->getTypeText();
            }

            $orderTitle = $order->custom_title ?: 'Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT);

            $csvContent .= '"' . $orderTitle . '",';
            $csvContent .= $order->created_at->format('H:i A') . ',';
            $csvContent .= '"' . $customerInfo . '",';
            $csvContent .= '"' . $tableInfo . '",';
            $csvContent .= number_format($order->total_amount, 2, '.', '') . ',';
            $csvContent .= '"' . $order->getStatusText() . '",';
            $csvContent .= '"' . ($order->notes ?? '') . '"' . "\n";
        }

        // Agregar totales al final
        $csvContent .= "\n";
        $csvContent .= "TOTALES DEL DÍA\n";
        $csvContent .= "Total Facturado: $" . number_format($dayTotals['total_sales'], 2, '.', '') . "\n";
        $csvContent .= "Total Órdenes: " . $dayTotals['total_orders'] . "\n";
        $csvContent .= "Valor Promedio: $" . number_format($dayTotals['avg_order_value'], 2, '.', '') . "\n";

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportDeliveries(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $period = $request->get('period', 'custom');
        
        // Reporte de deliverys diarios
        $dailyDeliveries = Order::select(
                DB::raw('DATE(created_at) as period'),
                DB::raw('SUM(delivery_cost) as total_delivery_cost'),
                DB::raw('COUNT(*) as total_delivery_orders'),
                DB::raw('AVG(delivery_cost) as avg_delivery_cost')
            )
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('period', 'asc')
            ->get();

        // Calcular totales del período
        $periodTotals = [
            'total_delivery_cost' => $dailyDeliveries->sum('total_delivery_cost'),
            'total_delivery_orders' => $dailyDeliveries->sum('total_delivery_orders'),
            'avg_delivery_cost' => $dailyDeliveries->sum('total_delivery_orders') > 0 ? $dailyDeliveries->sum('total_delivery_cost') / $dailyDeliveries->sum('total_delivery_orders') : 0
        ];

        $periodText = $this->getPeriodText($period, $dateRange);
        $filename = "Reporte_Deliverys_{$periodText}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        // Crear contenido CSV
        $csvContent = "Fecha,Total Costo Delivery ($),Número de Deliverys,Costo Promedio Delivery ($)\n";
        
        foreach ($dailyDeliveries as $delivery) {
            $csvContent .= Carbon::parse($delivery->period)->format('d/m/Y') . ",";
            $csvContent .= number_format($delivery->total_delivery_cost, 2, '.', '') . ",";
            $csvContent .= $delivery->total_delivery_orders . ",";
            $csvContent .= number_format($delivery->avg_delivery_cost, 2, '.', '') . "\n";
        }

        // Agregar totales al final
        $csvContent .= "\n";
        $csvContent .= "TOTALES DEL PERÍODO\n";
        $csvContent .= "Total Costo Delivery: $" . number_format($periodTotals['total_delivery_cost'], 2, '.', '') . "\n";
        $csvContent .= "Total Deliverys: " . $periodTotals['total_delivery_orders'] . "\n";
        $csvContent .= "Costo Promedio Delivery: $" . number_format($periodTotals['avg_delivery_cost'], 2, '.', '') . "\n";

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportDeliveriesDayDetail(Request $request, $date)
    {
        $selectedDate = Carbon::parse($date);
        
        // Obtener órdenes de delivery del día específico
        $dayDeliveries = Order::with(['table', 'customer', 'items.product'])
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular totales del día
        $dayTotals = [
            'total_delivery_cost' => $dayDeliveries->sum('delivery_cost'),
            'total_delivery_orders' => $dayDeliveries->count(),
            'avg_delivery_cost' => $dayDeliveries->count() > 0 ? $dayDeliveries->sum('delivery_cost') / $dayDeliveries->count() : 0
        ];

        $filename = "Detalle_Deliverys_" . $selectedDate->format('d-m-Y') . "_" . now()->format('H-i-s') . '.csv';

        // Crear contenido CSV
        $csvContent = "Orden,Hora,Cliente,Dirección,Costo Delivery ($),Total Orden ($),Estado,Notas\n";
        
        foreach ($dayDeliveries as $order) {
            $customerInfo = '';
            if ($order->customer) {
                $customerInfo = $order->customer->name;
                if ($order->customer->phone) {
                    $customerInfo .= " ({$order->customer->phone})";
                }
            } elseif ($order->customer_name) {
                $customerInfo = $order->customer_name;
            } else {
                $customerInfo = 'Cliente General';
            }

            $orderTitle = $order->custom_title ?: 'Pedido #' . str_pad($order->daily_number, 2, '0', STR_PAD_LEFT);

            $csvContent .= '"' . $orderTitle . '",';
            $csvContent .= $order->created_at->format('H:i A') . ',';
            $csvContent .= '"' . $customerInfo . '",';
            $csvContent .= '"' . ($order->delivery_address ?? 'No especificada') . '",';
            $csvContent .= number_format($order->delivery_cost, 2, '.', '') . ',';
            $csvContent .= number_format($order->total_amount, 2, '.', '') . ',';
            $csvContent .= '"' . $order->getStatusText() . '",';
            $csvContent .= '"' . ($order->notes ?? '') . '"' . "\n";
        }

        // Agregar totales al final
        $csvContent .= "\n";
        $csvContent .= "TOTALES DEL DÍA\n";
        $csvContent .= "Total Costo Delivery: $" . number_format($dayTotals['total_delivery_cost'], 2, '.', '') . "\n";
        $csvContent .= "Total Deliverys: " . $dayTotals['total_delivery_orders'] . "\n";
        $csvContent .= "Costo Promedio Delivery: $" . number_format($dayTotals['avg_delivery_cost'], 2, '.', '') . "\n";

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function getPeriodText($period, $dateRange)
    {
        switch ($period) {
            case '1_week':
                return '1_Semana';
            case '2_weeks':
                return '2_Semanas';
            case '1_month':
                return '1_Mes';
            case '3_months':
                return '3_Meses';
            case '6_months':
                return '6_Meses';
            case '1_year':
                return '1_Año';
            case 'custom':
            default:
                $start = Carbon::parse($dateRange['start'])->format('d-m-Y');
                $end = Carbon::parse($dateRange['end'])->format('d-m-Y');
                return "Personalizado_{$start}_a_{$end}";
        }
    }
}