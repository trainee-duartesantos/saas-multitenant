<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\BillingLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function checkout(Request $request, Plan $plan)
    {
        $tenant = $request->attributes->get('tenant');
        $user = $request->user();

        abort_unless(
            $user->isOwnerOfTenant($tenant->id),
            403,
            'Only owners can change the plan.'
        );

        abort_unless($plan->stripe_price_id, 403);

        // 🔁 JÁ TEM SUBSCRIÇÃO → SWAP
        if ($subscription = $tenant->activeSubscription()) {

            $previousPlanId = $tenant->plan_id;

            $subscription->swap($plan->stripe_price_id);

            $tenant->update(['plan_id' => $plan->id]);

            BillingLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'action' => 'plan_swapped',
                'stripe_subscription_id' => $subscription->stripe_id,
                'metadata' => [
                    'previous_plan_id' => $previousPlanId,
                ],
            ]);

            return redirect()
                ->route('pricing.index')
                ->with('success', 'Plan upgraded successfully.');
        }

        // 🆕 NÃO TEM SUBSCRIÇÃO → CHECKOUT
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

        // ⚠️ LOG DA CRIAÇÃO DA SUBSCRIÇÃO
        // 👉 será feito no success() ou webhook

        return Inertia::location($checkout->url);
    }

    public function success(Request $request)
    {
        // Aqui podes criar o log de subscription_created
        // ou deixar isto para o webhook (mais profissional)

        return redirect()
            ->route('dashboard')
            ->with('success', 'Subscription created successfully.');
    }
}
