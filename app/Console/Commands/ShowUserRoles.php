<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowUserRoles extends Command
{
    protected $signature = 'users:show-roles';
    protected $description = 'Muestra los usuarios y sus roles actuales';

    public function handle()
    {
        $users = User::with('roles')->get();
        
        $this->info('Usuarios y sus roles:');
        $this->info('===================');
        
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ');
            $this->line("• {$user->name} ({$user->email}) - Roles: {$roles}");
        }
        
        return 0;
    }
}

