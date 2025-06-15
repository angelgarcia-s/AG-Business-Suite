<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Super Admin puede acceder a cualquier tenant o sin tenant
        if ($user->esSuperAdmin()) {
            $this->setTenantContext($request);
            return $next($request);
        }

        // Usuario normal debe tener empresa asignada
        if (!$user->empresa_id) {
            abort(403, 'Usuario sin empresa asignada');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            abort(403, 'Empresa no encontrada');
        }

        if (!$empresa->activo) {
            abort(403, 'Empresa inactiva');
        }

        // Verificar que la suscripción del cliente esté activa
        if (!$empresa->cliente->suscripcion_activa) {
            abort(403, 'Suscripción vencida o inactiva');
        }

        // Establecer contexto de tenant
        $this->setTenantInContext($empresa, $request);

        return $next($request);
    }

    /**
     * Establecer contexto de tenant para Super Admin
     */
    private function setTenantContext(Request $request): void
    {
        // Para Super Admin, intentar obtener tenant desde parámetros o sesión
        $empresaId = $request->get('tenant_id') ?? session('current_tenant_id');

        if ($empresaId) {
            $empresa = Empresa::find($empresaId);
            if ($empresa) {
                $this->setTenantInContext($empresa, $request);
                session(['current_tenant_id' => $empresa->id]);
            }
        }
    }

    /**
     * Establecer empresa en el contexto global
     */
    private function setTenantInContext(Empresa $empresa, Request $request): void
    {
        // Disponible globalmente en la aplicación
        app()->instance('current_tenant', $empresa);

        // Disponible en las vistas
        View::share('currentTenant', $empresa);

        // Disponible en la request
        $request->attributes->set('tenant', $empresa);

        // Configurar zona horaria de la empresa
        if ($empresa->timezone) {
            config(['app.timezone' => $empresa->timezone]);
            date_default_timezone_set($empresa->timezone);
        }

        // Configurar moneda de la empresa
        if ($empresa->moneda) {
            config(['app.currency' => $empresa->moneda]);
        }
    }
}
