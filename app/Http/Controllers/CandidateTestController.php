<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\HiringRound;
use App\Models\RoundAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CandidateTestController extends Controller
{
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

            // Generate questions if not yet generated
            if (empty($attempt->questions)) {
                $questions = $this->generateQuestions($round);
                $attempt->update([
                    'questions'  => $questions,
                    'status'     => 'in_progress',
                    'started_at' => now(),
                ]);
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

            if ($q['type'] === 'mcq') {
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

            if (!is_array($questions) || empty($questions)) {
                throw new \Exception('Invalid JSON response from AI');
            }

            return $questions;
        } catch (\Exception $e) {
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

    private function callAI(string $prompt): string
    {
        // Use config() (not env()) so the key resolves correctly after config:cache in production.
        $openaiKey = config('ai.openai.api_key');

        if ($openaiKey && str_starts_with($openaiKey, 'sk-')) {
            $response = Http::timeout(30)->connectTimeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $openaiKey, 'Content-Type' => 'application/json'])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_completion_tokens' => 2000,
                    'temperature' => 0.7,
                ]);
            if (!$response->successful()) {
                throw new \Exception('OpenAI error ' . $response->status());
            }
            return $response->json('choices.0.message.content') ?? '';
        }

        $azureKey   = config('ai.azure.api_key');
        $deployment = config('ai.azure.deployment_id', 'gpt-5.4');
        $apiVersion = config('ai.azure.api_version', '2025-04-01-preview');
        $endpoint   = rtrim((string) config('ai.azure.endpoint'), '/');

        if (empty($azureKey)) {
            throw new \Exception('No AI credentials configured.');
        }

        $url      = "{$endpoint}/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";
        $response = Http::timeout(30)->connectTimeout(10)
            ->withHeaders(['api-key' => $azureKey, 'Content-Type' => 'application/json'])
            ->post($url, [
                'messages'   => [
                    ['role' => 'system', 'content' => 'You are an expert HR assessor. Return ONLY valid JSON with no markdown.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_completion_tokens' => 2000,
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Azure OpenAI error ' . $response->status());
        }

        return $response->json('choices.0.message.content') ?? '';
    }

    // ─── Fallback Questions ───────────────────────────────────────────────────

    private function fallbackQuestions(string $type, string $jobTitle): array
    {
        $base = [
            ['type' => 'mcq', 'question' => 'Which of the following best describes effective teamwork?', 'options' => ['Working independently', 'Collaborating and communicating clearly', 'Avoiding conflict at all costs', 'Following instructions without question'], 'correct' => 1],
            ['type' => 'mcq', 'question' => 'What is the most important factor in meeting project deadlines?', 'options' => ['Working longer hours', 'Proper planning and prioritization', 'Delegating all tasks', 'Avoiding scope changes'], 'correct' => 1],
            ['type' => 'mcq', 'question' => 'How should you handle a disagreement with a colleague?', 'options' => ['Ignore the issue', 'Escalate immediately to HR', 'Discuss professionally and find a solution', 'Complain to other colleagues'], 'correct' => 2],
            ['type' => 'short', 'question' => "Why are you interested in the {$jobTitle} role?"],
            ['type' => 'short', 'question' => 'Describe a challenge you faced at work and how you resolved it.'],
            ['type' => 'short', 'question' => 'What are your key strengths relevant to this position?'],
        ];
        return $base;
    }
}
