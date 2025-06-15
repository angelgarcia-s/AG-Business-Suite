<?php

namespace App\Helpers;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class TenantHelper
{
    /**
     * Obtener la empresa (tenant) actual
     */
    public static function current(): ?Empresa
    {
        // Intentar obtener del contexto de la aplicación
        if (app()->bound('current_tenant')) {
            return app('current_tenant');
        }

        // Fallback: obtener desde el usuario autenticado
        $user = Auth::user();
        if ($user && !$user->esSuperAdmin()) {
            return $user->empresa;
        }

        return null;
    }

    /**
     * Obtener ID de la empresa actual
     */
    public static function currentId(): ?int
    {
        $tenant = self::current();
        return $tenant?->id;
    }

    /**
     * Verificar si hay un tenant activo
     */
    public static function hasTenant(): bool
    {
        return self::current() !== null;
    }

    /**
     * Ejecutar callback en contexto de tenant específico
     */
    public static function runInContext(Empresa $empresa, callable $callback)
    {
        $previousTenant = app()->bound('current_tenant') ? app('current_tenant') : null;
        
        app()->instance('current_tenant', $empresa);
        
        try {
            return $callback();
        } finally {
            if ($previousTenant) {
                app()->instance('current_tenant', $previousTenant);
            } else {
                app()->forgetInstance('current_tenant');
            }
        }
    }

    /**
     * Obtener configuración específica del tenant
     */
    public static function config(string $key, $default = null)
    {
        $tenant = self::current();
        if (!$tenant || !$tenant->configuracion) {
            return $default;
        }

        return data_get($tenant->configuracion, $key, $default);
    }

    /**
     * Establecer configuración del tenant
     */
    public static function setConfig(string $key, $value): bool
    {
        $tenant = self::current();
        if (!$tenant) {
            return false;
        }

        $config = $tenant->configuracion ?? [];
        data_set($config, $key, $value);
        
        return $tenant->update(['configuracion' => $config]);
    }
}
