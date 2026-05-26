<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Services\ATSAnalyzerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResumeATSController extends Controller
{
    public function __construct(private readonly ATSAnalyzerService $ats) {}

    /**
     * Step 1 — ATS Report: run analysis and show the report page.
     * Analysis is cached by resume content hash (invalidated on any update).
     */
    public function show(Resume $resume)
    {
        $this->authorize('view', $resume);

        $analysis = $this->runAndCacheAnalysis($resume);

        return view('resume.ats', compact('resume', 'analysis'));
    }

    /**
     * Step 2 — Editable Resume View.
     */
    public function editor(Resume $resume)
    {
        $this->authorize('view', $resume);

        $analysis = $resume->ats_analysis ?? $this->runAndCacheAnalysis($resume);

        return view('resume.ats-editor', compact('resume', 'analysis'));
    }

    /**
     * AJAX save from the editable resume view.
     */
    public function save(Request $request, Resume $resume)
    {
        $this->authorize('update', $resume);

        $allowed = [
            'full_name', 'professional_summary', 'location',
            'linkedin_url', 'github_url', 'portfolio_url',
            'experience', 'education', 'skills',
            'certifications', 'achievements', 'languages',
        ];

        $data = $request->only($allowed);

        if (empty($data)) {
            return response()->json(['error' => 'No valid fields provided.'], 422);
        }

        foreach (['experience', 'education', 'skills', 'certifications', 'achievements', 'languages'] as $arrayField) {
            if (isset($data[$arrayField]) && !is_array($data[$arrayField])) {
                $decoded = json_decode($data[$arrayField], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['error' => "Invalid JSON for field: {$arrayField}"], 422);
                }
                $data[$arrayField] = $decoded;
            }
        }

        $resume->update($data);

        Cache::forget("ats_analysis_{$resume->id}");
        $analysis = $this->ats->analyze($resume);
        $resume->update([
            'ats_score'    => $this->scoreToLevel($analysis['score']),
            'ats_analysis' => $analysis,
        ]);

        return response()->json([
            'success'  => true,
            'score'    => $analysis['score'],
            'label'    => $analysis['label'],
            'analysis' => $analysis,
        ]);
    }

    /**
     * AI-powered bullet improvement suggestions.
     * Returns 3 specific rewrites tailored to the provided text.
     */
    public function suggestImprovement(Request $request, Resume $resume): JsonResponse
    {
        $this->authorize('update', $resume);

        $validated = $request->validate([
            'text' => 'required|string|max:600',
            'type' => 'required|in:metric,passive,buzzword,format',
            'word' => 'nullable|string|max:100',
        ]);

        $text = trim($validated['text']);
        $type = $validated['type'];

        try {
            $prompt = match ($type) {
                'metric' => <<<PROMPT
You are an expert resume writer. Rewrite this resume bullet point 3 different ways, each with specific measurable results added (use numbers, percentages, dollar amounts, timeframes, or team sizes relevant to a software/tech role).

Original: "{$text}"

Return ONLY a valid JSON array of exactly 3 improved strings. No markdown, no explanation, no keys — just the array.
Example: ["Optimized database queries reducing load time by 42% for 80K+ users", "Delivered REST API serving 15K requests/day with 99.9% uptime", "Reduced deployment time from 45 min to 8 min through CI/CD pipeline improvements"]
PROMPT,
                'passive' => <<<PROMPT
You are an expert resume writer. Rewrite this resume bullet point 3 different ways using strong action verbs and active voice. Make each version impactful and specific.

Original: "{$text}"

Return ONLY a valid JSON array of exactly 3 improved strings. No markdown, no explanation.
PROMPT,
                'buzzword' => <<<PROMPT
You are an expert resume writer. The word "{$validated['word']}" is a resume buzzword/cliché. Rewrite this bullet point 3 ways replacing that word with specific, concrete language.

Original: "{$text}"

Return ONLY a valid JSON array of exactly 3 improved strings. No markdown, no explanation.
PROMPT,
                'format' => <<<PROMPT
You are an expert resume writer. Convert this experience description into exactly 3 concise, ATS-friendly bullet points. Each must start with a strong action verb and ideally include a measurable result (numbers, %, $, timeframes).

Description: "{$text}"

Return ONLY a valid JSON array of exactly 3 bullet point strings. No markdown, no numbering, no extra text.
PROMPT,
            };

            $endpoint    = rtrim((string) config('ai.azure.endpoint', ''), '/');
            $apiKey      = (string) config('ai.azure.api_key', '');
            $deployId    = (string) config('ai.azure.deployment_id', config('ai.azure.models.chat', 'gpt-4o-mini'));
            $apiVersion  = (string) config('ai.azure.api_version', '2024-12-01-preview');

            if (empty($endpoint) || empty($apiKey)) {
                throw new \RuntimeException('Azure OpenAI not configured');
            }

            $url = "{$endpoint}/openai/deployments/{$deployId}/chat/completions?api-version={$apiVersion}";

            $httpResponse = Http::timeout(20)
                ->withHeaders(['api-key' => $apiKey, 'Content-Type' => 'application/json'])
                ->post($url, [
                    'messages'             => [
                        ['role' => 'system', 'content' => 'You are an expert resume writer. Return only valid JSON arrays. No markdown. No explanation.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_completion_tokens' => 500,
                    'temperature'          => 0.75,
                ]);

            if (!$httpResponse->successful()) {
                throw new \RuntimeException('Azure OpenAI error ' . $httpResponse->status());
            }

            $raw = $httpResponse->json('choices.0.message.content', '[]');
            $raw = preg_replace('/```(?:json)?\s*|\s*```/', '', trim((string) $raw));
            $suggestions = json_decode($raw, true);

            if (!is_array($suggestions) || empty($suggestions)) {
                throw new \RuntimeException('Unexpected AI response format');
            }

            return response()->json(['suggestions' => array_values(array_slice($suggestions, 0, 3))]);

        } catch (\Throwable $e) {
            Log::warning('ATS AI suggestion failed', [
                'resume_id' => $resume->id,
                'type'      => $type,
                'error'     => $e->getMessage(),
            ]);

            // Rule-based fallbacks so the UI never shows an empty state
            return response()->json(['suggestions' => $this->fallbackSuggestions($text, $type)]);
        }
    }

    /**
     * Re-run the analysis via form POST and redirect back to the report.
     */
    public function run(Request $request, Resume $resume)
    {
        $this->authorize('view', $resume);

        Cache::forget("ats_analysis_{$resume->id}");
        $analysis = $this->runAndCacheAnalysis($resume);

        if ($request->wantsJson()) {
            return response()->json($analysis);
        }

        return redirect()->route('resume.ats.show', $resume);
    }

    /**
     * Run ATS analysis and cache the result.
     */
    private function runAndCacheAnalysis(Resume $resume): array
    {
        $cacheKey = "ats_analysis_{$resume->id}";

        $cachedVersion = Cache::get("{$cacheKey}_version");
        $currentVersion = (string) $resume->updated_at?->timestamp;

        if ($cachedVersion !== $currentVersion || !Cache::has($cacheKey)) {
            $analysis = $this->ats->analyze($resume);

            $resume->update([
                'ats_score'    => $this->scoreToLevel($analysis['score']),
                'ats_analysis' => $analysis,
            ]);

            Cache::put($cacheKey, $analysis, now()->addHours(6));
            Cache::put("{$cacheKey}_version", $currentVersion, now()->addHours(6));

            return $analysis;
        }

        return Cache::get($cacheKey);
    }

    /**
     * Rule-based fallback suggestions when AI is unavailable.
     */
    private function fallbackSuggestions(string $text, string $type): array
    {
        if ($type === 'metric') {
            return [
                $text . ', improving efficiency by 30%',
                $text . ', resulting in 25% reduction in delivery time',
                $text . ', supporting a team of 5+ engineers',
            ];
        }
        if ($type === 'passive') {
            $verbs = ['Led', 'Built', 'Delivered', 'Implemented', 'Designed'];
            return array_map(fn($v) => $v . ' ' . lcfirst(preg_replace('/^(was|were|is|are)\s+/i', '', $text)), array_slice($verbs, 0, 3));
        }
        if ($type === 'format') {
            $short = rtrim(substr($text, 0, 60), " \t\n.…") . '…';
            return [
                'Led ' . lcfirst($short),
                'Delivered ' . lcfirst($short) . ', improving team productivity',
                'Implemented ' . lcfirst($short) . ' with measurable results',
            ];
        }
        return [$text];
    }

    private function scoreToLevel(int $score): string
    {
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'fair';
        return 'poor';
    }
}

