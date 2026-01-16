<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Tenant;
use App\Observers\TenantObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => function () {

                /** @var User|null $user */
                $user = auth()->user();

                return [
                    'user' => $user,
                    'tenants' => $user?->tenants()->get(['id', 'name']),
                    'currentTenantId' => session('tenant_id'),
                ];
            },
        ]);
        Tenant::observe(TenantObserver::class);
    }
}
