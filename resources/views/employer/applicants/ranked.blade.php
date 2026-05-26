@extends('layouts.dashboard')

@section('page-title', 'Ranked Candidates — ' . $job->title)
@section('page-description', 'Orin™ AI ranking results for ' . $job->title . ' at ' . ($job->company?->name ?? ''))

@section('content')

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('employer.home') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm hover:shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('employer.home') }}" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a>
                <span class="text-gray-300">/</span>
                <span class="text-sm text-gray-700">{{ $job->title }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Orin™ Ranked Shortlist</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $rankedCount }} candidates ranked · Top {{ $job->target_hire_count ?? 'N' }} shortlisted
                @if($job->final_date) · Results finalised {{ $job->final_date->format('d M Y') }} @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('employer.applicants.ranked.export', $job->id) }}"
                class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
            <span class="px-3 py-1.5 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                Phase: {{ ucfirst($job->application_phase ?? 'ranked') }}
            </span>
        </div>
    </div>

    {{-- Score breakdown legend --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
        <div class="flex flex-wrap gap-4 items-center text-xs">
            <span class="font-semibold text-blue-800">Score weights (Orin™):</span>
            <span class="bg-white border border-blue-200 px-2.5 py-1 rounded-lg text-blue-700">Evaluation <strong>45%</strong></span>
            <span class="bg-white border border-blue-200 px-2.5 py-1 rounded-lg text-blue-700">Skill Match <strong>25%</strong></span>
            <span class="bg-white border border-blue-200 px-2.5 py-1 rounded-lg text-blue-700">Resume Quality <strong>15%</strong></span>
            <span class="bg-white border border-blue-200 px-2.5 py-1 rounded-lg text-blue-700">Behavioural Fit <strong>15%</strong></span>
            <span class="text-gray-400 ml-auto">Anti-cheat penalties deducted before final score</span>
        </div>
    </div>

    {{-- Ranked candidates table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($applications->isEmpty())
            <div class="p-16 text-center">
                <span class="text-5xl">⏳</span>
                <p class="mt-4 text-lg font-semibold text-gray-900">No completed evaluations yet</p>
                <p class="text-gray-500 text-sm mt-1">Rankings will appear once candidates complete their Orin™ evaluation.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Rank</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Candidate</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Final Score</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Eval</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Skill</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Resume</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-600">Behaviour</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">Anti-Cheat</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($applications as $app)
                            @php
                                $rank = $app->rank_position ?? ($loop->index + 1);
                                $isShortlisted = $app->status === 'shortlisted';
                                $targetCount = $job->target_hire_count ?? 1;
                                $inTop = $rank <= $targetCount;
                                $name = $app->is_guest_applicant ? $app->guest_name : ($app->user?->name ?? 'Unknown');
                                $email = $app->is_guest_applicant ? $app->guest_email : $app->user?->email;
                                $score = $app->final_rank_score ?? 0;
                                $evalScore = $app->evaluation_score ?? 0;
                                $skillScore = $app->skill_match_score ?? 0;
                                $resumeScore = $app->resume_quality_score ?? 0;
                                $behavScore = $app->behavioural_fit_score ?? 0;
                                $session = $app->evaluationSession;
                                $tabSwitches = $session?->tab_switch_count ?? 0;
                                $flagged = $session?->flagged_for_review ?? false;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $inTop ? 'bg-green-50/30' : '' }}">
                                {{-- Rank --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($rank <= 3)
                                            <span class="text-xl">{{ ['🥇','🥈','🥉'][$rank-1] }}</span>
                                        @else
                                            <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-600">#{{ $rank }}</span>
                                        @endif
                                        @if($inTop)
                                            <span class="text-xs font-medium text-green-700 bg-green-100 px-1.5 py-0.5 rounded">Top {{ $targetCount }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Candidate --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900">{{ $name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $email }}</p>
                                    @if($app->portfolio_url)
                                        <a href="{{ $app->portfolio_url }}" target="_blank" class="text-xs text-blue-600 hover:underline">Portfolio</a>
                                    @endif
                                    @if($app->github_url)
                                        <a href="{{ $app->github_url }}" target="_blank" class="text-xs text-blue-600 hover:underline ml-2">GitHub</a>
                                    @endif
                                </td>

                                {{-- Final Score --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-xl font-bold {{ $score >= 75 ? 'text-green-600' : ($score >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                                            {{ number_format($score, 1) }}
                                        </span>
                                        <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="h-full rounded-full {{ $score >= 75 ? 'bg-green-500' : ($score >= 50 ? 'bg-yellow-500' : 'bg-red-400') }}" style="width: {{ min(100, $score) }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Score components --}}
                                <td class="px-5 py-4 text-right text-gray-600">{{ number_format($evalScore, 1) }}</td>
                                <td class="px-5 py-4 text-right text-gray-600">{{ number_format($skillScore, 1) }}</td>
                                <td class="px-5 py-4 text-right text-gray-600">{{ number_format($resumeScore, 1) }}</td>
                                <td class="px-5 py-4 text-right text-gray-600">{{ number_format($behavScore, 1) }}</td>

                                {{-- Anti-cheat --}}
                                <td class="px-5 py-4 text-center">
                                    @if($flagged)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-lg">⚠ Flagged</span>
                                    @elseif($tabSwitches > 3)
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-lg">{{ $tabSwitches }} tabs</span>
                                    @elseif($tabSwitches > 0)
                                        <span class="text-gray-400 text-xs">{{ $tabSwitches }} tab{{ $tabSwitches > 1 ? 's' : '' }}</span>
                                    @else
                                        <span class="text-green-600 text-xs">✓ Clean</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($app->status === 'shortlisted')
                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Shortlisted</span>
                                    @elseif($app->status === 'rejected')
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">Not selected</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-5 py-4">
                                    <a href="{{ route('applicants.show', $app->id) }}"
                                        class="text-xs text-blue-600 hover:underline font-medium">View →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Summary stats --}}
    @if($applications->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $targetCount = $job->target_hire_count ?? 1;
            $shortlisted = $applications->where('status', 'shortlisted')->count();
            $avgScore = $applications->avg('final_rank_score');
            $cleanCandidates = $applications->filter(fn($a) => ($a->evaluationSession?->tab_switch_count ?? 0) === 0)->count();
        @endphp
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $rankedCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Evaluated</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $shortlisted }}</p>
            <p class="text-xs text-gray-500 mt-1">Shortlisted</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ number_format($avgScore, 1) }}</p>
            <p class="text-xs text-gray-500 mt-1">Avg Score</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ $cleanCandidates }}</p>
            <p class="text-xs text-gray-500 mt-1">Clean Integrity</p>
        </div>
    </div>
    @endif

</div>
@endsection
