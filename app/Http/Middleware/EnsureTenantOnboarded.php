<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->attributes->get('tenant');

        // Segurança extra
        if (! $tenant) {
            return redirect()->route('dashboard');
        }

        $onboarding = $tenant->onboarding;

        if ($onboarding && ! $onboarding->completed) {
            if (
                ! $request->routeIs('onboarding.*') &&
                ! $request->routeIs('members.*')
            ) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}
