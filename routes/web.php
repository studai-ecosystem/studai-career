<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CareerProfileController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PublicApplyController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Employer\EmployerDashboardController;
use App\Http\Controllers\Employer\CompanyProfileController;
use App\Http\Controllers\Employer\JobPostingController;
use App\Http\Controllers\Employer\ApplicantTrackingController;
use App\Http\Controllers\Employer\InterviewManagementController;
use App\Http\Controllers\Employer\HiringTestManagerController;
use App\Http\Controllers\HiringTestController;
use App\Http\Controllers\Admin\ApplicationMonitorController;
use App\Http\Controllers\SkillAnalyzerWebController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\EnhancedAnalyticsController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\OfferLetterController;
use App\Http\Controllers\CareerCoachController;
use App\Http\Controllers\ResumeController;
use App\Livewire\ProfileWizard;
use Illuminate\Support\Facades\Route;

// Marketing Pages
Route::get('/', [MarketingController::class, 'home'])->name('home');

// ── Email Preview (dev only) ────────────────────────────────────────────────
if (app()->isLocal()) {
    Route::get('/preview/email/{type}/{event}', function (string $type, string $event) {
        // type = 'candidate' | 'hr'
        // event = 'shortlisted' | 'hired' | 'rejected'
        $allowedTypes  = ['candidate', 'hr'];
        $allowedEvents = ['shortlisted', 'hired', 'rejected'];

        if (!in_array($type, $allowedTypes) || !in_array($event, $allowedEvents)) {
            abort(404, 'Invalid preview type or event. Use: candidate|hr and shortlisted|hired|rejected');
        }

        // Pick a real application to use as data
        $statusMap   = ['shortlisted' => 'shortlisted', 'hired' => 'hired', 'rejected' => 'rejected'];
        $application = \App\Models\Application::with(['user.profile', 'job.company'])
            ->where('status', $statusMap[$event])
            ->first()
            ?? \App\Models\Application::with(['user.profile', 'job.company'])->first();

        $service    = app(\App\Services\HiringEmailService::class);
        $matchScore = (float) ($application->final_rank_score ?? $application->match_score ?? 85.0);

        if ($type === 'candidate') {
            $content = match ($event) {
                'hired'    => $service->generateCandidateHiredEmail($application),
                'rejected' => $service->generateCandidateRejectedEmail($application),
                default    => $service->generateCandidateShortlistedEmail($application, $matchScore),
            };

            $mail = new \App\Mail\CandidateHiringMail(
                emailSubject:  $content['subject'],
                body:          $content['body'],
                candidateName: $application->user->name ?? 'Candidate',
                jobTitle:      $application->job->title ?? 'Position',
                companyName:   $application->job->company->name ?? 'Company',
                eventType:     $event,
            );
        } else {
            $content = match ($event) {
                'hired'    => $service->generateHRHiredEmail($application),
                'rejected' => $service->generateHRRejectedEmail($application),
                default    => $service->generateHRShortlistedEmail($application, $matchScore),
            };

            $profile  = $application->user->profile;
            $profileData = [
                'headline'             => $profile?->headline ?? '',
                'summary'              => $profile?->summary ?? '',
                'skills'               => is_array($profile?->skills)
                                            ? implode(', ', $profile->skills)
                                            : ($profile?->skills ?? ''),
                'location'             => $profile?->current_location ?? '',
                'work_preference'      => $profile?->work_preference ?? '',
                'notice_period'        => $profile?->notice_period ?? '',
                'expected_salary'      => $profile?->expected_salary_min && $profile?->expected_salary_max
                                            ? '₹' . number_format((float)$profile->expected_salary_min) . ' – ₹' . number_format((float)$profile->expected_salary_max)
                                            : '',
                'profile_completeness' => $profile?->profile_completeness ?? 0,
            ];

            $mail = new \App\Mail\HRHiringMail(
                emailSubject:      $content['subject'],
                body:              $content['body'],
                candidateName:     $application->user->name ?? 'Candidate',
                candidateEmail:    $application->user->email ?? 'candidate@example.com',
                jobTitle:          $application->job->title ?? 'Position',
                companyName:       $application->job->company->name ?? 'Company',
                eventType:         $event,
                matchScore:        $matchScore,
                profile:           $profileData,
                coverLetter:       $application->cover_letter ?? '',
                applicationNumber: $application->application_number ?? '',
                appliedAt:         $application->submitted_at?->format('M d, Y') ?? '',
                rejectionReason:   $application->rejection_reason ?? '',
                linkedinUrl:       $application->linkedin_url ?? '',
                githubUrl:         $application->github_url ?? '',
                portfolioUrl:      $application->portfolio_url ?? '',
                resumeUrl:         $application->resume_file ? asset('storage/' . $application->resume_file) : '',
            );
        }

        return $mail->render();
    })->name('preview.email');
}
// ────────────────────────────────────────────────────────────────────────────
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('how-it-works');
Route::get('/blog', [MarketingController::class, 'blog'])->name('blog');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::get('/for-employers', [MarketingController::class, 'forEmployers'])->name('employers');

// Legal Pages
Route::get('/privacy-policy', [MarketingController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [MarketingController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [MarketingController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/cookie-policy', [MarketingController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/security', [MarketingController::class, 'security'])->name('security');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Contact Form
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Payment Gateway Callbacks (PayU)
Route::post('/payment/success', [PaymentController::class, 'payuSuccess'])->name('payment.payu.success');
Route::post('/payment/failure', [PaymentController::class, 'payuFailure'])->name('payment.payu.failure');

// Payment Gateway Callbacks (Stripe)
Route::get('/payment/stripe/success', [\App\Http\Controllers\StripeWebhookController::class, 'checkoutSuccess'])->name('payment.stripe.success');

// Payment Gateway - Razorpay
use App\Http\Controllers\RazorpayController;
Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.create-order');
Route::post('/verify-payment', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify-payment');

// Dashboard (Authenticated)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Subscriptions & Billing
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    // Public pricing page
    Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');
    
    // Authenticated subscription management
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/select-plan', [SubscriptionController::class, 'selectPlan'])->name('select-plan');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
    });
});

// Profile Management (Authenticated)
Route::middleware('auth')->group(function () {
    // Skill Gap Analyzer Routes
    Route::prefix('skills')->name('skills.')->group(function () {
        Route::get('/dashboard', [SkillAnalyzerWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/learning-paths', [SkillAnalyzerWebController::class, 'learningPaths'])->name('learning-paths');
        Route::get('/learning-path/{id}', [SkillAnalyzerWebController::class, 'showLearningPath'])->name('learning-path.show');
        Route::get('/validation', [SkillAnalyzerWebController::class, 'validation'])->name('validation');
        Route::get('/assessments', [SkillAnalyzerWebController::class, 'assessments'])->name('assessments');
        Route::get('/assessment/{id}', [SkillAnalyzerWebController::class, 'takeAssessment'])->name('assessment.take');
        Route::get('/daily-learning', [SkillAnalyzerWebController::class, 'dailyLearning'])->name('daily-learning');
    });
    
    // Applications Tracking
    Route::get('/applications', [DashboardController::class, 'applications'])->name('dashboard.applications');

    // AI Credits History
    Route::get('/ai-credits', [DashboardController::class, 'aiCredits'])->name('dashboard.ai-credits');

    // Notifications
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.all');
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/read', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return back();
    })->name('notifications.read');

    // Payment History
    Route::get('/payments', [PaymentHistoryController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentHistoryController::class, 'show'])->name('payments.show');
    
    // Job Search & Actions
    Route::get('/jobs/search', [JobController::class, 'search'])->name('jobs.search');
    Route::get('/jobs/saved', [JobController::class, 'saved'])->name('jobs.saved');
    Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');

    // Candidate Test Routes
    Route::get('/jobs/{jobId}/rounds/{roundId}/test', [\App\Http\Controllers\CandidateTestController::class, 'show'])->name('candidate.test.show');
    Route::post('/jobs/{jobId}/rounds/{roundId}/test', [\App\Http\Controllers\CandidateTestController::class, 'submit'])->name('candidate.test.submit');
    Route::get('/jobs/{jobId}/rounds/{roundId}/result', [\App\Http\Controllers\CandidateTestController::class, 'result'])->name('candidate.test.result');

    // Job Actions API
    Route::post('/api/jobs/{id}/toggle-save', [JobController::class, 'toggleSave'])->name('api.jobs.toggle-save');
    Route::post('/api/jobs/{id}/apply', [JobController::class, 'apply'])
        ->middleware('throttle:20,1')
        ->name('api.jobs.apply');
    // AI generation: max 10 cover letters per minute per user
    Route::post('/api/ai/generate-cover-letter', [JobController::class, 'generateCoverLetter'])
        ->middleware('throttle:10,1')
        ->name('api.ai.generate-cover-letter');
    
    // Account settings (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Career Profile Management
    Route::prefix('profile/career')->name('profile.career.')->group(function () {
        Route::get('/', [CareerProfileController::class, 'index'])->name('index');
        Route::get('/builder', ProfileWizard::class)->name('builder');
        
        // API endpoints for profile management
        Route::post('/upload-resume', [CareerProfileController::class, 'uploadResume'])->name('upload-resume');
        Route::post('/auto-fill', [CareerProfileController::class, 'autoFillFromResume'])->name('auto-fill');
        Route::patch('/section/{section}', [CareerProfileController::class, 'updateSection'])->name('update-section');
        Route::get('/suggestions', [CareerProfileController::class, 'getAISuggestions'])->name('suggestions');
    });

    // Interview Preparation & Mock Practice
    Route::prefix('interview')->name('interview.')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('index');
        Route::get('/create', [InterviewController::class, 'create'])->name('create');
        Route::post('/start', [InterviewController::class, 'start'])->name('start');

        Route::get('/session/{session}', [InterviewController::class, 'session'])->name('session');
        Route::post('/session/{session}/answer', [InterviewController::class, 'submitAnswer'])->name('submit-answer');
        Route::post('/session/{session}/follow-up', [InterviewController::class, 'getFollowUp'])->name('follow-up');
        Route::get('/session/{session}/complete', [InterviewController::class, 'complete'])->name('complete');
        Route::get('/session/{session}/pdf', [InterviewController::class, 'downloadPdf'])->name('pdf');
        Route::get('/session/{session}/skill-map', [InterviewController::class, 'skillMap'])->name('skill-map');

        Route::get('/common-questions', [InterviewController::class, 'commonQuestions'])->name('common-questions');
        Route::get('/star-guide', [InterviewController::class, 'starGuide'])->name('star-guide');
        Route::post('/format-star', [InterviewController::class, 'formatStar'])->name('format-star');

        Route::get('/salary-negotiation', [InterviewController::class, 'salaryNegotiation'])->name('salary-negotiation');
        Route::post('/salary-negotiation/guide', [InterviewController::class, 'getNegotiationGuide'])->name('get-negotiation-guide');

        Route::get('/tips', [InterviewController::class, 'tips'])->name('tips');
        Route::get('/coaches', [InterviewController::class, 'findCoaches'])->name('coaches');
        Route::post('/recordings', [InterviewController::class, 'saveRecording'])->name('recordings.save');
    });

    // Enhanced Analytics Dashboard Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        // Main dashboard
        Route::get('/', [EnhancedAnalyticsController::class, 'dashboard'])->name('dashboard');
        
        // Job Market Heatmap
        Route::get('/heatmap', [EnhancedAnalyticsController::class, 'heatmapView'])->name('heatmap');
        Route::get('/api/heatmap', [EnhancedAnalyticsController::class, 'heatmap'])->name('api.heatmap');
        
        // Salary Benchmarking
        Route::get('/salary-benchmark', [EnhancedAnalyticsController::class, 'salaryBenchmarkView'])->name('salary-benchmark');
        Route::get('/api/salary-benchmark', [EnhancedAnalyticsController::class, 'salaryBenchmark'])->name('api.salary-benchmark');
        
        // Skills Demand Forecasting
        Route::get('/skills-forecast', [EnhancedAnalyticsController::class, 'skillsForecastView'])->name('skills-forecast');
        Route::get('/api/skills-forecast', [EnhancedAnalyticsController::class, 'skillsForecast'])->name('api.skills-forecast');
        
        // Career Path Visualization
        Route::get('/career-path', [EnhancedAnalyticsController::class, 'careerPathView'])->name('career-path');
        Route::get('/api/career-path', [EnhancedAnalyticsController::class, 'careerPath'])->name('api.career-path');
        
        // Application Funnel Analytics
        Route::get('/application-funnel', [EnhancedAnalyticsController::class, 'applicationFunnelView'])->name('application-funnel');
        Route::get('/api/application-funnel', [EnhancedAnalyticsController::class, 'applicationFunnel'])->name('api.application-funnel');
        
        // Time-to-Hire Metrics
        Route::get('/time-to-hire', [EnhancedAnalyticsController::class, 'timeToHireView'])->name('time-to-hire');
        Route::get('/api/time-to-hire', [EnhancedAnalyticsController::class, 'timeToHire'])->name('api.time-to-hire');
        
        // Source Attribution
        Route::get('/source-attribution', [EnhancedAnalyticsController::class, 'sourceAttributionView'])->name('source-attribution');
        Route::get('/api/source-attribution', [EnhancedAnalyticsController::class, 'sourceAttribution'])->name('api.source-attribution');
        
        // Competitor Salary Comparison
        Route::get('/competitor-salary', [EnhancedAnalyticsController::class, 'competitorSalaryView'])->name('competitor-salary');
        Route::get('/api/competitor-salary', [EnhancedAnalyticsController::class, 'competitorSalary'])->name('api.competitor-salary');
        
        // Salary Trends
        Route::get('/api/salary-trends', [EnhancedAnalyticsController::class, 'salaryTrends'])->name('api.salary-trends');
    });

    // Email Template Library Routes
    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
        Route::get('/variables', [EmailTemplateController::class, 'variables'])->name('variables');
        Route::get('/analytics', [EmailTemplateController::class, 'userAnalytics'])->name('user-analytics');
        Route::get('/category/{categoryId}', [EmailTemplateController::class, 'byCategory'])->name('by-category');
        Route::get('/{id}', [EmailTemplateController::class, 'show'])->name('show');
        Route::get('/{id}/data', [EmailTemplateController::class, 'getData'])->name('data');
        Route::get('/{id}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EmailTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmailTemplateController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/{id}/preview', [EmailTemplateController::class, 'preview'])->name('preview');
        Route::post('/{id}/ai-customize', [EmailTemplateController::class, 'aiCustomize'])->name('ai-customize');
        Route::post('/{id}/accept-customization', [EmailTemplateController::class, 'acceptCustomization'])->name('accept-customization');
        Route::post('/{id}/send', [EmailTemplateController::class, 'send'])->name('send');
        Route::get('/{id}/analytics', [EmailTemplateController::class, 'analytics'])->name('analytics');
    });

    // AI Career Coach Routes
    Route::prefix('career-coach')->name('career-coach.')->group(function () {
        Route::get('/', [CareerCoachController::class, 'index'])->name('index');

        // Sessions
        Route::post('/session', [CareerCoachController::class, 'startSession'])->name('session.create');
        Route::get('/session/{session}', [CareerCoachController::class, 'session'])->name('session.show');
        Route::post('/session/{session}/message', [CareerCoachController::class, 'sendMessage'])->name('session.message');
        Route::post('/session/{session}/end', [CareerCoachController::class, 'endSession'])->name('session.end');

        // Goals
        Route::get('/goals', [CareerCoachController::class, 'goals'])->name('goals');
        Route::post('/goals', [CareerCoachController::class, 'createGoal'])->name('goals.create');
        Route::patch('/goals/{goal}', [CareerCoachController::class, 'updateGoal'])->name('goals.update');
        Route::post('/goals/{goal}/progress', [CareerCoachController::class, 'updateProgress'])->name('goals.progress');
        Route::delete('/goals/{goal}', [CareerCoachController::class, 'deleteGoal'])->name('goals.delete');

        // Preferences
        Route::get('/preferences', [CareerCoachController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [CareerCoachController::class, 'updatePreferences'])->name('preferences.update');

        // History
        Route::get('/history', [CareerCoachController::class, 'history'])->name('history');

        // Check-ins
        Route::get('/checkin', [CareerCoachController::class, 'checkin'])->name('checkin');
        Route::post('/checkin', [CareerCoachController::class, 'processCheckin'])->name('checkin.process');
        Route::post('/checkins/{checkin}/start', [CareerCoachController::class, 'startCheckin'])->name('checkin.start');
        Route::post('/checkins/{checkin}/skip', [CareerCoachController::class, 'skipCheckin'])->name('checkin.skip');

        // Suggestions
        Route::get('/suggestions', [CareerCoachController::class, 'suggestions'])->name('suggestions');
        Route::post('/suggestions/{suggestion}/dismiss', [CareerCoachController::class, 'dismissSuggestion'])->name('suggestions.dismiss');
        Route::post('/suggestions/{suggestion}/act', [CareerCoachController::class, 'actOnSuggestion'])->name('suggestions.act');
    });

    // Resume Builder Routes
    Route::prefix('resume')->name('resume.')->group(function () {
        Route::get('/', [ResumeController::class, 'index'])->name('index');
        Route::get('/create', [ResumeController::class, 'create'])->name('create');
        Route::post('/', [ResumeController::class, 'store'])->name('store');
        Route::get('/{resume}/edit', [ResumeController::class, 'edit'])->name('edit');
        Route::put('/{resume}', [ResumeController::class, 'update'])->name('update');
        Route::delete('/{resume}', [ResumeController::class, 'destroy'])->name('destroy');
        Route::get('/{resume}/preview', [ResumeController::class, 'preview'])->name('preview');
        Route::get('/{resume}/export/pdf', [ResumeController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{resume}/export/docx', [ResumeController::class, 'exportDocx'])->name('export.docx');
        Route::post('/{resume}/duplicate', [ResumeController::class, 'duplicate'])->name('duplicate');
        Route::post('/{resume}/set-default', [ResumeController::class, 'setDefault'])->name('set-default');
        Route::post('/{resume}/toggle-public', [ResumeController::class, 'togglePublic'])->name('toggle-public');
        
        // AI-Powered Features — rate limited to protect AI credit budget
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('/{resume}/ai/generate-summary', [ResumeController::class, 'generateSummary'])->name('ai.generate-summary');
            Route::post('/{resume}/ai/extract-skills', [ResumeController::class, 'extractSkills'])->name('ai.extract-skills');
            Route::post('/{resume}/ai/optimize-for-job', [ResumeController::class, 'optimizeForJob'])->name('ai.optimize-for-job');
            Route::post('/{resume}/ai/analyze-ats', [ResumeController::class, 'analyzeATS'])->name('ai.analyze-ats');
        });

        // Cover Letter
        Route::get('/{resume}/cover-letter', [\App\Http\Controllers\CoverLetterController::class, 'show'])->name('cover-letter.show');
        Route::post('/{resume}/cover-letter/generate', [\App\Http\Controllers\CoverLetterController::class, 'generate'])->name('cover-letter.generate');
        Route::get('/{resume}/cover-letter/download/pdf', [\App\Http\Controllers\CoverLetterController::class, 'downloadPdf'])->name('cover-letter.pdf');
        Route::get('/{resume}/cover-letter/download/docx', [\App\Http\Controllers\CoverLetterController::class, 'downloadDocx'])->name('cover-letter.docx');

        // ATS Checker (full page)
        Route::get('/{resume}/ats-check', [\App\Http\Controllers\ResumeATSController::class, 'show'])->name('ats.show');
        Route::post('/{resume}/ats-check/run', [\App\Http\Controllers\ResumeATSController::class, 'run'])->name('ats.run');
        Route::get('/{resume}/ats-check/resume', [\App\Http\Controllers\ResumeATSController::class, 'editor'])->name('ats.editor');
        Route::post('/{resume}/ats-check/save', [\App\Http\Controllers\ResumeATSController::class, 'save'])->name('ats.save');
        Route::post('/{resume}/ats-check/suggest', [\App\Http\Controllers\ResumeATSController::class, 'suggestImprovement'])->name('ats.suggest');

        // AI skill suggestions for resume builder (no resume ID needed — pre-creation)
        Route::post('/ai/suggest-skills', function (\Illuminate\Http\Request $request) {
            $request->validate(['job_role' => 'required|string|max:100']);
            try {
                $jobRole = $request->job_role;
                $prompt = "List exactly 20 relevant professional skills for a '{$jobRole}' role. Return ONLY a JSON array of skill name strings, no explanation, no markdown. Example: [\"JavaScript\",\"React\",\"Node.js\"]";
                $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                    'model' => config('openai.model', 'gpt-4o-mini'),
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 400,
                    'temperature' => 0.5,
                ]);
                $content = trim($response->choices[0]->message->content ?? '[]');
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                $content = preg_replace('/\s*```$/', '', $content);
                $skills = json_decode($content, true);
                if (!is_array($skills)) {
                    $skills = array_map('trim', explode(',', strip_tags($content)));
                }
                $skills = array_values(array_filter(array_slice($skills, 0, 20)));
                return response()->json(['skills' => $skills]);
            } catch (\Exception $e) {
                \Log::error('AI skill suggestion failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'AI service unavailable. Please try again.'], 500);
            }
        })->name('ai.suggest-skills');
        
        // Suggestions
        Route::post('/{resume}/suggestions/{suggestionId}/accept', [ResumeController::class, 'acceptSuggestion'])->name('suggestions.accept');
        Route::post('/{resume}/suggestions/{suggestionId}/reject', [ResumeController::class, 'rejectSuggestion'])->name('suggestions.reject');
    });
});

// Public Skill Certificate View (No Auth Required)
Route::get('/skills/certificate/{hash}', [SkillAnalyzerWebController::class, 'showCertificate'])->name('skills.certificate.public');

// Public Resume View (No Auth Required)
Route::get('/r/{shareToken}', [ResumeController::class, 'publicView'])->name('resume.public');

// ─────────────────────────────────────────────────────────────────────────────
// Orin™ Public Application Link — career.studai.one/apply/{token}
// No authentication required. Supports guests and logged-in users.
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('apply')->name('apply.')->middleware('throttle:60,1')->group(function () {
    // Landing / phase-aware page
    Route::get('/{token}', [PublicApplyController::class, 'show'])->name('show');

    // Application submission (rate limited tightly)
    Route::post('/{token}/submit', [PublicApplyController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('submit');

    // Evaluation interface
    Route::get('/{token}/evaluation', [PublicApplyController::class, 'evaluation'])->name('evaluation');

    // Evaluation API endpoints
    Route::post('/{token}/evaluation/question', [PublicApplyController::class, 'getQuestion'])->name('evaluation.question');
    Route::post('/{token}/evaluation/answer', [PublicApplyController::class, 'submitAnswer'])
        ->middleware('throttle:120,1')
        ->name('evaluation.answer');

    // Anti-cheat event recorder
    Route::post('/{token}/evaluation/anticheat', function (\Illuminate\Http\Request $req, string $token) {
        app(\App\Services\AI\OrinEvaluationService::class)->recordAntiCheatEvent(
            $req->input('session_token', ''),
            $req->input('event_type', 'tab_switch')
        );
        return response()->json(['ok' => true]);
    })->middleware('throttle:200,1')->name('evaluation.anticheat');

    // Results page
    Route::get('/{token}/results', [PublicApplyController::class, 'results'])->name('results');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Autonomous Agent Routes (Job Seekers only — employers are redirected)
    Route::prefix('agent')->name('agent.')->middleware(['verified', 'subscription', 'jobseeker'])->group(function () {
        Route::get('/dashboard', function() {
            $user = Auth::user();
            $config = $user->agentConfiguration;
            $configured = $config !== null && $config->target_roles !== null && count($config->target_roles) > 0;
            
            // Get stats if configured
            $stats = null;
            $recentApplications = collect();
            $upcomingTasks = collect();
            $internalMatches = collect();
            $internalStats = ['pending' => 0, 'applied' => 0, 'skipped' => 0];
            $statistics = ['total_analyzed' => 0, 'total_applications' => 0, 'today_applications' => 0, 'success_rate' => 0, 'successful_applications' => 0, 'pending_applications' => 0];
            $limits = ['daily_limit' => 10, 'daily_remaining' => 10];
            
            if ($configured && $config) {
                $stats = [
                    'applications_today' => $user->applications()->whereDate('created_at', today())->count(),
                    'applications_this_week' => $user->applications()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'applications_this_month' => $config->applications_this_month ?? 0,
                    'interviews_scheduled' => $user->applications()->where('status', 'interview')->count(),
                    'response_rate' => $user->applications()->count() > 0 
                        ? round(($user->applications()->whereNotIn('status', ['pending', 'applied'])->count() / $user->applications()->count()) * 100) 
                        : 0,
                ];
                
                $recentApplications = $user->applications()
                    ->with('job.company')
                    ->latest()
                    ->take(5)
                    ->get();

                // Variables expected by the existing dashboard view
                $dailyLimit   = $config->daily_application_limit ?? 10;
                $appliedToday = $user->applications()->whereDate('created_at', today())->count();
                $totalApps    = $user->applications()->count();
                $pendingApps  = $user->applications()->where('status', 'pending')->count();
                $statistics = [
                    'total_analyzed'         => \App\Models\AgentInternalMatch::where('user_id', $user->id)->count(),
                    'total_applications'     => $user->applications()->where('source', 'agent')->count(),
                    'today_applications'     => $appliedToday,
                    'success_rate'           => $totalApps > 0
                        ? round(($user->applications()->whereNotIn('status', ['pending', 'applied'])->count() / $totalApps) * 100)
                        : 0,
                    'successful_applications'=> $user->applications()->where('status', 'interview')->count(),
                    'pending_applications'   => $pendingApps,
                ];
                $limits = [
                    'daily_limit'     => $dailyLimit,
                    'daily_remaining' => max(0, $dailyLimit - $appliedToday),
                ];

                // Internal platform job matches
                $internalMatches = \App\Models\AgentInternalMatch::where('user_id', $user->id)
                    ->with('job.company')
                    ->orderByDesc('match_score')
                    ->take(20)
                    ->get();

                $internalStats = [
                    'pending' => \App\Models\AgentInternalMatch::where('user_id', $user->id)->where('status', 'pending')->count(),
                    'applied' => \App\Models\AgentInternalMatch::where('user_id', $user->id)->where('status', 'applied')->count(),
                    'skipped' => \App\Models\AgentInternalMatch::where('user_id', $user->id)->where('status', 'skipped')->count(),
                ];
            }
            
            return view('agent.dashboard', compact('configured', 'config', 'stats', 'recentApplications', 'upcomingTasks', 'internalMatches', 'internalStats', 'statistics', 'limits'));
        })->name('dashboard');
        
        Route::get('/configure', function() {
            $user = Auth::user();
            $config = $user->agentConfiguration;
            return view('agent.configure', compact('config'));
        })->name('configure');
        
        Route::post('/configure', function(\Illuminate\Http\Request $request) {
            $user = Auth::user();
            
            // Convert comma-separated keyword/location strings to arrays
            $criteria = $request->input('job_search_criteria', []);
            if (isset($criteria['keywords']) && is_string($criteria['keywords'])) {
                $criteria['keywords'] = array_values(array_filter(array_map('trim', explode(',', $criteria['keywords']))));
            }
            if (isset($criteria['locations']) && is_string($criteria['locations'])) {
                $criteria['locations'] = array_values(array_filter(array_map('trim', explode(',', $criteria['locations']))));
            }
            $request->merge(['job_search_criteria' => $criteria]);
            
            // Validate the form data
            $validated = $request->validate([
                'job_search_criteria' => 'required|array',
                'job_search_criteria.keywords' => 'required|array|min:1',
                'job_search_criteria.locations' => 'nullable',
                'job_search_criteria.job_types' => 'nullable|array',
                'job_search_criteria.experience_levels' => 'nullable|array',
                'job_search_criteria.min_salary' => 'nullable|integer|min:0',
                'job_search_criteria.max_salary' => 'nullable|integer|min:0',
                'job_search_criteria.remote_preference' => 'nullable|string',
                'preferences' => 'nullable|array',
                'preferences.match_threshold' => 'nullable|integer|min:50|max:95',
                'preferences.apply_to_external_jobs' => 'nullable',
                'preferences.auto_customize_resume' => 'nullable',
                'preferences.generate_cover_letter' => 'nullable',
                'daily_application_limit' => 'nullable|integer|min:1|max:50',
                'require_approval' => 'nullable',
                'auto_follow_up' => 'nullable',
                'follow_up_days' => 'nullable|integer|min:1|max:30',
                'active_hours' => 'nullable|array',
                'active_hours.start' => 'nullable|integer|min:0|max:23',
                'active_hours.end' => 'nullable|integer|min:0|max:23',
                'active_hours.days' => 'nullable|array',
                'enable_learning' => 'nullable',
                'send_digest' => 'nullable',
            ]);
            
            $jobSearchCriteria = $validated['job_search_criteria'] ?? [];
            $preferences = $validated['preferences'] ?? [];
            $activeHours = $validated['active_hours'] ?? [];
            
            // Create or update configuration
            $config = \App\Models\AgentConfiguration::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'target_roles' => $jobSearchCriteria['keywords'] ?? [],
                    'preferred_locations' => is_array($jobSearchCriteria['locations'] ?? []) ? ($jobSearchCriteria['locations'] ?? []) : [],
                    'employment_types' => $jobSearchCriteria['job_types'] ?? ['full_time'],
                    'min_experience_years' => null,
                    'max_experience_years' => null,
                    'min_salary' => $jobSearchCriteria['min_salary'] ?? null,
                    'max_salary' => $jobSearchCriteria['max_salary'] ?? null,
                    'work_arrangements' => [$jobSearchCriteria['remote_preference'] ?? 'no_preference'],
                    'match_threshold_percentage' => $preferences['match_threshold'] ?? 70,
                    'daily_application_limit' => $validated['daily_application_limit'] ?? 10,
                    'require_approval' => isset($validated['require_approval']),
                    'auto_follow_up' => isset($validated['auto_follow_up']),
                    'follow_up_days' => $validated['follow_up_days'] ?? 7,
                    'enable_learning' => isset($validated['enable_learning']),
                    'active_hours' => $activeHours,
                    'active_days' => $activeHours['days'] ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]
            );
            
            return redirect()->route('agent.dashboard')
                ->with('success', 'Agent configuration saved successfully! You can now activate your agent.');
        })->name('configure.store');
        
        Route::get('/applications', function() {
            $user = Auth::user();
            $query = $user->applications()->with('job.company')->latest();

            if (request('status')) {
                $query->where('status', request('status'));
            }
            if (request('from_date')) {
                $query->whereDate('created_at', '>=', request('from_date'));
            }
            if (request('to_date')) {
                $query->whereDate('created_at', '<=', request('to_date'));
            }

            $applications = $query->paginate(20);

            // Count by status
            $statusCounts = $user->applications()
                ->selectRaw('status, count(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status')
                ->toArray();

            // Outcome counts mapped from status values
            $outcomeCounts = [
                'interview_scheduled' => $user->applications()->where('status', 'interview')->count(),
                'offer_received'      => $user->applications()->where('status', 'offer')->count(),
                'accepted'            => $user->applications()->where('status', 'accepted')->count(),
                'rejected'            => $user->applications()->where('status', 'rejected')->count(),
                'withdrawn'           => $user->applications()->where('status', 'withdrawn')->count(),
            ];
            $pendingCount = $statusCounts['pending'] ?? 0;

            return view('agent.applications', compact('applications', 'statusCounts', 'outcomeCounts', 'pendingCount'));
        })->name('applications');

        Route::get('/metrics', function() {
            $user = Auth::user();
            $total = $user->applications()->count();
            $successful = $user->applications()->where('status', 'interview')->count();
            $avgScore = $user->applications()->whereNotNull('evaluation_score')->avg('evaluation_score');
            $avgDays  = 0;

            $metrics = [
                'total_applications'   => $total,
                'success_rate'         => $total > 0 ? round(($successful / $total) * 100) : 0,
                'successful_outcomes'  => $successful,
                'avg_match_score'      => $avgScore ? round($avgScore) : 0,
                'avg_successful_score' => $avgScore ? round($avgScore) : 0,
                'response_rate'        => $total > 0 ? round(($user->applications()->whereNotIn('status', ['pending', 'submitted'])->count() / $total) * 100) : 0,
                'avg_days_to_response' => $avgDays,
            ];

            return view('agent.metrics', compact('metrics'));
        })->name('metrics');
        
        // Agent action routes (proxy to API controller)
        Route::post('/activate', [\App\Http\Controllers\API\AgentController::class, 'activate'])->name('activate');
        Route::post('/pause', [\App\Http\Controllers\API\AgentController::class, 'pause'])->name('pause');
        Route::post('/resume', [\App\Http\Controllers\API\AgentController::class, 'resume'])->name('resume');
        Route::post('/deactivate', [\App\Http\Controllers\API\AgentController::class, 'deactivate'])->name('deactivate');
        Route::get('/learning', [\App\Http\Controllers\API\AgentController::class, 'learning'])->name('learning');

        // Internal platform job matches (auto-apply to jobs on this platform)
        Route::post('/internal/{match}/approve', function (\App\Models\AgentInternalMatch $match) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            abort_unless($match->user_id === $user->id, 403);
            abort_unless($match->status === 'pending', 422, 'Match already processed.');

            // Check daily limit
            $config = $user->agentConfiguration;
            $dailyLimit   = $config?->daily_application_limit ?? 10;
            $appliedToday = $user->applications()->whereDate('created_at', today())->count();
            if ($appliedToday >= $dailyLimit) {
                return back()->with('error', "Daily application limit ({$dailyLimit}) reached. Try again tomorrow.");
            }

            $matcher = app(\App\Services\Agent\InternalJobMatcherService::class);
            $application = $matcher->applyForMatch($match);

            return back()->with('success', 'Applied to "' . $match->job->title . '"! Application #' . $application->application_number . ' submitted.');
        })->name('internal.approve');

        Route::post('/internal/{match}/skip', function (\App\Models\AgentInternalMatch $match) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            abort_unless($match->user_id === $user->id, 403);
            abort_unless($match->status === 'pending', 422, 'Match already processed.');

            $match->update(['status' => 'skipped']);
            return back()->with('success', 'Match skipped.');
        })->name('internal.skip');

        Route::post('/internal/scan', function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $config = $user->agentConfiguration;
            abort_unless($config?->is_active, 422, 'Agent is not active.');

            \App\Jobs\Agent\ScanInternalJobsJob::dispatch();
            return back()->with('success', 'Scan queued — new matches will appear shortly.');
        })->name('internal.scan');

        Route::post('/internal/rescore', function () {
            /** @var \App\Models\User $user */
            $user   = Auth::user();
            $config = $user->agentConfiguration;
            abort_unless($config !== null, 422, 'No agent configuration found.');

            $service = app(\App\Services\Agent\InternalJobMatcherService::class);
            $rescored = $service->rescoreExisting($user, $config);

            return back()->with('success', "Rescored {$rescored} job matches with the latest AI scoring.");
        })->name('internal.rescore');
    });
});

// Candidate hiring test routes (no auth — access via secure token link in email)
Route::get('/hiring-test/{token}/{stage}', [HiringTestController::class, 'show'])->name('hiring-test.show');
Route::post('/hiring-test/{token}/{stage}/submit', [HiringTestController::class, 'submit'])->name('hiring-test.submit');

// Employer Dashboard & Features (Authenticated Employers)
Route::middleware(['auth', 'employer'])->prefix('employer')->name('employer.')->group(function () {
    // Onboarding / Corporate DNA Setup
    Route::get('/onboarding', [\App\Http\Controllers\Employer\EmployerOnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [\App\Http\Controllers\Employer\EmployerOnboardingController::class, 'save'])->name('onboarding.save');
    Route::post('/onboarding/ai-suggest', [\App\Http\Controllers\Employer\EmployerOnboardingController::class, 'aiSuggest'])->name('onboarding.ai-suggest');

    // Dashboard & Analytics (canonical employer.dashboard is in routes/employer.php)
    Route::get('/', [EmployerDashboardController::class, 'index'])->name('home');
    Route::get('/analytics', [EmployerDashboardController::class, 'analytics'])->name('analytics');
    
    // Job Posting Management
    Route::resource('jobs', JobPostingController::class)->except(['index', 'show']);
    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{id}', [JobPostingController::class, 'show'])->name('jobs.show');
    Route::patch('/jobs/{id}/close', [JobPostingController::class, 'close'])->name('jobs.close');
    Route::patch('/jobs/{id}/reopen', [JobPostingController::class, 'reopen'])->name('jobs.reopen');
    Route::post('/jobs/{id}/duplicate', [JobPostingController::class, 'duplicate'])->name('jobs.duplicate');
    Route::post('/jobs/ai-generate', [JobPostingController::class, 'generateAIContent'])->name('jobs.ai-generate');
    Route::post('/jobs/ai-suggest-rounds', [JobPostingController::class, 'generateRounds'])->name('jobs.ai-suggest-rounds');
    
    // Applicant Tracking System
    Route::get('/applicants', [ApplicantTrackingController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/kanban', [ApplicantTrackingController::class, 'kanban'])->name('applicants.kanban');
    Route::get('/applicants/{id}/ranked', [ApplicantTrackingController::class, 'ranked'])->name('applicants.ranked');
    Route::get('/applicants/{id}/ranked/export', [ApplicantTrackingController::class, 'exportRanked'])->name('applicants.ranked.export');
    Route::get('/applicants/{id}', [ApplicantTrackingController::class, 'show'])->name('applicants.show');
    Route::patch('/applicants/{id}/status', [ApplicantTrackingController::class, 'updateStatus'])->name('applicants.updateStatus');
    Route::patch('/applicants/bulk-status', [ApplicantTrackingController::class, 'bulkUpdateStatus'])->name('applicants.bulkStatus');
    Route::post('/applicants/{id}/note', [ApplicantTrackingController::class, 'addNote'])->name('applicants.addNote');
    Route::post('/applicants/{id}/pipeline-stage', [ApplicantTrackingController::class, 'setPipelineStage'])->name('applicants.setPipelineStage');
    Route::patch('/jobs/{jobId}/evaluation-date', [ApplicantTrackingController::class, 'setJobEvaluationDate'])->name('jobs.setEvaluationDate');
    Route::post('/applicants/export', [ApplicantTrackingController::class, 'export'])->name('applicants.export');

    // Hiring Test Management (employer creates/manages MCQ tests per stage)
    Route::get('/jobs/{jobId}/tests/{stage}/create', [HiringTestManagerController::class, 'create'])->name('tests.create');
    Route::post('/jobs/{jobId}/tests/{stage}', [HiringTestManagerController::class, 'store'])->name('tests.store');
    Route::get('/jobs/{jobId}/tests/{stage}/results', [HiringTestManagerController::class, 'results'])->name('tests.results');
    
    // Company Profile Management
    Route::get('/profile', [CompanyProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [CompanyProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [CompanyProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/logo', [CompanyProfileController::class, 'updateLogo'])->name('profile.updateLogo');
    Route::delete('/profile/logo', [CompanyProfileController::class, 'deleteLogo'])->name('profile.deleteLogo');
    
    // S.C.O.U.T. AI - Employer Intelligence Suite
    Route::prefix('scout')->name('scout.')->group(function () {
        Route::get('/', function () {
            $user = auth()->user();
            $companyId = $user->company_id;
            $dnaProfileData = null;
            if ($companyId) {
                $dnaProfile = \App\Models\CompanyDNAProfile::with(['cultureAnalysis'])
                    ->where('company_id', $companyId)->first();
                if ($dnaProfile) {
                    $dnaProfileData = [
                        'dna_profile' => $dnaProfile,
                        'health_metrics' => [
                            'dna_health_score'  => $dnaProfile->dnaHealthScore ?? 0,
                            'completion_status' => $dnaProfile->completionStatus ?? '—',
                            'confidence_level'  => $dnaProfile->confidenceLevel ?? '—',
                            'data_quality'      => $dnaProfile->dataQualityBadge ?? '—',
                        ],
                        'cultural_insights' => [
                            'archetypes'         => $dnaProfile->culturalArchetypes ?? [],
                            'top_success_traits' => $dnaProfile->topSuccessTraits ?? [],
                        ],
                        'analysis_metadata' => [
                            'last_analyzed'  => $dnaProfile->last_analyzed_at,
                            'needs_refresh'  => $dnaProfile->needsAnalysis(),
                            'can_generate_requirements' => $dnaProfile->canGenerateJobRequirements(),
                        ],
                    ];
                }
            }
            return view('scout.dna-dashboard', compact('dnaProfileData'));
        })->name('dashboard');
        Route::get('/dna', fn() => redirect()->route('scout.dashboard'))->name('dna');
        Route::get('/analyze-culture', fn() => view('scout.analyze-culture'))->name('analyze-culture');
        Route::get('/hiring-insights', fn() => view('scout.hiring-insights'))->name('hiring-insights');
        Route::get('/candidate-matching', fn() => view('scout.candidate-matching'))->name('candidate-matching');
        Route::get('/team-compatibility', fn() => view('scout.team-compatibility'))->name('team-compatibility');
        Route::get('/resume-analysis', fn() => view('scout.resume-analysis'))->name('resume-analysis');

        // Candidate search endpoint (used by candidate-matching page)
        Route::get('/search-candidates', function (\Illuminate\Http\Request $request) {
            $q = trim($request->query('q', ''));
            if (strlen($q) < 1) {
                return response()->json(['data' => []]);
            }
            $candidates = \App\Models\User::where('account_type', 'job_seeker')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                })
                ->select('id', 'name', 'email')
                ->limit(20)
                ->get();
            return response()->json(['data' => $candidates]);
        })->name('search-candidates');
        Route::get('/shortlisting', function () {
            $user = auth()->user();
            $company = $user->company;
            $jobsQuery = \App\Models\Job::withCount(['applications as pending_count' => fn($q) => $q->whereIn('status', ['pending', 'reviewing'])]);
            if ($company) {
                $jobsQuery->where('company_id', $company->id);
            } elseif ($user) {
                // Fallback: show jobs posted by this user, or all jobs in dev
                $jobs = $jobsQuery->where('posted_by', $user->id)->latest()->get(['id', 'title', 'status']);
                if ($jobs->isEmpty()) {
                    $jobs = $jobsQuery->latest()->get(['id', 'title', 'status']);
                }
                return view('scout.automated-shortlisting', compact('jobs'));
            }
            $jobs = $jobsQuery->latest()->get(['id', 'title', 'status']);
            return view('scout.automated-shortlisting', compact('jobs'));
        })->name('shortlisting');
        // JSON endpoint: fetch pending application IDs for a job
        Route::get('/jobs/{jobId}/applications', function (int $jobId) {
            $applications = \App\Models\Application::where('job_id', $jobId)
                ->whereIn('status', ['pending', 'reviewing'])
                ->with('user:id,name,email')
                ->get(['id', 'user_id', 'application_number', 'status']);
            return response()->json(['applications' => $applications]);
        })->name('job.applications');

        // JSON endpoint: run the shortlisting pipeline
        Route::post('/shortlist', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'job_id'          => 'required|integer|exists:job_listings,id',
                'application_ids' => 'required|array|min:1',
                'application_ids.*' => 'integer',
            ]);
            try {
                $service = app(\App\Services\AI\Scout\AutomatedShortlistingService::class);
                $result  = $service->executeShortlistingPipeline(
                    (int) $request->job_id,
                    $request->application_ids
                );

                if ($result['success'] ?? false) {
                    $data = $result['data'] ?? [];

                    // Update shortlisted candidates → status = 'shortlisted' + send notification
                    foreach ($data['shortlisted'] ?? [] as $candidate) {
                        $application = \App\Models\Application::find($candidate['application_id']);
                        if ($application) {
                            $application->update([
                                'status'            => 'shortlisted',
                                'status_updated_at' => now(),
                                'final_rank_score'  => $candidate['overall_score'] ?? null,
                            ]);
                            // Notify the candidate synchronously (bypasses queue — works without a worker)
                            $matchScore = (float) ($candidate['overall_score'] ?? 0);
                            if ($application->user) {
                                try {
                                    $application->user->notifyNow(
                                        new \App\Notifications\CandidateShortlistedNotification(
                                            $application,
                                            $matchScore
                                        )
                                    );
                                } catch (\Exception $e) {
                                    \Log::warning('Shortlist notification failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                                }
                            }
                            // AI emails: candidate + HR (runs immediately without queue worker)
                            try {
                                \App\Jobs\SendHiringEmailsJob::dispatchSync($application, 'shortlisted', $matchScore);
                            } catch (\Throwable $e) {
                                \Log::warning('Shortlist hiring emails failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                            }
                        }
                    }

                    // Reject all candidates that failed any round — store reason + notify
                    $rejectedByRound = $data['rejected_by_round'] ?? [];
                    foreach (['round_1', 'round_2', 'round_3', 'round_4'] as $round) {
                        foreach ($rejectedByRound[$round] ?? [] as $rejected) {
                            $application = \App\Models\Application::with('user', 'job')->find($rejected['application_id'] ?? null);
                            if ($application && !in_array($application->status, ['shortlisted', 'hired', 'rejected'])) {
                                // Build a human-readable rejection reason
                                $concerns = $rejected['reason'] ?? ['Did not meet the minimum criteria for this role'];
                                $reasonText = is_array($concerns)
                                    ? implode('; ', array_filter($concerns))
                                    : (string) $concerns;
                                $roundLabel = 'Round ' . str_replace('round_', '', $round);
                                $score = $rejected['score'] ?? 0;

                                $application->update([
                                    'status'           => 'rejected',
                                    'status_updated_at' => now(),
                                    'rejection_reason' => "Not selected after {$roundLabel} (score: {$score}). {$reasonText}",
                                ]);

                                // Notify the candidate
                                if ($application->user) {
                                    try {
                                        $application->user->notifyNow(
                                            new \App\Notifications\CandidateRejectedNotification($application)
                                        );
                                    } catch (\Exception $e) {
                                        \Log::warning('Rejection notification failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                                    }
                                }
                                // AI rejection emails: candidate + HR
                                try {
                                    \App\Jobs\SendHiringEmailsJob::dispatchSync($application, 'rejected');
                                } catch (\Throwable $e) {
                                    \Log::warning('Rejection hiring emails failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                                }
                            }
                        }
                    }

                    // Also reject any remaining pending/reviewing apps for this job that weren't in the pipeline at all
                    $processedIds = array_column($data['shortlisted'] ?? [], 'application_id');
                    foreach (['round_1','round_2','round_3','round_4'] as $round) {
                        foreach ($rejectedByRound[$round] ?? [] as $r) {
                            $processedIds[] = $r['application_id'] ?? 0;
                        }
                    }
                    if (!empty($processedIds)) {
                        $remaining = \App\Models\Application::with('user','job')
                            ->where('job_id', (int) $request->job_id)
                            ->whereIn('status', ['pending', 'reviewing'])
                            ->whereNotIn('id', $processedIds)
                            ->get();
                        foreach ($remaining as $application) {
                            $application->update([
                                'status'            => 'rejected',
                                'status_updated_at' => now(),
                                'rejection_reason'  => 'Application was not selected for further review.',
                            ]);
                            if ($application->user) {
                                try {
                                    $application->user->notifyNow(
                                        new \App\Notifications\CandidateRejectedNotification($application)
                                    );
                                } catch (\Exception $e) {
                                    \Log::warning('Remaining rejection notification failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                                }
                            }
                            // AI rejection emails for remaining apps
                            try {
                                \App\Jobs\SendHiringEmailsJob::dispatchSync($application, 'rejected');
                            } catch (\Throwable $e) {
                                \Log::warning('Remaining rejection hiring emails failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);
                            }
                        }
                    }
                }

                return response()->json($result);
            } catch (\Exception $e) {
                \Log::error('Shortlisting pipeline failed', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Shortlisting failed: ' . $e->getMessage()], 500);
            }
        })->name('shortlist');

        Route::get('/assessment', fn() => view('scout.adaptive-assessment'))->name('assessment');
        Route::get('/behavioral', fn() => view('scout.behavioral-intelligence'))->name('behavioral');
        Route::get('/bias-elimination', fn() => view('scout.bias-elimination'))->name('bias-elimination');
        Route::get('/predictive', fn() => view('scout.predictive-analytics'))->name('predictive');
        Route::get('/learning', fn() => view('scout.continuous-learning'))->name('learning');
    });

    // Interview Management — Full 5-Phase Pipeline
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/',                                 [InterviewManagementController::class, 'index'])->name('index');
        Route::get('/schedule/{application}',           [InterviewManagementController::class, 'scheduleForm'])->name('schedule');
        Route::post('/',                                [InterviewManagementController::class, 'store'])->name('store');
        Route::get('/{id}',                             [InterviewManagementController::class, 'show'])->name('show');
        Route::post('/{id}/scores',                     [InterviewManagementController::class, 'saveScore'])->name('scores');
        Route::get('/{id}/evaluate',                    [InterviewManagementController::class, 'evaluate'])->name('evaluate');
        Route::post('/{id}/evaluate',                   [InterviewManagementController::class, 'submitEvaluation'])->name('evaluate.submit');
        Route::get('/{id}/decide',                      [InterviewManagementController::class, 'decideForm'])->name('decide');
        Route::post('/{id}/decide',                     [InterviewManagementController::class, 'submitDecision'])->name('decide.submit');
        Route::patch('/{id}/complete',                  [InterviewManagementController::class, 'complete'])->name('complete');
        Route::patch('/{id}/cancel',                    [InterviewManagementController::class, 'cancel'])->name('cancel');
    });

    // ── Orin™ Conversational Employer Onboarding ──────────────────────────
    Route::get('/orin-onboarding', [\App\Http\Controllers\Employer\OrinOnboardingController::class, 'show'])->name('orin-onboarding');
    Route::post('/orin-onboarding/chat', [\App\Http\Controllers\Employer\OrinOnboardingController::class, 'chat'])->name('orin-onboarding.chat');
    Route::post('/orin-onboarding/skip', [\App\Http\Controllers\Employer\OrinOnboardingController::class, 'skip'])->name('orin-onboarding.skip');

    // ── Orin™ AI Job Creator ──────────────────────────────────────────────
    Route::get('/create-job', [\App\Http\Controllers\Employer\OrinJobCreatorController::class, 'show'])->name('orin-job-creator');
    Route::post('/create-job/chat', [\App\Http\Controllers\Employer\OrinJobCreatorController::class, 'chat'])->name('orin-job-creator.chat');
    Route::post('/create-job/quick-post', [\App\Http\Controllers\Employer\OrinJobCreatorController::class, 'quickPost'])->name('orin-job-creator.quick-post');
    Route::get('/create-job/my-jobs', [\App\Http\Controllers\Employer\OrinJobCreatorController::class, 'myJobs'])->name('orin-job-creator.my-jobs');
});

// AI Negotiation Strategist Routes (Authenticated Job Seekers)
Route::middleware(['auth', 'verified'])->prefix('negotiation')->name('negotiation.')->group(function () {
    // Dashboard - Main negotiation hub with strategy overview
    Route::get('/dashboard', function() {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $activeStrategies = $user->strategies()->count();
        $totalSessions = $user->negotiationSessions()->count();
        
        // Calculate average salary gain from completed strategies
        $completedStrategies = $user->strategies()
            ->whereNotNull('actual_outcome')
            ->get();
        
        $avgGainPercent = $completedStrategies->count() > 0 
            ? round($completedStrategies->avg('potential_gain_percentage'), 1)
            : 0;
        
        // Calculate success rate (strategies where actual >= optimal ask)
        $successfulCount = $completedStrategies->filter(function($strategy) {
            return $strategy->actual_outcome >= $strategy->optimal_ask;
        })->count();
        
        $successRate = $completedStrategies->count() > 0
            ? round(($successfulCount / $completedStrategies->count()) * 100)
            : 0;
        
        // Get active coaching sessions
        $activeSessions = $user->negotiationSessions()
            ->whereNull('outcome')
            ->count();
        
        // Get recent strategies with relationships
        $strategies = $user->strategies()
            ->with(['scenarios', 'scripts', 'sessions'])
            ->latest()
            ->paginate(10);
        
        $completedStrategiesCount = $completedStrategies->count();
        $totalValueGained = '₹0';

        return view('negotiation.dashboard', compact(
            'activeStrategies',
            'totalSessions',
            'completedStrategiesCount',
            'avgGainPercent',
            'successRate',
            'activeSessions',
            'strategies',
            'totalValueGained'
        ));
    })->name('dashboard');
    
    // Strategy Detail - Comprehensive strategy analysis with visualizations
    Route::get('/strategy/{id}', function($id) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $strategy = $user->strategies()->findOrFail($id);
        
        // Calculate readiness score based on completed components
        $readinessScore = 0;

        // Market Research — up to 20pts based on data completeness
        $marketPts = 0;
        if ($strategy->market_median)          $marketPts += 5;
        if ($strategy->market_percentile_25)   $marketPts += 5;
        if ($strategy->market_percentile_75)   $marketPts += 5;
        if ($strategy->market_percentile_90)   $marketPts += 5;

        // Negotiation Leverage — based on number of leverage points stored (4pts each, max 20)
        $leverageItems = count($strategy->strongest_points ?? []) + count($strategy->value_propositions ?? []);
        $leveragePts = min(20, $leverageItems * 4);

        // Scenarios — 3+ = full score
        $scenarioPts = min(20, (int) round(($strategy->scenarios->count() / 3) * 20));
        $scenarioStatus = $strategy->scenarios->count() >= 3 ? 'complete' : ($strategy->scenarios->count() > 0 ? 'partial' : 'pending');

        // Scripts — 3+ = full score
        $scriptPts = min(20, (int) round(($strategy->scripts->count() / 3) * 20));
        $scriptStatus = $strategy->scripts->count() >= 3 ? 'complete' : ($strategy->scripts->count() > 0 ? 'partial' : 'pending');

        // Company Intelligence — culture + manager perspective + flexibility
        $companyPts = 0;
        if ($strategy->company_culture_analysis)         $companyPts += 10;
        if ($strategy->hiring_manager_perspective)       $companyPts += 5;
        if ($strategy->company_negotiation_flexibility)  $companyPts += 5;

        $readinessFactors = [
            [
                'name' => 'Market Research',
                'points' => $marketPts,
                'max_points' => 20,
                'status' => $marketPts >= 20 ? 'complete' : ($marketPts > 0 ? 'partial' : 'pending')
            ],
            [
                'name' => 'Negotiation Leverage',
                'points' => $leveragePts,
                'max_points' => 20,
                'status' => $leveragePts >= 20 ? 'complete' : ($leveragePts > 0 ? 'partial' : 'pending')
            ],
            [
                'name' => 'Scenarios Prepared',
                'points' => $scenarioPts,
                'max_points' => 20,
                'status' => $scenarioStatus,
            ],
            [
                'name' => 'Scripts Ready',
                'points' => $scriptPts,
                'max_points' => 20,
                'status' => $scriptStatus,
            ],
            [
                'name' => 'Company Intelligence',
                'points' => $companyPts,
                'max_points' => 20,
                'status' => $companyPts >= 20 ? 'complete' : ($companyPts > 0 ? 'partial' : 'pending')
            ]
        ];
        
        $readinessScore = collect($readinessFactors)->sum('points');
        
        // Build leverage analysis for radar chart
        $leverageAnalysis = [
            'market_position' => min(100, ($strategy->market_position_percentile ?? 50) * 1.5),
            'experience' => min(100, ($strategy->experience_years ?? 0) * 5),
            'skills' => min(100, (count($strategy->skills ?? []) * 10)),
            'alternatives' => ($strategy->has_other_offers ?? false) ? 80 : (($strategy->is_currently_employed ?? false) ? 50 : 20)
        ];
        
        return view('negotiation.strategy', compact('strategy', 'readinessScore', 'readinessFactors', 'leverageAnalysis'));
    })->name('strategy');
    
    // Scenarios - Scenario comparison and risk/reward analysis
    Route::get('/scenarios/{id}', function($id) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $strategy = $user->strategies()->findOrFail($id);
        $scenarios = $strategy->scenarios()->orderBy('risk_level', 'asc')->get();
        
        return view('negotiation.scenarios', compact('strategy', 'scenarios'));
    })->name('scenarios');
    
    // Scripts - Script library with personalization tools
    Route::get('/scripts/{id}', function($id) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $strategy = $user->strategies()->findOrFail($id);

        // Auto-generate scripts if none exist and scenarios are available
        if ($strategy->scripts()->count() === 0) {
            $scenario = $strategy->scenarios()->first();
            if ($scenario) {
                try {
                    $scriptService = app(\App\Services\AI\NegotiationScriptService::class);
                    $scriptService->generateScripts($strategy, $scenario);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Script auto-generation failed', [
                        'strategy_id' => $strategy->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $scripts = $strategy->scripts()->orderBy('script_type', 'asc')->get();

        return view('negotiation.scripts', compact('strategy', 'scripts'));
    })->name('scripts');
    
    // Coaching - Real-time negotiation coaching session
    Route::get('/coaching/{sessionId}', function($sessionId) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $session = $user->negotiationSessions()->findOrFail($sessionId);
        $strategy = $session->strategy;
        $messages = $session->messages()->orderBy('created_at', 'asc')->get();
        
        return view('negotiation.coaching', compact('session', 'strategy', 'messages'));
    })->name('coaching');

    // Active Coaching Sessions - View all active negotiation sessions
    Route::get('/coaching-sessions', function() {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $activeSessions = $user->negotiationSessions()
            ->where('status', 'active')
            ->with('strategy')
            ->latest()
            ->paginate(10);
        
        return view('negotiation.sessions', compact('activeSessions'));
    })->name('coaching.active');
    
    // Tactics Library - Browse negotiation tactics and frameworks
    Route::get('/tactics', function() {
        $tactics = \App\Models\NegotiationTactic::orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('negotiation.tactics', compact('tactics'));
    })->name('tactics');

    // AI Salary Negotiation Chatbot
    Route::get('/chatbot', function() {
        return view('negotiation.chatbot');
    })->name('chatbot');

    // Chatbot API endpoint — POST /negotiation/chat
    Route::post('/chat', function(\Illuminate\Http\Request $request) {
        $request->validate(['message' => 'required|string|max:2000', 'history' => 'nullable|array']);

        $history = $request->input('history', []);
        $userMessage = $request->input('message');

        $contextLines = '';
        foreach (array_slice($history, -6) as $h) {
            $role = $h['role'] === 'user' ? 'User' : 'Assistant';
            $contextLines .= "{$role}: {$h['content']}\n";
        }

        $systemPrompt = <<<'SYSTEM'
You are an expert AI Salary Negotiation Coach for the Indian job market. You are conversational, warm, and to-the-point.

RESPONSE RULES — follow these strictly:
1. Keep every reply SHORT — max 4-5 bullet points or 3-4 sentences. No long essays.
2. Use LPA (Lakhs Per Annum) for all salary figures, never USD.
3. Give ONE clear, actionable tip or script — not a list of everything possible.
4. Be direct and specific — mention Indian cities, companies, and norms when relevant.

FORMAT RULE: After your reply (after a blank line), always add this exact line:
FOLLOWUPS: [question the user might ask you next 1?] | [question the user might ask you next 2?] | [question the user might ask you next 3?]

The FOLLOWUPS must be questions the USER would ask YOU (the coach) — things like "How do I counter if they say budget is fixed?", "What exact script should I use?", "Should I mention a competing offer?" — NOT questions you are asking the user.
SYSTEM;

        $fullPrompt = $contextLines ? "Conversation so far:\n{$contextLines}\nUser: {$userMessage}" : $userMessage;

        try {
            set_time_limit(120);
            $service = new class {
                use \App\Traits\InteractsWithAI;
                public function chat(string $prompt, string $system, array $options = []): string
                {
                    return $this->ai($prompt, $system, $options);
                }
            };
            $raw = $service->chat($fullPrompt, $systemPrompt, [
                'temperature' => 0.7,
                'max_tokens'  => 250,
                'timeout'     => 45,
            ]);

            // Parse follow-up questions out of the response
            $followUps = [];
            $reply = $raw;
            if (preg_match('/FOLLOWUPS:\s*(.+)$/mi', $raw, $m)) {
                $reply = trim(preg_replace('/[\n\r]*FOLLOWUPS:\s*.+$/mi', '', $raw));
                $parsed = array_values(array_filter(array_map(function($q) {
                    return trim(trim(trim($q), '[]'), '?') . '?';
                }, explode('|', $m[1])), fn($q) => strlen($q) > 5));
                if (count($parsed) >= 2) {
                    $followUps = array_slice($parsed, 0, 3);
                }
            }

            // Always provide follow-ups — generate topic-based fallbacks if AI didn't include them
            if (empty($followUps)) {
                $msg = strtolower($userMessage);
                if (str_contains($msg, 'email') || str_contains($msg, 'script') || str_contains($msg, 'write')) {
                    $followUps = ['Can you make it more assertive?', 'Add a deadline to this email?', 'Write a follow-up if they don\'t respond?'];
                } elseif (str_contains($msg, 'salary') || str_contains($msg, 'lpa') || str_contains($msg, 'offer')) {
                    $followUps = ['How much can I realistically push for?', 'What if they say the budget is fixed?', 'Should I mention a competing offer?'];
                } elseif (str_contains($msg, 'counter') || str_contains($msg, 'pushback') || str_contains($msg, 'best offer')) {
                    $followUps = ['What exact words should I use?', 'How many times can I counter?', 'When should I stop negotiating?'];
                } elseif (str_contains($msg, 'benefit') || str_contains($msg, 'bonus') || str_contains($msg, 'equity')) {
                    $followUps = ['How do I ask for a signing bonus?', 'Can I negotiate WFH days?', 'What if they won\'t move on salary but offer stock?'];
                } elseif (str_contains($msg, 'ctc') || str_contains($msg, 'current') || str_contains($msg, 'package')) {
                    $followUps = ['How do I justify a 30% hike?', 'Should I reveal my current CTC?', 'How do I handle the HR salary form?'];
                } else {
                    $followUps = ['How do I say this professionally?', 'What\'s the market rate for my role?', 'How do I handle their counter-offer?'];
                }
            }

            return response()->json(['reply' => $reply, 'followUps' => $followUps]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Negotiation chatbot error', ['error' => $e->getMessage()]);
            return response()->json(['reply' => 'I apologize, I\'m having trouble connecting right now. Please try again in a moment.', 'followUps' => []], 200);
        }
    })->name('chat');
});

// Company Reviews & Ratings Routes (Public & Authenticated)
Route::prefix('companies')->name('companies.')->group(function () {
    // Public company pages
    Route::get('/', [\App\Http\Controllers\CompanyReviewController::class, 'index'])->name('index');
    Route::get('/{company:slug}', [\App\Http\Controllers\CompanyReviewController::class, 'show'])->name('show');
    Route::get('/{company:slug}/reviews', [\App\Http\Controllers\CompanyReviewController::class, 'reviews'])->name('reviews');
    Route::get('/{company:slug}/salaries', [\App\Http\Controllers\CompanyReviewController::class, 'salaries'])->name('salaries');
    Route::get('/{company:slug}/interviews', [\App\Http\Controllers\CompanyReviewController::class, 'interviews'])->name('interviews');
    Route::get('/{company:slug}/jobs', [\App\Http\Controllers\CompanyReviewController::class, 'jobs'])->name('jobs');
    
    // Authenticated routes for submitting reviews
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/{company:slug}/reviews/create', [\App\Http\Controllers\CompanyReviewController::class, 'createReview'])->name('reviews.create');
        Route::get('/{company:slug}/salaries/create', [\App\Http\Controllers\CompanyReviewController::class, 'createSalary'])->name('salaries.create');
        Route::get('/{company:slug}/interviews/create', [\App\Http\Controllers\CompanyReviewController::class, 'createInterview'])->name('interviews.create');
        
        // Toggle follow/unfollow company
        Route::post('/{company:slug}/follow', [\App\Http\Controllers\CompanyReviewController::class, 'toggleFollow'])->name('follow');
    });
});

// Video Interview Routes (Authenticated Job Seekers)
Route::middleware(['auth', 'verified'])->prefix('video-interview')->name('video-interview.')->group(function () {
    // Session Management
    Route::get('/', \App\Livewire\VideoInterview\SessionList::class)->name('sessions');
    Route::get('/create', \App\Livewire\VideoInterview\CreateMockInterview::class)->name('create');
    Route::get('/record/{session}', \App\Livewire\VideoInterview\VideoRecorder::class)->name('record');
    Route::get('/results/{session}', \App\Livewire\VideoInterview\SessionResults::class)->name('results');
    
    // API Endpoints
    Route::get('/api/session/{session}/upload-url', [\App\Http\Controllers\VideoInterviewController::class, 'getUploadUrl'])->name('api.upload-url');
    Route::post('/api/session/{session}/upload', [\App\Http\Controllers\VideoInterviewController::class, 'uploadVideo'])->name('api.upload');
    Route::get('/api/session/{session}/analysis', [\App\Http\Controllers\VideoInterviewController::class, 'getAnalysis'])->name('api.analysis');
    Route::get('/api/recording/{recording}/playback-url', [\App\Http\Controllers\VideoInterviewController::class, 'getPlaybackUrl'])->name('api.playback-url');
    Route::post('/api/recording/{recording}/client-analysis', [\App\Http\Controllers\VideoInterviewController::class, 'submitClientAnalysis'])->name('api.client-analysis');
    
    // Invitation handling
    Route::get('/invitation/{invitation:token}', function(\App\Models\VideoInterviewInvitation $invitation) {
        return view('video-interview.invitation', compact('invitation'));
    })->name('invitation');
    Route::post('/invitation/{invitation}/accept', [\App\Http\Controllers\VideoInterviewController::class, 'acceptInvitation'])->name('invitation.accept');
    Route::post('/invitation/{invitation}/decline', [\App\Http\Controllers\VideoInterviewController::class, 'declineInvitation'])->name('invitation.decline');
});

// Mobile PWA Routes
Route::middleware(['auth'])->prefix('mobile')->name('mobile.')->group(function () {
    // Swipe Job Browser (Tinder-style)
    Route::get('/swipe', \App\Livewire\Mobile\SwipeJobBrowser::class)->name('swipe');
    
    // Saved Jobs (Offline-capable)
    Route::get('/saved', function () {
        return view('mobile.saved-jobs');
    })->name('saved');
    
    // Quick Apply Modal (included as component)
    
    // Push Notification Management
    Route::post('/push/subscribe', function (\Illuminate\Http\Request $request) {
        $service = app(\App\Services\PushNotificationService::class);
        $subscription = $service->subscribe(auth()->user(), $request->all());
        return response()->json(['success' => true, 'id' => $subscription->id]);
    })->name('push.subscribe');
    
    Route::post('/push/unsubscribe', function (\Illuminate\Http\Request $request) {
        $service = app(\App\Services\PushNotificationService::class);
        $service->unsubscribe(auth()->user(), $request->input('endpoint'));
        return response()->json(['success' => true]);
    })->name('push.unsubscribe');
    
    // Offline Data Sync
    Route::post('/sync/jobs', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $jobs = $request->input('jobs', []);
        
        foreach ($jobs as $job) {
            if ($job['action'] === 'save') {
                $user->savedJobs()->syncWithoutDetaching([$job['id']]);
            }
        }
        
        return response()->json(['success' => true, 'synced' => count($jobs)]);
    })->name('sync.jobs');
    
    Route::post('/sync/applications', function (\Illuminate\Http\Request $request) {
        // Handle offline application syncing
        return response()->json(['success' => true]);
    })->name('sync.applications');
});

// Mobile API for offline-first features
Route::middleware(['auth'])->prefix('api/mobile')->name('api.mobile.')->group(function () {
    // Get user's saved jobs for offline caching
    Route::get('/saved-jobs', function () {
        $jobs = auth()->user()->savedJobs()
            ->with('company:id,name,logo')
            ->select(['id', 'title', 'company_id', 'location', 'salary_min', 'salary_max', 'job_type', 'is_remote', 'created_at'])
            ->get();
        return response()->json($jobs);
    })->name('saved-jobs');
    
    // Get user's primary resume for offline quick apply
    Route::get('/primary-resume', function () {
        $resume = auth()->user()->resumes()->where('is_primary', true)->first();
        return response()->json($resume);
    })->name('primary-resume');
    
    // Get user profile data for offline access
    Route::get('/profile-data', function () {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar_url,
            'profile_completion' => $user->profile_completion ?? 0,
        ]);
    })->name('profile-data');
    
    // Get VAPID public key for push subscription
    Route::get('/vapid-key', function () {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    })->name('vapid-key');
});

// Professional Networking Routes (Candidate/Job Seeker)
Route::middleware(['auth', 'verified'])->prefix('network')->name('network.')->group(function () {
    // Activity Feed
    Route::get('/', function () {
        return view('network.feed');
    })->name('feed');
    
    // Connections
    Route::get('/connections', function () {
        return view('network.connections');
    })->name('connections');
    
    // Messaging
    Route::get('/messages', function () {
        return view('network.messages');
    })->name('messages');
    
    Route::get('/messages/{conversation}', function ($conversation) {
        $conversationModel = \App\Models\NetworkConversation::findOrFail($conversation);
        return view('network.messages', ['selectedConversation' => $conversationModel]);
    })->name('messages.show');
    
    // Groups
    Route::get('/groups', function () {
        return view('network.groups');
    })->name('groups');
    
    Route::get('/groups/{group}', function ($group) {
        $groupModel = \App\Models\Group::findOrFail($group);
        return view('network.group-detail', ['group' => $groupModel]);
    })->name('groups.show');
    
    // Events
    Route::get('/events', function () {
        return view('network.events');
    })->name('events');
    
    Route::get('/events/{event:slug}', function ($event) {
        $eventModel = \App\Models\NetworkEvent::where('slug', $event)->firstOrFail();
        return view('network.event-detail', ['event' => $eventModel]);
    })->name('events.show');
    
    // Mentorship
    Route::get('/mentorship', function () {
        return view('network.mentorship');
    })->name('mentorship');
    
    // Profile View (public-ish, for other users to view)
    Route::get('/profile/{user}', function ($user) {
        $userModel = \App\Models\User::findOrFail($user);
        return view('network.profile', ['user' => $userModel]);
    })->name('profile.show');
});

// Calendar Integration Routes (Job Seekers & Employers)
Route::middleware(['auth', 'verified'])->prefix('calendar')->name('calendar.')->group(function () {
    // Calendar Dashboard
    Route::get('/', [\App\Http\Controllers\CalendarController::class, 'index'])->name('dashboard');
    
    // Calendar Connections
    Route::get('/connect/{provider}', [\App\Http\Controllers\CalendarController::class, 'connect'])->name('connect')
        ->whereIn('provider', ['google', 'microsoft', 'apple']);
    Route::get('/callback/{provider}', [\App\Http\Controllers\CalendarController::class, 'callback'])->name('callback')
        ->whereIn('provider', ['google', 'microsoft', 'apple']);
    Route::delete('/disconnect/{connection}', [\App\Http\Controllers\CalendarController::class, 'disconnect'])->name('disconnect');
    
    // Events Management
    Route::get('/events', [\App\Http\Controllers\CalendarController::class, 'events'])->name('events');
    Route::post('/events', [\App\Http\Controllers\CalendarController::class, 'createEvent'])->name('events.create');
    Route::put('/events/{event}', [\App\Http\Controllers\CalendarController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\CalendarController::class, 'deleteEvent'])->name('events.delete');
    
    // Availability Management
    Route::get('/availability', [\App\Http\Controllers\CalendarController::class, 'availability'])->name('availability');
    Route::post('/availability', [\App\Http\Controllers\CalendarController::class, 'updateAvailability'])->name('availability.update');
    
    // Scheduling Links (Calendly-style)
    Route::get('/scheduling-links', [\App\Http\Controllers\CalendarController::class, 'schedulingLinks'])->name('scheduling-links');
    Route::post('/scheduling-links', [\App\Http\Controllers\CalendarController::class, 'createSchedulingLink'])->name('scheduling-links.create');
    Route::put('/scheduling-links/{link}', [\App\Http\Controllers\CalendarController::class, 'updateSchedulingLink'])->name('scheduling-links.update');
    Route::delete('/scheduling-links/{link}', [\App\Http\Controllers\CalendarController::class, 'deleteSchedulingLink'])->name('scheduling-links.delete');
});

// Public Scheduling Routes (No Auth Required - for booking meetings)
Route::prefix('schedule')->name('schedule.')->group(function () {
    // View scheduling link and available times
    Route::get('/{link:slug}', [\App\Http\Controllers\SchedulingController::class, 'show'])->name('show');
    Route::get('/{link:slug}/times', [\App\Http\Controllers\SchedulingController::class, 'getAvailableTimes'])->name('times');
    Route::post('/{link:slug}/book', [\App\Http\Controllers\SchedulingController::class, 'book'])->name('book');
    Route::get('/confirmation/{event}', [\App\Http\Controllers\SchedulingController::class, 'confirmation'])->name('confirmation');
});

// ATS Integration Routes (Employers)
Route::middleware(['auth', 'employer'])->prefix('employer/ats')->name('ats.')->group(function () {
    // Dashboard & Connection Management
    Route::get('/', [\App\Http\Controllers\AtsController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\AtsController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\AtsController::class, 'store'])->name('store');
    Route::get('/{connection}', [\App\Http\Controllers\AtsController::class, 'show'])->name('show');
    Route::put('/{connection}', [\App\Http\Controllers\AtsController::class, 'update'])->name('update');
    Route::delete('/{connection}', [\App\Http\Controllers\AtsController::class, 'destroy'])->name('destroy');
    
    // Sync & Testing
    Route::post('/{connection}/sync', [\App\Http\Controllers\AtsController::class, 'sync'])->name('sync');
    Route::get('/{connection}/test', [\App\Http\Controllers\AtsController::class, 'testConnection'])->name('test');
    
    // Data Views
    Route::get('/{connection}/logs', [\App\Http\Controllers\AtsController::class, 'syncLogs'])->name('logs');
    Route::get('/{connection}/candidates', [\App\Http\Controllers\AtsController::class, 'candidates'])->name('candidates');
    Route::get('/{connection}/jobs', [\App\Http\Controllers\AtsController::class, 'jobs'])->name('jobs');
});

// ATS OAuth Callback (No Auth Middleware - callback from ATS)
Route::get('/ats/callback/{provider}', [\App\Http\Controllers\AtsController::class, 'callback'])->name('ats.callback');

// ATS Webhook Endpoints (No Auth - authenticated via webhook signature)
Route::post('/webhooks/ats/{provider}/{connectionId}', [\App\Http\Controllers\AtsController::class, 'webhook'])->name('ats.webhook');

// Offer Letter Routes (Employers)
Route::middleware(['auth', 'employer'])->prefix('employer/offers')->name('offer-letters.')->group(function () {
    // Offer Management
    Route::get('/', [OfferLetterController::class, 'index'])->name('index');
    Route::get('/create', [OfferLetterController::class, 'create'])->name('create');
    Route::post('/', [OfferLetterController::class, 'store'])->name('store');
    Route::get('/statistics', [OfferLetterController::class, 'statistics'])->name('statistics');
    Route::get('/{offerLetter}', [OfferLetterController::class, 'show'])->name('show');
    Route::get('/{offerLetter}/edit', [OfferLetterController::class, 'edit'])->name('edit');
    Route::put('/{offerLetter}', [OfferLetterController::class, 'update'])->name('update');
    
    // Offer Actions
    Route::post('/{offerLetter}/send', [OfferLetterController::class, 'send'])->name('send');
    Route::get('/{offerLetter}/download', [OfferLetterController::class, 'download'])->name('download');
    Route::post('/{offerLetter}/withdraw', [OfferLetterController::class, 'withdraw'])->name('withdraw');
    Route::get('/{offerLetter}/activities', [OfferLetterController::class, 'activities'])->name('activities');
    
    // Digital Signature
    Route::post('/{offerLetter}/signature', [OfferLetterController::class, 'requestSignature'])->name('signature.request');
    Route::get('/{offerLetter}/signature/status', [OfferLetterController::class, 'signatureStatus'])->name('signature.status');
    
    // Counter Offers (Employer Response)
    Route::post('/counter-offers/{counterOffer}/respond', [OfferLetterController::class, 'respondToCounterOffer'])->name('counter-offers.respond');
    
    // Templates Management
    Route::get('/templates/list', [OfferLetterController::class, 'templates'])->name('templates.index');
    Route::get('/templates/{template}', [OfferLetterController::class, 'showTemplate'])->name('templates.show');
    Route::post('/templates', [OfferLetterController::class, 'storeTemplate'])->name('templates.store');
    
    // Benefits Packages
    Route::get('/benefits/list', [OfferLetterController::class, 'benefitsPackages'])->name('benefits.index');
    Route::post('/benefits', [OfferLetterController::class, 'storeBenefitsPackage'])->name('benefits.store');
    Route::get('/benefits/template', [OfferLetterController::class, 'benefitsTemplate'])->name('benefits.template');
});

// Candidate Offer Letter Routes
Route::middleware('auth')->prefix('my-offers')->name('candidate.offers.')->group(function () {
    Route::get('/', [OfferLetterController::class, 'index'])->name('index');
    Route::get('/{offerLetter}', [OfferLetterController::class, 'show'])->name('show');
    Route::get('/{offerLetter}/download', [OfferLetterController::class, 'download'])->name('download');
    
    // Offer Response
    Route::post('/{offerLetter}/accept', [OfferLetterController::class, 'accept'])->name('accept');
    Route::post('/{offerLetter}/decline', [OfferLetterController::class, 'decline'])->name('decline');
    Route::post('/{offerLetter}/counter-offer', [OfferLetterController::class, 'counterOffer'])->name('counter-offer');
    
    // AI Analysis
    Route::get('/{offerLetter}/analyze', [OfferLetterController::class, 'analyze'])->name('analyze');
    Route::get('/{offerLetter}/suggest-counter', [OfferLetterController::class, 'suggestCounterOffer'])->name('suggest-counter');
    
    // Offer Comparison
    Route::get('/compare/tool', [OfferLetterController::class, 'comparison'])->name('comparison');
    Route::post('/compare', [OfferLetterController::class, 'createComparison'])->name('comparison.create');
    Route::get('/compare/{comparison}', [OfferLetterController::class, 'comparisonReport'])->name('comparison.report');
});

// Background Check Routes (Employers)
Route::middleware(['auth', 'employer'])->prefix('employer/background-checks')->name('background-checks.')->group(function () {
    // Main Management
    Route::get('/', [\App\Http\Controllers\BackgroundCheckController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\BackgroundCheckController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\BackgroundCheckController::class, 'store'])->name('store');
    Route::get('/statistics', [\App\Http\Controllers\BackgroundCheckController::class, 'statistics'])->name('statistics');
    Route::get('/{backgroundCheck}', [\App\Http\Controllers\BackgroundCheckController::class, 'show'])->name('show');
    
    // Consent Management
    Route::post('/{backgroundCheck}/send-consent', [\App\Http\Controllers\BackgroundCheckController::class, 'sendConsent'])->name('send-consent');
    Route::post('/{backgroundCheck}/resend-consent', [\App\Http\Controllers\BackgroundCheckController::class, 'resendConsent'])->name('resend-consent');
    
    // Actions
    Route::post('/{backgroundCheck}/cancel', [\App\Http\Controllers\BackgroundCheckController::class, 'cancel'])->name('cancel');
    Route::get('/{backgroundCheck}/download', [\App\Http\Controllers\BackgroundCheckController::class, 'download'])->name('download');
    Route::post('/{backgroundCheck}/recheck', [\App\Http\Controllers\BackgroundCheckController::class, 'recheck'])->name('recheck');
    Route::patch('/{backgroundCheck}/notes', [\App\Http\Controllers\BackgroundCheckController::class, 'updateNotes'])->name('notes.update');
    
    // Adverse Action (FCRA Compliance)
    Route::post('/{backgroundCheck}/adverse-action', [\App\Http\Controllers\BackgroundCheckController::class, 'initiateAdverseAction'])->name('adverse-action');
    Route::post('/{backgroundCheck}/adverse-action/final', [\App\Http\Controllers\BackgroundCheckController::class, 'sendFinalAdverseAction'])->name('adverse-action.final');
    Route::post('/{backgroundCheck}/adverse-action/withdraw', [\App\Http\Controllers\BackgroundCheckController::class, 'withdrawAdverseAction'])->name('adverse-action.withdraw');
    
    // Packages
    Route::get('/packages/list', [\App\Http\Controllers\BackgroundCheckController::class, 'packages'])->name('packages');
    Route::post('/packages', [\App\Http\Controllers\BackgroundCheckController::class, 'storePackage'])->name('packages.store');
});

// Candidate Consent Routes (Signed URL - No Auth Required)
Route::prefix('background-check-consent')->name('background-check-consent.')->group(function () {
    Route::get('/{backgroundCheck}', [\App\Http\Controllers\BackgroundCheckController::class, 'consent'])
        ->name('show')
        ->middleware('signed');
    Route::post('/{backgroundCheck}', [\App\Http\Controllers\BackgroundCheckController::class, 'submitConsent'])
        ->name('submit')
        ->middleware('signed');
});

// Background Check Webhook (No Auth - authenticated via signature)
Route::post('/webhooks/background-check/{provider}', [\App\Http\Controllers\BackgroundCheckController::class, 'webhook'])
    ->name('background-check.webhook');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Application Monitoring
    Route::get('/applications/monitor', [ApplicationMonitorController::class, 'index'])->name('applications.monitor');
    Route::get('/applications/{application}', [ApplicationMonitorController::class, 'show'])->name('applications.show');
    Route::get('/applications/export', [ApplicationMonitorController::class, 'export'])->name('applications.export');
    Route::post('/applications/bulk-update', [ApplicationMonitorController::class, 'bulkUpdate'])->name('applications.bulkUpdate');

    // Agent Administration - Emergency Controls
    Route::prefix('agent')->name('agent.')->group(function () {
        // Global kill switch
        Route::post('/kill-all', [\App\Http\Controllers\Admin\AgentAdminController::class, 'killAll'])
            ->middleware('throttle:5,60') // Max 5 kill-all actions per hour
            ->name('kill-all');
        Route::post('/resume-all', [\App\Http\Controllers\Admin\AgentAdminController::class, 'resumeAll'])
            ->middleware('throttle:5,60')
            ->name('resume-all');
        Route::get('/status', [\App\Http\Controllers\Admin\AgentAdminController::class, 'status'])
            ->name('status');
        Route::get('/list', [\App\Http\Controllers\Admin\AgentAdminController::class, 'list'])
            ->name('list');

        // Individual agent controls
        Route::post('/{userId}/stop', [\App\Http\Controllers\Admin\AgentAdminController::class, 'stopAgent'])
            ->middleware('throttle:30,1')
            ->name('stop');
        Route::post('/{userId}/resume', [\App\Http\Controllers\Admin\AgentAdminController::class, 'resumeAgent'])
            ->middleware('throttle:30,1')
            ->name('resume');
    });
});

// ============================================================
// TALENT MARKETPLACE ROUTES
// ============================================================

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\FreelancerController;
use App\Http\Controllers\Marketplace\EmployerController as MarketplaceEmployerController;
use App\Http\Controllers\Marketplace\ContractController;

Route::prefix('marketplace')->name('marketplace.')->group(function () {
    // Public Routes
    Route::get('/', [MarketplaceController::class, 'index'])->name('index');
    Route::get('/projects', [MarketplaceController::class, 'projects'])->name('projects');
    Route::get('/projects/{project}', [MarketplaceController::class, 'project'])->name('project.show');
    Route::get('/freelancers', [MarketplaceController::class, 'freelancers'])->name('freelancers');
    Route::get('/freelancers/{profile}', [MarketplaceController::class, 'freelancer'])->name('freelancer.show');
    Route::get('/categories', [MarketplaceController::class, 'categories'])->name('categories');

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        // Freelancer Dashboard & Profile
        Route::prefix('freelancer')->name('freelancer.')->group(function () {
            Route::get('/dashboard', [FreelancerController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [FreelancerController::class, 'profile'])->name('profile');
            Route::post('/profile', [FreelancerController::class, 'updateProfile'])->name('profile.update');
            Route::get('/proposals', [FreelancerController::class, 'proposals'])->name('proposals');
            Route::get('/contracts', [FreelancerController::class, 'activeContracts'])->name('contracts');
            Route::get('/earnings', [FreelancerController::class, 'earnings'])->name('earnings');
            Route::get('/badges', [FreelancerController::class, 'badges'])->name('badges');
            Route::post('/projects/{project}/proposal', [FreelancerController::class, 'submitProposal'])->name('submit-proposal');
            Route::delete('/proposals/{proposal}', [FreelancerController::class, 'withdrawProposal'])->name('withdraw-proposal');
            Route::get('/saved-projects', [FreelancerController::class, 'savedProjects'])->name('saved-projects');
            Route::post('/projects/{project}/save', [FreelancerController::class, 'toggleSaveProject'])->name('toggle-save-project');
            // Offers
            Route::get('/offers', [FreelancerController::class, 'offers'])->name('offers');
            Route::post('/offers/{proposal}/accept', [FreelancerController::class, 'acceptOffer'])->name('offer.accept');
            Route::post('/offers/{proposal}/decline', [FreelancerController::class, 'declineOffer'])->name('offer.decline');
        });

        // Employer Dashboard & Project Management
        Route::prefix('employer')->name('employer.')->group(function () {
            Route::get('/dashboard', [MarketplaceEmployerController::class, 'dashboard'])->name('dashboard');
            Route::get('/projects', [MarketplaceEmployerController::class, 'projects'])->name('projects');
            Route::get('/projects/create', [MarketplaceEmployerController::class, 'createProject'])->name('create-project');
            Route::post('/projects', [MarketplaceEmployerController::class, 'storeProject'])->name('store-project');
            Route::post('/projects/enhance', [MarketplaceEmployerController::class, 'enhanceProject'])->name('enhance-project');
            Route::get('/projects/{project}/manage', [MarketplaceEmployerController::class, 'manageProject'])->name('manage-project');
            Route::get('/projects/{project}/edit', [MarketplaceEmployerController::class, 'editProject'])->name('edit-project');
            Route::put('/projects/{project}', [MarketplaceEmployerController::class, 'updateProject'])->name('update-project');
            Route::delete('/projects/{project}', [MarketplaceEmployerController::class, 'deleteProject'])->name('delete-project');
            Route::post('/projects/{project}/publish', [MarketplaceEmployerController::class, 'publishProject'])->name('publish-project');
            Route::post('/projects/{project}/close', [MarketplaceEmployerController::class, 'closeProject'])->name('close-project');
            Route::get('/projects/{project}/proposals', [MarketplaceEmployerController::class, 'reviewProposals'])->name('review-proposals');
            Route::post('/proposals/{proposal}/hire', [MarketplaceEmployerController::class, 'hireFreelancer'])->name('hire');
            Route::post('/proposals/{proposal}/reject', [MarketplaceEmployerController::class, 'rejectProposal'])->name('reject-proposal');
            Route::post('/proposals/{proposal}/shortlist', [MarketplaceEmployerController::class, 'shortlistProposal'])->name('shortlist-proposal');
            Route::post('/proposals/{proposal}/offer', [MarketplaceEmployerController::class, 'sendOffer'])->name('send-offer');
            Route::get('/freelancers/{profile}/invite', [MarketplaceEmployerController::class, 'showInviteForm'])->name('invite');
            Route::post('/freelancers/{profile}/invite', [MarketplaceEmployerController::class, 'sendInvitation'])->name('send-invitation');
            Route::get('/contracts', [MarketplaceEmployerController::class, 'contracts'])->name('contracts');
            Route::get('/saved', [MarketplaceEmployerController::class, 'savedFreelancers'])->name('saved');
            Route::post('/freelancers/{profile}/save', [MarketplaceEmployerController::class, 'toggleSaveFreelancer'])->name('toggle-save-freelancer');
        });

        // Contract Management (Both parties)
        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/{contract}', [ContractController::class, 'show'])->name('show');
            Route::get('/{contract}/milestones', [ContractController::class, 'milestones'])->name('milestones');
            Route::post('/{contract}/milestones', [ContractController::class, 'createMilestone'])->name('milestones.create');
            Route::post('/milestones/{milestone}/submit', [ContractController::class, 'submitMilestone'])->name('milestones.submit');
            Route::post('/milestones/{milestone}/approve', [ContractController::class, 'approveMilestone'])->name('milestones.approve');
            Route::post('/milestones/{milestone}/revision', [ContractController::class, 'requestRevision'])->name('milestones.revision');
            Route::post('/milestones/{milestone}/fund', [ContractController::class, 'fundMilestone'])->name('milestones.fund');
            Route::get('/{contract}/messages', [ContractController::class, 'messages'])->name('messages');
            Route::post('/{contract}/messages', [ContractController::class, 'sendMessage'])->name('messages.send');
            Route::post('/{contract}/complete', [ContractController::class, 'complete'])->name('complete');
            Route::post('/{contract}/cancel', [ContractController::class, 'cancel'])->name('cancel');
            Route::post('/{contract}/dispute', [ContractController::class, 'dispute'])->name('dispute');
            Route::post('/{contract}/review', [ContractController::class, 'submitReview'])->name('review');
        });

        // Escrow Payment Callbacks
        Route::prefix('escrow')->name('escrow.')->group(function () {
            Route::post('/razorpay/callback', [ContractController::class, 'razorpayCallback'])->name('razorpay.callback');
            Route::post('/payu/success', [ContractController::class, 'payuSuccess'])->name('payu.success');
            Route::post('/payu/failure', [ContractController::class, 'payuFailure'])->name('payu.failure');
        });

        // AI Helpers
        Route::post('/ai/cover-letter', [FreelancerController::class, 'generateCoverLetter'])->name('ai.cover-letter');

        // Contact project owner
        Route::post('/projects/{project}/contact', [MarketplaceController::class, 'contactProjectOwner'])->name('project.contact');

        // Messaging (between freelancer and employer)
        Route::get('/message/{profile}', [MarketplaceController::class, 'messageFreelancer'])->name('message');
        Route::post('/message/{profile}', [MarketplaceController::class, 'sendMessageToFreelancer'])->name('message.send');

        // ── GIG MARKETPLACE ───────────────────────────────────────────────
        // Public: browse & view gigs (company / visitor side)
        Route::get('/gigs', [\App\Http\Controllers\Marketplace\GigController::class, 'index'])->name('gigs');
        Route::get('/gigs/{gig}', [\App\Http\Controllers\Marketplace\GigController::class, 'show'])->name('gig.show');
        Route::post('/gigs/{gig}/order', [\App\Http\Controllers\Marketplace\GigController::class, 'placeOrder'])->name('gig.order');

        // Freelancer: manage their own gigs
        Route::prefix('freelancer')->name('freelancer.')->group(function () {
            Route::get('/gigs', [\App\Http\Controllers\Marketplace\GigController::class, 'myGigs'])->name('gigs');
            Route::get('/gigs/create', [\App\Http\Controllers\Marketplace\GigController::class, 'create'])->name('create-gig');
            Route::post('/gigs', [\App\Http\Controllers\Marketplace\GigController::class, 'store'])->name('store-gig');
            Route::get('/gigs/{gig}/edit', [\App\Http\Controllers\Marketplace\GigController::class, 'edit'])->name('edit-gig');
            Route::put('/gigs/{gig}', [\App\Http\Controllers\Marketplace\GigController::class, 'update'])->name('update-gig');
            Route::delete('/gigs/{gig}', [\App\Http\Controllers\Marketplace\GigController::class, 'destroy'])->name('delete-gig');
            Route::post('/gigs/{gig}/toggle', [\App\Http\Controllers\Marketplace\GigController::class, 'toggleStatus'])->name('toggle-gig');
        });
    });
});

// ============================================================
// GAMIFICATION LAYER ROUTES
// ============================================================

use App\Http\Controllers\GamificationController;

Route::middleware(['auth', 'verified'])->prefix('gamification')->name('gamification.')->group(function () {
    // Main Dashboard
    Route::get('/', [GamificationController::class, 'dashboard'])->name('dashboard');
    
    // Achievements
    Route::get('/achievements', [GamificationController::class, 'achievements'])->name('achievements');
    
    // Badges
    Route::get('/badges', [GamificationController::class, 'badges'])->name('badges');
    
    // Daily Challenges
    Route::get('/challenges', [GamificationController::class, 'challenges'])->name('challenges');
    Route::post('/challenges/{userChallenge}/claim', [GamificationController::class, 'claimChallenge'])->name('challenges.claim');
    
    // Leaderboards
    Route::get('/leaderboards', [GamificationController::class, 'leaderboards'])->name('leaderboards');
    Route::post('/leaderboards/toggle', [GamificationController::class, 'toggleLeaderboardOptIn'])->name('leaderboards.toggle');
    
    // Rewards Store
    Route::get('/rewards', [GamificationController::class, 'rewards'])->name('rewards');
    Route::post('/rewards/{reward}/redeem', [GamificationController::class, 'redeemReward'])->name('rewards.redeem');
    
    // Daily Reward
    Route::post('/claim-daily', [GamificationController::class, 'claimDailyReward'])->name('claim-daily');
    
    // Events & Referrals
    Route::get('/events', [GamificationController::class, 'events'])->name('events');
    Route::get('/referrals', [GamificationController::class, 'referrals'])->name('referrals');

    // History & Stats
    Route::get('/history', [GamificationController::class, 'history'])->name('history');
    Route::get('/activity', [GamificationController::class, 'history'])->name('activity');
    Route::get('/stats', [GamificationController::class, 'stats'])->name('stats');
    
    // API Endpoints for AJAX
    Route::get('/api/profile', [GamificationController::class, 'getProfile'])->name('api.profile');
    Route::get('/api/notifications', [GamificationController::class, 'getNotifications'])->name('api.notifications');
});

/*
|--------------------------------------------------------------------------
| Health Check Routes
|--------------------------------------------------------------------------
|
| These routes are used for monitoring and load balancer health checks.
| They do not require authentication.
|
*/

Route::get('/health', [\App\Http\Controllers\HealthCheckController::class, 'health'])
    ->name('health.check');

Route::get('/ready', [\App\Http\Controllers\HealthCheckController::class, 'ready'])
    ->name('health.ready');

// Include employer routes from separate file
require __DIR__.'/employer.php';

require __DIR__.'/auth.php';
