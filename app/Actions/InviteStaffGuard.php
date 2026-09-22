<?php

namespace App\Actions;

use App\Mail\StaffInviteMail;
use App\Models\Guard;
use Illuminate\Support\Facades\Mail;

class InviteStaffGuard
{
    public function __invoke(Guard $guard): void
    {
        $token = $guard->issueInviteToken();

        Mail::to($guard->email)->send(new StaffInviteMail($guard, $token));
    }
}
