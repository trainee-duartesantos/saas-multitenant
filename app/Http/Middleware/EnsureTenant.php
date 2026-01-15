<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    public function handle(Request $request, Closure $next)
    {
        $tenantId = session('tenant_id');

        if (! $tenantId) {
            abort(403, 'No active tenant selected.');
        }

        // ✅ BUSCAR O TENANT
        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            abort(403, 'Tenant not found.');
        }

        // ✅ VALIDAR SE O USER PERTENCE AO TENANT
        $user = $request->user();

        if (! $user->tenants()->where('tenants.id', $tenantId)->exists()) {
            abort(403, 'You do not belong to this tenant.');
        }

        // ✅ INJETAR O TENANT NO REQUEST
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
