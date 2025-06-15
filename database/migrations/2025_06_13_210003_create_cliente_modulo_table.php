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
        Schema::create('cliente_modulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('modulo_id')->constrained('modulos')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->date('fecha_activacion');
            $table->date('fecha_vencimiento')->nullable(); // Null = sin vencimiento
            $table->json('configuracion')->nullable(); // Configuraciones específicas del módulo para este cliente
            $table->timestamps();

            // Índices y constraints
            $table->unique(['cliente_id', 'modulo_id']); // Un cliente no puede tener el mismo módulo duplicado
            $table->index(['cliente_id', 'activo']);
            $table->index('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_modulo');
    }
};
