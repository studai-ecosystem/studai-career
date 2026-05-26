<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CompanyIntelligenceProfile;
use App\Models\Job;
use App\Services\AI\OrinJobCreatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Orin™ AI Job Creator
 *
 * Employer enters role name + brief description.
 * Orin™ conducts a conversational interview to gather all remaining job details,
 * then auto-generates a full JD, application form, and shareable link.
 */
class OrinJobCreatorController extends Controller
{
    public function __construct(private OrinJobCreatorService $orin)
    {
        $this->middleware(['auth', 'employer']);
    }

    /**
     * Show the AI Job Creator interface.
     */
    public function show(): View
    {
        $company = auth()->user()->company;
        $profile = $company
            ? CompanyIntelligenceProfile::where('company_id', $company->id)->first()
            : null;

        return view('employer.job-creator', compact('company', 'profile'));
    }

    /**
     * API: Drive the conversational job creation interview.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'role_name'        => ['required', 'string', 'max:255'],
            'role_description' => ['nullable', 'string', 'max:2000'],
            'history'          => ['required', 'array'],
        ]);

        $company = auth()->user()->company;
        if (! $company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        $profile = CompanyIntelligenceProfile::where('company_id', $company->id)->first();
        $companyProfile = $profile ? [
            'name'                 => $company->name,
            'industry'             => $profile->industry,
            'size'                 => $profile->company_size,
            'work_culture'         => $profile->work_culture,
            'work_mode_preference' => $profile->work_mode_preference,
            'compensation_philosophy' => $profile->compensation_philosophy,
            'salary_bands'         => $profile->salary_bands,
            'top_performer_traits' => $profile->top_performer_traits,
        ] : ['name' => $company->name];

        $roleName        = $request->input('role_name');
        $roleDescription = $request->input('role_description') ?? '';
        $history         = $request->input('history');

        // Check if conversation is complete (Orin signals READY or user typed GENERATE)
        $lastUserMsg = collect($history)->filter(fn($m) => $m['role'] === 'user')->last()['content'] ?? '';
        $lastAiMsg   = collect($history)->filter(fn($m) => $m['role'] === 'assistant')->last()['content'] ?? '';

        $isComplete = str_contains(strtoupper($lastAiMsg), 'READY TO GENERATE')
            || str_contains(strtolower($lastUserMsg), 'generate')
            || str_contains(strtoupper($lastUserMsg), 'DONE');

        if ($isComplete && count($history) >= 6) {
            // Extract data and create job
            $jobData = $this->orin->extractJobData($history, $roleName, $roleDescription);
            $job = $this->orin->createJob(auth()->user(), $roleName, $roleDescription, $jobData, $companyProfile);

            $applyUrl = url('/apply/' . $job->application_link_token);

            return response()->json([
                'complete'  => true,
                'message'   => "🎉 Job created! Here's your shareable application link:\n{$applyUrl}\n\nShare it on LinkedIn, WhatsApp, or anywhere. Orin™ will manage the rest — applications, evaluations, and ranking.",
                'job_id'    => $job->id,
                'apply_url' => $applyUrl,
                'job_title' => $job->title,
            ]);
        }

        // Continue conversation
        $reply = $this->orin->nextQuestion($history, $companyProfile, $roleName, $roleDescription);

        // Detect if Orin is signalling completion in the reply
        $readyToGenerate = str_contains(strtoupper($reply), 'READY TO GENERATE')
            || (count($history) >= 14 && str_contains(strtolower($reply), 'generate'));

        return response()->json([
            'complete'         => false,
            'message'          => $reply,
            'ready_to_generate' => $readyToGenerate,
        ]);
    }

    /**
     * Quick Post: immediately generate & publish a job from just role name + description.
     * No conversational back-and-forth — AI fills in smart defaults.
     */
    public function quickPost(Request $request): JsonResponse
    {
        $request->validate([
            'role_name'        => ['required', 'string', 'max:255'],
            'role_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $company = auth()->user()->company;
        if (! $company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        $profile = CompanyIntelligenceProfile::where('company_id', $company->id)->first();
        $companyProfile = $profile ? [
            'name'                    => $company->name,
            'industry'                => $profile->industry,
            'size'                    => $profile->company_size,
            'work_culture'            => $profile->work_culture,
            'work_mode_preference'    => $profile->work_mode_preference,
            'compensation_philosophy' => $profile->compensation_philosophy,
            'salary_bands'            => $profile->salary_bands,
            'top_performer_traits'    => $profile->top_performer_traits,
        ] : ['name' => $company->name];

        $roleName        = $request->input('role_name');
        $roleDescription = $request->input('role_description') ?? '';

        try {
            // Use smart defaults — AI will infer from role name + company profile
            $jobData = [];
            $job = $this->orin->createJob(auth()->user(), $roleName, $roleDescription, $jobData, $companyProfile);

            $applyUrl = url('/apply/' . $job->application_link_token);

            return response()->json([
                'success'   => true,
                'message'   => "Job posted successfully!",
                'job_id'    => $job->id,
                'apply_url' => $applyUrl,
                'job_title' => $job->title,
            ]);
        } catch (\Exception $e) {
            \Log::error('OrinJobCreator::quickPost failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create job. Please try again.'], 500);
        }
    }

    /**
     * Show employer's job listings with Orin-generated apply links.
     */
    public function myJobs(): JsonResponse
    {
        $company = auth()->user()->company;
        if (! $company) {
            return response()->json(['jobs' => []]);
        }

        $jobs = Job::where('company_id', $company->id)
            ->whereNotNull('application_link_token')
            ->latest()
            ->get(['id', 'title', 'application_phase', 'application_link_token', 'close_date', 'applications_count', 'created_at']);

        return response()->json([
            'jobs' => $jobs->map(fn($j) => [
                'id'           => $j->id,
                'title'        => $j->title,
                'phase'        => $j->application_phase ?? 'open',
                'apply_url'    => url('/apply/' . $j->application_link_token),
                'close_date'   => $j->close_date?->format('d M Y'),
                'applicants'   => $j->applications_count ?? 0,
                'created_at'   => $j->created_at->diffForHumans(),
            ]),
        ]);
    }
}
