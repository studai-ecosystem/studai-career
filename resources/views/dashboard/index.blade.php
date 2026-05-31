<x-layouts.dashboard :title="'Dashboard'">

@push('styles')
<style>
/* ── KEYFRAMES ────────────────────────────────────────── */
@keyframes dash-fadeUp   { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:translateY(0); } }
@keyframes dash-scaleIn  { from { opacity:0; transform:scale(0.88);      } to { opacity:1; transform:scale(1);    } }
@keyframes dash-slideR   { from { opacity:0; transform:translateX(-18px);} to { opacity:1; transform:translateX(0); } }
@keyframes dash-count    { from { opacity:0; transform:translateY(8px);  } to { opacity:1; transform:translateY(0); } }
@keyframes dash-gradient { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
@keyframes dash-bar      { from { width:0; } }
@keyframes dash-spin     { to { transform:rotate(360deg); } }

/* ── ANIMATION 1: Entrance stagger ───────────────────── */
.dash-anim-1  { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .05s both; }
.dash-anim-2  { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .12s both; }
.dash-anim-3  { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .18s both; }
.dash-anim-4  { animation: dash-scaleIn .5s cubic-bezier(.34,1.56,.64,1) .08s both; }
.dash-anim-5  { animation: dash-scaleIn .5s cubic-bezier(.34,1.56,.64,1) .18s both; }
.dash-anim-6  { animation: dash-scaleIn .5s cubic-bezier(.34,1.56,.64,1) .28s both; }
.dash-anim-7  { animation: dash-scaleIn .5s cubic-bezier(.34,1.56,.64,1) .38s both; }
.dash-anim-8  { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .28s both; }
.dash-anim-9  { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .36s both; }
.dash-anim-10 { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .44s both; }
.dash-anim-11 { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .20s both; }
.dash-anim-12 { animation: dash-fadeUp  .5s cubic-bezier(.22,1,.36,1) .30s both; }

/* ── ANIMATION 2: Lift + glow hover ──────────────────── */
.stat-card {
    transition: transform .28s cubic-bezier(.22,1,.36,1), box-shadow .28s cubic-bezier(.22,1,.36,1), filter .28s;
    position: relative; overflow: hidden; cursor: pointer;
}
.stat-card:hover {
    transform: translateY(-7px) scale(1.02);
    box-shadow: none;
    filter: brightness(1.04);
}

/* ── ANIMATION 3: Shimmer sweep on hover ─────────────── */
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: -100%; width: 60%; height: 100%;
    background: rgba(255,255,255,.45);
    transform: skewX(-20deg);
    transition: left 0s;
    pointer-events: none;
}
.stat-card:hover::before { left: 160%; transition: left .65s ease; }

/* ── ANIMATION 4: Icon float on hover ────────────────── */
@keyframes icon-float { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-6px) rotate(-5deg)} }
@keyframes icon-spin  { to{transform:rotate(360deg)} }
.stat-card:hover .stat-icon { animation: icon-float 1.2s ease-in-out infinite; }

/* ── ANIMATION 5: Pulsing glow ring on icon ─────────── */
@keyframes ring-pulse {
    0%,100% { box-shadow: none; }
    50%      { box-shadow: none; }
}
.stat-card:hover .stat-icon { animation: icon-float 1.2s ease-in-out infinite, ring-pulse 1.8s ease-in-out infinite; }

/* ── Animated gradient banner ────────────────────────── */
.banner-gradient {
    background: #2D6CDF;
    background-size: 300% 300%;
    animation: dash-gradient 5s ease infinite;
}

/* ── Animated progress bar ───────────────────────────── */
.anim-progress-bar { animation: dash-bar 1.2s cubic-bezier(.22,1,.36,1) .5s both; }

/* ── Number counter ──────────────────────────────────── */
.stat-number { animation: dash-count .6s cubic-bezier(.22,1,.36,1) .3s both; }

/* ── Sidebar AI badge pulse ──────────────────────────── */
@keyframes dash-pulse { 0%,100%{box-shadow: none} 70%{box-shadow: none} }
.ai-badge-pulse { animation: dash-pulse 2s cubic-bezier(.22,1,.36,1) infinite; }

/* ── Job card hover ──────────────────────────────────── */
.job-card-hover {
    transition: transform .2s cubic-bezier(.22,1,.36,1), box-shadow .2s, border-color .2s;
}
.job-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: none;
    border-color: #BFCFEE !important;
}

/* ── Secondary card hover ────────────────────────────── */
.secondary-card {
    transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
}
.secondary-card:hover { transform: translateY(-3px); box-shadow: none; }

/* ── Application row hover ───────────────────────────── */
.app-row { transition: background .18s, transform .18s; }
.app-row:hover { background: #EBF2FF !important; transform: translateX(3px); }

/* ── Spin slow ───────────────────────────────────────── */
.spin-slow { animation: dash-spin 4s linear infinite; }
</style>
@endpush

    {{-- Upgrade Banner --}}
    @php
        $isUnlimited = $subscriptionStats['is_unlimited'] ?? false;
        $appsRaw = $subscriptionStats['applications_remaining_raw'] ?? 0;
        $showBanner = !$isUnlimited && (($subscriptionStats['is_free_plan'] ?? true) || $appsRaw <= 0);
        $appsExhausted = !$isUnlimited && $appsRaw <= 0;
    @endphp
    @if($showBanner)
    <div class="mb-6 rounded-xl overflow-hidden dash-anim-1">
        @if($appsExhausted)
        <div class="bg-red-50 border border-red-200 px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-800">You've used all your applications this month</p>
                    <p class="text-xs text-red-600 mt-0.5">Upgrade to Basic (&#8377;499/mo) for 50 applications or Pro (&#8377;999/mo) for unlimited</p>
                </div>
            </div>
            <a href="{{ route('pricing') }}" class="flex-shrink-0 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap">
                Upgrade Now &rarr;
            </a>
        </div>
        @else
        <div class="banner-gradient px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3" style="border-radius:0.75rem;">
            <div class="text-white">
                <p class="text-sm font-semibold">&#9889; Unlock your full potential with StudAI Pro</p>
                <p class="text-xs text-blue-100 mt-0.5">Get unlimited applications, AI cover letters, interview prep &amp; career coaching</p>
            </div>
            <a href="{{ route('pricing') }}" class="flex-shrink-0 px-4 py-2 bg-white text-google-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-50 transition-colors whitespace-nowrap" style="box-shadow: none">
                See Plans &rarr;
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- Welcome Header --}}
    <div class="mb-8 dash-anim-2">
        <h1 class="text-2xl font-semibold text-ink-primary">
            Welcome back, <span style="background:#2D6CDF;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">{{ explode(' ', $user->name)[0] }}</span> &#128075;
        </h1>
        <p class="mt-1 text-sm text-ink-secondary">Here's what's happening with your job search</p>
    </div>

    {{-- Stats Overview Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">
        {{-- Applications Remaining --}}
        @php
            $appsRaw = $subscriptionStats['applications_remaining_raw'] ?? 0;
            $appsLimit = $subscriptionStats['applications_limit'] ?? 0;
            $appsUsed = $subscriptionStats['applications_used'] ?? 0;
            $isFreePlan = $subscriptionStats['is_free_plan'] ?? true;
            $isUnlimited = $subscriptionStats['is_unlimited'] ?? false;
            $appsAtLimit = !$isUnlimited && $appsRaw <= 0;
        @endphp
        <a href="{{ route('dashboard.applications') }}" class="stat-card dash-anim-4 block rounded-2xl p-5"
           style="{{ $appsAtLimit ? 'background:#fef2f2;border:1.5px solid #fca5a5;' : 'background:#EBF2FF;border:1.5px solid #2D6CDF;' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="{{ $appsAtLimit ? 'color:#2D6CDF' : 'color:#0C2E72' }}">Applications Left</p>
                    <p class="stat-number mt-2 text-4xl font-bold" style="{{ $appsAtLimit ? 'color:#2D6CDF' : 'color:#0C2E72' }}">
                        {{ $subscriptionStats['applications_remaining'] }}
                    </p>
                    @if(!$isUnlimited && $appsLimit > 0)
                        <p class="mt-1 text-xs" style="{{ $appsAtLimit ? 'color:#2D6CDF' : 'color:#2D6CDF' }}">{{ $appsUsed }} used of {{ $appsLimit }} this month</p>
                    @else
                        <p class="mt-1 text-xs" style="color:#2D6CDF">This month</p>
                    @endif
                </div>
                <div class="stat-icon p-3 rounded-xl" style="{{ $appsAtLimit ? 'background:rgba(185, 28, 28,.15)' : 'background:rgba(20, 71, 186,.18)' }}">
                    <svg class="w-7 h-7" style="{{ $appsAtLimit ? 'color:#2D6CDF' : 'color:#1B57C4' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            @if($appsAtLimit && $isFreePlan)
                <div class="mt-3 pt-3" style="border-top:1px solid #fca5a5">
                    <span onclick="event.preventDefault();window.location='{{ route('pricing') }}'" class="text-xs font-semibold hover:underline cursor-pointer" style="color:#2D6CDF">
                        &uarr; Upgrade to apply to more jobs
                    </span>
                </div>
            @endif
        </a>

        {{-- Profile Strength --}}
        <a href="{{ route('profile.career.builder') }}" class="stat-card dash-anim-5 block rounded-2xl p-5"
           style="background:#EBF2FF;border:1.5px solid #2D6CDF;">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#1B57C4">Profile Strength</p>
                    <p class="stat-number mt-2 text-4xl font-bold" style="color:#0C2E72">{{ $profileCompletion }}<span class="text-xl font-semibold" style="color:#1B57C4">%</span></p>
                    <p class="mt-1 text-xs" style="color:#1B57C4">
                        @if($profileCompletion >= 80) Excellent &mdash; keep it up!
                        @elseif($profileCompletion >= 50) Good &mdash; almost there
                        @else Tap to complete profile
                        @endif
                    </p>
                </div>
                <div class="ml-3 flex flex-col items-end gap-2">
                    <div class="stat-icon p-3 rounded-xl" style="background:rgba(20, 71, 186,.18)">
                        <svg class="w-7 h-7" style="color:#1B57C4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="mt-3 w-full rounded-full h-1.5" style="background:rgba(20, 71, 186,.2)">
                <div class="h-1.5 rounded-full transition-all duration-700" style="width:{{ $profileCompletion }}%;background:#2D6CDF"></div>
            </div>
        </a>

        {{-- AI Credits --}}
        @php
            $creditsRaw = $subscriptionStats['ai_credits_remaining_raw'] ?? 0;
            $creditsUsedDisplay = $subscriptionStats['ai_credits_used'] ?? 0;
            $creditsLimitDisplay = $subscriptionStats['ai_credits_limit'] ?? 0;
            $creditsUnlimited = $creditsRaw === -1;
            $creditsAtLimit = !$creditsUnlimited && $creditsRaw <= 0 && $creditsLimitDisplay > 0;
        @endphp
        <a href="{{ route('dashboard.ai-credits') }}" class="stat-card dash-anim-6 block rounded-2xl p-5"
           style="{{ $creditsAtLimit ? 'background:#FFF8EC;border:1.5px solid #E37400;' : 'background:#EBF2FF;border:1.5px solid #2D6CDF;' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="{{ $creditsAtLimit ? 'color:#E37400' : 'color:#0C2E72' }}">AI Credits</p>
                    <p class="stat-number mt-2 text-4xl font-bold" style="{{ $creditsAtLimit ? 'color:#E37400' : 'color:#0C2E72' }}">
                        {{ $creditsUnlimited ? '&infin;' : $subscriptionStats['ai_credits_remaining'] }}
                    </p>
                    @if(!$creditsUnlimited && $creditsLimitDisplay > 0)
                        <p class="mt-1 text-xs" style="{{ $creditsAtLimit ? 'color:#E37400' : 'color:#1B57C4' }}">{{ $creditsUsedDisplay }} used of {{ $creditsLimitDisplay }}</p>
                    @else
                        <p class="mt-1 text-xs" style="color:#1B57C4">This month</p>
                    @endif
                </div>
                <div class="stat-icon p-3 rounded-xl" style="{{ $creditsAtLimit ? 'background:rgba(251,191,36,.25)' : 'background:rgba(20, 71, 186,.18)' }}">
                    <svg class="w-7 h-7" style="{{ $creditsAtLimit ? 'color:#E37400' : 'color:#1B57C4' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
            </div>
            @if($creditsAtLimit)
                <div class="mt-3 pt-3" style="border-top:1px solid #E37400">
                    <p class="text-xs font-semibold" style="color:#E37400">&uarr; Upgrade for more AI credits</p>
                </div>
            @endif
        </a>

        {{-- Total Applications --}}
        <a href="{{ route('dashboard.applications') }}" class="stat-card dash-anim-7 block rounded-2xl p-5"
           style="background:#EBF2FF;border:1.5px solid #2D6CDF;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#2D6CDF">Applications Sent</p>
                    <p class="stat-number mt-2 text-4xl font-bold" style="color:#0C2E72">{{ $applicationStats['total'] }}</p>
                    <p class="mt-1 text-xs" style="color:#2D6CDF">All time</p>
                </div>
                <div class="stat-icon p-3 rounded-xl" style="background:rgba(217,70,239,.18)">
                    <svg class="w-7 h-7" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- Saved Jobs --}}
        <a href="{{ route('jobs.saved') }}" class="stat-card dash-anim-8 block rounded-2xl p-5"
           style="background:#FEF2F2;border:1.5px solid #FCA5A5;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#2D6CDF">Saved Jobs</p>
                    <p class="stat-number mt-2 text-4xl font-bold" style="color:#0C2E72">{{ $savedJobsCount }}</p>
                    <p class="mt-1 text-xs" style="color:#2D6CDF">Bookmarked</p>
                </div>
                <div class="stat-icon p-3 rounded-xl" style="background:rgba(251,113,133,.2)">
                    <svg class="w-7 h-7" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content Area (Left 2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile Completion Card --}}
            @if($profileCompletion < 100)
            <div class="dash-anim-8 rounded-xl p-6 text-white" style="background:#2D6CDF;box-shadow: none">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold" style="color:#ffffff">Complete Your Profile</h3>
                        <p class="text-sm mt-1" style="color:rgba(255,255,255,.85)">{{ $profileCompletion }}% complete</p>
                    </div>
                    <div class="text-3xl font-bold" style="color:#ffffff">{{ $profileCompletion }}%</div>
                </div>
                <div class="w-full rounded-full h-2 mb-4" style="background:rgba(255,255,255,.25)">
                    <div class="rounded-full h-2 anim-progress-bar" style="width:{{ $profileCompletion }}%;background:#ffffff;transition:width 1.2s cubic-bezier(.22,1,.36,1)"></div>
                </div>
                <p class="text-blue-100 mb-4 text-sm">A complete profile gets 3x more visibility to employers.</p>
                <a href="{{ route('profile.career.builder') }}" class="inline-flex items-center px-5 py-2.5 bg-white font-medium text-sm rounded-lg transition-all hover:-translate-y-0.5 hover:shadow-lg" style="color:#2D6CDF">
                    Complete Profile
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif

            {{-- Recent Applications --}}
            <div class="dash-anim-9 secondary-card rounded-2xl overflow-hidden" style="border:1.5px solid #BFCFEE;box-shadow: none">
                <div class="px-6 py-4 flex items-center justify-between" style="background:#1B57C4;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-80" style="color:#ffffff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <h3 class="text-base font-bold" style="color:#ffffff">Recent Applications</h3>
                    </div>
                    <a href="{{ route('dashboard.applications') }}" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all" style="background:rgba(255,255,255,.18);color:#ffffff">
                        View All
                    </a>
                </div>

                @if($recentApplications->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-surface-50 rounded-xl mb-4">
                            <svg class="w-7 h-7 text-ink-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-ink-primary mb-1">No applications yet</h4>
                        <p class="text-sm text-ink-tertiary mb-5">Start applying to jobs that match your skills</p>
                        <a href="{{ route('jobs.search') }}" class="inline-flex items-center px-5 py-2.5 bg-google-blue-600 text-white font-medium text-sm rounded-lg hover:bg-google-blue-700 transition-colors">
                            Browse Jobs
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-surface-100">
                        @foreach($recentApplications as $application)
                            <div class="app-row px-6 py-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-ink-primary">
                                            {{ $application->job->title }}
                                        </h4>
                                        <p class="text-sm text-ink-secondary mt-0.5">
                                            {{ $application->job->company_name }}
                                        </p>
                                        <div class="flex items-center mt-1.5 text-xs text-ink-tertiary">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Applied {{ $application->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        @if($application->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-google-yellow-50 text-google-yellow-700">
                                                Pending
                                            </span>
                                        @elseif($application->status === 'reviewing')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-google-blue-50 text-google-blue-700">
                                                Under Review
                                            </span>
                                        @elseif($application->status === 'shortlisted')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-google-green-50 text-google-green-700">
                                                Shortlisted
                                            </span>
                                        @elseif($application->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-google-red-50 text-google-red-700">
                                                Rejected
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recommended Jobs --}}
            <div class="dash-anim-10 secondary-card rounded-2xl overflow-hidden" style="border:1.5px solid #A3D9B4;box-shadow: none">
                <div class="px-6 py-4" style="background:#1E8E3E">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-80" style="color:#ffffff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <h3 class="text-base font-bold" style="color:#ffffff">Recommended For You</h3>
                    </div>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.85)">Jobs matching your profile and preferences</p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($recommendedJobs as $job)
                        <div class="job-card-hover border border-surface-200 rounded-xl p-4">
                            <h4 class="font-medium text-sm text-ink-primary mb-1">{{ $job->title }}</h4>
                            <p class="text-xs text-ink-secondary mb-2">{{ $job->company_name }}</p>
                            <div class="flex items-center text-xs text-ink-tertiary mb-3">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $job->location }}
                                @if($job->salary_min && $job->salary_max)
                                    <span class="mx-1.5 text-surface-300">|</span>
                                    <span class="text-google-green-700 font-medium">{{ number_format($job->salary_min / 100000, 1) }}L - {{ number_format($job->salary_max / 100000, 1) }}L</span>
                                @endif
                            </div>
                            <a href="{{ route('jobs.show', $job->id) }}" class="inline-flex items-center text-xs font-medium text-google-blue-600 hover:text-google-blue-700">
                                View Details
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-sm text-ink-tertiary">
                            No recommendations available yet. Complete your profile to get personalized job matches.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar (Right 1/3) --}}
        <div class="space-y-6">
            {{-- Subscription Status Card --}}
            <div class="dash-anim-11 secondary-card rounded-2xl overflow-hidden" style="border:1.5px solid #2D6CDF;box-shadow: none">
                <div class="px-5 py-4 text-white" style="background:#1B57C4;background-size:200%;animation:dash-gradient 5s ease infinite">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-80" style="color:#ffffff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <h3 class="text-sm font-bold tracking-wide" style="color:#ffffff">Your Plan</h3>
                    </div>
                </div>
                <div class="px-5 py-5" style="background:#EBF2FF">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl font-bold" style="color:#0C2E72">{{ $subscriptionStats['plan_name'] }}</span>
                        @if(!($subscriptionStats['is_free_plan'] ?? true))
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background:#EDFAF2;color:#1E8E3E">Active</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background:#EBF2FF;color:#0C2E72">Free</span>
                        @endif
                    </div>

                    @if(!($subscriptionStats['is_free_plan'] ?? true) && $subscriptionStats['next_billing_date'])
                        <p class="text-xs text-ink-tertiary mb-4">
                            Next billing: {{ $subscriptionStats['next_billing_date']?->format('M d, Y') ?? 'N/A' }}
                        </p>
                    @endif

                    <div class="space-y-4 mb-5">
                        {{-- Applications usage bar --}}
                        @php
                            $appsRaw2 = $subscriptionStats['applications_remaining_raw'] ?? 0;
                            $appsLimit2 = $subscriptionStats['applications_limit'] ?? 0;
                            $appsUsed2 = $subscriptionStats['applications_used'] ?? 0;
                            $isUnlimited2 = $subscriptionStats['is_unlimited'] ?? false;
                            $appsPct = ($appsLimit2 > 0 && !$isUnlimited2) ? min(100, ($appsUsed2 / $appsLimit2) * 100) : 0;
                            $appsBarColor = $appsPct >= 90 ? 'bg-red-500' : ($appsPct >= 70 ? 'bg-google-yellow-500' : 'bg-google-blue-600');
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-ink-secondary">Applications Left</span>
                                <span class="font-medium text-ink-primary">
                                    @if($isUnlimited2) ∞ Unlimited
                                    @else {{ max(0, $appsRaw2) }} / {{ $appsLimit2 }}
                                    @endif
                                </span>
                            </div>
                            @if(!$isUnlimited2 && $appsLimit2 > 0)
                                <div class="w-full bg-surface-100 rounded-full h-1.5">
                                    <div class="{{ $appsBarColor }} h-1.5 rounded-full transition-all" style="width: {{ $appsPct }}%"></div>
                                </div>
                            @endif
                        </div>

                        {{-- AI Credits usage bar --}}
                        @php
                            $creditsRaw2 = $subscriptionStats['ai_credits_remaining_raw'] ?? 0;
                            $creditsLimit2 = $subscriptionStats['ai_credits_limit'] ?? 0;
                            $creditsUsed2 = $subscriptionStats['ai_credits_used'] ?? 0;
                            $creditsUnlimited2 = $creditsRaw2 === -1;
                            $creditsPct = ($creditsLimit2 > 0 && !$creditsUnlimited2) ? min(100, ($creditsUsed2 / $creditsLimit2) * 100) : 0;
                            $creditsBarColor = $creditsPct >= 90 ? 'bg-red-500' : ($creditsPct >= 70 ? 'bg-google-yellow-500' : 'bg-purple-500');
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-ink-secondary">AI Credits Left</span>
                                <span class="font-medium text-ink-primary">
                                    @if($creditsUnlimited2) ∞ Unlimited
                                    @else {{ max(0, $creditsRaw2) }} / {{ $creditsLimit2 }}
                                    @endif
                                </span>
                            </div>
                            @if(!$creditsUnlimited2 && $creditsLimit2 > 0)
                                <div class="w-full bg-surface-100 rounded-full h-1.5">
                                    <div class="{{ $creditsBarColor }} h-1.5 rounded-full transition-all" style="width: {{ $creditsPct }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($subscriptionStats['is_free_plan'] ?? true)
                        <a href="{{ route('pricing') }}" class="secondary-card block w-full text-center px-4 py-3 text-white font-bold text-sm rounded-xl transition-all" style="background:#2D6CDF;box-shadow: none">
                            &#8593; Upgrade to Pro
                        </a>
                        <p class="text-xs text-center mt-2" style="color:#2D6CDF">Get 50+ applications &amp; unlimited AI</p>
                    @else
                        <a href="{{ route('subscriptions.index') }}" class="block w-full text-center px-4 py-2.5 bg-surface-50 text-ink-secondary font-medium text-sm rounded-lg hover:bg-surface-100 transition-colors">
                            Manage Subscription
                        </a>
                    @endif
                </div>
            </div>

            {{-- Application Status Breakdown --}}
            <div class="dash-anim-12 secondary-card rounded-2xl overflow-hidden" style="border:1.5px solid #E37400;box-shadow: none">
                <div class="px-5 py-4 text-white" style="background:#E37400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-90" style="color:#ffffff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <h3 class="text-sm font-bold tracking-wide" style="color:#ffffff">Application Status</h3>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-2" style="background:#FFF8EC">
                    <div class="status-row flex items-center justify-between p-2.5 rounded-xl" style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.25)">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full" style="background:#E37400;box-shadow: none"></div>
                            <span class="text-sm font-medium" style="color:#E37400">Pending</span>
                        </div>
                        <span class="text-sm font-bold px-2.5 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400">{{ $applicationStats['pending'] }}</span>
                    </div>
                    <div class="status-row flex items-center justify-between p-2.5 rounded-xl" style="background:rgba(20, 71, 186,.1);border:1px solid rgba(20, 71, 186,.2)">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full" style="background:#2D6CDF;box-shadow: none"></div>
                            <span class="text-sm font-medium" style="color:#0C2E72">Under Review</span>
                        </div>
                        <span class="text-sm font-bold px-2.5 py-0.5 rounded-full" style="background:#EBF2FF;color:#0C2E72">{{ $applicationStats['reviewing'] }}</span>
                    </div>
                    <div class="status-row flex items-center justify-between p-2.5 rounded-xl" style="background:rgba(15, 107, 49,.1);border:1px solid rgba(15, 107, 49,.2)">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full" style="background:#1E8E3E;box-shadow: none"></div>
                            <span class="text-sm font-medium" style="color:#1E8E3E">Shortlisted</span>
                        </div>
                        <span class="text-sm font-bold px-2.5 py-0.5 rounded-full" style="background:#EDFAF2;color:#1E8E3E">{{ $applicationStats['shortlisted'] }}</span>
                    </div>
                    <div class="status-row flex items-center justify-between p-2.5 rounded-xl" style="background:rgba(185, 28, 28,.09);border:1px solid rgba(185, 28, 28,.18)">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full" style="background:#2D6CDF;box-shadow: none"></div>
                            <span class="text-sm font-medium" style="color:#2D6CDF">Rejected</span>
                        </div>
                        <span class="text-sm font-bold px-2.5 py-0.5 rounded-full" style="background:#FEF2F2;color:#2D6CDF">{{ $applicationStats['rejected'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="dash-anim-12 secondary-card rounded-2xl overflow-hidden" style="border:1.5px solid #A3D9B4;box-shadow: none">
                <div class="px-5 py-4 text-white" style="background:#1E8E3E">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-90" style="color:#ffffff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <h3 class="text-sm font-bold tracking-wide" style="color:#ffffff">Quick Actions</h3>
                    </div>
                </div>
                <div class="p-3 space-y-1.5" style="background:#EDFAF2">
                    <a href="{{ route('jobs.search') }}" class="quick-action flex items-center p-3 rounded-xl transition-all" style="border:1px solid transparent">
                        <div class="p-2 rounded-xl mr-3 flex-shrink-0" style="background:#EBF2FF">
                            <svg class="w-4 h-4" style="color:#1B57C4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold" style="color:#0C2E72">Search Jobs</span>
                        <svg class="w-4 h-4 ml-auto" style="color:#1E8E3E" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('profile.career.builder') }}" class="quick-action flex items-center p-3 rounded-xl transition-all" style="border:1px solid transparent">
                        <div class="p-2 rounded-xl mr-3 flex-shrink-0" style="background:#EBF2FF">
                            <svg class="w-4 h-4" style="color:#1B57C4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold" style="color:#0C2E72">Edit Profile</span>
                        <svg class="w-4 h-4 ml-auto" style="color:#1E8E3E" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('jobs.saved') }}" class="quick-action flex items-center p-3 rounded-xl transition-all" style="border:1px solid transparent">
                        <div class="p-2 rounded-xl mr-3 flex-shrink-0" style="background:#FFF8EC">
                            <svg class="w-4 h-4" style="color:#E37400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold" style="color:#0C2E72">Saved Jobs</span>
                        <svg class="w-4 h-4 ml-auto" style="color:#1E8E3E" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="quick-action flex items-center p-3 rounded-xl transition-all" style="border:1px solid transparent">
                        <div class="p-2 rounded-xl mr-3 flex-shrink-0" style="background:#FEF2F2">
                            <svg class="w-4 h-4" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold" style="color:#0C2E72">Settings</span>
                        <svg class="w-4 h-4 ml-auto" style="color:#1E8E3E" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
