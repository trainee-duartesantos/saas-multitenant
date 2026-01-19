<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, string $feature)
    {
        $tenant = $request->attributes->get('tenant');

        abort_unless($tenant && $tenant->hasFeature($feature), 403);

        return $next($request);
    }
}
