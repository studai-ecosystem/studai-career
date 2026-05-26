<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\HiringRound;
use App\Models\Job;
use App\Services\AI\AIService;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobPostingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employer']);
    }

    public function index(Request $request)
    {
        $company = auth()->user()->company;
        
        $query = Job::where('company_id', $company->id)
            ->withCount('applications');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by expiry
        if ($request->filled('expiry')) {
            if ($request->expiry === 'active') {
                $query->where('expires_at', '>', now());
            } elseif ($request->expiry === 'expired') {
                $query->where('expires_at', '<=', now());
            }
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $jobs = $query->latest()->paginate(20);

        // Get counts for filter badges — single GROUP BY query instead of 4
        $rawStatusCounts = Job::where('company_id', $company->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusCounts = [
            'all'       => (int) $rawStatusCounts->sum(),
            'published' => (int) ($rawStatusCounts['published'] ?? 0),
            'draft'     => (int) ($rawStatusCounts['draft']     ?? 0),
            'closed'    => (int) ($rawStatusCounts['closed']    ?? 0),
        ];

        return view('employer.jobs.index', compact('jobs', 'statusCounts'));
    }

    public function create()
    {
        return view('employer.jobs.create');
    }

    /**
     * AI-generate job content from just the job title.
     */
    public function generateAIContent(Request $request)
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'experience_level' => ['nullable', 'string'],
            'job_type'         => ['nullable', 'string'],
        ]);

        $title           = $request->input('title');
        $experienceLevel = $request->input('experience_level', 'mid');
        $jobType         = $request->input('job_type', 'full-time');
        $company         = auth()->user()->company;
        $companyName     = $company?->name ?? 'our company';
        $industry        = $company?->industry ?? 'technology';

        $prompt = <<<PROMPT
You are an expert HR recruiter. Generate a complete, professional job posting for the following role.

Job Title: {$title}
Experience Level: {$experienceLevel}
Job Type: {$jobType}
Company: {$companyName}
Industry: {$industry}

Return a valid JSON object with EXACTLY these keys (no markdown, no code fences, pure JSON only):
{
  "description": "3-4 paragraph engaging job overview and what makes this role exciting",
  "responsibilities": "8-10 bullet points as a plain text list, each on a new line starting with • ",
  "qualifications": "6-8 bullet points as a plain text list, each on a new line starting with • ",
  "required_skills": ["skill1", "skill2", "skill3", "skill4", "skill5", "skill6", "skill7", "skill8"],
  "salary_min": 800000,
  "salary_max": 1500000,
  "salary_note": "e.g. Rs.8-15 LPA depending on experience"
}

Salary should be realistic in Indian Rupees (annual) for the role and experience level.
PROMPT;

        try {
            $raw = $this->callOpenAI($prompt);

            // Strip markdown code fences if present
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);

            $data = json_decode($cleaned, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['description'])) {
                throw new \Exception('AI returned invalid JSON: ' . substr($cleaned, 0, 200));
            }

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('AI job generation failed', ['error' => $e->getMessage(), 'title' => $title]);
            return response()->json([
                'success' => false,
                'message' => 'AI generation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Direct OpenAI API call — uses standard OpenAI key if set, else Azure OpenAI Responses API.
     */
    private function callOpenAI(string $prompt): string
    {
        $openaiKey = env('OPENAI_API_KEY');

        if ($openaiKey && str_starts_with($openaiKey, 'sk-')) {
            // Standard OpenAI chat completions
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'messages'    => [['role' => 'user', 'content' => $prompt]],
                    'max_completion_tokens' => 2000,
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI error ' . $response->status() . ': ' . substr($response->body(), 0, 300));
            }

            return $response->json('choices.0.message.content') ?? '';
        }

        // Azure OpenAI — Chat Completions API
        $azureKey   = config('ai.azure.api_key');
        $deployment = config('ai.azure.deployment_id', 'gpt-5.4');
        $apiVersion = config('ai.azure.api_version', '2025-04-01-preview');
        $endpoint   = rtrim(config('ai.azure.endpoint', 'https://studai-openai-2049701603.openai.azure.com'), '/');

        if (empty($azureKey)) {
            throw new \Exception('No AI credentials configured. Set OPENAI_API_KEY or AZURE_OPENAI_API_KEY in .env');
        }

        $url = "{$endpoint}/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

        $response = \Illuminate\Support\Facades\Http::timeout(60)
            ->withHeaders([
                'api-key'      => $azureKey,
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'messages'   => [
                    ['role' => 'system', 'content' => 'You are an expert HR recruiter. Return ONLY valid JSON with no markdown code blocks.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_completion_tokens' => 2000,
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Azure OpenAI error ' . $response->status() . ': ' . substr($response->body(), 0, 300));
        }

        return $response->json('choices.0.message.content') ?? '';
    }

    /**
     * AI-suggest hiring rounds based on job title and type.
     */
    public function generateRounds(Request $request)
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'experience_level' => ['nullable', 'string'],
        ]);

        $title  = $request->input('title');
        $level  = $request->input('experience_level', 'mid');
        $prompt = <<<PROMPT
You are an expert HR recruiter. Based on the job title "{$title}" at {$level} level, suggest which hiring rounds are appropriate.
Available round types: info_test, aptitude, technical, practical, hr_interview.
- info_test: Always include — basic company knowledge questions.
- aptitude: Include for roles requiring analytical thinking (finance, data, engineering, etc.).
- technical: Include ONLY for clearly technical roles (developer, engineer, data scientist, DevOps, etc.).
- practical: Include for non-technical or semi-technical roles (HR, marketing, sales, operations, design, etc.).
- hr_interview: Always include as the final round.
Return ONLY valid JSON: { "suggested": ["info_test", "aptitude", "technical", "hr_interview"], "rationale": "short reason" }
PROMPT;

        try {
            $raw     = $this->callOpenAI($prompt);
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
            $data    = json_decode($cleaned, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['suggested'])) {
                throw new \Exception('Invalid AI response');
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('AI round suggestion failed', ['error' => $e->getMessage()]);
            // Fallback: always suggest info_test and hr_interview
            return response()->json(['success' => true, 'data' => [
                'suggested'  => ['info_test', 'hr_interview'],
                'rationale'  => 'Default selection',
            ]]);
        }
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['required', 'string'],
            'responsibilities'   => ['nullable', 'string'],
            'qualifications'     => ['nullable', 'string'],
            'location'           => ['required', 'string', 'max:255'],
            'job_type'           => ['required', 'in:full-time,part-time,contract,internship,remote'],
            'experience_level'   => ['required', 'in:entry,mid,senior,lead'],
            'salary_min'         => ['nullable', 'integer', 'min:0'],
            'salary_max'         => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'required_skills'    => ['nullable', 'array'],
            'required_skills.*'  => ['string', 'max:100'],
            'benefits'           => ['nullable', 'array'],
            'benefits.*'         => ['string', 'max:255'],
            'expires_at'         => ['required', 'date', 'after:today'],
            'status'             => ['required', 'in:draft,published'],
            // Application dates
            'open_date'          => ['required', 'date', 'before_or_equal:close_date'],
            'close_date'         => ['required', 'date', 'after:open_date'],
            // Hiring rounds
            'rounds'             => ['nullable', 'array'],
            'rounds.*.type'      => ['required_with:rounds', 'string'],
            'rounds.*.name'      => ['required_with:rounds', 'string'],
            'rounds.*.test_date' => ['required_with:rounds', 'date'],
            'rounds.*.eval_days' => ['required_with:rounds', 'integer', 'in:5,10'],
        ]);

        $validated['employment_type'] = $validated['job_type'];
        unset($validated['job_type']);

        $validated['company_id']   = $company->id;
        $validated['company_name'] = $company->name ?? null;
        $validated['slug']         = Str::slug($validated['title']) . '-' . Str::random(8);

        $rounds = $validated['rounds'] ?? [];
        unset($validated['rounds']);

        $job = Job::create($validated);

        // Save hiring rounds
        foreach ($rounds as $index => $round) {
            $testDate  = Carbon::parse($round['test_date']);
            $evalDays  = (int) $round['eval_days'];
            HiringRound::create([
                'job_id'          => $job->id,
                'name'            => $round['name'],
                'type'            => $round['type'],
                'round_order'     => $index + 1,
                'test_date'       => $testDate->toDateString(),
                'evaluation_days' => $evalDays,
                'evaluation_date' => $testDate->copy()->addDays($evalDays)->toDateString(),
                'status'          => 'pending',
            ]);
        }

        return redirect()
            ->route('employer.jobs.show', $job->id)
            ->with('success', 'Job posted successfully!');
    }

    public function show($id)
    {
        $company = auth()->user()->company;
        
        $job = Job::where('company_id', $company->id)
            ->with(['hiringRounds' => fn($q) => $q->orderBy('round_order')])
            ->withCount([
                'applications',
                'applications as pending_count' => fn($q) => $q->where('status', 'pending'),
                'applications as reviewing_count' => fn($q) => $q->where('status', 'reviewing'),
                'applications as shortlisted_count' => fn($q) => $q->where('status', 'shortlisted'),
            ])
            ->findOrFail($id);

        return view('employer.jobs.show', compact('job'));
    }

    public function edit($id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        return view('employer.jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'in:full-time,part-time,contract,internship,remote'],
            'experience_level' => ['required', 'in:entry,mid,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:100'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:255'],
            'expires_at' => ['required', 'date', 'after:today'],
            'status' => ['required', 'in:draft,published,closed'],
        ]);

        $validated['employment_type'] = $validated['job_type'];
        unset($validated['job_type']);

        $job->update($validated);

        CacheService::onJobChanged($company->id);

        return redirect()
            ->route('employer.jobs.show', $job->id)
            ->with('success', 'Job updated successfully!');
    }

    public function destroy($id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        // Don't allow deletion if there are applications
        if ($job->applications()->count() > 0) {
            return back()->with('error', 'Cannot delete job with existing applications. Close it instead.');
        }

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function close($id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $job->update(['status' => 'closed']);
        CacheService::onJobChanged($company->id);

        return back()->with('success', 'Job closed successfully!');
    }

    public function reopen($id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        // Check if job is expired
        if ($job->expires_at < now()) {
            return back()->with('error', 'Cannot reopen expired job. Please update the expiry date first.');
        }

        $job->update(['status' => 'published']);
        CacheService::onJobChanged($company->id);

        return back()->with('success', 'Job reopened successfully!');
    }

    public function duplicate($id)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $newJob = $job->replicate();
        $newJob->status = 'draft';
        $newJob->title = $job->title . ' (Copy)';
        $newJob->expires_at = now()->addDays(30);
        $newJob->save();

        return redirect()
            ->route('employer.jobs.edit', $newJob->id)
            ->with('success', 'Job duplicated successfully!');
    }
}
