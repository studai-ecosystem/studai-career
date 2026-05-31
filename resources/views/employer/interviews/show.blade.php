@extends('layouts.dashboard')
@section('title', 'Conduct Interview')

@push('styles')
<style>
.iv-page { padding:1.75rem; background:#EBF2FF; min-height:100%; }
.iv-main { display:grid; grid-template-columns:1fr 380px; gap:1.25rem; align-items:start; }
.iv-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(20, 71, 186,.1); box-shadow: none; overflow:hidden; }
.iv-card-hd { padding:1rem 1.25rem; border-bottom:1px solid #EBF2FF; display:flex; align-items:center; justify-content:space-between; }
.iv-card-title { font-size:.9rem; font-weight:700; color:#0C0C0C; }
.iv-card-body { padding:1.25rem; }
.video-room { background:#0C0C0C; border-radius:1rem; aspect-ratio:16/9; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#fff; margin-bottom:1.25rem; }
.q-item { padding:.875rem 1rem; border:1.5px solid #E2E2E0; border-radius:.875rem; margin-bottom:.75rem; }
.q-item:hover { border-color:#2D6CDF; background:#EBF2FF; }
.q-num { width:1.5rem; height:1.5rem; border-radius:50%; background:#EBF2FF; color:#2D6CDF; font-size:.72rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.star { font-size:1.25rem; cursor:pointer; color:#C8C8C5; transition:color .1s; }
.star.on { color:#E37400; }
.iv-status { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .875rem; border-radius:9999px; font-size:.75rem; font-weight:700; }
.iv-status.scheduled { background:#EBF2FF; color:#2D6CDF; }
.iv-status.completed { background:#EDFAF2; color:#1E8E3E; }
.btn-prim { padding:.55rem 1.25rem; background:#2D6CDF; color:#fff; border:none; border-radius:.875rem; font-size:.82rem; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; transition:transform .15s; }
.btn-prim:hover { transform:translateY(-1px); }
.form-ctrl { width:100%; padding:.5rem .75rem; border:1.5px solid #E2E2E0; border-radius:.625rem; font-size:.82rem; resize:vertical; box-sizing:border-box; }
.form-ctrl:focus { outline:none; border-color:#2D6CDF; }
</style>
@endpush

@section('content')
<div class="iv-page">
<div style="max-width:1200px;margin:0 auto;">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <a href="{{ route('employer.interviews.index') }}" style="color:#737373;text-decoration:none;font-size:.85rem;">&#8592; Pipeline</a>
            <div>
                <h1 style="font-size:1.3rem;font-weight:800;color:#0C0C0C;margin:0;">Phase 3 &mdash; Conduct Interview</h1>
                <div style="font-size:.8rem;color:#737373;">{{ ucfirst($interview->interview_type) }} &bull; Round {{ $interview->round ?? 1 }} &bull; {{ $interview->duration_minutes }} min</div>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;align-items:center;">
            <span class="iv-status {{ $interview->status }}">{{ ucfirst($interview->status) }}</span>
            @if($interview->status === 'scheduled')
            <form method="POST" action="{{ route('employer.interviews.complete', $interview->id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-prim" style="background:#1E8E3E;">&#10003; Mark Complete</button>
            </form>
            @elseif($interview->status === 'completed' && !$interview->ai_score_summary)
            <a href="{{ route('employer.interviews.evaluate', $interview->id) }}" class="btn-prim" style="background:#E37400;">Go to Evaluate</a>
            @elseif($interview->status === 'completed' && $interview->ai_score_summary)
            <a href="{{ route('employer.interviews.decide', $interview->id) }}" class="btn-prim" style="background:#1E8E3E;">Go to Decide</a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div style="background:#EDFAF2;border:1px solid #A3D9B4;color:#1E8E3E;padding:.75rem 1.25rem;border-radius:.875rem;margin-bottom:1.25rem;font-size:.85rem;">&#10003; {{ session('success') }}</div>
    @endif

    <div class="iv-main">
        <div>
            @if($interview->meeting_link)
            <div class="iv-card" style="margin-bottom:1.25rem;">
                <div class="iv-card-hd">
                    <div class="iv-card-title">&#128249; Video Room</div>
                    <a href="{{ $interview->meeting_link }}" target="_blank" class="btn-prim" style="font-size:.75rem;padding:.35rem .875rem;">Join &#8599;</a>
                </div>
                <div class="iv-card-body">
                    <div class="video-room">
                        <svg style="width:2.5rem;height:2.5rem;opacity:.4;margin-bottom:.5rem" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p style="font-size:.85rem;opacity:.7;margin:0;">Click "Join" to open video session</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="iv-card">
                <div class="iv-card-hd"><div class="iv-card-title">&#128221; Question Set</div></div>
                <div class="iv-card-body">
                    @forelse($interview->question_set ?? [] as $q)
                    <div class="q-item">
                        <div style="display:flex;gap:.65rem;">
                            <div class="q-num">{{ $loop->iteration }}</div>
                            <div style="font-size:.875rem;color:#0C0C0C;">{{ $q['text'] }}</div>
                        </div>
                    </div>
                    @empty
                    <p style="color:#A8A8A8;font-size:.85rem;">No questions defined.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="iv-card" style="margin-bottom:1.25rem;">
                <div class="iv-card-hd"><div class="iv-card-title">Candidate</div></div>
                <div class="iv-card-body">
                    @php $app = $interview->application; @endphp
                    <div style="display:flex;gap:.75rem;align-items:center;margin-bottom:.875rem;">
                        <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:#2D6CDF;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
                        <div>
                            <div style="font-weight:700;color:#0C0C0C;">{{ $app->user->name ?? 'Guest' }}</div>
                            <div style="font-size:.78rem;color:#737373;">{{ $app->user->email ?? '' }}</div>
                        </div>
                    </div>
                    <div style="font-size:.82rem;color:#3D3D3D;margin-bottom:.875rem;"><span style="color:#A8A8A8;">Position:</span> {{ $app->job->title ?? 'N/A' }}</div>
                    <a href="{{ route('employer.ats.show', $app->id) }}" style="font-size:.78rem;color:#2D6CDF;text-decoration:none;">View application &#8599;</a>
                </div>
            </div>

            <div class="iv-card">
                <div class="iv-card-hd"><div class="iv-card-title">&#11088; Panel Scoring</div></div>
                <div class="iv-card-body">
                    <form method="POST" action="{{ route('employer.interviews.scores', $interview->id) }}">
                        @csrf
                        @forelse($interview->question_set ?? [] as $i => $q)
                        <div style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid #EBF2FF;">
                            <div style="font-size:.75rem;font-weight:700;color:#3D3D3D;margin-bottom:.35rem;">Q{{ $i+1 }}: {{ Str::limit($q['text'], 55) }}</div>
                            <input type="hidden" name="scores[{{ $i }}][key]" value="{{ $q['key'] }}">
                            <div style="display:flex;gap:.25rem;margin-bottom:.35rem;">
                                @for($s = 1; $s <= 5; $s++)
                                <label style="cursor:pointer;">
                                    <input type="radio" name="scores[{{ $i }}][score]" value="{{ $s }}" style="display:none;" required>
                                    <span class="star">&#9733;</span>
                                </label>
                                @endfor
                            </div>
                            <textarea name="scores[{{ $i }}][comment]" class="form-ctrl" rows="1" placeholder="Comment..."></textarea>
                        </div>
                        @empty
                        <p style="color:#A8A8A8;font-size:.82rem;">No questions to score.</p>
                        @endforelse
                        @if($interview->question_set)
                        <button type="submit" class="btn-prim" style="width:100%;text-align:center;">Save Scores</button>
                        @endif
                    </form>
                    @if($interview->panelScores->count() > 0)
                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #EBF2FF;text-align:center;">
                        <div style="font-size:.75rem;color:#A8A8A8;">Current avg score</div>
                        <div style="font-size:1.75rem;font-weight:800;color:#2D6CDF;">{{ number_format($interview->panelScores->avg('score'), 1) }}/5</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($interview->notes)
            <div class="iv-card" style="margin-top:1.25rem;">
                <div class="iv-card-hd"><div class="iv-card-title">Panel Brief</div></div>
                <div class="iv-card-body"><p style="font-size:.85rem;color:#3D3D3D;margin:0;white-space:pre-line;">{{ $interview->notes }}</p></div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@push('scripts')
<script>
document.querySelectorAll('form').forEach(function(form) {
    form.querySelectorAll('label').forEach(function(lbl) {
        var radio = lbl.querySelector('input[type=radio]');
        if (!radio) return;
        lbl.addEventListener('click', function() {
            var name = radio.getAttribute('name');
            form.querySelectorAll('input[name="'+name+'"]').forEach(function(r, i) {
                var s = r.closest('label').querySelector('.star');
                if (s) s.classList.toggle('on', r.value <= radio.value);
            });
        });
    });
});
</script>
@endpush
@endsection
