<?php

namespace App\Http\Controllers;

use App\Models\SkillAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SkillAnalyzerWebController extends Controller
{
    public function dashboard(): View|Response
    {
        $user = Auth::user();

        $gaps             = $this->safely('skillGaps',        fn () => $user->skillGaps()->with('learningPath')->rankedByPriority()->limit(20)->get());
        $activePaths      = $this->safely('learningPaths',    fn () => $user->learningPaths()->active()->with('resources')->get());
        $validations      = $this->safely('skillValidations', fn () => $user->skillValidations()->highConfidence()->limit(10)->get());
        $recentAssessments = $this->safely('skillAssessments', fn () => $user->skillAssessments()->latest()->limit(5)->get());

        // Mint the throwaway API token here (not in the Blade view).
        $apiToken = '';
        try {
            $apiToken = $user->createToken('skills-dashboard')->plainTextToken;
        } catch (\Throwable $e) {
            Log::error('Skills dashboard token mint failed', ['message' => $e->getMessage()]);
        }

        $emptyData = [
            'gaps'              => collect(),
            'activePaths'       => collect(),
            'validations'       => collect(),
            'recentAssessments' => collect(),
            'apiToken'          => '',
        ];

        // Pass 1: full data render.
        try {
            return response(view('skills.dashboard', compact('gaps', 'activePaths', 'validations', 'recentAssessments', 'apiToken'))->render());
        } catch (\Throwable $e) {
            Log::error('Skills dashboard full render failed', [
                'message'  => $e->getMessage(),
                'location' => $e->getFile() . ':' . $e->getLine(),
            ]);
        }

        // Pass 2: empty-collection render (layout issues still possible).
        try {
            return response(view('skills.dashboard', $emptyData)->render());
        } catch (\Throwable $e) {
            Log::error('Skills dashboard empty render failed', [
                'message'  => $e->getMessage(),
                'location' => $e->getFile() . ':' . $e->getLine(),
            ]);
        }

        // Pass 3: last resort — plain HTML response, no layout dependency.
        return response(<<<HTML
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title>Skill Dashboard</title>
<meta http-equiv="refresh" content="3;url=/dashboard">
<style>body{font-family:sans-serif;text-align:center;padding:60px;background:#f8fafc}</style>
</head><body>
<h2 style="color:#1A73E8">Skill Gap Analyzer</h2>
<p>Dashboard is loading… you will be redirected shortly.</p>
<a href="/dashboard" style="color:#1A73E8">Return to Dashboard</a>
</body></html>
HTML, 200);
    }

    /**
     * Run a dashboard query, returning an empty collection if the underlying
     * schema/relation is unavailable (prevents the page from 500ing on drift).
     */
    private function safely(string $context, callable $query): Collection
    {
        try {
            return $query();
        } catch (\Throwable $e) {
            Log::error('Skills dashboard query failed', [
                'context' => $context,
                'message' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    public function learningPaths(): View
    {
        $user = Auth::user();
        
        $activePaths = $user->learningPaths()->active()->with('resources', 'skillGap')->get();
        $completedPaths = $user->learningPaths()->completed()->with('resources', 'skillGap')->limit(10)->get();
        
        return view('skills.learning-paths', compact('activePaths', 'completedPaths'));
    }

    public function showLearningPath(int $id): View
    {
        $user = Auth::user();
        $path = $user->learningPaths()->with('resources', 'progress', 'skillGap')->findOrFail($id);
        
        return view('skills.learning-path-show', compact('path'));
    }

    public function validation(): View
    {
        $user = Auth::user();
        $validations = $user->skillValidations()->with('userSkill')->latest()->get();
        
        return view('skills.validation', compact('validations'));
    }

    public function assessments(): View
    {
        $user = Auth::user();
        
        $availableSkills = $user->skills()->verified()->get();
        $activeAssessments = $user->skillAssessments()->active()->latest()->get();
        $gradedAssessments = $user->skillAssessments()->where('status', 'graded')->latest()->limit(10)->get();
        
        return view('skills.assessments', compact('availableSkills', 'activeAssessments', 'gradedAssessments'));
    }

    public function takeAssessment(int $id): View
    {
        $user = Auth::user();
        $assessment = $user->skillAssessments()->findOrFail($id);
        
        return view('skills.assessment-take', compact('assessment'));
    }

    public function dailyLearning(): View
    {
        $user = Auth::user();
        
        $dailyTimeMinutes = $user->profile->learning_preferences['daily_time_commitment'] ?? 30;
        $activePaths = $user->learningPaths()->active()->with('resources')->get();
        
        $recommendations = [];
        foreach ($activePaths as $path) {
            $nextResource = $path->getNextResource();
            if ($nextResource) {
                $recommendations[] = [
                    'path' => $path,
                    'resource' => $nextResource,
                    'fits_schedule' => $nextResource->duration_minutes <= $dailyTimeMinutes,
                ];
            }
        }
        
        return view('skills.daily-learning', compact('recommendations', 'dailyTimeMinutes'));
    }

    public function showCertificate(string $hash): View
    {
        $assessment = SkillAssessment::where('certificate_hash', $hash)->firstOrFail();
        
        if (!$assessment->is_shareable) {
            abort(403, 'This certificate is not publicly shareable.');
        }
        
        if ($assessment->certificate_expires_at && $assessment->certificate_expires_at->isPast()) {
            abort(410, 'This certificate has expired.');
        }
        
        return view('skills.certificate-public', compact('assessment'));
    }
}
