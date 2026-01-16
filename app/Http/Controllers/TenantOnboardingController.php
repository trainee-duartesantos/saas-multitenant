<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class TenantOnboardingController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        return inertia('Onboarding/Index', [
            'onboarding' => $tenant->onboarding,
        ]);
    }

    public function show(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        return Inertia::render('Onboarding/Index', [
            'tenant' => $tenant,
            'onboarding' => $tenant->onboarding,
        ]);
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant = $request->attributes->get('tenant');

        $tenant->update([
            'name' => $request->name,
        ]);

        $onboarding = $tenant->onboarding;

        $onboarding->update([
            'current_step' => 'members',
        ]);

        // Caso raro: já tem membros
        if ($tenant->users()->count() > 1) {
            $onboarding->update([
                'completed' => true,
            ]);
        }

        return redirect()->route('onboarding.index');
    }
}
