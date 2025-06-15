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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre');
            $table->string('codigo_pais', 3); // MEX, USA, etc.
            $table->string('pais');
            $table->string('ciudad');
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('rfc')->nullable(); // RFC, Tax ID, etc.
            $table->string('logo')->nullable(); // Ruta del archivo de logo
            $table->string('timezone')->default('America/Mexico_City');
            $table->string('moneda', 3)->default('MXN'); // MXN, USD, etc.
            $table->boolean('activo')->default(true);
            $table->json('configuracion')->nullable(); // Configuraciones específicas
            $table->timestamps();

            // Índices
            $table->index(['cliente_id', 'activo']);
            $table->unique(['cliente_id', 'nombre']); // Nombres únicos por cliente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
