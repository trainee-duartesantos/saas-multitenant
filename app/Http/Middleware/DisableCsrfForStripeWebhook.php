<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForStripeWebhook
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('stripe/webhook')) {
            app('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken')
                ->disableFor($request);
        }

        return $next($request);
    }
}
