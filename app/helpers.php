<?php

use App\Helpers\TenantHelper;
use App\Models\Empresa;

if (!function_exists('tenant')) {
    /**
     * Obtener la empresa (tenant) actual
     */
    function tenant(): ?Empresa
    {
        return TenantHelper::current();
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Obtener ID del tenant actual
     */
    function tenant_id(): ?int
    {
        return TenantHelper::currentId();
    }
}

if (!function_exists('has_tenant')) {
    /**
     * Verificar si hay un tenant activo
     */
    function has_tenant(): bool
    {
        return TenantHelper::hasTenant();
    }
}

if (!function_exists('tenant_config')) {
    /**
     * Obtener configuración del tenant
     */
    function tenant_config(string $key, $default = null)
    {
        return TenantHelper::config($key, $default);
    }
}

if (!function_exists('set_tenant_config')) {
    /**
     * Establecer configuración del tenant
     */
    function set_tenant_config(string $key, $value): bool
    {
        return TenantHelper::setConfig($key, $value);
    }
}
