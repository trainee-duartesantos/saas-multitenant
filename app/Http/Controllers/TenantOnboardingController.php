<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Plan;

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

        // Atualiza nome do tenant
        $tenant->update([
            'name' => $request->name,
        ]);

        // 🟢 ATRIBUIR PLANO FREE + TRIAL (só se ainda não tiver)
        if (! $tenant->plan_id) {
            $freePlan = Plan::where('slug', 'free')->firstOrFail();

            $tenant->update([
                'plan_id' => $freePlan->id,
                'trial_ends_at' => now()->addDays(14),
            ]);
        }

        // Avançar onboarding
        $tenant->onboarding->update([
            'current_step' => 'members',
        ]);

        // Caso raro: já tem membros
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
