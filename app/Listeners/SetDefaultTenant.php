<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class SetDefaultTenant
{
    public function handle(Login $event): void
    {
        logger('LOGIN EVENT DISPARADO');
        /** @var User $user */
        $user = $event->user;

        $tenant = $user->tenants()->first();

        if ($tenant) {
            session(['tenant_id' => $tenant->id]);
        }
    }
}
