<?php

namespace App\Http\Controllers;

use App\Models\BillingLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingHistoryController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $logs = BillingLog::query()
            ->where('tenant_id', $tenant->id)
            ->with(['plan:id,name,slug', 'user:id,name,email'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'action' => $log->action,
                'plan' => $log->plan ? [
                    'name' => $log->plan->name,
                    'slug' => $log->plan->slug,
                ] : null,
                'actor' => $log->user ? [
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                ] : null,
                'stripe_subscription_id' => $log->stripe_subscription_id,
                'metadata' => $log->metadata,
                'created_at' => $log->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Billing/History', [
            'logs' => $logs,
        ]);
    }
}
