<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class DebugActiveOrders extends Command
{
    protected $signature = 'debug:active-orders';
    protected $description = 'Depurar órdenes activas para mesa 11';

    public function handle()
    {
        $this->info('=== DEPURANDO ÓRDENES ACTIVAS ===');
        
        // Buscar órdenes activas para mesa 11
        $orders = Order::with(['customer', 'table', 'items'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->where('table_id', 11)
            ->get();
            
        $this->info("Órdenes encontradas para mesa 11: " . $orders->count());
        
        foreach ($orders as $order) {
            $this->line("ID: {$order->id} | Diario: {$order->daily_number} | Status: {$order->status} | Mesa: {$order->table_id}");
        }
        
        // Buscar todas las órdenes activas
        $allActiveOrders = Order::with(['customer', 'table', 'items'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->get();
            
        $this->info("Total de órdenes activas: " . $allActiveOrders->count());
        
        foreach ($allActiveOrders as $order) {
            $this->line("ID: {$order->id} | Diario: {$order->daily_number} | Status: {$order->status} | Mesa: " . ($order->table_id ?? 'N/A'));
        }
        
        return 0;
    }
}

