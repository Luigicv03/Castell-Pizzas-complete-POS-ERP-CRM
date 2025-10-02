<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Table;

class CheckTableZones extends Command
{
    protected $signature = 'tables:check-zones';
    protected $description = 'Verificar las zonas de las mesas';

    public function handle()
    {
        $this->info('Verificando zonas de mesas...');
        
        // Verificar todas las mesas individualmente
        $this->line('Todas las mesas:');
        Table::select('name', 'zone')->orderBy('name')->get()->each(function($table) {
            $this->line("  {$table->name}: {$table->zone}");
        });
        
        $this->line('');
        $this->info('Agrupadas por zona:');
        
        $zones = Table::select('zone')->distinct()->pluck('zone');
        
        foreach ($zones as $zone) {
            $tables = Table::where('zone', $zone)->orderBy('name')->pluck('name');
            $count = $tables->count();
            
            $this->line("$zone: $count mesas");
            $this->line("  Mesas: " . $tables->join(', '));
        }
        
        return 0;
    }
}