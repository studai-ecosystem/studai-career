<?php

declare(strict_types=1);

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\AIDecisionLog;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * F14: Candidate-facing AI decision explanation (India DPDP Act / GDPR Art. 22).
 *
 * Gives a candidate access to a plain-language explanation of the automated
 * decision made about THEIR OWN application, including the headline score, the
 * contributing factors and the model used. Access is strictly scoped to the
 * authenticated candidate's own application — this controller does NOT use the
 * admin-only AIDecisionLogPolicy abilities (which deny non-admins by design).
 */
class DecisionExplanationController extends Controller
{
    public function show(Application $application): View
    {
        // Scope strictly to the authenticated candidate's own application.
        abort_unless($application->user_id === auth()->id(), 403);

        $decision = AIDecisionLog::query()
            ->where('subject_type', Application::class)
            ->where('subject_id', $application->id)
            ->whereIn('decision_type', [
                AIDecisionLog::TYPE_REJECT,
                AIDecisionLog::TYPE_SHORTLIST,
                AIDecisionLog::TYPE_SCORE,
                AIDecisionLog::TYPE_RECOMMEND,
            ])
            ->latest('id')
            ->first();

        $application->loadMissing('job');

        $factors = $this->normaliseFactors($decision);

        return view('candidate.decision-explanation', [
            'application' => $application,
            'decision'    => $decision,
            'factors'     => $factors,
        ]);
    }

    /**
     * Normalise stored score factors into a candidate-friendly label/value list.
     *
     * @return Collection<int, array{label: string, value: string}>
     */
    private function normaliseFactors(?AIDecisionLog $decision): Collection
    {
        $factors = $decision?->score_factors;

        if (! is_array($factors) || $factors === []) {
            return collect();
        }

        return collect($factors)
            ->map(function ($value, $key): ?array {
                if (! is_scalar($value)) {
                    return null;
                }

                $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));

                if (is_numeric($value)) {
                    $number = (float) $value;
                    $display = $number <= 1 && $number > 0
                        ? round($number * 100, 1) . '/100'
                        : round($number, 1) . '/100';
                } else {
                    $display = (string) $value;
                }

                return ['label' => $label, 'value' => $display];
            })
            ->filter()
            ->values();
    }
}
