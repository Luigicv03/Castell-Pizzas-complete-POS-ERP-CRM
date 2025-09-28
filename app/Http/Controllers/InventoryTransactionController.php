<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:inventory.view')->only(['index', 'show']);
        $this->middleware('can:inventory.create')->only(['create', 'store']);
        $this->middleware('can:inventory.edit')->only(['edit', 'update']);
        $this->middleware('can:inventory.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['ingredient.supplier', 'user']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('ingredient', function($ingredientQuery) use ($search) {
                    $ingredientQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('sku', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        $ingredients = Ingredient::orderBy('name')->get();

        $transactionTypes = [
            'initial' => 'Stock Inicial',
            'purchase' => 'Compra',
            'adjustment_in' => 'Ajuste Entrada',
            'adjustment_out' => 'Ajuste Salida',
            'adjustment_set' => 'Ajuste Manual',
            'consumption' => 'Consumo',
            'waste' => 'Desperdicio',
            'return' => 'Devolución',
        ];

        return view('inventory-transactions.index', compact('transactions', 'ingredients', 'transactionTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ingredients = Ingredient::with('supplier')->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $transactionTypes = [
            'purchase' => 'Compra',
            'adjustment_in' => 'Ajuste Entrada',
            'adjustment_out' => 'Ajuste Salida',
            'adjustment_set' => 'Ajuste Manual',
            'waste' => 'Desperdicio',
            'return' => 'Devolución',
        ];

        return view('inventory-transactions.create', compact('ingredients', 'suppliers', 'transactionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|string|in:purchase,adjustment_in,adjustment_out,adjustment_set,waste,return',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $ingredient = Ingredient::findOrFail($request->ingredient_id);
            $oldStock = $ingredient->current_stock;
            $quantity = $request->quantity;

            // Calcular nuevo stock según el tipo de transacción
            switch ($request->type) {
                case 'purchase':
                case 'adjustment_in':
                case 'return':
                    $newStock = $oldStock + $quantity;
                    break;
                case 'adjustment_out':
                case 'waste':
                    $newStock = max(0, $oldStock - $quantity);
                    break;
                case 'adjustment_set':
                    $newStock = $quantity;
                    break;
                default:
                    $newStock = $oldStock;
            }

            // Actualizar stock del ingrediente
            $ingredient->update(['current_stock' => $newStock]);

            // Crear transacción
            $transaction = InventoryTransaction::create([
                'ingredient_id' => $request->ingredient_id,
                'type' => $request->type,
                'quantity' => $quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $quantity * $request->unit_cost,
                'supplier_id' => $request->supplier_id,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('inventory-transactions.index')
                ->with('success', 'Transacción creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al crear la transacción: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->load(['ingredient.supplier', 'user', 'supplier']);
        
        return view('inventory-transactions.show', compact('inventoryTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryTransaction $inventoryTransaction)
    {
        $ingredients = Ingredient::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $transactionTypes = [
            'purchase' => 'Compra',
            'adjustment_in' => 'Ajuste Entrada',
            'adjustment_out' => 'Ajuste Salida',
            'adjustment_set' => 'Ajuste Manual',
            'waste' => 'Desperdicio',
            'return' => 'Devolución',
        ];

        return view('inventory-transactions.edit', compact('inventoryTransaction', 'ingredients', 'suppliers', 'transactionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        $request->validate([
            'type' => 'required|string|in:purchase,adjustment_in,adjustment_out,adjustment_set,waste,return',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Nota: En un sistema real, actualizar transacciones históricas podría ser complejo
        // ya que afectaría el stock actual. Por simplicidad, solo actualizamos los datos de la transacción.
        
        $inventoryTransaction->update([
            'type' => $request->type,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $request->quantity * $request->unit_cost,
            'supplier_id' => $request->supplier_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory-transactions.index')
            ->with('success', 'Transacción actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        // Nota: En un sistema real, eliminar transacciones históricas podría afectar
        // el stock actual. Por simplicidad, solo permitimos eliminar transacciones recientes.
        
        if ($inventoryTransaction->created_at->diffInDays(now()) > 7) {
            return back()->with('error', 'No se pueden eliminar transacciones de más de 7 días.');
        }

        $inventoryTransaction->delete();

        return redirect()->route('inventory-transactions.index')
            ->with('success', 'Transacción eliminada exitosamente.');
    }

    /**
     * Reporte de movimientos de inventario
     */
    public function report(Request $request)
    {
        $query = InventoryTransaction::with(['ingredient.supplier', 'user']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $ingredients = Ingredient::orderBy('name')->get();

        // Estadísticas
        $totalTransactions = $transactions->count();
        $totalCost = $transactions->sum('total_cost');
        $totalQuantity = $transactions->sum('quantity');

        $transactionsByType = $transactions->groupBy('type')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_cost' => $group->sum('total_cost'),
                'total_quantity' => $group->sum('quantity'),
            ];
        });

        return view('inventory-transactions.report', compact(
            'transactions', 
            'ingredients', 
            'totalTransactions', 
            'totalCost', 
            'totalQuantity', 
            'transactionsByType'
        ));
    }
}