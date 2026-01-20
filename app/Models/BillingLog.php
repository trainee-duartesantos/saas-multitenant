<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'plan_id',
        'action',
        'stripe_subscription_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
