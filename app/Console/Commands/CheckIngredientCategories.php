<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;

class CheckIngredientCategories extends Command
{
    protected $signature = 'ingredients:check-categories';
    protected $description = 'Verificar categorías de ingredientes';

    public function handle()
    {
        $this->info('Verificando categorías de ingredientes...');
        
        $categories = Category::whereIn('name', ['Ingredientes Tradicionales', 'Ingredientes Premium'])
            ->where('is_active', true)
            ->get();
        
        if ($categories->isEmpty()) {
            $this->error('No se encontraron categorías de ingredientes!');
            $this->line('Categorías existentes:');
            Category::all()->each(function($cat) {
                $this->line("  - {$cat->name}");
            });
        } else {
            foreach ($categories as $cat) {
                $count = Product::where('category_id', $cat->id)->where('is_active', true)->count();
                $this->line("{$cat->name}: {$count} productos");
                
                if ($count > 0) {
                    Product::where('category_id', $cat->id)->where('is_active', true)->take(5)->get()->each(function($product) {
                        $this->line("  - {$product->name} (\${$product->price})");
                    });
                }
            }
        }
        
        return 0;
    }
}

