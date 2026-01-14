<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        abort_unless(
            auth()->user()->tenants()->where('tenants.id', $request->tenant_id)->exists(),
            403
        );

        session(['tenant_id' => $request->tenant_id]);

        return back();
    }
}
