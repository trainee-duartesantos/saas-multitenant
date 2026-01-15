<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TenantMemberController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $members = $tenant->users()
            ->select('users.id', 'users.name', 'users.email', 'tenant_user.role')
            ->get();

        $invitations = $tenant->invitations()->pending()->get();

        return inertia('Members/Index', [
            'members' => $members,
            'invitations' => $invitations,
            'currentUserId' => $request->user()->id,
            'currentUserRole' => $request->user()->roleForTenant($tenant),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $tenant = $request->attributes->get('tenant');

        $this->authorize('updateMember', [$tenant, $user]);
        dd('PASSOU');

        $request->validate([
            'role' => 'required|in:owner,admin,member',
        ]);

        $tenant->users()->updateExistingPivot($user->id, [
            'role' => $request->role,
        ]);

        return back()->with('success', 'Role atualizada.');
    }

    public function destroy(Request $request, User $user)
    {
        $tenant = $request->attributes->get('tenant');

        $this->authorize('removeMember', [$tenant, $user]);

        $tenant->users()->detach($user->id);

        return back()->with('success', 'Membro removido.');
    }
}
