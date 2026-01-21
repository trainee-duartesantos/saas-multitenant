<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user()?->load('tenants');

        if ($user && ! session()->has('tenant_id')) {
            session(['tenant_id' => $user->tenants->first()?->id]);
        }

        $currentTenant = null;

        if ($user && session()->has('tenant_id')) {
            $currentTenant = $user->tenants()
                ->with([
                    'plan',
                    'users:id',
                    'invitations' => fn ($q) => $q->whereNull('accepted_at'),
                ])
                ->find(session('tenant_id'));
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'tenants' => $user?->tenants->map(fn ($tenant) => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                ])->values(),

                'currentTenantId' => session('tenant_id'),
                'currentTenantRole' => currentTenantRole()?->value,

                'currentTenant' => $currentTenant ? [
                    'id' => $currentTenant->id,
                    'name' => $currentTenant->name,

                    'usage' => [
                        'members' =>
                            ($currentTenant->users->count() ?? 0)
                            + ($currentTenant->invitations->count() ?? 0),
                    ],

                    'plan' => $currentTenant->plan ? [
                        'slug' => $currentTenant->plan->slug,
                        'name' => $currentTenant->plan->name,
                        'features' => [
                            'billing_access' => (bool) $currentTenant->plan->billing_access,
                            'advanced_permissions' => (bool) $currentTenant->plan->advanced_permissions,
                            'has_priority_support' => (bool) $currentTenant->plan->has_priority_support,
                        ],
                        'limits' => [
                            'max_members' => $currentTenant->plan->max_members,
                            'max_projects' => $currentTenant->plan->max_projects,
                        ],
                    ] : null,
                ] : null,

            ],

            'pendingInvitationsCount' => fn () =>
                $user
                    ? \App\Models\TenantInvitation::where('email', $user->email)
                        ->whereNull('accepted_at')
                        ->count()
                    : 0,
                    
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],

            'csrf_token' => csrf_token(),
        ]);
    }
}
