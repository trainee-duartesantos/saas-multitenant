<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\BillingLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

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

        /**
         * 🔁 Paid → Paid (upgrade imediato com pró-rata)
         */
        if ($tenant->subscribed('default')) {

            Stripe::setApiKey(config('services.stripe.secret'));

            $subscription = StripeSubscription::retrieve(
                $tenant->subscription('default')->stripe_id
            );

            StripeSubscription::update(
                $subscription->id,
                [
                    'items' => [[
                        'id' => $subscription->items->data[0]->id,
                        'price' => $plan->stripe_price_id,
                    ]],
                    'proration_behavior' => 'create_prorations',
                    'billing_cycle_anchor' => 'unchanged',
                ]
            );

            BillingLog::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'action' => 'plan_upgraded_prorated',
                'stripe_subscription_id' => $subscription->id,
            ]);

            return back()->with(
                'success',
                'Plano atualizado com cobrança pró-rata.'
            );
        }

        /**
         * 🆕 Free → Paid (checkout)
         */
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
            'Downgrade agendado para o próximo ciclo de faturação.'
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
