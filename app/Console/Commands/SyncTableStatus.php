<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Table;
use App\Models\Order;

class SyncTableStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar el estado de las mesas con las órdenes activas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sincronizando estados de mesas...');
        
        $tables = Table::all();
        $updated = 0;
        
        foreach ($tables as $table) {
            // Buscar órdenes activas para esta mesa
            $activeOrder = $table->orders()
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->latest()
                ->first();

            $oldStatus = $table->status;
            
            if ($activeOrder) {
                // Si hay una orden activa, la mesa debe estar ocupada
                if ($table->status !== Table::STATUS_OCCUPIED) {
                    $table->update(['status' => Table::STATUS_OCCUPIED]);
                    $this->line("Mesa {$table->name}: {$oldStatus} → " . Table::STATUS_OCCUPIED);
                    $updated++;
                }
            } else {
                // Si no hay órdenes activas, la mesa debe estar libre
                if ($table->status !== Table::STATUS_FREE) {
                    $table->update(['status' => Table::STATUS_FREE]);
                    $this->line("Mesa {$table->name}: {$oldStatus} → " . Table::STATUS_FREE);
                    $updated++;
                }
            }
        }
        
        $this->info("Sincronización completada. {$updated} mesas actualizadas.");
        
        return 0;
    }
}

