<?php

namespace App\Mail;

use App\Models\Guard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Guard $guard,
        public string $plainToken,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to '.config('norix.name').' — set up staff access',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.staff-invite',
            with: [
                'guard' => $this->guard,
                'url' => route('staff.set-password', ['token' => $this->plainToken]),
            ],
        );
    }
}
