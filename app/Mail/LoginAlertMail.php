<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $loginTime;
    public string $ipAddress;
    public string $device;

    public function __construct(User $user, string $ipAddress, string $device, string $loginTime)
    {
        $this->userName  = $user->name ?? 'there';
        $this->ipAddress = $ipAddress;
        $this->device    = $device;
        $this->loginTime = $loginTime;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 New sign-in to your StudAI Hire account',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.login-alert');
    }

    public function attachments(): array
    {
        return [];
    }
}
