@extends('layouts.dashboard')

@section('title', 'Job Postings')
@section('page-title', 'Job Postings')
@section('page-description', 'Manage your job listings and applications')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════
   JOB POSTINGS — LIGHT GRADIENT REDESIGN
═══════════════════════════════════════ */
.jobs-page {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    padding: 1.75rem;
    background: linear-gradient(160deg, #eef2ff 0%, #f5f3ff 30%, #fdf4ff 60%, #f0fdf4 100%);
    min-height: 100%;
}

/* ─ Animations ─ */
@keyframes fadeSlideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes gradientShift {
    0%   { background-position:0% 50%; }
    50%  { background-position:100% 50%; }
    100% { background-position:0% 50%; }
}
@keyframes floatY {
    0%,100% { transform:translateY(0) rotate(0deg); }
    50%      { transform:translateY(-10px) rotate(3deg); }
}
@keyframes floatY2 {
    0%,100% { transform:translateY(0) rotate(0deg); }
    50%      { transform:translateY(-14px) rotate(-4deg); }
}
@keyframes sparkle {
    0%,100% { opacity:.6; transform:scale(1); }
    50%      { opacity:1; transform:scale(1.2); }
}
@keyframes shimmerSlide {
    0%   { background-position:-200% center; }
    100% { background-position: 200% center; }
}
@keyframes pulseDot {
    0%,100% { transform:scale(.8); opacity:.6; }
    50%      { transform:scale(1.4); opacity:1; }
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(16px) scale(.98); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}

.j-a1 { animation:fadeSlideUp .4s ease both .05s; }
.j-a2 { animation:fadeSlideUp .4s ease both .12s; }
.j-a3 { animation:fadeSlideUp .4s ease both .2s; }

/* ─ HERO ─ */
.jobs-hero {
    border-radius: 1.75rem;
    padding: 2.25rem 2rem;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 35%, #a855f7 60%, #ec4899 100%);
    background-size: 250% 250%;
    animation: gradientShift 8s ease infinite;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 50px rgba(99,102,241,.3), 0 2px 12px rgba(0,0,0,.08);
    color: #fff;
}
.jobs-hero::before {
    content:'';
    position:absolute; inset:0;
    background:
        radial-gradient(ellipse at 15% 50%, rgba(255,255,255,.15) 0%, transparent 50%),
        radial-gradient(ellipse at 85% 20%, rgba(255,255,255,.1) 0%, transparent 40%);
    pointer-events:none;
}
.hero-orb1 {
    position:absolute; top:-40px; right:-40px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.1);
    animation: floatY 6s ease-in-out infinite;
}
.hero-orb2 {
    position:absolute; bottom:-30px; right:25%;
    width:100px; height:100px; border-radius:50%;
    background:rgba(255,255,255,.08);
    animation: floatY2 8s ease-in-out infinite .5s;
}
.hero-orb3 {
    position:absolute; top:50%; left:42%;
    width:60px; height:60px; border-radius:50%;
    background:rgba(255,255,255,.06);
    animation: floatY 5s ease-in-out infinite 1s;
}
.hero-title {
    font-size:1.75rem; font-weight:900; color:#fff;
    letter-spacing:-.03em; line-height:1.1;
    text-shadow:0 2px 10px rgba(0,0,0,.1);
}
.hero-sub { font-size:.9rem; color:rgba(255,255,255,.8); margin-top:.35rem; font-weight:500; }
.hero-icon-wrap {
    width:3.5rem; height:3.5rem; border-radius:1.1rem; flex-shrink:0;
    background:rgba(255,255,255,.2); backdrop-filter:blur(8px);
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 4px 16px rgba(0,0,0,.12);
    border:1.5px solid rgba(255,255,255,.3);
}
.hero-btn-primary {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.75rem 1.5rem;
    background:#fff; color:#6366f1;
    font-size:.9rem; font-weight:800;
    border-radius:1rem; text-decoration:none;
    box-shadow:0 4px 16px rgba(0,0,0,.15);
    transition:transform .2s, box-shadow .2s;
    border:none;
}
.hero-btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(0,0,0,.2); }

/* ─ STAT CHIPS in hero ─ */
.hero-chips { display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1.25rem; }
.hero-chip {
    padding:.35rem 1rem; border-radius:9999px;
    background:rgba(255,255,255,.18); backdrop-filter:blur(6px);
    border:1px solid rgba(255,255,255,.3);
    font-size:.78rem; font-weight:700; color:#fff;
    display:flex; align-items:center; gap:.45rem;
}
.hero-chip-dot { width:.45rem; height:.45rem; border-radius:50%; animation:pulseDot 2s ease-in-out infinite; }

/* ─ FILTER CARD ─ */
.jobs-filter {
    background:#fff;
    border-radius:1.25rem;
    border:1px solid rgba(99,102,241,.1);
    box-shadow:0 2px 16px rgba(99,102,241,.07);
    padding:1.25rem 1.5rem;
}
.jobs-filter input, .jobs-filter select {
    width:100%; padding:.625rem 1rem;
    border:1.5px solid #e5e7eb; border-radius:.875rem;
    font-size:.875rem; color:#1a1a2e; background:#fff;
    font-family:inherit; transition:border .15s, box-shadow .15s;
}
.jobs-filter input:focus, .jobs-filter select:focus {
    outline:none; border-color:#8b5cf6;
    box-shadow:0 0 0 4px rgba(139,92,246,.1);
}
.jobs-filter input::placeholder { color:#9ca3af; }
.filter-btn {
    padding:.625rem 1.5rem;
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    color:#fff; border:none; border-radius:.875rem;
    font-size:.875rem; font-weight:700; font-family:inherit;
    cursor:pointer; transition:transform .15s, box-shadow .15s;
    box-shadow:0 2px 10px rgba(99,102,241,.3);
}
.filter-btn:hover { transform:translateY(-1px); box-shadow:0 4px 18px rgba(99,102,241,.4); }
.filter-clear {
    padding:.625rem 1.25rem;
    background:#f9fafb; color:#6b7280;
    border:1.5px solid #e5e7eb; border-radius:.875rem;
    font-size:.875rem; font-weight:600; font-family:inherit;
    cursor:pointer; text-decoration:none; display:inline-flex;
    transition:background .15s;
}
.filter-clear:hover { background:#f0f0ff; border-color:#a5b4fc; color:#6366f1; }

/* ─ TABS ─ */
.jobs-card {
    background:#fff;
    border-radius:1.25rem;
    border:1px solid rgba(99,102,241,.08);
    box-shadow:0 2px 16px rgba(99,102,241,.07);
    overflow:hidden;
}
.tabs-bar {
    display:flex; border-bottom:1px solid rgba(99,102,241,.08);
    background:linear-gradient(to bottom, #fafafe, #fff);
    padding:0 .5rem;
    gap:.25rem;
}
.tab-link {
    padding:.875rem 1.1rem;
    font-size:.82rem; font-weight:600;
    color:#6b7280; text-decoration:none;
    border-bottom:2.5px solid transparent;
    transition:color .15s, border-color .15s;
    display:flex; align-items:center; gap:.5rem;
    white-space:nowrap;
}
.tab-link:hover { color:#6366f1; }
.tab-link.active { color:#6366f1; border-bottom-color:#6366f1; }
.tab-count {
    padding:.15rem .5rem; border-radius:9999px;
    font-size:.68rem; font-weight:800;
}
.tab-link.active .tab-count { background:#eef2ff; color:#6366f1; }
.tab-link:not(.active) .tab-count { background:#f3f4f6; color:#9ca3af; }

/* ─ JOB CARDS ─ */
.job-item {
    padding:1.5rem;
    border-bottom:1px solid rgba(99,102,241,.06);
    transition:background .2s;
    animation:cardIn .4s ease both;
    position:relative;
}
.job-item:last-child { border-bottom:none; }
.job-item:hover { background:linear-gradient(135deg, rgba(99,102,241,.03) 0%, rgba(168,85,247,.02) 100%); }

/* Left accent strip */
.job-item::before {
    content:'';
    position:absolute; left:0; top:20%; bottom:20%;
    width:3px; border-radius:9999px;
    opacity:0; transition:opacity .2s;
}
.job-item:hover::before { opacity:1; background:linear-gradient(to bottom,#6366f1,#ec4899); }

/* Job header */
.job-title-text {
    font-size:1.05rem; font-weight:800;
    color:#1a1a2e; letter-spacing:-.02em;
    text-decoration:none; transition:color .15s;
}
.job-title-text:hover { color:#6366f1; }

/* Status badges */
.badge {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.25rem .75rem; border-radius:9999px;
    font-size:.7rem; font-weight:800; letter-spacing:.03em; text-transform:uppercase;
}
.badge-published { background:linear-gradient(135deg,#dcfce7,#bbf7d0); color:#15803d; border:1px solid rgba(21,128,61,.15); }
.badge-draft     { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
.badge-closed    { background:#fff1f2; color:#be185d; border:1px solid rgba(190,24,93,.15); }
.badge-expired   { background:#fff7ed; color:#c2410c; border:1px solid rgba(194,65,12,.15); }

/* Meta row */
.job-meta-row {
    display:flex; flex-wrap:wrap; gap:.35rem .75rem;
    align-items:center; margin-top:.625rem;
}
.job-meta-item {
    font-size:.75rem; font-weight:500; color:#6b7280;
    display:flex; align-items:center; gap:.3rem;
}
.job-meta-sep { color:#d1d5db; }

/* Stats row */
.job-stats-row {
    display:flex; align-items:center; justify-content:space-between;
    margin-top:1.25rem; padding-top:1.1rem;
    border-top:1px dashed rgba(99,102,241,.12);
    flex-wrap:wrap; gap:.75rem;
}
.job-app-count { font-size:2rem; font-weight:900; color:#1a1a2e; line-height:1; }
.job-app-label { font-size:.7rem; color:#9ca3af; font-weight:500; margin-top:.1rem; }

/* Action buttons */
.btn-sm {
    padding:.4rem .9rem; border-radius:.625rem;
    font-size:.75rem; font-weight:700; font-family:inherit;
    cursor:pointer; text-decoration:none; display:inline-flex;
    align-items:center; gap:.35rem; border:none;
    transition:transform .15s, box-shadow .15s;
}
.btn-sm:hover { transform:translateY(-1px); }
.btn-close-job  { background:#fff0f3; color:#e11d48; border:1.5px solid rgba(225,29,72,.15); }
.btn-close-job:hover { background:#ffe4e6; box-shadow:0 2px 8px rgba(225,29,72,.15); }
.btn-reopen     { background:#f0fdf4; color:#16a34a; border:1.5px solid rgba(22,163,74,.15); }
.btn-reopen:hover { background:#dcfce7; box-shadow:0 2px 8px rgba(22,163,74,.15); }
.btn-duplicate  { background:#f9fafb; color:#6b7280; border:1.5px solid #e5e7eb; }
.btn-duplicate:hover { background:#f0f0ff; border-color:#a5b4fc; color:#6366f1; }
.btn-view-apps {
    padding:.55rem 1.25rem;
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    color:#fff; font-weight:700; font-size:.825rem;
    border-radius:.875rem; text-decoration:none;
    box-shadow:0 2px 12px rgba(99,102,241,.3);
    display:inline-flex; align-items:center; gap:.4rem;
    transition:transform .15s, box-shadow .15s;
}
.btn-view-apps:hover { transform:translateY(-2px); box-shadow:0 6px 22px rgba(99,102,241,.4); }

/* Icon buttons */
.icon-btn {
    width:2.25rem; height:2.25rem; border-radius:.625rem;
    display:flex; align-items:center; justify-content:center;
    transition:background .15s, color .15s; color:#9ca3af;
    text-decoration:none;
}
.icon-btn:hover { background:#eef2ff; color:#6366f1; }

/* Empty state */
.jobs-empty {
    padding:5rem 1rem; text-align:center;
}
.jobs-empty-icon {
    width:5rem; height:5rem; border-radius:1.5rem;
    background:linear-gradient(135deg,#eef2ff,#f5f3ff);
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 1.25rem;
    box-shadow:0 4px 20px rgba(99,102,241,.15);
    animation:floatY 4s ease-in-out infinite;
}
</style>
@endpush

@section('content')
<div class="jobs-page">

    {{-- HERO --}}
    <div class="jobs-hero j-a1" style="margin-bottom:1.25rem;">
        <div class="hero-orb1"></div>
        <div class="hero-orb2"></div>
        <div class="hero-orb3"></div>
        <div style="position:relative;display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1.5rem;">
            <div>
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem;">
                    <div class="hero-icon-wrap">
                        <svg style="width:1.5rem;height:1.5rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hero-title">Job Postings</div>
                        <div class="hero-sub">Manage your listings, track applications &amp; attract top talent</div>
                    </div>
                </div>
                <div class="hero-chips">
                    <div class="hero-chip">
                        <span class="hero-chip-dot" style="background:#4ade80;"></span>
                        {{ $statusCounts['published'] ?? 0 }} Published
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-dot" style="background:#facc15;animation-delay:.3s"></span>
                        {{ $statusCounts['draft'] ?? 0 }} Drafts
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-dot" style="background:#f9a8d4;animation-delay:.6s"></span>
                        {{ $statusCounts['all'] ?? 0 }} Total
                    </div>
                </div>
            </div>
            <a href="{{ route('employer.jobs.create') }}" class="hero-btn-primary">
                <svg style="width:1rem;height:1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Post New Job
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="jobs-filter j-a2" style="margin-bottom:1.25rem;">
        <form method="GET" action="{{ route('employer.jobs.index') }}">
            <div style="display:grid;grid-template-columns:1fr 1fr auto auto;gap:.875rem;align-items:end;flex-wrap:wrap;">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="&#128269; Search by job title...">
                </div>
                <div style="display:flex;gap:.625rem;">
                    <select name="status" style="flex:1;">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status')==='published'?'selected':'' }}>Published</option>
                        <option value="draft"     {{ request('status')==='draft'?'selected':'' }}>Draft</option>
                        <option value="closed"    {{ request('status')==='closed'?'selected':'' }}>Closed</option>
                    </select>
                    <select name="expiry" style="flex:1;">
                        <option value="">All Jobs</option>
                        <option value="active"  {{ request('expiry')==='active'?'selected':'' }}>Active</option>
                        <option value="expired" {{ request('expiry')==='expired'?'selected':'' }}>Expired</option>
                    </select>
                </div>
                <button type="submit" class="filter-btn">Apply Filters</button>
                @if(request()->hasAny(['search','status','expiry']))
                <a href="{{ route('employer.jobs.index') }}" class="filter-clear">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- JOB LIST CARD --}}
    <div class="jobs-card j-a3">

        {{-- Tabs --}}
        <div class="tabs-bar">
            @foreach([
                ['label'=>'All Jobs',  'value'=>null,        'count'=>$statusCounts['all']],
                ['label'=>'Published', 'value'=>'published', 'count'=>$statusCounts['published']],
                ['label'=>'Draft',     'value'=>'draft',     'count'=>$statusCounts['draft']],
                ['label'=>'Closed',    'value'=>'closed',    'count'=>$statusCounts['closed']],
            ] as $tab)
            @php $isActive = request('status') === $tab['value'] || (!request('status') && !$tab['value']); @endphp
            <a href="{{ route('employer.jobs.index', $tab['value'] ? ['status'=>$tab['value']] : []) }}"
               class="tab-link {{ $isActive ? 'active' : '' }}">
                {{ $tab['label'] }}
                <span class="tab-count">{{ $tab['count'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Empty --}}
        @if($jobs->isEmpty())
        <div class="jobs-empty">
            <div class="jobs-empty-icon">
                <svg style="width:2.5rem;height:2.5rem;color:#8b5cf6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 style="font-size:1.125rem;font-weight:800;color:#1a1a2e;margin:0 0 .5rem;">No Jobs Found</h2>
            <p style="font-size:.875rem;color:#9ca3af;max-width:26rem;margin:0 auto 1.5rem;">
                {{ request()->hasAny(['search','status','expiry']) ? 'Try adjusting your filters to find matching jobs.' : 'Start posting jobs to attract top talent.' }}
            </p>
            <a href="{{ route('employer.jobs.create') }}" class="btn-view-apps">Post Your First Job</a>
        </div>

        {{-- Job list --}}
        @else
        @foreach($jobs as $i => $job)
        <div class="job-item" style="animation-delay:{{ $i * 0.06 }}s;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                <div style="flex:1;min-width:0;">
                    {{-- Title + badges --}}
                    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.25rem;">
                        <a href="{{ route('employer.jobs.show', $job->id) }}" class="job-title-text">{{ $job->title }}</a>
                        @if($job->status === 'published')
                        <span class="badge badge-published">
                            <span style="width:.4rem;height:.4rem;border-radius:50%;background:#16a34a;display:inline-block;animation:pulseDot 2s ease-in-out infinite;"></span>
                            Published
                        </span>
                        @elseif($job->status === 'draft')
                        <span class="badge badge-draft">Draft</span>
                        @else
                        <span class="badge badge-closed">Closed</span>
                        @endif
                        @if($job->expires_at < now())
                        <span class="badge badge-expired">Expired</span>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div class="job-meta-row">
                        <span class="job-meta-item">
                            <svg style="width:.875rem;height:.875rem;color:#a855f7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $job->location }}
                        </span>
                        <span class="job-meta-sep">&bull;</span>
                        <span class="job-meta-item">
                            <svg style="width:.875rem;height:.875rem;color:#6366f1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ ucwords(str_replace('-',' ',$job->employment_type)) }}
                        </span>
                        <span class="job-meta-sep">&bull;</span>
                        <span class="job-meta-item">
                            <svg style="width:.875rem;height:.875rem;color:#f97316" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            {{ ucfirst($job->experience_level) }}
                        </span>
                        <span class="job-meta-sep">&bull;</span>
                        <span class="job-meta-item">
                            <svg style="width:.875rem;height:.875rem;color:#22c55e" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Posted {{ $job->created_at?->diffForHumans() ?? 'recently' }}
                        </span>
                        <span class="job-meta-sep">&bull;</span>
                        <span class="job-meta-item" style="color:{{ $job->expires_at && $job->expires_at < now() ? '#ef4444' : '#9ca3af' }}">
                            <svg style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Expires {{ $job->expires_at?->diffForHumans() ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- Icon actions --}}
                <div style="display:flex;align-items:center;gap:.25rem;flex-shrink:0;">
                    <a href="{{ route('employer.jobs.show', $job->id) }}" class="icon-btn" title="View">
                        <svg style="width:1.125rem;height:1.125rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('employer.jobs.edit', $job->id) }}" class="icon-btn" title="Edit">
                        <svg style="width:1.125rem;height:1.125rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Stats + Actions row --}}
            <div class="job-stats-row">
                <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:.65rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Applications</div>
                        <div style="display:flex;align-items:baseline;gap:.35rem;margin-top:.15rem;">
                            <span style="font-size:2rem;font-weight:900;color:#1a1a2e;line-height:1;letter-spacing:-.04em;">{{ $job->applications_count }}</span>
                            @if($job->applications_count > 0)
                            <span style="font-size:.7rem;color:#22c55e;font-weight:700;padding:.1rem .4rem;background:#f0fdf4;border-radius:9999px;">&#x25b2; Active</span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                        @if($job->status === 'published')
                        <form action="{{ route('employer.jobs.close', $job->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-close-job">
                                <svg style="width:.75rem;height:.75rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Close Job
                            </button>
                        </form>
                        @elseif($job->status === 'closed' && $job->expires_at > now())
                        <form action="{{ route('employer.jobs.reopen', $job->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-reopen">
                                <svg style="width:.75rem;height:.75rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reopen Job
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('employer.jobs.duplicate', $job->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-sm btn-duplicate">
                                <svg style="width:.75rem;height:.75rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Duplicate
                            </button>
                        </form>
                    </div>
                </div>
                <a href="{{ route('employer.applicants.index', ['job_id'=>$job->id]) }}" class="btn-view-apps">
                    View Applications
                    <svg style="width:.875rem;height:.875rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @endforeach

        @if($jobs->hasPages())
        <div style="padding:1.25rem 1.5rem;border-top:1px solid rgba(99,102,241,.08);">
            {{ $jobs->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
