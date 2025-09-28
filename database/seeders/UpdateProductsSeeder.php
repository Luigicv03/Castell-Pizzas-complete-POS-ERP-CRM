<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class UpdateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar productos existentes
        Product::truncate();
        
        // Obtener categorías
        $pizzaCategory = Category::where('name', 'Pizzas')->first();
        $ingredientsCategory = Category::where('name', 'Ingredientes Adicionales')->first();
        $boxesCategory = Category::where('name', 'Cajas de Pizza')->first();
        $pastichosCategory = Category::where('name', 'Pastichos')->first();

        // Pizzas con precios y costos reales
        $pizzas = [
            // Pizzas Pequeñas (8")
            [
                'name' => 'Margherita Pequeña (8")',
                'description' => 'Salsa de tomate, mozzarella, albahaca fresca',
                'price' => 8.50,
                'cost' => 3.20,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-MARG-S',
                'preparation_time' => 15,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pepperoni Pequeña (8")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni',
                'price' => 9.50,
                'cost' => 3.80,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-PEPP-S',
                'preparation_time' => 15,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hawaiana Pequeña (8")',
                'description' => 'Salsa de tomate, mozzarella, jamón, piña',
                'price' => 10.50,
                'cost' => 4.20,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-HAWA-S',
                'preparation_time' => 15,
                'sort_order' => 3,
            ],
            [
                'name' => 'Suprema Pequeña (8")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 11.50,
                'cost' => 4.60,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-SUPR-S',
                'preparation_time' => 18,
                'sort_order' => 4,
            ],

            // Pizzas Medianas (10")
            [
                'name' => 'Margherita Mediana (10")',
                'description' => 'Salsa de tomate, mozzarella, albahaca fresca',
                'price' => 12.50,
                'cost' => 4.50,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-MARG-M',
                'preparation_time' => 15,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pepperoni Mediana (10")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni',
                'price' => 13.50,
                'cost' => 5.20,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-PEPP-M',
                'preparation_time' => 15,
                'sort_order' => 6,
            ],
            [
                'name' => 'Hawaiana Mediana (10")',
                'description' => 'Salsa de tomate, mozzarella, jamón, piña',
                'price' => 14.50,
                'cost' => 5.80,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-HAWA-M',
                'preparation_time' => 15,
                'sort_order' => 7,
            ],
            [
                'name' => 'Suprema Mediana (10")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 15.50,
                'cost' => 6.20,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-SUPR-M',
                'preparation_time' => 18,
                'sort_order' => 8,
            ],

            // Pizzas Grandes (12")
            [
                'name' => 'Margherita Grande (12")',
                'description' => 'Salsa de tomate, mozzarella, albahaca fresca',
                'price' => 16.50,
                'cost' => 6.20,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-MARG-L',
                'preparation_time' => 15,
                'sort_order' => 9,
            ],
            [
                'name' => 'Pepperoni Grande (12")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni',
                'price' => 17.50,
                'cost' => 7.00,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-PEPP-L',
                'preparation_time' => 15,
                'sort_order' => 10,
            ],
            [
                'name' => 'Hawaiana Grande (12")',
                'description' => 'Salsa de tomate, mozzarella, jamón, piña',
                'price' => 18.50,
                'cost' => 7.40,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-HAWA-L',
                'preparation_time' => 15,
                'sort_order' => 11,
            ],
            [
                'name' => 'Suprema Grande (12")',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 19.50,
                'cost' => 7.80,
                'category_id' => $pizzaCategory->id,
                'sku' => 'PIZ-SUPR-L',
                'preparation_time' => 18,
                'sort_order' => 12,
            ],
        ];

        // Ingredientes Adicionales con precios y costos
        $ingredients = [
            [
                'name' => 'Queso Extra Pequeña',
                'description' => 'Porción extra de mozzarella para pizza pequeña',
                'price' => 1.50,
                'cost' => 0.60,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-QUE-S',
                'preparation_time' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Queso Extra Mediana',
                'description' => 'Porción extra de mozzarella para pizza mediana',
                'price' => 2.00,
                'cost' => 0.80,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-QUE-M',
                'preparation_time' => 0,
                'sort_order' => 2,
            ],
            [
                'name' => 'Queso Extra Grande',
                'description' => 'Porción extra de mozzarella para pizza grande',
                'price' => 2.50,
                'cost' => 1.00,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-QUE-L',
                'preparation_time' => 0,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pepperoni Extra Pequeña',
                'description' => 'Porción extra de pepperoni para pizza pequeña',
                'price' => 2.00,
                'cost' => 0.80,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-PEP-S',
                'preparation_time' => 0,
                'sort_order' => 4,
            ],
            [
                'name' => 'Pepperoni Extra Mediana',
                'description' => 'Porción extra de pepperoni para pizza mediana',
                'price' => 2.50,
                'cost' => 1.00,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-PEP-M',
                'preparation_time' => 0,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pepperoni Extra Grande',
                'description' => 'Porción extra de pepperoni para pizza grande',
                'price' => 3.00,
                'cost' => 1.20,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-PEP-L',
                'preparation_time' => 0,
                'sort_order' => 6,
            ],
            [
                'name' => 'Jamón Extra Pequeña',
                'description' => 'Porción extra de jamón para pizza pequeña',
                'price' => 1.80,
                'cost' => 0.72,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-JAM-S',
                'preparation_time' => 0,
                'sort_order' => 7,
            ],
            [
                'name' => 'Jamón Extra Mediana',
                'description' => 'Porción extra de jamón para pizza mediana',
                'price' => 2.20,
                'cost' => 0.88,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-JAM-M',
                'preparation_time' => 0,
                'sort_order' => 8,
            ],
            [
                'name' => 'Jamón Extra Grande',
                'description' => 'Porción extra de jamón para pizza grande',
                'price' => 2.60,
                'cost' => 1.04,
                'category_id' => $ingredientsCategory->id,
                'sku' => 'ING-JAM-L',
                'preparation_time' => 0,
                'sort_order' => 9,
            ],
        ];

        // Cajas de Pizza
        $boxes = [
            [
                'name' => 'Caja Pequeña (8")',
                'description' => 'Caja para pizza pequeña',
                'price' => 0.50,
                'cost' => 0.20,
                'category_id' => $boxesCategory->id,
                'sku' => 'CAJ-S',
                'preparation_time' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Caja Mediana (10")',
                'description' => 'Caja para pizza mediana',
                'price' => 0.60,
                'cost' => 0.24,
                'category_id' => $boxesCategory->id,
                'sku' => 'CAJ-M',
                'preparation_time' => 0,
                'sort_order' => 2,
            ],
            [
                'name' => 'Caja Grande (12")',
                'description' => 'Caja para pizza grande',
                'price' => 0.70,
                'cost' => 0.28,
                'category_id' => $boxesCategory->id,
                'sku' => 'CAJ-L',
                'preparation_time' => 0,
                'sort_order' => 3,
            ],
        ];

        // Pastichos
        $pastichos = [
            [
                'name' => 'Pasticho Pequeño',
                'description' => 'Pasticho individual pequeño',
                'price' => 6.50,
                'cost' => 2.60,
                'category_id' => $pastichosCategory->id,
                'sku' => 'PAS-S',
                'preparation_time' => 20,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pasticho Mediano',
                'description' => 'Pasticho mediano para 2 personas',
                'price' => 9.50,
                'cost' => 3.80,
                'category_id' => $pastichosCategory->id,
                'sku' => 'PAS-M',
                'preparation_time' => 25,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pasticho Grande',
                'description' => 'Pasticho grande familiar',
                'price' => 12.50,
                'cost' => 5.00,
                'category_id' => $pastichosCategory->id,
                'sku' => 'PAS-L',
                'preparation_time' => 30,
                'sort_order' => 3,
            ],
        ];

        // Insertar todos los productos
        $allProducts = array_merge($pizzas, $ingredients, $boxes, $pastichos);
        
        foreach ($allProducts as $productData) {
            Product::create($productData);
        }

        $this->command->info('Productos actualizados exitosamente:');
        $this->command->info('- ' . count($pizzas) . ' Pizzas');
        $this->command->info('- ' . count($ingredients) . ' Ingredientes Adicionales');
        $this->command->info('- ' . count($boxes) . ' Cajas de Pizza');
        $this->command->info('- ' . count($pastichos) . ' Pastichos');
        $this->command->info('Total: ' . count($allProducts) . ' productos');
    }
}