<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        $onboarding = $tenant->onboarding;

        if ($onboarding->completed) {
            return redirect()->route('dashboard');
        }

        return inertia('Onboarding/Index', [
            'step' => $onboarding->current_step,
            'tenant' => [
                'name' => $tenant->name,
            ],
        ]);
    }

    public function storeTenant(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant->update([
            'name' => $request->name,
        ]);

        $tenant->onboarding->update([
            'current_step' => 'members',
        ]);

        return redirect()->route('onboarding.show');
    }
}
