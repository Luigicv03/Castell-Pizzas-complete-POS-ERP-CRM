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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('usd_to_bsf', 15, 4); // Tasa USD a BsF
            $table->boolean('is_automatic')->default(true); // Si es automática o manual
            $table->timestamp('last_updated_at')->nullable(); // Última actualización
            $table->string('source')->default('bcv'); // Fuente (bcv, manual)
            $table->timestamps();
        });
        
        // Insertar tasa inicial
        DB::table('exchange_rates')->insert([
            'usd_to_bsf' => 36.50,
            'is_automatic' => true,
            'last_updated_at' => now(),
            'source' => 'manual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
