<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\CompanyWelcomeMail;
use App\Mail\StudentWelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a role-specific welcome email when a user registers.
 * - Employers receive the Company Welcome email (with dashboard + plan details).
 * - Students/job seekers receive the Student Welcome email (with career toolkit).
 */
class SendWelcomeEmail implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        if ($user->account_type === 'employer' && $user->company) {
            Mail::to($user->email)->send(new CompanyWelcomeMail($user, $user->company));
        } else {
            Mail::to($user->email)->send(new StudentWelcomeMail($user));
        }
    }
}
