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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->string('contacto')->nullable(); // Persona de contacto
            $table->foreignId('plan_id')->constrained('planes')->onDelete('restrict');
            $table->date('fecha_inicio_suscripcion');
            $table->date('fecha_fin_suscripcion')->nullable(); // Null = sin vencimiento
            $table->boolean('activo')->default(true);
            $table->json('metadata')->nullable(); // Configuraciones adicionales
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
