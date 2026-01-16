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
        'plan_id',
        'trial_ends_at',
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

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null
            && now()->lt($this->trial_ends_at);
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) $this->plan?->{$feature};
    }

    public function canAddMember(): bool
    {
        if (! $this->plan || $this->plan->max_members === null) {
            return true;
        }

        return $this->users()->count() < $this->plan->max_members;
    }
}
