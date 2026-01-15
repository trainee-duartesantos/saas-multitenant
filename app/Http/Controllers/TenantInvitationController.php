<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
use App\Enums\TenantRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantInvitationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|string',
        ]);

        $tenantId = session('tenant_id');

        abort_unless(
            currentTenantRole()?->isOwner(),
            403
        );

        TenantInvitation::create([
            'tenant_id' => $tenantId,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Invitation sent.',
        ], 201);

    }
}
