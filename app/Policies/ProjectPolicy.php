<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use App\Enums\TenantRole;

class ProjectPolicy
{
    /**
     * Qualquer user do tenant pode ver projetos
     */
    public function viewAny(User $user): bool
    {
        return currentTenantRole() !== null;
    }

    public function view(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Apenas OWNER e ADMIN podem criar
     */
    public function create(User $user): bool
    {
        return in_array(
            currentTenantRole(),
            [TenantRole::OWNER, TenantRole::ADMIN],
            true
        );
    }

    /**
     * Apenas OWNER e ADMIN podem atualizar
     */
    public function update(User $user, Project $project): bool
    {
        return in_array(
            currentTenantRole(),
            [TenantRole::OWNER, TenantRole::ADMIN],
            true
        );
    }

    /**
     * Apenas OWNER pode apagar
     */
    public function delete(User $user, Project $project): bool
    {
        return currentTenantRole() === TenantRole::OWNER;
    }
}
