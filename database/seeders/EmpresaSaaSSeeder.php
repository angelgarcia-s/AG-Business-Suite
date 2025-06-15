<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Modulo;
use App\Models\Plan;
use Carbon\Carbon;

class EmpresaSaaSSeeder extends Seeder
{
    /**
     * Seeder base del SaaS - Solo infraestructura esencial
     */
    public function run(): void
    {
        $this->command->info('🏗️ Configurando infraestructura del SaaS...');

        // 1. Crear planes de suscripción
        $this->crearPlanes();

        // 2. Crear módulos del sistema (seeder dedicado)
        $this->call(ModulosSeeder::class);

        // 3. Crear permisos y roles (seeder dedicado)
        $this->call(RolesAndPermissionsSeeder::class);

        // 4. Crear cliente y empresa matriz AG-Business-Suite
        $this->crearEmpresaMatriz();

        $this->command->info('✅ Infraestructura SaaS configurada');
    }

    private function crearPlanes()
    {
        $this->command->info('💳 Creando planes de suscripción...');

        $planes = [
            [
                'nombre' => 'Básico',
                'slug' => 'basico',
                'descripcion' => 'Plan ideal para pequeñas empresas que están comenzando',
                'precio_mensual' => 29000,
                'precio_anual' => 290000,
                'precio_usuario_adicional_mensual' => 8000,
                'precio_usuario_adicional_anual' => 80000,
                'precio_empresa_adicional_mensual' => 15000,
                'precio_empresa_adicional_anual' => 150000,
                'limite_empresas' => 1,
                'limite_usuarios' => 5,
                'permite_empresas_adicionales' => true,
                'permite_usuarios_adicionales' => true,
                'maximo_empresas_totales' => 2,
                'maximo_usuarios_totales' => 20,
                'limite_almacenamiento_gb' => 5,
                'soporte_prioritario' => false,
                'backup_automatico' => true,
                'api_acceso' => false,
                'orden' => 1,
                'activo' => true,
                'destacado' => false,
                'caracteristicas' => json_encode([
                    'Hasta 5 usuarios incluidos',
                    'Hasta 20 usuarios adicionales',
                    '1 empresa incluida',
                    'Hasta 2 empresas totales',
                    '5 GB de almacenamiento',
                    'Backup automático diario',
                    'Soporte estándar'
                ])
            ],
            [
                'nombre' => 'Profesional',
                'slug' => 'profesional',
                'descripcion' => 'Plan completo para empresas en crecimiento',
                'precio_mensual' => 79000,
                'precio_anual' => 790000,
                'precio_usuario_adicional_mensual' => 6000,
                'precio_usuario_adicional_anual' => 60000,
                'precio_empresa_adicional_mensual' => 25000,
                'precio_empresa_adicional_anual' => 250000,
                'limite_empresas' => 2,
                'limite_usuarios' => 10,
                'permite_empresas_adicionales' => true,
                'permite_usuarios_adicionales' => true,
                'maximo_empresas_totales' => 4,
                'maximo_usuarios_totales' => 40,
                'limite_almacenamiento_gb' => 10,
                'soporte_prioritario' => true,
                'backup_automatico' => true,
                'api_acceso' => true,
                'orden' => 2,
                'activo' => true,
                'destacado' => true,
                'caracteristicas' => json_encode([
                    'Hasta 10 usuarios incluidos',
                    'Hasta 40 usuarios adicionales',
                    'Hasta 4 empresas adicionales',
                    '10 GB de almacenamiento',
                    'Backup automático diario',
                    'API de integración',
                    'Soporte prioritario'
                ])
            ],
            [
                'nombre' => 'Empresarial',
                'slug' => 'empresarial',
                'descripcion' => 'Plan completo para grandes organizaciones',
                'precio_mensual' => 149000,
                'precio_anual' => 1490000,
                'precio_usuario_adicional_mensual' => 4000,
                'precio_usuario_adicional_anual' => 40000,
                'precio_empresa_adicional_mensual' => 20000,
                'precio_empresa_adicional_anual' => 200000,
                'limite_empresas' => 3,
                'limite_usuarios' => 25,
                'permite_empresas_adicionales' => true,
                'permite_usuarios_adicionales' => true,
                'maximo_empresas_totales' => null, // Sin límite
                'maximo_usuarios_totales' => null, // Sin límite
                'limite_almacenamiento_gb' => 50,
                'soporte_prioritario' => true,
                'backup_automatico' => true,
                'api_acceso' => true,
                'orden' => 3,
                'activo' => true,
                'destacado' => false,
                'caracteristicas' => json_encode([
                    'Hasta 25 usuarios incluidos',
                    'Usuarios adicionales ilimitados',
                    'Hasta 3 empresas incluidas',
                    'Empresas adicionales ilimitadas',
                    '50 GB de almacenamiento',
                    'Backup automático cada 6 horas',
                    'API completa',
                    'Soporte prioritario 24/7',
                    'Reportes avanzados'
                ])
            ],
            [
                'nombre' => 'Matriz SaaS',
                'slug' => 'matriz-saas',
                'descripcion' => 'Plan especial para la empresa matriz del SaaS',
                'precio_mensual' => 0,
                'precio_anual' => 0,
                'precio_usuario_adicional_mensual' => 0,
                'precio_usuario_adicional_anual' => 0,
                'precio_empresa_adicional_mensual' => 0,
                'precio_empresa_adicional_anual' => 0,
                'limite_empresas' => 999999,
                'limite_usuarios' => 999999,
                'permite_empresas_adicionales' => true,
                'permite_usuarios_adicionales' => true,
                'maximo_empresas_totales' => null,
                'maximo_usuarios_totales' => null,
                'limite_almacenamiento_gb' => 999999,
                'soporte_prioritario' => true,
                'backup_automatico' => true,
                'api_acceso' => true,
                'orden' => 0,
                'activo' => true,
                'destacado' => false,
                'caracteristicas' => json_encode([
                    'Acceso ilimitado a todos los módulos',
                    'Usuarios ilimitados',
                    'Empresas ilimitadas',
                    'Almacenamiento ilimitado',
                    'Acceso completo de administración',
                    'Plan especial para empresa matriz'
                ])
            ]
        ];

        foreach ($planes as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('✅ 4 planes de suscripción creados');
    }

    private function crearEmpresaMatriz()
    {
        $this->command->info('🏢 Creando empresa matriz AG Business Suite...');

        // 1. Obtener el plan especial para la matriz
        $planMatriz = Plan::where('slug', 'matriz-saas')->first();

        // 2. Crear cliente AG-Business-Suite (la empresa del SaaS)
        $clienteMatriz = Cliente::firstOrCreate(
            ['email' => 'facturacion@agbusinesssuite.com'],
            [
                'plan_id' => $planMatriz->id,
                'nombre' => 'AG Business Suite',
                'email' => 'facturacion@agbusinesssuite.com',
                'telefono' => '+57 300 123 4567',
                'direccion' => 'Cra 15 #93-07, Bogotá, Colombia',
                'contacto' => 'AG Development Team',
                'fecha_inicio_suscripcion' => Carbon::now(),
                'fecha_fin_suscripcion' => null, // Sin vencimiento (plan matriz)
                'activo' => true,
                'metadata' => json_encode([
                    'es_matriz' => true,
                    'origen_suscripcion' => 'fundador',
                    'acceso_total_sistema' => true,
                    'tipo_cliente' => 'matriz_saas',
                ]),
            ]
        );

        // 3. Asignar TODOS los módulos al cliente matriz
        $todosLosModulos = Modulo::all();
        foreach ($todosLosModulos as $modulo) {
            $clienteMatriz->modulos()->syncWithoutDetaching([
                $modulo->id => [
                    'activo' => true,
                    'fecha_activacion' => Carbon::now(),
                    'fecha_vencimiento' => null,
                    'configuracion' => json_encode(['acceso_completo' => true])
                ]
            ]);
        }

        // 4. Crear empresa matriz AG-Business-Suite
        $empresaMatriz = Empresa::firstOrCreate(
            [
                'cliente_id' => $clienteMatriz->id,
                'nombre' => 'AG Business Suite'
            ],
            [
                'codigo_pais' => 'CO',
                'pais' => 'Colombia',
                'ciudad' => 'Bogotá',
                'direccion' => 'Cra 15 #93-07, Bogotá, Colombia',
                'telefono' => '+57 300 123 4567',
                'email' => 'admin@agbusinesssuite.com',
                'rfc' => '900123456-1',
                'timezone' => 'America/Bogota',
                'moneda' => 'COP',
                'activo' => true,
                'configuracion' => json_encode([
                    'es_matriz' => true,
                    'acceso_total_sistema' => true,
                    'tipo' => 'Empresa Matriz SaaS'
                ]),
            ]
        );

        // 5. Crear Super Administrador asignado a la empresa matriz
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@agbusinesssuite.com'],
            [
                'name' => 'Super Administrador',
                'email_verified_at' => now(),
                'password' => bcrypt('AGBusiness*2025'),
                'tipo_usuario' => 'super_admin',
                'empresa_id' => $empresaMatriz->id,
                'activo' => true,
                'ultimo_acceso' => now(),
            ]
        );

        // Asignar rol si existe
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $roleSuperAdmin = \Spatie\Permission\Models\Role::where('name', 'Super Administrador')->first();
            if ($roleSuperAdmin) {
                $superAdmin->assignRole($roleSuperAdmin);
            }
        }

        $this->command->info('✅ Empresa matriz AG Business Suite creada');
        $this->command->info('👑 Super Admin: admin@agbusinesssuite.com / AGBusiness*2025');
    }
}
