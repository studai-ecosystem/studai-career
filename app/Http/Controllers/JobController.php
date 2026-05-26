<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use App\Services\AI\JobMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

class JobController extends Controller
{
    protected $jobMatchingService;

    public function __construct(JobMatchingService $jobMatchingService)
    {
        $this->jobMatchingService = $jobMatchingService;
    }

    /**
     * Display job search page with filters
     */
    public function search(Request $request)
    {
        $query = Job::where('status', 'published')
            ->where('expires_at', '>', now());

        // Apply filters
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            // JOIN companies once rather than a correlated subquery via whereHas
            $query->leftJoin('companies', 'job_listings.company_id', '=', 'companies.id')
                  ->where(function ($q) use ($keyword) {
                      $q->where('job_listings.title', 'like', "%{$keyword}%")
                        ->orWhere('companies.name', 'like', "%{$keyword}%");
                  })
                  ->select('job_listings.*'); // prevent column ambiguity
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        if ($request->filled('job_type')) {
            $query->whereIn('employment_type', (array)$request->job_type);
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_max', '>=', $request->salary_min * 100000);
        }

        if ($request->filled('skills')) {
            $skills = array_map('trim', explode(',', $request->skills));
            // Use LIKE on the stored JSON string — more portable than whereJsonContains loops
            $query->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    if ($skill !== '') {
                        $q->orWhere('required_skills', 'like', "%{$skill}%");
                    }
                }
            });
        }

        // Work Mode
        $workModes = [];
        if ($request->filled('remote')) $workModes[] = 'remote';
        if ($request->filled('hybrid')) $workModes[] = 'hybrid';
        if ($request->filled('onsite')) $workModes[] = 'onsite';

        if (!empty($workModes)) {
            $query->where(function ($q) use ($workModes) {
                $q->whereIn('work_mode', $workModes)->orWhereIn('location_type', $workModes);
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            case 'relevant':
                // AI-based relevance (if user is authenticated)
                if (Auth::check()) {
                    $query->orderBy('created_at', 'desc'); // Placeholder
                }
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $jobs = $query
            ->with('company')
            ->paginate(20);

        // Get filter options — cached for 30 minutes to avoid full-table scan on each search
        $locations = \Illuminate\Support\Facades\Cache::remember('job_locations', 1800, function () {
            return Job::where('status', 'published')
                ->distinct()
                ->pluck('location')
                ->filter()
                ->values();
        });

        $experienceLevels = ['entry', 'mid', 'senior', 'lead'];
        $jobTypes = ['full-time', 'part-time', 'contract', 'internship'];

        // Compute per-job AI match scores for authenticated users
        $matchScores = [];
        if (Auth::check()) {
            $user = Auth::user();
            $profile = $user->profile;

            // Collect user skills from UserSkill table + Profile->skills
            $userSkillNames = $user->skills()
                ->pluck('skill_name')
                ->map(fn($s) => strtolower(trim($s)))
                ->toArray();

            if ($profile && !empty($profile->skills)) {
                $profileSkills = array_map(fn($s) => strtolower(trim(is_array($s) ? ($s['name'] ?? '') : $s)), (array)$profile->skills);
                $userSkillNames = array_unique(array_merge($userSkillNames, array_filter($profileSkills)));
            }

            // Build user keyword pool from headline, summary, experience titles
            $userKeywords = $userSkillNames;
            if ($profile) {
                foreach (array_filter([
                    $profile->headline ?? '',
                    $profile->summary ?? '',
                ]) as $text) {
                    $words = preg_split('/[\s,\/\-]+/', strtolower($text));
                    $userKeywords = array_merge($userKeywords, array_filter($words, fn($w) => strlen($w) > 3));
                }
                foreach ((array)($profile->experience ?? []) as $exp) {
                    $title = is_array($exp) ? ($exp['title'] ?? $exp['role'] ?? '') : '';
                    if ($title) {
                        $words = preg_split('/[\s,\/\-]+/', strtolower($title));
                        $userKeywords = array_merge($userKeywords, array_filter($words, fn($w) => strlen($w) > 3));
                    }
                }
                $userKeywords = array_unique($userKeywords);
            }

            $hasProfileData = !empty($userSkillNames) || ($profile && ($profile->headline || $profile->summary));

            // Determine user experience level from profile experience array count
            $userExpLevel = 'entry';
            if ($profile && !empty($profile->experience)) {
                $expCount = count((array)$profile->experience);
                if ($expCount >= 6) $userExpLevel = 'lead';
                elseif ($expCount >= 3) $userExpLevel = 'senior';
                elseif ($expCount >= 1) $userExpLevel = 'mid';
            }

            $expMap = ['entry' => 0, 'mid' => 1, 'senior' => 2, 'lead' => 3];
            $userExpNum = $expMap[$userExpLevel] ?? 0;

            foreach ($jobs as $job) {
                // No data → skip (show nothing rather than a fake score)
                if (!$hasProfileData) {
                    continue;
                }

                // ── 1. Skill match (0–50 pts) ──────────────────────────────
                $jobSkills = [];
                if (!empty($job->required_skills)) {
                    $raw = is_array($job->required_skills) ? $job->required_skills : (json_decode($job->required_skills, true) ?? []);
                    $jobSkills = array_values(array_filter(array_map(
                        fn($s) => strtolower(trim(is_array($s) ? ($s['name'] ?? '') : (string)$s)), $raw
                    )));
                }

                $skillScore = 0;
                if (!empty($jobSkills)) {
                    if (!empty($userSkillNames)) {
                        $matched = 0;
                        foreach ($jobSkills as $js) {
                            foreach ($userSkillNames as $us) {
                                if ($us === $js || str_contains($us, $js) || str_contains($js, $us)) {
                                    $matched++;
                                    break;
                                }
                            }
                        }
                        $skillScore = (int) round(($matched / count($jobSkills)) * 50);
                    }
                    // Bonus: keyword match from headline/experience (partial, up to 20 pts)
                    $titleWords = preg_split('/[\s,\/\-]+/', strtolower($job->title ?? ''));
                    $kwMatched  = 0;
                    foreach (array_filter($titleWords, fn($w) => strlen($w) > 3) as $tw) {
                        foreach ($userKeywords as $uk) {
                            if ($uk === $tw || str_contains($uk, $tw) || str_contains($tw, $uk)) {
                                $kwMatched++;
                                break;
                            }
                        }
                    }
                    $titleScore = $titleWords ? min(20, (int) round(($kwMatched / max(1, count(array_filter($titleWords, fn($w) => strlen($w) > 3)))) * 20)) : 0;
                } else {
                    // No required skills listed — neutral
                    $skillScore  = 30;
                    $titleScore  = 0;
                }

                // ── 2. Experience level match (0–15 pts) ───────────────────
                $jobExpNum  = $expMap[$job->experience_level ?? ''] ?? 0;
                $expDiff    = abs($userExpNum - $jobExpNum);
                $expScore   = max(0, 15 - ($expDiff * 6));

                // ── 3. Salary overlap (0–10 pts) ───────────────────────────
                $salaryScore = 0;
                if ($profile && $profile->expected_salary_min && $job->salary_max) {
                    if ($profile->expected_salary_min <= $job->salary_max &&
                        ($profile->expected_salary_max ?? PHP_INT_MAX) >= $job->salary_min) {
                        $salaryScore = 10;
                    } elseif ($profile->expected_salary_min <= $job->salary_max * 1.2) {
                        $salaryScore = 5;
                    }
                }

                // ── 4. Location match (0–5 pts) ────────────────────────────
                $locationScore = 0;
                if ($profile && $profile->current_location && $job->location) {
                    if (str_contains(strtolower($job->location), strtolower($profile->current_location)) ||
                        str_contains(strtolower($profile->current_location), strtolower($job->location))) {
                        $locationScore = 5;
                    }
                }

                $total = $skillScore + ($titleScore ?? 0) + $expScore + $salaryScore + $locationScore;
                // Scale to 0–100, natural range is 0–100 (50+20+15+10+5)
                $matchScores[$job->id] = min(97, max(38, $total));
            }
        }

        return view('jobs.search', compact(
            'jobs',
            'locations',
            'experienceLevels',
            'jobTypes',
            'matchScores'
        ));
    }

    /**
     * Display job details
     */
    public function show($id)
    {
        $job = Job::with(['company', 'hiringRounds' => function ($q) {
            $q->orderBy('round_order');
        }])->findOrFail($id);
        
        // Check if user has already applied
        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = Application::where('user_id', Auth::id())
                ->where('job_id', $job->id)
                ->exists();
        }

        // Get similar jobs
        $similarJobs = Cache::remember(
            "similar_jobs_{$job->id}",
            3600,
            function () use ($job) {
                return Job::where('status', 'published')
                    ->where('expires_at', '>', now())
                    ->where('id', '!=', $job->id)
                    ->where(function ($query) use ($job) {
                        $query->where('location', $job->location)
                              ->orWhere('employment_type', $job->employment_type);
                    })
                    ->with('company')
                    ->take(4)
                    ->get();
            }
        );

        // Fetch authenticated user's saved resumes for the apply modal
        $savedResumes = collect();
        if (Auth::check()) {
            $savedResumes = \App\Models\Resume::where('user_id', Auth::id())
                ->select('id', 'title', 'full_name', 'updated_at')
                ->latest()
                ->get();
        }

        // Load existing test attempts for this user (keyed by hiring_round_id)
        $myAttempts = collect();
        if (Auth::check() && $job->hiringRounds->isNotEmpty()) {
            $myAttempts = \App\Models\RoundAttempt::where('user_id', Auth::id())
                ->whereIn('hiring_round_id', $job->hiringRounds->pluck('id'))
                ->get()
                ->keyBy('hiring_round_id');
        }

        return view('jobs.show', compact('job', 'hasApplied', 'similarJobs', 'savedResumes', 'myAttempts'));
    }

    /**
     * Save/unsave a job
     */
    public function toggleSave(Request $request, $id)
    {
        $user = $request->user();
        $job = Job::findOrFail($id);

        if ($user->savedJobs()->where('job_id', $id)->exists()) {
            $user->savedJobs()->detach($id);
            return response()->json(['saved' => false, 'message' => 'Job removed from saved jobs']);
        } else {
            $user->savedJobs()->attach($id);
            return response()->json(['saved' => true, 'message' => 'Job saved successfully']);
        }
    }

    /**
     * Display saved jobs
     */
    public function saved(Request $request)
    {
        $user = $request->user();
        $jobs = $user->savedJobs()
            ->where('status', 'published')
            ->where('expires_at', '>', now())
            ->withPivot('notes', 'created_at')
            ->with('company')
            ->orderBy('saved_jobs.created_at', 'desc')
            ->paginate(20);

        return view('jobs.saved', compact('jobs'));
    }

    /**
     * Apply to a job
     */
    public function apply(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Please login to apply'], 401);
            }
            
            $job = Job::find($id);
            
            if (!$job) {
                return response()->json(['success' => false, 'error' => 'Job not found'], 404);
            }

            // Check if already applied
            if (Application::where('user_id', $user->id)->where('job_id', $id)->exists()) {
                return response()->json(['success' => false, 'error' => 'You have already applied to this job'], 400);
            }

            // Check application limit (allow if no subscription - free tier with 5 apps/month)
            $subscription = $user->subscription;
            if ($subscription && !$user->canApplyToJobs()) {
                return response()->json(['success' => false, 'error' => 'You have reached your monthly application limit. Please upgrade your plan.'], 403);
            }

            $request->validate([
                'cover_letter'    => 'nullable|string|max:2000',
                'resume_file'     => 'nullable|string',
                'saved_resume_id' => 'nullable|integer|exists:resumes,id',
                'resume'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            // Resolve resume path: uploaded file > saved resume ID > profile resume
            $resumeFile = null;
            if ($request->hasFile('resume')) {
                $resumeFile = $request->file('resume')->store('resumes/applications', 'public');
            } elseif ($request->filled('saved_resume_id')) {
                $savedResume = \App\Models\Resume::where('id', $request->saved_resume_id)
                    ->where('user_id', $user->id)
                    ->first();
                if ($savedResume) {
                    $resumeFile = $savedResume->pdf_path ?: 'resume:' . $savedResume->id;
                }
            } elseif ($request->filled('resume_file')) {
                $resumeFile = $request->resume_file;
            } elseif ($user->profile) {
                $resumeFile = $user->profile->resume_path ?? null;
            }

            // Create application
            $application = Application::create([
                'user_id'            => $user->id,
                'job_id'             => $job->id,
                'application_number' => 'APP-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'cover_letter'       => $request->cover_letter,
                'resume_file'        => $resumeFile,
                'status'             => 'pending',
                'submitted_at'       => now(),
                'is_archived'        => false,
                'source'             => 'manual',
            ]);

            // Increment usage counter if subscription exists
            if ($subscription) {
                $subscription->increment('applications_used_this_month');
            }

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'application_id' => $application->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Job application error: ' . $e->getMessage(), [
                'job_id' => $id,
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false, 
                'error' => 'Failed to submit application. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate AI cover letter for job application
     */
    public function generateCoverLetter(Request $request)
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'job_title' => 'required|string',
                'company_name' => 'required|string',
                'job_description' => 'nullable|string',
            ]);

            $userName = $user->name;
            $userProfile = $user->profile;
            
            // Build user context
            $userSkills = '';
            $userExperience = '';
            
            if ($userProfile) {
                if ($userProfile->skills) {
                    $skills = is_array($userProfile->skills) ? $userProfile->skills : json_decode($userProfile->skills, true);
                    $userSkills = $skills ? implode(', ', array_slice($skills, 0, 10)) : '';
                }
                if ($userProfile->experience_years) {
                    $userExperience = $userProfile->experience_years . ' years of experience';
                }
                if ($userProfile->professional_summary) {
                    $userExperience .= '. ' . substr($userProfile->professional_summary, 0, 200);
                }
            }

            $prompt = "Write a professional cover letter for a job application with the following details:

Job Title: {$request->job_title}
Company: {$request->company_name}
Applicant Name: {$userName}
Applicant Skills: {$userSkills}
Applicant Background: {$userExperience}

Job Description Summary: " . substr($request->job_description ?? '', 0, 500) . "

Requirements:
1. Keep it concise (250-350 words)
2. Be professional but personable
3. Highlight relevant skills for the role
4. Show enthusiasm for the company
5. Include a strong opening and closing
6. Do NOT use placeholder text like [Your Name] - use the actual applicant name provided
7. Format with proper paragraphs

Write only the cover letter body, starting with 'Dear Hiring Manager,' and ending with the applicant's name.";

            // Try to use OpenAI
            try {
                $response = OpenAI::chat()->create([
                    'model' => config('ai.default_model', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional career advisor helping job seekers write compelling cover letters. Write natural, human-sounding letters that are tailored to the specific job and company.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_completion_tokens' => 800,
                    'temperature' => 0.7,
                ]);

                $coverLetter = $response->choices[0]->message->content;

                // Deduct 1 AI credit and log usage
                $user->deductAICredits(1, 'cover_letter',
                    "AI Cover Letter for {$request->job_title} at {$request->company_name}",
                    ['job_title' => $request->job_title, 'company' => $request->company_name]
                );

                return response()->json([
                    'success' => true,
                    'cover_letter' => trim($coverLetter),
                ]);
            } catch (\Exception $aiError) {
                \Log::warning('AI cover letter generation failed, using template', [
                    'error' => $aiError->getMessage()
                ]);
                
                // Fallback to a template
                $coverLetter = "Dear Hiring Manager,

I am writing to express my strong interest in the {$request->job_title} position at {$request->company_name}. With my background" . ($userSkills ? " in {$userSkills}" : "") . ($userExperience ? " and {$userExperience}" : "") . ", I am confident that I would be a valuable addition to your team.

I am particularly drawn to this opportunity because of {$request->company_name}'s reputation in the industry and the exciting challenges this role presents. I believe my skills and experience align well with your requirements, and I am eager to contribute to your team's continued success.

Throughout my career, I have developed strong problem-solving abilities and a commitment to delivering high-quality work. I am a quick learner who thrives in collaborative environments and consistently seeks opportunities to grow and take on new challenges.

I would welcome the opportunity to discuss how my background and skills can benefit {$request->company_name}. Thank you for considering my application. I look forward to the possibility of contributing to your team.

Best regards,
{$userName}";

                // Still deduct 1 credit for the feature usage (template fallback)
                $user->deductAICredits(1, 'cover_letter',
                    "AI Cover Letter (template) for {$request->job_title} at {$request->company_name}",
                    ['job_title' => $request->job_title, 'company' => $request->company_name, 'fallback' => true]
                );

                return response()->json([
                    'success' => true,
                    'cover_letter' => $coverLetter,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Cover letter generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate cover letter'
            ], 500);
        }
    }
}
