<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class TeaContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando envase para té...');

        // Buscar o crear la categoría de Extras
        $extrasCategory = Category::firstOrCreate(
            ['name' => 'Extras'],
            [
                'description' => 'Productos adicionales y complementos',
                'is_active' => true,
                'sort_order' => 100,
            ]
        );

        // Crear el producto de envase para té
        $container = Product::firstOrCreate(
            [
                'name' => 'Envase para Té',
                'category_id' => $extrasCategory->id,
            ],
            [
                'description' => 'Envase térmico para llevar té',
                'price' => 0.80,
                'cost' => 0.30,
                'is_active' => true,
                'preparation_time' => 1,
            ]
        );

        $this->command->info("✓ Envase para Té creado - \$0.80");
        $this->command->info('✓ Envase para Té configurado exitosamente!');
    }
}

