<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\TenantInvitation;

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

    public function pendingInvitations()
    {
        return $this->invitations()->whereNull('accepted_at');
    }

    /**
     * Pode convidar novo membro?
     * Conta membros atuais + convites pendentes.
     */
    public function canInviteMember(): bool
    {
        if (! $this->plan || $this->plan->max_members === null) {
            return true;
        }

        $used = $this->users()->count() + $this->pendingInvitations()->count();

        return $used < $this->plan->max_members;
    }

    /**
     * Pode aceitar um convite específico?
     * IMPORTANTE: ao aceitar, o convite deixa de ser pendente, então:
     * contamos convites pendentes EXCLUINDO este convite.
     */
    public function canAcceptInvitation(TenantInvitation $invitation): bool
    {
        if (! $this->plan || $this->plan->max_members === null) {
            return true;
        }

        $pendingExceptThis = $this->pendingInvitations()
            ->where('id', '!=', $invitation->id)
            ->count();

        $usedAfterAccept = $this->users()->count() + $pendingExceptThis;

        return $usedAfterAccept < $this->plan->max_members;
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function canCreateProject(): bool
    {
        // sem plano ou plano ilimitado
        if (! $this->plan || $this->plan->max_projects === null) {
            return true;
        }

        return $this->projects()->count() < $this->plan->max_projects;
    }
}
