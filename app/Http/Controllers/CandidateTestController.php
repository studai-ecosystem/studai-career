<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\HiringRound;
use App\Models\RoundAttempt;
use App\Services\AI\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CandidateTestController extends Controller
{
    public function __construct(private readonly AIService $aiService) {}

    // ─── Show / Start Test ───────────────────────────────────────────────────

    public function show(int $jobId, int $roundId)
    {
        try {
            $round = HiringRound::with('job')->findOrFail($roundId);

            abort_if($round->job_id !== $jobId, 404);

            $application = Application::where('user_id', Auth::id())
                ->where('job_id', $jobId)
                ->first();

            if (!$application) {
                return redirect()->route('jobs.show', $jobId)
                    ->with('error', 'You must apply for this job before taking the test.');
            }

            $attempt = RoundAttempt::firstOrCreate(
                ['hiring_round_id' => $roundId, 'user_id' => Auth::id()],
                ['application_id' => $application->id, 'status' => 'not_started']
            );

            // Already evaluated — redirect to result page
            if (in_array($attempt->status, ['submitted', 'evaluated'], true)) {
                return redirect()->route('candidate.test.result', [$jobId, $roundId]);
            }

            // Track which round type the stored questions were generated for so we can
            // refresh stale sets (e.g. questions generated before type-specific banks
            // existed, which made every round show the same company-info questions).
            $tracksType = \Illuminate\Support\Facades\Schema::hasColumn('round_attempts', 'generated_type');
            $typeMismatch = $tracksType && $attempt->generated_type !== null && $attempt->generated_type !== $round->type;
            // Heal legacy attempts (generated before the marker existed) once: their
            // questions may be the old generic/company-info set regardless of round type.
            $needsHeal = $tracksType && $attempt->generated_type === null && !empty($attempt->questions);
            // Column-independent safety net: detect company/culture-style questions
            // stored for a round type that should NOT be about the company. This fixes
            // existing attempts even before the generated_type migration runs.
            $contentStale = !empty($attempt->questions) && $this->questionsLookMismatched($attempt->questions, $round->type);

            // Generate questions if not yet generated, or if the stored set was
            // generated for a different round type (stale before submission).
            if (empty($attempt->questions) || $typeMismatch || $needsHeal || $contentStale) {
                $questions = $this->generateQuestions($round);
                $payload = [
                    'questions'  => $questions,
                    'status'     => 'in_progress',
                    'started_at' => $attempt->started_at ?? now(),
                ];
                if ($tracksType) {
                    $payload['generated_type'] = $round->type;
                }
                $attempt->update($payload);
            } elseif ($attempt->status === 'not_started') {
                $attempt->update(['status' => 'in_progress', 'started_at' => now()]);
            }

            return view('candidate.test', compact('round', 'attempt'));
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Let intentional aborts (404, etc.) bubble up untouched.
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to start candidate test', [
                'job_id'   => $jobId,
                'round_id' => $roundId,
                'user_id'  => Auth::id(),
                'error'    => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            return redirect()->route('jobs.show', $jobId)
                ->with('error', 'We could not start the test right now. Please try again in a moment.');
        }
    }

    // ─── Submit Test ──────────────────────────────────────────────────────────

    public function submit(Request $request, int $jobId, int $roundId)
    {
        try {
            $round   = HiringRound::with('job')->findOrFail($roundId);
            abort_if($round->job_id !== $jobId, 404);

            $attempt = RoundAttempt::where('hiring_round_id', $roundId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Already submitted — just redirect to result
            if ($attempt->status === 'submitted' || $attempt->status === 'evaluated') {
                return redirect()->route('candidate.test.result', [$jobId, $roundId]);
            }

            $answers = $request->input('answers', []);

            // Auto-score MCQ answers and collect short answers
            $questions   = $attempt->questions ?? [];
            $score       = 0;
            $total       = 0;
            $enriched    = [];

            foreach ($questions as $i => $q) {
                $given = $answers[$i] ?? null;
                $entry = $q;
                $entry['given'] = $given;

                if (($q['type'] ?? 'short') === 'mcq') {
                    $total++;
                    if ($given !== null && (int) $given === (int) ($q['correct'] ?? -1)) {
                        $score++;
                        $entry['correct_answer'] = true;
                    } else {
                        $entry['correct_answer'] = false;
                    }
                }
                $enriched[] = $entry;
            }

            $mcqScore = $total > 0 ? (int) round($score / $total * 100) : null;

            $attempt->update([
                'answers'      => $enriched,
                'score'        => $mcqScore,
                'violations'   => (int) $request->input('violations', 0),
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);

            // Fire async AI evaluation for short-answer questions
            // (kept lightweight — no queues needed for now)
            $this->evaluateShortAnswers($attempt, $round);

            return redirect()->route('candidate.test.result', [$jobId, $roundId])
                ->with('success', 'Test submitted! Your results are being processed.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to submit candidate test', [
                'job_id'   => $jobId,
                'round_id' => $roundId,
                'user_id'  => Auth::id(),
                'error'    => $e->getMessage(),
                'file'     => $e->getFile() . ':' . $e->getLine(),
            ]);

            return redirect()->route('candidate.test.show', [$jobId, $roundId])
                ->with('error', 'We could not submit your test. Please try again.');
        }
    }

    // ─── Result Page ──────────────────────────────────────────────────────────

    public function result(int $jobId, int $roundId)
    {
        $round   = HiringRound::with('job')->findOrFail($roundId);
        abort_if($round->job_id !== $jobId, 404);

        $attempt = RoundAttempt::where('hiring_round_id', $roundId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('candidate.test-result', compact('round', 'attempt'));
    }

    // ─── AI Question Generation ───────────────────────────────────────────────

    /**
     * Column-independent staleness check. Returns true when a stored question set
     * is clearly about the company/culture but the round is a skills round
     * (aptitude/technical/hr_interview/practical/portfolio_review). This catches
     * legacy attempts that stored the old generic company-info questions for every
     * round type, so they get regenerated with the correct type-specific bank.
     */
    private function questionsLookMismatched(array $questions, string $roundType): bool
    {
        $companyTypes = ['info_test', 'culture_fit'];
        if (in_array($roundType, $companyTypes, true)) {
            return false;
        }

        $total = 0;
        $companyHits = 0;
        foreach ($questions as $q) {
            $text = strtolower((string) ($q['question'] ?? ''));
            if ($text === '') {
                continue;
            }
            $total++;
            if (preg_match('/\b(company|culture|mission|values|organization|organisation|workplace|industry)\b/', $text)) {
                $companyHits++;
            }
        }

        // Treat as stale only when the set is dominated by company/culture wording.
        return $total > 0 && ($companyHits / $total) >= 0.5;
    }

    private function generateQuestions(HiringRound $round): array
    {
        $job         = $round->job;
        $roundType   = $round->type;
        $jobTitle    = $job->title ?? 'General';
        $company     = $job->company_name ?? 'the company';
        $description = $job->description ? substr($job->description, 0, 400) : '';

        $typeInstructions = [
            'info_test'       => "Generate 8 multiple-choice questions about the company '{$company}', its culture, values, and industry. Base them on: {$description}. Make them engaging and fair.",
            'aptitude'        => "Generate 5 logical reasoning MCQ and 3 short-answer questions testing numerical ability, pattern recognition, and verbal reasoning for a {$jobTitle} candidate.",
            'technical'       => "Generate 5 technical MCQ and 3 short-answer questions relevant to a '{$jobTitle}' role. Cover core concepts, tools, and problem-solving scenarios.",
            'practical'       => "Generate 3 MCQ and 5 short-answer scenario-based questions for a '{$jobTitle}' role. Present real workplace situations the candidate must respond to.",
            'hr_interview'    => "Generate 8 behavioral short-answer interview questions for a '{$jobTitle}' candidate. Use the STAR format prompts (Situation, Task, Action, Result).",
            'culture_fit'     => "Generate 4 MCQ and 4 short-answer questions assessing whether a candidate fits '{$company}' culture and values. Base culture on: {$description}.",
            'portfolio_review'=> "Generate 8 short-answer questions asking the candidate to describe past work, projects, and their approach to design/problem-solving for a '{$jobTitle}' role.",
        ];

        $instruction = $typeInstructions[$roundType] ?? "Generate 8 mixed questions (MCQ + short answer) for a '{$jobTitle}' assessment.";

        $prompt = <<<PROMPT
{$instruction}

Return ONLY a valid JSON array with this exact structure:
[
  {
    "type": "mcq",
    "question": "Question text here?",
    "options": ["Option A", "Option B", "Option C", "Option D"],
    "correct": 0
  },
  {
    "type": "short",
    "question": "Question text here?"
  }
]

Rules:
- "correct" is the 0-based index of the correct option for MCQ questions
- Short answer questions have no "options" or "correct" field
- Return pure JSON array only, no markdown, no explanation
PROMPT;

        try {
            $raw = $this->callAI($prompt);
            $raw = preg_replace('/^```(?:json)?\s*/m', '', $raw);
            $raw = preg_replace('/\s*```$/m', '', $raw);
            $questions = json_decode(trim($raw), true);

            // Azure/OpenAI sometimes wrap the array, e.g. {"questions": [...]}.
            if (is_array($questions) && isset($questions['questions']) && is_array($questions['questions'])) {
                $questions = $questions['questions'];
            }

            $questions = $this->normalizeQuestions(is_array($questions) ? $questions : []);

            if (empty($questions)) {
                throw new \Exception('Invalid JSON response from AI');
            }

            return $questions;
        } catch (\Throwable $e) {
            Log::warning('AI question generation failed, using fallback', ['error' => $e->getMessage()]);
            return $this->fallbackQuestions($roundType, $jobTitle);
        }
    }

    // ─── AI Short-Answer Evaluation ───────────────────────────────────────────

    private function evaluateShortAnswers(RoundAttempt $attempt, HiringRound $round): void
    {
        $answers   = $attempt->answers ?? [];
        $shortQAs  = array_filter($answers, fn($a) => ($a['type'] ?? '') === 'short' && !empty($a['given']));

        if (empty($shortQAs)) {
            $attempt->update(['status' => 'evaluated']);
            return;
        }

        $qa = '';
        foreach ($shortQAs as $item) {
            $qa .= "Q: {$item['question']}\nA: {$item['given']}\n\n";
        }

        $prompt = "You are evaluating candidate answers for a '{$round->name}' round for the role '{$round->job->title}'.

Evaluate these answers and provide:
1. A score out of 100 for the overall short-answer performance
2. A brief 2-3 sentence feedback summary

Q&A:
{$qa}

Return ONLY valid JSON: {\"score\": 85, \"feedback\": \"The candidate demonstrated...\"}";

        try {
            $raw      = $this->callAI($prompt);
            $raw      = preg_replace('/^```(?:json)?\s*/m', '', $raw);
            $raw      = preg_replace('/\s*```$/m', '', $raw);
            $result   = json_decode(trim($raw), true);
            $aiScore  = (int) ($result['score'] ?? 0);
            $feedback = $result['feedback'] ?? null;

            // Blend MCQ score and AI short-answer score
            $mcqCount   = count(array_filter($answers, fn($a) => ($a['type'] ?? '') === 'mcq'));
            $shortCount = count($shortQAs);
            $total      = $mcqCount + $shortCount;

            if ($total > 0 && $attempt->score !== null) {
                $blended = (int) round(
                    ($attempt->score * $mcqCount + $aiScore * $shortCount) / $total
                );
            } else {
                $blended = $aiScore ?: $attempt->score;
            }

            $attempt->update([
                'score'      => $blended,
                'ai_feedback'=> $feedback,
                'status'     => 'evaluated',
            ]);
        } catch (\Exception $e) {
            Log::warning('AI evaluation failed', ['error' => $e->getMessage()]);
            $attempt->update(['status' => 'evaluated']);
        }
    }

    // ─── AI Helper ────────────────────────────────────────────────────────────

    /**
     * Call AI using the centralized AIService (circuit breaker + Anthropic fallback).
     * Throws on total failure; caller falls back to static question banks.
     */
    private function callAI(string $prompt): string
    {
        return $this->aiService->generateText(
            $prompt,
            'You are an expert HR assessor. Return ONLY valid JSON with no markdown.',
            ['max_tokens' => 2000, 'skip_cache' => true]
        );
    }

    // ─── Fallback Questions ───────────────────────────────────────────────────

    /**
     * Normalize AI-generated questions into a safe, predictable structure so the
     * view can never crash on malformed data. Every MCQ is guaranteed to have at
     * least two string options and a valid 0-based "correct" index; anything
     * unusable is downgraded to a short-answer question or dropped.
     */
    private function normalizeQuestions(array $questions): array
    {
        $clean = [];

        foreach ($questions as $q) {
            if (!is_array($q)) {
                continue;
            }

            $text = trim((string) ($q['question'] ?? ''));
            if ($text === '') {
                continue;
            }

            $type = (($q['type'] ?? '') === 'mcq') ? 'mcq' : 'short';

            if ($type === 'mcq') {
                $options = $q['options'] ?? [];
                if (!is_array($options)) {
                    $options = [];
                }

                $options = array_values(array_filter(
                    array_map(fn ($o) => is_scalar($o) ? trim((string) $o) : '', $options),
                    fn ($o) => $o !== ''
                ));

                // Not a usable MCQ — downgrade to short answer.
                if (count($options) < 2) {
                    $clean[] = ['type' => 'short', 'question' => $text];
                    continue;
                }

                $correct = (int) ($q['correct'] ?? 0);
                if ($correct < 0 || $correct >= count($options)) {
                    $correct = 0;
                }

                $clean[] = [
                    'type'     => 'mcq',
                    'question' => $text,
                    'options'  => $options,
                    'correct'  => $correct,
                ];
            } else {
                $clean[] = ['type' => 'short', 'question' => $text];
            }
        }

        return $clean;
    }

    private function fallbackQuestions(string $type, string $jobTitle): array
    {
        $banks = [
            'info_test' => [
                ['type' => 'mcq', 'question' => 'When researching a company before joining, which source is generally most reliable for its official values?', 'options' => ['Anonymous forums', 'The company\'s official website and reports', 'Unverified social media posts', 'Competitor advertisements'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'Which of the following best reflects a strong cultural fit with an organization?', 'options' => ['Ignoring company values', 'Aligning your work with the company mission', 'Working only for the paycheck', 'Avoiding collaboration'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'Why is understanding a company\'s industry important for an employee?', 'options' => ['It is not important', 'It helps make informed, relevant decisions', 'Only managers need to know it', 'It slows down work'], 'correct' => 1],
                ['type' => 'short', 'question' => 'What attracts you most to this company and its mission?'],
                ['type' => 'short', 'question' => 'How would you contribute to this company\'s culture and values?'],
                ['type' => 'short', 'question' => 'What do you know about the products or services this company offers?'],
            ],
            'aptitude' => [
                ['type' => 'mcq', 'question' => 'What is the next number in the sequence: 2, 6, 12, 20, 30, ?', 'options' => ['36', '40', '42', '44'], 'correct' => 2],
                ['type' => 'mcq', 'question' => 'If 5 machines make 5 widgets in 5 minutes, how long do 100 machines take to make 100 widgets?', 'options' => ['100 minutes', '20 minutes', '5 minutes', '1 minute'], 'correct' => 2],
                ['type' => 'mcq', 'question' => 'A shirt costs $40 after a 20% discount. What was its original price?', 'options' => ['$48', '$50', '$52', '$60'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'Which word is the odd one out?', 'options' => ['Apple', 'Banana', 'Carrot', 'Mango'], 'correct' => 2],
                ['type' => 'mcq', 'question' => 'If "FACE" is coded as "GBDF", how is "BEAD" coded?', 'options' => ['CFBE', 'CFBD', 'CEBF', 'DFCE'], 'correct' => 0],
                ['type' => 'short', 'question' => 'Describe how you would approach solving a problem when you do not immediately know the answer.'],
                ['type' => 'short', 'question' => 'Give an example of a time you used logical reasoning to reach a conclusion.'],
                ['type' => 'short', 'question' => 'How do you check your work to avoid careless mistakes under time pressure?'],
            ],
            'technical' => [
                ['type' => 'mcq', 'question' => 'What is the primary purpose of version control systems like Git?', 'options' => ['Designing UIs', 'Tracking and managing changes to code', 'Hosting websites', 'Writing documentation'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'Which data structure uses Last-In-First-Out (LIFO) ordering?', 'options' => ['Queue', 'Stack', 'Linked list', 'Tree'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'What does an API primarily allow?', 'options' => ['Styling pages', 'Communication between software systems', 'Storing images', 'Encrypting passwords only'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'Which practice helps prevent introducing bugs when changing existing code?', 'options' => ['Skipping tests', 'Writing automated tests', 'Removing comments', 'Avoiding code review'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'What is the time complexity of binary search on a sorted array?', 'options' => ['O(n)', 'O(n log n)', 'O(log n)', 'O(1)'], 'correct' => 2],
                ['type' => 'short', 'question' => "Describe a technical problem you solved relevant to a {$jobTitle} role."],
                ['type' => 'short', 'question' => 'How do you keep your technical skills up to date?'],
                ['type' => 'short', 'question' => 'Explain how you would debug a feature that works locally but fails in production.'],
            ],
            'practical' => [
                ['type' => 'mcq', 'question' => 'A deadline is at risk because of an unexpected blocker. What is the best first step?', 'options' => ['Hide the problem', 'Communicate early with stakeholders and propose options', 'Miss the deadline silently', 'Blame a teammate'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'You receive conflicting instructions from two managers. What should you do?', 'options' => ['Ignore both', 'Pick one at random', 'Clarify priorities with both to align', 'Do nothing until told again'], 'correct' => 2],
                ['type' => 'mcq', 'question' => 'A customer reports an urgent issue. What is the most professional response?', 'options' => ['Ignore until convenient', 'Acknowledge, investigate, and keep them updated', 'Tell them it is not your job', 'Close the ticket immediately'], 'correct' => 1],
                ['type' => 'short', 'question' => 'Describe how you would prioritize multiple urgent tasks competing for your time.'],
                ['type' => 'short', 'question' => "Walk through how you would handle a typical real-world scenario in a {$jobTitle} role."],
                ['type' => 'short', 'question' => 'Tell us about a time you improved a process at work. What was the impact?'],
                ['type' => 'short', 'question' => 'How do you ensure quality when working quickly under pressure?'],
            ],
            'hr_interview' => [
                ['type' => 'short', 'question' => 'Tell us about yourself and why you are interested in this role.'],
                ['type' => 'short', 'question' => 'Describe a situation where you faced a significant challenge. (Situation/Task)'],
                ['type' => 'short', 'question' => 'What actions did you take to address that challenge? (Action)'],
                ['type' => 'short', 'question' => 'What was the outcome and what did you learn? (Result)'],
                ['type' => 'short', 'question' => 'Tell us about a time you worked in a team that had a conflict. How did you handle it?'],
                ['type' => 'short', 'question' => 'Where do you see yourself professionally in the next few years?'],
                ['type' => 'short', 'question' => 'Why are you leaving your current/most recent position?'],
                ['type' => 'short', 'question' => 'Describe a time you received difficult feedback and how you responded.'],
            ],
            'culture_fit' => [
                ['type' => 'mcq', 'question' => 'Which behavior best supports a positive team culture?', 'options' => ['Withholding information', 'Sharing knowledge and helping teammates', 'Taking sole credit', 'Avoiding feedback'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'How do you prefer to receive feedback?', 'options' => ['Never', 'Only when forced', 'Constructively and regularly', 'Anonymously only'], 'correct' => 2],
                ['type' => 'mcq', 'question' => 'What does inclusivity in the workplace mean to you?', 'options' => ['Excluding different views', 'Valuing diverse perspectives and people', 'Working alone always', 'Ignoring others'], 'correct' => 1],
                ['type' => 'mcq', 'question' => 'When a teammate succeeds, the best response is to:', 'options' => ['Feel threatened', 'Recognize and celebrate their success', 'Take the credit', 'Stay silent'], 'correct' => 1],
                ['type' => 'short', 'question' => 'Describe the kind of work environment in which you do your best work.'],
                ['type' => 'short', 'question' => 'How do you handle working with people whose styles differ from yours?'],
                ['type' => 'short', 'question' => 'What values are most important to you in a workplace?'],
                ['type' => 'short', 'question' => 'How do you contribute to a positive team atmosphere?'],
            ],
            'portfolio_review' => [
                ['type' => 'short', 'question' => 'Walk us through a project you are most proud of. What was your role?'],
                ['type' => 'short', 'question' => 'What problem did that project solve and who was it for?'],
                ['type' => 'short', 'question' => 'Describe a key decision you made during the project and your reasoning.'],
                ['type' => 'short', 'question' => 'What challenges did you encounter and how did you overcome them?'],
                ['type' => 'short', 'question' => 'What would you do differently if you started that project again?'],
                ['type' => 'short', 'question' => "What skills relevant to the {$jobTitle} role does your portfolio demonstrate?"],
                ['type' => 'short', 'question' => 'How do you measure the success or impact of your work?'],
                ['type' => 'short', 'question' => 'Describe your typical approach or process when starting a new project.'],
            ],
        ];

        $default = [
            ['type' => 'mcq', 'question' => 'Which of the following best describes effective teamwork?', 'options' => ['Working independently', 'Collaborating and communicating clearly', 'Avoiding conflict at all costs', 'Following instructions without question'], 'correct' => 1],
            ['type' => 'mcq', 'question' => 'What is the most important factor in meeting project deadlines?', 'options' => ['Working longer hours', 'Proper planning and prioritization', 'Delegating all tasks', 'Avoiding scope changes'], 'correct' => 1],
            ['type' => 'mcq', 'question' => 'How should you handle a disagreement with a colleague?', 'options' => ['Ignore the issue', 'Escalate immediately to HR', 'Discuss professionally and find a solution', 'Complain to other colleagues'], 'correct' => 2],
            ['type' => 'short', 'question' => "Why are you interested in the {$jobTitle} role?"],
            ['type' => 'short', 'question' => 'Describe a challenge you faced at work and how you resolved it.'],
            ['type' => 'short', 'question' => 'What are your key strengths relevant to this position?'],
        ];

        return $banks[$type] ?? $default;
    }
}
