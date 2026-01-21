<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\BillingLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    /**
     * 💳 Checkout / Upgrade
     * - Free → Paid  (checkout)
     * - Paid → Paid  (swap imediato)
     */
    public function checkout(Request $request, Plan $plan)
    {
        $tenant = $request->attributes->get('tenant');
        $user   = $request->user();

        abort_unless($user->isOwnerOfTenant($tenant->id), 403);
        abort_unless($plan->stripe_price_id, 403);

        if ($tenant->subscribed('default')) {
            $tenant->subscription('default')
                ->swap($plan->stripe_price_id);

            $tenant->update(['plan_id' => $plan->id]);

            return back()->with('success', 'Plan updated.');
        }

        $checkout = $tenant
            ->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url'  => route('pricing.index'),
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan_id'   => $plan->id,
                ],
            ]);

        return Inertia::location($checkout->url);

    }

    /**
     * ✅ Redirect após checkout
     * (estado real vem do webhook)
     */
    public function success()
    {
        return redirect()
            ->route('dashboard')
            ->with('success', 'Subscription created successfully.');
    }

    /**
     * 🔻 Downgrade (aplicado no próximo ciclo)
     */
    public function downgrade(Request $request, Plan $plan)
    {
        $tenant = $request->attributes->get('tenant');
        $user   = $request->user();

        abort_unless($user->isOwnerOfTenant($tenant->id), 403);

        $tenant->update([
            'pending_plan_id' => $plan->id,
        ]);

        return back()->with(
            'success',
            'Downgrade scheduled for next billing cycle.'
        );
    }

    /**
     * ❌ Cancelamento (no fim do ciclo)
     */
    public function cancel(Request $request)
    {
        $tenant = $request->attributes->get('tenant');
        $user   = $request->user();

        abort_unless($user->isOwnerOfTenant($tenant->id), 403);

        $subscription = $tenant->activeSubscription();
        abort_unless($subscription, 400);

        $subscription->cancel(); // grace period

        BillingLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'plan_id' => $tenant->plan_id,
            'action' => 'subscription_canceled',
            'stripe_subscription_id' => $subscription->stripe_id,
        ]);

        return back()->with(
            'success',
            'Subscription will be canceled at the end of the billing period.'
        );
    }
}
