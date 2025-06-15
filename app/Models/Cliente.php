<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'razon_social',
        'email',
        'telefono',
        'direccion',
        'contacto',
        'plan_id',
        'fecha_inicio_suscripcion',
        'fecha_fin_suscripcion',
        'activo',
        'metadata',
        // Campos fiscales básicos
        'rfc',
        'codigo_pais',
        'pais',
        'direccion_fiscal',
        'ciudad_fiscal',
        'estado_fiscal',
        'codigo_postal_fiscal',
        'regimen_fiscal',
        'uso_cfdi',
        // Email para recibos y configuración
        'email_facturacion',
        'auto_facturacion',
    ];

    protected $casts = [
        'fecha_inicio_suscripcion' => 'date',
        'fecha_fin_suscripcion' => 'date',
        'activo' => 'boolean',
        'metadata' => 'array',
        // Campos de facturación
        'auto_facturacion' => 'boolean',
    ];

    /**
     * Relación con plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relación con empresas
     */
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    /**
     * Relación con módulos (muchos a muchos)
     */
    public function modulos(): BelongsToMany
    {
        return $this->belongsToMany(Modulo::class, 'cliente_modulo')
            ->withPivot(['activo', 'fecha_activacion', 'fecha_vencimiento', 'configuracion'])
            ->withTimestamps();
    }

    /**
     * Solo módulos activos
     */
    public function modulosActivos(): BelongsToMany
    {
        return $this->modulos()->wherePivot('activo', true);
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function suscripcionActiva(): bool
    {
        if (!$this->activo) {
            return false;
        }

        // Si no hay fecha de fin, es ilimitada
        if (!$this->fecha_fin_suscripcion) {
            return true;
        }

        return $this->fecha_fin_suscripcion >= Carbon::today();
    }

    /**
     * Verificar si puede usar un módulo específico
     */
    public function puedeUsarModulo(string $slug): bool
    {
        if (!$this->suscripcionActiva()) {
            return false;
        }

        $modulo = $this->modulosActivos()
            ->where('slug', $slug)
            ->first();

        if (!$modulo) {
            return false;
        }

        // Verificar si el módulo tiene vencimiento
        if ($modulo->pivot->fecha_vencimiento) {
            return $modulo->pivot->fecha_vencimiento >= Carbon::today();
        }

        return true;
    }    /**
     * Obtener límites del plan
     */
    public function getLimites(): array
    {
        $metadata = $this->metadata ?? [];

        // Usar límites del plan como base
        $limitesBase = [
            'empresas' => $this->plan->limite_empresas ?? 1,
            'usuarios' => $this->plan->limite_usuarios ?? 10,
            'almacenamiento_gb' => $this->plan->limite_almacenamiento_gb ?? 1,
        ];

        // Override con metadata si existe
        return [
            'empresas' => $metadata['limite_empresas'] ?? $limitesBase['empresas'],
            'usuarios' => $metadata['limite_usuarios'] ?? $limitesBase['usuarios'],
            'almacenamiento_gb' => $metadata['limite_almacenamiento_gb'] ?? $limitesBase['almacenamiento_gb'],
        ];
    }

    /**
     * Verificar si puede crear más empresas
     */
    public function puedeCrearEmpresa(): bool
    {
        $limites = $this->getLimites();
        return $this->empresas()->count() < $limites['empresas'];
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSuscripcionVigente($query)
    {
        return $query->where('activo', true)
            ->where(function($q) {
                $q->whereNull('fecha_fin_suscripcion')
                  ->orWhere('fecha_fin_suscripcion', '>=', Carbon::today());
            });
    }

    /**
     * Verificar si es la empresa matriz del SaaS
     */
    public function esEmpresaMatriz(): bool
    {
        return ($this->metadata['es_empresa_matriz'] ?? false) === true;
    }

    /**
     * MUTADORES Y ACCESORES
     */

    /**
     * Mutador para normalizar el email
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * Mutador para normalizar el nombre
     */
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Mutador para normalizar RFC/NIT/RUC
     */
    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = $value ? strtoupper(trim($value)) : null;
    }

    /**
     * Mutador para normalizar razón social
     */
    public function setRazonSocialAttribute($value)
    {
        $this->attributes['razon_social'] = $value ? ucwords(strtolower(trim($value))) : null;
    }

    /**
     * Mutador para email de facturación
     */
    public function setEmailFacturacionAttribute($value)
    {
        $this->attributes['email_facturacion'] = $value ? strtolower(trim($value)) : null;
    }

    /**
     * Accesor para obtener días restantes de suscripción
     */
    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_fin_suscripcion) {
            return null;
        }

        $dias = Carbon::now()->diffInDays($this->fecha_fin_suscripcion, false);
        return $dias > 0 ? $dias : 0;
    }

    /**
     * Accesor para verificar si la suscripción está activa
     */
    public function getSuscripcionActivaAttribute(): bool
    {
        if (!$this->fecha_fin_suscripcion) {
            return false;
        }

        return Carbon::now()->lte($this->fecha_fin_suscripcion);
    }

    /**
     * Accesor para el estado de la suscripción
     */
    public function getEstadoSuscripcionAttribute(): string
    {
        if (!$this->fecha_fin_suscripcion) {
            return 'sin_suscripcion';
        }

        $now = Carbon::now();
        $finSuscripcion = $this->fecha_fin_suscripcion;

        if ($now->gt($finSuscripcion)) {
            return 'vencida';
        }

        $diasRestantes = $now->diffInDays($finSuscripcion);

        if ($diasRestantes <= 7) {
            return 'por_vencer';
        }

        return 'activa';
    }

    /**
     * Accesor para obtener el total de usuarios
     */
    public function getTotalUsuariosAttribute(): int
    {
        return $this->usuarios()->count();
    }

    /**
     * Accesor para obtener el total de empresas
     */
    public function getTotalEmpresasAttribute(): int
    {
        return $this->empresas()->count();
    }

    /**
     * Accesor para obtener el nombre fiscal (razón social o nombre)
     */
    public function getNombreFiscalAttribute(): string
    {
        return $this->razon_social ?: $this->nombre;
    }

    /**
     * Accesor para verificar si tiene configuración fiscal completa
     */
    public function getConfiguracionFiscalCompletaAttribute(): bool
    {
        return !empty($this->rfc) &&
               !empty($this->direccion_fiscal) &&
               !empty($this->ciudad_fiscal) &&
               !empty($this->regimen_fiscal);
    }

    /**
     * Accesor para obtener el email efectivo de facturación
     */
    public function getEmailFacturacionEfectivoAttribute(): string
    {
        return $this->email_facturacion ?: $this->email;
    }

    /**
     * Accesor para verificar si puede generar recibos fiscales
     */
    public function getPuedeGenerarRecibosFiscalesAttribute(): bool
    {
        return $this->configuracion_fiscal_completa &&
               !empty($this->uso_cfdi);
    }
}
