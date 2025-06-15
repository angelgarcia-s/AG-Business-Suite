<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'precio_mensual',
        'precio_anual',
        'precio_usuario_adicional_mensual',
        'precio_usuario_adicional_anual',
        'precio_empresa_adicional_mensual',
        'precio_empresa_adicional_anual',
        'limite_empresas',
        'limite_usuarios',
        'permite_empresas_adicionales',
        'permite_usuarios_adicionales',
        'maximo_empresas_totales',
        'maximo_usuarios_totales',
        'limite_almacenamiento_gb',
        'soporte_prioritario',
        'backup_automatico',
        'api_acceso',
        'orden',
        'activo',
        'destacado',
        'caracteristicas',
        'configuracion',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'precio_usuario_adicional_mensual' => 'decimal:2',
        'precio_usuario_adicional_anual' => 'decimal:2',
        'precio_empresa_adicional_mensual' => 'decimal:2',
        'precio_empresa_adicional_anual' => 'decimal:2',
        'permite_usuarios_adicionales' => 'boolean',
        'permite_empresas_adicionales' => 'boolean',
        'soporte_prioritario' => 'boolean',
        'backup_automatico' => 'boolean',
        'api_acceso' => 'boolean',
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'caracteristicas' => 'array',
        'configuracion' => 'array',
    ];

    /**
     * Relación con clientes
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'plan_id');
    }

    /**
     * Calcular descuento anual
     */
    public function getDescuentoAnualAttribute(): float
    {
        if ($this->precio_mensual == 0) {
            return 0;
        }

        $precioAnualSinDescuento = $this->precio_mensual * 12;
        return round((($precioAnualSinDescuento - $this->precio_anual) / $precioAnualSinDescuento) * 100, 1);
    }

    /**
     * Obtener precio formateado
     */
    public function getPrecioMensualFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio_mensual, 2);
    }

    public function getPrecioAnualFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio_anual, 2);
    }

    /**
     * Verificar si el plan permite algo específico
     */
    public function permite(string $caracteristica): bool
    {
        return in_array($caracteristica, $this->caracteristicas ?? []);
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    /**
     * Calcular precio total para un cliente específico
     */
    public function calcularPrecioTotal(Cliente $cliente, string $periodo = 'mensual'): array
    {
        $totalUsuarios = $cliente->usuarios()->count();
        $totalEmpresas = $cliente->empresas()->count();

        // Precio base del plan
        $precioBase = $periodo === 'anual' ? $this->precio_anual : $this->precio_mensual;

        // Calcular usuarios adicionales
        $usuariosAdicionales = 0;
        $costoUsuariosAdicionales = 0;

        if ($totalUsuarios > $this->limite_usuarios && $this->permite_usuarios_adicionales) {
            $usuariosAdicionales = $totalUsuarios - $this->limite_usuarios;
            $precioUsuarioAdicional = $periodo === 'anual' ?
                $this->precio_usuario_adicional_anual :
                $this->precio_usuario_adicional_mensual;
            $costoUsuariosAdicionales = $usuariosAdicionales * $precioUsuarioAdicional;
        }

        // Calcular empresas adicionales
        $empresasAdicionales = 0;
        $costoEmpresasAdicionales = 0;

        if ($totalEmpresas > $this->limite_empresas && $this->permite_empresas_adicionales) {
            $empresasAdicionales = $totalEmpresas - $this->limite_empresas;
            $precioEmpresaAdicional = $periodo === 'anual' ?
                $this->precio_empresa_adicional_anual :
                $this->precio_empresa_adicional_mensual;
            $costoEmpresasAdicionales = $empresasAdicionales * $precioEmpresaAdicional;
        }

        $precioTotal = $precioBase + $costoUsuariosAdicionales + $costoEmpresasAdicionales;

        return [
            'precio_base' => $precioBase,
            'usuarios_incluidos' => $this->limite_usuarios,
            'usuarios_adicionales' => $usuariosAdicionales,
            'costo_usuarios_adicionales' => $costoUsuariosAdicionales,
            'empresas_incluidas' => $this->limite_empresas,
            'empresas_adicionales' => $empresasAdicionales,
            'costo_empresas_adicionales' => $costoEmpresasAdicionales,
            'precio_total' => $precioTotal,
            'periodo' => $periodo,
            'desglose' => [
                'Plan ' . $this->nombre => '$' . number_format($precioBase, 2),
                'Usuarios adicionales (' . $usuariosAdicionales . ')' => '$' . number_format($costoUsuariosAdicionales, 2),
                'Empresas adicionales (' . $empresasAdicionales . ')' => '$' . number_format($costoEmpresasAdicionales, 2),
                'TOTAL' => '$' . number_format($precioTotal, 2),
            ]
        ];
    }

    /**
     * Verificar si el cliente puede agregar más usuarios
     */
    public function puedeAgregarUsuarios(Cliente $cliente): bool
    {
        $totalUsuarios = $cliente->usuarios()->count();

        // Si está dentro del límite base, puede agregar
        if ($totalUsuarios < $this->limite_usuarios) {
            return true;
        }

        // Si permite usuarios adicionales, verificar límite máximo
        if ($this->permite_usuarios_adicionales) {
            return $this->maximo_usuarios_totales === null ||
                   $totalUsuarios < $this->maximo_usuarios_totales;
        }

        return false;
    }

    /**
     * Verificar si el cliente puede agregar más empresas
     */
    public function puedeAgregarEmpresas(Cliente $cliente): bool
    {
        $totalEmpresas = $cliente->empresas()->count();

        // Si está dentro del límite base, puede agregar
        if ($totalEmpresas < $this->limite_empresas) {
            return true;
        }

        // Si permite empresas adicionales, verificar límite máximo
        if ($this->permite_empresas_adicionales) {
            return $this->maximo_empresas_totales === null ||
                   $totalEmpresas < $this->maximo_empresas_totales;
        }

        return false;
    }

    /**
     * Verificar si es plan ilimitado
     */
    public function esIlimitado(): bool
    {
        return $this->slug === 'ilimitado' ||
               $this->limite_empresas >= 999999 ||
               $this->limite_usuarios >= 999999;
    }
}
