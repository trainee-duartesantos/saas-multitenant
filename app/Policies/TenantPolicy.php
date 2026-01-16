<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;
use App\Enums\TenantRole;

class TenantPolicy
{
    /**
     * Alterar role de um membro
     */
    public function updateMember(User $authUser, Tenant $tenant, User $targetUser): bool
    {
        // Não pode alterar a si próprio
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        // Apenas OWNER ou ADMIN
        return in_array(
            currentTenantRole(),
            [TenantRole::OWNER, TenantRole::ADMIN],
            true
        );
    }

    /**
     * Remover um membro do tenant
     */
    public function removeMember(User $authUser, Tenant $tenant, User $targetUser): bool
    {
        // Não pode remover a si próprio
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        // Não pode remover o OWNER
        if ($targetUser->hasRole(TenantRole::OWNER, $tenant)) {
            return false;
        }

        // Apenas OWNER ou ADMIN
        return in_array(
            currentTenantRole(),
            [TenantRole::OWNER, TenantRole::ADMIN],
            true
        );
    }

    public function manageInvitations(User $user, Tenant $tenant): bool
    {
        return in_array(
            $user->roleForTenant($tenant),
            ['owner', 'admin'],
            true
        );
    }

    public function transferOwnership(User $user, Tenant $tenant, User $newOwner): bool
    {
        // Só o owner atual pode transferir
        if ($user->roleForTenant($tenant) !== 'owner') {
            return false;
        }

        // Não pode transferir para si próprio
        if ($user->id === $newOwner->id) {
            return false;
        }

        // Novo owner tem de pertencer ao tenant
        return $newOwner->tenants()
            ->where('tenants.id', $tenant->id)
            ->exists();
    }
}
