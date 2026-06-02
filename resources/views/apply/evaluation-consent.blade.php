<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Before You Begin — StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10">
            <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-[#2D6CDF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">Before you begin your evaluation</h1>
            <p class="text-gray-500 mt-2 text-sm">Role: <strong>{{ $job->title }}</strong> @ {{ $job->company?->name }}</p>

            <div class="mt-6 bg-amber-50 border border-amber-100 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-amber-800">This is a monitored assessment</h2>
                <p class="text-sm text-amber-700 mt-2">
                    To keep the evaluation fair for every candidate, Orin™ records integrity signals during your session, including:
                </p>
                <ul class="mt-3 space-y-1.5 text-sm text-amber-700 list-disc list-inside">
                    <li>Switching browser tabs or windows away from the assessment</li>
                    <li>Loss of focus on the assessment screen</li>
                    <li>Unusually fast or anomalous answer timing</li>
                </ul>
                <p class="text-sm text-amber-700 mt-3">
                    These signals are used only to flag a session for human review &mdash; they never automatically reject you.
                    You may pause between questions, but please stay on this screen while answering.
                </p>
            </div>

            <form method="POST" action="{{ route('apply.evaluation.consent', $token) }}" class="mt-6">
                @csrf
                @error('monitoring_consent')
                    <p class="text-sm text-red-600 mb-3">You must acknowledge monitoring to continue.</p>
                @enderror

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="monitoring_consent" value="1" required
                           class="mt-1 h-5 w-5 rounded border-gray-300 text-[#2D6CDF] focus:ring-[#2D6CDF]">
                    <span class="text-sm text-gray-700">
                        I understand that this evaluation is monitored for integrity as described above, and I consent to this
                        monitoring for the duration of my session.
                    </span>
                </label>

                <button type="submit"
                        class="w-full mt-6 px-6 py-3.5 bg-[#2D6CDF] hover:bg-[#1B57C4] text-white font-semibold rounded-xl transition-colors">
                    I Acknowledge &amp; Start Evaluation
                </button>
            </form>

            <a href="{{ route('apply.show', $token) }}" class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-4">
                Not now &mdash; back to job listing
            </a>
        </div>
    </div>
</body>
</html>
