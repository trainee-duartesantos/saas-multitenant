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
        $onboarding = $tenant->onboarding;

        return Inertia::render('Onboarding/Index', [
            'tenant' => $tenant,
            'onboarding' => $onboarding,
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

        // Avança o wizard
        $tenant->onboarding->update([
            'current_step' => 'members',
        ]);

        return redirect()->route('onboarding.index');
    }
}
