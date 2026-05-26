<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Not Found — StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
            <span class="text-5xl">🔍</span>
            <h1 class="text-xl font-bold text-gray-900 mt-5">Application not found</h1>
            <p class="text-gray-500 text-sm mt-3">
                We couldn't find an application linked to your session for
                <strong>{{ $job->title }}</strong>.
            </p>
            <p class="text-gray-400 text-sm mt-2">
                If you applied via a different browser or device, please use the same link and browser you applied from.
            </p>
            <a href="{{ route('apply.show', $token) }}"
               class="inline-block mt-6 px-6 py-3 bg-[#1A73E8] hover:bg-[#1557b0] text-white font-semibold rounded-xl transition-colors">
                Back to Application Page
            </a>
        </div>
    </div>
</body>
</html>
