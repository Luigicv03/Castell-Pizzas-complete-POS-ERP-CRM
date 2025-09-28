<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ingredients.view')->only(['index', 'show']);
        $this->middleware('can:ingredients.create')->only(['create', 'store']);
        $this->middleware('can:ingredients.edit')->only(['edit', 'update']);
        $this->middleware('can:ingredients.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ingredient::with('supplier');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('low_stock')) {
            $query->whereRaw('current_stock <= minimum_stock');
        }

        $ingredients = $query->orderBy('name')->paginate(15);
        $suppliers = Supplier::orderBy('name')->get();

        return view('ingredients.index', compact('ingredients', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('ingredients.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:ingredients',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'cost_per_unit' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'current_stock' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $ingredient = Ingredient::create($request->all());

            // Crear transacción inicial de inventario
            InventoryTransaction::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'initial',
                'quantity' => $request->current_stock,
                'unit_cost' => $request->cost_per_unit,
                'total_cost' => $request->current_stock * $request->cost_per_unit,
                'notes' => 'Stock inicial',
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('ingredients.index')
                ->with('success', 'Ingrediente creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al crear el ingrediente: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        $ingredient->load(['supplier', 'inventoryTransactions' => function($query) {
            $query->with('user')->orderBy('created_at', 'desc')->limit(10);
        }]);

        $lowStockIngredients = Ingredient::whereRaw('current_stock <= minimum_stock')
            ->where('id', '!=', $ingredient->id)
            ->limit(5)
            ->get();

        return view('ingredients.show', compact('ingredient', 'lowStockIngredients'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('ingredients.edit', compact('ingredient', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:ingredients,sku,' . $ingredient->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'cost_per_unit' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'current_stock' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        $ingredient->update($request->all());

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingrediente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        // Verificar si el ingrediente está siendo usado en recetas
        if ($ingredient->recipeIngredients()->exists()) {
            return back()->with('error', 'No se puede eliminar el ingrediente porque está siendo usado en recetas.');
        }

        $ingredient->delete();

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingrediente eliminado exitosamente.');
    }

    /**
     * Ajustar stock del ingrediente
     */
    public function adjustStock(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldStock = $ingredient->current_stock;
            $quantity = $request->quantity;

            switch ($request->adjustment_type) {
                case 'add':
                    $newStock = $oldStock + $quantity;
                    $transactionType = 'adjustment_in';
                    break;
                case 'subtract':
                    $newStock = max(0, $oldStock - $quantity);
                    $transactionType = 'adjustment_out';
                    break;
                case 'set':
                    $newStock = $quantity;
                    $transactionType = 'adjustment_set';
                    break;
            }

            $ingredient->update(['current_stock' => $newStock]);

            // Crear transacción de inventario
            InventoryTransaction::create([
                'ingredient_id' => $ingredient->id,
                'type' => $transactionType,
                'quantity' => abs($newStock - $oldStock),
                'unit_cost' => $ingredient->cost_per_unit,
                'total_cost' => abs($newStock - $oldStock) * $ingredient->cost_per_unit,
                'notes' => $request->notes ?: "Ajuste de stock: {$request->adjustment_type}",
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock ajustado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al ajustar el stock: ' . $e->getMessage());
        }
    }

    /**
     * Obtener ingredientes con stock bajo
     */
    public function lowStock()
    {
        $ingredients = Ingredient::with('supplier')
            ->whereRaw('current_stock <= minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->get();

        return view('ingredients.low-stock', compact('ingredients'));
    }
}