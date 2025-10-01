<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ShowRolePermissions extends Command
{
    protected $signature = 'roles:show-permissions';
    protected $description = 'Muestra los permisos de cada rol';

    public function handle()
    {
        $roles = Role::with('permissions')->get();
        
        foreach ($roles as $role) {
            $this->info("\n========================================");
            $this->info("ROL: {$role->name}");
            $this->info("========================================");
            
            $permissions = $role->permissions->groupBy(function($permission) {
                return explode('.', $permission->name)[0];
            });
            
            if ($permissions->isEmpty()) {
                $this->line("  Sin permisos asignados");
            } else {
                foreach ($permissions as $module => $perms) {
                    $this->line("\n  📁 " . strtoupper($module) . ":");
                    foreach ($perms as $perm) {
                        $this->line("     • {$perm->name}");
                    }
                }
            }
        }
        
        $this->newLine();
        return 0;
    }
}

