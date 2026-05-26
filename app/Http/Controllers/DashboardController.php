<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Services\AI\JobMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $jobMatchingService;

    public function __construct(JobMatchingService $jobMatchingService)
    {
        $this->jobMatchingService = $jobMatchingService;
    }

    /**
     * Display the user dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Redirect employers to their own dashboard
        if ($user->isEmployer()) {
            return redirect()->route('employer.home');
        }

        // Get or create user subscription
        $freePlan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Free plan',
                'price' => 0,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'features' => [],
                'is_active' => true,
            ]
        );

        $subscription = UserSubscription::firstOrCreate(
            ['user_id' => $user->id],
            [
                'subscription_plan_id' => $freePlan->id,
                'status' => 'active',
                'starts_at' => now(),
            ]
        );

        // Get recent applications (last 5)
        $recentApplications = Application::where('user_id', $user->id)
            ->with(['job.company'])
            ->latest()
            ->take(5)
            ->get();

        // Get application statistics — single aggregated query with caching (2 min TTL)
        $applicationStats = Cache::remember("app_stats_{$user->id}", 120, function () use ($user) {
            $counts = Application::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status IN ('submitted','pending') THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status IN ('viewed','reviewing') THEN 1 ELSE 0 END) as reviewing,
                    SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                ")
                ->first();

            return [
                'total'       => (int) ($counts->total ?? 0),
                'pending'     => (int) ($counts->pending ?? 0),
                'reviewing'   => (int) ($counts->reviewing ?? 0),
                'shortlisted' => (int) ($counts->shortlisted ?? 0),
                'rejected'    => (int) ($counts->rejected ?? 0),
            ];
        });

        // Get profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($user);

        // Get job recommendations (cached for 1 hour)
        $recommendedJobs = Cache::remember(
            "job_recommendations_{$user->id}",
            3600,
            function () use ($user) {
                try {
                    $recommendations = $this->jobMatchingService->getRecommendations($user, [], 6);
                    return collect($recommendations)->pluck('job');
                } catch (\Exception $e) {
                    // Fallback to recent jobs if AI matching fails
                    return Job::where('status', 'published')
                        ->where('expires_at', '>', now())
                        ->latest()
                        ->take(6)
                        ->get();
                }
            }
        );

        // Get saved jobs count
        $savedJobsCount = $user->savedJobs()->count();

        // Get subscription usage stats
        $remainingApps = $user->getRemainingApplications();
        $remainingCredits = $user->getRemainingAICredits();
        $plan = $subscription->subscriptionPlan ?? null;
        $appsLimit = $plan?->applications_limit ?? 0;
        $creditsLimit = ($plan?->ai_credits === -1 || $plan?->ai_credits === null) ? null : ($plan?->ai_credits ?? 0);
        $appsUsed = $subscription?->applications_used_this_month ?? 0;
        $creditsUsed = $subscription?->ai_credits_used_this_month ?? 0;

        $subscriptionStats = [
            'applications_remaining' => $remainingApps === -1 ? '∞' : $remainingApps,
            'applications_remaining_raw' => $remainingApps,
            'applications_limit' => $appsLimit,
            'applications_used' => $appsUsed,
            'ai_credits_remaining' => $remainingCredits === -1 ? '∞' : $remainingCredits,
            'ai_credits_remaining_raw' => $remainingCredits,
            'ai_credits_limit' => $creditsLimit,
            'ai_credits_used' => $creditsUsed,
            'is_free_plan' => ($plan?->price ?? 0) == 0,
            'is_unlimited' => $remainingApps === -1,
            'plan_name' => $plan?->name ?? 'Free',
            'billing_period' => $plan?->billing_period ?? 'monthly',
            'next_billing_date' => $subscription?->next_billing_date,
        ];

        return view('dashboard.index', compact(
            'user',
            'subscription',
            'recentApplications',
            'applicationStats',
            'profileCompletion',
            'recommendedJobs',
            'savedJobsCount',
            'subscriptionStats'
        ));
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user): int
    {
        $profile = $user->profile;
        if (!$profile) {
            return 0;
        }

        $completionFactors = [
            'basic_info' => !empty($user->name) && !empty($user->email) ? 10 : 0,
            'bio' => !empty($profile->bio) ? 10 : 0,
            'phone' => !empty($profile->phone) ? 5 : 0,
            'location' => !empty($profile->location) ? 5 : 0,
            'education' => !empty($profile->education) ? 20 : 0,
            'experience' => !empty($profile->experience) ? 20 : 0,
            'skills' => !empty($profile->skills) ? 15 : 0,
            'resume' => !empty($profile->resume_path) ? 10 : 0,
            'preferences' => !empty($profile->job_preferences) ? 5 : 0,
        ];

        return array_sum($completionFactors);
    }

    /**
     * Display AI Credits history page
     */
    public function aiCredits(Request $request)
    {
        $user = $request->user();

        $logs = \App\Models\AICreditLog::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        // Single aggregated query instead of two separate SUM calls
        $aggRow = \App\Models\AICreditLog::where('user_id', $user->id)
            ->selectRaw("
                SUM(credits_used) as total_used,
                SUM(CASE WHEN strftime('%m', created_at) = ? AND strftime('%Y', created_at) = ? THEN credits_used ELSE 0 END) as this_month
            ", [str_pad(now()->month, 2, '0', STR_PAD_LEFT), (string) now()->year])
            ->first();

        $totalUsed = (int) ($aggRow->total_used ?? 0);
        $thisMonth = (int) ($aggRow->this_month ?? 0);

        $byAction = \App\Models\AICreditLog::where('user_id', $user->id)
            ->selectRaw('action, SUM(credits_used) as total, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('total')
            ->get();

        // Subscription stats for the header
        $subscription = $user->subscription;
        $plan = $subscription?->subscriptionPlan;
        $creditsLimit = $plan?->ai_credits ?? 0;
        $creditsUsed  = $subscription?->ai_credits_used_this_month ?? 0;
        $creditsLeft  = $user->getRemainingAICredits();

        return view('dashboard.ai-credits', compact(
            'logs', 'totalUsed', 'thisMonth', 'byAction',
            'creditsLimit', 'creditsUsed', 'creditsLeft', 'plan'
        ));
    }

    /**
     * Display application tracking page
     */
    public function applications(Request $request)
    {
        $user = $request->user();
        
        // Get filter parameters
        $status = $request->get('status');
        $search = $request->get('search');

        // Build query
        $query = Application::where('user_id', $user->id)
            ->with(['job.company', 'job']);

        // Apply status filter
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search filter
        if ($search) {
            $query->whereHas('job', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Paginate results
        $applications = $query->latest()->paginate(20);

        // Get status counts for filter badges — single aggregated query
        $rawCounts = Application::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('submitted','pending') THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status IN ('viewed','reviewing') THEN 1 ELSE 0 END) as reviewing,
                SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN status = 'hired' THEN 1 ELSE 0 END) as hired,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        $statusCounts = [
            'all'         => (int) ($rawCounts->total ?? 0),
            'pending'     => (int) ($rawCounts->pending ?? 0),
            'reviewing'   => (int) ($rawCounts->reviewing ?? 0),
            'shortlisted' => (int) ($rawCounts->shortlisted ?? 0),
            'hired'       => (int) ($rawCounts->hired ?? 0),
            'rejected'    => (int) ($rawCounts->rejected ?? 0),
        ];

        return view('dashboard.applications', compact('applications', 'statusCounts', 'status', 'search'));
    }
}
