<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Application;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationSession;
use App\Models\Job;
use App\Models\QuestionBank;
use App\Services\AI\Scout\CandidateCompositeScoreService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orin™ Adaptive Evaluation Engine
 *
 * Generates unique per-candidate question banks, manages adaptive difficulty,
 * scores answers, and produces final weighted evaluation scores.
 */
class OrinEvaluationService extends AIService
{
    private const QUESTIONS_PER_DIFFICULTY = 5;
    private const ESCALATE_THRESHOLD = 3;  // consecutive correct to escalate
    private const DEESCALATE_THRESHOLD = 2; // consecutive wrong to de-escalate
    private const SESSION_TTL = 7200;       // Redis TTL: 2 hours

    /**
     * B4/F5: Minimum proportion of assigned questions a candidate must answer
     * before they are allowed to finish the evaluation via the UI completion
     * button. Prevents low-signal sessions from being finalised prematurely.
     */
    public const MIN_COMPLETION_PERCENTAGE = 80;


    private const SYSTEM_PROMPT = <<<PROMPT
You are Orin™, the AI evaluation engine for StudAI Hire.
You are evaluating candidates for job roles. Your questions must be:
- Specific to the candidate's background and the role requirements
- Professionally worded and unambiguous
- Progressive in difficulty
- Fair and bias-free
Return only valid JSON as instructed.
PROMPT;

    /**
     * Generate a personalised question bank for a candidate.
     * Questions are unique per candidate — no two get identical questions.
     */
    public function generateQuestionBank(Job $job, Application $application): Collection
    {
        $candidateContext = $this->buildCandidateContext($application);
        $jobContext = $this->buildJobContext($job);

        $questions = collect();

        foreach (['foundational', 'intermediate', 'advanced'] as $difficulty) {
            $batch = $this->generateQuestionsForDifficulty(
                $job, $candidateContext, $jobContext, $difficulty
            );
            $questions = $questions->merge($batch);
        }

        // Persist to question_banks table
        $saved = collect();
        foreach ($questions as $q) {
            $saved->push(QuestionBank::create([
                'job_id'              => $job->id,
                'difficulty'          => $q['difficulty'],
                'question_type'       => $q['question_type'],
                'topic'               => $q['topic'] ?? null,
                'question_text'       => $q['question_text'],
                'options'             => $q['options'] ?? null,
                'correct_answer'      => $q['correct_answer'] ?? null,
                'evaluation_rubric'   => $q['evaluation_rubric'] ?? null,
                'time_limit_seconds'  => $q['time_limit_seconds'] ?? 120,
                'max_score'           => $q['max_score'] ?? 10,
                'is_behavioural'      => $q['is_behavioural'] ?? false,
                'is_culture_fit'      => $q['is_culture_fit'] ?? false,
            ]));
        }

        return $saved;
    }

    /**
     * Start an evaluation session for a candidate.
     * Stores active state in Redis, persists skeleton to DB.
     */
    public function startSession(Application $application, bool $monitoringConsent = false, ?string $consentIp = null): EvaluationSession
    {
        // F4: A candidate must affirmatively acknowledge anti-cheat monitoring
        // (tab-switch / focus-loss / time-anomaly tracking) before any
        // evaluation session is created.
        if (! $monitoringConsent) {
            throw new \RuntimeException('Monitoring consent is required before an evaluation session can begin.');
        }

        $job = $application->job;

        // F13: Enforce the retake policy. Count prior sessions for this
        // application to derive the attempt number and block once the
        // configured maximum is reached.
        $priorSessions = EvaluationSession::where('application_id', $application->id)
            ->orderByDesc('id')
            ->get();

        $attemptNumber = $priorSessions->count() + 1;
        $maxAttempts = (int) config('ai.evaluation.retake.max_attempts', 1);

        if ($attemptNumber > $maxAttempts) {
            throw new \RuntimeException(
                "Maximum evaluation attempts ({$maxAttempts}) reached for this application."
            );
        }

        $isRetake = $priorSessions->isNotEmpty();
        $policy = (string) config('ai.evaluation.retake.policy', 'new_bank');

        // Generate (or, on a 'same_bank' retake, reuse) the question bank.
        if ($isRetake && $policy === 'same_bank') {
            $priorQuestionIds = $priorSessions->first()->assigned_question_ids ?? [];
            $questionBank = QuestionBank::whereIn('id', $priorQuestionIds)->get();

            if ($questionBank->isEmpty()) {
                $questionBank = $this->generateQuestionBank($job, $application);
            }
        } else {
            $questionBank = $this->generateQuestionBank($job, $application);
        }

        // Shuffle within each difficulty tier (not across) to maintain progression
        $ordered = $questionBank->sortBy(fn($q) => match($q->difficulty) {
            'foundational' => 0,
            'intermediate' => 1,
            'advanced'     => 2,
            default        => 3,
        })->values();

        $sessionToken = Str::random(64);
        $redisKey = "eval_session:{$sessionToken}";

        $session = EvaluationSession::create([
            'application_id'         => $application->id,
            'job_id'                 => $job->id,
            'user_id'                => $application->user_id,
            'status'                 => 'in_progress',
            'session_token'          => $sessionToken,
            'redis_key'              => $redisKey,
            'assigned_question_ids'  => $ordered->pluck('id')->toArray(),
            'current_question_index' => 0,
            'total_questions'        => $ordered->count(),
            'current_difficulty'     => 'foundational',
            'attempt_number'         => $attemptNumber,
            'started_at'             => now(),
            'expires_at'             => now()->addHours(3),
            'monitoring_consent_at'  => now(),
            'monitoring_consent_ip'  => $consentIp,
        ]);

        // Store active state in Redis
        Cache::put($redisKey, [
            'session_id'             => $session->id,
            'application_id'         => $application->id,
            'question_ids'           => $ordered->pluck('id')->toArray(),
            'current_index'          => 0,
            'current_difficulty'     => 'foundational',
            'consecutive_correct'    => 0,
            'consecutive_incorrect'  => 0,
            'tab_switch_count'       => 0,
            'answers'                => [],
        ], self::SESSION_TTL);

        // Update application status
        $application->update([
            'evaluation_status'    => 'in_progress',
            'evaluation_started_at' => now(),
        ]);

        return $session;
    }

    /**
     * Get the current question for a session.
     */
    public function getCurrentQuestion(string $sessionToken): ?array
    {
        $redisData = $this->loadSessionState($sessionToken);
        if (! $redisData) {
            return null;
        }

        $index = $redisData['current_index'];
        $questionId = $redisData['question_ids'][$index] ?? null;

        if (! $questionId) {
            return null;
        }

        $question = QuestionBank::find($questionId);
        if (! $question) {
            return null;
        }

        return [
            'index'              => $index,
            'total'              => count($redisData['question_ids']),
            'question_id'        => $question->id,
            'question_text'      => $question->question_text,
            'question_type'      => $question->question_type,
            'difficulty'         => $question->difficulty,
            'options'            => $question->options,
            'time_limit_seconds' => $question->time_limit_seconds,
            'max_score'          => $question->max_score,
        ];
    }

    /**
     * Submit an answer and advance the session with adaptive difficulty.
     */
    public function submitAnswer(string $sessionToken, int $questionId, mixed $answerData): array
    {
        $redisKey = "eval_session:{$sessionToken}";
        $redisData = $this->loadSessionState($sessionToken);

        if (! $redisData) {
            return ['error' => 'Session expired'];
        }

        $question = QuestionBank::find($questionId);
        if (! $question) {
            return ['error' => 'Question not found'];
        }

        // F6: the DB session row is the source of truth for progress.
        $session = EvaluationSession::where('session_token', $sessionToken)->first();
        if (! $session || $session->status !== 'in_progress') {
            return ['error' => 'Session is not active'];
        }

        // Score the answer
        $scoring = $this->scoreAnswer($question, $answerData);

        $answerIndex = $redisData['current_index'];
        $timeTaken = is_array($answerData) ? ($answerData['time_taken_seconds'] ?? null) : null;

        // Record answer
        $redisData['answers'][] = [
            'question_id'       => $questionId,
            'answer'            => $answerData,
            'score'             => $scoring['score'],
            'is_correct'        => $scoring['is_correct'],
            'time_taken'        => $timeTaken,
        ];

        // Adaptive difficulty logic
        if ($scoring['is_correct']) {
            $redisData['consecutive_correct']++;
            $redisData['consecutive_incorrect'] = 0;
        } else {
            $redisData['consecutive_incorrect']++;
            $redisData['consecutive_correct'] = 0;
        }

        $nextDifficulty = $this->calculateNextDifficulty(
            $redisData['current_difficulty'],
            $redisData['consecutive_correct'],
            $redisData['consecutive_incorrect']
        );
        $redisData['current_difficulty'] = $nextDifficulty;
        $redisData['current_index']++;

        // F6: persist the answer and updated session state to the database on
        // every submission. Redis remains a hot cache; the DB is authoritative
        // so a session can be recovered if Redis expires mid-evaluation.
        $this->persistAnswerState($session, $question, $answerIndex, $answerData, $scoring, $timeTaken, $redisData);

        // Refresh the hot cache
        Cache::put($redisKey, $redisData, self::SESSION_TTL);

        $isComplete = $redisData['current_index'] >= count($redisData['question_ids']);

        if ($isComplete) {
            $this->finaliseSession($sessionToken, $redisData);
        }

        return [
            'scored'         => $scoring['score'],
            'feedback'       => $scoring['feedback'],
            'next_difficulty' => $nextDifficulty,
            'is_complete'    => $isComplete,
            'progress'       => [
                'current' => $redisData['current_index'],
                'total'   => count($redisData['question_ids']),
            ],
        ];
    }

    /**
     * B4/F5: Explicitly finalise an evaluation in response to the candidate
     * pressing the "Finish" button. Unlike the auto-finalise path (which fires
     * only when every question has been answered), this allows early
     * completion but enforces a minimum completeness so partial, low-signal
     * sessions cannot be submitted. Returns a structured error (not an
     * exception) when the threshold is not met so the UI can keep the session
     * open and prompt the candidate to answer more questions.
     *
     * @return array<string, mixed>
     */
    public function completeEvaluation(string $sessionToken): array
    {
        $redisData = $this->loadSessionState($sessionToken);
        if (! $redisData) {
            return ['error' => 'Session expired'];
        }

        $session = EvaluationSession::where('session_token', $sessionToken)->first();
        if (! $session || $session->status !== 'in_progress') {
            return ['error' => 'Session is not active'];
        }

        $total = count($redisData['question_ids'] ?? []);
        $answered = count($redisData['answers'] ?? []);
        $completeness = $total > 0 ? (int) round(($answered / $total) * 100) : 0;

        if ($completeness < self::MIN_COMPLETION_PERCENTAGE) {
            $required = self::MIN_COMPLETION_PERCENTAGE;

            return [
                'error'        => 'incomplete',
                'message'      => "Please answer at least {$required}% of the questions before finishing. You have completed {$completeness}%.",
                'completeness' => $completeness,
                'required'     => $required,
                'answered'     => $answered,
                'total'        => $total,
            ];
        }

        $this->finaliseSession($sessionToken, $redisData);

        return [
            'is_complete'  => true,
            'completeness' => $completeness,
            'answered'     => $answered,
            'total'        => $total,
        ];
    }


    /**
     * F6: Load live session state, preferring the Redis hot cache but falling
     * back to a DB-driven rehydration when the cache has expired.
     */
    private function loadSessionState(string $sessionToken): ?array
    {
        $redisData = Cache::get("eval_session:{$sessionToken}");
        if ($redisData) {
            return $redisData;
        }

        return $this->rehydrateSessionFromDb($sessionToken);
    }

    /**
     * F6: Rebuild the in-memory session shape from the authoritative DB records
     * when the Redis cache is gone. Returns null if the session cannot/should
     * not be resumed (missing, completed, or past its deadline).
     */
    private function rehydrateSessionFromDb(string $sessionToken): ?array
    {
        $session = EvaluationSession::where('session_token', $sessionToken)->first();

        if (! $session || $session->status !== 'in_progress') {
            return null;
        }

        if ($session->expires_at && now()->greaterThan($session->expires_at)) {
            $session->update(['status' => 'expired']);
            return null;
        }

        $answers = EvaluationAnswer::where('evaluation_session_id', $session->id)
            ->orderBy('question_index')
            ->get();

        $redisData = [
            'session_id'            => $session->id,
            'application_id'        => $session->application_id,
            'question_ids'          => $session->assigned_question_ids ?? [],
            'current_index'         => $session->current_question_index,
            'current_difficulty'    => $session->current_difficulty,
            'consecutive_correct'   => $session->consecutive_correct,
            'consecutive_incorrect' => $session->consecutive_incorrect,
            'tab_switch_count'      => $session->tab_switch_count,
            'focus_loss_count'      => $session->focus_loss_count,
            'answers'               => $answers->map(fn (EvaluationAnswer $a) => [
                'question_id' => $a->question_id,
                'answer'      => $a->answer_text ?? $a->answer_options,
                'score'       => (float) $a->score_awarded,
                'is_correct'  => $a->is_correct,
                'time_taken'  => $a->time_taken_seconds,
            ])->all(),
        ];

        // Repopulate the hot cache so subsequent calls are fast again.
        Cache::put("eval_session:{$sessionToken}", $redisData, self::SESSION_TTL);

        return $redisData;
    }

    /**
     * F6: Persist a single answer and the advanced session state to the DB.
     */
    private function persistAnswerState(
        EvaluationSession $session,
        QuestionBank $question,
        int $answerIndex,
        mixed $answerData,
        array $scoring,
        ?int $timeTaken,
        array $redisData
    ): void {
        $answerText = is_array($answerData) ? ($answerData['answer'] ?? null) : $answerData;
        $answerOptions = is_array($answerData) ? ($answerData['options'] ?? $answerData['selected'] ?? null) : null;
        $answerTextValue = is_scalar($answerText) ? (string) $answerText : null;

        EvaluationAnswer::updateOrCreate(
            [
                'evaluation_session_id' => $session->id,
                'question_index'        => $answerIndex,
            ],
            [
                'question_id'        => $question->id,
                'answer_text'        => $answerTextValue,
                'answer_options'     => is_array($answerOptions) ? $answerOptions : null,
                'score_awarded'      => $scoring['score'],
                'max_score'          => $question->max_score ?? 10,
                'ai_feedback'        => $scoring['feedback'] ?? null,
                'is_correct'         => $scoring['is_correct'],
                'is_auto_scored'     => true,
                'time_taken_seconds' => $timeTaken,
                'answered_at'        => now(),
            ]
        );

        $session->update([
            'current_question_index' => $redisData['current_index'],
            'current_difficulty'     => $redisData['current_difficulty'],
            'consecutive_correct'    => $redisData['consecutive_correct'],
            'consecutive_incorrect'  => $redisData['consecutive_incorrect'],
        ]);
    }

    /**
     * Record an anti-cheat event (tab switch, focus loss).
     */
    public function recordAntiCheatEvent(string $sessionToken, string $eventType): void
    {
        $redisKey = "eval_session:{$sessionToken}";
        $redisData = $this->loadSessionState($sessionToken);

        if (! $redisData) {
            return;
        }

        if ($eventType === 'tab_switch') {
            $redisData['tab_switch_count'] = ($redisData['tab_switch_count'] ?? 0) + 1;
        }

        if ($eventType === 'focus_loss') {
            $redisData['focus_loss_count'] = ($redisData['focus_loss_count'] ?? 0) + 1;
        }

        Cache::put($redisKey, $redisData, self::SESSION_TTL);

        // F6: anti-cheat counters are persisted to the DB source of truth too.
        $flagged = ($redisData['tab_switch_count'] ?? 0) >= 5;
        EvaluationSession::where('session_token', $sessionToken)->update(array_filter([
            'tab_switch_count' => $redisData['tab_switch_count'] ?? 0,
            'focus_loss_count' => $redisData['focus_loss_count'] ?? 0,
            'flagged_for_review' => $flagged ?: null,
        ], fn ($v) => $v !== null));
    }

    /**
     * Finalise session: compute final scores and persist to DB.
     */
    private function finaliseSession(string $sessionToken, array $redisData): void
    {
        $session = EvaluationSession::where('session_token', $sessionToken)->first();
        if (! $session) {
            return;
        }

        $answers = $redisData['answers'] ?? [];
        $totalScore = array_sum(array_column($answers, 'score'));
        $maxPossible = $session->total_questions * 10;
        $rawScore = $maxPossible > 0 ? ($totalScore / $maxPossible) * 100 : 0;

        // Weight by difficulty (advanced questions worth more)
        $weightedScore = $this->calculateWeightedScore($answers, $session->assigned_question_ids);

        $session->update([
            'status'           => 'completed',
            'raw_score'        => round($rawScore, 2),
            'weighted_score'   => round($weightedScore, 2),
            'tab_switch_count' => $redisData['tab_switch_count'] ?? 0,
            'completed_at'     => now(),
            'total_time_seconds' => now()->diffInSeconds($session->started_at),
        ]);

        $application = $session->application;

        // Unification bridge: derive the three S.C.O.U.T. composite inputs from
        // trusted candidate-side signals so employer ranking is never blocked.
        $composite = $this->resolveCompositeScores($application);

        $application->update([
            'evaluation_status'        => 'completed',
            'evaluation_score'         => round($weightedScore, 2),
            'evaluation_completed_at'  => now(),
            'skill_match_score'        => $composite['skill_match_score'],
            'resume_quality_score'     => $composite['resume_quality_score'],
            'behavioural_fit_score'    => $composite['behavioural_fit_score'],
        ]);

        // Clear Redis
        Cache::forget("eval_session:{$sessionToken}");
    }

    /**
     * Resolve the three composite ranking inputs, degrading to neutral
     * baselines on any failure so evaluation finalisation never breaks.
     *
     * @return array{skill_match_score: float, resume_quality_score: float, behavioural_fit_score: float}
     */
    private function resolveCompositeScores(Application $application): array
    {
        try {
            $application->loadMissing(['user.skills', 'user.resumes', 'job']);
            $resolved = app(CandidateCompositeScoreService::class)->resolve($application);

            Log::info('Composite ranking inputs resolved', [
                'application_id' => $application->id,
                'sources'        => $resolved['signal_sources'] ?? [],
            ]);

            return [
                'skill_match_score'     => $resolved['skill_match_score'],
                'resume_quality_score'  => $resolved['resume_quality_score'],
                'behavioural_fit_score' => $resolved['behavioural_fit_score'],
            ];
        } catch (\Throwable $e) {
            Log::error('Composite ranking input resolution failed; using neutral baselines', [
                'application_id' => $application->id,
                'error'          => $e->getMessage(),
            ]);

            return [
                'skill_match_score'     => 50.0,
                'resume_quality_score'  => 50.0,
                'behavioural_fit_score' => 50.0,
            ];
        }
    }

    /**
     * Score an individual answer using AI for open-ended types.
     */
    private function scoreAnswer(QuestionBank $question, mixed $answerData): array
    {
        $answerText = is_array($answerData) ? ($answerData['answer_text'] ?? '') : (string) $answerData;

        // Auto-score MCQ
        if ($question->question_type === 'mcq' && $question->correct_answer) {
            $selectedOption = $answerData['selected_option'] ?? '';
            $isCorrect = strtolower(trim($selectedOption)) === strtolower(trim($question->correct_answer));
            return [
                'score'      => $isCorrect ? $question->max_score : 0,
                'is_correct' => $isCorrect,
                'feedback'   => $isCorrect ? 'Correct!' : "The correct answer was: {$question->correct_answer}",
            ];
        }

        // AI-score open-ended answers
        if (empty($answerText)) {
            return ['score' => 0, 'is_correct' => false, 'feedback' => 'No answer provided.'];
        }

        try {
            $rubric = $question->evaluation_rubric ?? "Score based on accuracy, depth, and clarity.";
            $prompt = <<<PROMPT
Question: {$question->question_text}

Candidate's Answer: {$answerText}

Evaluation Rubric: {$rubric}

Score this answer from 0 to {$question->max_score}. Return JSON:
{"score": number, "is_correct": boolean, "feedback": "brief 1-sentence feedback for candidate"}
PROMPT;

            $response = $this->callAzureOpenAI([
                ['role' => 'system', 'content' => 'Score the answer. Return only valid JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ], ['temperature' => 0.1, 'max_completion_tokens' => 200, 'json_mode' => true]);

            $json = trim(preg_replace('/^```json\s*|\s*```$/m', '', $response));
            $result = json_decode($json, true);

            return [
                'score'      => min((float)($result['score'] ?? 0), $question->max_score),
                'is_correct' => (bool)($result['is_correct'] ?? false),
                'feedback'   => $result['feedback'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('OrinEvaluation::scoreAnswer failed', ['error' => $e->getMessage()]);
            return ['score' => 0, 'is_correct' => false, 'feedback' => 'Scoring pending review.'];
        }
    }

    private function calculateNextDifficulty(string $current, int $consecutiveCorrect, int $consecutiveIncorrect): string
    {
        if ($consecutiveCorrect >= self::ESCALATE_THRESHOLD) {
            return match ($current) {
                'foundational' => 'intermediate',
                'intermediate' => 'advanced',
                default        => 'advanced',
            };
        }

        if ($consecutiveIncorrect >= self::DEESCALATE_THRESHOLD) {
            return match ($current) {
                'advanced'     => 'intermediate',
                'intermediate' => 'foundational',
                default        => 'foundational',
            };
        }

        return $current;
    }

    private function calculateWeightedScore(array $answers, array $questionIds): float
    {
        if (empty($answers)) {
            return 0.0;
        }

        $questions = QuestionBank::whereIn('id', $questionIds)->get()->keyBy('id');
        $total = 0.0;
        $maxTotal = 0.0;

        foreach ($answers as $answer) {
            $q = $questions->get($answer['question_id']);
            if (! $q) {
                continue;
            }
            $weight = match ($q->difficulty) {
                'foundational' => 1.0,
                'intermediate' => 1.5,
                'advanced'     => 2.0,
                default        => 1.0,
            };
            $total    += ($answer['score'] ?? 0) * $weight;
            $maxTotal += $q->max_score * $weight;
        }

        return $maxTotal > 0 ? ($total / $maxTotal) * 100 : 0.0;
    }

    private function generateQuestionsForDifficulty(Job $job, string $candidateContext, string $jobContext, string $difficulty): array
    {
        $count = self::QUESTIONS_PER_DIFFICULTY;
        $prompt = <<<PROMPT
Generate {$count} unique {$difficulty}-level evaluation questions for a candidate.

Job Context: {$jobContext}
Candidate Context: {$candidateContext}

Return a JSON object with a single key "questions" whose value is an array of {$count} question objects. Each question object:
{
  "difficulty": "{$difficulty}",
  "question_type": "mcq"|"short_answer"|"scenario"|"code_snippet"|"case_study",
  "topic": "topic name",
  "question_text": "the question",
  "options": ["A. option1", "B. option2", "C. option3", "D. option4"] or null,
  "correct_answer": "for mcq only, e.g. A" or null,
  "evaluation_rubric": "scoring criteria for open questions" or null,
  "time_limit_seconds": 60-300,
  "max_score": 10,
  "is_behavioural": false,
  "is_culture_fit": false
}

Questions must be:
- Unique to THIS candidate based on their background
- Specific to the role's required skills
- Varied in type (mix MCQ with open-ended)
Return ONLY valid JSON, no markdown.
PROMPT;

        try {
            $response = $this->callAzureOpenAI([
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT . "\nReturn only valid JSON, no markdown."],
                ['role' => 'user', 'content' => $prompt],
            ], ['temperature' => 0.8, 'max_completion_tokens' => 3000, 'json_mode' => true]);

            $json = trim(preg_replace('/^```json\s*|\s*```$/m', '', $response));
            $decoded = json_decode($json, true);
            $questions = $decoded['questions'] ?? $decoded ?? null;

            return is_array($questions) ? $questions : [];
        } catch (\Exception $e) {
            Log::error("OrinEvaluation::generateQuestionsForDifficulty ({$difficulty}) failed", ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function buildCandidateContext(Application $application): string
    {
        $parts = [];

        if ($application->user && $application->user->profile) {
            $profile = $application->user->profile;
            $skills  = is_array($profile->skills) ? implode(', ', $profile->skills) : ($profile->skills ?? '');
            $parts[] = "Candidate skills: {$skills}";
            if ($profile->current_title) {
                $parts[] = "Current role: {$profile->current_title}";
            }
            if ($profile->years_of_experience) {
                $parts[] = "Years of experience: {$profile->years_of_experience}";
            }
        }

        if ($application->guest_name) {
            $parts[] = "Candidate: {$application->guest_name}";
        }

        return implode('. ', $parts) ?: 'No candidate profile available.';
    }

    private function buildJobContext(Job $job): string
    {
        $skills = is_array($job->required_skills) ? implode(', ', $job->required_skills) : '';
        return "Role: {$job->title}. Required skills: {$skills}. Level: {$job->experience_level}.";
    }

    /**
     * Generate personalised skill feedback for a candidate after evaluation.
     * Returns markdown-formatted performance summary and improvement plan.
     */
    public function generateSkillFeedback(Application $application): string
    {
        $job         = $application->job;
        $session     = $application->evaluationSession;
        $candidateCtx = $this->buildCandidateContext($application);
        $jobCtx      = $this->buildJobContext($job);

        $evalScore    = number_format((float) ($application->evaluation_score ?? 0), 1);
        $skillScore   = number_format((float) ($application->skill_match_score ?? 0), 1);
        $finalScore   = number_format((float) ($application->final_rank_score ?? 0), 1);
        $totalQuestions = $session?->total_questions ?? 0;
        $questionsAnswered = $totalQuestions;

        $messages = [
            [
                'role'    => 'system',
                'content' => self::SYSTEM_PROMPT,
            ],
            [
                'role'    => 'user',
                'content' => <<<PROMPT
Generate a personalised performance feedback report for a candidate who just completed the Orin™ AI evaluation.

Candidate context: {$candidateCtx}
Job context: {$jobCtx}
Evaluation score: {$evalScore}/100
Skill match score: {$skillScore}/100
Final composite score: {$finalScore}/100
Questions attempted: {$questionsAnswered}

Provide:
1. A brief (2-3 sentence) overall performance summary — encouraging yet honest.
2. Top 3 strengths demonstrated in this evaluation.
3. Top 3 specific improvement areas with actionable next steps (courses, practice suggestions).
4. One motivational closing note.

Format your response as plain HTML (no markdown code fences). Use <h3>, <p>, <ul>, <li> tags only. Keep it concise — under 400 words total.
PROMPT,
            ],
        ];

        try {
            $html = $this->callAzureOpenAI($messages, ['max_completion_tokens' => 600]);

            // F9: sanitise AI-generated HTML before it is injected into emails.
            return \App\Support\HtmlSanitizer::clean($html);
        } catch (\Exception $e) {
            Log::warning('OrinEvaluationService: Skill feedback generation failed', ['error' => $e->getMessage()]);
            return "<p>Thank you for completing the Orin™ evaluation for the <strong>{$job->title}</strong> role. Your results have been recorded and our team will be in touch soon.</p>";
        }
    }
}
