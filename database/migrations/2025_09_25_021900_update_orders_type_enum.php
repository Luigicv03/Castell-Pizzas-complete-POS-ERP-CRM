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
        // Actualizar el enum de type para incluir todos los tipos
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_type_check");
        DB::statement("ALTER TABLE orders ALTER COLUMN type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_type_check CHECK (type IN ('dine_in', 'takeaway', 'delivery', 'pickup'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los valores originales
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_type_check");
        DB::statement("ALTER TABLE orders ALTER COLUMN type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_type_check CHECK (type IN ('dine_in', 'takeout', 'delivery'))");
    }
};
