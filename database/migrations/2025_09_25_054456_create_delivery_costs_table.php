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
        Schema::create('delivery_costs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_distance', 5, 2); // Distancia mínima en km
            $table->decimal('max_distance', 5, 2); // Distancia máxima en km
            $table->decimal('cost', 8, 2); // Costo del delivery
            $table->string('description')->nullable(); // Descripción opcional
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['min_distance', 'max_distance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_costs');
    }
};