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
        Schema::table('clientes', function (Blueprint $table) {
            // Datos fiscales básicos (para recibos/comprobantes)
            $table->string('razon_social')->nullable()->after('nombre'); // Razón social completa
            $table->string('rfc')->nullable()->after('razon_social'); // RFC/NIT/RUC según país
            $table->string('codigo_pais', 3)->default('MEX')->after('rfc'); // MEX, COL, USA, etc.
            $table->string('pais')->default('México')->after('codigo_pais');

            // Dirección fiscal (simplificada)
            $table->string('direccion_fiscal')->nullable()->after('direccion');
            $table->string('ciudad_fiscal')->nullable()->after('direccion_fiscal');
            $table->string('estado_fiscal')->nullable()->after('ciudad_fiscal');
            $table->string('codigo_postal_fiscal')->nullable()->after('estado_fiscal');

            // Configuración fiscal específica por país
            $table->enum('regimen_fiscal', [
                'general',
                'simplificado_confianza',
                'fisica_actividad_empresarial',
                'incorporacion_fiscal',
                'opcional_grupos_sociedades',
                'sueldos_salarios',
                'arrendamiento',
                'actividades_agricolas',
                'otros'
            ])->default('general')->after('codigo_postal_fiscal');

            // Uso CFDI específico para México
            $table->enum('uso_cfdi', [
                'G01', // Adquisición de mercancías
                'G02', // Devoluciones, descuentos o bonificaciones
                'G03', // Gastos en general
                'I01', // Construcciones
                'I02', // Mobilario y equipo de oficina por inversiones
                'I03', // Equipo de transporte
                'I04', // Equipo de cómputo y accesorios
                'I05', // Dados, troqueles, moldes, matrices y herramental
                'I06', // Comunicaciones telefónicas
                'I07', // Comunicaciones satelitales
                'I08', // Otra maquinaria y equipo
                'D01', // Honorarios médicos, dentales y gastos hospitalarios
                'D02', // Gastos médicos por incapacidad o discapacidad
                'D03', // Gastos funerales
                'D04', // Donativos
                'D05', // Intereses reales efectivamente pagados por créditos hipotecarios
                'D06', // Aportaciones voluntarias al SAR
                'D07', // Primas por seguros de gastos médicos
                'D08', // Gastos de transportación escolar obligatoria
                'D09', // Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones
                'D10', // Pagos por servicios educativos
                'P01', // Por definir
                'S01'  // Sin efectos fiscales
            ])->default('G03')->after('regimen_fiscal'); // G03 = Gastos en general (más común para SaaS)

            // Email para recibos y configuración de cobro
            $table->string('email_facturacion')->nullable()->after('uso_cfdi'); // Email específico para recibos
            $table->boolean('auto_facturacion')->default(true)->after('email_facturacion'); // Cobro automático habilitado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'razon_social',
                'rfc',
                'codigo_pais',
                'pais',
                'direccion_fiscal',
                'ciudad_fiscal',
                'estado_fiscal',
                'codigo_postal_fiscal',
                'regimen_fiscal',
                'uso_cfdi',
                'email_facturacion',
                'auto_facturacion',
            ]);
        });
    }
};
