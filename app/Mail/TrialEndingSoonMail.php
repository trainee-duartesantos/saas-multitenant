<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Mail\Mailable;

class TrialEndingSoonMail extends Mailable
{
    public function __construct(
        public Tenant $tenant
    ) {}

    public function build()
    {
        return $this
            ->subject('O seu trial está a terminar')
            ->view('emails.trial-ending-soon');
    }
}
