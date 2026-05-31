<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Application Details') }}
            </h2>
            <a href="{{ route('employer.applicants.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Applications
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Responsible AI Disclaimer --}}
            <div class="mb-4">
                <x-ai-disclaimer context="employer_screening"
                                 subject-type="App\Models\Application"
                                 :subject-id="$application->id" />
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Application Header -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        @php
                            $candidateName  = $application->is_guest_applicant ? $application->guest_name : $application->user?->name ?? 'Unknown';
                            $candidateEmail = $application->is_guest_applicant ? $application->guest_email : $application->user?->email;
                            $candidatePhone = $application->is_guest_applicant ? $application->guest_phone : $application->user?->profile?->phone;
                        @endphp
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold mr-4">
                                    {{ strtoupper(substr($candidateName, 0, 1)) }}
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $candidateName }}</h1>
                                    @if($candidateEmail)<p class="text-gray-600">{{ $candidateEmail }}</p>@endif
                                    @if($candidatePhone)<p class="text-gray-600">{{ $candidatePhone }}</p>@endif
                                    @if($application->is_guest_applicant)
                                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Guest Applicant</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Applied on</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ $application->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Job Info -->
                        <div class="p-4 bg-gray-50 rounded-lg mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Applied for</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $application->job->title }}</p>
                            <p class="text-gray-600">{{ $application->job->location }} • {{ ucfirst(str_replace(['_', '-'], ' ', $application->job->employment_type ?? '')) }}</p>
                        </div>

                        <!-- Status Timeline -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Status</h3>
                            <div class="relative">
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                                <div class="space-y-4">
                                    <div class="relative flex items-start">
                                        <div class="absolute left-0 w-8 h-8 rounded-full {{ $application->status === 'pending' || $application->status === 'reviewing' || $application->status === 'shortlisted' || $application->status === 'rejected' || $application->status === 'hired' ? 'bg-blue-600' : 'bg-gray-300' }} flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-12">
                                            <p class="font-medium text-gray-900">Application Submitted</p>
                                            <p class="text-sm text-gray-500">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>

                                    @if($application->status === 'reviewing' || $application->status === 'shortlisted' || $application->status === 'rejected' || $application->status === 'hired')
                                        <div class="relative flex items-start">
                                            <div class="absolute left-0 w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-12">
                                                <p class="font-medium text-gray-900">Under Review</p>
                                                <p class="text-sm text-gray-500">{{ $application->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->status === 'shortlisted' || $application->status === 'hired')
                                        <div class="relative flex items-start">
                                            <div class="absolute left-0 w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-12">
                                                <p class="font-medium text-gray-900">Shortlisted</p>
                                                <p class="text-sm text-gray-500">{{ $application->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->status === 'hired')
                                        <div class="relative flex items-start">
                                            <div class="absolute left-0 w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-12">
                                                <p class="font-medium text-gray-900">Hired</p>
                                                <p class="text-sm text-gray-500">{{ $application->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->status === 'rejected')
                                        <div class="relative flex items-start">
                                            <div class="absolute left-0 w-8 h-8 rounded-full bg-red-600 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-12">
                                                <p class="font-medium text-gray-900">Application Rejected</p>
                                                <p class="text-sm text-gray-500">{{ $application->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($application->cover_letter)
                            <!-- Cover Letter -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cover Letter</h3>
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $application->cover_letter }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ── HIRING ROUND TEST RESULTS ─────────────────────────── --}}
                    @if($application->job->hiringRounds->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-xl font-bold text-gray-900">Hiring Round Results</h2>
                            @if($overallTestScore !== null)
                                @php
                                    $oc = $overallTestScore >= 70 ? 'bg-green-100 text-green-700' : ($overallTestScore >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                @endphp
                                <span class="text-sm font-bold px-3 py-1 rounded-full {{ $oc }}">
                                    Avg Score: {{ $overallTestScore }}%
                                </span>
                            @else
                                <span class="text-sm text-gray-400 italic">No tests completed yet</span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @foreach($application->job->hiringRounds as $round)
                                @php
                                    $attempt = $roundAttempts->get($round->id);
                                    $typeColors = [
                                        'info_test'       => 'bg-blue-100 text-blue-700',
                                        'aptitude'        => 'bg-purple-100 text-purple-700',
                                        'technical'       => 'bg-orange-100 text-orange-700',
                                        'practical'       => 'bg-teal-100 text-teal-700',
                                        'hr_interview'    => 'bg-pink-100 text-pink-700',
                                        'culture_fit'     => 'bg-indigo-100 text-indigo-700',
                                        'portfolio_review'=> 'bg-cyan-100 text-cyan-700',
                                    ];
                                    $typeColor = $typeColors[$round->round_type] ?? $typeColors[$round->type] ?? 'bg-gray-100 text-gray-600';
                                    $typeLabel = ucwords(str_replace('_', ' ', $round->round_type ?? $round->type));
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-4">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-bold text-gray-400">ROUND {{ $round->round_order }}</span>
                                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $typeColor }}">{{ $typeLabel }}</span>
                                            </div>
                                            <h4 class="font-semibold text-gray-900">{{ $round->name }}</h4>
                                        </div>
                                        @if($attempt && $attempt->score !== null)
                                            @php
                                                $sc = $attempt->score >= 70 ? 'bg-green-500' : ($attempt->score >= 50 ? 'bg-amber-500' : 'bg-red-500');
                                            @endphp
                                            <div class="flex-shrink-0 text-center">
                                                <div class="w-14 h-14 rounded-full {{ $sc }} flex items-center justify-center text-white font-bold text-lg shadow">
                                                    {{ $attempt->score }}
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">/ 100</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Status badge --}}
                                    @if(!$attempt)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">
                                            ⬜ Not Started
                                        </span>
                                    @elseif($attempt->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-600">
                                            🔵 In Progress
                                        </span>
                                    @elseif(in_array($attempt->status, ['submitted', 'evaluated']))
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                            ✅ Completed
                                        </span>
                                        @if($attempt->violations > 0)
                                            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 ml-2">
                                                ⚠️ {{ $attempt->violations }} violation{{ $attempt->violations > 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    @endif

                                    {{-- AI Feedback --}}
                                    @if($attempt?->ai_feedback)
                                        <div class="mt-3 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                            <p class="text-xs font-semibold text-indigo-600 mb-1">🤖 AI Evaluation</p>
                                            <p class="text-sm text-gray-700">{{ $attempt->ai_feedback }}</p>
                                        </div>
                                    @endif

                                    {{-- Submitted at --}}
                                    @if($attempt?->submitted_at)
                                        <p class="text-xs text-gray-400 mt-2">Submitted {{ $attempt->submitted_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Candidate Profile -->
                    @if($application->user->profile)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Candidate Profile</h2>

                            <!-- Location -->
                            @if($application->user->profile->location)
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Location</h3>
                                    <p class="text-gray-900">{{ $application->user->profile->location }}</p>
                                </div>
                            @endif

                            <!-- Experience -->
                            @if($application->user->profile->experience && count($application->user->profile->experience) > 0)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Experience</h3>
                                    <div class="space-y-4">
                                        @foreach($application->user->profile->experience as $exp)
                                            <div class="border-l-2 border-blue-500 pl-4">
                                                <h4 class="font-semibold text-gray-900">{{ $exp['title'] ?? 'N/A' }}</h4>
                                                <p class="text-gray-600">{{ $exp['company'] ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $exp['start_date'] ?? 'N/A' }} - {{ $exp['end_date'] ?? 'Present' }}
                                                </p>
                                                @if(isset($exp['description']))
                                                    <p class="mt-2 text-gray-700">{{ $exp['description'] }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Education -->
                            @if($application->user->profile->education && count($application->user->profile->education) > 0)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Education</h3>
                                    <div class="space-y-4">
                                        @foreach($application->user->profile->education as $edu)
                                            <div class="border-l-2 border-green-500 pl-4">
                                                <h4 class="font-semibold text-gray-900">{{ $edu['degree'] ?? 'N/A' }}</h4>
                                                <p class="text-gray-600">{{ $edu['institution'] ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $edu['start_date'] ?? 'N/A' }} - {{ $edu['end_date'] ?? 'Present' }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Skills -->
                            @if($application->user->profile->skills && count($application->user->profile->skills) > 0)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($application->user->profile->skills as $skill)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ is_array($skill) ? ($skill['name'] ?? $skill) : $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Notes Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Internal Notes</h2>
                        
                        @if($application->notes)
                            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-line">{{ $application->notes }}</p>
                            </div>
                        @endif

                        <form action="{{ route('employer.applicants.addNote', $application->id) }}" method="POST">
                            @csrf
                            <textarea name="notes" id="notes" rows="4" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add or update internal notes about this candidate...">{{ old('notes', $application->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Save Notes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Current Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Current Status</h2>
                        <div class="text-center">
                            @if($application->status === 'pending')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-orange-100 text-orange-800">
                                    Pending Review
                                </span>
                            @elseif($application->status === 'reviewing')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-blue-100 text-blue-800">
                                    Under Review
                                </span>
                            @elseif($application->status === 'shortlisted')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-green-100 text-green-800">
                                    Shortlisted
                                </span>
                            @elseif($application->status === 'rejected')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-purple-100 text-purple-800">
                                    Hired
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ── AI HIRING RECOMMENDATION ─────────────────────────── --}}
                    @php
                        $evalScore = $application->final_rank_score ?? $application->evaluation_score ?? null;
                        $blendScore = null;
                        if ($overallTestScore !== null && $evalScore !== null) {
                            $blendScore = (int) round($overallTestScore * 0.6 + (float) $evalScore * 0.4);
                        } elseif ($overallTestScore !== null) {
                            $blendScore = $overallTestScore;
                        } elseif ($evalScore !== null) {
                            $blendScore = (int) round((float) $evalScore);
                        }

                        if ($blendScore !== null) {
                            if ($blendScore >= 80) {
                                $recColor  = 'from-green-500 to-emerald-600';
                                $recBg     = 'bg-green-50 border-green-200';
                                $recLabel  = 'Strong Hire';
                                $recIcon   = '🌟';
                                $recText   = "With a combined score of {$blendScore}%, this candidate is an excellent match. Highly recommended for hire — they demonstrated strong performance across evaluations and are well-suited for the team.";
                            } elseif ($blendScore >= 65) {
                                $recColor  = 'from-blue-500 to-indigo-600';
                                $recBg     = 'bg-blue-50 border-blue-200';
                                $recLabel  = 'Recommended';
                                $recIcon   = '👍';
                                $recText   = "With a combined score of {$blendScore}%, this candidate shows solid potential. They meet the core requirements and should be a good addition to the team.";
                            } elseif ($blendScore >= 50) {
                                $recColor  = 'from-amber-500 to-orange-500';
                                $recBg     = 'bg-amber-50 border-amber-200';
                                $recLabel  = 'Consider with Caution';
                                $recIcon   = '🤔';
                                $recText   = "With a combined score of {$blendScore}%, this candidate shows moderate potential. Consider a follow-up interview to assess gaps before making a final decision.";
                            } else {
                                $recColor  = 'from-red-500 to-rose-600';
                                $recBg     = 'bg-red-50 border-red-200';
                                $recLabel  = 'Not Recommended';
                                $recIcon   = '❌';
                                $recText   = "With a combined score of {$blendScore}%, this candidate does not meet the minimum threshold. Rejection is advised unless there are strong extenuating circumstances.";
                            }
                        }
                    @endphp
                    @if($blendScore !== null)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br {{ $recColor }} flex items-center justify-center text-white text-xs">🤖</div>
                            <h2 class="text-lg font-bold text-gray-900">AI Recommendation</h2>
                        </div>

                        {{-- Score ring --}}
                        <div class="flex items-center gap-4 mb-4">
                            @php $dash = round((1 - $blendScore / 100) * 251); @endphp
                            <svg class="w-20 h-20 -rotate-90" viewBox="0 0 86 86">
                                <circle cx="43" cy="43" r="40" fill="none" stroke="#E2E2E0" stroke-width="6"/>
                                <circle cx="43" cy="43" r="40" fill="none"
                                    stroke="{{ $blendScore >= 80 ? '#1E8E3E' : ($blendScore >= 65 ? '#2D6CDF' : ($blendScore >= 50 ? '#E37400' : '#2D6CDF')) }}"
                                    stroke-width="6" stroke-linecap="round"
                                    stroke-dasharray="251" stroke-dashoffset="{{ $dash }}"/>
                            </svg>
                            <div>
                                <p class="text-3xl font-bold text-gray-900">{{ $blendScore }}%</p>
                                <p class="text-xs text-gray-500">Hire Readiness</p>
                                <span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $blendScore >= 80 ? 'bg-green-100 text-green-700' : ($blendScore >= 65 ? 'bg-blue-100 text-blue-700' : ($blendScore >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ $recIcon }} {{ $recLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl border text-sm text-gray-700 leading-relaxed {{ $recBg }}">
                            {{ $recText }}
                        </div>

                        @if($overallTestScore !== null)
                            <div class="mt-3 flex gap-2 text-xs text-gray-500">
                                <span class="flex-1 text-center bg-gray-50 rounded-lg p-2">
                                    <div class="font-bold text-gray-800 text-base">{{ $overallTestScore }}%</div>
                                    Test Avg
                                </span>
                                @if($evalScore !== null)
                                <span class="flex-1 text-center bg-gray-50 rounded-lg p-2">
                                    <div class="font-bold text-gray-800 text-base">{{ round((float)$evalScore) }}%</div>
                                    AI Profile
                                </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Change Status</h2>
                        
                        <div class="space-y-2">
                            @if($application->status !== 'reviewing')
                                <form action="{{ route('employer.applicants.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="reviewing">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Start Review
                                    </button>
                                </form>
                            @endif

                            @if($application->status !== 'shortlisted')
                                <form action="{{ route('employer.applicants.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="shortlisted">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Shortlist Candidate
                                    </button>
                                </form>
                            @endif

                            @if($application->status !== 'hired')
                                <form action="{{ route('employer.applicants.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="hired">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Mark as Hired
                                    </button>
                                </form>
                            @endif

                            @if($application->status !== 'rejected')
                                <form action="{{ route('employer.applicants.updateStatus', $application->id) }}" method="POST" 
                                    onsubmit="return confirm('Are you sure you want to reject this application?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Reject Application
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Hiring Pipeline -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-1">Hiring Pipeline</h2>
                        <p class="text-xs text-gray-500 mb-4">Advance candidate through stages — emails are sent automatically.</p>

                        {{-- Current stage badge --}}
                        @php
                            $stageLabels = [
                                'company_info_test' => ['label'=>'Company Info Test','color'=>'bg-blue-100 text-blue-700','icon'=>'📋'],
                                'aptitude'          => ['label'=>'Aptitude Assessment','color'=>'bg-purple-100 text-purple-700','icon'=>'🧠'],
                                'tech_test'         => ['label'=>'Technical Test','color'=>'bg-orange-100 text-orange-700','icon'=>'💻'],
                                'non_tech_test'     => ['label'=>'Non-Technical Test','color'=>'bg-teal-100 text-teal-700','icon'=>'📝'],
                            ];
                            $currentStage = $application->hiring_stage;
                        @endphp
                        @if($currentStage && isset($stageLabels[$currentStage]))
                        <div class="mb-4 flex items-center gap-2 p-3 rounded-xl {{ $stageLabels[$currentStage]['color'] }}">
                            <span>{{ $stageLabels[$currentStage]['icon'] }}</span>
                            <div>
                                <div class="text-xs font-semibold">Current Stage</div>
                                <div class="text-sm font-bold">{{ $stageLabels[$currentStage]['label'] }}</div>
                                @if($application->pipeline_stage_date)
                                <div class="text-xs">📅 {{ \Carbon\Carbon::parse($application->pipeline_stage_date)->format('d M Y') }}</div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Pipeline Stage Form --}}
                        <form id="pipeline-form" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Advance to Stage</label>
                                <select name="stage" id="pipeline-stage" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
                                    <option value="">-- Select Stage --</option>
                                    <option value="company_info_test" {{ $currentStage === 'company_info_test' ? 'selected' : '' }}>📋 Company Info Test</option>
                                    <option value="aptitude" {{ $currentStage === 'aptitude' ? 'selected' : '' }}>🧠 Aptitude Assessment</option>
                                    <option value="tech_test" {{ $currentStage === 'tech_test' ? 'selected' : '' }}>💻 Technical Test</option>
                                    <option value="non_tech_test" {{ $currentStage === 'non_tech_test' ? 'selected' : '' }}>📝 Non-Technical Test</option>
                                    <option value="hired">🎉 Mark as Hired</option>
                                    <option value="rejected">❌ Reject</option>
                                </select>
                            </div>
                            <div id="stage-date-wrap">
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Scheduled Date</label>
                                <input type="date" name="stage_date" id="pipeline-stage-date"
                                    value="{{ $application->pipeline_stage_date ? $application->pipeline_stage_date->format('Y-m-d') : '' }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Notes to Candidate (optional)</label>
                                <textarea name="stage_notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400" placeholder="e.g. Please join via Google Meet link...">{{ $application->pipeline_stage_notes }}</textarea>
                            </div>
                            <button type="button" onclick="advancePipelineStage()" class="w-full py-2.5 text-white text-sm font-semibold rounded-xl transition-all" style="background:#2D6CDF;">
                                Advance & Send Email
                            </button>
                            <div id="pipeline-result" class="hidden text-sm font-medium rounded-xl p-3"></div>
                        </form>
                    </div>

                    <!-- Resume -->
                    @php
                        $hasResume = false;
                        if ($application->resume_file) {
                            // Covers uploaded files and saved/AI resume references ("resume:{id}").
                            $hasResume = true;
                        } elseif (!$application->is_guest_applicant && $application->user?->profile?->resume_path) {
                            $hasResume = true;
                        }
                        // Always serve through the authorized employer route so saved resumes
                        // (stored as "resume:{id}") are rendered to PDF on demand.
                        $resumeUrl = $hasResume ? route('employer.applicants.resume', $application->id) : null;
                    @endphp
                    @if($resumeUrl)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Resume</h2>
                            <a href="{{ $resumeUrl }}" target="_blank"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Resume
                            </a>
                        </div>
                    @else
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-2">Resume</h2>
                            <p class="text-sm text-gray-400 italic">No resume file uploaded by this candidate.</p>
                        </div>
                    @endif

                    <!-- Contact Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h2>
                        <div class="space-y-3">
                            @php
                                $contactEmail = $application->is_guest_applicant ? $application->guest_email : $application->user?->email;
                                $contactPhone = $application->is_guest_applicant ? $application->guest_phone : $application->user?->profile?->phone;
                                $linkedinUrl  = $application->is_guest_applicant ? null : $application->user?->profile?->linkedin_url;
                            @endphp
                            @if($contactEmail)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <a href="mailto:{{ $contactEmail }}" class="text-blue-600 hover:underline">
                                    {{ $contactEmail }}
                                </a>
                            </div>
                            @endif
                            @if($contactPhone)
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <a href="tel:{{ $contactPhone }}" class="text-blue-600 hover:underline">
                                        {{ $contactPhone }}
                                    </a>
                                </div>
                            @endif
                            @if($linkedinUrl)
                                <div>
                                    <p class="text-sm text-gray-500">LinkedIn</p>
                                    <a href="{{ $linkedinUrl }}" target="_blank" class="text-blue-600 hover:underline">
                                        View Profile
                                    </a>
                                </div>
                            @endif
                            @if($application->portfolio_url)
                                <div>
                                    <p class="text-sm text-gray-500">Portfolio</p>
                                    <a href="{{ $application->portfolio_url }}" target="_blank" class="text-blue-600 hover:underline">
                                        View Portfolio
                                    </a>
                                </div>
                            @endif
                            @if($application->github_url)
                                <div>
                                    <p class="text-sm text-gray-500">GitHub</p>
                                    <a href="{{ $application->github_url }}" target="_blank" class="text-blue-600 hover:underline">
                                        View GitHub
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
function advancePipelineStage() {
    const stage     = document.getElementById('pipeline-stage').value;
    const stageDate = document.getElementById('pipeline-stage-date').value;
    const notes     = document.querySelector('[name="stage_notes"]').value;
    const result    = document.getElementById('pipeline-result');

    if (!stage) {
        result.className = 'text-sm font-medium rounded-xl p-3 bg-red-50 text-red-700';
        result.textContent = 'Please select a stage.';
        result.classList.remove('hidden');
        return;
    }
    if (!['hired','rejected'].includes(stage) && !stageDate) {
        result.className = 'text-sm font-medium rounded-xl p-3 bg-red-50 text-red-700';
        result.textContent = 'Please select a scheduled date.';
        result.classList.remove('hidden');
        return;
    }

    const btn = document.querySelector('#pipeline-form button[onclick]');
    btn.textContent = 'Sending…';
    btn.disabled = true;

    fetch('{{ route("employer.applicants.setPipelineStage", $application->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ stage, stage_date: stageDate, stage_notes: notes }),
    })
    .then(r => {
        if (!r.ok) {
            return r.text().then(t => {
                let msg = 'Server error ' + r.status;
                try { const j = JSON.parse(t); msg = j.message || (j.errors ? Object.values(j.errors).flat().join(', ') : msg); } catch(e) {}
                throw new Error(msg);
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            result.className = 'text-sm font-medium rounded-xl p-3 bg-green-50 text-green-700';
            result.textContent = '✅ ' + data.message;
        } else {
            result.className = 'text-sm font-medium rounded-xl p-3 bg-red-50 text-red-700';
            result.textContent = '❌ ' + (data.message || 'Something went wrong.');
        }
        result.classList.remove('hidden');
        btn.textContent = 'Advance & Send Email';
        btn.disabled = false;
    })
    .catch(err => {
        result.className = 'text-sm font-medium rounded-xl p-3 bg-red-50 text-red-700';
        result.textContent = '❌ ' + (err.message || 'Network error. Please try again.');
        result.classList.remove('hidden');
        btn.textContent = 'Advance & Send Email';
        btn.disabled = false;
    });
}

// Hide date picker for hired/rejected
document.getElementById('pipeline-stage').addEventListener('change', function() {
    const wrap = document.getElementById('stage-date-wrap');
    wrap.style.display = ['hired','rejected'].includes(this.value) ? 'none' : '';
});
</script>
@endpush

</x-app-layout>
