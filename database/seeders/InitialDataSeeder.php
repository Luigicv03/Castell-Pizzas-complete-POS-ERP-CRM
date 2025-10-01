<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Table;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario super administrador
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@pizzeria.com',
            'password' => Hash::make('password'),
            'phone' => '+584121234567',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador General',
            'email' => 'manager@pizzeria.com',
            'password' => Hash::make('password'),
            'phone' => '+584121234568',
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Crear usuario cajero
        $cashier = User::create([
            'name' => 'Cajero Principal',
            'email' => 'cashier@pizzeria.com',
            'password' => Hash::make('password'),
            'phone' => '+584121234569',
            'is_active' => true,
        ]);
        $cashier->assignRole('Cashier');

        // Crear usuario mesero
        $waiter = User::create([
            'name' => 'Mesero Principal',
            'email' => 'waiter@pizzeria.com',
            'password' => Hash::make('password'),
            'phone' => '+584121234570',
            'is_active' => true,
        ]);
        $waiter->assignRole('Waiter');

        // Crear categorías de productos
        $categories = [
            [
                'name' => 'Pizzas',
                'description' => 'Pizzas artesanales con ingredientes frescos',
                'color' => '#EF4444',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bebidas',
                'description' => 'Bebidas frías y calientes',
                'color' => '#3B82F6',
                'sort_order' => 2,
            ],
            [
                'name' => 'Postres',
                'description' => 'Postres caseros y helados',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'Entradas',
                'description' => 'Aperitivos y entradas',
                'color' => '#10B981',
                'sort_order' => 4,
            ],
            [
                'name' => 'Combos',
                'description' => 'Combos especiales',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Crear mesas
        for ($i = 1; $i <= 20; $i++) {
            Table::create([
                'name' => "Mesa {$i}",
                'capacity' => rand(2, 8),
                'position_x' => rand(0, 100),
                'position_y' => rand(0, 100),
                'status' => 'free',
                'is_active' => true,
            ]);
        }

        // Crear proveedores
        $suppliers = [
            [
                'name' => 'Distribuidora de Alimentos S.A.',
                'contact_person' => 'Juan Pérez',
                'email' => 'ventas@distribuidora.com',
                'phone' => '+584121111111',
                'address' => 'Av. Principal, Caracas',
                'tax_id' => 'J-12345678-9',
                'is_active' => true,
            ],
            [
                'name' => 'Carnes Premium',
                'contact_person' => 'María González',
                'email' => 'pedidos@carnespremium.com',
                'phone' => '+584122222222',
                'address' => 'Zona Industrial, Valencia',
                'tax_id' => 'J-87654321-0',
                'is_active' => true,
            ],
            [
                'name' => 'Verduras Frescas',
                'contact_person' => 'Carlos Rodríguez',
                'email' => 'info@verdurasfrescas.com',
                'phone' => '+584123333333',
                'address' => 'Mercado Municipal, Maracay',
                'tax_id' => 'J-11223344-5',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
