<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = session('tenant_id');
        $user = $request->user();

        if (! $tenantId || ! $user) {
            abort(403, 'Tenant not selected');
        }

        if (! $user->tenants()->where('tenants.id', $tenantId)->exists()) {
            abort(403, 'You do not belong to this tenant');
        }

        return $next($request);
    }
}
