@extends('layouts.dashboard')

@section('title', 'Job Search')
@section('page-title', 'Find Jobs')
@section('page-description', 'Discover opportunities matched by AI')

@push('styles')
<style>
/* Force all text in job search to be visible */
#job-search-page { color: #1a1a2e; }
#job-search-page p, #job-search-page span, #job-search-page label,
#job-search-page h1, #job-search-page h2, #job-search-page h3,
#job-search-page h4, #job-search-page h5, #job-search-page h6,
#job-search-page a:not(.btn-apply), #job-search-page li,
#job-search-page select option { color: #1a1a2e; }
#job-search-page .text-muted, #job-search-page .text-gray-400,
#job-search-page .text-gray-500, #job-search-page .text-gray-600 { color: #6b7280 !important; }
#job-search-page .text-gray-700, #job-search-page .text-gray-800,
#job-search-page .text-gray-900 { color: #1a1a2e !important; }

/* Hide scrollbars on sticky panels */
#job-search-page aside::-webkit-scrollbar { display: none; }
/* Hide Alpine x-cloak elements until Alpine initialises */
[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div id="job-search-page">
{{-- HERO --}}
<style>
@keyframes heroGradientShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes floatOrb1 {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: .55; }
    33%       { transform: translate(30px, -20px) scale(1.15); opacity: .7; }
    66%       { transform: translate(-20px, 15px) scale(0.9); opacity: .45; }
}
@keyframes floatOrb2 {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: .4; }
    40%       { transform: translate(-35px, 20px) scale(1.2); opacity: .6; }
    70%       { transform: translate(20px, -10px) scale(0.85); opacity: .35; }
}
@keyframes floatOrb3 {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: .3; }
    50%       { transform: translate(15px, 25px) scale(1.1); opacity: .5; }
}
@keyframes shimmerText {
    0%,100% { background-position: 0% center; }
    50%      { background-position: 100% center; }
}
@keyframes sparkle {
    0%,100% { opacity:0; transform:scale(0) rotate(0deg); }
    50%      { opacity:1; transform:scale(1) rotate(180deg); }
}
@keyframes searchPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
    50%      { box-shadow: 0 0 0 6px rgba(255,255,255,0); }
}
.hero-search-btn:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 8px 24px rgba(0,0,0,.25) !important; }
.hero-search-btn { transition: transform .2s ease, box-shadow .2s ease; }
.hero-input:focus { box-shadow: 0 0 0 3px rgba(167,139,250,0.6), 0 2px 8px rgba(0,0,0,.1) !important; outline: none !important; }
</style>

<div class="relative overflow-hidden rounded-2xl p-6 mb-6" style="background:linear-gradient(-45deg,#e8005a,#7c3aed,#6d28d9,#b845eb,#e8005a,#7c3aed); background-size:400% 400%; animation:heroGradientShift 8s ease infinite; z-index:0; isolation:isolate;">

    {{-- Floating orbs --}}
    <div class="absolute" style="top:-30px;left:-30px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(255,182,255,0.55),transparent 70%);animation:floatOrb1 7s ease-in-out infinite;pointer-events:none;"></div>
    <div class="absolute" style="bottom:-40px;right:5%;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(255,80,120,0.45),transparent 70%);animation:floatOrb2 9s ease-in-out infinite;pointer-events:none;"></div>
    <div class="absolute" style="top:10px;right:25%;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(200,170,255,0.5),transparent 70%);animation:floatOrb3 6s ease-in-out infinite;pointer-events:none;"></div>

    {{-- Sparkle dots --}}
    <div class="absolute" style="top:18px;right:18%;width:6px;height:6px;background:white;border-radius:50%;animation:sparkle 2.5s ease-in-out infinite;pointer-events:none;"></div>
    <div class="absolute" style="top:38px;right:35%;width:4px;height:4px;background:rgba(255,200,255,1);border-radius:50%;animation:sparkle 3.2s ease-in-out infinite .8s;pointer-events:none;"></div>
    <div class="absolute" style="bottom:22px;left:30%;width:5px;height:5px;background:rgba(255,255,200,1);border-radius:50%;animation:sparkle 2.8s ease-in-out infinite 1.4s;pointer-events:none;"></div>
    <div class="absolute" style="top:50%;right:12%;width:4px;height:4px;background:white;border-radius:50%;animation:sparkle 3.5s ease-in-out infinite .4s;pointer-events:none;"></div>

    {{-- Noise texture overlay --}}
    <div class="absolute inset-0 rounded-2xl" style="background-image:radial-gradient(circle at 80% 20%,rgba(255,255,255,.18) 0%,transparent 55%),radial-gradient(circle at 10% 80%,rgba(255,100,200,.15) 0%,transparent 45%); pointer-events:none;"></div>

    <div class="relative">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center rounded-xl" style="width:44px;height:44px;background:rgba(255,255,255,0.22);flex-shrink:0;backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.3);animation:searchPulse 2.5s ease-in-out infinite;">
                <svg style="width:22px;height:22px;color:white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color:white;text-shadow:0 2px 12px rgba(0,0,0,.25);">Find Your Next Opportunity</h1>
                <p class="text-sm" style="color:rgba(255,255,255,0.9);">AI-matched jobs based on your profile</p>
            </div>
        </div>
        <form method="GET" action="{{ route('jobs.search') }}" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Job title, skills, or keywords..."
                   class="hero-input"
                   style="flex:1;min-width:200px;background:rgba(255,255,255,0.95);color:#1a1a2e;border:none;border-radius:12px;padding:12px 16px;font-size:14px;box-shadow:0 2px 12px rgba(0,0,0,.15);transition:box-shadow .2s;">
            <select name="location"
                    style="width:160px;background:rgba(255,255,255,0.95);color:#1a1a2e;border:none;border-radius:12px;padding:12px 16px;font-size:14px;outline:none;box-shadow:0 2px 12px rgba(0,0,0,.15);cursor:pointer;">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
            <button type="submit" class="hero-search-btn" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:white;color:#7c3aed;font-weight:700;font-size:14px;border:none;border-radius:12px;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.18);white-space:nowrap;">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search Jobs
            </button>
        </form>
    </div>
</div>

<div x-data="{ 
    selectedJob: null,
    selectedJobData: null,
    showFilters: true,
    activeFilters: 0,
    selectJob(id, rawData, el) {
        let data;
        try {
            data = (typeof rawData === 'string') ? JSON.parse(rawData) : rawData;
        } catch(e) {
            console.error('Job data parse error:', e);
            return;
        }
        if (!Array.isArray(data.skills)) data.skills = [];
        this.selectedJobData = data;
        this.selectedJob = id;
        if (el) {
            const cardTop = el.getBoundingClientRect().top + window.scrollY;
            window.scrollTo({ top: Math.max(0, cardTop - 88), behavior: 'smooth' });
        }
    },
    formatSalary(min, max) {
        if (!min && !max) return null;
        const fmt = n => n >= 100000 ? (n/100000).toFixed(1)+'L' : (n/1000).toFixed(0)+'K';
        return '\u20B9' + fmt(min) + ' - \u20B9' + fmt(max);
    }
}">
    {{-- Search Bar (hidden â€” using hero above) --}}
    <form method="GET" action="{{ route('jobs.search') }}" class="hidden">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Main Search --}}
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="keyword" 
                       value="{{ request('keyword') }}" 
                       placeholder="Job title, skills, or company..." 
                       class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-soft focus:ring-2 focus:ring-violet-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-400 transition-all">
            </div>
            {{-- Location Search --}}
            <div class="lg:w-64 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                </div>
                <select name="location" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-soft focus:ring-2 focus:ring-violet-500 focus:border-transparent text-gray-900 dark:text-white appearance-none cursor-pointer transition-all">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Search Button --}}
            <x-studai.button type="submit" variant="primary" class="lg:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search
            </x-studai.button>
        </div>
    </form>

    {{-- 3-Column Layout (sticky sidebar â€” page scrolls naturally) --}}
    <div class="flex gap-5 items-start">
        {{-- Left: Filters Panel --}}
        <aside class="hidden lg:block flex-shrink-0" style="width:232px; position:sticky; top:80px; max-height:calc(100vh - 96px); overflow-y:auto; overflow-x:hidden; align-self:flex-start; scrollbar-width:none;">
            <x-studai.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Filters</h3>
                    @if(request()->hasAny(['keyword', 'location', 'experience_level', 'job_type', 'salary_min']))
                        <a href="{{ route('jobs.search') }}" class="text-sm text-violet-600 hover:text-violet-700">Clear all</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('jobs.search') }}" class="space-y-6">
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                    <input type="hidden" name="location" value="{{ request('location') }}">

                    {{-- Experience Level --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Experience Level</h4>
                        <div class="space-y-2">
                            @foreach($experienceLevels as $level)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="experience_level[]" value="{{ $level }}" 
                                           {{ in_array($level, (array)request('experience_level')) ? 'checked' : '' }}
                                           class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ ucfirst($level) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Job Type --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Job Type</h4>
                        <div class="space-y-2">
                            @foreach($jobTypes as $type)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="job_type[]" value="{{ $type }}"
                                           {{ in_array($type, (array)request('job_type')) ? 'checked' : '' }}
                                           class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ ucwords(str_replace('-', ' ', $type)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Salary Range --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Salary Range</h4>
                        <div class="space-y-3">
                            <x-studai.input type="number" name="salary_min" :value="request('salary_min')" placeholder="Min (LPA)" size="sm" />
                            <x-studai.input type="number" name="salary_max" :value="request('salary_max')" placeholder="Max (LPA)" size="sm" />
                        </div>
                    </div>

                    {{-- Work Mode --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Work Mode</h4>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer">
                                <input type="checkbox" name="remote" value="1" class="sr-only peer" {{ request('remote') ? 'checked' : '' }}>
                                <span class="inline-flex px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-full peer-checked:bg-violet-50 peer-checked:border-violet-500 peer-checked:text-violet-600 transition-all">Remote</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="hybrid" value="1" class="sr-only peer" {{ request('hybrid') ? 'checked' : '' }}>
                                <span class="inline-flex px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-full peer-checked:bg-violet-50 peer-checked:border-violet-500 peer-checked:text-violet-600 transition-all">Hybrid</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="onsite" value="1" class="sr-only peer" {{ request('onsite') ? 'checked' : '' }}>
                                <span class="inline-flex px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-full peer-checked:bg-violet-50 peer-checked:border-violet-500 peer-checked:text-violet-600 transition-all">On-site</span>
                            </label>
                        </div>
                    </div>

                    <x-studai.button type="submit" variant="primary" class="w-full">Apply Filters</x-studai.button>
                </form>
            </x-studai.card>
        </aside>

        {{-- Center: Job List --}}
        <div class="flex-1 min-w-0">
            {{-- Results Header --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $jobs->total() }}</span> jobs found
                        @if(request('keyword'))
                            for "<span class="font-medium">{{ request('keyword') }}</span>"
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <select name="sort" form="sort-form" onchange="this.form.submit()" class="text-sm bg-transparent border-0 text-gray-600 dark:text-gray-400 focus:ring-0 cursor-pointer pr-8">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="salary_high" {{ request('sort') == 'salary_high' ? 'selected' : '' }}>Highest Salary</option>
                        <option value="relevant" {{ request('sort') == 'relevant' ? 'selected' : '' }}>Most Relevant</option>
                    </select>
                    <form id="sort-form" method="GET" action="{{ route('jobs.search') }}" class="hidden">
                        @foreach(request()->except('sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
            </div>

            @if($jobs->isEmpty())
                {{-- Empty State --}}
                <x-studai.card class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No jobs found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filters</p>
                    <x-studai.button href="{{ route('jobs.search') }}" variant="primary">
                        View All Jobs
                    </x-studai.button>
                </x-studai.card>
            @else
                {{-- Job Cards List --}}
                <div class="space-y-3">
                    @foreach($jobs as $job)
                        @php
                            $rawSkills = $job->required_skills ?? [];
                            if (is_string($rawSkills)) {
                                $rawSkills = json_decode($rawSkills, true) ?? [];
                            }
                            $jobSkills = collect($rawSkills)
                                ->map(fn($s) => is_array($s) ? ($s['name'] ?? '') : (string)$s)
                                ->filter()->values()->toArray();
                            $jobData = [
                                'id'               => $job->id,
                                'title'            => $job->title,
                                'company_name'     => $job->company_name ?? ($job->company->name ?? 'Unknown'),
                                'location'         => $job->location ?? '',
                                'job_type'         => $job->employment_type ?? $job->job_type ?? '',
                                'experience_level' => $job->experience_level ?? '',
                                'salary_min'       => $job->salary_min,
                                'salary_max'       => $job->salary_max,
                                'skills'           => $jobSkills,
                                'posted_at'        => $job->created_at->diffForHumans(),
                                'work_mode'        => $job->work_mode ?? $job->location_type ?? '',
                                'show_url'         => route('jobs.show', $job->id),
                                'apply_url'        => route('api.jobs.apply', $job->id),
                                'match_score'      => $matchScores[$job->id] ?? null,
                                'has_applied'      => in_array((int) $job->id, $appliedJobIds ?? [], true),
                            ];
                            $palette = [
                                ['accent'=>'#6366f1','light'=>'rgba(99,102,241,.065)','avatar'=>'135deg,#818cf8,#6366f1'],
                                ['accent'=>'#0ea5e9','light'=>'rgba(14,165,233,.065)','avatar'=>'135deg,#38bdf8,#0ea5e9'],
                                ['accent'=>'#10b981','light'=>'rgba(16,185,129,.065)','avatar'=>'135deg,#34d399,#10b981'],
                                ['accent'=>'#f59e0b','light'=>'rgba(245,158,11,.065)','avatar'=>'135deg,#fbbf24,#f59e0b'],
                                ['accent'=>'#ec4899','light'=>'rgba(236,72,153,.065)','avatar'=>'135deg,#f472b6,#ec4899'],
                            ];
                            $c = $palette[$loop->index % 5];
                        @endphp
                        <div @click="selectJob({{ $job->id }}, $el.dataset.job, $el)"
                             data-job='{!! json_encode($jobData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) !!}'
                             data-job-id="{{ $job->id }}"
                             :class="{ 'ring-2 border-violet-400': selectedJob === {{ $job->id }} }"
                             class="group cursor-pointer rounded-2xl border border-gray-100 p-5 transition-all duration-200 relative overflow-hidden"
                             style="background:white;box-shadow:0 1px 3px rgba(0,0,0,.04);"
                             onmouseenter="this.style.background='{{ $c['light'] }}';this.style.boxShadow='0 6px 20px rgba(0,0,0,.09)';this.style.transform='translateY(-1px)'"
                             onmouseleave="this.style.background='white';this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.transform=''">
                            {{-- Left accent bar --}}
                            <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:{{ $c['accent'] }};border-radius:4px 0 0 4px;"></div>
                            <div class="flex items-start gap-4 pl-3">
                                {{-- Company Logo --}}
                                <div class="flex-shrink-0 rounded-xl flex items-center justify-center font-bold text-white"
                                     style="width:44px;height:44px;font-size:18px;background:linear-gradient({{ $c['avatar'] }});box-shadow:0 3px 10px rgba(0,0,0,.18);flex-shrink:0;">
                                    {{ strtoupper(substr($job->company_name ?? 'J', 0, 1)) }}
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-violet-600 transition-colors line-clamp-1">
                                                {{ $job->title }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $job->company_name }}</p>
                                        </div>
                                        @auth
                                            @if(isset($matchScores[$job->id]))
                                                <x-studai.ai-score :score="$matchScores[$job->id]" size="sm" />
                                            @endif
                                        @endauth
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                            {{ $job->location }}
                                        </span>
                                        <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('-', ' ', $job->employment_type)) }}</span>
                                        @if($job->salary_min && $job->salary_max)
                                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                            <span class="text-xs font-semibold" style="color:{{ $c['accent'] }}">
                                                ₹{{ number_format($job->salary_min / 100000, 1) }}L – ₹{{ number_format($job->salary_max / 100000, 1) }}L
                                            </span>
                                        @endif
                                    </div>

                                    @if($job->required_skills)
                                        @php
                                            $skillList = is_array($job->required_skills) ? $job->required_skills : (json_decode($job->required_skills, true) ?? []);
                                            $skillList = array_slice(array_filter(array_map(fn($s) => is_array($s) ? ($s['name'] ?? '') : (string)$s, $skillList)), 0, 4);
                                        @endphp
                                        @if(count($skillList) > 0)
                                        <div class="flex flex-wrap gap-1.5 mt-3">
                                            @foreach($skillList as $skill)
                                                <x-studai.chip size="xs">{{ $skill }}</x-studai.chip>
                                            @endforeach
                                        </div>
                                        @endif
                                    @endif

                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50 dark:border-gray-700">
                                        <span class="text-xs text-gray-400">{{ $job->created_at->diffForHumans() }}</span>
                                        @auth
                                            <button onclick="event.stopPropagation(); toggleSave({{ $job->id }})" 
                                                    id="save-btn-{{ $job->id }}"
                                                    class="p-1.5 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                </svg>
                                            </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6 pb-4">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Right: Job Detail Panel (lg+ sticky) --}}
        <aside class="hidden lg:block flex-shrink-0" style="width:310px; position:sticky; top:80px; max-height:calc(100vh - 96px); overflow-y:auto; overflow-x:hidden; align-self:flex-start; scrollbar-width:none;">
            <div x-show="!selectedJob" style="display:flex;align-items:center;justify-content:center;padding:48px 0;">
                <x-studai.card class="text-center py-12 w-full">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Select a job</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Click on a job card to see details here</p>
                </x-studai.card>
            </div>

            <template x-if="selectedJob && selectedJobData">
                <div style="display:flex; flex-direction:column; background:white; border:1px solid #ebebf5; border-radius:16px; box-shadow:0 4px 24px rgba(99,102,241,.10), 0 1px 4px rgba(0,0,0,.05);">

                    {{-- â”€â”€ FIXED TOP: header + apply button â”€â”€ --}}
                    <div class="flex-shrink-0 p-5 border-b" style="border-color:#ebebf5">
                        {{-- Header --}}
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                                 style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)"
                                 x-text="selectedJobData.company_name.charAt(0).toUpperCase()">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="font-bold line-clamp-2" style="font-size:15px; color:#1a1a2e" x-text="selectedJobData.title"></h2>
                                <p class="text-sm mt-0.5" style="color:#6b7280" x-text="selectedJobData.company_name"></p>
                                <p class="text-xs mt-1" style="color:#9ca3af" x-text="'Posted ' + selectedJobData.posted_at"></p>
                            </div>
                        </div>

                        {{-- Apply button ALWAYS VISIBLE --}}
                        <div class="flex gap-2">
                            @auth
                            {{-- Already applied: show status + view description --}}
                            <template x-if="selectedJobData.has_applied">
                                <div class="flex-1 flex gap-2">
                                    <span class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm"
                                          style="background:#e8f5e9; color:#15803d; border:1.5px solid #bbf7d0">
                                        <i class="fas fa-check-circle" style="font-size:13px"></i>
                                        Applied Already
                                    </span>
                                    <a :href="selectedJobData.show_url"
                                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-all hover:-translate-y-0.5"
                                       style="border:1.5px solid #8b5cf6; color:#7c3aed">
                                        <i class="fas fa-file-alt" style="font-size:13px"></i>
                                        View Job Description
                                    </a>
                                </div>
                            </template>
                            {{-- Not applied yet: show apply button --}}
                            <template x-if="!selectedJobData.has_applied">
                                <a :href="selectedJobData.show_url + '#apply'"
                                   class="btn-apply flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm text-white transition-all hover:-translate-y-0.5"
                                   style="background:linear-gradient(135deg,#8b5cf6,#7c3aed); box-shadow:0 4px 14px rgba(139,92,246,.3)">
                                    <i class="fas fa-paper-plane" style="font-size:13px"></i>
                                    Apply Now
                                </a>
                            </template>
                            <button @click.stop="toggleSaveJob(selectedJobData.id)"
                                    :id="'save-panel-btn-' + selectedJobData.id"
                                    class="p-2.5 rounded-xl transition-colors"
                                    style="border:1.5px solid #ebebf5; color:#6b7280">
                                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                            @else
                            <a href="{{ route('login') }}"
                               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-semibold text-sm text-white"
                               style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                                Login to Apply
                            </a>
                            @endauth
                        </div>
                    </div>

                    {{-- â”€â”€ BODY â”€â”€ --}}
                    <div class="flex-1 p-5 space-y-5">

                        @auth
                        {{-- AI Match --}}
                        <div class="p-3 rounded-xl" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe)"
                             x-show="selectedJobData && selectedJobData.match_score != null && selectedJobData.match_score > 0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold" style="color:#1a1a2e">AI Match Score</p>
                                    <p class="text-xs mt-0.5" style="color:#6b7280">Based on your profile &amp; skills</p>
                                </div>
                                {{-- Inline Alpine-driven ring (replaces Blade component) --}}
                                <div class="relative flex-shrink-0" style="width:72px;height:72px">
                                    <svg style="width:100%;height:100%;transform:rotate(-90deg)" viewBox="0 0 72 72">
                                        <circle stroke="#e5e7eb" stroke-width="4" fill="transparent" r="34" cx="36" cy="36"/>
                                        <circle :stroke="(selectedJobData.match_score||0)>=85?'#10b981':(selectedJobData.match_score||0)>=70?'#1A73E8':(selectedJobData.match_score||0)>=50?'#f59e0b':'#ef4444'"
                                                stroke-width="4" stroke-linecap="round" fill="transparent" r="34" cx="36" cy="36"
                                                :stroke-dasharray="213.6"
                                                :stroke-dashoffset="213.6*(1-(selectedJobData.match_score||0)/100)"
                                                style="transition:stroke-dashoffset 0.8s ease-out,stroke 0.3s"/>
                                    </svg>
                                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                        <span class="font-bold"
                                              style="font-size:15px"
                                              :style="'color:'+((selectedJobData.match_score||0)>=85?'#10b981':(selectedJobData.match_score||0)>=70?'#1A73E8':(selectedJobData.match_score||0)>=50?'#f59e0b':'#ef4444')"
                                              x-text="(selectedJobData.match_score||0)+'%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endauth

                        {{-- Quick Info --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-3 rounded-xl" style="background:#f9f9ff; border:1px solid #ebebf5">
                                <p class="text-xs" style="color:#9ca3af">Salary</p>
                                <p class="text-sm font-semibold mt-0.5" style="color:#1a1a2e"
                                   x-text="formatSalary(selectedJobData.salary_min, selectedJobData.salary_max) || 'Not specified'"></p>
                            </div>
                            <div class="p-3 rounded-xl" style="background:#f9f9ff; border:1px solid #ebebf5">
                                <p class="text-xs" style="color:#9ca3af">Location</p>
                                <p class="text-sm font-semibold mt-0.5 line-clamp-1" style="color:#1a1a2e" x-text="selectedJobData.location || 'Not specified'"></p>
                            </div>
                            <div class="p-3 rounded-xl" style="background:#f9f9ff; border:1px solid #ebebf5">
                                <p class="text-xs" style="color:#9ca3af">Experience</p>
                                <p class="text-sm font-semibold mt-0.5 capitalize" style="color:#1a1a2e" x-text="selectedJobData.experience_level || 'Not specified'"></p>
                            </div>
                            <div class="p-3 rounded-xl" style="background:#f9f9ff; border:1px solid #ebebf5">
                                <p class="text-xs" style="color:#9ca3af">Type</p>
                                <p class="text-sm font-semibold mt-0.5 capitalize" style="color:#1a1a2e" x-text="(selectedJobData.job_type || 'N/A').replace(/-/g, ' ')"></p>
                            </div>
                        </div>

                        {{-- Skills --}}
                        <template x-if="selectedJobData.skills && selectedJobData.skills.length > 0">
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#9ca3af">Required Skills</h4>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="skill in selectedJobData.skills.slice(0, 8)" :key="skill">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                              style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe"
                                              x-text="skill"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Full details link --}}
                        <a :href="selectedJobData.show_url"
                           class="block text-center text-sm font-semibold transition-colors"
                           style="color:#8b5cf6">
                            View Full Job Description â†’
                        </a>
                    </div>
                </div>
            </template>
        </aside>
    </div>
</div>
</div>

@auth
<script>
function showSaveToast(message, saved) {
    let toast = document.getElementById('save-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'save-toast';
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;color:white;box-shadow:0 4px 20px rgba(0,0,0,.18);transition:opacity .3s;display:flex;align-items:center;gap:8px;';
        document.body.appendChild(toast);
    }
    toast.style.background = saved ? '#8b5cf6' : '#6b7280';
    toast.innerHTML = saved
        ? '<svg style="width:16px;height:16px" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg> Job saved!'
        : '<svg style="width:16px;height:16px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg> Job removed';
    toast.style.opacity = '1';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 2500);
}

function applySaveStyle(btn, saved) {
    if (!btn) return;
    const svg = btn.querySelector('svg');
    if (saved) {
        btn.style.color = '#8b5cf6';
        btn.style.background = '#f5f3ff';
        btn.style.borderColor = '#ddd6fe';
        if (svg) { svg.setAttribute('fill', 'currentColor'); svg.style.color = '#8b5cf6'; }
    } else {
        btn.style.color = '#9ca3af';
        btn.style.background = '';
        btn.style.borderColor = '';
        if (svg) { svg.setAttribute('fill', 'none'); svg.style.color = ''; }
    }
}

function toggleSave(jobId) {
    const btn = document.getElementById(`save-btn-${jobId}`);
    const panelBtn = document.getElementById(`save-panel-btn-${jobId}`);

    fetch(`/api/jobs/${jobId}/toggle-save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        applySaveStyle(btn, data.saved);
        applySaveStyle(panelBtn, data.saved);
        showSaveToast(data.message, data.saved);
    })
    .catch(error => {
        console.error('Error saving job:', error);
        showSaveToast('Something went wrong. Please try again.', false);
    });
}

function toggleSaveJob(jobId) {
    toggleSave(jobId);
}
</script>
@endauth

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
}
</style>
</div>{{-- end #job-search-page --}}
@endsection

