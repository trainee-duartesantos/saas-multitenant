<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Mail\TrialEndingSoonMail;
use Illuminate\Support\Facades\Mail;

class NotifyTrialEnding extends Command
{
    protected $signature = 'trial:notify-ending';

    protected $description = 'Notify tenants whose trial is ending soon';

    public function handle()
    {
        Tenant::whereNotNull('trial_ends_at')
            ->whereDate('trial_ends_at', now()->addDays(3)->toDateString())
            ->get()
            ->each(function (Tenant $tenant) {
                $owner = $tenant->users()
                    ->wherePivot('role', 'owner')
                    ->first();

                if ($owner) {
                    Mail::to($owner->email)
                        ->send(new TrialEndingSoonMail($tenant));
                }
            });
    }
}
