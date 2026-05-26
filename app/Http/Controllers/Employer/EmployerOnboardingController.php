<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerOnboardingController extends Controller
{
    public function __construct(private readonly AIService $ai)
    {
        $this->middleware(['auth', 'employer']);
    }

    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        // If company already has full description, skip onboarding
        if ($user->company && filled($user->company->description)) {
            return redirect()->route('employer.home');
        }

        return view('employer.onboarding');
    }

    public function save(Request $request): RedirectResponse
    {
        $user    = auth()->user();
        $company = $user->company;

        if (! $company) {
            return redirect()->route('employer.onboarding');
        }

        $request->validate([
            'headquarters'    => ['nullable', 'string', 'max:255'],
            'founded_year'    => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'description'     => ['nullable', 'string', 'max:5000'],
            'mission'         => ['nullable', 'string', 'max:2000'],
            'culture_values'  => ['nullable', 'string'],
            'work_style'      => ['nullable', 'string', 'max:100'],
            'team_vibe'       => ['nullable', 'string', 'max:100'],
            'hiring_priorities' => ['nullable', 'string'],
            'roles_hiring_for' => ['nullable', 'string', 'max:1000'],
            'open_to_remote'  => ['nullable'],
            'top_perks'       => ['nullable', 'string'],
        ]);

        // Parse JSON arrays from hidden inputs
        $cultureValues    = $this->parseJsonArray($request->input('culture_values'));
        $hiringPriorities = $this->parseJsonArray($request->input('hiring_priorities'));
        $topPerks         = $this->parseJsonArray($request->input('top_perks'));

        // Build metadata blob to store in benefits column (already json-cast)
        $dnaData = [
            'work_style'        => $request->input('work_style'),
            'team_vibe'         => $request->input('team_vibe'),
            'culture_values'    => $cultureValues,
            'hiring_priorities' => $hiringPriorities,
            'roles_hiring_for'  => $request->input('roles_hiring_for'),
            'open_to_remote'    => $request->boolean('open_to_remote'),
            'top_perks'         => $topPerks,
            'mission'           => $request->input('mission'),
            'onboarding_complete' => true,
        ];

        $company->update([
            'headquarters' => $request->input('headquarters') ?? $company->headquarters,
            'founded_year' => $request->input('founded_year') ?? $company->founded_year,
            'description'  => $request->input('description') ?? $company->description,
            'benefits'     => $dnaData,
        ]);

        return redirect()->route('employer.home')
            ->with('success', 'Your Corporate DNA profile is set up. S.C.O.U.T™ is now calibrating your hiring intelligence!');
    }

    public function aiSuggest(Request $request): JsonResponse
    {
        $request->validate([
            'field'      => ['required', 'in:description,mission'],
            'company_name' => ['required', 'string', 'max:255'],
            'industry'   => ['nullable', 'string', 'max:100'],
            'headquarters' => ['nullable', 'string', 'max:255'],
            'founded_year' => ['nullable', 'integer'],
        ]);

        $field       = $request->input('field');
        $companyName = $request->input('company_name');
        $industry    = $request->input('industry', 'technology');
        $hq          = $request->input('headquarters', '');
        $year        = $request->input('founded_year', '');

        if ($field === 'description') {
            $prompt = <<<PROMPT
Generate 3 distinct, compelling company description options for "{$companyName}".
Industry: {$industry}
Location: {$hq}
Founded: {$year}

Each description should be 2–3 sentences, professional, highlight what the company does, the problem it solves, and what makes it different.
Vary the tone: (1) bold & innovative, (2) people-first & warm, (3) results-driven & credible.

Return ONLY a JSON array of 3 strings, no markdown:
["description 1", "description 2", "description 3"]
PROMPT;
        } else {
            $prompt = <<<PROMPT
Generate 3 distinct mission/vision statement options for "{$companyName}".
Industry: {$industry}
Location: {$hq}

Each should be 1 concise, inspiring sentence that captures purpose and ambition.
Vary the style: (1) aspirational & visionary, (2) customer-centric, (3) impact-driven.

Return ONLY a JSON array of 3 strings, no markdown:
["mission 1", "mission 2", "mission 3"]
PROMPT;
        }

        try {
            $raw  = $this->ai->generateText($prompt, 'You are a professional brand copywriter. Return only valid JSON.', [
                'max_tokens' => 600,
                'temperature' => 0.8,
                'skip_cache' => true,
            ]);
            // Strip markdown code fences if present
            $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
            $clean = preg_replace('/\s*```$/i', '', $clean);
            $suggestions = json_decode($clean, true);

            if (!is_array($suggestions) || count($suggestions) < 1) {
                throw new \Exception('Invalid AI response format');
            }

            return response()->json(['suggestions' => array_values($suggestions)]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not generate suggestions. Please try again.'], 422);
        }
    }

    private function parseJsonArray(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
