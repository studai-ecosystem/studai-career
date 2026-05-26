<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\JobApplied;
use App\Notifications\NotifyEmployer;
use App\Notifications\SendApplicationConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class HandleJobApplied implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'default';

    public function handle(JobApplied $event): void
    {
        $application = $event->application;
        $user        = $event->user;
        $job         = $event->job;

        // ── Notify applicant (confirmation email with Reply-To = hr_email) ──
        $user->notify(new SendApplicationConfirmation($application));

        // ── Notify HR about new application ───────────────────────────────
        // If company has a dedicated HR email, notify it directly.
        // Otherwise fall back to the company owner's account.
        $company  = $job->company;
        $hrEmail  = $company?->hr_email ?? null;
        $employer = $company?->owner ?? null;

        if ($hrEmail) {
            // Send a plain notification email to the HR inbox
            Mail::to($hrEmail)->send(
                new \App\Mail\NewApplicationMail(
                    application: $application,
                    hrName:      $company->name . ' HR',
                )
            );
        } elseif ($employer) {
            $employer->notify(new NotifyEmployer($application));
        }
    }

    public function shouldQueue(JobApplied $event): bool
    {
        return true;
    }
}
