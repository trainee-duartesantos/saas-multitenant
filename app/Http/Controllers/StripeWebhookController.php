<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\BillingLog;
use App\Models\Tenant;
use App\Models\Plan;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $secret
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event),
            'invoice.payment_succeeded' => $this->handleInvoicePaid($event),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
            default => null,
        };

        return response('Webhook handled', 200);
    }

    /**
     * 🆕 Free → Paid (checkout)
     */
    protected function handleCheckoutCompleted($event)
    {
        $session = $event->data->object;

        $tenantId = $session->metadata->tenant_id ?? null;
        $planId   = $session->metadata->plan_id ?? null;

        if (! $tenantId || ! $planId) return;

        $tenant = Tenant::find($tenantId);
        $plan   = Plan::find($planId);

        if (! $tenant || ! $plan) return;

        $tenant->update([
            'plan_id' => $plan->id,
            'trial_ends_at' => null,
        ]);

        BillingLog::create([
            'tenant_id' => $tenant->id,
            'plan_id'   => $plan->id,
            'action'    => 'subscription_created',
            'stripe_subscription_id' => $session->subscription,
            'metadata' => [
                'checkout_session_id' => $session->id,
            ],
        ]);
    }

    /**
     * 🔻 Apply scheduled downgrade at renewal
     */
    protected function handleInvoicePaid($event)
    {
        $subscriptionId = $event->data->object->subscription;

        $tenant = Tenant::whereHas('subscriptions', fn ($q) =>
            $q->where('stripe_id', $subscriptionId)
        )->first();

        if (! $tenant || ! $tenant->pending_plan_id) {
            return;
        }

        $tenant->update([
            'plan_id' => $tenant->pending_plan_id,
            'pending_plan_id' => null,
        ]);

        BillingLog::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $tenant->plan_id,
            'action' => 'plan_downgraded',
            'stripe_subscription_id' => $subscriptionId,
        ]);
    }

    /**
     * ❌ Subscription ended (cancel)
     */
    protected function handleSubscriptionDeleted($event)
    {
        $subscriptionId = $event->data->object->id;

        $tenant = Tenant::whereHas('subscriptions', fn ($q) =>
            $q->where('stripe_id', $subscriptionId)
        )->first();

        if (! $tenant) {
            return;
        }

        $freePlan = Plan::where('slug', 'free')->first();

        $tenant->update([
            'plan_id' => $freePlan->id,
            'pending_plan_id' => null,
        ]);

        BillingLog::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'action' => 'subscription_ended',
            'stripe_subscription_id' => $subscriptionId,
        ]);
    }
}
