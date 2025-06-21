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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo',['local','para_llevar']);
            $table->unsignedBigInteger('mesa_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->enum('estado', ['pendiente','en_preparacion','listo','entregado','cancelado']);
            $table->decimal('total', 10, 2)->default(0.00);

            $table->foreign('mesa_id')->references('id')->on('mesas');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
