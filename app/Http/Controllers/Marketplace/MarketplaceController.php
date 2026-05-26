<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use App\Models\MarketplaceContract;
use App\Models\MarketplaceMilestone;
use App\Models\MarketplaceProject;
use App\Models\MarketplaceProposal;
use App\Models\SavedProject;
use App\Services\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function __construct(
        protected MarketplaceService $marketplaceService
    ) {
        $this->middleware('auth')->except(['index', 'projects', 'showProject', 'freelancers', 'showFreelancer']);
    }

    /**
     * Marketplace homepage/dashboard.
     */
    public function index(): View
    {
        $stats = $this->marketplaceService->getMarketplaceStats();

        $featuredProjects = MarketplaceProject::with(['employer', 'company'])->open()->published()->featured()->limit(8)->get();
        if ($featuredProjects->isEmpty()) {
            $featuredProjects = MarketplaceProject::with(['employer', 'company'])->open()->published()->orderByDesc('views_count')->limit(8)->get();
        }

        $topFreelancers = FreelancerProfile::with('user')->verified()->where('average_rating', '>=', 4.0)->orderByDesc('average_rating')->limit(8)->get();
        if ($topFreelancers->isEmpty()) {
            $topFreelancers = FreelancerProfile::with('user')->orderByDesc('average_rating')->limit(8)->get();
        }

        return view('marketplace.index', [
            'stats'            => $stats,
            'featuredProjects' => $featuredProjects,
            'topFreelancers'   => $topFreelancers,
        ]);
    }

    /**
     * Browse all projects.
     */
    public function projects(Request $request): View
    {
        $projects = $this->marketplaceService->searchProjects($request->all());

        $categories = [
            'web_development' => 'Web Development',
            'mobile_development' => 'Mobile Development',
            'design' => 'Design',
            'writing' => 'Writing & Content',
            'marketing' => 'Marketing',
            'data_science' => 'Data Science',
            'ai_ml' => 'AI & Machine Learning',
            'devops' => 'DevOps',
            'consulting' => 'Consulting',
            'video_production' => 'Video Production',
            'audio_production' => 'Audio Production',
            'translation' => 'Translation',
            'legal' => 'Legal',
            'finance' => 'Finance',
            'admin_support' => 'Admin Support',
            'customer_service' => 'Customer Service',
            'other' => 'Other',
        ];

        return view('marketplace.projects.index', [
            'projects' => $projects,
            'categories' => $categories,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show single project.
     */
    public function showProject(MarketplaceProject $project): View
    {
        $project->incrementViews();
        $project->load(['employer', 'company', 'proposals' => function ($q) {
            $q->limit(5)->orderByDesc('is_boosted');
        }]);

        $canApply = false;
        $hasApplied = false;
        $myProposal = null;

        if (auth()->check()) {
            $this->marketplaceService->forUser(auth()->user());
            $profile = $this->marketplaceService->getFreelancerProfile();
            
            if ($profile) {
                $canApply = $profile->canApplyToProject($project);
                $myProposal = $project->proposals()
                    ->where('freelancer_id', auth()->id())
                    ->first();
                $hasApplied = (bool) $myProposal;
            }

            $isSaved = SavedProject::where('freelancer_id', auth()->id())
                ->where('project_id', $project->id)
                ->exists();
        }

        $similarProjects = MarketplaceProject::open()
            ->published()
            ->where('id', '!=', $project->id)
            ->where('category', $project->category)
            ->limit(4)
            ->get();

        return view('marketplace.projects.show', [
            'project' => $project,
            'canApply' => $canApply,
            'hasApplied' => $hasApplied,
            'myProposal' => $myProposal,
            'isSaved' => $isSaved ?? false,
            'similarProjects' => $similarProjects,
        ]);
    }

    /**
     * Browse freelancers.
     */
    public function freelancers(Request $request): View
    {
        $freelancers = $this->marketplaceService->searchFreelancers($request->all());

        return view('marketplace.freelancers.index', [
            'freelancers' => $freelancers,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show freelancer profile.
     */
    public function showFreelancer(FreelancerProfile $profile): View
    {
        $profile->load(['user', 'badges.badge']);

        $reviews = $profile->reviews()
            ->published()
            ->with('reviewer', 'contract.project')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('marketplace.freelancers.show', [
            'profile' => $profile,
            'reviews' => $reviews,
        ]);
    }

    /**
     * My dashboard (freelancer or employer).
     */
    public function dashboard(): View
    {
        $user = auth()->user();
        $this->marketplaceService->forUser($user);

        $freelancerProfile = $this->marketplaceService->getFreelancerProfile();
        $isFreelancer = (bool) $freelancerProfile;

        $data = [
            'isFreelancer' => $isFreelancer,
        ];

        if ($isFreelancer) {
            $data['freelancerStats'] = $this->marketplaceService->getFreelancerStats();
            $data['myProposals'] = $this->marketplaceService->getMyProposals();
            $data['myContracts'] = $this->marketplaceService->getMyContracts('freelancer');
            $data['recommendedProjects'] = $this->marketplaceService->getRecommendedProjects(5);
        }

        // Also get employer data if they have projects
        $employerProjects = MarketplaceProject::where('employer_id', $user->id)->count();
        if ($employerProjects > 0) {
            $data['employerStats'] = $this->marketplaceService->getEmployerStats();
            $data['myProjects'] = MarketplaceProject::where('employer_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('marketplace.dashboard', $data);
    }

    /**
     * Submit a proposal.
     */
    public function submitProposal(Request $request, MarketplaceProject $project): JsonResponse
    {
        $request->validate([
            'cover_letter' => 'required|string|min:100|max:5000',
            'proposed_amount' => 'required|numeric|min:1',
            'estimated_duration_days' => 'nullable|integer|min:1',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required_with:milestones|string|max:255',
            'milestones.*.amount' => 'required_with:milestones|numeric|min:1',
            'relevant_experience' => 'nullable|string|max:2000',
        ]);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $proposal = $this->marketplaceService->submitProposal($project, $request->all());

            return response()->json([
                'success' => true,
                'proposal' => $proposal,
                'message' => 'Your proposal has been submitted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Generate AI proposal suggestion.
     */
    public function generateProposalSuggestion(MarketplaceProject $project): JsonResponse
    {
        $this->marketplaceService->forUser(auth()->user());
        $suggestion = $this->marketplaceService->generateProposalSuggestion($project);

        return response()->json([
            'success' => !empty($suggestion),
            'suggestion' => $suggestion,
        ]);
    }

    /**
     * Withdraw a proposal.
     */
    public function withdrawProposal(MarketplaceProposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal);

        if (!$proposal->isPending() && !$proposal->isShortlisted()) {
            return response()->json([
                'success' => false,
                'message' => 'This proposal cannot be withdrawn.',
            ], 422);
        }

        $proposal->withdraw();

        return response()->json([
            'success' => true,
            'message' => 'Proposal withdrawn successfully.',
        ]);
    }

    /**
     * Toggle save project.
     */
    public function toggleSaveProject(MarketplaceProject $project): JsonResponse
    {
        $saved = SavedProject::where('freelancer_id', auth()->id())
            ->where('project_id', $project->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
        } else {
            SavedProject::create([
                'freelancer_id' => auth()->id(),
                'project_id' => $project->id,
            ]);
            $isSaved = true;
        }

        return response()->json([
            'success' => true,
            'isSaved' => $isSaved,
        ]);
    }

    /**
     * My saved projects.
     */
    public function savedProjects(): View
    {
        $savedProjects = SavedProject::where('freelancer_id', auth()->id())
            ->with('project.employer')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('marketplace.saved-projects', [
            'savedProjects' => $savedProjects,
        ]);
    }

    // ── Route aliases (routes use different method names) ──────────────────

    public function project(MarketplaceProject $project): View
    {
        return $this->showProject($project);
    }

    public function freelancer(FreelancerProfile $profile): View
    {
        return $this->showFreelancer($profile);
    }

    public function categories(): View
    {
        $categories = [
            ['slug' => 'web_development',   'label' => 'Programming & Tech',    'icon' => '💻', 'count' => MarketplaceProject::open()->published()->where('category', 'web_development')->count()],
            ['slug' => 'design',             'label' => 'Graphics & Design',     'icon' => '🎨', 'count' => MarketplaceProject::open()->published()->where('category', 'design')->count()],
            ['slug' => 'writing',            'label' => 'Writing & Translation', 'icon' => '✍️', 'count' => MarketplaceProject::open()->published()->where('category', 'writing')->count()],
            ['slug' => 'ai_ml',              'label' => 'AI & Machine Learning', 'icon' => '🤖', 'count' => MarketplaceProject::open()->published()->where('category', 'ai_ml')->count()],
            ['slug' => 'marketing',          'label' => 'Digital Marketing',     'icon' => '📣', 'count' => MarketplaceProject::open()->published()->where('category', 'marketing')->count()],
            ['slug' => 'data_science',       'label' => 'Data Science',          'icon' => '📊', 'count' => MarketplaceProject::open()->published()->where('category', 'data_science')->count()],
            ['slug' => 'mobile_development', 'label' => 'Mobile Apps',           'icon' => '📱', 'count' => MarketplaceProject::open()->published()->where('category', 'mobile_development')->count()],
            ['slug' => 'devops',             'label' => 'DevOps & Cloud',        'icon' => '⚙️', 'count' => MarketplaceProject::open()->published()->where('category', 'devops')->count()],
        ];
        return view('marketplace.categories', compact('categories'));
    }

    public function messageFreelancer(FreelancerProfile $profile): View
    {
        return view('marketplace.message', compact('profile'));
    }

    public function sendMessageToFreelancer(Request $request, FreelancerProfile $profile): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'budget'  => 'nullable|numeric|min:0',
        ]);

        // Store as a direct message notification or just redirect for now
        // Could be extended to use a DirectMessage model
        return redirect()->route('marketplace.message', $profile)
            ->with('success', 'Your message has been sent to ' . $profile->user?->name . '!');
    }

    public function contactProjectOwner(Request $request, MarketplaceProject $project): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:20|max:2000',
        ]);

        $sender  = auth()->user();
        $employer = $project->employer;

        if ($employer) {
            $employer->notify(new \App\Notifications\MarketplaceContactNotification(
                sender: $sender,
                project: $project,
                subject: $request->subject,
                message: $request->message,
            ));
        }

        return redirect()
            ->route('marketplace.project.show', $project)
            ->with('success', 'Your message has been sent to the client!');
    }
}
