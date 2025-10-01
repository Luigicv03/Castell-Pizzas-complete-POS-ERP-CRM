<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CoffeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando categoría y productos de café...');

        // Crear la categoría de Café
        $category = Category::firstOrCreate(
            ['name' => 'Café'],
            [
                'description' => 'Variedad de cafés especiales',
                'is_active' => true,
                'sort_order' => 10,
            ]
        );

        $this->command->info("✓ Categoría: Café");

        // Lista de cafés con sus precios
        $coffees = [
            ['name' => 'Árabe', 'price' => 1.5],
            ['name' => 'Americano', 'price' => 2.0],
            ['name' => 'Cappuccino', 'price' => 2.5],
            ['name' => 'Espresso Romano', 'price' => 2.5],
            ['name' => 'Latte Leche', 'price' => 3.0],
            ['name' => 'Macchiato Manchado', 'price' => 2.5],
            ['name' => 'Espresso', 'price' => 2.0],
            ['name' => 'Doppio Doble', 'price' => 3.0],
            ['name' => 'Irlandés', 'price' => 5.0],
            ['name' => 'Caravel Macchiato', 'price' => 3.5],
            ['name' => 'Viena', 'price' => 4.0],
            ['name' => 'Breve', 'price' => 4.0],
            ['name' => 'Lungo Largo', 'price' => 2.0],
            ['name' => 'Affogato Ahogado', 'price' => 4.0],
            ['name' => 'Bombón', 'price' => 3.0],
            ['name' => 'Caribeño', 'price' => 2.5],
            ['name' => 'Amaretto', 'price' => 5.0],
            ['name' => 'Ristretto Restringido', 'price' => 2.0],
            ['name' => 'Hawaiano', 'price' => 3.5],
            ['name' => 'Cubano', 'price' => 2.5],
            ['name' => 'Panna', 'price' => 4.0],
            ['name' => 'Latte Vainilla', 'price' => 3.0],
            ['name' => 'Cappuccino Árabe', 'price' => 3.0],
            ['name' => 'Pistacho', 'price' => 4.0],
        ];

        // Crear los productos
        foreach ($coffees as $coffee) {
            $product = Product::firstOrCreate(
                [
                    'name' => $coffee['name'],
                    'category_id' => $category->id,
                ],
                [
                    'description' => 'Café ' . $coffee['name'],
                    'price' => $coffee['price'],
                    'cost' => $coffee['price'] * 0.35, // 35% del precio como costo estimado
                    'is_active' => true,
                    'preparation_time' => 3, // 3 minutos para preparar café
                ]
            );

            $this->command->line("  • {$coffee['name']} - \${$coffee['price']}");
        }

        $this->command->newLine();
        $this->command->info('✓ Todos los cafés han sido creados exitosamente!');
        $this->command->info('✓ Total: ' . count($coffees) . ' productos de café');
    }
}

