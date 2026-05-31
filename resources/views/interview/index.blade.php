@extends('layouts.dashboard')

@section('title', 'Interview Lab')
@section('page-title', 'Interview Lab')
@section('page-description', 'AI-powered mock interviews & coaching')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:#0C2E72;">
        <div class="absolute inset-0" style="background-image:rgba(167,139,250,.3);"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,.2);color:#fff;">PRACTICE MODE</span>
                <h1 class="text-2xl font-bold mt-2" style="color:#fff;">Interview Lab</h1>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,.85);">AI-powered mock interviews with real-time coaching &amp; feedback.</p>
            </div>
            <a href="{{ route('interview.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all shadow-sm text-sm flex-shrink-0" style="background:#fff;color:#1B57C4;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Start Practice Session
            </a>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $sessions->count() ?? 0 }}</div>
            <div class="text-sm text-gray-600 mt-1">Practice Sessions</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $avgScore }}<span class="text-lg font-semibold text-gray-500">%</span></div>
            <div class="text-sm text-gray-600 mt-1">Avg. Score</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#1B57C4;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $questionsPracticed }}</div>
            <div class="text-sm text-gray-600 mt-1">Questions Practiced</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $interviewReadyPct }}<span class="text-lg font-semibold text-gray-500">%</span></div>
            <div class="text-sm text-gray-600 mt-1">Interview Ready</div>
        </div>
    </div>

    {{-- QUICK ACTION TILES --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Mock Interview --}}
        <div class="bg-white rounded-2xl p-6 text-center group transition-all" style="border:1.5px solid #BFCFEE;" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='#fff'">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg transition-transform group-hover:scale-110" style="background:#2D6CDF;">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Mock Interview</h3>
            <p class="text-xs text-gray-500 mb-4">AI-powered with real-time feedback</p>
            <a href="{{ route('interview.create') }}" class="inline-flex items-center justify-center w-full px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" style="background:#2D6CDF;" onmouseover="this.style.background='#1B57C4'" onmouseout="this.style.background='#2D6CDF'">Start Now</a>
        </div>

        {{-- Question Bank --}}
        <div class="bg-white rounded-2xl p-6 text-center group transition-all" style="border:1.5px solid #BFCFEE;" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='#fff'">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg transition-transform group-hover:scale-110" style="background:#2D6CDF;">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Question Bank</h3>
            <p class="text-xs text-gray-500 mb-4">500+ curated interview questions</p>
            <a href="{{ route('interview.common-questions') }}" class="inline-flex items-center justify-center w-full px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" style="background:#2D6CDF;" onmouseover="this.style.background='#2D6CDF'" onmouseout="this.style.background='#2D6CDF'">Browse Questions</a>
        </div>

        {{-- Pro Tips --}}
        <div class="bg-white rounded-2xl p-6 text-center group transition-all" style="border:1.5px solid #BFCFEE;" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='#fff'">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg transition-transform group-hover:scale-110" style="background:#2D6CDF;">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Pro Tips &amp; Guides</h3>
            <p class="text-xs text-gray-500 mb-4">Master interview techniques &amp; strategies</p>
            <a href="{{ route('interview.tips') }}" class="inline-flex items-center justify-center w-full px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" style="background:#1B57C4;" onmouseover="this.style.background='#0C2E72'" onmouseout="this.style.background='#1B57C4'">View Tips</a>
        </div>
    </div>

    {{-- SESSIONS + SIDEBAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Recent Sessions --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Recent Sessions</h2>
                <a href="#" class="text-sm font-medium hover:underline" style="color:#2D6CDF;">View all ?</a>
            </div>

            @if(isset($sessions) && $sessions->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($sessions->take(5) as $session)
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:{{ $session->completed_at ? '#EBF2FF' : '#EBF2FF' }};">
                        @if($session->completed_at)
                            <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-900 truncate">{{ $session->job_title ?? $session->role_title ?? 'General Interview' }}</span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                  style="{{ $session->completed_at ? 'background:#EBF2FF;color:#1B57C4;' : 'background:#EBF2FF;color:#2D6CDF;' }}">
                                {{ $session->completed_at ? 'Completed' : 'In Progress' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $session->questions_answered ?? 0 }} questions &middot; {{ $session->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($session->overall_score)
                        <div class="relative w-10 h-10">
                            <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E2E2E0" stroke-width="3"/>
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $session->overall_score >= 90 ? '#2D6CDF' : ($session->overall_score >= 75 ? '#2D6CDF' : '#2D6CDF') }}" stroke-width="3" stroke-dasharray="{{ $session->overall_score }},100"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-gray-900" style="font-size:10px;font-weight:700;">{{ $session->overall_score }}</span>
                        </div>
                        @endif
                        @if($session->completed_at)
                            <a href="{{ route('interview.complete', $session->cache_key) }}" class="text-xs font-semibold hover:underline" style="color:#2D6CDF;">Report ➟</a>
                        @else
                            <a href="{{ route('interview.session', $session) }}" class="px-3 py-1.5 text-white text-xs font-semibold rounded-lg transition-colors" style="background:#2D6CDF;">Continue</a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-14 text-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3" style="background:#EBF2FF;">
                    <svg class="w-7 h-7" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">No sessions yet</p>
                <p class="text-xs text-gray-500 mt-1 mb-4">Start your first mock interview to track your progress</p>
                <a href="{{ route('interview.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" style="background:#2D6CDF;">Start Practice</a>
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">
            {{-- AI Coach Insights --}}
            <div class="rounded-2xl p-5 text-white" style="background:#0C2E72;">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,.2);">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div class="text-sm font-semibold text-white">AI Coach Insights</div>
                </div>
                <div class="space-y-2.5">
                    <div class="rounded-xl p-3 text-sm text-white" style="background:rgba(255,255,255,.15);">&#x1F4A1; <span class="font-medium">Use STAR method</span> � Structure behavioral answers for clarity.</div>
                    <div class="rounded-xl p-3 text-sm text-white" style="background:rgba(255,255,255,.15);">&#x2B50; <span class="font-medium">Strong technical</span> � Your code explanations are clear.</div>
                </div>
            </div>

            {{-- Learning Resources --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Learning Resources</h3>
                <div class="space-y-1.5">
                    <a href="{{ route('interview.star-guide') }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors group" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#EBF2FF;">&#x2B50;</span>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">STAR Method Guide</div>
                            <div class="text-xs text-gray-500">Master behavioral interviews</div>
                        </div>
                    </a>
                    <a href="{{ route('interview.salary-negotiation') }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors group" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#EBF2FF;">&#x1F4B0;</span>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Salary Negotiation</div>
                            <div class="text-xs text-gray-500">Get the compensation you deserve</div>
                        </div>
                    </a>
                    @if(isset($sessions) && $sessions->where('vantage_score', '>', 0)->isNotEmpty())
                    <a href="{{ route('interview.skill-map', $sessions->where('vantage_score', '>', 0)->sortByDesc('created_at')->first()) }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors group" style="border:1px solid #BFCFEE;" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#EBF2FF;">&#x1F9E0;</span>
                        <div>
                            <div class="text-sm font-semibold" style="color:#1B57C4;">Vantage Skill Map</div>
                            <div class="text-xs text-gray-500">Future-ready competency radar</div>
                        </div>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Upcoming --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Upcoming Interviews</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:#EBF2FF;color:#1B57C4;">2 this week</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex flex-col items-center justify-center flex-shrink-0" style="background:#EBF2FF;">
                            <span class="font-bold" style="font-size:9px;color:#1B57C4;">DEC</span>
                            <span class="text-sm font-bold leading-none" style="color:#1B57C4;">2</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Google � Technical</div>
                            <div class="text-xs text-gray-500">10:00 AM PST</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex flex-col items-center justify-center flex-shrink-0" style="background:#EBF2FF;">
                            <span class="font-bold" style="font-size:9px;color:#2D6CDF;">DEC</span>
                            <span class="text-sm font-bold leading-none" style="color:#2D6CDF;">5</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Spotify � Culture Fit</div>
                            <div class="text-xs text-gray-500">2:30 PM PST</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
