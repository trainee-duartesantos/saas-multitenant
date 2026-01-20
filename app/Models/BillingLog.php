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

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

}
