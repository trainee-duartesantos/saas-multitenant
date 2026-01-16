<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Gerar UUID automaticamente ao criar tenant
     */
    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if (! $tenant->uuid) {
                $tenant->uuid = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(TenantInvitation::class);
    }

    public function onboarding()
    {
        return $this->hasOne(TenantOnboarding::class);
    }
}
