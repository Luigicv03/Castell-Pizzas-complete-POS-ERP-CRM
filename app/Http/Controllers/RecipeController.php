<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:recipes.view')->only(['index', 'show']);
        $this->middleware('can:recipes.create')->only(['create', 'store']);
        $this->middleware('can:recipes.edit')->only(['edit', 'update']);
        $this->middleware('can:recipes.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recipe::with(['product', 'recipeIngredients.ingredient']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $recipes = $query->orderBy('name')->paginate(15);
        $products = Product::orderBy('name')->get();

        return view('recipes.index', compact('recipes', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $ingredients = Ingredient::with('supplier')->orderBy('name')->get();
        
        return view('recipes.create', compact('products', 'ingredients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'servings' => 'required|integer|min:1',
            'prep_time' => 'required|integer|min:0',
            'cook_time' => 'required|integer|min:0',
            'instructions' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $recipe = Recipe::create([
                'name' => $request->name,
                'description' => $request->description,
                'product_id' => $request->product_id,
                'servings' => $request->servings,
                'prep_time' => $request->prep_time,
                'cook_time' => $request->cook_time,
                'instructions' => $request->instructions,
            ]);

            // Crear ingredientes de la receta
            foreach ($request->ingredients as $ingredientData) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredientData['ingredient_id'],
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.index')
                ->with('success', 'Receta creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al crear la receta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['product', 'recipeIngredients.ingredient.supplier']);
        
        // Calcular costo total de la receta
        $totalCost = $recipe->recipeIngredients->sum(function($recipeIngredient) {
            return $recipeIngredient->quantity * $recipeIngredient->ingredient->cost_per_unit;
        });

        // Verificar disponibilidad de ingredientes
        $unavailableIngredients = $recipe->recipeIngredients->filter(function($recipeIngredient) {
            return $recipeIngredient->ingredient->current_stock < $recipeIngredient->quantity;
        });

        return view('recipes.show', compact('recipe', 'totalCost', 'unavailableIngredients'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $recipe->load('recipeIngredients');
        $products = Product::orderBy('name')->get();
        $ingredients = Ingredient::with('supplier')->orderBy('name')->get();
        
        return view('recipes.edit', compact('recipe', 'products', 'ingredients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'servings' => 'required|integer|min:1',
            'prep_time' => 'required|integer|min:0',
            'cook_time' => 'required|integer|min:0',
            'instructions' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $recipe->update([
                'name' => $request->name,
                'description' => $request->description,
                'product_id' => $request->product_id,
                'servings' => $request->servings,
                'prep_time' => $request->prep_time,
                'cook_time' => $request->cook_time,
                'instructions' => $request->instructions,
            ]);

            // Eliminar ingredientes existentes
            $recipe->recipeIngredients()->delete();

            // Crear nuevos ingredientes
            foreach ($request->ingredients as $ingredientData) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredientData['ingredient_id'],
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.index')
                ->with('success', 'Receta actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al actualizar la receta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Receta eliminada exitosamente.');
    }

    /**
     * Verificar disponibilidad de ingredientes para una receta
     */
    public function checkAvailability(Recipe $recipe)
    {
        $recipe->load('recipeIngredients.ingredient');
        
        $unavailableIngredients = $recipe->recipeIngredients->filter(function($recipeIngredient) {
            return $recipeIngredient->ingredient->current_stock < $recipeIngredient->quantity;
        });

        if ($unavailableIngredients->isEmpty()) {
            return response()->json([
                'available' => true,
                'message' => 'Todos los ingredientes están disponibles'
            ]);
        }

        return response()->json([
            'available' => false,
            'message' => 'Algunos ingredientes no están disponibles',
            'unavailable_ingredients' => $unavailableIngredients->map(function($item) {
                return [
                    'name' => $item->ingredient->name,
                    'required' => $item->quantity,
                    'available' => $item->ingredient->current_stock,
                    'missing' => $item->quantity - $item->ingredient->current_stock
                ];
            })
        ]);
    }

    /**
     * Calcular costo de una receta
     */
    public function calculateCost(Recipe $recipe)
    {
        $recipe->load('recipeIngredients.ingredient');
        
        $totalCost = $recipe->recipeIngredients->sum(function($recipeIngredient) {
            return $recipeIngredient->quantity * $recipeIngredient->ingredient->cost_per_unit;
        });

        $costPerServing = $totalCost / $recipe->servings;

        return response()->json([
            'total_cost' => $totalCost,
            'cost_per_serving' => $costPerServing,
            'servings' => $recipe->servings
        ]);
    }
}