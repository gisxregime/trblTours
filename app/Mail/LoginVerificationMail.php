<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $verificationUrl,
        public string $email,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your TribalTours login',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login-verification',
            with: [
                'verificationUrl' => $this->verificationUrl,
                'email' => $this->email,
            ],
        );
    }
}
