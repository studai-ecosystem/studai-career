<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\HiringTest;
use App\Models\Job;
use Illuminate\Http\Request;

class HiringTestManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employer']);
    }

    /** Show create/edit test form for a job+stage */
    public function create(int $jobId, string $stage)
    {
        $company = auth()->user()->company;
        $job  = Job::where('company_id', $company->id)->findOrFail($jobId);

        $validStages = ['company_info_test', 'aptitude'];
        abort_if(!in_array($stage, $validStages), 404, 'Only company_info_test and aptitude have tests.');

        $test = HiringTest::where('job_id', $jobId)->where('stage', $stage)->first();

        return view('employer.tests.create', compact('job', 'stage', 'test'));
    }

    /** Save test questions */
    public function store(Request $request, int $jobId, string $stage)
    {
        $company = auth()->user()->company;
        $job = Job::where('company_id', $company->id)->findOrFail($jobId);

        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:200'],
            'instructions'        => ['nullable', 'string', 'max:1000'],
            'pass_score'          => ['required', 'integer', 'min:1', 'max:100'],
            'time_limit_minutes'  => ['required', 'integer', 'min:5', 'max:180'],
            'questions'           => ['required', 'array', 'min:1'],
            'questions.*.question'      => ['required', 'string'],
            'questions.*.options'       => ['required', 'array', 'min:2'],
            'questions.*.options.*'     => ['required', 'string'],
            'questions.*.correct_index' => ['required', 'integer', 'min:0'],
        ]);

        HiringTest::updateOrCreate(
            ['job_id' => $jobId, 'stage' => $stage],
            [
                'title'              => $validated['title'],
                'instructions'       => $validated['instructions'] ?? null,
                'questions'          => $validated['questions'],
                'pass_score'         => $validated['pass_score'],
                'time_limit_minutes' => $validated['time_limit_minutes'],
                'is_active'          => true,
            ]
        );

        return redirect()
            ->route('employer.jobs.show', $jobId)
            ->with('success', 'Test saved! Candidates will see a test link in their stage email.');
    }

    /** View results for a job+stage */
    public function results(int $jobId, string $stage)
    {
        $company = auth()->user()->company;
        $job  = Job::where('company_id', $company->id)->findOrFail($jobId);
        $test = HiringTest::where('job_id', $jobId)->where('stage', $stage)->firstOrFail();
        $attempts = $test->attempts()->with('application.user')->latest()->get();

        return view('employer.tests.results', compact('job', 'stage', 'test', 'attempts'));
    }
}
