<?php

declare(strict_types=1);

namespace App\Services\Agent;

use App\Models\AgentConfiguration;
use App\Models\DiscoveredJob;
use App\Models\JobMatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Discovers external jobs (via RSS/API aggregation) for an agent configuration,
 * scores them against the user's preferences, and persists JobMatch records.
 *
 * Used by DiscoverJobsJob (scheduled) and AgentController::discover() (manual trigger).
 */
class JobDiscoveryService
{
    public function __construct(
        protected JobAggregationService $aggregator
    ) {}

    /**
     * Discover and score jobs for a single agent configuration.
     *
     * Returns a Collection of newly created JobMatch models
     * (for DiscoverJobsJob compatibility, which calls ->count() on the result).
     *
     * Also returns array stats for the API response from AgentController::discover().
     *
     * @return Collection<JobMatch>
     */
    public function discoverJobs(AgentConfiguration $config): Collection
    {
        $user      = $config->user;
        $threshold = $config->match_threshold_percentage ?? 60;

        // Build search params from agent config
        $searchParams = $this->buildSearchParams($config);

        // Aggregate fresh external jobs (RSS / Indeed Publisher / crawlers)
        try {
            $this->aggregator->aggregateFromAllSources($searchParams);
        } catch (\Throwable $e) {
            Log::warning('JobDiscoveryService: aggregation error (continuing with existing jobs)', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        // Exclude jobs already matched for this user
        $alreadyMatchedJobIds = JobMatch::where('user_id', $user->id)
            ->pluck('discovered_job_id');

        // Pull unprocessed discovered jobs
        $jobs = DiscoveredJob::where('is_processed', false)
            ->where('is_duplicate', false)
            ->whereNotIn('id', $alreadyMatchedJobIds)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->limit(500)
            ->get();

        $newMatches = collect();
        $profile    = $user->profile;

        foreach ($jobs as $job) {
            $result = $this->scoreJob($job, $config, $profile);

            // Mark as processed regardless of score
            $job->update(['is_processed' => true]);

            if ($result['score'] < $threshold) {
                continue;
            }

            $decision = $result['score'] >= ($config->approval_threshold ?? 85) && !$config->require_approval
                ? 'apply'
                : 'review';

            $match = JobMatch::create([
                'user_id'           => $user->id,
                'discovered_job_id' => $job->id,
                'overall_match_score' => $result['score'],
                'score_breakdown'   => $result['breakdown'],
                'matching_skills'   => $result['matching_skills'],
                'missing_skills'    => $result['missing_skills'],
                'agent_decision'    => $decision,
                'decision_reasoning'=> $result['reasoning'],
                'confidence_score'  => $result['confidence'],
                'has_applied'       => false,
            ]);

            $newMatches->push($match);
        }

        Log::info('JobDiscoveryService: discovery complete', [
            'user_id'         => $user->id,
            'jobs_checked'    => $jobs->count(),
            'matches_created' => $newMatches->count(),
            'threshold'       => $threshold,
        ]);

        return $newMatches;
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function buildSearchParams(AgentConfiguration $config): array
    {
        $keywords  = $config->target_roles ?? [];
        $locations = $config->preferred_locations ?? [];

        return [
            'keywords'  => implode(' ', array_slice($keywords, 0, 3)),
            'location'  => implode(', ', array_slice($locations, 0, 2)),
            'job_types' => $config->employment_types ?? [],
            'remote'    => in_array('remote', array_map('strtolower', $config->work_arrangements ?? []), true),
        ];
    }

    private function scoreJob(DiscoveredJob $job, AgentConfiguration $config, $profile): array
    {
        $breakdown      = [];
        $matchingSkills = [];
        $missingSkills  = [];
        $total          = 0;

        // 1. Role / keyword match (35 pts)
        $roleScore = $this->scoreRole($job, $config);
        $breakdown['role'] = $roleScore;
        $total += $roleScore;

        // 2. Skills match (30 pts)
        $skillResult = $this->scoreSkills($job, $config, $profile);
        $breakdown['skills'] = $skillResult['score'];
        $matchingSkills      = $skillResult['matching'];
        $missingSkills       = $skillResult['missing'];
        $total += $skillResult['score'];

        // 3. Salary match (15 pts)
        $salaryScore = $this->scoreSalary($job, $config);
        $breakdown['salary'] = $salaryScore;
        $total += $salaryScore;

        // 4. Location / remote match (12 pts)
        $locationScore = $this->scoreLocation($job, $config);
        $breakdown['location'] = $locationScore;
        $total += $locationScore;

        // 5. Excluded keyword penalty
        if ($this->hasExcludedKeywords($job, $config)) {
            $total = (int) round($total * 0.3);
            $breakdown['excluded_keyword_penalty'] = true;
        }

        $score      = min(100, max(0, (int) round($total)));
        $confidence = $this->calculateConfidence($job);

        return [
            'score'          => $score,
            'breakdown'      => $breakdown,
            'matching_skills'=> $matchingSkills,
            'missing_skills' => $missingSkills,
            'reasoning'      => $this->buildReasoning($breakdown, $score),
            'confidence'     => $confidence,
        ];
    }

    private function scoreRole(DiscoveredJob $job, AgentConfiguration $config): int
    {
        $targetRoles = array_map('strtolower', $config->target_roles ?? []);
        if (empty($targetRoles)) {
            return 20;
        }

        $jobTitle = strtolower($job->title ?? '');
        $jobDesc  = strtolower(strip_tags($job->description ?? ''));

        $matched = 0;
        foreach ($targetRoles as $role) {
            if (str_contains($jobTitle, $role) || str_contains($jobDesc, $role)) {
                $matched++;
            }
        }

        if ($matched === 0) {
            return 0;
        }

        return (int) round(($matched / count($targetRoles)) * 35);
    }

    private function scoreSkills(DiscoveredJob $job, AgentConfiguration $config, $profile): array
    {
        $agentRequired = array_map('strtolower', $config->required_skills ?? []);
        $profileSkills = array_map('strtolower', $profile?->skills ?? []);
        $jobSkills     = array_map('strtolower', $job->extracted_skills['required_skills'] ?? $job->extracted_skills ?? []);

        if (!is_array($jobSkills)) {
            $jobSkills = [];
        }

        $matching = [];
        $missing  = [];

        foreach ($jobSkills as $skill) {
            if (in_array($skill, $profileSkills, true)) {
                $matching[] = $skill;
            } else {
                $missing[] = $skill;
            }
        }

        // Also flag agent-required skills not in job
        $agentMissing = array_diff($agentRequired, $jobSkills);

        $profileRatio = empty($jobSkills) ? 0 : count($matching) / count($jobSkills);
        $agentRatio   = empty($agentRequired) ? 1 : (count($agentRequired) - count($agentMissing)) / max(1, count($agentRequired));

        $combined = empty($agentRequired) ? $profileRatio : ($profileRatio * 0.6 + $agentRatio * 0.4);

        return [
            'score'    => (int) round($combined * 30),
            'matching' => array_values(array_unique($matching)),
            'missing'  => array_values(array_unique(array_merge($missing, $agentMissing))),
        ];
    }

    private function scoreSalary(DiscoveredJob $job, AgentConfiguration $config): int
    {
        $agentMin = $config->min_salary;
        $agentMax = $config->max_salary;
        $jobMin   = $job->salary_min;
        $jobMax   = $job->salary_max;

        if (!$agentMin && !$agentMax) {
            return 10;
        }
        if (!$jobMin && !$jobMax) {
            return 8;
        }

        $agentMinVal = $agentMin ?? 0;
        $agentMaxVal = $agentMax ?? PHP_INT_MAX;
        $jobMinVal   = $jobMin ?? 0;
        $jobMaxVal   = $jobMax ?? PHP_INT_MAX;

        return ($agentMinVal <= $jobMaxVal && $agentMaxVal >= $jobMinVal) ? 15 : 3;
    }

    private function scoreLocation(DiscoveredJob $job, AgentConfiguration $config): int
    {
        $preferredLocations = array_map('strtolower', $config->preferred_locations ?? []);
        $workArrangements   = array_map('strtolower', $config->work_arrangements ?? []);
        $jobRemote          = $job->is_remote ?? false;
        $jobLocation        = strtolower($job->location ?? '');

        $score = 0;

        if (in_array('remote', $workArrangements, true) && $jobRemote) {
            $score += 6;
        } elseif (empty($workArrangements)) {
            $score += 4;
        }

        if (empty($preferredLocations)) {
            $score += 4;
        } elseif ($jobRemote) {
            $score += 6; // Remote matches any location preference
        } else {
            foreach ($preferredLocations as $pref) {
                if (str_contains($jobLocation, $pref) || str_contains($pref, $jobLocation)) {
                    $score += 6;
                    break;
                }
            }
        }

        return min(12, $score);
    }

    private function hasExcludedKeywords(DiscoveredJob $job, AgentConfiguration $config): bool
    {
        $excluded = array_map('strtolower', $config->excluded_keywords ?? []);
        if (empty($excluded)) {
            return false;
        }

        $haystack = strtolower($job->title . ' ' . strip_tags($job->description ?? ''));
        foreach ($excluded as $kw) {
            if (str_contains($haystack, $kw)) {
                return true;
            }
        }

        return false;
    }

    private function calculateConfidence(DiscoveredJob $job): float
    {
        $score = 0.5; // base confidence

        if ($job->title) {
            $score += 0.1;
        }
        if ($job->description && strlen($job->description) > 200) {
            $score += 0.1;
        }
        if (!empty($job->extracted_skills)) {
            $score += 0.15;
        }
        if ($job->salary_min || $job->salary_max) {
            $score += 0.1;
        }
        if ($job->company_name) {
            $score += 0.05;
        }

        return min(1.0, $score);
    }

    private function buildReasoning(array $breakdown, int $score): string
    {
        $parts = [];

        if (($breakdown['role'] ?? 0) >= 25) {
            $parts[] = 'strong role alignment';
        }
        if (($breakdown['skills'] ?? 0) >= 22) {
            $parts[] = 'excellent skills overlap';
        } elseif (($breakdown['skills'] ?? 0) >= 12) {
            $parts[] = 'good skills match';
        }
        if (($breakdown['salary'] ?? 0) >= 12) {
            $parts[] = 'salary aligns with expectations';
        }
        if (($breakdown['location'] ?? 0) >= 10) {
            $parts[] = 'location matches preferences';
        }
        if ($breakdown['excluded_keyword_penalty'] ?? false) {
            return 'Deprioritised: contains excluded keywords.';
        }

        if (empty($parts)) {
            return $score >= 60 ? 'Meets minimum match threshold.' : 'Below match threshold.';
        }

        return ucfirst(implode(', ', $parts)) . '.';
    }
}
