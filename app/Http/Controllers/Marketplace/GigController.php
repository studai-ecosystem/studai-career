<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\FreelancerGig;
use App\Models\FreelancerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    // ─────────────────────────────────────────────────────────────
    //  PUBLIC — Companies browse & view gigs
    // ─────────────────────────────────────────────────────────────

    /**
     * Browse all active gigs (company / visitor side).
     */
    public function index(Request $request): View
    {
        $query = FreelancerGig::active()
            ->with('freelancerProfile.user')
            ->withCount([]);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('budget_max')) {
            // Filter by starting price (cheapest package price)
            // We store packages as JSON; filter via raw
            $max = (int) $request->budget_max;
            $query->whereRaw("json_extract(packages, '$[0].price') <= ?", [$max]);
        }

        $sort = $request->get('sort', 'featured');
        match ($sort) {
            'rating'   => $query->orderByDesc('average_rating'),
            'orders'   => $query->orderByDesc('orders_count'),
            'newest'   => $query->orderByDesc('created_at'),
            default    => $query->orderByDesc('is_featured')->orderByDesc('average_rating'),
        };

        $gigs       = $query->paginate(12)->withQueryString();
        $categories = $this->categories();

        return view('marketplace.gigs.index', compact('gigs', 'categories'));
    }

    /**
     * Show a single gig detail page (company views to order).
     */
    public function show(FreelancerGig $gig): View
    {
        if ($gig->status !== 'active') {
            abort(404);
        }

        $gig->increment('views_count');
        $gig->load('freelancerProfile.user');

        $relatedGigs = FreelancerGig::active()
            ->where('category', $gig->category)
            ->where('id', '!=', $gig->id)
            ->with('freelancerProfile.user')
            ->limit(4)
            ->get();

        return view('marketplace.gigs.show', compact('gig', 'relatedGigs'));
    }

    /**
     * Place an order (company submits a request for a gig package).
     */
    public function placeOrder(Request $request, FreelancerGig $gig): RedirectResponse
    {
        $request->validate([
            'package_type'   => 'required|in:basic,standard,premium',
            'requirements'   => 'required|string|min:20|max:2000',
        ]);

        $pkg = $gig->getPackage($request->package_type);
        if (!$pkg) {
            return back()->with('error', 'Selected package not found.');
        }

        // Redirect to message the freelancer with the order details as a pre-filled message
        $message = "Hi! I'd like to order your **{$gig->title}** ({$pkg['title']} package).\n\n" .
                   "Budget: ₹" . number_format($pkg['price']) . "\n" .
                   "Delivery: {$pkg['delivery_days']} days\n\n" .
                   "My requirements:\n{$request->requirements}";

        return redirect()
            ->route('marketplace.message', $gig->freelancerProfile)
            ->with('prefill_message', $message)
            ->with('success', 'Your order request has been sent! The freelancer will contact you shortly.');
    }

    // ─────────────────────────────────────────────────────────────
    //  FREELANCER — Manage their own gigs
    // ─────────────────────────────────────────────────────────────

    /**
     * Freelancer's gig management dashboard.
     */
    public function myGigs(): View
    {
        $profile = auth()->user()->freelancerProfile;

        if (!$profile) {
            return view('marketplace.freelancer.gigs', [
                'gigs'    => collect(),
                'profile' => null,
            ]);
        }

        $gigs = FreelancerGig::where('freelancer_profile_id', $profile->id)
            ->withTrashed()
            ->orderByDesc('created_at')
            ->get();

        return view('marketplace.freelancer.gigs', compact('gigs', 'profile'));
    }

    /**
     * Show the create-gig form.
     */
    public function create(): View
    {
        $profile = auth()->user()->freelancerProfile;
        return view('marketplace.freelancer.create-gig', [
            'gig'        => null,
            'profile'    => $profile,
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Show the edit-gig form.
     */
    public function edit(FreelancerGig $gig): View
    {
        $this->authorizeGig($gig);
        return view('marketplace.freelancer.create-gig', [
            'gig'        => $gig,
            'profile'    => auth()->user()->freelancerProfile,
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Store a new gig.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->freelancerProfile;
        if (!$profile) {
            return back()->with('error', 'Please set up your freelancer profile first.');
        }

        $data = $this->validateGig($request);
        $data['freelancer_profile_id'] = $profile->id;
        $data['packages'] = $this->buildPackages($request);

        FreelancerGig::create($data);

        return redirect()->route('marketplace.freelancer.gigs')
            ->with('success', 'Gig created successfully! It\'s now live on the marketplace.');
    }

    /**
     * Update an existing gig.
     */
    public function update(Request $request, FreelancerGig $gig): RedirectResponse
    {
        $this->authorizeGig($gig);

        $data = $this->validateGig($request);
        $data['packages'] = $this->buildPackages($request);

        $gig->update($data);

        return redirect()->route('marketplace.freelancer.gigs')
            ->with('success', 'Gig updated successfully.');
    }

    /**
     * Delete a gig.
     */
    public function destroy(FreelancerGig $gig): RedirectResponse
    {
        $this->authorizeGig($gig);
        $gig->delete();

        return back()->with('success', 'Gig removed from marketplace.');
    }

    /**
     * Toggle gig status (active ↔ paused).
     */
    public function toggleStatus(FreelancerGig $gig): RedirectResponse
    {
        $this->authorizeGig($gig);
        $gig->update([
            'status' => $gig->status === 'active' ? 'paused' : 'active',
        ]);

        return back()->with('success', 'Gig status updated.');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────

    private function authorizeGig(FreelancerGig $gig): void
    {
        $profile = auth()->user()->freelancerProfile;
        if (!$profile || $gig->freelancer_profile_id !== $profile->id) {
            abort(403);
        }
    }

    private function validateGig(Request $request): array
    {
        return $request->validate([
            'title'        => 'required|string|min:15|max:150',
            'description'  => 'required|string|min:50',
            'category'     => 'required|string',
            'tags'         => 'nullable|string',
            'requirements' => 'nullable|string',
            'status'       => 'in:draft,active,paused',
        ]) + [
            'tags'         => $this->parseTags($request->tags),
            'status'       => $request->input('status', 'active'),
        ];
    }

    private function parseTags(?string $raw): array
    {
        if (!$raw) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    private function buildPackages(Request $request): array
    {
        $packages = [];
        foreach (['basic', 'standard', 'premium'] as $type) {
            $packages[] = [
                'type'          => $type,
                'title'         => $request->input("pkg_{$type}_title", ucfirst($type)),
                'price'         => (int) $request->input("pkg_{$type}_price", 0),
                'delivery_days' => (int) $request->input("pkg_{$type}_days", 7),
                'revisions'     => (int) $request->input("pkg_{$type}_revisions", 2),
                'features'      => array_values(array_filter(
                    array_map('trim', explode("\n", $request->input("pkg_{$type}_features") ?? ''))
                )),
            ];
        }
        return $packages;
    }

    private function categories(): array
    {
        return [
            'web_development'    => ['label' => 'Web Development',    'icon' => '💻', 'bg' => 'linear-gradient(135deg,#3b82f6,#4f46e5)'],
            'mobile_development' => ['label' => 'Mobile Apps',        'icon' => '📱', 'bg' => 'linear-gradient(135deg,#a855f7,#ec4899)'],
            'design'             => ['label' => 'Design & Creative',  'icon' => '🎨', 'bg' => 'linear-gradient(135deg,#ec4899,#f43f5e)'],
            'writing'            => ['label' => 'Writing & Content',  'icon' => '✍️', 'bg' => 'linear-gradient(135deg,#f59e0b,#f97316)'],
            'marketing'          => ['label' => 'Digital Marketing',  'icon' => '📣', 'bg' => 'linear-gradient(135deg,#22c55e,#14b8a6)'],
            'data_science'       => ['label' => 'Data & Analytics',   'icon' => '📊', 'bg' => 'linear-gradient(135deg,#06b6d4,#3b82f6)'],
            'ai_ml'              => ['label' => 'AI & ML',            'icon' => '🤖', 'bg' => 'linear-gradient(135deg,#7c3aed,#9333ea)'],
            'devops'             => ['label' => 'DevOps & Cloud',     'icon' => '⚙️', 'bg' => 'linear-gradient(135deg,#64748b,#374151)'],
            'video_production'   => ['label' => 'Video & Animation',  'icon' => '🎬', 'bg' => 'linear-gradient(135deg,#ef4444,#ec4899)'],
            'consulting'         => ['label' => 'Consulting',         'icon' => '💼', 'bg' => 'linear-gradient(135deg,#14b8a6,#06b6d4)'],
        ];
    }
}
