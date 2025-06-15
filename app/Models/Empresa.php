<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'nombre',
        'codigo_pais',
        'pais',
        'ciudad',
        'direccion',
        'telefono',
        'email',
        'rfc',
        'logo',
        'timezone',
        'moneda',
        'activo',
        'configuracion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'configuracion' => 'array',
    ];

    /**
     * Relación con cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con usuarios de la empresa
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Solo usuarios activos
     */
    public function usuariosActivos(): HasMany
    {
        return $this->usuarios()->where('activo', true);
    }

    /**
     * Verificar si puede usar un módulo específico
     */
    public function puedeUsarModulo(string $slug): bool
    {
        return $this->cliente->puedeUsarModulo($slug);
    }

    /**
     * Obtener módulos disponibles para esta empresa
     */
    public function modulosDisponibles()
    {
        return $this->cliente->modulosActivos();
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
     * Mutador para normalizar el RFC
     */
    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = strtoupper(trim($value));
    }

    /**
     * Accesor para obtener la URL completa del logo
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        // Si ya es una URL completa, devolverla
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        // Si es un path relativo, generar URL completa
        return Storage::url($this->logo);
    }

    /**
     * Accesor para obtener iniciales de la empresa
     */
    public function getInicialesAttribute(): string
    {
        $palabras = explode(' ', $this->nombre);
        $iniciales = '';

        foreach (array_slice($palabras, 0, 3) as $palabra) {
            $iniciales .= strtoupper(substr($palabra, 0, 1));
        }

        return $iniciales ?: 'EMP';
    }

    /**
     * Accesor para obtener el total de usuarios
     */
    public function getTotalUsuariosAttribute(): int
    {
        return $this->usuarios()->count();
    }

    /**
     * Accesor para obtener usuarios activos
     */
    public function getUsuariosActivosAttribute(): int
    {
        return $this->usuarios()->where('activo', true)->count();
    }

    /**
     * Verificar si puede crear más usuarios
     */
    public function puedeCrearUsuario(): bool
    {
        $limites = $this->cliente->getLimites();
        $totalUsuariosCliente = User::where('empresa_id', '!=', null)
            ->whereHas('empresa', function($query) {
                $query->where('cliente_id', $this->cliente_id);
            })->count();

        return $totalUsuariosCliente < $limites['usuarios'];
    }

    /**
     * Obtener configuración específica
     */
    public function getConfiguracion(string $key, $default = null)
    {
        return data_get($this->configuracion, $key, $default);
    }

    /**
     * Establecer configuración específica
     */
    public function setConfiguracion(string $key, $value): void
    {
        $configuracion = $this->configuracion ?? [];
        data_set($configuracion, $key, $value);
        $this->update(['configuracion' => $configuracion]);
    }

    /**
     * Scopes
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorPais($query, string $pais)
    {
        return $query->where('pais', $pais);
    }

    /**
     * Verificar si es la empresa matriz del SaaS
     */
    public function esEmpresaMatriz(): bool
    {
        return $this->cliente->esEmpresaMatriz() &&
               ($this->configuracion['es_empresa_matriz'] ?? false) === true;
    }

    /**
     * Obtener nombre completo para mostrar
     */
    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->nombre;

        if ($this->esEmpresaMatriz()) {
            $nombre .= ' (Matriz SaaS)';
        } else {
            $nombre .= ' - ' . $this->cliente->nombre;
        }

        return $nombre;
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Al eliminar una empresa, eliminar su logo del storage
        static::deleting(function ($empresa) {
            if ($empresa->logo && !filter_var($empresa->logo, FILTER_VALIDATE_URL)) {
                Storage::delete($empresa->logo);
            }
        });
    }
}
