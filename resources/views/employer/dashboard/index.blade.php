@extends('layouts.dashboard')

@section('title', 'Employer Dashboard')
@section('page-title', 'Employer Dashboard')

@push('styles')
<style>
/* ------------------------------------------
   LIGHT COLORFUL EMPLOYER DASHBOARD
------------------------------------------ */

/* Page wrapper */
.emp-dash {
    padding: 1.5rem;
    background: #EBF2FF;
    min-height: 100%;
}

/* -- Entrance animations -- */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(18px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes popIn {
    from { opacity:0; transform:scale(.94); }
    to   { opacity:1; transform:scale(1); }
}
@keyframes shimmer {
    0%   { background-position: -200% center; }
    100% { background-position:  200% center; }
}
@keyframes float {
    0%,100% { transform:translateY(0); }
    50%      { transform:translateY(-6px); }
}
@keyframes pulse-ring {
    0%   { transform:scale(.8);  opacity:.8; }
    100% { transform:scale(1.8); opacity:0; }
}
@keyframes gradFlow {
    0%   { background-position:0% 50%; }
    50%  { background-position:100% 50%; }
    100% { background-position:0% 50%; }
}

.anim-1 { animation: fadeUp .45s ease both; animation-delay:.05s; }
.anim-2 { animation: fadeUp .45s ease both; animation-delay:.12s; }
.anim-3 { animation: fadeUp .45s ease both; animation-delay:.19s; }
.anim-4 { animation: fadeUp .45s ease both; animation-delay:.26s; }
.anim-5 { animation: fadeUp .45s ease both; animation-delay:.33s; }
.anim-6 { animation: fadeUp .45s ease both; animation-delay:.4s; }

/* -- HERO -- */
.emp-hero {
    border-radius: 1.5rem;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    background: #2D6CDF;
    background-size: 250% 250%;
    animation: gradFlow 8s ease infinite;
    box-shadow: none;
    color: #fff;
}
.emp-hero::before {
    content:'';
    position:absolute; inset:0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%238b5cf6' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events:none;
}
.emp-hero::after {
    content:'';
    position:absolute; top:-60px; right:-60px;
    width:200px; height:200px; border-radius:50%;
    background: rgba(139,92,246,.08);
    animation: float 5s ease-in-out infinite;
}
.emp-hero-blob2 {
    position:absolute; bottom:-40px; left:30%;
    width:140px; height:140px; border-radius:50%;
    background: rgba(99,102,241,.06);
    animation: float 7s ease-in-out infinite .5s;
    pointer-events:none;
}

/* Funnel bars */
.funnel-bar { border-radius:.4rem .4rem 0 0; transition:height .6s cubic-bezier(.22,.68,0,1.2); }

/* -- STAT CARDS -- */
.emp-stat {
    border-radius: 1.25rem;
    padding: 1.4rem;
    position: relative;
    overflow: hidden;
    border: none;
    box-shadow: none;
    transition: transform .25s cubic-bezier(.22,.68,0,1.2), box-shadow .25s;
    animation: popIn .4s ease both;
    color: #475569;
}
.emp-stat:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: none;
}

/* Card backgrounds — solid light, on-brand */
.stat-blue   { background: #EBF2FF; color:#2D6CDF; }
.stat-violet { background: #EBF2FF; color:#2D6CDF; }
.stat-amber  { background: #FFF8EC; color:#E37400; }
.stat-rose   { background: #EBF2FF; color:#2D6CDF; }

/* Soft sheen overlay */
.emp-stat::before {
    content:'';
    position:absolute; inset:0;
    background: rgba(255,255,255,.18);
    pointer-events:none;
}

/* Decorative circles */
.emp-stat::after {
    content:'';
    position:absolute; bottom:-25px; right:-25px;
    width:100px; height:100px; border-radius:50%;
    background: rgba(255,255,255,.5);
    animation: float 5s ease-in-out infinite;
}
.emp-stat .stat-circle2 {
    position:absolute; top:-20px; right:40px;
    width:60px; height:60px; border-radius:50%;
    background: rgba(255,255,255,.35);
    pointer-events:none;
    animation: float 7s ease-in-out infinite .8s;
}

.emp-stat .stat-icon {
    width:2.75rem; height:2.75rem; border-radius:.875rem;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background: rgba(255,255,255,.75);
    color: currentColor;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.emp-stat .stat-num {
    font-size:2.5rem; font-weight:900; line-height:1; letter-spacing:-.04em;
    color: #1e293b;
    text-shadow: none;
    -webkit-text-fill-color: #1e293b;
}

.emp-stat .stat-lbl { font-size:.8rem; color:#64748b; margin-top:.2rem; font-weight:500; }
.emp-stat .stat-badge {
    font-size:.7rem; font-weight:700; padding:.25rem .65rem; border-radius:9999px;
    background: rgba(255,255,255,.7); color:currentColor;
    border:1px solid rgba(0,0,0,.05);
}

/* Progress/dot bars on pastel cards */
.stat-track-bg { background: rgba(0,0,0,.06) !important; }
.stat-track-fill { background: currentColor !important; }
.stat-dots-filled { background: currentColor !important; }
.stat-dots-empty  { background: rgba(0,0,0,.06) !important; }
.stat-action-link { color: currentColor !important; font-weight:700; text-decoration:none; }
.stat-action-link:hover { opacity:.8; }

/* -- MAIN CARD -- */
.emp-card {
    background: #EBF2FF;
    border: 1.5px solid rgba(20, 71, 186,.15);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: none;
}
.emp-card-header {
    background: #EBF2FF;
    border-bottom: 1px solid rgba(20, 71, 186,.1);
    padding: 1rem 1.5rem;
    display:flex; align-items:center; justify-content:space-between;
}
.emp-card-header h2 { font-size:.925rem; font-weight:700; color:#0C0C0C; }
.emp-card-header a  { font-size:.8rem; font-weight:600; color:#2D6CDF; transition:color .15s; text-decoration:none; }
.emp-card-header a:hover { color:#2D6CDF; }

/* -- KANBAN -- */
.kanban-col-wrap { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; }
@media (min-width:640px) { .kanban-col-wrap { grid-template-columns:repeat(4,1fr); } }
.kanban-col { border-radius:.875rem; overflow:hidden; }
.kanban-col-head {
    padding:.625rem .875rem;
    display:flex; align-items:center; justify-content:space-between;
    font-size:.72rem; font-weight:700; letter-spacing:.03em;
}
.kanban-col.col-blue   { background:#EBF2FF; border:1.5px solid rgba(20, 71, 186,.2); }
.kanban-col.col-amber  { background:#FFF8EC; border:1.5px solid rgba(146, 80, 10,.2); }
.kanban-col.col-cyan   { background:#EDFAF2; border:1.5px solid rgba(6,182,212,.2); }
.kanban-col.col-rose   { background:#FEF2F2; border:1.5px solid rgba(244,63,94,.2); }
.kanban-col.col-blue  .kanban-col-head { background:#EBF2FF; color:#2D6CDF; }
.kanban-col.col-amber .kanban-col-head { background:#FFF8EC; color:#E37400; }
.kanban-col.col-cyan  .kanban-col-head { background:#EBF2FF; color:#2D6CDF; }
.kanban-col.col-rose  .kanban-col-head { background:#FEF2F2; color:#2D6CDF; }
.kanban-col-body { padding:.5rem; }
.kanban-candidate {
    background: #EBF2FF;
    border:1px solid rgba(20, 71, 186,.13);
    border-radius:.5rem; padding:.5rem .65rem;
    font-size:.72rem; margin-bottom:.4rem;
    box-shadow: none;
    transition: box-shadow .2s, transform .2s;
}
.kanban-candidate:hover { box-shadow: none; transform:translateY(-2px); }
.kanban-candidate .cn { font-weight:600; color:#0C0C0C; }
.kanban-candidate .ca { color:#A8A8A8; margin-top:.15rem; }
.kanban-more { text-align:center; font-size:.7rem; color:#2D6CDF; font-weight:600; padding:.3rem 0; }

/* -- RECENT APPS -- */
.app-row {
    display:flex; align-items:center; gap:1rem;
    padding:.875rem 1.5rem;
    border-bottom:1px solid rgba(20, 71, 186,.06);
    transition:background .15s;
}
.app-row:last-child { border-bottom:none; }
.app-row:hover { background:rgba(20, 71, 186,.04); }
.app-name { font-size:.875rem; font-weight:600; color:#0C0C0C; }
.app-meta { font-size:.72rem; color:#A8A8A8; }
.app-link { font-size:.75rem; font-weight:700; color:#2D6CDF; white-space:nowrap; text-decoration:none; transition:color .15s; }
.app-link:hover { color:#2D6CDF; }
.app-badge { font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:9999px; white-space:nowrap; }
.badge-hired       { background:#EDFAF2; color:#1E8E3E; border:1px solid rgba(15, 107, 49,.2); }
.badge-rejected    { background:#FEF2F2; color:#2D6CDF; border:1px solid rgba(244,63,94,.2); }
.badge-shortlisted { background:#EBF2FF; color:#2D6CDF; border:1px solid rgba(20, 71, 186,.2); }
.badge-interviewed { background:#EBF2FF; color:#2D6CDF; border:1px solid rgba(6,182,212,.2); }
.badge-pending     { background:#FFF8EC; color:#E37400; border:1px solid rgba(146, 80, 10,.2); }

/* -- SCOUT PANEL -- */
.scout-panel {
    background: #EBF2FF;
    border: 1.5px solid rgba(20, 71, 186,.18);
    border-radius: 1.25rem;
    padding: 1.25rem;
    position:relative; overflow:hidden;
    box-shadow: none;
}
.scout-panel::before {
    content:'';
    position:absolute; top:-30px; right:-30px;
    width:130px; height:130px; border-radius:50%;
    background: rgba(20, 71, 186,.15);
    pointer-events:none;
}
.scout-panel::after {
    content:'';
    position:absolute; bottom:-40px; left:-20px;
    width:100px; height:100px; border-radius:50%;
    background: rgba(20, 71, 186,.1);
    pointer-events:none;
}
.scout-skill-track { height:7px; background:rgba(20, 71, 186,.1); border-radius:9999px; overflow:hidden; }
.scout-skill-fill  {
    height:100%; border-radius:9999px;
    background:#2D6CDF;
    background-size:200%;
    animation: shimmer 3s linear infinite;
    transition:width .9s cubic-bezier(.22,.68,0,1.2);
}

/* -- SUB CARDS -- */
.emp-subcard {
    background: #EBF2FF;
    border: 1.5px solid rgba(20, 71, 186,.13);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: none;
}
.emp-subcard-p { padding: 1.25rem; }

/* -- JOB ITEMS -- */
.job-item { display:flex; align-items:flex-start; gap:.75rem; padding:.625rem 0; }
.job-item + .job-item { border-top:1px solid rgba(20, 71, 186,.07); }
.job-letter {
    width:2rem; height:2rem; border-radius:.5rem; flex-shrink:0;
    background: #2D6CDF;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.75rem; font-weight:700;
    box-shadow: none;
}
.job-title { font-size:.875rem; font-weight:600; color:#0C0C0C; display:block; text-decoration:none; transition:color .15s; }
.job-title:hover { color:#2D6CDF; }
.job-meta { font-size:.7rem; color:#A8A8A8; }

/* -- ACTION ITEMS -- */
.action-item {
    display:flex; align-items:center; gap:.65rem;
    padding:.625rem .875rem; border-radius:.75rem; border:1.5px solid;
    font-size:.78rem; font-weight:500; text-decoration:none;
    transition: transform .2s cubic-bezier(.22,.68,0,1.2), box-shadow .2s;
}
.action-item:hover { transform:translateX(4px); }
.action-blue  { background:#EBF2FF; border-color:rgba(20, 71, 186,.2);  color:#2D6CDF; box-shadow: none; }
.action-cyan  { background:#EBF2FF; border-color:rgba(6,182,212,.2);   color:#1B57C4; box-shadow: none; }
.action-amber { background:#FFF8EC; border-color:rgba(146, 80, 10,.2);  color:#E37400; box-shadow: none; }
.action-rose  { background:#FEF2F2; border-color:rgba(244,63,94,.2);   color:#2D6CDF; box-shadow: none; }
.action-dot { width:.5rem; height:.5rem; border-radius:50%; flex-shrink:0; }
.action-blue  .action-dot { background:#2D6CDF; }
.action-cyan  .action-dot { background:#2D6CDF; }
.action-amber .action-dot { background:#E37400; }
.action-rose  .action-dot { background:#2D6CDF; }

/* Ping indicator */
.ping-dot { position:relative; display:inline-flex; width:.625rem; height:.625rem; }
.ping-dot-ring {
    position:absolute; inset:0;
    border-radius:50%; animation:pulse-ring .9s cubic-bezier(0,0,.2,1) infinite;
}
.ping-dot-inner { position:relative; display:inline-flex; width:.625rem; height:.625rem; border-radius:50%; }
</style>
@endpush

@section('content')
<div class="emp-dash">
    <div class="space-y-5">

    {{-- HERO -- anim-1 --}}
    <div class="emp-hero anim-1">
        <div class="emp-hero-blob2"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-sm font-semibold mb-1" style="color:#8b5cf6">{{ now()->format('l, F j') }}</p>
                <h1 class="text-2xl font-black" style="color:#1e293b">
                    Welcome back, {{ $company->name ?? 'Employer' }}!
                </h1>
                <p class="text-sm mt-1" style="color:#64748b">Here's your hiring pipeline at a glance</p>
            </div>

            {{-- Funnel bars --}}
            <div class="flex items-end gap-3">
                @php $funnelData = [
                    ['label'=>'Applied',   'count'=>$totalApplications ?? 4,                         'bg'=>'rgba(139,92,246,.55)',  'w'=>'5rem'],
                    ['label'=>'Screening', 'count'=>round(($totalApplications??4)*.6+1),              'bg'=>'rgba(139,92,246,.4)',   'w'=>'3.75rem'],
                    ['label'=>'Interview', 'count'=>round(($totalApplications??4)*.3+0.5),            'bg'=>'rgba(139,92,246,.28)',  'w'=>'2.5rem'],
                    ['label'=>'Offer',     'count'=>round(($totalApplications??4)*.1),                'bg'=>'rgba(139,92,246,.18)',  'w'=>'1.5rem'],
                ]; @endphp
                @foreach($funnelData as $f)
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs font-bold" style="color:#475569">{{ $f['count'] }}</span>
                    <div class="funnel-bar rounded-t-lg"
                         style="width:{{ $f['w'] }};height:{{ min(80, 18+$f['count']*8) }}px;background:{{ $f['bg'] }};border-radius:.375rem .375rem 0 0;"></div>
                    <span class="text-[10px]" style="color:#94a3b8">{{ $f['label'] }}</span>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3">
                <a href="{{ route('employer.jobs.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 font-bold rounded-xl text-sm transition-all hover:-translate-y-0.5 hover:shadow-xl"
                   style="background:#fff;color:#2D6CDF;box-shadow: none;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Post New Job
                </a>
                <a href="{{ route('employer.applicants.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 font-semibold rounded-xl text-sm transition-all hover:-translate-y-0.5"
                   style="background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.4);">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    All Applicants
                </a>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Jobs --}}
        <div class="emp-stat stat-blue anim-2">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="stat-badge">{{ $activeJobs ?? 0 }} Active</span>
            </div>
            <div class="stat-num">{{ $totalJobs ?? 0 }}</div>
            <div class="stat-lbl">Total Jobs</div>
            <div class="mt-3 h-1.5 rounded-full" style="background:#EBF2FF">
                <div class="h-full rounded-full" style="width:{{ min(100,($activeJobs??0)/max(1,$totalJobs??1)*100) }}%;background:#2D6CDF;transition:width 1s ease;"></div>
            </div>
        </div>

        {{-- Applications --}}
        <div class="emp-stat stat-violet anim-3">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="stat-badge">+{{ $newApplications ?? 0 }} this week</span>
            </div>
            <div class="stat-num">{{ $totalApplications ?? 0 }}</div>
            <div class="stat-lbl">Total Applications</div>
            <div class="mt-3 flex gap-0.5">
                @for($i=0;$i<10;$i++)
                <div class="flex-1 h-1.5 rounded-full"
                     style="background:{{ $i < min(10,($totalApplications??0)/5) ? '#2D6CDF' : 'rgba(20, 71, 186,.12)' }};transition:background .3s;"></div>
                @endfor
            </div>
        </div>

        {{-- Pending Review --}}
        <div class="emp-stat stat-amber anim-4">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="ping-dot" style="margin-top:.25rem;">
                    <span class="ping-dot-ring" style="background:rgba(146, 80, 10,.4);"></span>
                    <span class="ping-dot-inner" style="background:#E37400;"></span>
                </span>
            </div>
            <div class="stat-num">{{ $applicationsByStatus['pending'] ?? 0 }}</div>
            <div class="stat-lbl">Pending Review</div>
            <a href="{{ route('employer.applicants.index', ['status' => 'pending']) }}"
               class="mt-2 text-xs font-bold inline-block transition-all hover:translate-x-1"
               style="color:#E37400;text-decoration:none;">Review Now &#8594;</a>
        </div>

        {{-- Shortlisted --}}
        <div class="emp-stat stat-rose anim-5">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="stat-badge">S.C.O.U.T.</span>
            </div>
            <div class="stat-num">{{ $applicationsByStatus['shortlisted'] ?? 0 }}</div>
            <div class="stat-lbl">Shortlisted</div>
            <a href="{{ route('employer.applicants.index', ['status' => 'shortlisted']) }}"
               class="mt-2 text-xs font-bold inline-block transition-all hover:translate-x-1"
               style="color:#2D6CDF;text-decoration:none;">View All &#8594;</a>
        </div>
    </div>

    {{-- KANBAN + SIDEBAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-6">

        {{-- Left: Kanban + Recent Apps --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Kanban --}}
            <div class="emp-card">
                <div class="emp-card-header">
                    <h2>Hiring Pipeline Kanban</h2>
                    <a href="{{ route('employer.applicants.index') }}">Full view &#8594;</a>
                </div>
                <div class="p-5">
                    <div class="kanban-col-wrap">
                        @php $kanban = [
                            ['label'=>'Applied',    'count'=>$applicationsByStatus['pending']??0,    'cls'=>'col-blue'],
                            ['label'=>'Screening',  'count'=>$applicationsByStatus['reviewing']??0,  'cls'=>'col-amber'],
                            ['label'=>'Interview',  'count'=>$applicationsByStatus['interviewed']??0, 'cls'=>'col-cyan'],
                            ['label'=>'Shortlisted','count'=>$applicationsByStatus['shortlisted']??0, 'cls'=>'col-rose'],
                        ]; @endphp
                        @foreach($kanban as $col)
                        <div class="kanban-col {{ $col['cls'] }}">
                            <div class="kanban-col-head">
                                <span>{{ $col['label'] }}</span>
                                <span class="font-black">{{ $col['count'] }}</span>
                            </div>
                            <div class="kanban-col-body">
                                @if($col['count'] === 0)
                                <div class="text-center py-4 text-xs" style="color:#A8A8A8">No candidates</div>
                                @else
                                @php
                                    $statusMap = ['Applied'=>'pending','Screening'=>'reviewing','Interview'=>'interviewed','Shortlisted'=>'shortlisted'];
                                    $colStatus = $statusMap[$col['label']] ?? 'pending';
                                    $colApps = $recentApplications->where('status', $colStatus)->take(3);
                                @endphp
                                @foreach($colApps as $app)
                                <div class="kanban-candidate">
                                    <div class="cn">{{ $app->applicant->name ?? 'Applicant' }}</div>
                                    <div class="ca">{{ $app->created_at->diffForHumans() }}</div>
                                </div>
                                @endforeach
                                @if($col['count'] > 3)
                                <div class="kanban-more">+{{ $col['count']-3 }} more</div>
                                @endif
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Recent Applications --}}
            <div class="emp-card">
                <div class="emp-card-header">
                    <h2>Recent Applications</h2>
                    <a href="{{ route('employer.applicants.index') }}">View All &#8594;</a>
                </div>
                @if(isset($recentApplications) && $recentApplications->isEmpty())
                <div class="py-12 text-center">
                    <div style="width:3.5rem;height:3.5rem;border-radius:1rem;background:#EBF2FF;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <svg class="w-7 h-7" style="color:#BFCFEE" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">No applications yet</p>
                    <p class="text-xs mt-1 text-gray-400">Post a job to start receiving applications</p>
                </div>
                @else
                @foreach($recentApplications ?? [] as $app)
                @php
                    $st = $app->status ?? 'pending';
                    $bCls = match($st) {
                        'hired'       => 'badge-hired',
                        'rejected'    => 'badge-rejected',
                        'shortlisted' => 'badge-shortlisted',
                        'interviewed' => 'badge-interviewed',
                        default       => 'badge-pending',
                    };
                @endphp
                <div class="app-row">
                    <x-studai.avatar :name="$app->applicant->name ?? 'A'" size="sm" />
                    <div class="flex-1 min-w-0">
                        <div class="app-name">{{ $app->applicant->name ?? 'Applicant' }}</div>
                        <div class="app-meta">{{ $app->job->title ?? 'Job' }} &middot; {{ $app->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="app-badge {{ $bCls }}">{{ ucfirst($st) }}</span>
                    <a href="{{ route('employer.applicants.show', $app->id) }}" class="app-link">View &#8594;</a>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- S.C.O.U.T. --}}
            <div class="scout-panel">
                <div class="flex items-center gap-3 mb-4 relative z-10">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:#EBF2FF;box-shadow: none;">
                        <svg class="w-5 h-5" style="color:#2D6CDF" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm" style="color:#0C0C0C">S.C.O.U.T. AI</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="ping-dot">
                                <span class="ping-dot-ring" style="background:rgba(15, 107, 49,.45);"></span>
                                <span class="ping-dot-inner" style="background:#1E8E3E;"></span>
                            </span>
                            <span class="text-xs font-medium" style="color:#737373">Active &middot; Matching candidates</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mb-4 relative z-10">
                    @if($totalApplications > 0)
                    @php
                        $scoutSkills = [
                            'Culture Fit'       => $applicationsByStatus ? round((($applicationsByStatus['hired'] ?? 0) + ($applicationsByStatus['shortlisted'] ?? 0)) / max(1, $totalApplications) * 100) : 0,
                            'Applications'      => min(100, $totalApplications * 5),
                            'Shortlist Rate'    => $totalApplications > 0 ? round(($applicationsByStatus['shortlisted'] ?? 0) / $totalApplications * 100) : 0,
                            'Interview Rate'    => $totalApplications > 0 ? round(($applicationsByStatus['interviewed'] ?? 0) / $totalApplications * 100) : 0,
                        ];
                    @endphp
                    @foreach($scoutSkills as $skill => $pct)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-500" style="color:#737373">{{ $skill }}</span>
                            <span class="font-bold" style="color:#2D6CDF">{{ $pct }}%</span>
                        </div>
                        <div class="scout-skill-track">
                            <div class="scout-skill-fill" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-xs text-center py-3" style="color:#A8A8A8">Metrics will appear once candidates apply to your jobs.</p>
                    @endif
                </div>

                <a href="{{ route('employer.scout.dashboard') }}"
                   class="flex items-center justify-center gap-1.5 text-sm font-bold relative z-10 transition-all hover:-translate-y-0.5"
                   style="color:#2D6CDF;text-decoration:none;">
                    Full S.C.O.U.T. Report
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Active Jobs --}}
            <div class="emp-subcard emp-subcard-p">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold" style="color:#0C0C0C">Active Job Postings</h3>
                    <a href="{{ route('employer.jobs.create') }}"
                       class="text-xs font-bold px-2.5 py-1 rounded-full transition-all hover:-translate-y-0.5"
                       style="background:#2D6CDF;color:#fff;text-decoration:none;box-shadow: none;">+ Post</a>
                </div>
                @if(isset($recentJobs) && $recentJobs->isEmpty())
                <p class="text-xs text-center py-4" style="color:#A8A8A8">No active job postings</p>
                @else
                <div>
                    @foreach($recentJobs ?? [] as $job)
                    <div class="job-item">
                        <div class="job-letter">{{ strtoupper(substr($job->title??'J',0,1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('employer.jobs.show', $job->id) }}" class="job-title line-clamp-1">{{ $job->title ?? 'Job' }}</a>
                            <div class="job-meta">{{ $job->applications_count ?? 0 }} applicants &middot; {{ $job->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Action Items --}}
            <div class="emp-subcard emp-subcard-p">
                <h3 class="text-sm font-bold mb-3" style="color:#0C0C0C">Action Items</h3>
                <div class="space-y-2">
                    @php
                        $actions = [];
                        $pendingCount = $applicationsByStatus['pending'] ?? 0;
                        $shortlistedCount = $applicationsByStatus['shortlisted'] ?? 0;
                        $interviewedCount = $applicationsByStatus['interviewed'] ?? 0;
                        if ($pendingCount > 0)
                            $actions[] = ['text' => "Review {$pendingCount} pending application" . ($pendingCount > 1 ? 's' : ''), 'cls' => 'action-blue'];
                        if ($shortlistedCount > 0)
                            $actions[] = ['text' => "Schedule interviews for {$shortlistedCount} shortlisted candidate" . ($shortlistedCount > 1 ? 's' : ''), 'cls' => 'action-cyan'];
                        if ($interviewedCount > 0)
                            $actions[] = ['text' => "Make decisions on {$interviewedCount} interviewed candidate" . ($interviewedCount > 1 ? 's' : ''), 'cls' => 'action-amber'];
                        if ($totalJobs === 0)
                            $actions[] = ['text' => 'Post your first job to start receiving applications', 'cls' => 'action-rose'];
                        if (empty($actions))
                            $actions[] = ['text' => 'All caught up! Post a job to get started.', 'cls' => 'action-blue'];
                    @endphp
                    @foreach($actions as $a)
                    <div class="action-item {{ $a['cls'] }}">
                        <span class="action-dot"></span>
                        {{ $a['text'] }}
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate skill bars from 0
    document.querySelectorAll('.scout-skill-fill').forEach(el => {
        const w = el.style.width;
        el.style.width = '0';
        requestAnimationFrame(() => {
            setTimeout(() => { el.style.width = w; }, 400);
        });
    });

    // Animate stat numbers (count-up)
    document.querySelectorAll('.stat-num').forEach(el => {
        const target = parseInt(el.textContent.trim()) || 0;
        if (target === 0) return;
        let current = 0;
        const duration = 900;
        const step = target / (duration / 16);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = Math.floor(current);
            if (current >= target) clearInterval(timer);
        }, 16);
    });
});
</script>
@endpush

@endsection
