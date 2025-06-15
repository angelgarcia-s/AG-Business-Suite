<?php

namespace App\Traits;

use App\Models\Empresa;
use App\Helpers\TenantHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

trait TenantScope
{
    /**
     * Boot del trait
     */
    protected static function bootTenantScope()
    {
        // Aplicar scope automático en consultas
        static::addGlobalScope(new TenantGlobalScope);

        // Establecer empresa_id automáticamente al crear
        static::creating(function (Model $model) {
            if (static::shouldApplyTenantScope() && !$model->empresa_id) {
                $tenantId = TenantHelper::currentId();
                if ($tenantId) {
                    $model->empresa_id = $tenantId;
                }
            }
        });
    }

    /**
     * Scope para incluir solo registros del tenant actual
     */
    public function scopeCurrentTenant(Builder $builder): Builder
    {
        $tenantId = TenantHelper::currentId();

        if ($tenantId) {
            return $builder->where('empresa_id', $tenantId);
        }

        return $builder;
    }

    /**
     * Scope para incluir registros de cualquier tenant (sin filtro)
     */
    public function scopeAllTenants(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope(TenantGlobalScope::class);
    }

    /**
     * Scope para incluir registros de un tenant específico
     */
    public function scopeForTenant(Builder $builder, $tenantId): Builder
    {
        return $builder->withoutGlobalScope(TenantGlobalScope::class)
                      ->where('empresa_id', $tenantId);
    }

    /**
     * Verificar si se debe aplicar el scope de tenant
     */
    protected static function shouldApplyTenantScope(): bool
    {
        // No aplicar para Super Admins o en ciertos contextos
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->esSuperAdmin()) {
            return false;
        }

        return true;
    }
}

/**
 * Global Scope para aplicar filtro de tenant automáticamente
 */
class TenantGlobalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Solo aplicar si el modelo tiene el campo empresa_id
        if (!$model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'empresa_id')) {
            return;
        }

        // No aplicar para Super Admins
        $user = auth()->user();
        if ($user && $user->esSuperAdmin()) {
            return;
        }

        // Aplicar filtro de tenant
        $tenantId = TenantHelper::currentId();
        if ($tenantId) {
            $builder->where($model->getTable() . '.empresa_id', $tenantId);
        }
    }
}
