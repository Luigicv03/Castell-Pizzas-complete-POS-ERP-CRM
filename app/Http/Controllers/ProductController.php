<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:products.view');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'preparation_time' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'cost' => $request->cost,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'preparation_time' => $request->preparation_time ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'product' => $product->load('category')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'preparation_time' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'cost' => $request->cost,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'preparation_time' => $request->preparation_time ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'product' => $product->fresh()->load('category')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Disponibilidad del producto actualizada.',
            'is_active' => $product->is_active
        ]);
    }

    /**
     * Toggle product featured status
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json([
            'success' => true,
            'message' => 'Estado destacado del producto actualizado.',
            'is_featured' => $product->is_featured
        ]);
    }
}
