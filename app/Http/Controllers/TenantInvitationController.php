<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
use App\Enums\TenantRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TenantInvitationController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        if (! $tenant->canInviteMember()) {
            return back()->with('error', 'O seu plano atingiu o limite de membros. Faça upgrade para convidar mais.');
        }

        $request->validate([
            'email' => 'required|email',
            'role' => 'required|string',
        ]);

        $tenantId = session('tenant_id');

        abort_unless(
            currentTenantRole()?->isOwner(),
            403
        );

        if (
            TenantInvitation::where('tenant_id', $tenantId)
                ->where('email', $request->email)
                ->whereNull('accepted_at')
                ->exists()
        ) {
            return back()->with('error', 'Este email já tem um convite pendente.');
        }

        TenantInvitation::create([
            'tenant_id' => $tenantId,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::uuid(),
        ]);

        return redirect()->back()->with('success', 'Convite enviado.');
    }

    public function destroy(Request $request, TenantInvitation $invitation)
    {
        $tenant = $request->attributes->get('tenant');

        $this->authorize('manageInvitations', $tenant);

        abort_unless(
            $invitation->tenant_id === $tenant->id,
            403
        );

        $invitation->delete();

        return back()->with('success', 'Convite cancelado.');
    }

    public function resend(Request $request, TenantInvitation $invitation)
    {
        $tenant = $request->attributes->get('tenant');

        $this->authorize('manageInvitations', $tenant);

        abort_unless(
            $invitation->tenant_id === $tenant->id,
            403
        );

        // aqui depois podes ligar email real
        // Mail::to($invitation->email)->send(...)

        return back()->with('success', 'Convite reenviado.');
    }
}
