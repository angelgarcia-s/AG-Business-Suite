<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seeder dedicado solo a roles y permisos
     * Basado en módulos existentes y acciones estándar
     */
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔐 Creando permisos y roles...');

        // 1. Crear permisos
        $this->crearPermisos();

        // 2. Crear roles
        $this->crearRoles();

        // 3. Asignar permisos a roles
        $this->asignarPermisosARoles();

        // Resetear caché después de crear todo
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Sistema de permisos configurado');
    }

    /**
     * Crear todos los permisos del sistema
     */
    private function crearPermisos(): void
    {
        // === ACCIONES ESTÁNDAR ===
        $acciones = $this->getAccionesEstandar();

        // === PERMISOS ADMINISTRATIVOS (SaaS) ===
        $permisosAdmin = [
            'clientes' => 'gestión de clientes',
            'empresas' => 'gestión de empresas',
            'modulos' => 'gestión de módulos',
            'suscripciones' => 'gestión de suscripciones',
            'usuarios' => 'gestión de usuarios',
            'roles' => 'gestión de roles y permisos',
            'configuracion-sistema' => 'configuración del sistema',
            'reportes-sistema' => 'reportes del sistema',
            'planes' => 'gestión de planes de suscripción',
        ];

        $ordenAdmin = 10; // Orden base para categoría administrativa
        foreach ($permisosAdmin as $recurso => $descripcion) {
            foreach ($acciones as $accion => $accionDesc) {
                Permission::updateOrCreate(
                    ['name' => "{$recurso}-{$accion}"],
                    [
                        'description' => "{$accionDesc} {$descripcion}",
                        'category' => 'Administración SaaS',
                        'orden' => $ordenAdmin,
                    ]
                );
            }
        }

        // === PERMISOS DE MÓDULOS DE NEGOCIO ===
        $modulos = $this->getModulosParaPermisos();
        
        // Categorías de módulos con orden específico (simplificadas)
        $categoriasModulos = [
            'core' => ['orden' => 10, 'categoria' => 'Sistema Core'],
            'negocio' => ['orden' => 20, 'categoria' => 'Módulos de Negocio'],
        ];

        foreach ($modulos as $slug => $datos) {
            $descripcion = $datos['descripcion'];
            $categoriaSlug = $datos['categoria'];
            $categoriaInfo = $categoriasModulos[$categoriaSlug];
            
            foreach ($acciones as $accion => $accionDesc) {
                Permission::updateOrCreate(
                    ['name' => "{$slug}-{$accion}"],
                    [
                        'description' => "{$accionDesc} {$descripcion}",
                        'category' => $categoriaInfo['categoria'],
                        'orden' => $categoriaInfo['orden'],
                    ]
                );
            }
        }

        // === PERMISOS ESPECIALES ===
        $permisosEspeciales = [
            'dashboard-admin' => [
                'descripcion' => 'Acceso al dashboard administrativo',
                'categoria' => 'Dashboard',
                'orden' => 5
            ],
            'dashboard-empresa' => [
                'descripcion' => 'Acceso al dashboard de empresa',
                'categoria' => 'Dashboard',
                'orden' => 5
            ],
            'cambiar-empresa' => [
                'descripcion' => 'Cambiar entre empresas del cliente',
                'categoria' => 'Navegación',
                'orden' => 70
            ],
            'configurar-empresa' => [
                'descripcion' => 'Configurar datos de la empresa',
                'categoria' => 'Configuración',
                'orden' => 80
            ],
            'ver-reportes-avanzados' => [
                'descripcion' => 'Acceso a reportes y analytics avanzados',
                'categoria' => 'Reportes',
                'orden' => 90
            ],
            'gestionar-suscripcion' => [
                'descripcion' => 'Gestionar plan y suscripción del cliente',
                'categoria' => 'Suscripción',
                'orden' => 15
            ],
        ];

        foreach ($permisosEspeciales as $permiso => $datos) {
            Permission::updateOrCreate(
                ['name' => $permiso],
                [
                    'description' => $datos['descripcion'],
                    'category' => $datos['categoria'],
                    'orden' => $datos['orden'],
                ]
            );
        }

        $this->command->info("✅ Permisos creados");
    }

    /**
     * Crear todos los roles del sistema
     */
    private function crearRoles(): void
    {
        $roles = [
            'Super Administrador' => 'Acceso total al sistema SaaS',
            'Administrador de Cliente' => 'Gestiona las empresas de su cliente',
            'Gerente de Empresa' => 'Administrador de una empresa específica',
            'Empleado' => 'Usuario con acceso limitado a módulos',
            'Visualizador' => 'Solo lectura en los módulos asignados',
        ];

        foreach ($roles as $nombre => $descripcion) {
            Role::updateOrCreate(
                ['name' => $nombre],
                ['description' => $descripcion]
            );
        }

        $this->command->info("✅ Roles creados");
    }

    /**
     * Asignar permisos específicos a cada rol
     */
    private function asignarPermisosARoles(): void
    {
        // === SUPER ADMINISTRADOR ===
        $superAdmin = Role::findByName('Super Administrador');
        $superAdmin->syncPermissions(Permission::all());

        // === ADMINISTRADOR DE CLIENTE ===
        $adminCliente = Role::findByName('Administrador de Cliente');
        $permisosAdminCliente = Permission::whereIn('category', [
            'Módulos de Negocio',
            'Dashboard',
            'Navegación',
            'Configuración',
            'Suscripción'
        ])->get();
        $adminCliente->syncPermissions($permisosAdminCliente);

        // === GERENTE DE EMPRESA ===
        $gerente = Role::findByName('Gerente de Empresa');
        $permisosGerente = Permission::where('category', 'Módulos de Negocio')
            ->orWhere('name', 'dashboard-empresa')
            ->orWhere('name', 'configurar-empresa')
            ->orWhere('name', 'ver-reportes-avanzados')
            ->get();
        $gerente->syncPermissions($permisosGerente);

        // === EMPLEADO ===
        $empleado = Role::findByName('Empleado');
        $permisosEmpleado = Permission::whereIn('name', [
            'dashboard-empresa',
            'productos-list', 'productos-show', 'productos-create', 'productos-edit',
            'inventario-list', 'inventario-show', 'inventario-create', 'inventario-edit',
            'clientes-crm-list', 'clientes-crm-show', 'clientes-crm-create', 'clientes-crm-edit',
        ])->get();
        $empleado->syncPermissions($permisosEmpleado);

        // === VISUALIZADOR ===
        $visualizador = Role::findByName('Visualizador');
        $permisosVisualizador = Permission::where('name', 'like', '%-list')
            ->orWhere('name', 'like', '%-show')
            ->orWhere('name', 'dashboard-empresa')
            ->get();
        $visualizador->syncPermissions($permisosVisualizador);

        $this->command->info("✅ Permisos asignados a roles");
    }

    /**
     * Acciones estándar para todos los recursos
     */
    private function getAccionesEstandar(): array
    {
        return [
            'list' => 'Listar',
            'show' => 'Ver',
            'create' => 'Crear',
            'edit' => 'Editar',
            'destroy' => 'Eliminar',
            'import' => 'Importar',
            'export' => 'Exportar',
            'download' => 'Descargar',
        ];
    }

    /**
     * Módulos disponibles para permisos con categorías
     * Se basa en los módulos existentes en la BD
     */
    private function getModulosParaPermisos(): array
    {
        // Si existen módulos en BD, usarlos
        if (Schema::hasTable('modulos')) {
            $modulos = \App\Models\Modulo::all();
            $resultado = [];
            
            foreach ($modulos as $modulo) {
                $categoria = $this->determinarCategoriaModulo($modulo->slug);
                $resultado[$modulo->slug] = [
                    'descripcion' => $modulo->descripcion,
                    'categoria' => $categoria
                ];
            }
            
            return $resultado;
        }

        // Fallback: módulos por defecto con categorías simplificadas
        return [
            // Módulos CORE
            'dashboard' => ['descripcion' => 'dashboard', 'categoria' => 'core'],
            'configuracion' => ['descripcion' => 'configuración', 'categoria' => 'core'],
            'usuarios' => ['descripcion' => 'usuarios', 'categoria' => 'core'],
            'permisos' => ['descripcion' => 'permisos', 'categoria' => 'core'],
            
            // Primer módulo de negocio
            'productos' => ['descripcion' => 'productos', 'categoria' => 'negocio'],
        ];
    }

    /**
     * Determinar categoría de un módulo basado en su slug
     * Simplificado para arquitectura básica
     */
    private function determinarCategoriaModulo(string $slug): string
    {
        $categorias = [
            'core' => ['dashboard', 'configuracion', 'usuarios', 'permisos'],
            'negocio' => ['productos'],
        ];

        foreach ($categorias as $categoria => $modulos) {
            if (in_array($slug, $modulos)) {
                return $categoria;
            }
        }

        return 'negocio'; // Default para nuevos módulos
    }

    /**
     * Agregar permisos para un nuevo módulo fácilmente
     * Adaptado para la nueva arquitectura simplificada
     */
    public function agregarPermisosModulo(string $slug, string $descripcion, string $categoria = 'negocio'): void
    {
        $acciones = $this->getAccionesEstandar();
        
        $categoriasOrden = [
            'core' => 10,
            'negocio' => 20,
        ];

        $categoriasNombres = [
            'core' => 'Sistema Core',
            'negocio' => 'Módulos de Negocio',
        ];

        $orden = $categoriasOrden[$categoria] ?? 99;
        $categoriaNombre = $categoriasNombres[$categoria] ?? 'Otros';

        foreach ($acciones as $accion => $accionDesc) {
            Permission::updateOrCreate(
                ['name' => "{$slug}-{$accion}"],
                [
                    'description' => "{$accionDesc} {$descripcion}",
                    'category' => $categoriaNombre,
                    'orden' => $orden,
                ]
            );
        }

        $this->command->info("✅ Permisos creados para módulo: {$descripcion}");
    }
}
