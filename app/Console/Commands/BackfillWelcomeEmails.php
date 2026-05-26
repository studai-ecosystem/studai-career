<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\CompanyWelcomeMail;
use App\Mail\StudentWelcomeMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class BackfillWelcomeEmails extends Command
{
    protected $signature = 'emails:backfill-welcome
                            {--dry-run : Preview without sending}';

    protected $description = 'Send welcome emails to all existing users who never received one.';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('StudAI Hire — Backfill Welcome Emails');
        $this->info($isDryRun ? '⚠  DRY RUN — no emails will be sent' : '✉  LIVE — sending now');
        $this->newLine();

        $users = User::with('company')->get();
        $total = $users->count();

        $this->info("Found {$total} user(s).");

        if ($total === 0) {
            $this->warn('No users found.');
            return self::SUCCESS;
        }

        if (!$isDryRun && !$this->confirm("Send welcome emails to all {$total} users?", true)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        $bar     = $this->output->createProgressBar($total);
        $sent    = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($users as $user) {
            $bar->advance();

            if (!$user->email) {
                $skipped++;
                continue;
            }

            if ($isDryRun) {
                $type = $user->account_type === 'employer' ? 'Company' : 'Student';
                $this->newLine();
                $this->line("  [{$type}] {$user->name} <{$user->email}>");
                $sent++;
                continue;
            }

            try {
                if ($user->account_type === 'employer' && $user->company) {
                    Mail::to($user->email)->send(new CompanyWelcomeMail($user, $user->company));
                } else {
                    Mail::to($user->email)->send(new StudentWelcomeMail($user));
                }
                $sent++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("  Failed {$user->email}: " . $e->getMessage());
                $failed++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                [$isDryRun ? 'Would send' : 'Sent', $sent],
                ['Skipped (no email)', $skipped],
                ['Failed', $failed],
            ]
        );

        return self::SUCCESS;
    }
}
