<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\GenerateCandidateQuestions;
use App\Models\Application;
use App\Models\EvaluationSession;
use App\Models\Job;
use App\Services\AI\OrinEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Handles the public application link flow: /apply/{token}
 * No authentication required. Supports guest applicants.
 */
class PublicApplyController extends Controller
{
    public function __construct(private OrinEvaluationService $evaluationService)
    {
    }

    /**
     * Show the application landing page.
     * Phase-aware: shows apply / closed / evaluating / results based on dates.
     */
    public function show(string $token): View
    {
        $job = Job::where('application_link_token', $token)
            ->with('company')
            ->firstOrFail();

        $phase = $this->resolvePhase($job);

        // If authenticated and has existing application, pass it
        $existingApplication = null;
        $savedResumes        = collect();
        if (auth()->check()) {
            $existingApplication = Application::where('job_id', $job->id)
                ->where('user_id', auth()->id())
                ->first();
            $savedResumes = \App\Models\Resume::where('user_id', auth()->id())
                ->select('id', 'title', 'full_name', 'updated_at')
                ->latest()
                ->get();
        }

        return view('apply.show', compact('job', 'phase', 'token', 'existingApplication', 'savedResumes'));
    }

    /**
     * Submit a new application (authenticated or guest).
     */
    public function submit(Request $request, string $token): JsonResponse
    {
        $job = Job::where('application_link_token', $token)->firstOrFail();

        // Validate phase
        if (! in_array($job->application_phase, ['open'])) {
            return response()->json(['error' => 'Applications are no longer being accepted for this position.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'full_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['required', 'string', 'max:20'],
            'linkedin_url'    => ['nullable', 'url'],
            'portfolio_url'   => ['nullable', 'url'],
            'github_url'      => ['nullable', 'url'],
            'cover_letter'    => ['nullable', 'string', 'max:3000'],
            'resume'          => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'saved_resume_id' => ['nullable', 'integer', 'exists:resumes,id'],
            'screening_answers' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Must provide either a file upload or a saved resume
        $hasFile  = $request->hasFile('resume');
        $hasSaved = (bool) $request->input('saved_resume_id');
        if (! $hasFile && ! $hasSaved) {
            return response()->json(['errors' => ['resume' => ['Please upload a resume or select one of your saved resumes.']]], 422);
        }

        // Check duplicate submission
        $existingQuery = Application::where('job_id', $job->id);
        if (auth()->check()) {
            $existingQuery->where('user_id', auth()->id());
        } else {
            $existingQuery->where('guest_email', $request->email);
        }

        if ($existingQuery->exists()) {
            return response()->json(['error' => 'You have already applied for this position.'], 422);
        }

        // Handle resume — uploaded file takes priority, then saved resume
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes/applications', 'public');
        } elseif ($request->input('saved_resume_id') && auth()->check()) {
            $savedResume = \App\Models\Resume::where('id', $request->input('saved_resume_id'))
                ->where('user_id', auth()->id())
                ->first();
            if ($savedResume) {
                // Use the PDF export path if available, else store a reference string
                $resumePath = $savedResume->pdf_path ?: 'resume:' . $savedResume->id;
            }
        }
        $appNumber   = 'APP-' . strtoupper(Str::random(8));
        $accessToken = Str::random(40);

        try {
            $application = Application::create([
                'job_id'              => $job->id,
                'user_id'             => auth()->id(),
                'application_number'  => $appNumber,
                'status'              => 'pending',
                'evaluation_status'   => 'pending',
                'cover_letter'        => $request->cover_letter,
                'resume_file'         => $resumePath,
                'is_guest_applicant'  => ! auth()->check(),
                'guest_name'          => ! auth()->check() ? $request->full_name : null,
                'guest_email'         => ! auth()->check() ? $request->email : null,
                'guest_phone'         => ! auth()->check() ? $request->phone : null,
                'portfolio_url'       => $request->portfolio_url,
                'github_url'          => $request->github_url,
                'linkedin_url'        => $request->linkedin_url ?? null,
                'screening_answers'   => $request->screening_answers,
                'access_token'        => $accessToken,
                'application_email_sent' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Application create failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save application: ' . $e->getMessage()], 500);
        }

        // Queue question generation (background — fires when eval starts)
        try {
            GenerateCandidateQuestions::dispatch($application->id)
                ->onQueue('ai-processing')
                ->delay(now()->addSeconds(5));
        } catch (\Exception $e) {
            \Log::warning('Could not dispatch GenerateCandidateQuestions', ['error' => $e->getMessage()]);
        }

        // Send confirmation email (queued)
        try {
            \App\Jobs\SendApplicationConfirmationEmail::dispatch($application->id)->onQueue('notifications');
        } catch (\Exception $e) {
            \Log::warning('Could not dispatch confirmation email', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success'            => true,
            'application_number' => $application->application_number,
            'eval_date'          => $job->eval_start_date?->format('l, d F Y') ?? 'to be announced',
            'access_token'       => $accessToken,
            'message'            => 'Application submitted successfully! Check your email for confirmation.',
        ]);
    }

    /**
     * Show evaluation interface (same link, phase = evaluating).
     */
    public function evaluation(Request $request, string $token): View|JsonResponse
    {
        $job = Job::where('application_link_token', $token)->with('company')->firstOrFail();

        if (! in_array($job->application_phase, ['evaluating'])) {
            return redirect()->route('apply.show', $token);
        }

        // Find the application by access token or auth
        $application = $this->resolveApplication($request, $job);

        if (! $application) {
            return view('apply.no-application', compact('job', 'token'));
        }

        // Check if evaluation already done
        if ($application->evaluation_status === 'completed') {
            return view('apply.evaluation-complete', compact('job', 'application', 'token'));
        }

        // Get or create session
        $session = EvaluationSession::where('application_id', $application->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $session) {
            $session = $this->evaluationService->startSession($application);
        }

        return view('apply.evaluation', compact('job', 'application', 'session', 'token'));
    }

    /**
     * API: Get next question for evaluation session.
     */
    public function getQuestion(Request $request, string $token): JsonResponse
    {
        $sessionToken = $request->input('session_token');
        $question = $this->evaluationService->getCurrentQuestion($sessionToken);

        if (! $question) {
            return response()->json(['complete' => true]);
        }

        return response()->json($question);
    }

    /**
     * API: Submit answer for evaluation session.
     */
    public function submitAnswer(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'session_token'      => ['required', 'string'],
            'question_id'        => ['required', 'integer'],
            'answer'             => ['nullable'],
            'time_taken_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        // Record anti-cheat events
        if ($request->input('tab_switch')) {
            $this->evaluationService->recordAntiCheatEvent($request->input('session_token'), 'tab_switch');
        }

        $result = $this->evaluationService->submitAnswer(
            $request->input('session_token'),
            $request->integer('question_id'),
            [
                'answer_text'        => $request->input('answer'),
                'selected_option'    => $request->input('answer'),
                'time_taken_seconds' => $request->integer('time_taken_seconds'),
            ]
        );

        return response()->json($result);
    }

    /**
     * Show results page.
     */
    public function results(Request $request, string $token): View
    {
        $job = Job::where('application_link_token', $token)->with('company')->firstOrFail();
        $application = $this->resolveApplication($request, $job);

        return view('apply.results', compact('job', 'application', 'token'));
    }

    private function resolvePhase(Job $job): string
    {
        $now = now()->toDateString();

        if ($job->application_phase === 'complete') {
            return 'results';
        }
        if ($job->application_phase === 'ranked' || $job->application_phase === 'evaluating') {
            return 'evaluating';
        }
        if ($job->close_date && $now > $job->close_date) {
            return 'closed';
        }
        if ($job->open_date && $now < $job->open_date) {
            return 'coming_soon';
        }

        return 'open';
    }

    private function resolveApplication(Request $request, Job $job): ?Application
    {
        if (auth()->check()) {
            return Application::where('job_id', $job->id)->where('user_id', auth()->id())->first();
        }

        $accessToken = $request->input('access_token') ?? $request->cookie('apply_token_' . $job->id);
        if ($accessToken) {
            return Application::where('job_id', $job->id)->where('access_token', $accessToken)->first();
        }

        return null;
    }
}
