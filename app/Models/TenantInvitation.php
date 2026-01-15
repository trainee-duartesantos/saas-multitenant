<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantInvitation extends Model
{
    protected $fillable = [
        'tenant_id',
        'email',
        'role',
        'token',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isAccepted(): bool
    {
        return ! is_null($this->accepted_at);
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at');
    }

}
