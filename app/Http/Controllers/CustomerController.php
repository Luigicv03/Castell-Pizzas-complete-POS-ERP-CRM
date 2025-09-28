<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:customers.view');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::withSum(['orders as total_spent' => function($query) {
                $query->where('status', 'delivered');
            }], 'total_amount')
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date|before:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('status', 'delivered')->sum('total_amount'),
            'avg_order_value' => $customer->orders()->where('status', 'delivered')->avg('total_amount'),
            'last_order' => $customer->orders()->latest()->first(),
        ];

        return view('customers.show', compact('customer', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date|before:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Search customers for API
     */
    public function search(Request $request)
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

    /**
     * Get customer orders
     */
    public function orders(Customer $customer)
    {
        $orders = $customer->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('customers.orders', compact('customer', 'orders'));
    }

    /**
     * Get customer statistics
     */
    public function statistics(Customer $customer)
    {
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->where('status', 'delivered')->count(),
            'total_spent' => $customer->orders()->where('status', 'delivered')->sum('total_amount'),
            'avg_order_value' => $customer->orders()->where('status', 'delivered')->avg('total_amount'),
            'first_order' => $customer->orders()->oldest()->first(),
            'last_order' => $customer->orders()->latest()->first(),
            'favorite_products' => $customer->orders()
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

}