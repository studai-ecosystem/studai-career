@extends('layouts.dashboard')
@section('title', 'Evaluate Interview')

@push('styles')
<style>
.ev-page { padding:1.75rem; background:#EBF2FF; min-height:100%; }
.ev-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(20, 71, 186,.1); box-shadow: none; overflow:hidden; margin-bottom:1.25rem; }
.ev-card-hd { padding:1rem 1.25rem; border-bottom:1px solid #EBF2FF; }
.ev-card-title { font-size:.9rem; font-weight:700; color:#0C0C0C; }
.ev-card-body { padding:1.25rem; }
.score-bar { height:.5rem; background:#F0F0EE; border-radius:9999px; overflow:hidden; flex:1; }
.score-fill { height:100%; border-radius:9999px; transition:width .6s ease; }
.panel-score { display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:#F7F7F5; border-radius:.625rem; margin-bottom:.4rem; }
.rec-badge { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1.25rem; border-radius:9999px; font-size:.85rem; font-weight:700; }
.rec-hire   { background:#EDFAF2; color:#1E8E3E; border:2px solid #A3D9B4; }
.rec-next   { background:#EBF2FF; color:#2D6CDF; border:2px solid #BFCFEE; }
.rec-silver { background:#FFF8EC; color:#E37400; border:2px solid #F0C77A; }
.rec-reject { background:#FEF2F2; color:#2D6CDF; border:2px solid #FCA5A5; }
.btn-decide { padding:.75rem 2rem; background:#1E8E3E; color:#fff; border:none; border-radius:.875rem; font-size:.9rem; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; transition:transform .15s; box-shadow: none; }
.btn-decide:hover { transform:translateY(-1px); }
.form-ctrl { width:100%; padding:.6rem .875rem; border:1.5px solid #E2E2E0; border-radius:.75rem; font-size:.85rem; resize:vertical; box-sizing:border-box; }
.form-ctrl:focus { outline:none; border-color:#2D6CDF; }
</style>
@endpush

@section('content')
<div class="ev-page">
<div style="max-width:860px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <a href="{{ route('employer.interviews.show', $interview->id) }}" style="color:#737373;text-decoration:none;font-size:.85rem;">&#8592; Conduct</a>
        <h1 style="font-size:1.4rem;font-weight:800;color:#0C0C0C;margin:0;">Phase 4 &mdash; AI Evaluation</h1>
    </div>

    {{-- Candidate banner --}}
    @php
        $app = $interview->application;
        $totalScores = $scoresByQuestion->count();
        $overallAvg  = $totalScores ? round($scoresByQuestion->avg('avg'), 1) : null;
        $pct         = $overallAvg ? ($overallAvg / 5) * 100 : 0;
        $color       = $overallAvg >= 4 ? '#1E8E3E' : ($overallAvg >= 3 ? '#E37400' : '#2D6CDF');
        $recLabel    = $overallAvg >= 4 ? 'Hire' : ($overallAvg >= 3 ? 'Next Round' : ($overallAvg >= 2.5 ? 'Silver Medal' : 'Reject'));
        $recClass    = $overallAvg >= 4 ? 'hire' : ($overallAvg >= 3 ? 'next' : ($overallAvg >= 2.5 ? 'silver' : 'reject'));
    @endphp
    <div style="background:#fff;border-radius:1.25rem;border:1px solid rgba(20, 71, 186,.1);padding:1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:3rem;height:3rem;border-radius:50%;background:#2D6CDF;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
            <div>
                <div style="font-size:1.1rem;font-weight:800;color:#0C0C0C;">{{ $app->user->name ?? 'Guest' }}</div>
                <div style="font-size:.82rem;color:#737373;">{{ $app->job->title ?? 'N/A' }} &bull; Round {{ $interview->round ?? 1 }} {{ ucfirst($interview->interview_type) }}</div>
            </div>
        </div>
        <div style="text-align:right;">
            @if($overallAvg)
            <div style="font-size:2rem;font-weight:900;color:{{ $color }};">{{ $overallAvg }}<span style="font-size:1rem;color:#A8A8A8;">/5</span></div>
            <span class="rec-badge rec-{{ $recClass }}">AI Recommendation: {{ $recLabel }}</span>
            @else
            <div style="color:#A8A8A8;font-size:.85rem;">No panel scores yet</div>
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
                $qCol  = $data['avg'] >= 4 ? '#1E8E3E' : ($data['avg'] >= 3 ? '#E37400' : '#2D6CDF');
            @endphp
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:.82rem;font-weight:600;color:#3D3D3D;margin-bottom:.5rem;">{{ $qText }}</div>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.4rem;">
                    <div class="score-bar"><div class="score-fill" style="width:{{ $qPct }}%;background:{{ $qCol }};"></div></div>
                    <span style="font-size:.85rem;font-weight:700;color:{{ $qCol }};min-width:2rem;">{{ $data['avg'] }}</span>
                </div>
                @foreach($data['scores'] as $ps)
                <div class="panel-score">
                    <div style="width:.4rem;height:.4rem;border-radius:50%;background:#2D6CDF;"></div>
                    <span style="font-size:.75rem;color:#737373;flex:1;">{{ $ps['interviewer'] }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#0C0C0C;">{{ $ps['score'] }}/5</span>
                    @if($ps['comment'])<span style="font-size:.72rem;color:#A8A8A8;margin-left:.5rem;">— {{ $ps['comment'] }}</span>@endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="ev-card">
        <div class="ev-card-body" style="text-align:center;padding:2rem;">
            <div style="font-size:.9rem;color:#A8A8A8;">No panel scores submitted yet. Ask interviewers to complete the scoring sheet from the Conduct page.</div>
            <a href="{{ route('employer.interviews.show', $interview->id) }}" style="display:inline-block;margin-top:1rem;font-size:.85rem;color:#2D6CDF;text-decoration:none;">&#8592; Go to Conduct page</a>
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
                    <a href="{{ route('employer.interviews.index') }}" style="color:#737373;font-size:.85rem;text-decoration:none;">Back to Pipeline</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection
