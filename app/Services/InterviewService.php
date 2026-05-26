<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\Interview;
use App\Models\SilverMedalist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InterviewService
{
    /**
     * Schedule a new interview for an application.
     * Phase 2: Creates record, updates application status, sends scheduling mail.
     */
    public function schedule(Application $application, array $data): Interview
    {
        $interview = Interview::create([
            'application_id'   => $application->id,
            'interview_type'   => $data['interview_type'] ?? 'video',
            'scheduled_at'     => $data['scheduled_at'],
            'duration_minutes' => $data['duration_minutes'] ?? 60,
            'location'         => $data['location'] ?? null,
            'meeting_link'     => $data['meeting_link'] ?? $this->generateMeetingLink(),
            'notes'            => $data['notes'] ?? null,
            'round'            => $data['round'] ?? 1,
            'status'           => 'scheduled',
            'question_set'     => $this->buildQuestionSet($application, $data['interview_type'] ?? 'video'),
        ]);

        // Update application status
        $application->update([
            'status'            => Application::STATUS_INTERVIEW_SCHEDULED,
            'status_updated_at' => now(),
        ]);

        // Send scheduling notification to candidate
        $this->sendSchedulingEmail($interview);

        return $interview;
    }

    /**
     * Phase 4 — Evaluate: compute AI score summary, build feedback report.
     */
    public function evaluate(Interview $interview, array $panelScores): array
    {
        $questionSet = $interview->question_set ?? [];
        $scoresByQuestion = [];

        foreach ($panelScores as $questionKey => $scores) {
            $vals = array_filter(array_column($scores, 'score'), fn($s) => is_numeric($s));
            $scoresByQuestion[$questionKey] = [
                'avg'      => count($vals) ? round(array_sum($vals) / count($vals), 1) : null,
                'scores'   => $scores,
            ];
        }

        $allAvgs   = array_filter(array_column($scoresByQuestion, 'avg'));
        $overallAvg = count($allAvgs) ? round(array_sum($allAvgs) / count($allAvgs), 1) : null;

        $summary = [
            'overall_score'        => $overallAvg,
            'scores_by_question'   => $scoresByQuestion,
            'recommendation'       => $this->deriveRecommendation($overallAvg),
            'generated_at'         => now()->toISOString(),
        ];

        $interview->update([
            'ai_score_summary'   => $summary,
            'ai_recommendation'  => $summary['recommendation'],
            'rating'             => $overallAvg,
            'status'             => 'completed',
            'completed_at'       => now(),
        ]);

        $interview->application->update([
            'status'            => Application::STATUS_INTERVIEW_COMPLETED,
            'status_updated_at' => now(),
        ]);

        return $summary;
    }

    /**
     * Phase 5 — Decision: hire.
     */
    public function decide(Interview $interview, string $decision, array $data = []): void
    {
        $application = $interview->application->load(['user', 'job.company']);

        match ($decision) {
            'hire'       => $this->processHire($interview, $application, $data),
            'reject'     => $this->processReject($interview, $application, $data),
            'next_round' => $this->processNextRound($interview, $application, $data),
            default      => throw new \InvalidArgumentException("Unknown decision: {$decision}"),
        };
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    private function processHire(Interview $interview, Application $application, array $data): void
    {
        $application->update([
            'status'            => 'hired',
            'status_updated_at' => now(),
        ]);

        // Notify candidate
        try {
            if ($application->user) {
                $application->user->notifyNow(new \App\Notifications\CandidateHiredNotification($application));
            }
        } catch (\Throwable $e) {
            Log::warning('Hire notification failed', ['app_id' => $application->id, 'error' => $e->getMessage()]);
        }

        // Fire hiring emails (candidate congratulations + company summary)
        try {
            \App\Jobs\SendHiringEmailsJob::dispatchSync($application, 'hired');
        } catch (\Throwable $e) {
            Log::warning('Hire email failed', ['app_id' => $application->id, 'error' => $e->getMessage()]);
        }
    }

    private function processReject(Interview $interview, Application $application, array $data): void
    {
        $reason = $data['reason'] ?? 'Thank you for interviewing. We have decided to proceed with another candidate.';

        $application->update([
            'status'            => 'rejected',
            'rejection_reason'  => $reason,
            'status_updated_at' => now(),
        ]);

        // Tag as silver medalist if score was good
        $score = $interview->rating ?? 0;
        if ($score >= 3.5) {
            $this->tagSilverMedalist($interview, $application, $score, $reason);
        }

        // Notify candidate with AI reason mail
        try {
            if ($application->user) {
                $application->user->notifyNow(new \App\Notifications\CandidateRejectedNotification($application));
            }
        } catch (\Throwable $e) {
            Log::warning('Reject notification failed', ['app_id' => $application->id, 'error' => $e->getMessage()]);
        }

        try {
            \App\Jobs\SendHiringEmailsJob::dispatchSync($application, 'rejected');
        } catch (\Throwable $e) {
            Log::warning('Reject email failed', ['app_id' => $application->id, 'error' => $e->getMessage()]);
        }
    }

    private function processNextRound(Interview $interview, Application $application, array $data): void
    {
        $nextRound = ($interview->round ?? 1) + 1;

        // Schedule next round interview
        $nextInterview = Interview::create([
            'application_id'   => $application->id,
            'interview_type'   => $data['interview_type'] ?? $interview->interview_type,
            'scheduled_at'     => $data['scheduled_at'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? $interview->duration_minutes,
            'meeting_link'     => $data['meeting_link'] ?? $this->generateMeetingLink(),
            'notes'            => $data['notes'] ?? null,
            'round'            => $nextRound,
            'status'           => $data['scheduled_at'] ? 'scheduled' : 'pending',
            'question_set'     => $this->buildQuestionSet($application, $data['interview_type'] ?? $interview->interview_type),
        ]);

        // Keep application in interview_scheduled
        $application->update([
            'status'            => Application::STATUS_INTERVIEW_SCHEDULED,
            'status_updated_at' => now(),
        ]);

        if ($data['scheduled_at'] ?? null) {
            $this->sendSchedulingEmail($nextInterview);
        }
    }

    private function tagSilverMedalist(Interview $interview, Application $application, float $score, string $reason): void
    {
        try {
            SilverMedalist::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'company_id'            => $application->job->company_id ?? null,
                    'user_id'               => $application->user_id,
                    'job_id'                => $application->job_id,
                    'silver_medal_reason'   => $reason,
                    'interview_score'       => $score,
                    'silver_medal_date'     => now()->toDateString(),
                    'added_to_talent_pipeline' => true,
                    're_engagement_status'  => 'pending',
                    'strengths'             => $interview->ai_score_summary['strengths'] ?? [],
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Silver medalist tagging failed', ['app_id' => $application->id, 'error' => $e->getMessage()]);
        }
    }

    private function generateMeetingLink(): string
    {
        $id = strtoupper(substr(md5(uniqid()), 0, 10));
        return url("/interview-room/{$id}");
    }

    private function buildQuestionSet(Application $application, string $type): array
    {
        $role    = $application->job->title ?? 'the role';
        $defaults = [
            'behavioral' => [
                ['key' => 'q1', 'text' => "Tell me about yourself and why you're applying for {$role}."],
                ['key' => 'q2', 'text' => 'Describe a challenging situation you faced and how you handled it.'],
                ['key' => 'q3', 'text' => 'What is your greatest professional achievement?'],
                ['key' => 'q4', 'text' => 'Where do you see yourself in 5 years?'],
                ['key' => 'q5', 'text' => 'Do you have any questions for us?'],
            ],
            'technical' => [
                ['key' => 'q1', 'text' => "Walk me through your technical background relevant to {$role}."],
                ['key' => 'q2', 'text' => 'Describe a complex technical problem you solved recently.'],
                ['key' => 'q3', 'text' => 'How do you approach system design / architecture decisions?'],
                ['key' => 'q4', 'text' => 'What tools or technologies are you most proficient in?'],
                ['key' => 'q5', 'text' => 'Do you have any questions for the engineering team?'],
            ],
        ];

        return $defaults[$type] ?? $defaults['behavioral'];
    }

    private function sendSchedulingEmail(Interview $interview): void
    {
        try {
            $application = $interview->application->load(['user', 'job.company']);
            if ($application->user?->email) {
                // Use existing notification system
                $application->user->notifyNow(
                    new \App\Notifications\CandidateShortlistedNotification($application, 0)
                );
            }
        } catch (\Throwable $e) {
            Log::info('Interview scheduling email skipped', ['error' => $e->getMessage()]);
        }
    }

    private function deriveRecommendation(mixed $score): string
    {
        if ($score === null) return 'pending';
        if ($score >= 4.0) return 'hire';
        if ($score >= 3.0) return 'next_round';
        if ($score >= 2.5) return 'silver_medal';
        return 'reject';
    }
}
