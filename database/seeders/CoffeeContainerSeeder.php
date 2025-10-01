<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CoffeeContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando envase para café...');

        // Buscar o crear la categoría de Extras
        $extrasCategory = Category::firstOrCreate(
            ['name' => 'Extras'],
            [
                'description' => 'Productos adicionales y complementos',
                'is_active' => true,
                'sort_order' => 100,
            ]
        );

        // Crear el producto de envase para café
        $container = Product::firstOrCreate(
            [
                'name' => 'Envase para Café',
                'category_id' => $extrasCategory->id,
            ],
            [
                'description' => 'Vaso térmico para café con tapa',
                'price' => 0.50,
                'cost' => 0.20,
                'is_active' => true,
                'preparation_time' => 1,
            ]
        );

        $this->command->info("✓ Envase para Café creado - \$0.50");
        $this->command->info('✓ Envase para Café configurado exitosamente!');
    }
}

