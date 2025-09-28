<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Table;

class CheckDataConsistency extends Command
{
    protected $signature = 'data:check-consistency';
    protected $description = 'Verificar consistencia de datos entre órdenes y mesas';

    public function handle()
    {
        $this->info('=== VERIFICANDO CONSISTENCIA DE DATOS ===');
        
        // Órdenes activas
        $this->info('--- ÓRDENES ACTIVAS ---');
        $activeOrders = Order::whereIn('status', ['pending', 'preparing', 'ready'])
            ->with('table')
            ->get(['id', 'daily_number', 'table_id', 'status', 'type']);
            
        foreach ($activeOrders as $order) {
            $this->line("ID: {$order->id} | Diario: {$order->daily_number} | Mesa: " . ($order->table_id ?? 'N/A') . " | Status: {$order->status} | Tipo: {$order->type}");
        }
        
        $this->info('--- MESAS ---');
        $tables = Table::all(['id', 'name', 'status']);
        foreach ($tables as $table) {
            $this->line("ID: {$table->id} | Nombre: {$table->name} | Status: {$table->status}");
        }
        
        $this->info('--- ANÁLISIS DE INCONSISTENCIAS ---');
        
        // Verificar mesas ocupadas sin órdenes activas
        $occupiedTables = $tables->where('status', 'occupied');
        foreach ($occupiedTables as $table) {
            $hasActiveOrder = $activeOrders->where('table_id', $table->id)->count() > 0;
            if (!$hasActiveOrder) {
                $this->error("❌ Mesa {$table->name} está marcada como ocupada pero NO tiene orden activa");
            } else {
                $this->info("✅ Mesa {$table->name} está correctamente ocupada");
            }
        }
        
        // Verificar órdenes activas con mesas libres
        foreach ($activeOrders as $order) {
            if ($order->table_id) {
                $table = $tables->find($order->table_id);
                if ($table && $table->status !== 'occupied') {
                    $this->error("❌ Orden {$order->daily_number} está en Mesa {$table->name} pero la mesa está marcada como {$table->status}");
                } else {
                    $this->info("✅ Orden {$order->daily_number} está correctamente asignada a Mesa {$table->name}");
                }
            }
        }
        
        return 0;
    }
}
