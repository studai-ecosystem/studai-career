<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use App\Models\MarketplaceContract;
use App\Models\MarketplaceInvitation;
use App\Models\MarketplaceMilestone;
use App\Models\MarketplaceProject;
use App\Models\MarketplaceProposal;
use App\Models\SavedFreelancer;
use App\Notifications\ProposalOfferNotification;
use App\Notifications\ProposalStatusNotification;
use App\Services\MarketplaceService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerController extends Controller
{
    public function __construct(
        protected MarketplaceService $marketplaceService,
        protected PaymentGatewayService $paymentService
    ) {
        $this->middleware('auth');
    }

    /**
     * Employer dashboard.
     */
    public function dashboard(): View
    {
        $this->marketplaceService->forUser(auth()->user());
        $stats = $this->marketplaceService->getEmployerStats();

        $projects = MarketplaceProject::where('employer_id', auth()->id())
            ->withCount('proposals')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $activeContracts = MarketplaceContract::where('employer_id', auth()->id())
            ->whereIn('status', ['pending', 'active'])
            ->with(['freelancer', 'project', 'milestones'])
            ->orderByDesc('created_at')
            ->get();

        $recentProposals = MarketplaceProposal::whereHas('project', function ($q) {
            $q->where('employer_id', auth()->id());
        })->pending()->with(['freelancer', 'project'])->limit(10)->get();

        $savedFreelancers = SavedFreelancer::where('employer_id', auth()->id())
            ->with(['freelancerProfile.user'])
            ->limit(5)
            ->get();

        $spending = [
            'this_month'  => \App\Models\MarketplaceEscrow::where('payer_id', auth()->id())
                ->released()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'last_month'  => \App\Models\MarketplaceEscrow::where('payer_id', auth()->id())
                ->released()
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('amount'),
            'in_escrow'   => \App\Models\MarketplaceEscrow::where('payer_id', auth()->id())
                ->held()
                ->sum('amount'),
        ];

        return view('marketplace.employer.dashboard', [
            'stats'           => $stats,
            'projects'        => $projects,
            'activeContracts' => $activeContracts,
            'recentProposals' => $recentProposals,
            'savedFreelancers'=> $savedFreelancers,
            'spending'        => $spending,
        ]);
    }

    /**
     * Create project form.
     */
    public function createProject(): View
    {
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

        return view('marketplace.employer.create-project', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store new project.
     */
    public function storeProject(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50|max:10000',
            'requirements' => 'nullable|string|max:5000',
            'deliverables' => 'nullable|string|max:5000',
            'project_type' => 'required|in:fixed_price,hourly,milestone',
            'category' => 'required|string',
            'skills_required' => 'required|array|min:1|max:10',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'hourly_rate_min' => 'nullable|numeric|min:0',
            'hourly_rate_max' => 'nullable|numeric|min:0|gte:hourly_rate_min',
            'currency' => 'required|string|max:10',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'estimated_duration_days' => 'nullable|integer|min:1',
            'duration_type' => 'required|in:days,weeks,months',
            'allows_remote' => 'boolean',
            'location' => 'nullable|string|max:255',
            'deadline' => 'nullable|date|after:today',
            'is_urgent' => 'boolean',
            'publish' => 'boolean',
        ]);

        $this->marketplaceService->forUser(auth()->user());
        $project = $this->marketplaceService->createProject($request->all());

        return response()->json([
            'success' => true,
            'project' => $project,
            'message' => $request->publish 
                ? 'Project published successfully!' 
                : 'Project saved as draft.',
            'redirect' => route('marketplace.employer.manage-project', $project),
        ]);
    }

    /**
     * AI-enhanced project description.
     */
    public function enhanceProject(Request $request): JsonResponse
    {
        $request->validate([
            'brief' => 'required|string|min:20',
            'category' => 'required|string',
        ]);

        $this->marketplaceService->forUser(auth()->user());
        $enhanced = $this->marketplaceService->enhanceProjectDescription(
            $request->brief,
            $request->category
        );

        return response()->json([
            'success' => !empty($enhanced),
            'enhanced' => $enhanced,
        ]);
    }

    /**
     * View project with proposals.
     */
    public function showProject(MarketplaceProject $project): View
    {
        $this->authorize('view', $project);

        $project->load(['proposals.freelancer', 'proposals.freelancerProfile.badges.badge']);
        
        $this->marketplaceService->forUser(auth()->user());
        $proposals = $this->marketplaceService->getProjectProposals($project);

        return view('marketplace.employer.project', [
            'project' => $project,
            'proposals' => $proposals,
        ]);
    }

    /**
     * Edit project.
     */
    public function editProject(MarketplaceProject $project): View
    {
        $this->authorize('update', $project);

        $categories = [
            'web_development' => 'Web Development',
            'mobile_development' => 'Mobile Development',
            // ... (same categories as create)
        ];

        return view('marketplace.employer.edit-project', [
            'project' => $project,
            'categories' => $categories,
        ]);
    }

    /**
     * Update project.
     */
    public function updateProject(Request $request, MarketplaceProject $project): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50|max:10000',
            // ... (same validation as store)
        ]);

        $project->update($request->only([
            'title', 'description', 'requirements', 'deliverables',
            'project_type', 'category', 'skills_required',
            'budget_min', 'budget_max', 'hourly_rate_min', 'hourly_rate_max',
            'currency', 'experience_level', 'estimated_duration_days',
            'duration_type', 'allows_remote', 'location', 'deadline', 'is_urgent',
        ]));

        return response()->json([
            'success' => true,
            'project' => $project->fresh(),
            'message' => 'Project updated successfully!',
        ]);
    }

    /**
     * Publish a draft project.
     */
    public function publishProject(MarketplaceProject $project): JsonResponse
    {
        $this->authorize('update', $project);

        if ($project->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft projects can be published.',
            ], 422);
        }

        $project->publish();

        return response()->json([
            'success' => true,
            'message' => 'Project published successfully!',
        ]);
    }

    /**
     * Close a project.
     */
    public function closeProject(MarketplaceProject $project): JsonResponse
    {
        $this->authorize('update', $project);

        if (!$project->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'This project is not open.',
            ], 422);
        }

        $project->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Project closed.',
        ]);
    }

    /**
     * Shortlist a proposal.
     */
    public function shortlistProposal(MarketplaceProposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal->project);

        if (!$proposal->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This proposal cannot be shortlisted.',
            ], 422);
        }

        $proposal->shortlist();
        $proposal->freelancer?->notify(new ProposalStatusNotification($proposal, 'shortlisted'));

        return response()->json([
            'success' => true,
            'message' => 'Proposal shortlisted! Freelancer has been notified.',
        ]);
    }

    /**
     * Send a hiring offer to a freelancer.
     */
    public function sendOffer(MarketplaceProposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal->project);

        if (!in_array($proposal->status, ['pending', 'shortlisted'], true)) {
            return response()->json(['success' => false, 'message' => 'Cannot send offer for this proposal.'], 422);
        }

        $proposal->sendOffer();
        $proposal->freelancer?->notify(new ProposalOfferNotification($proposal));

        return response()->json([
            'success' => true,
            'message' => 'Offer sent! The freelancer has been notified by email.',
        ]);
    }

    /**
     * Accept a proposal and create contract.
     */
    public function acceptProposal(Request $request, MarketplaceProposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal->project);

        $request->validate([
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required_with:milestones|string|max:255',
            'milestones.*.description' => 'nullable|string|max:1000',
            'milestones.*.amount' => 'required_with:milestones|numeric|min:1',
            'milestones.*.due_date' => 'nullable|date|after:today',
        ]);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $contract = $this->marketplaceService->acceptProposal($proposal, $request->milestones);

            return response()->json([
                'success' => true,
                'contract' => $contract,
                'message' => 'Proposal accepted! Contract created.',
                'redirect' => route('marketplace.contracts.show', $contract),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject a proposal.
     */
    public function rejectProposal(MarketplaceProposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal->project);

        $proposal->reject();
        $proposal->freelancer?->notify(new ProposalStatusNotification($proposal, 'rejected'));

        return response()->json([
            'success' => true,
            'message' => 'Proposal rejected. Freelancer has been notified.',
        ]);
    }

    /**
     * Find freelancers for a project (AI-matched).
     */
    public function findFreelancers(MarketplaceProject $project): JsonResponse
    {
        $this->authorize('view', $project);

        $this->marketplaceService->forUser(auth()->user());
        $freelancers = $this->marketplaceService->matchProjectWithFreelancers($project);

        return response()->json([
            'success' => true,
            'freelancers' => $freelancers,
        ]);
    }

    /**
     * Invite a freelancer to a project.
     */
    public function inviteFreelancer(Request $request, MarketplaceProject $project): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
        ]);

        // Check if already invited
        $existing = MarketplaceInvitation::where('project_id', $project->id)
            ->where('freelancer_id', $request->freelancer_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This freelancer has already been invited.',
            ], 422);
        }

        $invitation = MarketplaceInvitation::create([
            'project_id' => $project->id,
            'employer_id' => auth()->id(),
            'freelancer_id' => $request->freelancer_id,
            'message' => $request->message,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: Send notification to freelancer

        return response()->json([
            'success' => true,
            'invitation' => $invitation,
            'message' => 'Invitation sent!',
        ]);
    }

    /**
     * Save/unsave a freelancer.
     */
    public function toggleSaveFreelancer(FreelancerProfile $profile): JsonResponse
    {
        $saved = SavedFreelancer::where('employer_id', auth()->id())
            ->where('freelancer_id', $profile->user_id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
        } else {
            SavedFreelancer::create([
                'employer_id' => auth()->id(),
                'freelancer_id' => $profile->user_id,
            ]);
            $isSaved = true;
        }

        return response()->json([
            'success' => true,
            'isSaved' => $isSaved,
        ]);
    }

    /**
     * View saved freelancers.
     */
    public function savedFreelancers(): View
    {
        $savedFreelancers = SavedFreelancer::where('employer_id', auth()->id())
            ->with('freelancerProfile.user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('marketplace.employer.saved-freelancers', [
            'savedFreelancers' => $savedFreelancers,
        ]);
    }

    /**
     * My contracts as employer.
     */
    public function contracts(Request $request): View
    {
        $this->marketplaceService->forUser(auth()->user());
        
        $status = $request->get('status');
        $contracts = $this->marketplaceService->getMyContracts('employer', $status);

        return view('marketplace.employer.contracts', [
            'contracts' => $contracts,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Fund a milestone.
     */
    public function fundMilestone(Request $request, MarketplaceMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone->contract);

        if (!$milestone->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This milestone cannot be funded.',
            ], 422);
        }

        // Create escrow and initiate payment
        try {
            $this->marketplaceService->forUser(auth()->user());
            
            // For now, we'll create a simple payment flow
            // In production, integrate with Razorpay/PayU
            $escrow = \App\Models\MarketplaceEscrow::createForMilestone($milestone);

            return response()->json([
                'success' => true,
                'escrow' => $escrow,
                'payment_url' => route('marketplace.payment.create', $escrow),
                'message' => 'Proceed to payment to fund this milestone.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Request revision on milestone.
     */
    public function requestRevision(Request $request, MarketplaceMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone->contract);

        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $this->marketplaceService->requestRevision($milestone, $request->note);

            return response()->json([
                'success' => true,
                'message' => 'Revision requested.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Approve and release milestone payment.
     */
    public function approveMilestone(MarketplaceMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone->contract);

        try {
            $this->marketplaceService->forUser(auth()->user());
            $this->marketplaceService->approveMilestone($milestone);

            return response()->json([
                'success' => true,
                'message' => 'Milestone approved and payment released!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ── Route aliases & missing methods ───────────────────────────────────

    /** Route calls manageProject(); controller has showProject() */
    public function manageProject(MarketplaceProject $project): View
    {
        return $this->showProject($project);
    }

    /** Route calls reviewProposals() – dedicated proposal review page with AI scoring */
    public function reviewProposals(MarketplaceProject $project): View
    {
        $this->authorize('view', $project);

        $proposals = MarketplaceProposal::where('project_id', $project->id)
            ->with(['freelancer.freelancerProfile', 'project'])
            ->orderByDesc('ai_match_score')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'       => $proposals->count(),
            'pending'     => $proposals->where('status', 'pending')->count(),
            'shortlisted' => $proposals->where('status', 'shortlisted')->count(),
            'offered'     => $proposals->where('offer_sent_at', '!=', null)->where('status', 'shortlisted')->count(),
            'accepted'    => $proposals->where('status', 'accepted')->count(),
            'rejected'    => $proposals->where('status', 'rejected')->count(),
            'avg_score'   => $proposals->whereNotNull('ai_match_score')->avg('ai_match_score'),
        ];

        return view('marketplace.employer.review-proposals', compact('project', 'proposals', 'stats'));
    }

    /** Route calls hireFreelancer(); controller has acceptProposal() */
    public function hireFreelancer(Request $request, MarketplaceProposal $proposal): JsonResponse
    {
        return $this->acceptProposal($request, $proposal);
    }

    /** List all employer's projects */
    public function projects(Request $request): View
    {
        $status   = $request->get('status');
        $query    = MarketplaceProject::where('employer_id', auth()->id())->withCount('proposals')->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }
        $projects = $query->paginate(15);
        return view('marketplace.employer.projects', compact('projects'));
    }

    /** Delete a project */
    public function deleteProject(MarketplaceProject $project): JsonResponse
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->json(['success' => true, 'message' => 'Project deleted.']);
    }

    /** Show invite-a-freelancer form */
    public function showInviteForm(FreelancerProfile $profile): View
    {
        $myProjects = MarketplaceProject::where('employer_id', auth()->id())->open()->published()->get();
        return view('marketplace.employer.invite', compact('profile', 'myProjects'));
    }

    /** Send invitation (alias for inviteFreelancer – but takes profile, not project) */
    public function sendInvitation(Request $request, FreelancerProfile $profile): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:marketplace_projects,id',
            'message'    => 'nullable|string|max:1000',
        ]);

        $project = MarketplaceProject::findOrFail($request->project_id);
        $this->authorize('update', $project);

        $existing = \App\Models\MarketplaceInvitation::where('project_id', $project->id)
            ->where('freelancer_id', $profile->user_id)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Already invited.'], 422);
        }

        \App\Models\MarketplaceInvitation::create([
            'project_id'    => $project->id,
            'employer_id'   => auth()->id(),
            'freelancer_id' => $profile->user_id,
            'message'       => $request->message,
            'status'        => 'pending',
            'expires_at'    => now()->addDays(7),
        ]);

        return response()->json(['success' => true, 'message' => 'Invitation sent!']);
    }
}
