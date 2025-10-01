<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class BeveragesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías de Bebidas
        $categories = [
            'Refrescos 1.5 Lts' => [
                ['name' => 'Pepsi Cola 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Coca Cola 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Fanta 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Naranja 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Chinoto 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Uva 1.5L', 'price' => 2.5, 'cost' => 1.0],
                ['name' => 'Piña 1.5L', 'price' => 2.5, 'cost' => 1.0],
            ],
            'Refrescos 355ml' => [
                ['name' => 'Pepsi Cola 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Coca Cola 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Fanta 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Naranja 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Chinoto 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Uva 355ml', 'price' => 1.5, 'cost' => 0.6],
                ['name' => 'Piña 355ml', 'price' => 1.5, 'cost' => 0.6],
            ],
            'Refrescos Lata' => [
                ['name' => 'Pepsi Lata', 'price' => 2.0, 'cost' => 0.8],
                ['name' => 'Pepsi Zero Lata', 'price' => 2.0, 'cost' => 0.8],
                ['name' => 'Pepsi Light Lata', 'price' => 2.0, 'cost' => 0.8],
            ],
            'Agua' => [
                ['name' => 'Agua Mineral 335ml', 'price' => 0.5, 'cost' => 0.2],
                ['name' => 'Agua Mineral 600ml', 'price' => 1.0, 'cost' => 0.4],
                ['name' => 'Agua Saborizada Manzana', 'price' => 2.0, 'cost' => 0.8],
            ],
            'Té Naturales' => [
                ['name' => 'Té Verde', 'price' => 2.0, 'cost' => 0.5],
                ['name' => 'Té Negro', 'price' => 2.0, 'cost' => 0.5],
                ['name' => 'Flor de Jamaica', 'price' => 2.0, 'cost' => 0.5],
                ['name' => 'Té Matcha', 'price' => 3.0, 'cost' => 1.0],
            ],
        ];

        $this->command->info('Creando categorías y productos de bebidas...');

        foreach ($categories as $categoryName => $products) {
            // Crear o encontrar la categoría
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                [
                    'description' => "Categoría de {$categoryName}",
                    'is_active' => true,
                ]
            );

            $this->command->info("✓ Categoría: {$categoryName}");

            // Crear los productos de esta categoría
            foreach ($products as $productData) {
                $product = Product::firstOrCreate(
                    [
                        'name' => $productData['name'],
                        'category_id' => $category->id,
                    ],
                    [
                        'description' => $productData['name'],
                        'price' => $productData['price'],
                        'cost' => $productData['cost'],
                        'is_active' => true,
                        'preparation_time' => 2, // Bebidas se preparan rápido
                    ]
                );

                $this->command->line("  • {$productData['name']} - \${$productData['price']}");
            }

            $this->command->newLine();
        }

        $this->command->info('✓ Todas las bebidas han sido creadas exitosamente!');
    }
}

