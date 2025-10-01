<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // POS
            'pos.view',
            'pos.create',
            'pos.edit',
            'pos.delete',
            
            // Mesas
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',
            
            // Productos
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            
            // Categorías
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            
            // Órdenes
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.cancel',
            
            // Pagos
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            
            // Clientes
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            
            // Inventario
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            
            // Ingredientes
            'ingredients.view',
            'ingredients.create',
            'ingredients.edit',
            'ingredients.delete',
            
            // Proveedores
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',
            
            // Recetas
            'recipes.view',
            'recipes.create',
            'recipes.edit',
            'recipes.delete',
            
            // Reportes
            'reports.view',
            'reports.sales',
            'reports.inventory',
            'reports.financial',
            'reports.export',
            
            // Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Roles y Permisos
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Configuración
            'settings.view',
            'settings.edit',
            
            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles (usar firstOrCreate para evitar duplicados)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $waiter = Role::firstOrCreate(['name' => 'Waiter']);

        // Limpiar permisos anteriores
        $superAdmin->syncPermissions([]);
        $admin->syncPermissions([]);
        $manager->syncPermissions([]);
        $cashier->syncPermissions([]);
        $waiter->syncPermissions([]);

        // Asignar permisos a roles
        
        // Super Admin: Acceso total a todo
        $superAdmin->givePermissionTo(Permission::all());

        // Admin: Acceso total a todo
        $admin->givePermissionTo(Permission::all());

        // Manager (Gerente): POS, Mesas, Productos, Clientes e Inventario
        $manager->givePermissionTo([
            // POS
            'pos.view', 'pos.create', 'pos.edit', 'pos.delete',
            // Mesas
            'tables.view', 'tables.create', 'tables.edit', 'tables.delete',
            // Productos
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            // Órdenes
            'orders.view', 'orders.create', 'orders.edit', 'orders.cancel', 'orders.delete',
            // Pagos
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            // Clientes
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            // Inventario completo
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'ingredients.view', 'ingredients.create', 'ingredients.edit', 'ingredients.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'recipes.view', 'recipes.create', 'recipes.edit', 'recipes.delete',
        ]);

        // Cashier (Cajero): Solo POS y Mesas (sin acceso a Dashboard, Clientes, Productos)
        $cashier->givePermissionTo([
            // POS
            'pos.view', 'pos.create', 'pos.edit',
            // Mesas
            'tables.view', 'tables.create', 'tables.edit',
            // Órdenes
            'orders.view', 'orders.create', 'orders.edit',
            // Pagos
            'payments.view', 'payments.create', 'payments.edit',
        ]);

        // Waiter (Mesero): Solo POS y Mesas
        $waiter->givePermissionTo([
            // POS
            'pos.view', 'pos.create', 'pos.edit',
            // Mesas
            'tables.view', 'tables.create', 'tables.edit',
            // Órdenes
            'orders.view', 'orders.create', 'orders.edit',
        ]);
    }
}
