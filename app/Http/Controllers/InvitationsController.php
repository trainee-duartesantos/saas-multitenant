<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TenantInvitation;

class InvitationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $invitations = TenantInvitation::with('tenant')
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->latest()
            ->get()
            ->map(fn ($inv) => [
                'id' => $inv->id,
                'token' => $inv->token,
                'role' => $inv->role,
                'tenant' => [
                    'name' => $inv->tenant->name,
                ],
                'created_at' => $inv->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }
}
