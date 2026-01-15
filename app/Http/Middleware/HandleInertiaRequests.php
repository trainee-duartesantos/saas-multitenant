<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\User;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () {

                /** @var \App\Models\User|null $user */
                $user = request()->user()?->load('tenants');

                if ($user && !session()->has('tenant_id')) {
                    session(['tenant_id' => $user->tenants->first()?->id]);
                }

                return [
                    'user' => $user,
                    'tenants' => $user?->tenants->map(fn ($tenant) => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                    ])->values(),
                    'currentTenantId' => session('tenant_id'),

                    'currentTenantRole' => currentTenantRole()?->value,
                ];
            },
        ]);
    }
}
