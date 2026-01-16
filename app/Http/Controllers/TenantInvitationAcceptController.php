<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantInvitationAcceptController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        // 1) convite válido
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            abort(410, 'Invitation already accepted.');
        }

        // 2) precisa estar autenticado
        if (! $request->user()) {
            session(['pending_invitation_token' => $token]);
            return redirect()->route('login');
        }

        $user = $request->user();

        // 3) segurança: email tem de bater certo
        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403, 'This invitation was sent to another email address.');
        }

        // 4) operação atómica
        DB::transaction(function () use ($user, $invitation) {
            if (! $user->tenants()->where('tenants.id', $invitation->tenant_id)->exists()) {
                $user->tenants()->attach($invitation->tenant_id, [
                    'role' => $invitation->role,
                ]);
            }

            $invitation->forceFill([
                'accepted_at' => now(),
            ])->save();
        });

        // 5) selecionar tenant ativo
        session(['tenant_id' => $invitation->tenant_id]);
        session()->forget('pending_invitation_token');

        return redirect()->route('dashboard');
    }

    public function show(string $token)
    {
        $invitation = TenantInvitation::with('tenant')
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->isAccepted()) {
            return inertia('Invitations/AlreadyAccepted');
        }

        return inertia('Invitations/Accept', [
            'invitation' => [
                'email' => $invitation->email,
                'role' => $invitation->role,
                'tenant' => [
                    'name' => $invitation->tenant->name,
                ],
            ],
            'authenticated' => auth()->check(),
        ]);
    }

    /**
     * Aceitar convite
     */
    public function accept(Request $request, string $token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            abort(410, 'Invitation already accepted.');
        }

        if (! $request->user()) {
            session(['pending_invitation_token' => $token]);
            return redirect()->route('login');
        }

        $user = $request->user();

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403, 'This invitation was sent to another email address.');
        }

        DB::transaction(function () use ($user, $invitation) {
            if (! $user->tenants()->where('tenants.id', $invitation->tenant_id)->exists()) {
                $user->tenants()->attach($invitation->tenant_id, [
                    'role' => $invitation->role,
                ]);
            }

            $invitation->update([
                'accepted_at' => now(),
            ]);

            // ✅ FECHAR ONBOARDING AQUI
            $tenant = $invitation->tenant;

            if ($tenant->onboarding && ! $tenant->onboarding->completed) {
                $tenant->onboarding->update([
                    'completed' => true,
                ]);
            }
        });

        session(['tenant_id' => $invitation->tenant_id]);
        session()->forget('pending_invitation_token');

        return redirect()->route('dashboard');
    }
}
