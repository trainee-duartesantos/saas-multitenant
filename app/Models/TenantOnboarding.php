<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantOnboarding extends Model
{
    protected $fillable = [
        'tenant_id',
        'current_step',
        'completed',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
