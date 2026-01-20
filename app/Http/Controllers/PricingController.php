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

        $plans = Plan::all()->map(fn ($plan) => [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => $plan->price,
            'max_members' => $plan->max_members,
            'max_projects' => $plan->max_projects,
            'has_priority_support' => $plan->has_priority_support,
            'stripe_price_id' => $plan->stripe_price_id,
            'is_current' => $tenant->plan_id === $plan->id,
        ]);

        return Inertia::render('Pricing/Index', [
            'plans' => $plans,
        ]);
    }
}
