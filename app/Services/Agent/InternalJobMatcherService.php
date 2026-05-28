<?php

declare(strict_types=1);

namespace App\Services\Agent;

use App\Models\AgentConfiguration;
use App\Models\AgentInternalMatch;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Matches a user's agent configuration against internal platform job listings
 * and auto-generates AI cover letters for strong matches.
 */
class InternalJobMatcherService
{
    /**
     * Find and score internal jobs matching the user's agent configuration.
     * Stores results in agent_internal_matches. Skips already-matched jobs.
     *
     * @return int Number of new matches stored
     */
    public function scanAndStore(User $user, AgentConfiguration $config): int
    {
        if (!$config->is_active) {
            return 0;
        }

        $threshold = $config->match_threshold_percentage ?? 60;

        // Jobs already applied to or already matched (any status)
        $appliedJobIds = Application::where('user_id', $user->id)->pluck('job_id');
        $alreadyMatchedJobIds = AgentInternalMatch::where('user_id', $user->id)->pluck('job_id');
        $excludeJobIds = $appliedJobIds->merge($alreadyMatchedJobIds)->unique();

        // Build target role keywords for hard pre-filter
        $targetRoles = array_values(array_filter(
            array_map('trim', $config->target_roles ?? [])
        ));

        // Candidate pool: published, not expired, matching target roles
        $query = Job::where('status', 'published')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereNotIn('id', $excludeJobIds)
            ->with('company');

        // Hard-filter by target roles — only fetch jobs whose title contains
        // at least one configured role keyword (or any significant word in it)
        if (!empty($targetRoles)) {
            $query->where(function ($q) use ($targetRoles) {
                foreach ($targetRoles as $role) {
                    $q->orWhere('title', 'like', '%' . $role . '%');
                    // Also try individual words (>2 chars) for multi-word roles
                    foreach (explode(' ', $role) as $word) {
                        if (strlen(trim($word)) > 2) {
                            $q->orWhere('title', 'like', '%' . trim($word) . '%');
                        }
                    }
                }
            });
        }

        $jobs = $query->get();

        if ($jobs->isEmpty()) {
            return 0;
        }

        $profile   = $user->profile;
        $newStored = 0;

        foreach ($jobs as $job) {
            $result = $this->scoreJob($job, $config, $profile);

            if ($result['score'] < $threshold) {
                continue;
            }

            // Generate AI cover letter for good matches (≥70)
            $coverLetter = null;
            if ($result['score'] >= 70) {
                $coverLetter = $this->generateCoverLetter($user, $job, $profile);
            }

            AgentInternalMatch::create([
                'user_id'       => $user->id,
                'job_id'        => $job->id,
                'match_score'   => $result['score'],
                'score_breakdown' => $result['breakdown'],
                'ai_reasoning'  => $result['reasoning'],
                'cover_letter'  => $coverLetter,
                'status'        => $config->require_approval ? 'pending' : 'approved',
            ]);

            $newStored++;
        }

        Log::info('InternalJobMatcherService: scan complete', [
            'user_id'    => $user->id,
            'checked'    => $jobs->count(),
            'new_matches'=> $newStored,
            'threshold'  => $threshold,
        ]);

        return $newStored;
    }

    /**
     * Rescore all existing non-applied matches for a user with the latest scoring algorithm.
     * Removes matches that fall below threshold after rescoring.
     *
     * @return int Number of matches rescored
     */
    public function rescoreExisting(User $user, AgentConfiguration $config): int
    {
        $threshold   = $config->match_threshold_percentage ?? 60;
        $profile     = $user->profile;
        $targetRoles = array_values(array_filter(array_map('trim', $config->target_roles ?? [])));

        $matches = AgentInternalMatch::where('user_id', $user->id)
            ->whereNotIn('status', ['applied'])
            ->with('job.company')
            ->get();

        $rescored = 0;

        foreach ($matches as $match) {
            if (!$match->job) {
                $match->delete();
                continue;
            }

            // Drop matches whose job title no longer matches any target role
            if (!empty($targetRoles) && !$this->jobMatchesTargetRoles($match->job, $targetRoles)) {
                $match->delete();
                continue;
            }

            $result = $this->scoreJob($match->job, $config, $profile);

            if ($result['score'] < $threshold) {
                $match->delete();
                continue;
            }

            $match->update([
                'match_score'    => $result['score'],
                'score_breakdown' => $result['breakdown'],
                'ai_reasoning'   => $result['reasoning'],
            ]);

            $rescored++;
        }

        Log::info('InternalJobMatcherService: rescore complete', [
            'user_id'  => $user->id,
            'rescored' => $rescored,
        ]);

        return $rescored;
    }

    /**
     * Submit an agent-approved internal match as a real Application.
     */
    public function applyForMatch(AgentInternalMatch $match): Application
    {
        $user = $match->user;
        $job  = $match->job;

        // Generate cover letter now if not already present
        $coverLetter = $match->cover_letter
            ?? $this->generateCoverLetter($user, $job, $user->profile);

        $application = Application::create([
            'user_id'            => $user->id,
            'job_id'             => $job->id,
            'application_number' => 'APP-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'cover_letter'       => $coverLetter,
            'status'             => 'pending',
            'submitted_at'       => now(),
            'is_archived'        => false,
            'source'             => 'agent',
            'match_analysis'     => $match->score_breakdown,
        ]);

        $match->update([
            'status'         => 'applied',
            'application_id' => $application->id,
            'applied_at'     => now(),
        ]);

        Log::info('InternalJobMatcherService: auto-application submitted', [
            'user_id'        => $user->id,
            'job_id'         => $job->id,
            'application_id' => $application->id,
            'match_score'    => $match->match_score,
        ]);

        return $application;
    }

    // ── Private helpers ────────────────────────────────────────────────────

    /**
     * Score a job against agent configuration. Returns score (0-100) + breakdown.
     */
    private function scoreJob(Job $job, AgentConfiguration $config, $profile): array
    {
        $breakdown = [];
        $total     = 0;

        // 1. Role / keyword match (35 pts)
        $roleScore = $this->scoreRoleKeywords($job, $config);
        $breakdown['role'] = $roleScore;
        $total += $roleScore;

        // 2. Skills match (30 pts)
        $skillScore = $this->scoreSkills($job, $config, $profile);
        $breakdown['skills'] = $skillScore;
        $total += $skillScore;

        // 3. Salary range match (15 pts)
        $salaryScore = $this->scoreSalary($job, $config);
        $breakdown['salary'] = $salaryScore;
        $total += $salaryScore;

        // 4. Location / work arrangement (12 pts)
        $locationScore = $this->scoreLocation($job, $config);
        $breakdown['location'] = $locationScore;
        $total += $locationScore;

        // 5. Excluded keywords penalty
        if ($this->hasExcludedKeywords($job, $config)) {
            $total = (int) round($total * 0.3); // heavy penalty
            $breakdown['excluded_keyword_penalty'] = true;
        }

        $score = min(100, max(0, (int) round($total)));

        return [
            'score'     => $score,
            'breakdown' => $breakdown,
            'reasoning' => $this->buildReasoning($breakdown, $job),
        ];
    }

    private function scoreRoleKeywords(Job $job, AgentConfiguration $config): int
    {
        $targetRoles = array_map('strtolower', array_filter($config->target_roles ?? []));
        if (empty($targetRoles)) {
            return 18; // no filter = neutral partial credit
        }

        $jobTitle = strtolower($job->title ?? '');
        // Limit description to first 800 chars to avoid noise
        $jobDesc  = strtolower(substr(strip_tags($job->description ?? ''), 0, 800));

        $bestScore = 0;

        foreach ($targetRoles as $role) {
            $roleWords = array_values(array_filter(explode(' ', $role), fn($w) => strlen($w) > 2));

            // Exact phrase in job title = full credit
            if ($jobTitle !== '' && str_contains($jobTitle, $role)) {
                $bestScore = max($bestScore, 35);
                continue;
            }

            // Exact phrase in description = strong credit
            if ($jobDesc !== '' && str_contains($jobDesc, $role)) {
                $bestScore = max($bestScore, 26);
                continue;
            }

            // Word-level match against title (proportional)
            if (!empty($roleWords)) {
                $titleHits = array_filter($roleWords, fn($w) => str_contains($jobTitle, $w));
                $titleRatio = count($titleHits) / count($roleWords);

                if ($titleRatio >= 0.5) {
                    // Good title match — scale 17–32
                    $bestScore = max($bestScore, (int) round(17 + ($titleRatio * 15)));
                } else {
                    // Weaker match — check description words
                    $descHits  = array_filter($roleWords, fn($w) => str_contains($jobDesc, $w));
                    $descRatio = count($descHits) / count($roleWords);
                    if ($descRatio > 0) {
                        $bestScore = max($bestScore, (int) round($descRatio * 16));
                    }
                }
            }
        }

        return min(35, max(0, $bestScore));
    }

    private function scoreSkills(Job $job, AgentConfiguration $config, $profile): int
    {
        $toArray = static fn ($v) => is_array($v) ? $v : (is_string($v) ? (json_decode($v, true) ?? []) : []);

        $requiredSkills = array_values(array_map('strtolower', array_filter($toArray($config->required_skills))));
        $profileSkills  = array_values(array_map('strtolower', array_filter($toArray($profile?->skills))));
        $jobSkills      = array_values(array_map('strtolower', array_filter($toArray($job->required_skills))));

        // Full job text for fuzzy matching — CRITICAL: replaces relying only on structured field
        $jobText = strtolower(
            ($job->title ?? '') . ' ' .
            strip_tags($job->description ?? '') . ' ' .
            ($job->requirements ?? '')
        );

        // Combine candidate skills from profile + agent config (deduplicated)
        $candidateSkills = array_values(array_unique(array_merge($profileSkills, $requiredSkills)));

        if (empty($candidateSkills)) {
            // No skills data on candidate side — give neutral score
            return 12;
        }

        $matched = 0.0;
        $total   = 0;

        foreach ($candidateSkills as $skill) {
            if (strlen($skill) < 2) {
                continue;
            }
            $total++;

            // 1. Exact match in structured required_skills field (highest confidence)
            if (!empty($jobSkills) && in_array($skill, $jobSkills, true)) {
                $matched += 1.0;
                continue;
            }

            // 2. Exact substring match in full job text
            if (str_contains($jobText, $skill)) {
                $matched += 0.8;
                continue;
            }

            // 3. Partial word-level match for multi-word skills (e.g. "react native")
            if (strlen($skill) >= 4) {
                $words    = array_filter(explode(' ', $skill), fn($w) => strlen($w) >= 3);
                $wordHits = array_filter($words, fn($w) => str_contains($jobText, $w));
                if (!empty($words) && count($wordHits) >= (int) ceil(count($words) * 0.6)) {
                    $matched += 0.5;
                }
            }
        }

        $ratio = $total > 0 ? ($matched / $total) : 0.0;
        return (int) round($ratio * 30);
    }

    private function scoreSalary(Job $job, AgentConfiguration $config): int
    {
        $agentMin = $config->min_salary;
        $agentMax = $config->max_salary;
        $jobMin   = $job->salary_min;
        $jobMax   = $job->salary_max;

        if (!$agentMin && !$agentMax) {
            // No salary preference — neutral score, slightly differentiated by job range
            return ($jobMin || $jobMax) ? 9 : 7;
        }

        if (!$jobMin && !$jobMax) {
            return 7; // job doesn't specify salary = uncertain
        }

        $agentMinVal = (float) ($agentMin ?? 0);
        $agentMaxVal = (float) ($agentMax ?? PHP_INT_MAX);
        $jobMinVal   = (float) ($jobMin ?? 0);
        $jobMaxVal   = (float) ($jobMax ?? PHP_INT_MAX);

        // No overlap = very low score
        if ($agentMinVal > $jobMaxVal || $agentMaxVal < $jobMinVal) {
            return 2;
        }

        // Measure how well the ranges overlap
        $overlapMin   = max($agentMinVal, $jobMinVal);
        $overlapMax   = min($agentMaxVal, $jobMaxVal);
        $overlapRange = max(0.0, $overlapMax - $overlapMin);
        $agentRange   = max(1.0, $agentMaxVal - $agentMinVal);
        $jobRange     = max(1.0, $jobMaxVal - $jobMinVal);
        $overlapRatio = $overlapRange / min($agentRange, $jobRange);

        return (int) round(max(5, min(15, $overlapRatio * 15)));
    }

    private function scoreLocation(Job $job, AgentConfiguration $config): int
    {
        $preferredLocations = array_map('strtolower', array_filter($config->preferred_locations ?? []));
        $workArrangements   = array_map('strtolower', array_filter($config->work_arrangements ?? []));
        $jobLocationType    = strtolower($job->location_type ?? $job->work_mode ?? '');
        $jobLocation        = strtolower($job->location ?? '');

        $score = 0;

        // ── Work arrangement match (0–6 pts) ────────────────────────────────
        if (empty($workArrangements)) {
            $score += 3; // no preference = minimal neutral
        } elseif (in_array('remote', $workArrangements, true)) {
            if (str_contains($jobLocationType, 'remote'))      $score += 6;
            elseif (str_contains($jobLocationType, 'hybrid')) $score += 4;
            else                                               $score += 1;
        } elseif (in_array('hybrid', $workArrangements, true)) {
            if (str_contains($jobLocationType, 'hybrid'))      $score += 6;
            elseif (str_contains($jobLocationType, 'remote')) $score += 5;
            else                                               $score += 2;
        } else {
            // on-site or other preference
            if (!str_contains($jobLocationType, 'remote'))     $score += 6;
            elseif (str_contains($jobLocationType, 'hybrid')) $score += 3;
            else                                               $score += 1;
        }

        // ── Location match (0–6 pts) ────────────────────────────────────────
        if (empty($preferredLocations)) {
            $score += 3; // no preference = minimal neutral
        } else {
            $locationMatched = false;
            foreach ($preferredLocations as $pref) {
                if (str_contains($jobLocation, $pref) || str_contains($pref, $jobLocation)) {
                    $score += 6;
                    $locationMatched = true;
                    break;
                }
            }
            // Remote job: location preference matters less
            if (!$locationMatched && str_contains($jobLocationType, 'remote')) {
                $score += 3;
            }
        }

        return min(12, $score);
    }

    private function hasExcludedKeywords(Job $job, AgentConfiguration $config): bool
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

    private function buildReasoning(array $breakdown, Job $job): string
    {
        $parts = [];

        $role     = $breakdown['role']     ?? 0;
        $skills   = $breakdown['skills']   ?? 0;
        $salary   = $breakdown['salary']   ?? 0;
        $location = $breakdown['location'] ?? 0;

        // Role
        if ($role >= 32) {
            $parts[] = 'Exact role match with your target positions';
        } elseif ($role >= 22) {
            $parts[] = 'Good role alignment with your targets';
        } elseif ($role >= 12) {
            $parts[] = 'Partial role overlap with your targets';
        } else {
            $parts[] = 'Role differs from your primary targets';
        }

        // Skills
        if ($skills >= 24) {
            $parts[] = 'excellent skills overlap with the job requirements';
        } elseif ($skills >= 15) {
            $parts[] = 'solid skills match for this role';
        } elseif ($skills >= 7) {
            $parts[] = 'some relevant skills match';
        } else {
            $parts[] = 'limited skills overlap detected';
        }

        // Salary
        if ($salary >= 13) {
            $parts[] = 'salary fully within your range';
        } elseif ($salary >= 8) {
            $parts[] = 'salary roughly aligns with your expectations';
        } elseif ($salary <= 3) {
            $parts[] = 'salary may be outside your range';
        }

        // Location
        if ($location >= 10) {
            $parts[] = 'location and work arrangement match your preferences';
        } elseif ($location >= 6) {
            $parts[] = 'work arrangement partially matches your preferences';
        }

        return ucfirst(implode(', ', $parts)) . '.';
    }

    /**
     * Generate a personalised AI cover letter for the job.
     */
    private function generateCoverLetter(User $user, Job $job, $profile): ?string
    {
        try {
            $name        = $user->name ?? 'Applicant';
            $jobTitle    = $job->title ?? 'this role';
            $company     = $job->company?->name ?? 'your company';
            $skills      = implode(', ', array_slice($profile?->skills ?? [], 0, 10));
            $headline    = $profile?->headline ?? '';
            $experience  = collect($profile?->experience ?? [])->map(
                fn ($e) => ($e['title'] ?? '') . ' at ' . ($e['company'] ?? '')
            )->take(3)->implode('; ');

            $prompt = <<<PROMPT
Write a concise, professional cover letter for the following job application.

Candidate: {$name}
Headline: {$headline}
Key Skills: {$skills}
Recent Experience: {$experience}

Job Title: {$jobTitle}
Company: {$company}
Job Description (excerpt): {$this->excerpt($job->description, 600)}

Instructions:
- 3 short paragraphs, no more than 200 words total
- Tone: confident, professional, human (not robotic)
- Opening: express genuine interest in the role and company
- Middle: highlight the 2-3 most relevant skills/experiences
- Closing: call to action for next steps
- Do NOT include salutation lines like "Dear Hiring Manager" or signatures
- Return ONLY the body paragraphs
PROMPT;

            $content = app(\App\Services\AI\AIService::class)->callWithMessages(
                [['role' => 'user', 'content' => $prompt]],
                ['temperature' => 0.7, 'max_tokens' => 400, 'skip_cache' => true]
            );

            return trim($content);
        } catch (\Throwable $e) {
            Log::warning('InternalJobMatcherService: cover letter generation failed', [
                'job_id' => $job->id,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function excerpt(?string $text, int $length): string
    {
        if (!$text) {
            return '';
        }
        $plain = strip_tags($text);
        return mb_strlen($plain) > $length ? mb_substr($plain, 0, $length) . '…' : $plain;
    }

    /**
     * Check whether a job title matches any of the configured target roles.
     * Uses the same word-level logic as the DB pre-filter.
     *
     * @param  string[]  $targetRoles  Trimmed, un-lowercased role strings
     */
    private function jobMatchesTargetRoles(Job $job, array $targetRoles): bool
    {
        $jobTitle = strtolower($job->title ?? '');
        foreach ($targetRoles as $role) {
            if (str_contains($jobTitle, strtolower($role))) {
                return true;
            }
            foreach (explode(' ', $role) as $word) {
                if (strlen(trim($word)) > 2 && str_contains($jobTitle, strtolower(trim($word)))) {
                    return true;
                }
            }
        }
        return false;
    }
}
