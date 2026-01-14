<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsSet
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('tenant_id')) {
            abort(403, 'No tenant selected.');
        }

        return $next($request);
    }
}
