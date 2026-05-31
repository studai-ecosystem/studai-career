<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Result — {{ $job->title }} | StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full">
        <div class="text-center mb-6">
            <span class="text-[#2D6CDF] font-bold text-xl">StudAI Hire</span>
            <span class="text-gray-400 text-sm ml-2">| Powered by Orin™</span>
        </div>

        @if(! $application)
            {{-- No application found --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10 text-center">
                <span class="text-5xl">🔍</span>
                <h1 class="text-xl font-bold text-gray-900 mt-4">Application not found</h1>
                <p class="text-gray-500 mt-2 text-sm">We couldn't find an application associated with your session.</p>
                <a href="{{ route('apply.show', $token) }}" class="inline-block mt-6 px-6 py-3 bg-[#2D6CDF] text-white rounded-xl font-semibold">
                    Back to Job Listing
                </a>
            </div>
        @elseif($application->status === 'shortlisted')
            {{-- Shortlisted --}}
            <div class="bg-white rounded-3xl shadow-sm border border-green-200 p-10 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-4xl">🎉</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">Congratulations!</h1>
                <p class="text-green-700 font-medium mt-1">You have been shortlisted</p>
                <p class="text-gray-500 text-sm mt-3">
                    You're among the top candidates for <strong>{{ $job->title }}</strong>.
                    The {{ $job->company?->name }} team will be in touch with next steps.
                </p>
                <div class="mt-4 bg-green-50 rounded-xl p-4 text-left space-y-1">
                    <p class="text-sm text-green-800"><span class="font-medium">Application:</span> APP-{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
                    @if($application->rank_position)
                        <p class="text-sm text-green-800"><span class="font-medium">Rank:</span> #{{ $application->rank_position }}</p>
                    @endif
                    @if($application->final_rank_score)
                        <p class="text-sm text-green-800"><span class="font-medium">Score:</span> {{ number_format($application->final_rank_score, 1) }} / 100</p>
                    @endif
                </div>
            </div>
        @elseif($application->status === 'rejected')
            {{-- Rejected --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-10 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-4xl">📋</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mt-4">Thank you for applying</h1>
                <p class="text-gray-600 mt-2 text-sm">
                    We appreciate your time and effort. After careful evaluation, we've decided to move forward with other candidates for
                    <strong>{{ $job->title }}</strong> at {{ $job->company?->name }}.
                </p>
                <p class="text-gray-400 text-sm mt-3">
                    Don't be discouraged — there are many opportunities on StudAI Hire.
                </p>
                <a href="{{ route('jobs.search') }}" class="inline-block mt-6 px-6 py-3 bg-[#2D6CDF] text-white rounded-xl font-semibold">
                    Browse More Jobs
                </a>
            </div>
        @elseif(in_array($application->evaluation_status, ['pending', 'invited']))
            {{-- Pending / Awaiting evaluation --}}
            <div class="bg-white rounded-3xl shadow-sm border border-blue-100 p-10 text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-4xl">⏳</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mt-4">Results Pending</h1>
                <p class="text-gray-500 text-sm mt-2">
                    Orin™ is still evaluating all candidates. Results will be announced by
                    <strong>{{ $job->final_date?->format('d F Y') ?? 'email' }}</strong>.
                </p>
            </div>
        @else
            {{-- Generic status --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10 text-center">
                <span class="text-4xl">🤖</span>
                <h1 class="text-xl font-bold text-gray-900 mt-4">Evaluation in Progress</h1>
                <p class="text-gray-500 text-sm mt-2">Orin™ is still reviewing responses. Check back soon.</p>
            </div>
        @endif
    </div>
</body>
</html>
