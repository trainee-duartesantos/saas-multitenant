<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'tenant_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $tenantId = $request->tenant_id;

        $belongs = $user->tenants()
            ->where('tenants.id', $tenantId)
            ->exists();

        if (! $belongs) {
            abort(403, 'You do not belong to this tenant.');
        }

        session(['tenant_id' => $tenantId]);

        return back();
    }
}
