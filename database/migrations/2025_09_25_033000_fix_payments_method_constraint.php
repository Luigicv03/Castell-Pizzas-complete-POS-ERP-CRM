<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar cualquier restricción existente en payment_method
        DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check");
        DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_method_check");
        
        // Asegurar que la columna sea VARCHAR
        DB::statement("ALTER TABLE payments ALTER COLUMN payment_method TYPE VARCHAR(20)");
        
        // Agregar la nueva restricción con todos los métodos válidos
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IN ('cash', 'mobile_payment', 'zelle', 'binance', 'pos', 'transfer'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la restricción
        DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check");
    }
};
