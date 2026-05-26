<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚀 Welcome to StudAI Hire — Your Career Journey Starts Now',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.student-welcome');
    }

    public function attachments(): array
    {
        return [];
    }
}
