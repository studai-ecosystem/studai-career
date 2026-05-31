<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Complete — StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">You've already completed</h1>
            <h2 class="text-2xl font-bold text-gray-900 mt-1">your evaluation!</h2>
            <p class="text-gray-500 mt-3 text-sm">
                Orin™ is analysing all candidate responses. Results will be shared by
                <strong>{{ $job->final_date?->format('d F Y') ?? 'email' }}</strong>.
            </p>
            <div class="mt-6 bg-blue-50 rounded-xl p-4">
                <p class="text-sm text-blue-700">
                    Application: <strong>APP-{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </p>
                <p class="text-sm text-blue-600 mt-1">Role: {{ $job->title }}</p>
            </div>
            <a href="{{ route('apply.show', $token) }}"
               class="inline-block mt-6 px-6 py-3 bg-[#2D6CDF] hover:bg-[#1B57C4] text-white font-semibold rounded-xl transition-colors">
                Back to Job Listing
            </a>
        </div>
    </div>
</body>
</html>
