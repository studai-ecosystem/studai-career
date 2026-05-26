<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendHiringEmailsJob;
use App\Models\Application;
use Illuminate\Console\Command;

class BackfillStatusEmails extends Command
{
    protected $signature = 'emails:backfill-status
                            {--status=* : Limit to specific statuses (shortlisted,interviewed,hired,rejected). Defaults to all.}
                            {--dry-run  : Preview what would be sent without actually dispatching}
                            {--limit=0  : Max applications to process (0 = all)}';

    protected $description = 'Send AI status emails to existing users whose applications already have a notable status (hired, rejected, shortlisted, interviewed).';

    private const VALID_STATUSES = ['shortlisted', 'interviewed', 'hired', 'rejected'];

    public function handle(): int
    {
        $targetStatuses = array_filter((array) $this->option('status'));
        if (empty($targetStatuses)) {
            $targetStatuses = self::VALID_STATUSES;
        }

        // Validate supplied statuses
        $invalid = array_diff($targetStatuses, self::VALID_STATUSES);
        if (!empty($invalid)) {
            $this->error('Invalid statuses: ' . implode(', ', $invalid));
            $this->line('Valid options: ' . implode(', ', self::VALID_STATUSES));
            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $limit    = (int) $this->option('limit');

        $this->info('StudAI Hire — Backfill Status Emails');
        $this->info('Statuses: ' . implode(', ', $targetStatuses));
        $this->info($isDryRun ? '⚠  DRY RUN — no emails will be sent' : '✉  LIVE — emails will be dispatched');
        $this->newLine();

        // Count first
        $query = Application::with(['user', 'job.company'])
            ->whereIn('status', $targetStatuses)
            ->whereNotNull('user_id')
            ->whereHas('user');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->warn('No applications found with those statuses.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} application(s) to process.");

        if (!$isDryRun && !$this->confirm("Send emails to all {$total} users?", true)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        $bar       = $this->output->createProgressBar($total);
        $sent      = 0;
        $skipped   = 0;
        $failed    = 0;

        $query->each(function (Application $application) use (
            $isDryRun,
            $bar,
            &$sent,
            &$skipped,
            &$failed
        ): void {
            $bar->advance();

            $user  = $application->user;
            $email = $user?->email;

            if (!$email) {
                $skipped++;
                return;
            }

            $status    = $application->status;
            $matchScore = (float) ($application->final_rank_score ?? $application->match_score ?? 0);

            if ($isDryRun) {
                $sent++;
                return;
            }

            try {
                SendHiringEmailsJob::dispatch($application, $status, $matchScore)
                    ->onQueue('notifications');
                $sent++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Failed app #{$application->id}: " . $e->getMessage());
                $failed++;
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                [$isDryRun ? 'Would send' : 'Dispatched', $sent],
                ['Skipped (no email)', $skipped],
                ['Failed', $failed],
            ]
        );

        if (!$isDryRun && $sent > 0) {
            $this->info('✅ Done! Run your queue worker to deliver the emails:');
            $this->line('   php artisan queue:work --queue=notifications,default');
        }

        return self::SUCCESS;
    }
}
