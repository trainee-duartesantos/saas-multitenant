<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenantId = session('tenant_id');

        if (! $user || ! $tenantId) {
            abort(403);
        }

        $belongs = $user->tenants()
            ->where('tenants.id', $tenantId)
            ->exists();

        if (! $belongs) {
            abort(403, 'You do not belong to this tenant.');
        }

        return $next($request);
    }
}
