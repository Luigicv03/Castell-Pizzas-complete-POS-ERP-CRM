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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['cash', 'pago_movil', 'zelle', 'binance', 'card'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('currency', 3)->default('USD');
            $table->string('reference')->nullable(); // referencia de pago
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // quien procesó el pago
            $table->timestamps();
            
            $table->index(['order_id', 'method']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
