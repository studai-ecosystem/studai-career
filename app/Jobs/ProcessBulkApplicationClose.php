<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\Job;
use App\Models\BulkEmailLog;
use App\Notifications\ApplicationStatusChangedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Triggered when a job's close_date passes.
 * Closes the application phase and notifies all applicants that evaluation begins soon.
 */
class ProcessBulkApplicationClose implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;

    public function __construct(public readonly int $jobId)
    {
    }

    public function handle(): void
    {
        $job = Job::find($this->jobId);
        if (! $job) {
            Log::warning('ProcessBulkApplicationClose: Job not found', ['job_id' => $this->jobId]);
            return;
        }

        // Mark job phase as closed
        $job->update(['application_phase' => 'closed']);

        // Get all applications that haven't been notified yet
        $applications = Application::where('job_id', $job->id)
            ->where('application_email_sent', true) // Only those who applied
            ->where('evaluation_invite_sent', false)
            ->get();

        if ($applications->isEmpty()) {
            Log::info('ProcessBulkApplicationClose: No applicants to notify', ['job_id' => $job->id]);
            return;
        }

        // Create bulk email log
        $log = BulkEmailLog::create([
            'job_id'           => $job->id,
            'email_type'       => 'evaluation_open',
            'total_recipients' => $applications->count(),
            'status'           => 'processing',
            'started_at'       => now(),
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($applications as $application) {
            try {
                // Dispatch individual evaluation invite notification
                SendEvaluationOpenNotification::dispatch($application->id, $job->id)
                    ->onQueue('notifications');
                $sent++;
            } catch (\Exception $e) {
                Log::error('ProcessBulkApplicationClose: Failed to queue notification', [
                    'application_id' => $application->id,
                    'error'          => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $log->update([
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'status'       => $failed === $applications->count() ? 'failed' : 'complete',
            'completed_at' => now(),
        ]);

        Log::info('ProcessBulkApplicationClose: Complete', [
            'job_id' => $job->id,
            'sent'   => $sent,
            'failed' => $failed,
        ]);
    }
}
