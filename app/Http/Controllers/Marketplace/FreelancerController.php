<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use App\Models\MarketplaceProject;
use App\Models\MarketplaceProposal;
use App\Models\SavedProject;
use App\Models\SkillBadge;
use App\Models\UserSkillBadge;
use App\Services\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FreelancerController extends Controller
{
    public function __construct(
        protected MarketplaceService $marketplaceService
    ) {
        $this->middleware('auth');
    }

    /**
     * Show profile setup/edit page.
     */
    public function profile(): View
    {
        $this->marketplaceService->forUser(auth()->user());
        $profile = $this->marketplaceService->getFreelancerProfile();
        $badges = $this->marketplaceService->getMyBadges();
        $availableBadges = $this->marketplaceService->getAvailableBadges();

        return view('marketplace.freelancer.profile', [
            'profile' => $profile,
            'badges' => $badges,
            'availableBadges' => $availableBadges,
        ]);
    }

    /**
     * Update freelancer profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'professional_title' => 'required|string|max:255',
            'bio' => 'required|string|min:50|max:2000',
            'overview' => 'nullable|string|max:5000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'skills' => 'required|array|min:1|max:20',
            'skills.*' => 'string|max:100',
            'languages' => 'nullable|array',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'availability' => 'required|in:full_time,part_time,hourly,not_available',
            'hours_per_week' => 'nullable|integer|min:1|max:80',
            'available_for_remote' => 'boolean',
            'available_for_onsite' => 'boolean',
            'preferred_project_size' => 'nullable|in:small,medium,large',
            'portfolio' => 'nullable|array',
            'portfolio.*.title' => 'required_with:portfolio|string|max:255',
            'portfolio.*.url' => 'required_with:portfolio|url',
            'certifications' => 'nullable|array',
        ]);

        $this->marketplaceService->forUser(auth()->user());
        $profile = $this->marketplaceService->updateFreelancerProfile($request->all());

        return response()->json([
            'success' => true,
            'profile' => $profile,
            'message' => 'Profile updated successfully!',
        ]);
    }

    /**
     * My proposals page.
     */
    public function proposals(Request $request): View
    {
        $this->marketplaceService->forUser(auth()->user());
        
        $status = $request->get('status');
        $proposals = $this->marketplaceService->getMyProposals($status);

        return view('marketplace.freelancer.proposals', [
            'proposals' => $proposals,
            'currentStatus' => $status,
        ]);
    }

    /**
     * My contracts page.
     */
    public function contracts(Request $request): View
    {
        $this->marketplaceService->forUser(auth()->user());
        
        $status = $request->get('status');
        $contracts = $this->marketplaceService->getMyContracts('freelancer', $status);

        return view('marketplace.freelancer.contracts', [
            'contracts' => $contracts,
            'currentStatus' => $status,
        ]);
    }

    /**
     * My earnings page.
     */
    public function earnings(): View
    {
        $this->marketplaceService->forUser(auth()->user());
        $stats = $this->marketplaceService->getFreelancerStats();
        $profile = $this->marketplaceService->getFreelancerProfile();

        // Get recent payments
        $recentPayments = \App\Models\MarketplaceEscrow::where('payee_id', auth()->id())
            ->where('status', 'released')
            ->with('contract.project')
            ->orderByDesc('released_at')
            ->limit(20)
            ->get();

        return view('marketplace.freelancer.earnings', [
            'stats' => $stats,
            'profile' => $profile,
            'recentPayments' => $recentPayments,
        ]);
    }

    /**
     * Apply for a skill badge.
     */
    public function applyForBadge(Request $request, SkillBadge $badge): JsonResponse
    {
        $request->validate([
            'evidence' => 'nullable|string|max:2000',
        ]);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $userBadge = $this->marketplaceService->applyForBadge($badge, $request->evidence);

            return response()->json([
                'success' => true,
                'badge' => $userBadge->load('badge'),
                'message' => $userBadge->isVerified() 
                    ? 'Badge earned successfully!' 
                    : 'Badge application submitted for verification.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get recommended projects.
     */
    public function recommendedProjects(): JsonResponse
    {
        $this->marketplaceService->forUser(auth()->user());
        $projects = $this->marketplaceService->getRecommendedProjects(10);

        return response()->json([
            'success' => true,
            'projects' => $projects,
        ]);
    }

    // ── Route aliases & missing methods ───────────────────────────────────

    /** Route calls activeContracts(), controller has contracts() */
    public function activeContracts(Request $request): View
    {
        return $this->contracts($request);
    }

    /** Badges overview page */
    public function badges(): View
    {
        $this->marketplaceService->forUser(auth()->user());
        $badges         = $this->marketplaceService->getMyBadges();
        $availableBadges = SkillBadge::where('is_active', true)->get();

        return view('marketplace.freelancer.badges', compact('badges', 'availableBadges'));
    }

    /** Submit proposal to a project */
    public function submitProposal(Request $request, MarketplaceProject $project): JsonResponse
    {
        $request->validate([
            'cover_letter'    => 'required|string|min:50|max:5000',
            'proposed_amount' => 'required|numeric|min:1',
            'estimated_days'  => 'nullable|integer|min:1',
        ]);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $proposal = $this->marketplaceService->submitProposal($project, $request->all());

            return response()->json([
                'success'  => true,
                'proposal' => $proposal,
                'message'  => 'Proposal submitted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** Withdraw a proposal */
    public function withdrawProposal(MarketplaceProposal $proposal): JsonResponse
    {
        if ($proposal->freelancer_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $proposal->delete();

        return response()->json(['success' => true, 'message' => 'Proposal withdrawn.']);
    }

    /** Freelancer dashboard */
    public function dashboard(): View
    {
        $this->marketplaceService->forUser(auth()->user());
        $profile     = $this->marketplaceService->getFreelancerProfile();
        $stats       = $profile ? $this->marketplaceService->getFreelancerStats() : [];
        $proposals   = $this->marketplaceService->getMyProposals();
        $contracts   = $this->marketplaceService->getMyContracts('freelancer');
        $recommended = $this->marketplaceService->getRecommendedProjects(6);

        return view('marketplace.freelancer.dashboard', compact(
            'profile', 'stats', 'proposals', 'contracts', 'recommended'
        ));
    }

    /** Toggle save/unsave a project */
    public function toggleSaveProject(MarketplaceProject $project): JsonResponse
    {
        $saved = SavedProject::where('freelancer_id', auth()->id())
            ->where('project_id', $project->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
        } else {
            SavedProject::create(['freelancer_id' => auth()->id(), 'project_id' => $project->id]);
            $isSaved = true;
        }

        return response()->json(['success' => true, 'isSaved' => $isSaved]);
    }

    /** Saved projects list */
    public function savedProjects(): View
    {
        $savedProjects = SavedProject::where('freelancer_id', auth()->id())
            ->with('project.employer')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('marketplace.freelancer.saved-projects', compact('savedProjects'));
    }

    /** View all pending offers */
    public function offers(): View
    {
        $offers = MarketplaceProposal::where('freelancer_id', auth()->id())
            ->where('status', 'shortlisted')
            ->whereNotNull('offer_sent_at')
            ->with(['project.employer'])
            ->orderByDesc('offer_sent_at')
            ->get();

        return view('marketplace.freelancer.offers', compact('offers'));
    }

    /** Accept a hiring offer → creates contract */
    public function acceptOffer(MarketplaceProposal $proposal): RedirectResponse
    {
        if ($proposal->freelancer_id !== auth()->id()) {
            abort(403);
        }

        if (!$proposal->isOfferSent()) {
            return back()->with('error', 'This offer is no longer available.');
        }

        try {
            $this->marketplaceService->forUser(auth()->user());
            $contract = $proposal->acceptOffer();

            $proposal->project->employer?->notify(
                new \App\Notifications\MarketplaceContactNotification(
                    sender: auth()->user(),
                    project: $proposal->project,
                    subject: 'Offer Accepted',
                    message: auth()->user()->name . ' has accepted your offer for "' . $proposal->project->title . '".',
                )
            );

            return redirect()
                ->route('marketplace.contracts.show', $contract)
                ->with('success', '🎉 Offer accepted! Your contract is ready. Let\'s get to work!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not accept the offer: ' . $e->getMessage());
        }
    }

    /** Decline a hiring offer */
    public function declineOffer(MarketplaceProposal $proposal): RedirectResponse
    {
        if ($proposal->freelancer_id !== auth()->id()) {
            abort(403);
        }

        $proposal->declineOffer();

        return redirect()
            ->route('marketplace.freelancer.offers')
            ->with('success', 'Offer declined. You can continue browsing other projects.');
    }

    /** AI-generate a cover letter for a project proposal */
    public function generateCoverLetter(Request $request): JsonResponse    {
        $request->validate([
            'project_title'       => 'required|string|max:200',
            'project_description' => 'nullable|string|max:800',
            'skills_required'     => 'nullable|array',
            'budget'              => 'nullable|numeric',
        ]);

        $user = auth()->user();
        $profile = $user->freelancerProfile;

        $userName = $user->name ?? 'I';
        $skills = implode(', ', (array) ($request->skills_required ?? []));
        $bio = $profile?->bio ?? '';
        $projectTitle = $request->project_title;
        $projectDescription = $request->project_description ?? '';
        $budget = $request->budget ? '₹' . number_format((float) $request->budget) : '';

        $prompt = <<<PROMPT
Write a compelling, professional freelance proposal cover letter for the following project.

Project: {$projectTitle}
{$projectDescription}
Skills needed: {$skills}
Budget: {$budget}

Freelancer name: {$userName}
Freelancer bio: {$bio}

Instructions:
- Write in first person as the freelancer
- 3–4 short paragraphs: hook, relevant experience/skills, methodology, call to action
- Sound enthusiastic but professional
- Under 220 words
- Do NOT include a subject line or greeting header — just the body text
PROMPT;

        try {
            $endpoint = config('ai.azure.endpoint');
            $deployment = config('ai.azure.deployment_id', 'gpt-5.4');
            $apiVersion = config('ai.azure.api_version', '2024-12-01-preview');
            $apiKey = config('ai.azure.api_key');

            $url = rtrim((string) $endpoint, '/') . "/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

            $response = Http::withHeaders(['api-key' => $apiKey])
                ->timeout(30)
                ->post($url, [
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert freelance proposal writer. Write concise, persuasive cover letters.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_completion_tokens' => 350,
                    'temperature'           => 0.75,
                ]);

            if ($response->failed()) {
                Log::error('AI cover letter API error', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['error' => 'AI service unavailable. Please write your cover letter manually.'], 503);
            }

            $coverLetter = trim($response->json('choices.0.message.content') ?? '');

            if (empty($coverLetter)) {
                return response()->json(['error' => 'AI returned an empty response. Please try again.'], 500);
            }

            $user->deductAICredits(1, 'marketplace_cover_letter', 'AI Proposal Cover Letter: ' . $projectTitle);

            return response()->json(['cover_letter' => $coverLetter]);

        } catch (\Throwable $e) {
            Log::error('AI cover letter generation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to generate cover letter. Please try again.'], 500);
        }
    }
}
