<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:tables.view');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las mesas ordenadas por zona y nombre
        $tables = Table::orderBy('zone')->orderBy('name')->get();
        
        // Sincronizar el estado de las mesas con las órdenes activas
        $this->syncTableStatuses($tables);
        
        // Agrupar mesas por zona
        $tablesByZone = $tables->groupBy('zone');
        
        return view('tables.index', compact('tables', 'tablesByZone'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:free,occupied,reserved,pending_payment',
        ]);

        Table::create($request->all());

        return redirect()->route('tables.index')
            ->with('success', 'Mesa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        return view('tables.show', compact('table'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:free,occupied,reserved,pending_payment',
        ]);

        $table->update($request->all());

        return redirect()->route('tables.index')
            ->with('success', 'Mesa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Mesa eliminada exitosamente.');
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:free,occupied,reserved,pending_payment',
        ]);

        $table->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de mesa actualizado exitosamente.',
            'table' => $table->fresh()
        ]);
    }

    /**
     * Sincronizar el estado de las mesas con las órdenes activas
     */
    private function syncTableStatuses($tables)
    {
        foreach ($tables as $table) {
            $table->syncStatus();
        }
    }

    /**
     * API endpoint para sincronizar estados de mesas
     */
    public function syncStatuses()
    {
        $tables = Table::all();
        $updated = 0;
        
        foreach ($tables as $table) {
            $oldStatus = $table->status;
            $table->syncStatus();
            
            if ($table->status !== $oldStatus) {
                $updated++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Estados sincronizados. {$updated} mesas actualizadas.",
            'updated_count' => $updated
        ]);
    }
}