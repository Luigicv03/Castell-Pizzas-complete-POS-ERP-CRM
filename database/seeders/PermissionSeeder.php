<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Dashboard
            'dashboard.view',
            
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
            
            // Clientes
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            
            // Inventario
            'ingredients.view',
            'ingredients.create',
            'ingredients.edit',
            'ingredients.delete',
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',
            'recipes.view',
            'recipes.create',
            'recipes.edit',
            'recipes.delete',
            'inventory-transactions.view',
            'inventory-transactions.create',
            'inventory-transactions.edit',
            'inventory-transactions.delete',
            
            // Reportes
            'reports.view',
            'reports.export',
            
            // CRM
            'crm.view',
            'crm.segmentation',
            'crm.campaigns',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $manager = Role::create(['name' => 'Manager']);
        $cashier = Role::create(['name' => 'Cashier']);
        $waiter = Role::create(['name' => 'Waiter']);

        // Asignar todos los permisos al Super Admin
        $superAdmin->givePermissionTo(Permission::all());

        // Asignar permisos al Admin (todos excepto algunos críticos)
        $admin->givePermissionTo([
            'dashboard.view',
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view', 'tables.create', 'tables.edit',
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'ingredients.view', 'ingredients.create', 'ingredients.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'recipes.view', 'recipes.create', 'recipes.edit',
            'inventory-transactions.view', 'inventory-transactions.create', 'inventory-transactions.edit',
            'reports.view', 'reports.export',
            'crm.view', 'crm.segmentation', 'crm.campaigns',
        ]);

        // Asignar permisos al Manager
        $manager->givePermissionTo([
            'dashboard.view',
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view', 'tables.create', 'tables.edit',
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'ingredients.view', 'ingredients.create', 'ingredients.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'recipes.view', 'recipes.create', 'recipes.edit',
            'inventory-transactions.view', 'inventory-transactions.create', 'inventory-transactions.edit',
            'reports.view',
            'crm.view', 'crm.segmentation',
        ]);

        // Asignar permisos al Cashier
        $cashier->givePermissionTo([
            'dashboard.view',
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view',
            'products.view',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view',
        ]);

        // Asignar permisos al Waiter
        $waiter->givePermissionTo([
            'dashboard.view',
            'pos.view', 'pos.create', 'pos.edit',
            'tables.view',
            'products.view',
            'customers.view', 'customers.create', 'customers.edit',
        ]);

        // Asignar rol Super Admin al usuario por defecto
        $user = User::first();
        if ($user) {
            $user->assignRole('Super Admin');
        }
    }
}