<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret');

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
            \Log::warning('Checkout completed without metadata', [
                'session_id' => $session->id,
            ]);
            return;
        }

        $tenant = \App\Models\Tenant::find($tenantId);
        $plan = \App\Models\Plan::find($planId);

        if (! $tenant || ! $plan) {
            \Log::warning('Tenant or Plan not found', compact('tenantId', 'planId'));
            return;
        }

        $tenant->update([
            'plan_id' => $plan->id,
            'stripe_subscription_id' => $session->subscription,
            'trial_ends_at' => null,
        ]);

        \Log::info('Tenant upgraded successfully', [
            'tenant_id' => $tenant->id,
            'plan' => $plan->slug,
        ]);
    }

}
