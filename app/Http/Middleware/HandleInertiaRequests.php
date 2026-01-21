<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\TenantInvitation;
use App\Models\Project;

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
                ->with('plan')
                ->find(session('tenant_id'));
        }

        $usage = null;

        if ($currentTenant) {
            $membersCount = $currentTenant->users()->count();
            $pendingInvitesCount = TenantInvitation::where('tenant_id', $currentTenant->id)
                ->whereNull('accepted_at')
                ->count();

            $projectsCount = Project::where('tenant_id', $currentTenant->id)->count();

            $usage = [
                'members' => $membersCount + $pendingInvitesCount,
                'projects' => $projectsCount,
            ];
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
                    'usage' => $usage,
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
                    ? TenantInvitation::where('email', $user->email)
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
