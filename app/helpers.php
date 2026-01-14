<?php

use App\Models\Tenant;

if (! function_exists('currentTenant')) {
    function currentTenant(): ?Tenant
    {
        $tenantId = session('tenant_id');

        return $tenantId
            ? Tenant::find($tenantId)
            : null;
    }
}
