<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\InterviewPanelScore;
use App\Services\InterviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InterviewManagementController extends Controller
{
    public function __construct(private readonly InterviewService $service)
    {
        $this->middleware(['auth', 'employer']);
    }

    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $interviewQuery = Interview::with(['application.user', 'application.job'])
            ->whereHas('application.job', fn ($q) => $q->where('company_id', $company->id));

        if ($request->filled('status')) {
            $interviewQuery->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $interviewQuery->where('interview_type', $request->type);
        }
        if ($request->filled('date_from')) {
            $interviewQuery->whereDate('scheduled_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $interviewQuery->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'oldest' => $interviewQuery->oldest('scheduled_at'),
            'round'  => $interviewQuery->orderBy('round'),
            default  => $interviewQuery->latest('scheduled_at'),
        };

        $interviews = $interviewQuery->paginate(20);

        $pendingSchedule = Application::with(['user', 'job'])
            ->whereHas('job', fn ($q) => $q->where('company_id', $company->id))
            ->where('status', 'shortlisted')
            ->doesntHave('interviews')
            ->latest('status_updated_at')
            ->limit(20)
            ->get();

        $stats = [
            'total'     => Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))->count(),
            'scheduled' => Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))->where('status', 'scheduled')->count(),
            'completed' => Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))->where('status', 'completed')->count(),
            'pending'   => $pendingSchedule->count(),
        ];

        return view('employer.interviews.index', compact('interviews', 'stats', 'pendingSchedule'));
    }

    public function scheduleForm(int $applicationId): View
    {
        $company     = Auth::user()->company;
        $application = Application::with(['user', 'job', 'interviews'])
            ->whereHas('job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($applicationId);

        $existingRound = $application->interviews->count();

        return view('employer.interviews.schedule', compact('application', 'existingRound'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_id'   => 'required|exists:applications,id',
            'interview_type'   => 'required|in:phone,video,onsite,technical,behavioral,panel',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'location'         => 'nullable|string|max:500',
            'meeting_link'     => 'nullable|url|max:500',
            'notes'            => 'nullable|string|max:2000',
            'round'            => 'nullable|integer|min:1|max:10',
        ]);

        $company     = Auth::user()->company;
        $application = Application::whereHas('job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($validated['application_id']);

        $interview = $this->service->schedule($application, $validated);

        return redirect()
            ->route('employer.interviews.show', $interview->id)
            ->with('success', 'Interview scheduled! Candidate notified.');
    }

    public function show(int $id): View
    {
        $company   = Auth::user()->company;
        $interview = Interview::with(['application.user', 'application.job', 'interviewers', 'panelScores.interviewer'])
            ->whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        return view('employer.interviews.show', compact('interview'));
    }

    public function saveScore(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'scores'           => 'required|array',
            'scores.*.key'     => 'required|string',
            'scores.*.score'   => 'required|integer|min:1|max:5',
            'scores.*.comment' => 'nullable|string|max:500',
        ]);

        $company   = Auth::user()->company;
        $interview = Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $userId = Auth::id();
        foreach ($validated['scores'] as $s) {
            InterviewPanelScore::updateOrCreate(
                ['interview_id' => $interview->id, 'user_id' => $userId, 'question_key' => $s['key']],
                ['score' => $s['score'], 'comment' => $s['comment'] ?? null]
            );
        }

        return back()->with('success', 'Scores saved.');
    }

    public function evaluate(int $id): View
    {
        $company   = Auth::user()->company;
        $interview = Interview::with(['application.user', 'application.job', 'panelScores.interviewer', 'interviewers'])
            ->whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $scoresByQuestion = $interview->panelScores
            ->groupBy('question_key')
            ->map(fn ($group) => [
                'avg'    => round($group->avg('score'), 1),
                'scores' => $group->map(fn ($s) => [
                    'interviewer' => $s->interviewer?->name ?? 'Unknown',
                    'score'       => $s->score,
                    'comment'     => $s->comment,
                ])->values()->toArray(),
            ]);

        return view('employer.interviews.evaluate', compact('interview', 'scoresByQuestion'));
    }

    public function submitEvaluation(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'interviewer_notes' => 'nullable|string|max:3000',
        ]);

        $company   = Auth::user()->company;
        $interview = Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $panelScores = $interview->panelScores
            ->groupBy('question_key')
            ->map(fn ($group) => $group->map(fn ($s) => ['score' => $s->score, 'comment' => $s->comment])->toArray())
            ->toArray();

        if (!empty($validated['interviewer_notes'])) {
            $interview->update(['interviewer_notes' => $validated['interviewer_notes']]);
        }

        $summary = $this->service->evaluate($interview, $panelScores);

        return redirect()
            ->route('employer.interviews.decide', $interview->id)
            ->with('success', 'Evaluation complete. AI score: ' . ($summary['overall_score'] ?? 'N/A') . '/5');
    }

    public function decideForm(int $id): View
    {
        $company   = Auth::user()->company;
        $interview = Interview::with(['application.user', 'application.job'])
            ->whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        return view('employer.interviews.decide', compact('interview'));
    }

    public function submitDecision(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'decision'         => 'required|in:hire,reject,next_round',
            'reason'           => 'nullable|string|max:2000',
            'scheduled_at'     => 'nullable|date|after:now',
            'interview_type'   => 'nullable|in:phone,video,onsite,technical,behavioral,panel',
            'duration_minutes' => 'nullable|integer|min:15|max:240',
            'meeting_link'     => 'nullable|url|max:500',
        ]);

        $company   = Auth::user()->company;
        $interview = Interview::with(['application.user', 'application.job'])
            ->whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $this->service->decide($interview, $validated['decision'], $validated);

        $messages = [
            'hire'       => 'Candidate hired! Emails sent.',
            'reject'     => 'Decision recorded. Candidate notified.',
            'next_round' => 'Next round scheduled.',
        ];

        return redirect()
            ->route('employer.interviews.index')
            ->with('success', $messages[$validated['decision']]);
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $company   = Auth::user()->company;
        $interview = Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $interview->update([
            'status'              => 'canceled',
            'canceled_at'         => now(),
            'cancellation_reason' => $request->input('reason', ''),
        ]);

        return back()->with('success', 'Interview canceled.');
    }

    public function complete(int $id): RedirectResponse
    {
        $company   = Auth::user()->company;
        $interview = Interview::whereHas('application.job', fn ($q) => $q->where('company_id', $company->id))
            ->findOrFail($id);

        $interview->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()
            ->route('employer.interviews.evaluate', $interview->id)
            ->with('info', 'Interview marked complete. Submit evaluation now.');
    }
}