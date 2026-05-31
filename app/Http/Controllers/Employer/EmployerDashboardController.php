<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\CompanyIntelligenceProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employer']);
    }

    public function index()
    {
        $user    = auth()->user();
        $company = $user->company;

        // Guard: employer has no company linked yet — prompt them to complete onboarding
        if (! $company) {
            return redirect()->route('employer.onboarding')
                ->with('info', 'Please complete your company profile to access the dashboard.');
        }

        $cid = $company->id;

        // ------------------------------------------------------------------
        // All base counts in a single batch â€” no N+1
        // ------------------------------------------------------------------
        $jobCounts = Cache::remember("employer_job_counts_{$cid}", 300, function () use ($cid) {
            $nowTs = now()->toDateTimeString();
            return Job::where('company_id', $cid)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'published' AND (expires_at IS NULL OR expires_at > ?) THEN 1 ELSE 0 END) as active
                ", [$nowTs])
                ->first();
        });

        $totalJobs  = (int) ($jobCounts->total  ?? 0);
        $activeJobs = (int) ($jobCounts->active ?? 0);

        // Application counts â€” one query with GROUP BY status
        $appCounts = Cache::remember("employer_app_counts_{$cid}", 120, function () use ($cid) {
            $weekAgo = now()->subDays(7)->toDateTimeString();
            return Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
                ->where('job_listings.company_id', $cid)
                ->selectRaw("COUNT(*) as total, SUM(CASE WHEN applications.status = 'pending' AND applications.created_at >= ? THEN 1 ELSE 0 END) as new_pending", [$weekAgo])
                ->first();
        });

        $totalApplications = (int) ($appCounts->total       ?? 0);
        $newApplications   = (int) ($appCounts->new_pending ?? 0);

        // Status breakdown â€” single GROUP BY query
        $applicationsByStatus = Cache::remember("employer_status_counts_{$cid}", 120, function () use ($cid) {
            return Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
                ->where('job_listings.company_id', $cid)
                ->select('applications.status', DB::raw('COUNT(*) as count'))
                ->groupBy('applications.status')
                ->pluck('count', 'applications.status')
                ->toArray();
        });

        // Recent applications â€” eager loaded, limited
        $recentApplications = Application::with(['job:id,title,location', 'user:id,name,email', 'user.profile:id,user_id,avatar'])
            ->join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $cid)
            ->select('applications.*')
            ->latest('applications.created_at')
            ->take(10)
            ->get();

        // Jobs with counts
        $jobsWithApplications = Job::where('company_id', $cid)
            ->withCount([
                'applications as total_applications',
                'applications as new_applications' => fn($q) => $q->where('created_at', '>=', now()->subDays(7)),
            ])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Weekly trend â€” ONE batch query instead of 4 separate queries
        $weeklyTrend = Cache::remember("employer_weekly_trend_{$cid}", 300, function () use ($cid) {
            $weekExpr = DB::connection()->getDriverName() === 'sqlite'
                ? "strftime('%Y-%W', applications.created_at)"
                : "DATE_FORMAT(applications.created_at, '%Y-%u')";
            $rows = Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
                ->where('job_listings.company_id', $cid)
                ->where('applications.created_at', '>=', now()->subWeeks(4)->startOfWeek())
                ->selectRaw("{$weekExpr} as week_key, COUNT(*) as count")
                ->groupByRaw($weekExpr)
                ->orderBy('week_key')
                ->pluck('count', 'week_key');

            $result = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $key       = $weekStart->format('Y-W');
                $result[]  = [
                    'week'  => $weekStart->format('M d'),
                    'count' => (int) ($rows[$key] ?? 0),
                ];
            }
            return $result;
        });

        // Top performing jobs
        $topJobs = Job::where('company_id', $cid)
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->take(5)
            ->get();

        // Orinâ„¢ active jobs
        $orinJobs = Job::where('company_id', $cid)
            ->whereNotNull('application_link_token')
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        // Recent active jobs for sidebar
        $recentJobs = Job::where('company_id', $cid)
            ->where('status', 'published')
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        $intelligenceProfile = CompanyIntelligenceProfile::where('company_id', $cid)->first();

        return view('employer.dashboard.index', compact(
            'company', 'totalJobs', 'activeJobs', 'totalApplications', 'newApplications',
            'applicationsByStatus', 'recentApplications', 'jobsWithApplications',
            'weeklyTrend', 'topJobs', 'orinJobs', 'recentJobs', 'intelligenceProfile'
        ));
    }

    public function analytics()
    {
        $user    = auth()->user();
        $company = $user->company;
        $cid     = $company->id;

        // Monthly trend â€” ONE batch query instead of 12 separate queries
        $monthlyRaw = Cache::remember("employer_monthly_trend_{$cid}", 600, function () use ($cid) {
            $monthExpr = DB::connection()->getDriverName() === 'sqlite'
                ? "strftime('%Y-%m', applications.created_at)"
                : "DATE_FORMAT(applications.created_at, '%Y-%m')";
            return Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
                ->where('job_listings.company_id', $cid)
                ->where('applications.created_at', '>=', now()->subMonths(12)->startOfMonth())
                ->selectRaw("{$monthExpr} as month_key, COUNT(*) as count")
                ->groupByRaw($monthExpr)
                ->pluck('count', 'month_key');
        });

        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date        = now()->subMonths($i);
            $key         = $date->format('Y-m');
            $monthlyData[] = [
                'month'        => $date->format('M Y'),
                'applications' => (int) ($monthlyRaw[$key] ?? 0),
            ];
        }

        // Applications by job type â€” single JOIN query
        $applicationsByJobType = Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $cid)
            ->select('job_listings.employment_type', DB::raw('COUNT(*) as count'))
            ->groupBy('job_listings.employment_type')
            ->pluck('count', 'employment_type')
            ->toArray();

        // Average time to hire â€” DB aggregation, no PHP avg()
        $diffExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "AVG(julianday(applications.updated_at) - julianday(applications.created_at))"
            : "AVG(DATEDIFF(applications.updated_at, applications.created_at))";
        $averageTimeToHire = Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $cid)
            ->where('applications.status', 'hired')
            ->whereNotNull('applications.updated_at')
            ->selectRaw("{$diffExpr} as avg_days")
            ->value('avg_days');

        // Conversion rates â€” single query with conditional counts
        $conversionRaw = Application::join('job_listings', 'applications.job_id', '=', 'job_listings.id')
            ->where('job_listings.company_id', $cid)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN applications.status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN applications.status = 'hired' THEN 1 ELSE 0 END) as hired
            ")
            ->first();

        $totalApps      = (int) ($conversionRaw->total       ?? 0);
        $shortlistedApps = (int) ($conversionRaw->shortlisted ?? 0);
        $hiredApps      = (int) ($conversionRaw->hired        ?? 0);

        $conversionRates = [
            'shortlist_rate' => $totalApps > 0 ? round(($shortlistedApps / $totalApps) * 100, 1) : 0,
            'hire_rate'      => $totalApps > 0 ? round(($hiredApps       / $totalApps) * 100, 1) : 0,
        ];

        return view('employer.dashboard.analytics', compact(
            'monthlyData', 'applicationsByJobType', 'averageTimeToHire',
            'conversionRates', 'totalApps', 'shortlistedApps', 'hiredApps'
        ));
    }

    /** Bust dashboard caches when application data changes */
    public static function bustCaches(int $companyId): void
    {
        Cache::forget("employer_job_counts_{$companyId}");
        Cache::forget("employer_app_counts_{$companyId}");
        Cache::forget("employer_status_counts_{$companyId}");
        Cache::forget("employer_weekly_trend_{$companyId}");
        Cache::forget("employer_monthly_trend_{$companyId}");
    }
}
