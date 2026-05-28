<?php

namespace App\Services;

use App\Models\User;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ApplicationTrackerService
{
    /**
     * Get application pipeline statistics
     */
    public function getPipelineStats(User $user)
    {
        // Single aggregated SQL query instead of loading all applications into PHP
        $counts = Application::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN status = 'viewed' THEN 1 ELSE 0 END) as viewed,
                SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN status = 'interview_scheduled' THEN 1 ELSE 0 END) as interview_scheduled,
                SUM(CASE WHEN status = 'interviewed' THEN 1 ELSE 0 END) as interviewed,
                SUM(CASE WHEN status = 'offered' THEN 1 ELSE 0 END) as offered,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn,
                SUM(CASE WHEN status IN ('submitted','viewed','shortlisted','interview_scheduled','interviewed','offered','accepted','rejected') THEN 1 ELSE 0 END) as submitted_total,
                SUM(CASE WHEN status NOT IN ('draft','submitted') THEN 1 ELSE 0 END) as responded,
                SUM(CASE WHEN status NOT IN ('rejected','withdrawn','accepted') THEN 1 ELSE 0 END) as active
            ")
            ->first();

        $byStatus = [
            'draft'                => (int) ($counts->draft ?? 0),
            'submitted'            => (int) ($counts->submitted ?? 0),
            'viewed'               => (int) ($counts->viewed ?? 0),
            'shortlisted'          => (int) ($counts->shortlisted ?? 0),
            'interview_scheduled'  => (int) ($counts->interview_scheduled ?? 0),
            'interviewed'          => (int) ($counts->interviewed ?? 0),
            'offered'              => (int) ($counts->offered ?? 0),
            'accepted'             => (int) ($counts->accepted ?? 0),
            'rejected'             => (int) ($counts->rejected ?? 0),
            'withdrawn'            => (int) ($counts->withdrawn ?? 0),
        ];

        $submittedTotal = (int) ($counts->submitted_total ?? 0);
        $responded      = (int) ($counts->responded ?? 0);
        $successful     = $byStatus['offered'] + $byStatus['accepted'];

        $stats = [
            'total'              => (int) ($counts->total ?? 0),
            'by_status'          => $byStatus,
            'success_rate'       => $submittedTotal > 0 ? round(($successful / $submittedTotal) * 100, 1) : 0,
            'response_rate'      => $submittedTotal > 0 ? round(($responded / $submittedTotal) * 100, 1) : 0,
            'avg_response_time'  => 0,
            'active_applications'=> (int) ($counts->active ?? 0),
        ];

        // Average response time requires row-level data — keep a targeted query
        $avgDays = Application::where('user_id', $user->id)
            ->whereNotNull('viewed_at')
            ->whereNotNull('submitted_at')
            ->selectRaw('AVG(DATEDIFF(viewed_at, submitted_at)) as avg_days')
            ->value('avg_days');

        if ($avgDays !== null) {
            $stats['avg_response_time'] = round((float) $avgDays, 1);
        }

        return $stats;
    }
    
    /**
     * Get timeline visualization data
     */
    public function getApplicationTimeline(User $user)
    {
        $applications = Application::where('user_id', $user->id)
            ->with('job')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        $timeline = [];
        
        foreach ($applications as $application) {
            $timelineData = json_decode($application->timeline, true) ?? [];
            
            foreach ($timelineData as $event) {
                $timeline[] = [
                    'application_id' => $application->id,
                    'job_title' => $application->job->title,
                    'company' => $application->job->company_name,
                    'event_type' => $event['status'],
                    'event_date' => $event['timestamp'],
                    'notes' => $event['notes'] ?? null,
                ];
            }
        }
        
        // Sort by date descending
        usort($timeline, function ($a, $b) {
            return strtotime($b['event_date']) - strtotime($a['event_date']);
        });
        
        return $timeline;
    }
    
    /**
     * Get pending follow-ups
     */
    public function getPendingFollowUps(User $user)
    {
        $applications = Application::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'viewed', 'interview_scheduled'])
            ->where(function ($q) {
                // Pre-filter: only fetch apps older than 3 days to avoid loading everything
                $q->where('submitted_at', '<', now()->subDays(3))
                  ->orWhere('updated_at', '<', now()->subDays(3));
            })
            ->with('job')
            ->get();
        
        $followUps = [];
        
        foreach ($applications as $application) {
            $daysSinceSubmission = Carbon::parse($application->submitted_at)->diffInDays(now());
            $daysSinceLastUpdate = Carbon::parse($application->updated_at)->diffInDays(now());
            
            $shouldFollowUp = false;
            $urgency = 'low';
            $message = '';
            
            // Follow-up logic based on status and time
            if ($application->status === 'submitted' && $daysSinceSubmission >= 7) {
                $shouldFollowUp = true;
                $urgency = $daysSinceSubmission >= 14 ? 'high' : 'medium';
                $message = "No response after {$daysSinceSubmission} days. Consider following up.";
            }
            
            if ($application->status === 'viewed' && $daysSinceLastUpdate >= 5) {
                $shouldFollowUp = true;
                $urgency = 'medium';
                $message = "Application viewed {$daysSinceLastUpdate} days ago. Follow up to express continued interest.";
            }
            
            if ($application->status === 'interview_scheduled' && $daysSinceLastUpdate >= 3) {
                $shouldFollowUp = true;
                $urgency = 'high';
                $message = "Interview scheduled. Send confirmation or preparation questions.";
            }
            
            if ($shouldFollowUp) {
                $followUps[] = [
                    'application' => $application,
                    'urgency' => $urgency,
                    'message' => $message,
                    'days_since_submission' => $daysSinceSubmission,
                    'days_since_update' => $daysSinceLastUpdate,
                    'suggested_action' => $this->getSuggestedFollowUpAction($application),
                ];
            }
        }
        
        // Sort by urgency (high, medium, low)
        usort($followUps, function ($a, $b) {
            $priority = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priority[$b['urgency']] - $priority[$a['urgency']];
        });
        
        return $followUps;
    }
    
    /**
     * Get suggested follow-up action
     */
    protected function getSuggestedFollowUpAction(Application $application)
    {
        $actions = [
            'submitted' => [
                'type' => 'email',
                'subject' => 'Following Up on Application for ' . $application->job->title,
                'template' => 'follow_up_initial',
                'message' => 'Express continued interest and inquire about timeline',
            ],
            'viewed' => [
                'type' => 'email',
                'subject' => 'Checking In: ' . $application->job->title . ' Position',
                'template' => 'follow_up_viewed',
                'message' => 'Reiterate interest and availability for discussion',
            ],
            'interview_scheduled' => [
                'type' => 'email',
                'subject' => 'Interview Confirmation - ' . $application->job->title,
                'template' => 'interview_confirmation',
                'message' => 'Confirm attendance and ask about preparation materials',
            ],
        ];
        
        return $actions[$application->status] ?? [
            'type' => 'email',
            'subject' => 'Following Up',
            'template' => 'generic_follow_up',
            'message' => 'Send a polite follow-up email',
        ];
    }
    
    /**
     * Get response rate analytics by company
     */
    public function getResponseRateByCompany(User $user)
    {
        $applications = Application::where('user_id', $user->id)
            ->with('job')
            ->whereNotNull('submitted_at')
            ->get();
        
        $byCompany = [];
        
        foreach ($applications as $application) {
            if (!$application->job) continue;
            $company = $application->job->company_name ?? 'Unknown';
            
            if (!isset($byCompany[$company])) {
                $byCompany[$company] = [
                    'total' => 0,
                    'responded' => 0,
                    'response_rate' => 0,
                    'avg_response_time' => 0,
                    'response_times' => [],
                ];
            }
            
            $byCompany[$company]['total']++;
            
            if ($application->viewed_at) {
                $byCompany[$company]['responded']++;
                $responseTime = Carbon::parse($application->submitted_at)
                    ->diffInDays(Carbon::parse($application->viewed_at));
                $byCompany[$company]['response_times'][] = $responseTime;
            }
        }
        
        // Calculate rates and averages
        foreach ($byCompany as $company => &$data) {
            if ($data['total'] > 0) {
                $data['response_rate'] = round(($data['responded'] / $data['total']) * 100, 1);
            }
            
            if (count($data['response_times']) > 0) {
                $data['avg_response_time'] = round(array_sum($data['response_times']) / count($data['response_times']), 1);
            }
            
            unset($data['response_times']); // Remove raw data
        }
        
        // Sort by total applications descending
        uasort($byCompany, function ($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        return $byCompany;
    }
    
    /**
     * Get application trends over time
     */
    public function getApplicationTrends(User $user, $months = 6)
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        $rows = Application::where('user_id', $user->id)
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw("
                DATE_FORMAT(submitted_at, '%Y-%m') as month_key,
                COUNT(*) as submitted,
                SUM(CASE WHEN viewed_at IS NOT NULL THEN 1 ELSE 0 END) as responses,
                SUM(CASE WHEN status IN ('interview_scheduled','interviewed') THEN 1 ELSE 0 END) as interviews,
                SUM(CASE WHEN status IN ('offered','accepted') THEN 1 ELSE 0 END) as offers,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejections
            ")
            ->groupByRaw("DATE_FORMAT(submitted_at, '%Y-%m')")
            ->pluck(null, 'month_key')
            ->all();

        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $month    = now()->subMonths($months - $i - 1);
            $monthKey = $month->format('Y-m');
            $row      = $rows[$monthKey] ?? null;

            $trends[] = [
                'month'       => $month->format('M Y'),
                'month_key'   => $monthKey,
                'submitted'   => (int) ($row->submitted   ?? 0),
                'responses'   => (int) ($row->responses   ?? 0),
                'interviews'  => (int) ($row->interviews  ?? 0),
                'offers'      => (int) ($row->offers      ?? 0),
                'rejections'  => (int) ($row->rejections  ?? 0),
            ];
        }

        return $trends;
    }
    
    /**
     * Get interview scheduling data
     */
    public function getInterviewSchedule(User $user)
    {
        $applications = Application::where('user_id', $user->id)
            ->whereIn('status', ['interview_scheduled', 'interviewed'])
            ->with('job')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $schedule = [
            'upcoming' => [],
            'past' => [],
            'total' => $applications->count(),
        ];
        
        foreach ($applications as $application) {
            $timeline = json_decode($application->timeline, true) ?? [];
            $interviewDate = null;
            
            // Find interview date in timeline
            foreach ($timeline as $event) {
                if ($event['status'] === 'interview_scheduled' && isset($event['interview_date'])) {
                    $interviewDate = Carbon::parse($event['interview_date']);
                    break;
                }
            }
            
            if ($interviewDate) {
                $interviewData = [
                    'application_id' => $application->id,
                    'job_title' => $application->job->title,
                    'company' => $application->job->company_name,
                    'date' => $interviewDate->format('Y-m-d H:i'),
                    'days_until' => now()->diffInDays($interviewDate, false),
                    'type' => $timeline[array_key_last($timeline)]['interview_type'] ?? 'Not specified',
                    'status' => $application->status,
                ];
                
                if ($interviewDate->isFuture()) {
                    $schedule['upcoming'][] = $interviewData;
                } else {
                    $schedule['past'][] = $interviewData;
                }
            }
        }
        
        // Sort upcoming by date ascending
        usort($schedule['upcoming'], function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        // Sort past by date descending
        usort($schedule['past'], function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $schedule;
    }
    
    /**
     * Update application status and timeline
     */
    public function updateApplicationStatus(Application $application, $newStatus, $notes = null)
    {
        $timeline = json_decode($application->timeline, true) ?? [];
        
        // Add new event to timeline
        $timeline[] = [
            'status' => $newStatus,
            'timestamp' => now()->toDateTimeString(),
            'notes' => $notes,
        ];
        
        // Update application
        $application->update([
            'status' => $newStatus,
            'timeline' => json_encode($timeline),
            'viewed_at' => in_array($newStatus, ['viewed', 'shortlisted', 'interview_scheduled']) && !$application->viewed_at
                ? now()
                : $application->viewed_at,
        ]);
        
        return $application;
    }
    
    /**
     * Generate follow-up reminder email content
     */
    public function generateFollowUpEmail(Application $application)
    {
        $job = $application->job;
        $daysSinceSubmission = Carbon::parse($application->submitted_at)->diffInDays(now());
        
        $templates = [
            'submitted' => "Subject: Following Up on {$job->title} Application\n\nDear Hiring Manager,\n\nI hope this email finds you well. I recently applied for the {$job->title} position at {$job->company_name} and wanted to follow up on my application submitted {$daysSinceSubmission} days ago.\n\nI remain very interested in this opportunity and believe my skills in [YOUR KEY SKILLS] would be a great match for your team's needs.\n\nI would welcome the opportunity to discuss how I can contribute to {$job->company_name}. Please let me know if you need any additional information.\n\nThank you for your consideration.\n\nBest regards,\n[YOUR NAME]",
            
            'viewed' => "Subject: Continued Interest in {$job->title} Position\n\nDear Hiring Manager,\n\nThank you for reviewing my application for the {$job->title} position at {$job->company_name}.\n\nI wanted to reiterate my strong interest in this role and my enthusiasm about the possibility of joining your team. I'm particularly excited about [SPECIFIC ASPECT OF THE JOB/COMPANY].\n\nI would be happy to provide any additional information or discuss my qualifications further at your convenience.\n\nLooking forward to hearing from you.\n\nBest regards,\n[YOUR NAME]",
            
            'interview_scheduled' => "Subject: Interview Confirmation - {$job->title}\n\nDear Hiring Manager,\n\nThank you for inviting me to interview for the {$job->title} position. I'm writing to confirm my attendance and express my enthusiasm about this opportunity.\n\nI'm looking forward to learning more about the role and discussing how my experience can benefit {$job->company_name}.\n\nPlease let me know if there are any materials I should review or prepare in advance of our meeting.\n\nThank you again for this opportunity.\n\nBest regards,\n[YOUR NAME]",
        ];
        
        return $templates[$application->status] ?? $templates['submitted'];
    }
    
    /**
     * Get application success insights
     */
    public function getSuccessInsights(User $user)
    {
        $applications = Application::where('user_id', $user->id)
            ->with('job')
            ->whereNotNull('submitted_at')
            ->get();
        
        if ($applications->count() === 0) {
            return null;
        }
        
        $insights = [];
        
        // Best performing job types
        $byJobType = $applications->groupBy(fn($app) => $app->job->employment_type ?? 'unknown');
        $jobTypeStats = [];
        foreach ($byJobType as $type => $apps) {
            $responses = $apps->whereNotNull('viewed_at')->count();
            $jobTypeStats[$type] = [
                'total' => $apps->count(),
                'response_rate' => $apps->count() > 0 ? round(($responses / $apps->count()) * 100, 1) : 0,
            ];
        }
        arsort($jobTypeStats);
        $insights['best_job_type'] = array_key_first($jobTypeStats);
        
        // Best time to apply (day of week)
        $byDayOfWeek = $applications->groupBy(fn($app) => 
            Carbon::parse($app->submitted_at)->dayOfWeek
        );
        $dayStats = [];
        foreach ($byDayOfWeek as $day => $apps) {
            $responses = $apps->whereNotNull('viewed_at')->count();
            $dayStats[$day] = [
                'total' => $apps->count(),
                'response_rate' => $apps->count() > 0 ? round(($responses / $apps->count()) * 100, 1) : 0,
            ];
        }
        arsort($dayStats);
        $insights['best_day_to_apply'] = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][array_key_first($dayStats)];
        
        // Average match score of successful applications
        $successful = $applications->whereIn('status', ['offered', 'accepted', 'interviewed']);
        if ($successful->count() > 0) {
            $insights['avg_successful_match_score'] = round($successful->avg('match_score'), 1);
        }
        
        return $insights;
    }
}
