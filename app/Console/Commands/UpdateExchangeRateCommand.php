<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExchangeRate;

class UpdateExchangeRateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rate:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la tasa de cambio del dólar desde el BCV automáticamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando tasa de cambio desde BCV...');
        
        try {
            $exchangeRate = ExchangeRate::updateFromBCV();
            
            $this->info('✓ Tasa actualizada exitosamente');
            $this->info('Nueva tasa: 1 USD = ' . $exchangeRate->usd_to_bsf . ' BsF');
            $this->info('Fuente: ' . $exchangeRate->source);
            $this->info('Última actualización: ' . $exchangeRate->last_updated_at->format('d/m/Y H:i:s'));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Error al actualizar la tasa de cambio');
            $this->error($e->getMessage());
            
            return Command::FAILURE;
        }
    }
}


