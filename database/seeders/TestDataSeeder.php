<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PermissionSeeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario por defecto
        User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@pizzeria.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Ejecutar seeder de permisos
        $this->call(PermissionSeeder::class);

        // Crear categorías
        $categories = [
            ['name' => 'Pizzas', 'description' => 'Pizzas tradicionales y especiales', 'is_active' => true],
            ['name' => 'Bebidas', 'description' => 'Bebidas frías y calientes', 'is_active' => true],
            ['name' => 'Postres', 'description' => 'Postres y dulces', 'is_active' => true],
            ['name' => 'Entradas', 'description' => 'Aperitivos y entradas', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Crear productos
        $products = [
            ['name' => 'Pizza Margherita', 'description' => 'Pizza clásica con tomate, mozzarella y albahaca', 'price' => 12.99, 'category_id' => 1, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Pizza Pepperoni', 'description' => 'Pizza con pepperoni y queso mozzarella', 'price' => 15.99, 'category_id' => 1, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Pizza Hawaiana', 'description' => 'Pizza con jamón y piña', 'price' => 16.99, 'category_id' => 1, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Coca Cola', 'description' => 'Bebida gaseosa 500ml', 'price' => 2.50, 'category_id' => 2, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Agua', 'description' => 'Agua mineral 500ml', 'price' => 1.50, 'category_id' => 2, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Tiramisu', 'description' => 'Postre italiano clásico', 'price' => 6.99, 'category_id' => 3, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Alitas de Pollo', 'description' => 'Alitas de pollo con salsa BBQ', 'price' => 8.99, 'category_id' => 4, 'is_active' => true, 'is_featured' => false],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Crear mesas
        for ($i = 1; $i <= 12; $i++) {
            $statuses = ['free', 'occupied', 'reserved', 'pending_payment'];
            $capacities = [2, 4, 6, 8];
            
            Table::create([
                'name' => 'Mesa ' . $i,
                'capacity' => $capacities[array_rand($capacities)],
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        // Crear clientes
        $customers = [
            ['name' => 'Juan Pérez', 'email' => 'juan@email.com', 'phone' => '555-0001', 'address' => 'Calle Principal 123'],
            ['name' => 'María García', 'email' => 'maria@email.com', 'phone' => '555-0002', 'address' => 'Avenida Central 456'],
            ['name' => 'Carlos López', 'email' => 'carlos@email.com', 'phone' => '555-0003', 'address' => 'Plaza Mayor 789'],
            ['name' => 'Ana Martínez', 'email' => 'ana@email.com', 'phone' => '555-0004', 'address' => 'Calle Secundaria 321'],
            ['name' => 'Luis Rodríguez', 'email' => 'luis@email.com', 'phone' => '555-0005', 'address' => 'Boulevard Norte 654'],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Crear órdenes de ejemplo
        $customers = Customer::all();
        $products = Product::all();
        $tables = Table::all();

        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $table = $tables->random();
            $statuses = ['pending', 'preparing', 'ready', 'delivered'];
            
            $order = Order::create([
                'order_number' => 'ORD-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'daily_number' => $i + 1, // Número diario secuencial
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'user_id' => 1, // Usuario por defecto
                'status' => $statuses[array_rand($statuses)],
                'type' => 'dine_in',
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
            ]);

            // Agregar items a la orden
            $numItems = rand(1, 4);
            $subtotal = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            $taxAmount = $subtotal * 0.16; // 16% IVA
            $totalAmount = $subtotal + $taxAmount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }
    }
}
