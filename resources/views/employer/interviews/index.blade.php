@extends('layouts.dashboard')

@section('title', 'Interview Pipeline')

@push('styles')
<style>
.ip-page { padding:1.75rem; background:linear-gradient(135deg,#f0f4ff 0%,#f5f3ff 50%,#fff0fb 100%); min-height:100%; }

/* Phase breadcrumb */
.phase-bar { display:flex; gap:0; margin-bottom:1.75rem; border-radius:1rem; overflow:hidden; box-shadow:0 2px 12px rgba(99,102,241,.1); }
.phase-step { flex:1; padding:.6rem .5rem; text-align:center; font-size:.7rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; background:#f3f4f6; color:#9ca3af; position:relative; cursor:default; }
.phase-step.done  { background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; }
.phase-step.active{ background:#fff; color:#6366f1; border:2px solid #6366f1; }

/* Stat cards */
.ip-stat { background:#fff; border-radius:1.25rem; padding:1.25rem 1.5rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 2px 12px rgba(99,102,241,.07); display:flex; align-items:center; gap:1rem; transition:transform .2s; position:relative; overflow:hidden; }
.ip-stat:hover { transform:translateY(-3px); }
.ip-stat::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:1.25rem 1.25rem 0 0; }
.ip-stat.s-indigo::before { background:linear-gradient(90deg,#6366f1,#818cf8); }
.ip-stat.s-orange::before { background:linear-gradient(90deg,#f97316,#fb923c); }
.ip-stat.s-green::before  { background:linear-gradient(90deg,#22c55e,#4ade80); }
.ip-stat.s-amber::before  { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.ip-stat-icon { width:2.75rem; height:2.75rem; border-radius:.875rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.s-indigo .ip-stat-icon { background:#eef2ff; color:#6366f1; }
.s-orange .ip-stat-icon  { background:#fff7ed; color:#f97316; }
.s-green .ip-stat-icon   { background:#f0fdf4; color:#22c55e; }
.s-amber .ip-stat-icon   { background:#fffbeb; color:#f59e0b; }
.ip-stat-num { font-size:1.75rem; font-weight:800; color:#1a1a2e; line-height:1.1; }
.ip-stat-lbl { font-size:.75rem; color:#9ca3af; font-weight:500; }

/* Cards */
.ip-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 2px 12px rgba(99,102,241,.07); overflow:hidden; margin-bottom:1.25rem; }
.ip-card-hd { padding:1rem 1.25rem; border-bottom:1px solid #f0f0f8; display:flex; align-items:center; justify-content:space-between; }
.ip-card-title { font-size:.9rem; font-weight:700; color:#1a1a2e; }

/* Table */
.ip-thead th { padding:.75rem 1rem; font-size:.7rem; font-weight:700; color:#6b7280; letter-spacing:.05em; text-transform:uppercase; background:#f9fafb; border-bottom:1px solid #f0f0f8; white-space:nowrap; }
.ip-tbody tr { transition:background .15s; border-bottom:1px solid #f8f9fb; }
.ip-tbody tr:last-child { border-bottom:none; }
.ip-tbody tr:hover { background:#fafbff; }
.ip-tbody td { padding:.75rem 1rem; vertical-align:middle; }

/* Pills */
.ipill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .65rem; border-radius:9999px; font-size:.7rem; font-weight:700; }
.ipill-scheduled { background:#eff6ff; color:#3b82f6; }
.ipill-completed  { background:#f0fdf4; color:#16a34a; }
.ipill-canceled   { background:#fff1f2; color:#f43f5e; }
.ipill-pending    { background:#fef9c3; color:#b45309; }

/* Avatar */
.ip-av { width:2rem; height:2rem; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#6366f1,#8b5cf6); }

/* Pending candidates list */
.pc-item { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-bottom:1px solid #f8f9fb; }
.pc-item:last-child { border-bottom:none; }

/* Action buttons */
.btn-primary { padding:.4rem 1rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-radius:.625rem; font-size:.78rem; font-weight:600; text-decoration:none; transition:opacity .15s; }
.btn-primary:hover { opacity:.88; }
.btn-outline { padding:.4rem .875rem; border:1.5px solid #e5e7eb; color:#6b7280; border-radius:.625rem; font-size:.78rem; font-weight:600; text-decoration:none; transition:background .15s; }
.btn-outline:hover { background:#f9fafb; }

/* Filter bar */
.ip-filter { background:#fff; border-radius:1.25rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 2px 10px rgba(99,102,241,.06); padding:.875rem 1.25rem; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end; }
.ip-filter label { font-size:.7rem; font-weight:600; color:#6b7280; display:block; margin-bottom:.25rem; }
.ip-filter select, .ip-filter input[type=date] { padding:.4rem .65rem; border:1.5px solid #e5e7eb; border-radius:.55rem; font-size:.8rem; color:#374151; min-width:120px; }
.ip-filter select:focus, .ip-filter input[type=date]:focus { outline:none; border-color:#6366f1; }
.btn-filter { padding:.45rem 1.1rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:.55rem; font-size:.8rem; font-weight:600; cursor:pointer; }

@keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
.ip-stat { animation:fadeUp .3s ease both; }
.ip-stat:nth-child(1){animation-delay:.05s} .ip-stat:nth-child(2){animation-delay:.1s} .ip-stat:nth-child(3){animation-delay:.15s} .ip-stat:nth-child(4){animation-delay:.2s}
</style>
@endpush

@section('content')
<div class="ip-page">
<div style="max-width:1200px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:800;color:#1a1a2e;margin:0;">Interview Pipeline</h1>
            <p style="font-size:.875rem;color:#6b7280;margin:.25rem 0 0;">5-phase candidate interview flow — schedule, conduct, evaluate, decide</p>
        </div>
        <a href="{{ route('employer.ats.index') }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
            <svg style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            ATS
        </a>
    </div>

    {{-- Phase breadcrumb --}}
    <div class="phase-bar">
        <div class="phase-step done">1 Shortlist</div>
        <div class="phase-step active">2 Schedule</div>
        <div class="phase-step">3 Conduct</div>
        <div class="phase-step">4 Evaluate</div>
        <div class="phase-step">5 Decide</div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div class="ip-stat s-amber">
            <div class="ip-stat-icon">
                <svg style="width:1.2rem;height:1.2rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="ip-stat-num">{{ $stats['pending'] }}</div><div class="ip-stat-lbl">Awaiting Schedule</div></div>
        </div>
        <div class="ip-stat s-indigo">
            <div class="ip-stat-icon">
                <svg style="width:1.2rem;height:1.2rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div><div class="ip-stat-num">{{ $stats['total'] }}</div><div class="ip-stat-lbl">Total Interviews</div></div>
        </div>
        <div class="ip-stat s-orange">
            <div class="ip-stat-icon">
                <svg style="width:1.2rem;height:1.2rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div><div class="ip-stat-num">{{ $stats['scheduled'] }}</div><div class="ip-stat-lbl">Scheduled</div></div>
        </div>
        <div class="ip-stat s-green">
            <div class="ip-stat-icon">
                <svg style="width:1.2rem;height:1.2rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="ip-stat-num">{{ $stats['completed'] }}</div><div class="ip-stat-lbl">Completed</div></div>
        </div>
    </div>

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.875rem 1.25rem;border-radius:.875rem;margin-bottom:1.25rem;font-size:.875rem;font-weight:500;">
        &#10003; {{ session('success') }}
    </div>
    @endif

    {{-- Pending schedule section --}}
    @if($pendingSchedule->isNotEmpty())
    <div class="ip-card" style="border-left:4px solid #f59e0b;">
        <div class="ip-card-hd">
            <div>
                <div class="ip-card-title">&#9888; Awaiting Interview Schedule</div>
                <div style="font-size:.78rem;color:#9ca3af;margin-top:.1rem;">These shortlisted candidates need an interview slot</div>
            </div>
        </div>
        @foreach($pendingSchedule as $app)
        <div class="pc-item">
            <div class="ip-av">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.875rem;font-weight:600;color:#1a1a2e;">{{ $app->user->name ?? 'Guest' }}</div>
                <div style="font-size:.75rem;color:#9ca3af;">{{ $app->job->title ?? 'N/A' }}</div>
            </div>
            <div style="font-size:.75rem;color:#9ca3af;margin-right:1rem;">Shortlisted {{ $app->status_updated_at?->diffForHumans() }}</div>
            <a href="{{ route('employer.interviews.schedule', $app->id) }}" class="btn-primary" style="font-size:.75rem;padding:.35rem .875rem;white-space:nowrap;">
                &#128197; Schedule
            </a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('employer.interviews.index') }}" class="ip-filter">
        <div>
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="canceled"  {{ request('status') === 'canceled'  ? 'selected' : '' }}>Canceled</option>
            </select>
        </div>
        <div>
            <label>Type</label>
            <select name="type">
                <option value="">All Types</option>
                <option value="video"      {{ request('type') === 'video'      ? 'selected' : '' }}>Video</option>
                <option value="phone"      {{ request('type') === 'phone'      ? 'selected' : '' }}>Phone</option>
                <option value="onsite"     {{ request('type') === 'onsite'     ? 'selected' : '' }}>Onsite</option>
                <option value="technical"  {{ request('type') === 'technical'  ? 'selected' : '' }}>Technical</option>
                <option value="panel"      {{ request('type') === 'panel'      ? 'selected' : '' }}>Panel</option>
            </select>
        </div>
        <div>
            <label>From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}">
        </div>
        <div>
            <label>Sort</label>
            <select name="sort">
                <option value="recent" {{ request('sort','recent') === 'recent' ? 'selected' : '' }}>Most Recent</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
            </select>
        </div>
        <div style="display:flex;gap:.5rem;align-items:flex-end;">
            <button type="submit" class="btn-filter">Filter</button>
            @if(request()->hasAny(['status','type','date_from','date_to']))
            <a href="{{ route('employer.interviews.index') }}" class="btn-outline">Clear</a>
            @endif
        </div>
    </form>

    {{-- Main interviews table --}}
    <div class="ip-card">
        @if($interviews->isEmpty())
        <div style="padding:3rem 1rem;text-align:center;">
            <div style="width:3.5rem;height:3.5rem;border-radius:1rem;background:#eef2ff;color:#6366f1;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:1.75rem;height:1.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin:0 0 .4rem;">No interviews yet</h3>
            <p style="font-size:.875rem;color:#9ca3af;max-width:28rem;margin:0 auto;">Schedule interviews from the section above or promote candidates to interview stage via the ATS.</p>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr class="ip-thead">
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Type / Round</th>
                    <th>Scheduled</th>
                    <th>Status</th>
                    <th>AI Score</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody class="ip-tbody">
                @foreach($interviews as $iv)
                @php
                    $app = $iv->application;
                    $score = $iv->rating;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem;">
                            <div class="ip-av">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
                            <div>
                                <div style="font-size:.85rem;font-weight:600;color:#1a1a2e;">{{ $app->user->name ?? 'Guest' }}</div>
                                <div style="font-size:.72rem;color:#9ca3af;">{{ $app->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.85rem;font-weight:600;color:#374151;">{{ $app->job->title ?? 'N/A' }}</div>
                        <div style="font-size:.72rem;color:#9ca3af;">{{ $app->job->location ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:.82rem;font-weight:600;color:#6366f1;text-transform:capitalize;">{{ $iv->interview_type }}</div>
                        <div style="font-size:.72rem;color:#9ca3af;">Round {{ $iv->round ?? 1 }} &bull; {{ $iv->duration_minutes }} min</div>
                    </td>
                    <td>
                        @if($iv->scheduled_at)
                        <div style="font-size:.82rem;color:#374151;">{{ $iv->scheduled_at->format('d M Y') }}</div>
                        <div style="font-size:.72rem;color:#9ca3af;">{{ $iv->scheduled_at->format('g:i A') }}</div>
                        @else
                        <span style="font-size:.78rem;color:#d1d5db;">Not set</span>
                        @endif
                    </td>
                    <td>
                        @if($iv->status === 'scheduled')
                        <span class="ipill ipill-scheduled"><span style="width:.4rem;height:.4rem;border-radius:50%;background:#3b82f6;display:inline-block;"></span>Scheduled</span>
                        @elseif($iv->status === 'completed')
                        <span class="ipill ipill-completed"><span style="width:.4rem;height:.4rem;border-radius:50%;background:#16a34a;display:inline-block;"></span>Completed</span>
                        @elseif($iv->status === 'canceled')
                        <span class="ipill ipill-canceled">Canceled</span>
                        @else
                        <span class="ipill ipill-pending">{{ ucfirst($iv->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($score)
                        @php $pct = ($score / 5) * 100; $col = $score >= 4 ? '#22c55e' : ($score >= 3 ? '#f59e0b' : '#f43f5e'); @endphp
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div style="flex:1;height:.35rem;background:#f3f4f6;border-radius:9999px;overflow:hidden;min-width:3rem;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $col }};border-radius:9999px;"></div>
                            </div>
                            <span style="font-size:.78rem;font-weight:700;color:{{ $col }};">{{ number_format($score, 1) }}</span>
                        </div>
                        @else
                        <span style="font-size:.75rem;color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.4rem;justify-content:flex-end;">
                            <a href="{{ route('employer.interviews.show', $iv->id) }}" class="btn-outline" style="font-size:.72rem;">View</a>
                            @if($iv->status === 'scheduled')
                            <a href="{{ route('employer.interviews.show', $iv->id) }}" class="btn-primary" style="font-size:.72rem;">Conduct</a>
                            @elseif($iv->status === 'completed' && !$iv->ai_score_summary)
                            <a href="{{ route('employer.interviews.evaluate', $iv->id) }}" class="btn-primary" style="font-size:.72rem;background:linear-gradient(135deg,#f59e0b,#f97316);">Evaluate</a>
                            @elseif($iv->status === 'completed' && $iv->ai_score_summary)
                            <a href="{{ route('employer.interviews.decide', $iv->id) }}" class="btn-primary" style="font-size:.72rem;background:linear-gradient(135deg,#22c55e,#16a34a);">Decide</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($interviews->hasPages())
        <div style="padding:.875rem 1rem;border-top:1px solid #f0f0f8;">
            {{ $interviews->withQueryString()->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
</div>
@endsection