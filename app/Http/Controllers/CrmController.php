<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:crm.view');
    }

    /**
     * Dashboard principal de CRM
     */
    public function index(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Estadísticas de clientes
        $customerStats = [
            'total_customers' => Customer::count(),
            'new_customers_today' => Customer::whereDate('created_at', Carbon::today())->count(),
            'new_customers_month' => Customer::whereBetween('created_at', [
                Carbon::now()->startOfMonth(), 
                Carbon::now()->endOfMonth()
            ])->count(),
            'active_customers' => Customer::whereHas('orders', function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })->count(),
        ];

        // Clientes más valiosos - Simplificado
        $topCustomers = Customer::withSum(['orders as total_spent' => function($query) {
                $query->where('status', 'delivered');
            }], 'total_amount')
            ->withCount('orders')
            ->orderByRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE customers.id = orders.customer_id AND status = ?) DESC', ['delivered'])
            ->limit(10)
            ->get();

        // Clientes recientes
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Segmentación de clientes - Simplificada
        $customerSegments = [
            'vip' => 0, // Se calculará después
            'frequent' => 0, // Se calculará después
            'new' => Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'inactive' => Customer::whereDoesntHave('orders', function($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(90));
            })->count(),
        ];

        // Calcular segmentos complejos de forma separada
        $allCustomers = Customer::withSum(['orders as total_spent' => function($query) {
            $query->where('status', 'delivered');
        }], 'total_amount')->withCount('orders')->get();

        $customerSegments['vip'] = $allCustomers->where('total_spent', '>=', 500)->count();
        $customerSegments['frequent'] = $allCustomers->where('orders_count', '>=', 10)->count();

        // Análisis de comportamiento
        $behaviorAnalysis = [
            'avg_order_frequency' => $this->calculateAverageOrderFrequency(),
            'avg_customer_lifetime' => $this->calculateAverageCustomerLifetime(),
            'retention_rate' => $this->calculateRetentionRate(),
        ];

        return view('crm.index', compact(
            'customerStats',
            'topCustomers', 
            'recentCustomers',
            'customerSegments',
            'behaviorAnalysis',
            'dateRange'
        ));
    }

    /**
     * Análisis de segmentación de clientes
     */
    public function segmentation(Request $request)
    {
        // Segmentos predefinidos - Simplificados
        $allCustomers = Customer::withSum(['orders as total_spent' => function($query) {
            $query->where('status', 'completed');
        }], 'total_amount')->withCount('orders')->get();

        $segments = [
            'vip' => [
                'name' => 'Clientes VIP',
                'description' => 'Clientes con más de $500 en compras',
                'customers' => $allCustomers->where('total_spent', '>=', 500)->values()
            ],
            'frequent' => [
                'name' => 'Clientes Frecuentes',
                'description' => 'Clientes con 10+ órdenes',
                'customers' => $allCustomers->where('orders_count', '>=', 10)->values()
            ],
            'new' => [
                'name' => 'Clientes Nuevos',
                'description' => 'Registrados en los últimos 30 días',
                'customers' => Customer::where('created_at', '>=', Carbon::now()->subDays(30))
                    ->orderBy('created_at', 'desc')->get()
            ],
            'inactive' => [
                'name' => 'Clientes Inactivos',
                'description' => 'Sin órdenes en los últimos 90 días',
                'customers' => Customer::whereDoesntHave('orders', function($query) {
                    $query->where('created_at', '>=', Carbon::now()->subDays(90));
                })->get()
            ],
        ];

        $selectedSegment = $request->get('segment', 'vip');
        $currentSegment = $segments[$selectedSegment] ?? $segments['vip'];

        return view('crm.segmentation', compact('segments', 'currentSegment', 'selectedSegment'));
    }

    /**
     * Análisis de comportamiento de clientes
     */
    public function behavior(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Productos más populares por cliente
        $popularProducts = Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('products.id', 'products.name', 'products.description', 'products.category_id', 'products.price', 'products.is_featured', 'products.is_available', 'products.image', 'products.created_at', 'products.updated_at')
            ->orderBy(DB::raw('COUNT(order_items.id)'), 'desc')
            ->limit(10)
            ->get();

        // Patrones de pedidos por hora
        $orderPatterns = Order::select(
                DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('EXTRACT(HOUR FROM created_at)'))
            ->orderBy('hour')
            ->get();

        // Análisis de valor de vida del cliente
        $lifetimeValues = Customer::select('customers.*')
            ->withSum(['orders as lifetime_value' => function($query) {
                $query->where('status', 'completed');
            }], 'total_amount')
            ->withCount('orders')
            ->having('lifetime_value', '>', 0)
            ->orderBy('lifetime_value', 'desc')
            ->limit(20)
            ->get();

        return view('crm.behavior', compact(
            'popularProducts',
            'orderPatterns', 
            'lifetimeValues',
            'dateRange'
        ));
    }

    /**
     * Campañas de marketing
     */
    public function campaigns(Request $request)
    {
        // Esta funcionalidad se puede expandir para incluir
        // campañas de email marketing, promociones, etc.
        
        $campaigns = [
            [
                'id' => 1,
                'name' => 'Promoción Pizza Familiar',
                'type' => 'Descuento',
                'status' => 'Activa',
                'target_segment' => 'Familias',
                'created_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addDays(20),
            ],
            [
                'id' => 2,
                'name' => 'Reactivación Clientes Inactivos',
                'type' => 'Email',
                'status' => 'Borrador',
                'target_segment' => 'Clientes Inactivos',
                'created_at' => Carbon::now()->subDays(5),
                'expires_at' => null,
            ],
        ];

        return view('crm.campaigns', compact('campaigns'));
    }

    /**
     * Análisis de retención
     */
    public function retention(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Análisis de retención por cohortes
        $cohortAnalysis = $this->calculateCohortAnalysis($dateRange);
        
        // Clientes que regresaron
        $returningCustomers = Customer::whereHas('orders', function($query) use ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->whereHas('orders', function($query) use ($dateRange) {
            $query->where('created_at', '<', $dateRange['start']);
        })->withCount('orders')->get();

        // Tasa de retención mensual
        $monthlyRetention = $this->calculateMonthlyRetention();

        return view('crm.retention', compact(
            'cohortAnalysis',
            'returningCustomers',
            'monthlyRetention',
            'dateRange'
        ));
    }

    /**
     * Calcular frecuencia promedio de órdenes
     */
    private function calculateAverageOrderFrequency()
    {
        $customersWithOrders = Customer::whereHas('orders')->withCount('orders')->get();
        
        if ($customersWithOrders->isEmpty()) {
            return 0;
        }

        return $customersWithOrders->avg('orders_count');
    }

    /**
     * Calcular tiempo de vida promedio del cliente
     */
    private function calculateAverageCustomerLifetime()
    {
        $customers = Customer::whereHas('orders')
            ->with(['orders' => function($query) {
                $query->orderBy('created_at');
            }])
            ->get();

        if ($customers->isEmpty()) {
            return 0;
        }

        $totalLifetime = 0;
        $validCustomers = 0;

        foreach ($customers as $customer) {
            if ($customer->orders->count() >= 2) {
                $firstOrder = $customer->orders->first()->created_at;
                $lastOrder = $customer->orders->last()->created_at;
                $lifetime = $lastOrder->diffInDays($firstOrder);
                $totalLifetime += $lifetime;
                $validCustomers++;
            }
        }

        return $validCustomers > 0 ? $totalLifetime / $validCustomers : 0;
    }

    /**
     * Calcular tasa de retención
     */
    private function calculateRetentionRate()
    {
        $lastMonth = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $customersLastMonth = Customer::whereHas('orders', function($query) use ($lastMonth) {
            $query->whereMonth('created_at', $lastMonth->month)
                  ->whereYear('created_at', $lastMonth->year);
        })->count();

        $returningCustomers = Customer::whereHas('orders', function($query) use ($lastMonth) {
            $query->whereMonth('created_at', $lastMonth->month)
                  ->whereYear('created_at', $lastMonth->year);
        })->whereHas('orders', function($query) use ($twoMonthsAgo) {
            $query->whereMonth('created_at', $twoMonthsAgo->month)
                  ->whereYear('created_at', $twoMonthsAgo->year);
        })->count();

        return $customersLastMonth > 0 ? ($returningCustomers / $customersLastMonth) * 100 : 0;
    }

    /**
     * Calcular análisis de cohortes
     */
    private function calculateCohortAnalysis($dateRange)
    {
        // Implementación simplificada del análisis de cohortes
        // En una implementación real, esto sería más complejo
        return [
            'month_1' => 100,
            'month_2' => 75,
            'month_3' => 60,
            'month_4' => 45,
            'month_5' => 35,
            'month_6' => 30,
        ];
    }

    /**
     * Calcular retención mensual
     */
    private function calculateMonthlyRetention()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'retention_rate' => rand(60, 85) // Datos simulados
            ];
        }
        return $months;
    }

    /**
     * Obtener rango de fechas
     */
    private function getDateRange(Request $request)
    {
        $period = $request->get('period', '30_days');
        
        switch ($period) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case '30_days':
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now();
                break;
            case '90_days':
                $start = Carbon::now()->subDays(90);
                $end = Carbon::now();
                break;
            default:
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now();
        }

        return compact('start', 'end', 'period');
    }
}