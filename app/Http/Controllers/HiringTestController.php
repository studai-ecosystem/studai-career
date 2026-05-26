<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\HiringTest;
use App\Models\HiringTestAttempt;
use App\Mail\PipelineStageAdvancedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class HiringTestController extends Controller
{
    /**
     * Candidate lands here via email link: /hiring-test/{token}/{stage}
     */
    public function show(string $token, string $stage)
    {
        $application = Application::where('test_link_token', $token)
            ->with(['job.company', 'user'])
            ->firstOrFail();

        // Verify the stage matches the current pipeline stage (or is accessible)
        $validStages = ['company_info_test', 'aptitude', 'tech_test', 'non_tech_test'];
        abort_if(!in_array($stage, $validStages), 404);

        // Check if already attempted
        $attempt = HiringTestAttempt::where('application_id', $application->id)
            ->where('stage', $stage)
            ->first();

        if ($attempt && $attempt->submitted_at) {
            return view('hiring-test.result', compact('application', 'attempt', 'stage'));
        }

        // Find the test for this job+stage
        $test = HiringTest::where('job_id', $application->job_id)
            ->where('stage', $stage)
            ->where('is_active', true)
            ->first();

        if (!$test) {
            return view('hiring-test.no-test', compact('application', 'stage'));
        }

        // Start attempt if not started
        if (!$attempt) {
            $attempt = HiringTestAttempt::create([
                'application_id' => $application->id,
                'hiring_test_id' => $test->id,
                'stage'          => $stage,
                'started_at'     => now(),
            ]);
        }

        return view('hiring-test.take', compact('application', 'test', 'attempt', 'stage', 'token'));
    }

    /**
     * Candidate submits answers.
     */
    public function submit(Request $request, string $token, string $stage)
    {
        $application = Application::where('test_link_token', $token)
            ->with(['job.company', 'user'])
            ->firstOrFail();

        $attempt = HiringTestAttempt::where('application_id', $application->id)
            ->where('stage', $stage)
            ->whereNull('submitted_at')
            ->firstOrFail();

        $test = $attempt->test;

        $validated = $request->validate([
            'answers'   => ['required', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        // Score the test
        $questions = $test->questions;
        $answers   = $validated['answers'];
        $correct   = 0;

        foreach ($questions as $i => $q) {
            if (isset($answers[$i]) && (int) $answers[$i] === (int) ($q['correct_index'] ?? -1)) {
                $correct++;
            }
        }

        $total     = count($questions);
        $score     = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed    = $score >= $test->pass_score;

        $attempt->update([
            'answers'      => $answers,
            'score'        => $score,
            'passed'       => $passed,
            'submitted_at' => now(),
        ]);

        // Notify company via log (extend with email/notification as needed)
        Log::info('Hiring test submitted', [
            'application_id' => $application->id,
            'stage'          => $stage,
            'score'          => $score,
            'passed'         => $passed,
        ]);

        return redirect()->route('hiring-test.show', ['token' => $token, 'stage' => $stage])
            ->with('submitted', true);
    }
}
