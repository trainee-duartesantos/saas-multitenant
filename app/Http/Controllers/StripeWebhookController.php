<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\BillingLog;

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
            default => null,
        };

        return response('Webhook handled', 200);
    }

    protected function handleCheckoutCompleted($event)
    {
        $session = $event->data->object;

        $tenantId = $session->metadata->tenant_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        if (! $tenantId || ! $planId) {
            return;
        }

        $tenant = \App\Models\Tenant::find($tenantId);
        $plan = \App\Models\Plan::find($planId);

        if (! $tenant || ! $plan) {
            return;
        }

        $tenant->update([
            'plan_id' => $plan->id,
            'trial_ends_at' => null,
        ]);

        BillingLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => null, // webhook
            'plan_id' => $plan->id,
            'action' => 'subscription_created',
            'stripe_subscription_id' => $session->subscription,
            'metadata' => [
                'checkout_session_id' => $session->id,
                'payment_status' => $session->payment_status,
            ],
        ]);
    }
}
