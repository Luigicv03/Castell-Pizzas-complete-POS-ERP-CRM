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
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $superAdmin = Role::create(['name' => 'super_admin']);
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $cashier = Role::create(['name' => 'cashier']);
        $waiter = Role::create(['name' => 'waiter']);

        // Asignar permisos a roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view', 'tables.create', 'tables.edit',
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'orders.view', 'orders.create', 'orders.edit', 'orders.cancel',
            'payments.view', 'payments.create', 'payments.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'ingredients.view', 'ingredients.create', 'ingredients.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'recipes.view', 'recipes.create', 'recipes.edit',
            'reports.view', 'reports.sales', 'reports.inventory',
            'users.view', 'users.create', 'users.edit',
            'dashboard.view',
        ]);

        $manager->givePermissionTo([
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view', 'tables.create', 'tables.edit',
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'orders.view', 'orders.create', 'orders.edit', 'orders.cancel',
            'payments.view', 'payments.create', 'payments.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'ingredients.view', 'ingredients.create', 'ingredients.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'recipes.view', 'recipes.create', 'recipes.edit',
            'reports.view', 'reports.sales', 'reports.inventory', 'reports.financial',
            'dashboard.view',
        ]);

        $cashier->givePermissionTo([
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view',
            'products.view',
            'categories.view',
            'orders.view', 'orders.create', 'orders.edit',
            'payments.view', 'payments.create', 'payments.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view', 'reports.sales',
            'dashboard.view',
        ]);

        $waiter->givePermissionTo([
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view',
            'products.view',
            'categories.view',
            'orders.view', 'orders.create', 'orders.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'dashboard.view',
        ]);
    }
}
