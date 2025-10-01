<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class FixUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige los roles de los usuarios existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Corrigiendo roles de usuarios...');

        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('✓ Caché de permisos limpiado');

        // Mapeo de roles antiguos a nuevos
        $roleMapping = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'cashier' => 'Cashier',
            'waiter' => 'Waiter',
        ];

        $users = User::with('roles')->get();
        $updated = 0;

        foreach ($users as $user) {
            foreach ($user->roles as $role) {
                $oldRoleName = $role->name;
                
                // Si el rol está en el mapeo, actualizarlo
                if (array_key_exists($oldRoleName, $roleMapping)) {
                    $newRoleName = $roleMapping[$oldRoleName];
                    
                    // Verificar que el nuevo rol exista
                    $newRole = Role::where('name', $newRoleName)->first();
                    
                    if ($newRole) {
                        // Remover rol antiguo y asignar nuevo rol
                        $user->removeRole($oldRoleName);
                        $user->assignRole($newRoleName);
                        
                        $this->info("✓ Usuario '{$user->name}' actualizado de '{$oldRoleName}' a '{$newRoleName}'");
                        $updated++;
                    }
                }
            }
        }

        $this->info("\n✓ Proceso completado. {$updated} usuario(s) actualizado(s).");
        $this->info('✓ Por favor, cierra sesión y vuelve a iniciar sesión para ver los cambios.');
        
        return 0;
    }
}

