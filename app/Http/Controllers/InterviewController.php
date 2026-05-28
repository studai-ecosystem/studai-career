<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\InterviewSession;
use App\Services\MockInterviewService;
use App\Services\VantageEvaluatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    protected $mockInterviewService;
    protected VantageEvaluatorService $vantageEvaluator;

    public function __construct(MockInterviewService $mockInterviewService, VantageEvaluatorService $vantageEvaluator)
    {
        $this->middleware('auth');
        $this->mockInterviewService = $mockInterviewService;
        $this->vantageEvaluator = $vantageEvaluator;
    }
    
    /**
     * Show interview preparation dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's interview sessions
        $sessions = InterviewSession::where('user_id', $user->id)
            ->latest()
            ->get();
        
        // Get recent/upcoming interviews
        $upcomingInterviews = $user->applications()
            ->whereIn('status', ['shortlisted', 'interview_scheduled'])
            ->with('job.company')
            ->latest()
            ->take(5)
            ->get();
        
        // Get interview tips
        $tips = $this->mockInterviewService->getGenericTips();
        
        return view('interview.index', compact('sessions', 'upcomingInterviews', 'tips'));
    }
    
    /**
     * Show mock interview setup page
     */
    public function create(Request $request)
    {
        $jobId = $request->get('job_id');
        $job = null;
        $company = null;
        
        if ($jobId) {
            $job = Job::with('company')->find($jobId);
            $company = $job?->company;
        }
        
        return view('interview.create', compact('job', 'company'));
    }
    
    /**
     * Start a mock interview session
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,mid,senior,executive',
            'company_id' => 'nullable|exists:companies,id',
            'question_count' => 'integer|min:5|max:20',
        ]);
        
        $company = null;
        if (!empty($validated['company_id'])) {
            $company = Company::find($validated['company_id']);
        }
        
        // Generate questions
        $questions = $this->mockInterviewService->generateQuestions(
            $validated['job_title'],
            $validated['experience_level'],
            $company,
            $validated['question_count'] ?? 10
        );
        
        // Store session in cache for 24 hours
        $sessionId = uniqid('interview_', true);
        $sessionData = [
            'job_title'        => $validated['job_title'],
            'experience_level' => $validated['experience_level'],
            'company'          => $company?->name,
            'questions'        => $questions,
            'answers'          => [],
            'started_at'       => now()->toDateTimeString(),
        ];
        Cache::put("interview_session_{$sessionId}", $sessionData, 86400);

        // Persist to DB so the session survives cache flushes (e.g. container restarts)
        // Wrapped in try-catch: if the migration hasn't run yet (e.g. during deployment),
        // the interview still works via cache — DB persistence is a durability bonus.
        try {
            InterviewSession::create([
                'user_id'          => Auth::id(),
                'cache_key'        => $sessionId,
                'job_title'        => $validated['job_title'],
                'experience_level' => $validated['experience_level'],
                'company_name'     => $company?->name,
                'status'           => 'in_progress',
                'session_data'     => $sessionData,
                'started_at'       => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('InterviewSession DB persist failed (migration pending?): ' . $e->getMessage());
        }

        return redirect()->route('interview.session', ['session' => $sessionId]);
    }
    
    /**
     * Display interview session
     */
    public function session($sessionId)
    {
        $session = Cache::get("interview_session_{$sessionId}");
        
        if (!$session) {
            return redirect()->route('interview.index')
                ->with('error', 'Interview session expired or not found.');
        }
        
        return view('interview.session', [
            'sessionId' => $sessionId,
            'session' => $session,
        ]);
    }
    
    /**
     * Submit answer to a question
     */
    public function submitAnswer(Request $request, $sessionId)
    {
        $validated = $request->validate([
            'question_index' => 'required|integer',
            'question' => 'required|string',
            'answer' => 'required|string|max:5000',
        ]);

        $session = Cache::get("interview_session_{$sessionId}");

        // If cache was cleared (e.g. container restart), recover session from DB
        if (!$session) {
            $dbRecord = InterviewSession::where('user_id', Auth::id())
                ->where('cache_key', $sessionId)
                ->where('status', 'in_progress')
                ->first();

            if ($dbRecord && !empty($dbRecord->session_data)) {
                $session = $dbRecord->session_data;
                // Re-prime the cache so subsequent requests are fast
                Cache::put("interview_session_{$sessionId}", $session, 86400);
            } else {
                return response()->json(['error' => 'Session expired. Please start a new interview.'], 404);
            }
        }

        // Store raw answer — evaluation happens once at the end (no AI call per question)
        $session['answers'][$validated['question_index']] = [
            'question'    => $validated['question'],
            'answer'      => $validated['answer'],
            'answered_at' => now()->toDateTimeString(),
        ];

        Cache::put("interview_session_{$sessionId}", $session, 86400);

        // Keep DB copy in sync so answers survive future cache flushes
        try {
            InterviewSession::where('cache_key', $sessionId)
                ->where('user_id', Auth::id())
                ->update([
                    'session_data'       => $session,
                    'questions_answered' => count($session['answers']),
                ]);
        } catch (\Throwable $e) {
            Log::warning('InterviewSession DB sync failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
    
    /**
     * Get follow-up questions
     */
    public function getFollowUp(Request $request, $sessionId)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);
        
        $followUps = $this->mockInterviewService->generateFollowUp(
            $validated['question'],
            $validated['answer']
        );
        
        return response()->json($followUps);
    }
    
    /**
     * Complete interview session and show results
     */
    public function complete($sessionId)
    {
        $session = Cache::get("interview_session_{$sessionId}");

        // If cache expired, load session_data from the DB record (if previously saved)
        if (!$session) {
            $dbRecord = InterviewSession::where('user_id', Auth::id())
                ->where('cache_key', $sessionId)
                ->first();

            if ($dbRecord && !empty($dbRecord->session_data)) {
                $session = $dbRecord->session_data;
            } else {
                return redirect()->route('interview.index')
                    ->with('error', 'Interview session expired or not found.');
            }
        }

        $answers = $session['answers'] ?? [];

        // Count total questions
        $totalQuestions = 0;
        foreach (($session['questions'] ?? []) as $items) {
            if (is_array($items)) {
                $totalQuestions += count($items);
            }
        }
        if ($totalQuestions === 0) {
            $totalQuestions = count($answers);
        }

        // Run Vantage evaluation once at session end (if not already done)
        $skillMap = $session['skill_map'] ?? [];
        if (empty($skillMap) && !empty($answers)) {
            $transcript = [];
            foreach ($answers as $answer) {
                if (!empty($answer['question'])) {
                    $transcript[] = ['role' => 'assistant', 'content' => $answer['question']];
                }
                if (!empty($answer['answer'])) {
                    $transcript[] = ['role' => 'user', 'content' => $answer['answer']];
                }
            }

            if (!empty($transcript)) {
                try {
                    $context = [
                        'role'       => $session['job_title'] ?? '',
                        'experience' => $session['experience_level'] ?? '',
                    ];
                    $skillMap = $this->vantageEvaluator->evaluate($transcript, Auth::user(), $context);
                    $session['skill_map']         = $skillMap;
                    $session['evaluator_ran_at']  = now()->toDateTimeString();
                    Cache::put("interview_session_{$sessionId}", $session, 7200);
                } catch (\Exception $e) {
                    Log::error('Vantage evaluation failed at session completion', [
                        'session_id' => $sessionId,
                        'error'      => $e->getMessage(),
                    ]);
                    $skillMap = [];
                }
            }
        }

        // Evaluate each individual answer (score + feedback per question)
        $questionLookup = [];
        $qi = 0;
        foreach (($session['questions'] ?? []) as $type => $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    $questionLookup[$qi++] = ['type' => ucfirst((string) $type), 'meta' => $item];
                }
            }
        }

        $evalContext = [
            'job_title'        => $session['job_title'] ?? '',
            'experience_level' => $session['experience_level'] ?? '',
        ];
        $evaluationUpdated = false;
        foreach ($session['answers'] as $index => $answer) {
            if (empty($answer['evaluation']) && !empty($answer['answer'])) {
                try {
                    $session['answers'][$index]['evaluation'] = $this->mockInterviewService->evaluateAnswer(
                        $answer['question'] ?? '',
                        $answer['answer'],
                        $evalContext
                    );
                    $evaluationUpdated = true;
                } catch (\Exception $e) {
                    Log::error('Per-question evaluation failed', ['index' => $index, 'error' => $e->getMessage()]);
                }
            }
        }
        if ($evaluationUpdated) {
            Cache::put("interview_session_{$sessionId}", $session, 7200);
        }
        $answers = $session['answers'];

        // Convert Vantage composite score (1–5) to 0–100
        $vantageComposite = (float) ($skillMap['overall'] ?? 0.0);
        $vantageScore     = (int) round($vantageComposite * 20);
        $grade            = $this->getPerformanceGrade($vantageScore);

        // ── Persist to database so it appears in Recent Sessions ──────────
        $scoreSum   = 0;
        $scoredCount = 0;
        foreach ($answers as $a) {
            $s = $a['evaluation']['score'] ?? null;
            if (is_numeric($s)) { $scoreSum += $s; $scoredCount++; }
        }
        $perQuestionAvg = $scoredCount > 0 ? (int) round($scoreSum / $scoredCount) : null;
        $finalScore     = $vantageScore > 0 ? $vantageScore : $perQuestionAvg;

        InterviewSession::updateOrCreate(
            ['cache_key' => $sessionId],
            [
                'user_id'            => Auth::id(),
                'job_title'          => $session['job_title'] ?? null,
                'role_title'         => $session['job_title'] ?? null,
                'company_name'       => $session['company'] ?? null,
                'interview_type'     => 'mock',
                'status'             => 'completed',
                'total_questions'    => $totalQuestions,
                'questions_answered' => count($answers),
                'overall_score'      => $finalScore,
                'ai_insights'        => $skillMap ?: null,
                'session_data'       => $session,
                'started_at'         => $session['started_at'] ?? now(),
                'completed_at'       => now(),
            ]
        );
        // ──────────────────────────────────────────────────────────────────

        return view('interview.complete', [
            'sessionId'      => $sessionId,
            'session'        => $session,
            'averageScore'   => $vantageScore,
            'vantageScore'   => $vantageScore,
            'grade'          => $grade,
            'totalQuestions' => $totalQuestions,
        ]);
    }

    /**
     * Render a print-ready PDF report page
     */
    public function downloadPdf($sessionId)
    {
        $session = Cache::get("interview_session_{$sessionId}");

        if (!$session) {
            abort(404, 'Session expired or not found.');
        }

        $answers = $session['answers'] ?? [];
        $totalAnswered = count($answers);
        $scoreSum = 0;
        $scoredCount = 0;

        $questionLookup = [];
        $qi = 0;
        foreach (($session['questions'] ?? []) as $type => $items) {
            foreach ($items as $item) {
                $questionLookup[$qi++] = ['type' => ucfirst($type), 'meta' => $item];
            }
        }

        $allStrengths    = [];
        $allImprovements = [];
        $allSuggestions  = [];

        foreach ($answers as $answer) {
            $score = $answer['evaluation']['score'] ?? null;
            if (is_numeric($score)) {
                $scoredCount++;
                $scoreSum += $score;
            }
            $allStrengths    = array_merge($allStrengths,    $answer['evaluation']['strengths'] ?? []);
            $allImprovements = array_merge($allImprovements, $answer['evaluation']['areas_for_improvement'] ?? []);
            $allSuggestions  = array_merge($allSuggestions,  $answer['evaluation']['suggestions'] ?? []);
        }

        $averageScore = $scoredCount > 0 ? round($scoreSum / $scoredCount) : 0;
        $grade = $this->getPerformanceGrade($averageScore);

        return view('interview.pdf', [
            'session'        => $session,
            'answers'        => $answers,
            'questionLookup' => $questionLookup,
            'totalAnswered'  => $totalAnswered,
            'totalQuestions' => count($questionLookup) ?: $totalAnswered,
            'averageScore'   => $averageScore,
            'grade'          => $grade,
            'topStrengths'   => collect($allStrengths)->filter()->unique()->take(5),
            'topImprovements'=> collect($allImprovements)->filter()->unique()->take(5),
            'topSuggestions' => collect($allSuggestions)->filter()->unique()->take(5),
            'generatedAt'    => now()->format('d M Y, h:i A'),
        ]);
    }
    
    /**
     * Show Vantage skill map for a completed session.
     */
    public function skillMap(string $sessionId)
    {
        $session = Cache::get("interview_session_{$sessionId}");

        if (!$session) {
            return redirect()->route('interview.index')
                ->with('error', 'Interview session expired or not found.');
        }

        // Run Vantage evaluation now if it hasn't been done yet
        $skillMap = $session['skill_map'] ?? [];

        if (empty($skillMap) || (($skillMap['overall'] ?? 0.0) == 0.0)) {
            $answers = $session['answers'] ?? [];

            if (!empty($answers)) {
                $transcript = [];
                foreach ($answers as $answer) {
                    if (!empty($answer['question'])) {
                        $transcript[] = ['role' => 'assistant', 'content' => $answer['question']];
                    }
                    if (!empty($answer['answer'])) {
                        $transcript[] = ['role' => 'user', 'content' => $answer['answer']];
                    }
                }

                if (!empty($transcript)) {
                    try {
                        $context  = [
                            'role'       => $session['job_title'] ?? '',
                            'experience' => $session['experience_level'] ?? '',
                        ];
                        $skillMap = $this->vantageEvaluator->evaluate($transcript, Auth::user(), $context);
                        $session['skill_map']        = $skillMap;
                        $session['evaluator_ran_at'] = now()->toDateTimeString();
                        Cache::put("interview_session_{$sessionId}", $session, 7200);
                    } catch (\Exception $e) {
                        Log::error('Vantage evaluation failed on skill-map page', [
                            'session_id' => $sessionId,
                            'error'      => $e->getMessage(),
                        ]);
                        $skillMap = [];
                    }
                }
            }
        }

        return view('interview.skill-map', [
            'sessionId' => $sessionId,
            'session'   => $session,
            'skillMap'  => $skillMap,
        ]);
    }

    /**
     * Show common questions for a role
     */
    public function commonQuestions(Request $request)
    {
        $jobTitle = $request->get('job_title', 'Software Developer');
        $questions = $this->mockInterviewService->getCommonQuestions($jobTitle);
        
        return view('interview.common-questions', compact('jobTitle', 'questions'));
    }
    
    /**
     * Show STAR method guide
     */
    public function starGuide()
    {
        return view('interview.star-guide');
    }
    
    /**
     * Format answer with STAR method
     */
    public function formatStar(Request $request)
    {
        $validated = $request->validate([
            'answer' => 'required|string|max:5000',
        ]);
        
        $formatted = $this->mockInterviewService->formatWithSTAR($validated['answer']);
        
        return response()->json($formatted);
    }
    
    /**
     * Show salary negotiation guide
     */
    public function salaryNegotiation()
    {
        $user = Auth::user();
        
        return view('interview.salary-negotiation', [
            'user' => $user,
        ]);
    }
    
    /**
     * Get personalized negotiation guide
     */
    public function getNegotiationGuide(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'current_salary' => 'required|numeric|min:0',
            'target_salary' => 'required|numeric|min:0',
            'years_experience' => 'nullable|integer',
            'unique_skills' => 'nullable|array',
        ]);
        
        $guide = $this->mockInterviewService->getSalaryNegotiationGuide(
            $validated['job_title'],
            $validated['current_salary'],
            $validated['target_salary'],
            [
                'years_experience' => $validated['years_experience'] ?? 0,
                'unique_skills' => $validated['unique_skills'] ?? [],
            ]
        );
        
        return response()->json($guide);
    }
    
    /**
     * Interview tips for specific job
     */
    public function tips(Request $request)
    {
        $jobId = $request->get('job_id');
        $job = null;
        $company = null;
        
        if ($jobId) {
            $job = Job::with('company')->find($jobId);
            $company = $job?->company;
        }
        
        $tips = $this->mockInterviewService->getInterviewTips(
            $job?->title ?? 'General Position',
            $company
        );
        
        return view('interview.tips', compact('job', 'company', 'tips'));
    }
    
    /**
     * Find interview coaches (placeholder for future LMS integration)
     */
    public function findCoaches(Request $request)
    {
        $jobTitle = $request->get('job_title', '');
        
        // For now, redirect to Google search
        // TODO: Integrate with LMS API when available
        $searchQuery = urlencode("interview coach for {$jobTitle}");
        $searchUrl = "https://www.google.com/search?q={$searchQuery}";
        
        // Store preference for future notifications
        if (Auth::check()) {
            $user = Auth::user();
            $preferences = $user->preferences ?? [];
            $preferences['coaching_interest'] = [
                'job_title' => $jobTitle,
                'interested_at' => now()->toDateTimeString(),
            ];
            $user->update(['preferences' => $preferences]);
        }
        
        return redirect()->away($searchUrl);
    }
    
    /**
     * Save practice recording metadata
     */
    public function saveRecording(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'duration' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Store recording metadata in user preferences
        $user = Auth::user();
        $preferences = $user->preferences ?? [];
        $recordings = $preferences['interview_recordings'] ?? [];
        
        $recordings[] = [
            'question' => $validated['question'],
            'duration' => $validated['duration'],
            'notes' => $validated['notes'] ?? '',
            'recorded_at' => now()->toDateTimeString(),
        ];
        
        // Keep only last 20 recordings
        if (count($recordings) > 20) {
            $recordings = array_slice($recordings, -20);
        }
        
        $preferences['interview_recordings'] = $recordings;
        $user->update(['preferences' => $preferences]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get performance grade based on score
     */
    protected function getPerformanceGrade($score)
    {
        if ($score >= 90) {
            return ['grade' => 'A+', 'label' => 'Excellent', 'color' => 'green'];
        } elseif ($score >= 80) {
            return ['grade' => 'A', 'label' => 'Very Good', 'color' => 'green'];
        } elseif ($score >= 70) {
            return ['grade' => 'B', 'label' => 'Good', 'color' => 'blue'];
        } elseif ($score >= 60) {
            return ['grade' => 'C', 'label' => 'Fair', 'color' => 'yellow'];
        } else {
            return ['grade' => 'D', 'label' => 'Needs Improvement', 'color' => 'red'];
        }
    }
}
