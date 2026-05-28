<?php

declare(strict_types=1);

namespace App\Services\ResponsibleAI;

use App\Models\AIDecisionLog;
use App\Models\HumanOverride;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * HumanOverrideService
 *
 * Records and applies human overrides on any AI decision.
 * The override ALWAYS wins — AI is advisory only.
 */
class HumanOverrideService
{
    /**
     * Apply a human override to an AI decision.
     *
     * @param  AIDecisionLog  $log             The original AI decision
     * @param  string         $overrideDecision The human's decision
     * @param  string         $reason           Why the human is overriding
     * @param  array          $options {
     *   'override_score'   => float|null,
     *   'category'         => HumanOverride::CAT_* string,
     *   'is_bias_correction' => bool,
     *   'additional_context' => array,
     *   'justification'    => string,
     * }
     * @param  int|null       $overriderId      Defaults to Auth::id()
     */
    public function override(
        AIDecisionLog $log,
        string $overrideDecision,
        string $reason,
        array $options = [],
        ?int $overriderId = null
    ): HumanOverride {
        $userId = $overriderId ?? Auth::id();
        $userRole = Auth::user()?->getRoleNames()->first() ?? 'system';

        $isBiasCorrection = (bool) ($options['is_bias_correction'] ?? false);

        $override = HumanOverride::create([
            'ai_decision_log_id'     => $log->id,
            'subject_type'           => $log->subject_type,
            'subject_id'             => $log->subject_id,
            'overrider_id'           => $userId,
            'overrider_role'         => $userRole,
            'original_decision'      => $log->ai_recommendation,
            'original_score'         => $log->ai_score,
            'override_decision'      => $overrideDecision,
            'override_score'         => $options['override_score'] ?? null,
            'reason'                 => $reason,
            'override_category'      => $options['category'] ?? HumanOverride::CAT_GENERAL,
            'is_bias_correction'     => $isBiasCorrection,
            'additional_context'     => $options['additional_context'] ?? null,
            'requires_justification' => $isBiasCorrection || ($log->bias_flagged ?? false),
            'justification'          => $options['justification'] ?? null,
            'acknowledged_at'        => now(),
        ]);

        // Mark the AI log as overridden
        $log->update([
            'was_overridden' => true,
            'final_decision' => $overrideDecision,
        ]);

        Log::info('Human override applied', [
            'ai_decision_log_id' => $log->id,
            'subject'            => "{$log->subject_type}:{$log->subject_id}",
            'original'           => $log->ai_recommendation,
            'override'           => $overrideDecision,
            'overrider_id'       => $userId,
            'category'           => $options['category'] ?? HumanOverride::CAT_GENERAL,
            'is_bias_correction' => $isBiasCorrection,
        ]);

        return $override;
    }

    /**
     * Apply a direct override without a prior AIDecisionLog (ad-hoc).
     *
     * Use this when no AI log exists but a human is still overriding a
     * system-generated value (e.g., status change on an application).
     */
    public function overrideDirect(
        string $subjectType,
        int $subjectId,
        string $originalDecision,
        string $overrideDecision,
        string $reason,
        array $options = []
    ): HumanOverride {
        $userId = Auth::id();
        $userRole = Auth::user()?->getRoleNames()->first() ?? 'unknown';

        $override = HumanOverride::create([
            'ai_decision_log_id'     => null,
            'subject_type'           => $subjectType,
            'subject_id'             => $subjectId,
            'overrider_id'           => $userId,
            'overrider_role'         => $userRole,
            'original_decision'      => $originalDecision,
            'original_score'         => $options['original_score'] ?? null,
            'override_decision'      => $overrideDecision,
            'override_score'         => $options['override_score'] ?? null,
            'reason'                 => $reason,
            'override_category'      => $options['category'] ?? HumanOverride::CAT_GENERAL,
            'is_bias_correction'     => (bool) ($options['is_bias_correction'] ?? false),
            'additional_context'     => $options['additional_context'] ?? null,
            'requires_justification' => (bool) ($options['requires_justification'] ?? false),
            'justification'          => $options['justification'] ?? null,
            'acknowledged_at'        => now(),
        ]);

        return $override;
    }

    /**
     * Check if a subject has an active human override.
     */
    public function hasOverride(string $subjectType, int $subjectId): bool
    {
        return HumanOverride::forSubject($subjectType, $subjectId)->exists();
    }

    /**
     * Get the most recent override for a subject.
     */
    public function getLatest(string $subjectType, int $subjectId): ?HumanOverride
    {
        return HumanOverride::forSubject($subjectType, $subjectId)
            ->with('overrider')
            ->latest()
            ->first();
    }

    /**
     * Get the effective decision for a subject (override wins over AI).
     */
    public function getEffectiveDecision(
        string $subjectType,
        int $subjectId,
        string $fallbackDecision = 'pending'
    ): array {
        $override = $this->getLatest($subjectType, $subjectId);

        if ($override) {
            return [
                'decision'     => $override->override_decision,
                'score'        => $override->override_score,
                'source'       => 'human',
                'overrider'    => $override->overrider?->name,
                'reason'       => $override->reason,
                'overridden_at' => $override->created_at,
            ];
        }

        $aiLog = AIDecisionLog::forSubject($subjectType, $subjectId)->latest()->first();
        if ($aiLog) {
            return [
                'decision' => $aiLog->ai_recommendation ?? $fallbackDecision,
                'score'    => $aiLog->ai_score,
                'source'   => 'ai',
                'model'    => $aiLog->model_used,
            ];
        }

        return ['decision' => $fallbackDecision, 'source' => 'default'];
    }
}
