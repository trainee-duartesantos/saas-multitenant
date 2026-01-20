<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{

    public function checkout(Request $request, Plan $plan)
    {
        $tenant = $request->attributes->get('tenant');
        $user = $request->user();

        // 🔒 Só o Owner pode fazer upgrade
        abort_unless(
            $user->isOwnerOfTenant($tenant->id),
            403,
            'Apenas o owner pode alterar o plano.'
        );

        abort_unless($plan->stripe_price_id, 403, 'Plano não subscritível');

        $checkout = $tenant
            ->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url' => route('pricing.index'),
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                ],
            ]);

        return Inertia::location($checkout->url);
    }

    public function success(Request $request)
    {
        return redirect()
            ->route('dashboard')
            ->with('success', 'Plano atualizado com sucesso.');
    }
}
