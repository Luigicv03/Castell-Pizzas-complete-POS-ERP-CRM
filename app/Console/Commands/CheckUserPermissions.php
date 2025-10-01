<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserPermissions extends Command
{
    protected $signature = 'users:check-permissions {email}';
    protected $description = 'Verifica los permisos de un usuario específico';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->with('roles.permissions', 'permissions')->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado");
            return 1;
        }
        
        $this->info("Usuario: {$user->name} ({$user->email})");
        $this->info("Roles: " . $user->roles->pluck('name')->join(', '));
        $this->newLine();
        
        // Verificar permiso específico
        $hasUsersView = $user->can('users.view');
        $this->info("¿Tiene permiso 'users.view'? " . ($hasUsersView ? '✓ SÍ' : '✗ NO'));
        
        // Mostrar todos los permisos
        $this->newLine();
        $this->info("Todos los permisos directos e indirectos:");
        $permissions = $user->getAllPermissions();
        
        foreach ($permissions as $permission) {
            $this->line("  • {$permission->name}");
        }
        
        return 0;
    }
}

