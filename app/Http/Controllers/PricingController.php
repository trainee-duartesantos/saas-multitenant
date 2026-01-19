<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        return Inertia::render('Pricing/Index', [
            'plans' => Plan::where('is_active', true)->get(),
            'currentPlanId' => $tenant->plan_id,
        ]);
    }

    public function upgrade(Request $request, Plan $plan)
    {
        $tenant = $request->attributes->get('tenant');

        if ($tenant->plan_id === $plan->id) {
            return back()->with('error', 'Este já é o seu plano atual.');
        }

        $tenant->update([
            'plan_id' => $plan->id,
            'trial_ends_at' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', "Plano atualizado para {$plan->name}.");
    }
}
