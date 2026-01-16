<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        $tenant->onboarding()->create([
            'current_step' => 'tenant',
            'completed' => false,
        ]);
    }
}
