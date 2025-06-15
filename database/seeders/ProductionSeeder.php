<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seeder seguro para producción
     * Solo ejecuta la infraestructura base, sin datos de prueba
     */
    public function run(): void
    {
        // Verificar que estamos en producción
        if (!app()->environment('production')) {
            $this->command->warn('⚠️  Este seeder está diseñado solo para producción');
            $this->command->ask('¿Continuar? (y/N)', 'N');
        }

        $this->command->info('🚀 Ejecutando seeder de producción...');

        // Solo ejecutar EmpresaSaaSSeeder (infraestructura base)
        $this->call(EmpresaSaaSSeeder::class);

        $this->command->info('✅ Sistema AG Business Suite listo para producción:');
        $this->command->info('🏢 Empresa matriz: AG Business Suite creada');
        $this->command->info('👑 Super Admin: admin@agbusinesssuite.com');
        $this->command->info('📦 5 módulos disponibles (4 CORE + 1 negocio)');
        $this->command->info('💳 4 planes de suscripción configurados');
        $this->command->info('🔐 Sistema de permisos y roles listo');
        $this->command->info('');
        $this->command->info('🎯 El sistema está listo para recibir clientes reales');
        $this->command->info('⚡ Los clientes se crearán a través de la aplicación web');
    }
}
