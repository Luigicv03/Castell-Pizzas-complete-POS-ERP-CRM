<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\InventoryTransaction;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Crear proveedores
        $suppliers = [
            [
                'name' => 'Distribuidora de Alimentos S.A.',
                'contact_person' => 'María González',
                'email' => 'maria@distribuidora.com',
                'phone' => '+58 212 555-0101',
                'address' => 'Av. Principal 123',
                'city' => 'Caracas',
                'state' => 'Distrito Capital',
                'postal_code' => '1010',
                'country' => 'Venezuela',
                'payment_terms' => '30 días',
                'notes' => 'Proveedor principal de ingredientes frescos',
                'is_active' => true,
            ],
            [
                'name' => 'Importadora de Especias',
                'contact_person' => 'Carlos Rodríguez',
                'email' => 'carlos@especias.com',
                'phone' => '+58 212 555-0202',
                'address' => 'Calle Comercio 456',
                'city' => 'Valencia',
                'state' => 'Carabobo',
                'postal_code' => '2001',
                'country' => 'Venezuela',
                'payment_terms' => '15 días',
                'notes' => 'Especialistas en especias y condimentos',
                'is_active' => true,
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $supplierData) {
            $createdSuppliers[] = Supplier::create($supplierData);
        }

        // Crear ingredientes básicos
        $ingredients = [
            [
                'name' => 'Harina de Trigo',
                'description' => 'Harina de trigo para masa de pizza',
                'sku' => 'HAR-001',
                'supplier_id' => $createdSuppliers[0]->id,
                'unit' => 'kg',
                'cost_per_unit' => 2.50,
                'minimum_stock' => 50,
                'current_stock' => 75,
                'location' => 'Almacén A - Estante 1',
            ],
            [
                'name' => 'Mozzarella',
                'description' => 'Queso mozzarella para pizza',
                'sku' => 'MOZ-001',
                'supplier_id' => $createdSuppliers[0]->id,
                'unit' => 'kg',
                'cost_per_unit' => 15.00,
                'minimum_stock' => 20,
                'current_stock' => 35,
                'location' => 'Refrigerador 3',
            ],
            [
                'name' => 'Tomates Frescos',
                'description' => 'Tomates frescos para salsa',
                'sku' => 'TOM-001',
                'supplier_id' => $createdSuppliers[0]->id,
                'unit' => 'kg',
                'cost_per_unit' => 3.50,
                'minimum_stock' => 30,
                'current_stock' => 45,
                'location' => 'Refrigerador 2',
            ],
            [
                'name' => 'Chorizo',
                'description' => 'Chorizo español picante',
                'sku' => 'CHO-001',
                'supplier_id' => $createdSuppliers[1]->id,
                'unit' => 'kg',
                'cost_per_unit' => 16.00,
                'minimum_stock' => 6,
                'current_stock' => 4, // Stock bajo para probar alertas
                'location' => 'Refrigerador 4',
            ],
        ];

        $createdIngredients = [];
        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::create($ingredientData);
            $createdIngredients[] = $ingredient;

            // Crear transacción inicial
            InventoryTransaction::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'initial',
                'quantity' => $ingredient->current_stock,
                'unit' => $ingredient->unit,
                'cost_per_unit' => $ingredient->cost_per_unit,
                'total_cost' => $ingredient->current_stock * $ingredient->cost_per_unit,
                'notes' => 'Stock inicial del sistema',
                'user_id' => 1,
            ]);
        }

        $this->command->info('Datos de inventario creados: ' . count($createdSuppliers) . ' proveedores, ' . count($createdIngredients) . ' ingredientes');
    }
}