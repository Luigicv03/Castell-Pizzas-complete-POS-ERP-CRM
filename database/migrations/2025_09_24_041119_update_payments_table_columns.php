<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Renombrar la columna 'method' a 'payment_method'
            $table->renameColumn('method', 'payment_method');
            
            // Agregar columna 'status' si no existe
            if (!Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revertir el cambio
            $table->renameColumn('payment_method', 'method');
            
            // Eliminar columna 'status' si existe
            if (Schema::hasColumn('payments', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
