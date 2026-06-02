<?php

declare(strict_types=1);

namespace App\Services\AI\Scout;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * CandidateCompositeScoreService
 *
 * The unification bridge between the candidate-side AI pipeline and the
 * employer S.C.O.U.T. ranking pipeline. It derives the three composite
 * inputs that ScoreAndRankCandidates requires — skill_match_score,
 * resume_quality_score and behavioural_fit_score — from already-computed,
 * trusted candidate-side signals:
 *
 *   - skill_match_score     ← Orin-verified UserSkills vs Job::required_skills
 *   - resume_quality_score  ← latest Resume ATS score
 *   - behavioural_fit_score ← Vantage composite score (User::vantage_score)
 *
 * All computations are cheap DB reads (no inline AI / embeddings) so this can
 * run synchronously at evaluation finalisation. Every signal degrades to a
 * documented neutral baseline (NEUTRAL_BASELINE) rather than null or 0, so the
 * ranking guard never blocks and fairness is never skewed by a silent zero.
 */
class CandidateCompositeScoreService
{
    /**
     * Neutral baseline (0-100) used when a signal is genuinely unavailable.
     * A mid-point keeps an under-instrumented candidate neither rewarded nor
     * penalised relative to fully-instrumented peers.
     */
    private const NEUTRAL_BASELINE = 50.0;

    /**
     * Resolve all three composite scores for an application.
     *
     * @return array{skill_match_score: float, resume_quality_score: float, behavioural_fit_score: float, signal_sources: array<string, string>}
     */
    public function resolve(Application $application): array
    {
        $user = $application->user;
        $job  = $application->job;

        if (! $user instanceof User || ! $job instanceof Job) {
            Log::warning('CandidateCompositeScoreService: missing user or job, using neutral baselines', [
                'application_id' => $application->id,
            ]);

            return [
                'skill_match_score'     => self::NEUTRAL_BASELINE,
                'resume_quality_score'  => self::NEUTRAL_BASELINE,
                'behavioural_fit_score' => self::NEUTRAL_BASELINE,
                'signal_sources'        => [
                    'skill_match_score'     => 'neutral_baseline',
                    'resume_quality_score'  => 'neutral_baseline',
                    'behavioural_fit_score' => 'neutral_baseline',
                ],
            ];
        }

        [$skillMatch, $skillSource]   = $this->resolveSkillMatch($user, $job);
        [$resumeScore, $resumeSource] = $this->resolveResumeQuality($user);
        [$behavScore, $behavSource]   = $this->resolveBehaviouralFit($user);

        return [
            'skill_match_score'     => $skillMatch,
            'resume_quality_score'  => $resumeScore,
            'behavioural_fit_score' => $behavScore,
            'signal_sources'        => [
                'skill_match_score'     => $skillSource,
                'resume_quality_score'  => $resumeSource,
                'behavioural_fit_score' => $behavSource,
            ],
        ];
    }

    /**
     * Skill match: proportion of the job's required skills that the candidate
     * holds, prioritising Orin-verified skills and weighting by proficiency.
     *
     * @return array{0: float, 1: string}
     */
    private function resolveSkillMatch(User $user, Job $job): array
    {
        $required = $this->normaliseSkillList($job->required_skills);

        if ($required === []) {
            return [self::NEUTRAL_BASELINE, 'neutral_baseline_no_required_skills'];
        }

        $verified = $user->skills()->where('is_verified', true)->get();
        $source   = 'verified_skills';

        if ($verified->isEmpty()) {
            $verified = $user->skills()->get();
            $source   = 'self_reported_skills';
        }

        if ($verified->isEmpty()) {
            return [self::NEUTRAL_BASELINE, 'neutral_baseline_no_candidate_skills'];
        }

        // Map normalised skill name => proficiency (0-100, default 70 when null).
        $candidateSkills = [];
        foreach ($verified as $skill) {
            $name = $this->normaliseSkillName((string) $skill->skill_name);
            if ($name === '') {
                continue;
            }
            $proficiency = $skill->proficiency_score !== null
                ? (float) $skill->proficiency_score
                : 70.0;
            $candidateSkills[$name] = max($candidateSkills[$name] ?? 0.0, $proficiency);
        }

        $matchedProficiencySum = 0.0;
        $matchedCount          = 0;
        foreach ($required as $requiredSkill) {
            $name = $this->normaliseSkillName($requiredSkill);
            if ($name !== '' && isset($candidateSkills[$name])) {
                $matchedProficiencySum += $candidateSkills[$name];
                $matchedCount++;
            }
        }

        $requiredCount = count($required);

        // Coverage: how many required skills are held (0-1).
        $coverage = $matchedCount / $requiredCount;
        // Depth: average proficiency of the matched skills (0-1).
        $depth = $matchedCount > 0 ? ($matchedProficiencySum / $matchedCount) / 100 : 0.0;

        // Blend coverage (breadth) and depth so a candidate who covers most
        // required skills at high proficiency scores highest.
        $score = (($coverage * 0.7) + ($coverage * $depth * 0.3)) * 100;

        return [$this->clamp($score), $source];
    }

    /**
     * Resume quality: latest resume's numeric ATS score.
     *
     * @return array{0: float, 1: string}
     */
    private function resolveResumeQuality(User $user): array
    {
        $resume = $user->resumes()->latest('updated_at')->first();

        if ($resume === null) {
            return [self::NEUTRAL_BASELINE, 'neutral_baseline_no_resume'];
        }

        $ats = $resume->numeric_ats_score;

        if ($ats === null) {
            return [self::NEUTRAL_BASELINE, 'neutral_baseline_no_ats_score'];
        }

        return [$this->clamp((float) $ats), 'resume_ats_score'];
    }

    /**
     * Behavioural fit: the candidate's Vantage composite score.
     *
     * @return array{0: float, 1: string}
     */
    private function resolveBehaviouralFit(User $user): array
    {
        $vantage = $user->vantage_score;

        if ($vantage === null || (float) $vantage <= 0.0) {
            return [self::NEUTRAL_BASELINE, 'neutral_baseline_no_vantage'];
        }

        return [$this->clamp((float) $vantage), 'vantage_score'];
    }

    /**
     * Normalise a job's required_skills value (array or JSON string) to a
     * flat list of skill name strings.
     *
     * @param mixed $value
     * @return array<int, string>
     */
    private function normaliseSkillList(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value   = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($value)) {
            return [];
        }

        $skills = [];
        foreach ($value as $item) {
            if (is_string($item) && trim($item) !== '') {
                $skills[] = $item;
            } elseif (is_array($item) && isset($item['name']) && is_string($item['name'])) {
                $skills[] = $item['name'];
            }
        }

        return array_values(array_unique($skills));
    }

    /**
     * Normalise a skill name for case/whitespace-insensitive comparison.
     *
     * E2: applies lightweight semantic canonicalisation (alias map + common
     * noise stripping such as the ".js" suffix and trailing version numbers) so
     * equivalent skills like "JS"/"JavaScript" or "React.js"/"React" match
     * without an inline embeddings call. This keeps the service synchronous and
     * cost-free while capturing the bulk of real-world skill-name variation.
     */
    private function normaliseSkillName(string $name): string
    {
        $name = strtolower(trim($name));

        if ($name === '') {
            return '';
        }

        // Strip trailing version numbers, e.g. "python 3", "java 17", "vue 3.x".
        $name = preg_replace('/\s+v?\d+(\.\d+)*(\.x)?$/', '', $name) ?? $name;

        // Collapse internal punctuation/whitespace to single spaces.
        $name = preg_replace('/[\/\-_]+/', ' ', $name) ?? $name;
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;
        $name = trim($name);

        // Drop a redundant ".js" suffix ("react.js" => "react") but preserve
        // names where "js" is intrinsic (handled by the alias map below).
        if (str_ends_with($name, '.js')) {
            $name = substr($name, 0, -3);
        }

        return self::SKILL_ALIASES[$name] ?? $name;
    }

    /**
     * E2: canonical alias map for common skill-name synonyms/abbreviations.
     * Keys are already lower-cased and punctuation-normalised.
     *
     * @var array<string, string>
     */
    private const SKILL_ALIASES = [
        'js' => 'javascript',
        'ecmascript' => 'javascript',
        'ts' => 'typescript',
        'py' => 'python',
        'golang' => 'go',
        'reactjs' => 'react',
        'react native' => 'react',
        'nodejs' => 'node',
        'node js' => 'node',
        'vuejs' => 'vue',
        'nextjs' => 'next',
        'nuxtjs' => 'nuxt',
        'postgres' => 'postgresql',
        'postgre' => 'postgresql',
        'mongo' => 'mongodb',
        'k8s' => 'kubernetes',
        'tf' => 'terraform',
        'gcp' => 'google cloud',
        'aws cloud' => 'aws',
        'ms sql' => 'sql server',
        'mssql' => 'sql server',
        'c sharp' => 'c#',
        'csharp' => 'c#',
        'dotnet' => '.net',
        'rest api' => 'rest',
        'restful' => 'rest',
        'ml' => 'machine learning',
        'ai' => 'artificial intelligence',
        'nlp' => 'natural language processing',
        'ci cd' => 'ci/cd',
        'cicd' => 'ci/cd',
    ];

    /**
     * Clamp a value into the 0-100 range and round to 2 decimals.
     */
    private function clamp(float $value): float
    {
        return round(max(0.0, min(100.0, $value)), 2);
    }
}
