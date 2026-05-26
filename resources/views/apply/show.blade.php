<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->title }} — Apply via StudAI Hire</title>
    <meta name="description" content="Apply for {{ $job->title }} at {{ $job->company?->name }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1A73E8',
                        'primary-dark': '#1557b0',
                        success: '#34A853',
                    }
                }
            }
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .prose h2 { font-size: 1.1rem; font-weight: 600; margin: 1rem 0 0.5rem; color: #1f2937; }
        .prose ul { list-style: disc; padding-left: 1.5rem; margin: 0.5rem 0; }
        .prose li { margin: 0.25rem 0; }
    </style>
</head>
<body class="h-full bg-gray-50">

{{-- Header --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-primary font-bold text-xl">StudAI</span>
            <span class="text-gray-400 text-sm">Career</span>
            <span class="ml-2 px-2 py-0.5 bg-blue-50 text-primary text-xs rounded-full font-medium">Powered by Orin™</span>
        </div>
        @if($job->close_date)
            <div class="text-sm text-gray-500">
                Closes: <span class="font-medium text-gray-700">{{ $job->close_date->format('d M Y') }}</span>
            </div>
        @endif
    </div>
</header>

<main class="max-w-4xl mx-auto px-4 py-8">

    {{-- Phase Banner --}}
    @if($phase === 'coming_soon')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-2xl">⏳</span>
            <div>
                <p class="font-semibold text-yellow-800">Applications open on {{ $job->open_date->format('d F Y') }}</p>
                <p class="text-sm text-yellow-700">Bookmark this page to apply when applications open.</p>
            </div>
        </div>
    @elseif($phase === 'closed')
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-2xl">🔒</span>
            <div>
                <p class="font-semibold text-orange-800">Applications closed</p>
                <p class="text-sm text-orange-700">
                    AI evaluation begins on {{ $job->eval_start_date?->format('d F Y') ?? 'soon' }}.
                    All applicants will be notified.
                </p>
            </div>
        </div>
    @elseif($phase === 'evaluating')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-2xl">🤖</span>
            <div>
                <p class="font-semibold text-blue-800">Orin™ evaluation in progress</p>
                <p class="text-sm text-blue-700">
                    If you applied and received an invitation, 
                    <a href="{{ route('apply.evaluation', $token) }}" class="underline font-medium">click here to begin your evaluation</a>.
                </p>
            </div>
        </div>
    @elseif($phase === 'results')
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-2xl">🏆</span>
            <div>
                <p class="font-semibold text-green-800">Results are in</p>
                <p class="text-sm text-green-700">
                    All candidates have been notified. 
                    <a href="{{ route('apply.results', $token) }}" class="underline font-medium">Check your result</a>.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Job Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Job Header --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start gap-4">
                    @if($job->company?->logo)
                        <img src="{{ asset('storage/' . $job->company->logo) }}"
                             alt="{{ $job->company->name }}"
                             class="w-16 h-16 rounded-xl object-contain border border-gray-100">
                    @else
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-2xl font-bold text-primary">
                            {{ substr($job->company?->name ?? $job->title, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $job->title }}</h1>
                        <p class="text-gray-600 mt-1">{{ $job->company?->name }}</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-3 py-1 bg-blue-50 text-primary text-sm rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $job->location_type ?? 'hybrid')) }}
                            </span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                {{ ucfirst(str_replace('_', '-', $job->employment_type ?? 'full-time')) }}
                            </span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                {{ ucwords(str_replace('_', ' ', $job->experience_level ?? 'mid')) }} level
                            </span>
                            @if($job->salary_min && $job->salary_max)
                                <span class="px-3 py-1 bg-green-50 text-green-700 text-sm rounded-full">
                                    ₹{{ number_format($job->salary_min / 100000, 1) }}L – ₹{{ number_format($job->salary_max / 100000, 1) }}L / yr
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Description --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">About this Role</h2>
                <div class="prose text-gray-700 text-sm leading-relaxed">
                    {!! \Illuminate\Support\Str::markdown($job->description ?? '') !!}
                </div>
            </div>

            {{-- Required Skills --}}
            @if($job->required_skills)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Required Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(is_array($job->required_skills) ? $job->required_skills : [] as $skill)
                            <span class="px-3 py-1.5 bg-blue-50 text-primary text-sm rounded-lg font-medium border border-blue-100">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: Apply Panel / Timeline --}}
        <div class="space-y-4">

            {{-- Timeline --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-3">
                    @php
                        $timelineItems = [
                            ['label' => 'Applications Open', 'date' => $job->open_date?->format('d M Y'), 'done' => true],
                            ['label' => 'Applications Close', 'date' => $job->close_date?->format('d M Y'), 'done' => $phase !== 'open'],
                            ['label' => 'AI Evaluation Begins', 'date' => $job->eval_start_date?->format('d M Y'), 'done' => in_array($phase, ['evaluating', 'results'])],
                            ['label' => 'Results Announced', 'date' => $job->final_date?->format('d M Y'), 'done' => $phase === 'results'],
                        ];
                    @endphp
                    @foreach($timelineItems as $item)
                        @if($item['date'])
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 {{ $item['done'] ? 'bg-success text-white' : 'bg-gray-100 text-gray-400' }}">
                                    @if($item['done'])
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $item['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['date'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Apply Form / Status Panel --}}
            @if($phase === 'open')
                @if($existingApplication)
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
                        <span class="text-3xl">✅</span>
                        <p class="mt-2 font-semibold text-green-800">You've already applied!</p>
                        <p class="text-sm text-green-700 mt-1">Application #APP-{{ str_pad($existingApplication->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="font-semibold text-gray-900 mb-4">Apply Now</h3>
                        <div id="apply-success" class="hidden bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                            <span class="text-3xl">🎉</span>
                            <p class="mt-2 font-semibold text-green-800">Application submitted!</p>
                            <p class="text-sm text-green-700 mt-1" id="apply-app-number"></p>
                            <p class="text-sm text-green-700" id="apply-eval-date"></p>
                            <p class="text-xs text-green-600 mt-2">Check your email for confirmation.</p>
                        </div>
                        <form id="apply-form" class="space-y-3" enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="full_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                    placeholder="Your full name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="email" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                    placeholder="your@email.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="tel" name="phone" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                    placeholder="+91 XXXXX XXXXX">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile</label>
                                <input type="url" name="linkedin_url"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                    placeholder="https://linkedin.com/in/...">
                            </div>
                            @if($job->requires_portfolio)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Portfolio URL *</label>
                                    <input type="url" name="portfolio_url" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                        placeholder="https://yourportfolio.com">
                                </div>
                            @endif
                            @if($job->requires_github)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GitHub Profile *</label>
                                    <input type="url" name="github_url" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                                        placeholder="https://github.com/...">
                                </div>
                            @endif

                            {{-- Cover Letter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Letter</label>
                                <textarea name="cover_letter" rows="5" maxlength="3000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none"
                                    placeholder="Tell us why you're a great fit for this role, what excites you about this opportunity, and any relevant experience you'd like to highlight... (optional, up to 3000 characters)"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Optional · Max 3000 characters</p>
                            </div>

                            {{-- Resume Upload --}}
                            <div x-data="resumePicker()" x-init="init()">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Resume / CV <span class="text-red-500">*</span></label>

                                {{-- Selected state display --}}
                                <div x-show="chosen" class="flex items-center gap-3 bg-blue-50 border border-primary rounded-xl px-4 py-3 mb-2">
                                    <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p x-text="chosenLabel" class="text-sm font-semibold text-primary truncate"></p>
                                        <p x-text="chosenSub" class="text-xs text-gray-500"></p>
                                    </div>
                                    <button type="button" @click="reset()" class="text-xs text-gray-400 hover:text-red-500 flex-shrink-0">✕ Change</button>
                                </div>

                                {{-- Picker trigger button --}}
                                <button type="button" x-show="!chosen" @click="open = true"
                                    class="w-full border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-primary transition-colors">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700">Click to add your resume</p>
                                    <p class="text-xs text-gray-400 mt-1">Upload a file, use an AI resume, or paste from a link</p>
                                </button>

                                {{-- Actual file input (hidden, triggered by "upload from device") --}}
                                <input type="file" id="resume-file-input" name="resume" accept=".pdf,.doc,.docx" class="hidden"
                                    @change="onFileChange($event)">

                                {{-- Selected saved resume ID --}}
                                <input type="hidden" name="saved_resume_id" :value="savedResumeId">

                                {{-- Modal --}}
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
                                    @click.self="open = false">
                                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>
                                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-5 z-10">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="font-semibold text-gray-900 text-base">Add Your Resume</h4>
                                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        {{-- Option 1: Upload from device --}}
                                        <button type="button" @click="triggerFileInput()"
                                            class="w-full flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-primary hover:bg-blue-50 transition-colors text-left mb-2">
                                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Upload from device</p>
                                                <p class="text-xs text-gray-500">PDF, DOC, DOCX · Max 5 MB</p>
                                            </div>
                                        </button>

                                        {{-- Option 2: Saved AI Resumes --}}
                                        @auth
                                            @if($savedResumes->isNotEmpty())
                                                <div class="mb-2">
                                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 px-1">Your AI-Created Resumes</p>
                                                    <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                                        @foreach($savedResumes as $sr)
                                                            <button type="button"
                                                                @click="selectSaved({{ $sr->id }}, '{{ addslashes($sr->title) }}', '{{ $sr->updated_at->diffForHumans() }}')"
                                                                class="w-full flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-primary hover:bg-blue-50 transition-colors text-left">
                                                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $sr->title }}</p>
                                                                    <p class="text-xs text-gray-500">Updated {{ $sr->updated_at->diffForHumans() }}</p>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth

                                        {{-- Option 3: Create new AI resume --}}
                                        <a href="{{ route('resume.create') }}"
                                            class="w-full flex items-center gap-3 p-3 rounded-xl border border-dashed border-gray-300 hover:border-purple-400 hover:bg-purple-50 transition-colors text-left">
                                            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Create AI Resume</p>
                                                <p class="text-xs text-gray-500">Build a new resume with Orin™ AI in minutes</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Mandatory screening questions --}}
                            @if($job->mandatory_screening_questions)
                                @foreach(is_array($job->mandatory_screening_questions) ? $job->mandatory_screening_questions : [] as $i => $question)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $question }} *</label>
                                        <textarea name="screening_answers[{{ $i }}]" required rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none"></textarea>
                                    </div>
                                @endforeach
                            @endif

                            <div id="apply-error" class="hidden text-red-600 text-sm p-3 bg-red-50 rounded-lg"></div>

                            <button type="submit" id="apply-submit"
                                class="w-full py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <span id="apply-btn-text">Submit Application</span>
                                <svg id="apply-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            @endif

            {{-- Orin™ badge --}}
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white text-center">
                <p class="font-semibold">Powered by Orin™ AI</p>
                <p class="text-blue-200 text-xs mt-1">Every candidate gets a unique, personalised evaluation. No two evaluations are the same.</p>
            </div>

        </div>
    </div>
</main>

<script>
function resumePicker() {
    return {
        open: false,
        chosen: false,
        chosenLabel: '',
        chosenSub: '',
        savedResumeId: '',
        init() {
            // nothing to pre-load
        },
        triggerFileInput() {
            this.open = false;
            document.getElementById('resume-file-input').click();
        },
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.savedResumeId = '';
            this.chosenLabel  = file.name;
            this.chosenSub    = (file.size / 1024 / 1024).toFixed(2) + ' MB · from your device';
            this.chosen = true;
        },
        selectSaved(id, title, ago) {
            this.open = false;
            // Clear any file input
            const fi = document.getElementById('resume-file-input');
            fi.value = '';
            this.savedResumeId = id;
            this.chosenLabel   = title;
            this.chosenSub     = 'AI Resume · updated ' + ago;
            this.chosen = true;
        },
        reset() {
            this.chosen = false;
            this.chosenLabel  = '';
            this.chosenSub    = '';
            this.savedResumeId = '';
            const fi = document.getElementById('resume-file-input');
            if (fi) fi.value = '';
        }
    };
}

document.getElementById('apply-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('apply-submit');
    const btnText = document.getElementById('apply-btn-text');
    const spinner = document.getElementById('apply-spinner');
    const errorEl = document.getElementById('apply-error');

    // Validate resume: must have file OR saved resume
    const fileInput  = document.getElementById('resume-file-input');
    const savedInput = this.querySelector('[name="saved_resume_id"]');
    const hasFile    = fileInput && fileInput.files && fileInput.files.length > 0;
    const hasSaved   = savedInput && savedInput.value !== '';
    if (!hasFile && !hasSaved) {
        errorEl.textContent = 'Please select a resume before submitting.';
        errorEl.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btnText.textContent = 'Submitting...';
    spinner.classList.remove('hidden');
    errorEl.classList.add('hidden');

    const formData = new FormData(this);

    try {
        const response = await fetch('{{ route("apply.submit", $token) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('apply-form').classList.add('hidden');
            document.getElementById('apply-success').classList.remove('hidden');
            document.getElementById('apply-app-number').textContent = 'Application ' + data.application_number;
            document.getElementById('apply-eval-date').textContent = 'AI evaluation: ' + data.eval_date;

            // Store access token in cookie
            document.cookie = 'apply_token_{{ $job->id }}=' + data.access_token + '; path=/; max-age=31536000';
        } else if (data.errors) {
            const msgs = Object.values(data.errors).flat().join('. ');
            errorEl.textContent = msgs;
            errorEl.classList.remove('hidden');
        } else {
            errorEl.textContent = data.error || 'Submission failed. Please try again.';
            errorEl.classList.remove('hidden');
        }
    } catch (err) {
        errorEl.textContent = 'Network error. Please try again.';
        errorEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Submit Application';
        spinner.classList.add('hidden');
    }
});
</script>

</body>
</html>
