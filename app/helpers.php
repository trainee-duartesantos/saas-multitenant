<?php

use App\Models\Tenant;
use App\Enums\TenantRole;

if (! function_exists('currentTenantRole')) {
    function currentTenantRole(): ?TenantRole
    {
        $user = auth()->user();
        $tenantId = session('tenant_id');

        if (! $user || ! $tenantId) {
            return null;
        }

        $pivot = $user->tenants
            ->firstWhere('id', $tenantId)
            ?->pivot;

        return $pivot
            ? TenantRole::from($pivot->role)
            : null;
    }

    if (! function_exists('isTenantOwner')) {
        function isTenantOwner(): bool
        {
            return currentTenantRole() === TenantRole::OWNER;
        }
    }

    if (! function_exists('canManageTenant')) {
        function canManageTenant(): bool
        {
            return in_array(
                currentTenantRole(),
                [TenantRole::OWNER, TenantRole::ADMIN],
                true
            );
        }
    }
    

}
