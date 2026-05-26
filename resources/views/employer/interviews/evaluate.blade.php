@extends('layouts.dashboard')
@section('title', 'Evaluate Interview')

@push('styles')
<style>
.ev-page { padding:1.75rem; background:linear-gradient(135deg,#f0f4ff 0%,#f5f3ff 50%,#fff0fb 100%); min-height:100%; }
.ev-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 2px 16px rgba(99,102,241,.08); overflow:hidden; margin-bottom:1.25rem; }
.ev-card-hd { padding:1rem 1.25rem; border-bottom:1px solid #f0f0f8; }
.ev-card-title { font-size:.9rem; font-weight:700; color:#1a1a2e; }
.ev-card-body { padding:1.25rem; }
.score-bar { height:.5rem; background:#f3f4f6; border-radius:9999px; overflow:hidden; flex:1; }
.score-fill { height:100%; border-radius:9999px; transition:width .6s ease; }
.panel-score { display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:#f9fafb; border-radius:.625rem; margin-bottom:.4rem; }
.rec-badge { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1.25rem; border-radius:9999px; font-size:.85rem; font-weight:700; }
.rec-hire   { background:#f0fdf4; color:#16a34a; border:2px solid #bbf7d0; }
.rec-next   { background:#eff6ff; color:#3b82f6; border:2px solid #bfdbfe; }
.rec-silver { background:#fef9c3; color:#b45309; border:2px solid #fde68a; }
.rec-reject { background:#fff1f2; color:#f43f5e; border:2px solid #fda4af; }
.btn-decide { padding:.75rem 2rem; background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; border-radius:.875rem; font-size:.9rem; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; transition:transform .15s; box-shadow:0 4px 14px rgba(34,197,94,.3); }
.btn-decide:hover { transform:translateY(-1px); }
.form-ctrl { width:100%; padding:.6rem .875rem; border:1.5px solid #e5e7eb; border-radius:.75rem; font-size:.85rem; resize:vertical; box-sizing:border-box; }
.form-ctrl:focus { outline:none; border-color:#6366f1; }
</style>
@endpush

@section('content')
<div class="ev-page">
<div style="max-width:860px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <a href="{{ route('employer.interviews.show', $interview->id) }}" style="color:#6b7280;text-decoration:none;font-size:.85rem;">&#8592; Conduct</a>
        <h1 style="font-size:1.4rem;font-weight:800;color:#1a1a2e;margin:0;">Phase 4 &mdash; AI Evaluation</h1>
    </div>

    {{-- Candidate banner --}}
    @php
        $app = $interview->application;
        $totalScores = $scoresByQuestion->count();
        $overallAvg  = $totalScores ? round($scoresByQuestion->avg('avg'), 1) : null;
        $pct         = $overallAvg ? ($overallAvg / 5) * 100 : 0;
        $color       = $overallAvg >= 4 ? '#22c55e' : ($overallAvg >= 3 ? '#f59e0b' : '#f43f5e');
        $recLabel    = $overallAvg >= 4 ? 'Hire' : ($overallAvg >= 3 ? 'Next Round' : ($overallAvg >= 2.5 ? 'Silver Medal' : 'Reject'));
        $recClass    = $overallAvg >= 4 ? 'hire' : ($overallAvg >= 3 ? 'next' : ($overallAvg >= 2.5 ? 'silver' : 'reject'));
    @endphp
    <div style="background:#fff;border-radius:1.25rem;border:1px solid rgba(99,102,241,.1);padding:1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:3rem;height:3rem;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
            <div>
                <div style="font-size:1.1rem;font-weight:800;color:#1a1a2e;">{{ $app->user->name ?? 'Guest' }}</div>
                <div style="font-size:.82rem;color:#6b7280;">{{ $app->job->title ?? 'N/A' }} &bull; Round {{ $interview->round ?? 1 }} {{ ucfirst($interview->interview_type) }}</div>
            </div>
        </div>
        <div style="text-align:right;">
            @if($overallAvg)
            <div style="font-size:2rem;font-weight:900;color:{{ $color }};">{{ $overallAvg }}<span style="font-size:1rem;color:#9ca3af;">/5</span></div>
            <span class="rec-badge rec-{{ $recClass }}">AI Recommendation: {{ $recLabel }}</span>
            @else
            <div style="color:#9ca3af;font-size:.85rem;">No panel scores yet</div>
            @endif
        </div>
    </div>

    {{-- Scores breakdown --}}
    @if($scoresByQuestion->isNotEmpty())
    <div class="ev-card">
        <div class="ev-card-hd"><div class="ev-card-title">Panel Score Breakdown by Question</div></div>
        <div class="ev-card-body">
            @foreach($scoresByQuestion as $qKey => $data)
            @php
                $qText = collect($interview->question_set ?? [])->firstWhere('key', $qKey)['text'] ?? $qKey;
                $qPct  = ($data['avg'] / 5) * 100;
                $qCol  = $data['avg'] >= 4 ? '#22c55e' : ($data['avg'] >= 3 ? '#f59e0b' : '#f43f5e');
            @endphp
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.5rem;">{{ $qText }}</div>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.4rem;">
                    <div class="score-bar"><div class="score-fill" style="width:{{ $qPct }}%;background:{{ $qCol }};"></div></div>
                    <span style="font-size:.85rem;font-weight:700;color:{{ $qCol }};min-width:2rem;">{{ $data['avg'] }}</span>
                </div>
                @foreach($data['scores'] as $ps)
                <div class="panel-score">
                    <div style="width:.4rem;height:.4rem;border-radius:50%;background:#6366f1;"></div>
                    <span style="font-size:.75rem;color:#6b7280;flex:1;">{{ $ps['interviewer'] }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1a1a2e;">{{ $ps['score'] }}/5</span>
                    @if($ps['comment'])<span style="font-size:.72rem;color:#9ca3af;margin-left:.5rem;">— {{ $ps['comment'] }}</span>@endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="ev-card">
        <div class="ev-card-body" style="text-align:center;padding:2rem;">
            <div style="font-size:.9rem;color:#9ca3af;">No panel scores submitted yet. Ask interviewers to complete the scoring sheet from the Conduct page.</div>
            <a href="{{ route('employer.interviews.show', $interview->id) }}" style="display:inline-block;margin-top:1rem;font-size:.85rem;color:#6366f1;text-decoration:none;">&#8592; Go to Conduct page</a>
        </div>
    </div>
    @endif

    {{-- Interviewer notes + submit --}}
    <div class="ev-card">
        <div class="ev-card-hd"><div class="ev-card-title">Interviewer Notes &amp; Final Comments</div></div>
        <div class="ev-card-body">
            <form method="POST" action="{{ route('employer.interviews.evaluate.submit', $interview->id) }}">
                @csrf
                <textarea name="interviewer_notes" class="form-ctrl" rows="5" placeholder="Overall impressions, cultural fit observations, key strengths/concerns...">{{ old('interviewer_notes', $interview->interviewer_notes) }}</textarea>
                <div style="margin-top:1.25rem;display:flex;gap:1rem;align-items:center;">
                    <button type="submit" class="btn-decide">Generate AI Score &amp; Proceed to Decision &#8594;</button>
                    <a href="{{ route('employer.interviews.index') }}" style="color:#6b7280;font-size:.85rem;text-decoration:none;">Back to Pipeline</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection
