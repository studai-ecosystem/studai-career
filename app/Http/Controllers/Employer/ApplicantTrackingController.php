<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationConfirmationMail;
use App\Mail\PipelineStageAdvancedMail;
use App\Models\Application;
use App\Models\Job;
use App\Models\Resume;
use App\Jobs\SendHiringEmailsJob;
use App\Notifications\CandidateHiredNotification;
use App\Notifications\CandidateRejectedNotification;
use App\Notifications\CandidateShortlistedNotification;
use App\Notifications\PipelineStageAdvancedNotification;
use App\Services\ResumeExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employer']);
    }

    public function index(Request $request)
    {
        $company = auth()->user()->company;

        // Direct JOIN instead of whereHas — uses index, no subquery overhead
        $query = Application::with(['job:id,title,location,company_id', 'user:id,name,email', 'user.profile:id,user_id,avatar'])
            ->join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $company->id)
            ->select('applications.*');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('applications.status', $request->status);
        }

        // Filter by job
        if ($request->filled('job_id')) {
            $query->where('applications.job_id', $request->job_id);
        }

        // Search by candidate name — join users only when needed
        if ($request->filled('search')) {
            $search = $request->search;
            $query->join('users', 'applications.user_id', '=', 'users.id')
                  ->where(function ($q) use ($search) {
                      $q->where('users.name',  'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                  });
        }

        // Sort
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('applications.created_at');
                break;
            case 'name':
                // Already joined users if searching; otherwise join here
                if (! $request->filled('search')) {
                    $query->join('users', 'applications.user_id', '=', 'users.id');
                }
                $query->orderBy('users.name');
                break;
            default:
                $query->orderByDesc('applications.created_at');
        }

        $applications = $query->paginate(20)->withQueryString();

        // Get jobs for filter dropdown
        $jobs = Job::where('company_id', $company->id)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        // Get status counts — single query with GROUP BY (was 5 separate queries)
        $rawCounts = Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $company->id)
            ->select('applications.status', DB::raw('COUNT(*) as count'))
            ->groupBy('applications.status')
            ->pluck('count', 'applications.status');

        $statusCounts = [
            'all'         => (int) $rawCounts->sum(),
            'pending'     => (int) ($rawCounts['pending']     ?? 0),
            'reviewing'   => (int) ($rawCounts['reviewing']   ?? 0),
            'shortlisted' => (int) ($rawCounts['shortlisted'] ?? 0),
            'rejected'    => (int) ($rawCounts['rejected']    ?? 0),
        ];

        return view('employer.applicants.index', compact('applications', 'jobs', 'statusCounts'));
    }

    public function show($id)
    {
        $company = auth()->user()->company;

        $application = Application::with(['job.hiringRounds' => fn($q) => $q->orderBy('round_order'), 'user.profile'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        // Load the candidate's test attempts for each hiring round
        $roundAttempts = collect();
        if ($application->user_id && $application->job->hiringRounds->isNotEmpty()) {
            $roundIds      = $application->job->hiringRounds->pluck('id');
            $roundAttempts = \App\Models\RoundAttempt::whereIn('hiring_round_id', $roundIds)
                ->where('user_id', $application->user_id)
                ->get()
                ->keyBy('hiring_round_id');
        }

        $completedAttempts = $roundAttempts->filter(
            fn($a) => in_array($a->status, ['submitted', 'evaluated']) && $a->score !== null
        );
        $overallTestScore = $completedAttempts->isNotEmpty()
            ? (int) round($completedAttempts->avg('score'))
            : null;

        return view('employer.applicants.show', compact('application', 'roundAttempts', 'overallTestScore'));
    }

    /**
     * Stream/download an applicant's resume.
     *
     * Resolves the resume from (in order): a saved/AI resume reference ("resume:{id}"),
     * an uploaded application file, or the candidate profile resume. Scoped to the
     * employer's company so only authorized recruiters can access it.
     */
    public function resume($id)
    {
        $company = auth()->user()->company;
        abort_unless($company, 403);

        $application = Application::with(['user.profile'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        $resumeFile = $application->resume_file;

        // Case 1: saved/AI-generated resume reference -> render PDF from the Resume model.
        if ($resumeFile && str_starts_with($resumeFile, 'resume:')) {
            $resumeId = (int) substr($resumeFile, strlen('resume:'));
            $resume = Resume::find($resumeId);
            abort_unless($resume, 404, 'Resume not found.');

            $path = $resume->pdf_path;
            if (!$path || !Storage::disk('public')->exists($path)) {
                $path = app(ResumeExportService::class)->exportToPDF($resume);
                $resume->update(['pdf_path' => $path]);
            }

            return response()->download(
                Storage::disk('public')->path($path),
                Str::slug($resume->title ?: 'resume') . '.pdf'
            );
        }

        // Case 2: an uploaded application file or the candidate profile resume.
        $candidatePaths = array_filter([
            $resumeFile,
            $application->user?->profile?->resume_path,
        ]);

        foreach ($candidatePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return response()->download(Storage::disk('public')->path($path));
            }
            if (Storage::disk('private')->exists($path)) {
                return response()->download(Storage::disk('private')->path($path));
            }
        }

        abort(404, 'No resume file available for this candidate.');
    }

    public function updateStatus(Request $request, $id)
    {
        $company = auth()->user()->company;
        
        $application = Application::whereHas('job', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'status'          => ['required', 'in:pending,reviewing,shortlisted,interviewed,rejected,hired'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $application->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus, 'status_updated_at' => now()];
        if (!empty($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }
        if (!empty($validated['rejection_reason'])) {
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        }
        $application->update($updateData);

        // Notify candidate when status changes to a notable state
        if ($oldStatus !== $newStatus && $application->user) {
            try {
                match ($newStatus) {
                    'shortlisted' => $application->user->notify(
                        new CandidateShortlistedNotification($application, (float) ($application->final_rank_score ?? 0))
                    ),
                    'hired' => $application->user->notify(
                        new CandidateHiredNotification($application)
                    ),
                    'rejected' => $application->user->notify(
                        new CandidateRejectedNotification($application)
                    ),
                    default => null,
                };
            } catch (\Throwable $e) {
                Log::warning('Status-change notification failed', [
                    'application_id' => $application->id,
                    'status'         => $newStatus,
                    'error'          => $e->getMessage(),
                ]);
            }

            // AI emails fire for all meaningful status changes
            if (in_array($newStatus, ['shortlisted', 'interviewed', 'hired', 'rejected'])) {
                try {
                    $matchScore = (float) ($application->final_rank_score ?? 0);
                    SendHiringEmailsJob::dispatch($application, $newStatus, $matchScore)
                        ->onQueue('notifications');
                } catch (\Throwable $e) {
                    Log::warning('Hiring emails failed on status update', [
                        'application_id' => $application->id,
                        'error'          => $e->getMessage(),
                    ]);
                }
            }
        }

        // Bust dashboard caches so new status reflects immediately
        EmployerDashboardController::bustCaches($company->id);

        return $request->wantsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Application status updated successfully!',
                'status'  => $application->status,
            ])
            : redirect()->route('employer.applicants.show', $id)
                ->with('success', 'Status updated to ' . ucfirst($newStatus) . '. ' .
                    (in_array($newStatus, ['shortlisted', 'hired', 'rejected'])
                        ? 'Email notifications sent to candidate and company.'
                        : ''));
    }

    public function bulkUpdateStatus(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'application_ids' => ['required', 'array'],
            'application_ids.*' => ['integer', 'exists:applications,id'],
            'status' => ['required', 'in:pending,reviewing,shortlisted,interviewed,rejected,hired'],
        ]);

        $applications = Application::whereIn('id', $validated['application_ids'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->with('user', 'job')
            ->get();

        $newStatus = $validated['status'];
        $updated   = 0;

        foreach ($applications as $application) {
            $oldStatus = $application->status;
            $application->update(['status' => $newStatus, 'status_updated_at' => now()]);
            $updated++;

            if ($oldStatus !== $newStatus && $application->user) {
                try {
                    match ($newStatus) {
                        'shortlisted' => $application->user->notifyNow(
                            new CandidateShortlistedNotification($application, (float) ($application->final_rank_score ?? 0))
                        ),
                        'hired' => $application->user->notifyNow(
                            new CandidateHiredNotification($application)
                        ),
                        'rejected' => $application->user->notifyNow(
                            new CandidateRejectedNotification($application)
                        ),
                        default => null,
                    };
                } catch (\Throwable $e) {
                    Log::warning('Bulk status-change notification failed', [
                        'application_id' => $application->id,
                        'status'         => $newStatus,
                        'error'          => $e->getMessage(),
                    ]);
                }

                // AI emails for shortlist/interview/hire/rejection
                if ($oldStatus !== $newStatus && in_array($newStatus, ['shortlisted', 'interviewed', 'hired', 'rejected'])) {
                    try {
                        $matchScore = (float) ($application->final_rank_score ?? 0);
                        SendHiringEmailsJob::dispatch($application, $newStatus, $matchScore)
                            ->onQueue('notifications');
                    } catch (\Throwable $e) {
                        Log::warning('Bulk hiring emails failed', [
                            'application_id' => $application->id,
                            'error'          => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        EmployerDashboardController::bustCaches($company->id);

        return response()->json([
            'success' => true,
            'message' => "{$updated} applications updated successfully!",
        ]);
    }

    public function addNote(Request $request, $id)
    {
        $company = auth()->user()->company;
        
        $application = Application::whereHas('job', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $application->update(['notes' => $validated['notes']]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully!',
        ]);
    }

    /**
     * Set the hiring pipeline stage and schedule date, send email to candidate.
     */
    public function setPipelineStage(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $company = auth()->user()->company;

        $application = Application::with(['user', 'job.company'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->findOrFail($id);

        $validated = $request->validate([
            'stage'         => ['required', 'in:company_info_test,aptitude,tech_test,non_tech_test,hired,rejected'],
            'stage_date'    => ['required_unless:stage,hired,rejected', 'nullable', 'date'],
            'stage_notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        $stage = $validated['stage'];
        $notes = $validated['stage_notes'] ?? null;

        // Update application
        $updateData = [
            'hiring_stage'         => in_array($stage, ['hired', 'rejected']) ? null : $stage,
            'pipeline_stage_date'  => $validated['stage_date'] ?? null,
            'pipeline_stage_notes' => $notes,
        ];

        // Also update main status for hired/rejected
        if ($stage === 'hired') {
            $updateData['status'] = 'hired';
            $updateData['status_updated_at'] = now();
        } elseif ($stage === 'rejected') {
            $updateData['status'] = 'rejected';
            $updateData['status_updated_at'] = now();
        }

        $application->update($updateData);

        // Send email to candidate
        if ($application->user && $application->user->email) {
            try {
                $stageNames = [
                        'company_info_test' => 'Company Info Test',
                        'aptitude'          => 'Aptitude Assessment',
                        'tech_test'         => 'Technical Test',
                        'non_tech_test'     => 'Non-Technical Test',
                    ];
                $candidateName  = $application->user->name ?? 'Candidate';
                $jobTitle       = $application->job->title ?? 'the position';
                $companyName    = $application->job->company?->name ?? 'your company';
                $hrEmail        = $application->job->company?->hr_email
                    ?? $application->job->company?->company_email
                    ?? auth()->user()->email;

                if (in_array($stage, ['company_info_test', 'aptitude', 'tech_test', 'non_tech_test'])) {
                    // 1. Candidate gets detailed test invitation
                    Mail::to($application->user->email)
                        ->send(new PipelineStageAdvancedMail($application, $stage, $notes));

                    // Reset SMTP connection before second send
                    try { app('mail.manager')->purge('smtp'); } catch (\Throwable $e) {}

                    // 2. Company/HR gets confirmation summary
                    $stageName    = $stageNames[$stage];
                    $scheduledOn  = !empty($validated['stage_date'])
                        ? \Carbon\Carbon::parse($validated['stage_date'])->format('d M Y')
                        : 'To be confirmed';
                    $bodyText = "You have scheduled {$candidateName} for the {$stageName} for the {$jobTitle} position.\n\n"
                        . "Test Date: {$scheduledOn}\n"
                        . ($notes ? "Notes: {$notes}\n" : '')
                        . "\nThe candidate has been notified via email with test details and date.";

                    Mail::to($hrEmail)->send(new \App\Mail\HRHiringMail(
                        emailSubject:      "Test Scheduled: {$candidateName} — {$stageName} for {$jobTitle}",
                        body:              $bodyText,
                        candidateName:     $candidateName,
                        candidateEmail:    $application->user->email ?? '',
                        jobTitle:          $jobTitle,
                        companyName:       $companyName,
                        eventType:         'interviewed',
                        applicationNumber: $application->application_number ?? '',
                        appliedAt:         $application->created_at?->format('d M Y') ?? '',
                    ));

                } elseif ($stage === 'hired' || $stage === 'rejected') {
                    $matchScore = (float) ($application->final_rank_score ?? 0);
                    SendHiringEmailsJob::dispatch($application, $stage, $matchScore)
                        ->onQueue('notifications');
                }
            } catch (\Throwable $e) {
                Log::warning('Pipeline stage email failed', [
                    'application_id' => $application->id,
                    'stage'          => $stage,
                    'error'          => $e->getMessage(),
                ]);
            }

            // In-app notification — always sent
            try {
                $application->user->notify(new PipelineStageAdvancedNotification($application, $stage));
            } catch (\Throwable $e) {
                Log::warning('Pipeline stage in-app notification failed', [
                    'application_id' => $application->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        $stageLabels = [
            'company_info_test' => 'Company Info Test',
            'aptitude'          => 'Aptitude Assessment',
            'tech_test'         => 'Technical Test',
            'non_tech_test'     => 'Non-Technical Test',
            'hired'             => 'Hired',
            'rejected'          => 'Rejected',
        ];

        return response()->json([
            'success' => true,
            'message' => "Candidate advanced to: {$stageLabels[$stage]}. Email sent.",
            'stage'   => $stage,
        ]);
    }

    /**
     * Set evaluation date on a job (all applications for that job share this date).
     */
    public function setJobEvaluationDate(Request $request, int $jobId): \Illuminate\Http\JsonResponse
    {
        $company = auth()->user()->company;

        $job = Job::where('company_id', $company->id)->findOrFail($jobId);

        $validated = $request->validate([
            'eval_start_date' => ['required', 'date'],
            'close_date'      => ['nullable', 'date'],
        ]);

        $job->update([
            'eval_start_date' => $validated['eval_start_date'],
            'close_date'      => $validated['close_date'] ?? $job->close_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evaluation date updated. All future confirmation emails will include this date.',
        ]);
    }

    public function kanban(Request $request)
    {
        $company = auth()->user()->company;
        $jobId   = $request->get('job_id');

        // Use direct JOIN instead of whereHas (faster) + limit per status to avoid memory spikes
        $query = Application::with(['user:id,name,email', 'user.profile:id,user_id,avatar', 'job:id,title,company_id'])
            ->join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $company->id)
            ->select('applications.*');

        if ($jobId) {
            $query->where('applications.job_id', $jobId);
        }

        // Cap at 500 total cards to prevent memory spikes
        $applications = $query->latest('applications.created_at')->limit(500)->get();

        // Group in PHP (already in memory — no extra queries)
        $kanbanData = [
            'pending'     => $applications->where('status', 'pending'),
            'reviewing'   => $applications->where('status', 'reviewing'),
            'shortlisted' => $applications->where('status', 'shortlisted'),
            'rejected'    => $applications->where('status', 'rejected'),
            'hired'       => $applications->where('status', 'hired'),
        ];

        $jobs = Job::where('company_id', $company->id)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return view('employer.applicants.kanban', compact('kanbanData', 'jobs', 'jobId'));
    }

    public function export(Request $request)
    {
        $company = auth()->user()->company;
        
        $query = Application::with(['job', 'user.profile'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        $applications = $query->get();

        // Create CSV
        $csv = [];
        $csv[] = ['Name', 'Email', 'Job Title', 'Applied Date', 'Status', 'Experience', 'Skills'];

        foreach ($applications as $app) {
            $profile = $app->user->profile;
            
            $skills = [];
            $experienceText = 'N/A';
            
            if ($profile) {
                $skills = $profile->skills ?? [];
                if (is_string($skills)) {
                    $skills = json_decode($skills, true) ?? [];
                }
                
                $experience = $profile->experience ?? [];
                if (is_string($experience)) {
                    $experience = json_decode($experience, true) ?? [];
                }
                $experienceText = !empty($experience) ? count($experience) . ' positions' : 'N/A';
            }

            $csv[] = [
                $app->user->name,
                $app->user->email,
                $app->job->title,
                $app->created_at->format('Y-m-d'),
                $app->status,
                $experienceText,
                implode(', ', $skills),
            ];
        }

        $filename = 'applications_' . now()->format('Y-m-d') . '.csv';
        
        $handle = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Orin™ ranked shortlist view for a specific job.
     */
    public function ranked(int $id)
    {
        $company = auth()->user()->company;

        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $applications = Application::where('job_id', $job->id)
            ->whereNotNull('final_rank_score')
            ->with(['user', 'evaluationSession'])
            ->orderBy('rank_position')
            ->get();

        $rankedCount = $applications->count();

        return view('employer.applicants.ranked', compact('job', 'applications', 'rankedCount'));
    }

    /**
     * CSV export of Orin™ ranked shortlist for a specific job.
     */
    public function exportRanked(int $id)
    {
        $company = auth()->user()->company;

        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $applications = Application::where('job_id', $job->id)
            ->whereNotNull('final_rank_score')
            ->with(['user', 'evaluationSession'])
            ->orderBy('rank_position')
            ->get();

        $csv   = [];
        $csv[] = ['Rank', 'Name', 'Email', 'Final Score', 'Eval Score', 'Skill Match', 'Resume Quality', 'Behavioural Fit', 'Status', 'Flagged'];

        foreach ($applications as $app) {
            $name    = $app->is_guest_applicant ? $app->guest_name : ($app->user?->name ?? 'N/A');
            $email   = $app->is_guest_applicant ? $app->guest_email : ($app->user?->email ?? 'N/A');
            $session = $app->evaluationSession;

            $csv[] = [
                $app->rank_position,
                $name,
                $email,
                number_format((float) ($app->final_rank_score ?? 0), 2),
                number_format((float) ($app->evaluation_score ?? 0), 2),
                number_format((float) ($app->skill_match_score ?? 0), 2),
                number_format((float) ($app->resume_quality_score ?? 0), 2),
                number_format((float) ($app->behavioural_fit_score ?? 0), 2),
                $app->status ?? 'pending',
                ($session?->flagged_for_review ? 'Yes' : 'No'),
            ];
        }

        $filename = 'ranked_' . \Str::slug($job->title) . '_' . now()->format('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
