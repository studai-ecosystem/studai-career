@extends('layouts.dashboard')
@section('title', 'Interview Decision')

@push('styles')
<style>
.dc-page { padding:1.75rem; background:linear-gradient(135deg,#f0f4ff 0%,#f5f3ff 50%,#fff0fb 100%); min-height:100%; }
.dc-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 2px 16px rgba(99,102,241,.08); overflow:hidden; margin-bottom:1.25rem; padding:1.75rem; }
.decision-btn { width:100%; padding:1.1rem; border-radius:1rem; border:2px solid transparent; cursor:pointer; text-align:left; transition:all .2s; margin-bottom:.75rem; display:flex; align-items:flex-start; gap:1rem; }
.decision-btn:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
.decision-btn.hire    { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-color:#86efac; }
.decision-btn.reject  { background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-color:#fda4af; }
.decision-btn.next    { background:linear-gradient(135deg,#eff6ff,#dbeafe); border-color:#93c5fd; }
.decision-btn.hire:hover    { border-color:#22c55e; }
.decision-btn.reject:hover  { border-color:#f43f5e; }
.decision-btn.next:hover    { border-color:#3b82f6; }
.decision-icon { width:2.5rem; height:2.5rem; border-radius:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; }
.hire   .decision-icon { background:#bbf7d0; }
.reject .decision-icon { background:#fda4af; }
.next   .decision-icon { background:#bfdbfe; }
.decision-title { font-size:1rem; font-weight:800; color:#1a1a2e; margin-bottom:.2rem; }
.decision-sub   { font-size:.8rem; color:#6b7280; }
.ai-rec { display:inline-flex; align-items:center; gap:.5rem; padding:.4rem 1.1rem; border-radius:9999px; font-size:.82rem; font-weight:700; margin-bottom:1.25rem; }
.ai-rec.hire   { background:#f0fdf4; color:#16a34a; border:1.5px solid #bbf7d0; }
.ai-rec.next   { background:#eff6ff; color:#3b82f6; border:1.5px solid #bfdbfe; }
.ai-rec.silver { background:#fef9c3; color:#b45309; border:1.5px solid #fde68a; }
.ai-rec.reject { background:#fff1f2; color:#f43f5e; border:1.5px solid #fda4af; }
.form-ctrl { width:100%; padding:.6rem .875rem; border:1.5px solid #e5e7eb; border-radius:.75rem; font-size:.85rem; resize:vertical; box-sizing:border-box; }
.form-ctrl:focus { outline:none; border-color:#6366f1; }
.extra-fields { display:none; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid #f0f0f8; }
.btn-submit { padding:.7rem 2rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:.875rem; font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,.3); transition:transform .15s; }
.btn-submit:hover { transform:translateY(-1px); }
</style>
@endpush

@section('content')
<div class="dc-page">
<div style="max-width:760px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <a href="{{ route('employer.interviews.evaluate', $interview->id) }}" style="color:#6b7280;text-decoration:none;font-size:.85rem;">&#8592; Evaluate</a>
        <h1 style="font-size:1.4rem;font-weight:800;color:#1a1a2e;margin:0;">Phase 5 &mdash; Decision</h1>
    </div>

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.75rem 1.25rem;border-radius:.875rem;margin-bottom:1.25rem;font-size:.85rem;">&#10003; {{ session('success') }}</div>
    @endif

    {{-- Candidate summary --}}
    @php
        $app   = $interview->application;
        $score = $interview->rating;
        $aiRec = $interview->ai_recommendation ?? 'pending';
        $recMap = ['hire' => 'hire', 'next_round' => 'next', 'silver_medal' => 'silver', 'reject' => 'reject', 'pending' => 'next'];
        $recClass = $recMap[$aiRec] ?? 'next';
        $recLabel = ['hire' => 'Hire', 'next_round' => 'Next Round', 'silver_medal' => 'Silver Medalist', 'reject' => 'Reject', 'pending' => 'Pending Evaluation'][$aiRec] ?? ucfirst($aiRec);
    @endphp
    <div class="dc-card" style="padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:.875rem;">
                <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">{{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}</div>
                <div>
                    <div style="font-weight:700;color:#1a1a2e;">{{ $app->user->name ?? 'Guest' }}</div>
                    <div style="font-size:.78rem;color:#6b7280;">{{ $app->job->title ?? 'N/A' }} &bull; Round {{ $interview->round ?? 1 }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                @if($score)
                <div style="font-size:1.5rem;font-weight:900;color:#6366f1;">{{ number_format($score, 1) }}<span style="font-size:.9rem;color:#9ca3af;">/5</span></div>
                @endif
                <span class="ai-rec {{ $recClass }}">&#129302; AI: {{ $recLabel }}</span>
            </div>
        </div>
        @if($interview->interviewer_notes)
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f0f0f8;font-size:.82rem;color:#374151;white-space:pre-line;">{{ $interview->interviewer_notes }}</div>
        @endif
    </div>

    {{-- Decision form --}}
    <div class="dc-card">
        <div style="font-size:.9rem;font-weight:700;color:#1a1a2e;margin-bottom:1.25rem;">Select Decision</div>

        <form method="POST" action="{{ route('employer.interviews.decide.submit', $interview->id) }}" id="decisionForm">
            @csrf
            <input type="hidden" name="decision" id="decisionInput">

            <button type="button" class="decision-btn hire" onclick="selectDecision('hire')">
                <div class="decision-icon">&#127881;</div>
                <div>
                    <div class="decision-title">Hire</div>
                    <div class="decision-sub">Offer letter generated. Congratulations email fires to candidate + company summary.</div>
                </div>
            </button>

            <button type="button" class="decision-btn next" onclick="selectDecision('next_round')">
                <div class="decision-icon">&#128260;</div>
                <div>
                    <div class="decision-title">Next Round</div>
                    <div class="decision-sub">Loop back to Phase 2 — schedule the next interview round.</div>
                </div>
            </button>

            <button type="button" class="decision-btn reject" onclick="selectDecision('reject')">
                <div class="decision-icon">&#10060;</div>
                <div>
                    <div class="decision-title">Reject</div>
                    <div class="decision-sub">AI reason mail fires to candidate. If score &ge; 3.5, candidate is tagged as Silver Medalist for S.C.O.U.T.</div>
                </div>
            </button>

            {{-- Hire extras --}}
            <div id="extras-hire" class="extra-fields">
                <div style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.5rem;">Rejection reason (sent to candidate)</div>
                <textarea name="reason" class="form-ctrl" rows="3" placeholder="We've decided to proceed with another candidate because..."></textarea>
            </div>

            {{-- Next round extras --}}
            <div id="extras-next_round" class="extra-fields">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">Next Interview Type</label>
                        <select name="interview_type" class="form-ctrl">
                            <option value="video">Video</option>
                            <option value="technical">Technical</option>
                            <option value="panel">Panel</option>
                            <option value="onsite">Onsite</option>
                            <option value="behavioral">Behavioral</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">Schedule Date &amp; Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-ctrl" min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div style="margin-top:.875rem;">
                    <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">Meeting Link</label>
                    <input type="url" name="meeting_link" class="form-ctrl" placeholder="https://meet.google.com/...">
                </div>
            </div>

            <div id="submitArea" style="display:none;margin-top:1.5rem;">
                <button type="submit" class="btn-submit">Confirm Decision &#8594;</button>
            </div>
        </form>
    </div>

</div>
</div>

@push('scripts')
<script>
function selectDecision(d) {
    document.getElementById('decisionInput').value = d;
    document.querySelectorAll('.decision-btn').forEach(function(b) { b.style.boxShadow = ''; });
    document.querySelectorAll('.extra-fields').forEach(function(e) { e.style.display = 'none'; });
    var extraEl = document.getElementById('extras-' + d);
    if (extraEl) extraEl.style.display = 'block';
    document.getElementById('submitArea').style.display = 'block';
}
</script>
@endpush
@endsection
