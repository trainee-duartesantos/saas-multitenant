<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantOnboardingController extends Controller
{
    public function index(Request $request)
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

        $tenant->onboarding->update([
            'current_step' => 'members',
        ]);

        // Se já houver membros, fecha logo
        if ($tenant->users()->count() > 1) {
            $tenant->onboarding->update(['completed' => true]);
            return redirect()->route('dashboard');
        }

        return redirect()->route('onboarding.index');
    }

    public function complete(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $tenant->onboarding->update([
            'completed' => true,
        ]);

        return redirect(
            $request->input('redirect', route('dashboard'))
        );
    }
}
