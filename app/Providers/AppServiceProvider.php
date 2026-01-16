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

            'onboardingChecklist' => function () {
                $user = auth()->user();
                $tenantId = session('tenant_id');

                if (! $user || ! $tenantId) {
                    return null;
                }

                $tenant = $user->tenants()->find($tenantId);

                if (! $tenant || ! $tenant->onboarding) {
                    return null;
                }

                return [
                    [
                        'key' => 'tenant',
                        'label' => 'Configurar tenant',
                        'done' => true,
                    ],
                    [
                        'key' => 'members',
                        'label' => 'Convidar membros',
                        'done' => $tenant->users()->count() > 1,
                    ],
                ];
            },

        ]);
        Tenant::observe(TenantObserver::class);
    }
}
