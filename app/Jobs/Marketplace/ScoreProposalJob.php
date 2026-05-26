<?php

declare(strict_types=1);

namespace App\Jobs\Marketplace;

use App\Models\MarketplaceProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScoreProposalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        private readonly MarketplaceProposal $proposal
    ) {}

    public function handle(): void
    {
        $proposal = $this->proposal->fresh(['project']);
        if (!$proposal || !$proposal->project) {
            return;
        }

        $project          = $proposal->project;
        $requiredSkills   = (array) ($project->skills_required ?? []);
        $coverLetter      = $proposal->cover_letter ?? '';
        $freelancerProfile = $proposal->freelancer?->freelancerProfile;
        $freelancerSkills = $freelancerProfile ? (array) ($freelancerProfile->skills ?? []) : [];

        if (empty($requiredSkills)) {
            $proposal->update(['ai_match_score' => 70, 'ai_match_breakdown' => ['reasoning' => 'No skills specified for project.', 'matched_skills' => [], 'missing_skills' => []]]);
            return;
        }

        $requiredList   = implode(', ', $requiredSkills);
        $freelancerList = implode(', ', $freelancerSkills) ?: 'Not specified';
        $coverExcerpt   = mb_substr($coverLetter, 0, 600);
        $projectTitle   = $project->title ?? 'Untitled Project';

        $prompt = <<<PROMPT
You are an expert technical recruiter scoring a freelancer's proposal for a project.

Project: {$projectTitle}
Skills required: {$requiredList}

Freelancer's listed skills: {$freelancerList}
Freelancer's cover letter excerpt: "{$coverExcerpt}"

Score this proposal from 0 to 100 based on skill alignment. Also list which required skills are matched and which are missing.

Respond ONLY with valid JSON in this exact format (no markdown, no extra text):
{
  "score": 85,
  "matched_skills": ["React Native", "Laravel"],
  "missing_skills": ["GraphQL"],
  "reasoning": "One short sentence explaining the score."
}
PROMPT;

        try {
            $endpoint   = config('ai.azure.endpoint');
            $deployment = config('ai.azure.deployment_id', 'gpt-5.4');
            $apiVersion = config('ai.azure.api_version', '2024-12-01-preview');
            $apiKey     = config('ai.azure.api_key');

            $url = rtrim((string) $endpoint, '/') . "/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

            $response = Http::withHeaders(['api-key' => $apiKey])
                ->timeout(45)
                ->post($url, [
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a technical recruiter. Always respond with valid JSON only.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_completion_tokens' => 200,
                    'temperature'           => 0.2,
                ]);

            if ($response->failed()) {
                Log::warning('ScoreProposalJob: API failed', ['status' => $response->status(), 'proposal_id' => $proposal->id]);
                $proposal->update(['ai_match_score' => 50, 'ai_match_breakdown' => ['reasoning' => 'AI scoring unavailable.', 'matched_skills' => [], 'missing_skills' => []]]);
                return;
            }

            $raw     = trim($response->json('choices.0.message.content') ?? '');
            $raw     = preg_replace('/^```json\s*/i', '', $raw);
            $raw     = preg_replace('/\s*```$/', '', $raw);
            $decoded = json_decode($raw, true);

            if (!is_array($decoded) || !isset($decoded['score'])) {
                throw new \RuntimeException('Invalid JSON from AI: ' . $raw);
            }

            $score     = max(0, min(100, (int) $decoded['score']));
            $breakdown = [
                'matched_skills' => $decoded['matched_skills'] ?? [],
                'missing_skills' => $decoded['missing_skills'] ?? [],
                'reasoning'      => $decoded['reasoning'] ?? '',
            ];

            $proposal->update([
                'ai_match_score'     => $score,
                'ai_match_breakdown' => $breakdown,
            ]);

            Log::info('ScoreProposalJob: scored', ['proposal_id' => $proposal->id, 'score' => $score]);

        } catch (\Throwable $e) {
            Log::error('ScoreProposalJob: failed', ['proposal_id' => $proposal->id, 'error' => $e->getMessage()]);
            $proposal->update(['ai_match_score' => 50, 'ai_match_breakdown' => ['reasoning' => 'Scoring error: ' . $e->getMessage(), 'matched_skills' => [], 'missing_skills' => []]]);
        }
    }
}
