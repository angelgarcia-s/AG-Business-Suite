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
    {        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Básico, Premium, Empresarial
            $table->string('slug')->unique(); // basico, premium, empresarial
            $table->text('descripcion')->nullable();

            // Precios base
            $table->decimal('precio_mensual', 10, 2)->default(0);
            $table->decimal('precio_anual', 10, 2)->default(0);

            // Pricing híbrido - Precios por extras
            $table->decimal('precio_usuario_adicional_mensual', 8, 2)->default(0);
            $table->decimal('precio_usuario_adicional_anual', 8, 2)->default(0);
            $table->decimal('precio_empresa_adicional_mensual', 8, 2)->default(0);
            $table->decimal('precio_empresa_adicional_anual', 8, 2)->default(0);

            // Límites base incluidos en el plan
            $table->integer('limite_empresas')->default(1);
            $table->integer('limite_usuarios')->default(10);

            // Flexibilidad para extras
            $table->boolean('permite_empresas_adicionales')->default(false);
            $table->boolean('permite_usuarios_adicionales')->default(false);
            $table->integer('maximo_empresas_totales')->nullable(); // NULL = sin límite
            $table->integer('maximo_usuarios_totales')->nullable(); // NULL = sin límite

            // Otras características
            $table->integer('limite_almacenamiento_gb')->default(1); // GB
            $table->boolean('soporte_prioritario')->default(false);
            $table->boolean('backup_automatico')->default(false);
            $table->boolean('api_acceso')->default(false);

            // Configuración y orden
            $table->integer('orden')->default(0); // Para mostrar ordenados
            $table->boolean('activo')->default(true);
            $table->boolean('destacado')->default(false); // Plan recomendado
            $table->json('caracteristicas')->nullable(); // Array de características
            $table->json('configuracion')->nullable(); // Configuraciones adicionales
            $table->timestamps();

            // Índices
            $table->index(['activo', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
