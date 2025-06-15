<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario',
        'empresa_id',
        'activo',
        'ultimo_acceso',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
        'ultimo_acceso' => 'datetime',
    ];

    /**
     * Relación con empresa (para usuarios normales)
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Verificar si es Super Administrador
     */
    public function esSuperAdmin(): bool
    {
        return $this->tipo_usuario === 'super_admin';
    }

    /**
     * Verificar si es usuario normal (de empresa)
     */
    public function esUsuarioNormal(): bool
    {
        return $this->tipo_usuario === 'empresa';
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para Super Administradores
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('tipo_usuario', 'super_admin');
    }

    /**
     * Scope para usuarios normales
     */
    public function scopeUsuariosNormales($query)
    {
        return $query->where('tipo_usuario', 'empresa');
    }

    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso(): void
    {
        $this->update(['ultimo_acceso' => now()]);
    }

    /**
     * Obtener empresa del usuario o null si es super admin
     */
    public function obtenerEmpresa(): ?Empresa
    {
        if ($this->esSuperAdmin()) {
            return null;
        }

        return $this->empresa;
    }

    /**
     * Verificar si puede acceder a un módulo
     */
    public function puedeUsarModulo(string $slug): bool
    {
        if ($this->esSuperAdmin()) {
            return true; // Super admin puede usar todos los módulos
        }

        if (!$this->empresa) {
            return false;
        }

        return $this->empresa->puedeUsarModulo($slug);
    }

    /**
     * Obtener nombre para mostrar
     */
    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->name;

        if ($this->esSuperAdmin()) {
            $nombre .= ' (Super Admin)';
        } elseif ($this->empresa) {
            $nombre .= ' - ' . $this->empresa->nombre;
        }

        return $nombre;
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
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Accesor para obtener iniciales
     */
    public function getInicialesAttribute(): string
    {
        $palabras = explode(' ', $this->name);
        $iniciales = '';
        
        foreach (array_slice($palabras, 0, 2) as $palabra) {
            $iniciales .= strtoupper(substr($palabra, 0, 1));
        }
        
        return $iniciales ?: 'US';
    }

    /**
     * Accesor para el estado del usuario
     */
    public function getEstadoAttribute(): string
    {
        if (!$this->activo) {
            return 'inactivo';
        }

        if (!$this->ultimo_acceso) {
            return 'nunca_conectado';
        }

        $diasSinConectar = Carbon::now()->diffInDays($this->ultimo_acceso);

        if ($diasSinConectar === 0) {
            return 'activo_hoy';
        } elseif ($diasSinConectar <= 7) {
            return 'activo_reciente';
        } elseif ($diasSinConectar <= 30) {
            return 'activo_mes';
        } else {
            return 'inactivo_mucho_tiempo';
        }
    }

    /**
     * Accesor para verificar si está online
     */
    public function getEstaOnlineAttribute(): bool
    {
        if (!$this->ultimo_acceso) {
            return false;
        }

        return Carbon::now()->diffInMinutes($this->ultimo_acceso) <= 5;
    }
}
