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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique(); // productos, inventario, etc.
            $table->string('categoria'); // Inventario, Ventas, Producción, etc.
            $table->string('icono')->nullable(); // Icono para UI
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0); // Orden de visualización
            $table->boolean('activo')->default(true);
            $table->json('configuracion')->nullable(); // Configuraciones del módulo
            $table->timestamps();

            // Índices
            $table->index(['categoria', 'orden']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};