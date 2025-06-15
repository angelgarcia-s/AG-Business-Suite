<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Modulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'activo',
        'orden',
        'categoria',
        'configuracion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'configuracion' => 'array',
    ];

    /**
     * Relación con clientes que tienen este módulo
     */
    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cliente_modulo')
            ->withPivot(['activo', 'fecha_activacion', 'fecha_vencimiento', 'configuracion'])
            ->withTimestamps();
    }

    /**
     * Solo clientes activos con este módulo
     */
    public function clientesActivos(): BelongsToMany
    {
        return $this->clientes()
            ->wherePivot('activo', true)
            ->where('clientes.activo', true);
    }

    /**
     * Scope para módulos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Obtener módulos ordenados
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }
}
