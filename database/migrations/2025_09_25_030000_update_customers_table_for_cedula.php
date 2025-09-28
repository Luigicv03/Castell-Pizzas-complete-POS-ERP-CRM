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
        Schema::table('customers', function (Blueprint $table) {
            // Agregar columna cedula
            $table->string('cedula')->nullable()->after('phone');
            
            // Remover restricción única del email
            $table->dropUnique(['email']);
            
            // Agregar restricción única a la cédula
            $table->unique('cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Remover restricción única de la cédula
            $table->dropUnique(['cedula']);
            
            // Agregar restricción única al email
            $table->unique('email');
            
            // Remover columna cedula
            $table->dropColumn('cedula');
        });
    }
};
