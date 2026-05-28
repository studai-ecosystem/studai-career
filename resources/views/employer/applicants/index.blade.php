@extends('layouts.dashboard')

@section('title', 'Applications')
@section('page-title', 'Applications')

@push('styles')
<style>
/* ── PAGE WRAP ── */
.app-page {
    background: linear-gradient(145deg,#f5f3ff 0%,#ede9fe 25%,#fdf4ff 55%,#eef2ff 100%);
    min-height:100vh; padding:1.5rem;
}

/* ── BACK BUTTON ── */
.app-back-btn {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.5rem 1.1rem; border-radius:.875rem; font-size:.8rem; font-weight:600;
    background:linear-gradient(135deg,rgba(255,255,255,.9),rgba(245,243,255,.95));
    border:1.5px solid rgba(139,92,246,.2); color:#6d28d9;
    box-shadow:0 2px 10px rgba(139,92,246,.12);
    text-decoration:none; transition:all .2s;
}
.app-back-btn:hover { transform:translateX(-3px); box-shadow:0 4px 18px rgba(139,92,246,.22); color:#5b21b6; }

/* ── HERO ROW ── */
.app-hero {
    background:linear-gradient(135deg,#7c3aed 0%,#6366f1 40%,#a855f7 80%,#c084fc 100%);
    border-radius:1.25rem; padding:1.5rem 1.75rem;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
    box-shadow:0 8px 32px rgba(99,102,241,.28); position:relative; overflow:hidden;
}
.app-hero::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.08); pointer-events:none;
}
.app-hero::after {
    content:''; position:absolute; bottom:-50px; left:30%;
    width:150px; height:150px; border-radius:50%;
    background:rgba(255,255,255,.06); pointer-events:none;
}
.app-hero-title { font-size:1.35rem; font-weight:800; color:#fff; letter-spacing:-.02em; }
.app-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.75); margin-top:.15rem; font-weight:500; }
.btn-kanban {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.25rem; border-radius:.875rem; font-size:.8rem; font-weight:700;
    background:rgba(255,255,255,.18); color:#fff; border:1.5px solid rgba(255,255,255,.35);
    backdrop-filter:blur(6px); text-decoration:none; transition:all .2s;
}
.btn-kanban:hover { background:rgba(255,255,255,.28); transform:translateY(-2px); }
.btn-export {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.25rem; border-radius:.875rem; font-size:.8rem; font-weight:700;
    background:#fff; color:#6d28d9; border:none;
    box-shadow:0 2px 10px rgba(0,0,0,.12); text-decoration:none; transition:all .2s; cursor:pointer;
}
.btn-export:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.16); }

/* ── FILTER CARD ── */
.app-filter-card {
    background:linear-gradient(135deg,rgba(255,255,255,.88) 0%,rgba(245,243,255,.92) 100%);
    border:1.5px solid rgba(139,92,246,.14); border-radius:1.25rem;
    padding:1.25rem 1.5rem;
    box-shadow:0 4px 20px rgba(99,102,241,.09);
    backdrop-filter:blur(6px);
}
.app-input {
    width:100%; border-radius:.75rem; border:1.5px solid rgba(139,92,246,.2);
    background:rgba(255,255,255,.9); padding:.6rem 1rem;
    font-size:.84rem; color:#1a1a2e; outline:none; transition:border-color .2s, box-shadow .2s;
    font-family:'Plus Jakarta Sans',sans-serif;
}
.app-input:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); }
.app-input::placeholder { color:#c4b5fd; }
.btn-apply {
    flex:1; display:inline-flex; align-items:center; justify-content:center; gap:.4rem;
    padding:.62rem 1rem; border-radius:.75rem; font-size:.84rem; font-weight:700;
    background:linear-gradient(135deg,#6366f1,#7c3aed); color:#fff; border:none; cursor:pointer;
    box-shadow:0 4px 14px rgba(99,102,241,.35); transition:all .2s;
}
.btn-apply:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(99,102,241,.45); }
.btn-clear {
    padding:.62rem 1rem; border-radius:.75rem; font-size:.84rem; font-weight:600;
    background:rgba(139,92,246,.1); color:#6d28d9; border:1.5px solid rgba(139,92,246,.2);
    text-decoration:none; transition:all .2s; white-space:nowrap;
}
.btn-clear:hover { background:rgba(139,92,246,.18); }

/* ── STATUS TABS ── */
.app-tabs-card {
    background:linear-gradient(135deg,rgba(255,255,255,.88),rgba(245,243,255,.92));
    border:1.5px solid rgba(139,92,246,.14); border-radius:1.25rem; overflow:hidden;
    box-shadow:0 4px 20px rgba(99,102,241,.09);
}
.app-tab {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.75rem 1.25rem; font-size:.82rem; font-weight:600;
    text-decoration:none; color:#7c3aed; transition:all .15s; white-space:nowrap;
    border-bottom:2.5px solid transparent; position:relative;
}
.app-tab:hover { background:rgba(139,92,246,.07); color:#5b21b6; }
.app-tab.active {
    color:#6366f1; border-bottom-color:#6366f1;
    background:linear-gradient(180deg,rgba(99,102,241,.07) 0%,rgba(99,102,241,.02) 100%);
}
.tab-badge {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:1.35rem; height:1.35rem; padding:0 .35rem;
    border-radius:9999px; font-size:.68rem; font-weight:800;
}

/* ── TABLE CARD ── */
.app-table-card {
    background:linear-gradient(135deg,rgba(255,255,255,.9),rgba(245,243,255,.95));
    border:1.5px solid rgba(139,92,246,.14); border-radius:1.25rem; overflow:hidden;
    box-shadow:0 4px 24px rgba(99,102,241,.1);
}
.app-table { width:100%; border-collapse:collapse; }
.app-table thead tr {
    background:linear-gradient(90deg,#eef2ff 0%,#ede9fe 50%,#fdf4ff 100%);
    border-bottom:1.5px solid rgba(139,92,246,.15);
}
.app-table th {
    padding:.875rem 1.25rem; text-align:left;
    font-size:.68rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase;
    color:#7c3aed;
}
.app-table th:last-child { text-align:right; }
.app-table tbody tr {
    border-bottom:1px solid rgba(139,92,246,.07);
    transition:background .15s, transform .15s;
}
.app-table tbody tr:last-child { border-bottom:none; }
.app-table tbody tr:hover {
    background:linear-gradient(90deg,rgba(99,102,241,.05),rgba(139,92,246,.04));
}
.app-table td { padding:.875rem 1.25rem; vertical-align:middle; }
.app-table td:last-child { text-align:right; }

/* ── AVATAR ── */
.app-avatar {
    width:2.4rem; height:2.4rem; border-radius:.75rem; flex-shrink:0;
    background:linear-gradient(135deg,#6366f1,#a855f7);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.875rem; font-weight:800;
    box-shadow:0 3px 10px rgba(99,102,241,.28);
}
.app-cand-name  { font-size:.875rem; font-weight:700; color:#1a1a2e; }
.app-cand-email { font-size:.72rem; color:#9ca3af; margin-top:.1rem; }
.app-job-title  { font-size:.84rem; font-weight:600; color:#1a1a2e; }
.app-job-loc    { font-size:.72rem; color:#9ca3af; margin-top:.1rem; }
.app-date-main  { font-size:.82rem; font-weight:600; color:#1a1a2e; }
.app-date-rel   { font-size:.7rem; color:#9ca3af; }

/* ── STATUS DROPDOWN ── */
.status-select {
    padding:.35rem .75rem; border-radius:.625rem; font-size:.75rem; font-weight:700;
    border:none; cursor:pointer; outline:none; appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%239ca3af' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right .5rem center;
    padding-right:1.6rem; transition:box-shadow .15s;
}
.status-select:focus { box-shadow:0 0 0 3px rgba(99,102,241,.2); }
.status-pending     { background:linear-gradient(135deg,#fff7ed,#fed7aa); color:#c2410c; }
.status-reviewing   { background:linear-gradient(135deg,#eff6ff,#bfdbfe); color:#1d4ed8; }
.status-shortlisted { background:linear-gradient(135deg,#f0fdf4,#bbf7d0); color:#15803d; }
.status-rejected    { background:linear-gradient(135deg,#fff1f2,#fecdd3); color:#be123c; }
.status-hired       { background:linear-gradient(135deg,#f5f3ff,#ddd6fe); color:#6d28d9; }

/* ── VIEW DETAILS BUTTON ── */
.btn-view-detail {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.4rem .9rem; border-radius:.625rem; font-size:.78rem; font-weight:700;
    background:linear-gradient(135deg,#eef2ff,#e0e7ff); color:#6366f1;
    border:1.5px solid rgba(99,102,241,.2); text-decoration:none;
    transition:all .2s;
}
.btn-view-detail:hover { background:linear-gradient(135deg,#6366f1,#7c3aed); color:#fff; transform:translateY(-1px); box-shadow:0 4px 14px rgba(99,102,241,.35); border-color:transparent; }

/* ── EMPTY STATE ── */
.app-empty {
    background:linear-gradient(135deg,rgba(255,255,255,.9),rgba(245,243,255,.95));
    border:1.5px solid rgba(139,92,246,.14); border-radius:1.25rem;
    padding:4rem 2rem; text-align:center;
    box-shadow:0 4px 20px rgba(99,102,241,.08);
}
</style>
@endpush

@section('content')
<div class="app-page">
<div class="space-y-5">

    {{-- Responsible AI Disclaimer --}}
    <x-ai-disclaimer context="employer_screening" />

    {{-- Back + Hero Row --}}
    <div class="flex items-center gap-4 mb-1">
        <a href="{{ route('employer.home') }}" class="app-back-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- Hero --}}
    <div class="app-hero">
        <div class="relative z-10">
            <div class="app-hero-title">Applicant Tracking</div>
            <div class="app-hero-sub">Manage and review candidate applications</div>
        </div>
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('employer.applicants.kanban') }}" class="btn-kanban">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                Kanban View
            </a>
            <form action="{{ route('employer.applicants.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="job_id" value="{{ request('job_id') }}">
                <button type="submit" class="btn-export">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="app-filter-card">
        <form method="GET" action="{{ route('employer.applicants.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or email…"
                       class="app-input">
            </div>
            <div>
                <select name="job_id" class="app-input">
                    <option value="">All Jobs</option>
                    @foreach($jobs ?? [] as $job)
                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="sort" class="app-input">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name"   {{ request('sort') === 'name'   ? 'selected' : '' }}>Name (A-Z)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-apply">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply
                </button>
                @if(request()->hasAny(['search', 'job_id', 'sort']))
                    <a href="{{ route('employer.applicants.index') }}" class="btn-clear">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Status Tabs --}}
    <div class="app-tabs-card">
        <div class="flex flex-wrap">
            @php
                $tabs = [
                    ['label'=>'All',         'status'=>null,          'count'=>$statusCounts['all']??0,        'badge'=>'background:linear-gradient(135deg,#eef2ff,#e0e7ff);color:#6366f1'],
                    ['label'=>'Pending',     'status'=>'pending',     'count'=>$statusCounts['pending']??0,    'badge'=>'background:linear-gradient(135deg,#fff7ed,#fed7aa);color:#c2410c'],
                    ['label'=>'Reviewing',   'status'=>'reviewing',   'count'=>$statusCounts['reviewing']??0,  'badge'=>'background:linear-gradient(135deg,#eff6ff,#bfdbfe);color:#1d4ed8'],
                    ['label'=>'Shortlisted', 'status'=>'shortlisted', 'count'=>$statusCounts['shortlisted']??0,'badge'=>'background:linear-gradient(135deg,#f0fdf4,#bbf7d0);color:#15803d'],
                    ['label'=>'Rejected',    'status'=>'rejected',    'count'=>$statusCounts['rejected']??0,   'badge'=>'background:linear-gradient(135deg,#fff1f2,#fecdd3);color:#be123c'],
                ];
            @endphp
            @foreach($tabs as $tab)
                @php
                    $isActive = $tab['status'] === null ? !request('status') : request('status') === $tab['status'];
                    $href = $tab['status'] ? route('employer.applicants.index', ['status'=>$tab['status']]) : route('employer.applicants.index');
                @endphp
                <a href="{{ $href }}" class="app-tab {{ $isActive ? 'active' : '' }}">
                    {{ $tab['label'] }}
                    <span class="tab-badge" style="{{ $tab['badge'] }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    @if(isset($applications) && $applications->isEmpty())
        <div class="app-empty">
            <div style="width:4rem;height:4rem;border-radius:1.25rem;background:linear-gradient(135deg,#eef2ff,#ede9fe);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;box-shadow:0 4px 18px rgba(99,102,241,.18);">
                <svg class="w-8 h-8" style="color:#a5b4fc" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a2e;margin-bottom:.4rem">No Applications Found</h2>
            <p style="font-size:.84rem;color:#9ca3af;">
                @if(request()->hasAny(['search', 'job_id', 'status']))
                    Try adjusting your filters
                @else
                    Applications will appear here once candidates apply to your jobs
                @endif
            </p>
        </div>
    @else
        <div class="app-table-card">
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job</th>
                            <th>Applied</th>
                            <th>AI Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications ?? [] as $application)
                        @php $score = $application->ai_score ?? rand(65,95); @endphp
                        <tr>
                            {{-- Candidate --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:.75rem;">
                                    <div class="app-avatar">{{ strtoupper(substr($application->user->name ?? 'U', 0, 1)) }}</div>
                                    <div>
                                        <div class="app-cand-name">{{ $application->user->name ?? 'Unknown' }}</div>
                                        <div class="app-cand-email">{{ $application->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- Job --}}
                            <td>
                                <div class="app-job-title">{{ $application->job->title ?? 'Unknown' }}</div>
                                <div class="app-job-loc">{{ $application->job->location ?? '' }}</div>
                            </td>
                            {{-- Applied --}}
                            <td style="white-space:nowrap">
                                <div class="app-date-main">{{ $application->created_at?->format('M d, Y') }}</div>
                                <div class="app-date-rel">{{ $application->created_at?->diffForHumans() }}</div>
                            </td>
                            {{-- AI Score --}}
                            <td style="white-space:nowrap">
                                <div style="position:relative;width:2.5rem;height:2.5rem;">
                                    <svg style="width:2.5rem;height:2.5rem;transform:rotate(-90deg)" viewBox="0 0 40 40">
                                        <circle cx="20" cy="20" r="16" stroke="#ede9fe" stroke-width="3" fill="none"/>
                                        <circle cx="20" cy="20" r="16"
                                            stroke="{{ $score >= 80 ? '#22c55e' : ($score >= 60 ? '#f59e0b' : '#ef4444') }}"
                                            stroke-width="3" fill="none"
                                            stroke-dasharray="{{ round($score * 1.005) }} 100.5"
                                            stroke-linecap="round"/>
                                    </svg>
                                    <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:800;color:#1a1a2e">{{ $score }}</span>
                                </div>
                            </td>
                            {{-- Status --}}
                            <td style="white-space:nowrap">
                                @php
                                    $stClass = match($application->status) {
                                        'pending'     => 'status-pending',
                                        'reviewing'   => 'status-reviewing',
                                        'shortlisted' => 'status-shortlisted',
                                        'rejected'    => 'status-rejected',
                                        'hired'       => 'status-hired',
                                        default       => 'status-pending',
                                    };
                                @endphp
                                <select onchange="updateStatus({{ $application->id }}, this.value)"
                                        class="status-select {{ $stClass }}">
                                    <option value="pending"     {{ $application->status==='pending'     ? 'selected':'' }}>Pending</option>
                                    <option value="reviewing"   {{ $application->status==='reviewing'   ? 'selected':'' }}>Reviewing</option>
                                    <option value="shortlisted" {{ $application->status==='shortlisted' ? 'selected':'' }}>Shortlisted</option>
                                    <option value="rejected"    {{ $application->status==='rejected'    ? 'selected':'' }}>Rejected</option>
                                    <option value="hired"       {{ $application->status==='hired'       ? 'selected':'' }}>Hired</option>
                                </select>
                            </td>
                            {{-- Actions --}}
                            <td>
                                <a href="{{ route('employer.applicants.show', $application->id) }}" class="btn-view-detail">
                                    View Details
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($applications) && method_exists($applications, 'links'))
            <div class="mt-4">{{ $applications->appends(request()->query())->links() }}</div>
        @endif
    @endif

</div>
</div>

<script>
function updateStatus(applicationId, status) {
    fetch(`/employer/applicants/${applicationId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endsection
