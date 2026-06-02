<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\HireOutcomeRecorded;
use App\Jobs\AnalyzeSkillGapsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * C4: Candidate-side learning signal.
 *
 * When a terminal hire decision is recorded, a rejection is a strong signal
 * that the candidate has actionable skill gaps. We re-run skill-gap analysis
 * so their learning recommendations reflect the most recent outcome. This is
 * decoupled from the decision flow via the HireOutcomeRecorded event.
 */
class RecordCandidateLearningSignal implements ShouldQueue
{
    public string $queue = 'skill-analysis';

    /**
     * Handle the event.
     */
    public function handle(HireOutcomeRecorded $event): void
    {
        $outcome = $event->outcome;

        // Only rejections feed the learning signal; a hire needs no remediation.
        if ($outcome->outcome !== 'rejected') {
            return;
        }

        $user = $outcome->user;

        if ($user === null) {
            Log::debug('RecordCandidateLearningSignal: outcome has no associated user', [
                'hire_outcome_id' => $outcome->id,
            ]);

            return;
        }

        AnalyzeSkillGapsJob::dispatch($user)->onQueue('skill-analysis');

        Log::info('Candidate learning signal queued after rejection', [
            'hire_outcome_id' => $outcome->id,
            'user_id' => $user->id,
            'job_id' => $outcome->job_id,
        ]);
    }
}
