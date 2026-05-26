<?php

declare(strict_types=1);

namespace App\Actions\Resume;

use App\Models\Job;
use App\Models\Resume;
use App\Models\User;
use App\Services\AI\ResumeAIService;
use Illuminate\Support\Facades\DB;

class CreateResumeAction
{
    public function __construct(
        private ResumeAIService $aiService
    ) {}

    public function execute(User $user, array $data): Resume
    {
        return DB::transaction(function () use ($user, $data) {
            $resume = $user->resumes()->create($data);

            // Generate AI summary if not provided
            if (empty($data['professional_summary'])) {
                $targetJob = isset($data['target_job_id']) ? Job::find($data['target_job_id']) : null;
                try {
                    $summary = $this->aiService->generateProfessionalSummary($resume, $targetJob);
                    $resume->update([
                        'professional_summary' => $summary,
                        'summary_is_ai_generated' => true,
                    ]);
                } catch (\Exception $e) {
                    // Non-fatal: resume created without AI summary, user can generate later
                    \Illuminate\Support\Facades\Log::warning('Resume AI summary skipped', ['error' => $e->getMessage()]);
                }
            }

            // Track creation (non-fatal — analytics table has no updated_at)
            try {
                \Illuminate\Support\Facades\DB::table('resume_analytics')->insert([
                    'resume_id'  => $resume->id,
                    'event_type' => 'created',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Analytics failure must never prevent resume creation
            }

            return $resume;
        });
    }
}
