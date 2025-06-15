<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modulo;

class ModulosSeeder extends Seeder
{
    /**
     * Seeder dedicado solo a módulos del sistema
     * Incluye únicamente módulos CORE y el primer módulo de negocio (Productos)
     * Los módulos específicos de industria se agregan conforme se desarrollen
     */
    public function run(): void
    {
        $this->command->info('📦 Creando módulos del sistema...');

        $modulos = $this->getModulos();

        foreach ($modulos as $moduloData) {
            $modulo = Modulo::updateOrCreate(
                ['slug' => $moduloData['slug']],
                $moduloData
            );

            $this->command->info("✅ Módulo: {$modulo->nombre}");
        }

        $this->command->info("📦 {$modulos->count()} módulos creados/actualizados");
    }

    /**
     * Definición centralizada de módulos del sistema
     * Solo incluye módulos CORE y el primer módulo de negocio
     * Los demás módulos se agregan conforme se desarrollen
     */
    private function getModulos(): \Illuminate\Support\Collection
    {
        return collect([
            // === MÓDULOS CORE ===
            [
                'nombre' => 'Dashboard',
                'slug' => 'dashboard',
                'descripcion' => 'Panel principal con métricas y KPIs del negocio',
                'categoria' => 'Core',
                'icono' => 'chart-bar',
                'orden' => 1,
                'activo' => true,
                'configuracion' => [
                    'widgets_personalizables' => true,
                    'metricas_tiempo_real' => true,
                    'exportar_reportes' => true
                ]
            ],
            [
                'nombre' => 'Configuración',
                'slug' => 'configuracion',
                'descripcion' => 'Configuración general del sistema y empresa',
                'categoria' => 'Core',
                'icono' => 'cog',
                'orden' => 2,
                'activo' => true,
                'configuracion' => [
                    'configuracion_empresa' => true,
                    'parametros_sistema' => true,
                    'integraciones' => true
                ]
            ],
            [
                'nombre' => 'Usuarios',
                'slug' => 'usuarios',
                'descripcion' => 'Gestión de usuarios del sistema',
                'categoria' => 'Core',
                'icono' => 'users',
                'orden' => 3,
                'activo' => true,
                'configuracion' => [
                    'autenticacion_2fa' => false,
                    'sesiones_multiples' => true,
                    'auditoria_accesos' => true
                ]
            ],
            [
                'nombre' => 'Permisos',
                'slug' => 'permisos',
                'descripcion' => 'Control de acceso y roles del sistema',
                'categoria' => 'Core',
                'icono' => 'shield',
                'orden' => 4,
                'activo' => true,
                'configuracion' => [
                    'roles_personalizados' => true,
                    'permisos_granulares' => true,
                    'herencia_roles' => true
                ]
            ],

            // === PRIMER MÓDULO DE NEGOCIO ===
            [
                'nombre' => 'Productos',
                'slug' => 'productos',
                'descripcion' => 'Gestión completa de productos y catálogos',
                'categoria' => 'Negocio',
                'icono' => 'box',
                'orden' => 5,
                'activo' => true,
                'configuracion' => [
                    'requiere_codigo_barras' => false,
                    'permite_variantes' => true,
                    'control_stock' => true,
                    'categorias_multinivel' => true,
                    'imagenes_multiples' => true
                ]
            ]
        ]);
    }

    /**
     * Agregar un nuevo módulo fácilmente
     * Útil para cuando se desarrollen nuevos módulos de negocio
     */
    public function agregarModulo(array $modulo): void
    {
        Modulo::updateOrCreate(
            ['slug' => $modulo['slug']],
            $modulo
        );

        $this->command->info("✅ Nuevo módulo agregado: {$modulo['nombre']}");
    }
}
