@extends('layouts.dashboard')

@section('title', 'Auto Shortlisting - S.C.O.U.T.')

@push('styles')
<style>
@keyframes slFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@keyframes slPulse { 0%,100%{opacity:.6;transform:scale(1)} 50%{opacity:1;transform:scale(1.08)} }
@keyframes slSlideIn { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
@keyframes slSpin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

.sl-page {
    min-height:100vh;
    background:#EBF2FF;
    padding:2rem 1rem;
}
.sl-hero {
    background:#2D6CDF;
    border-radius:1.25rem;padding:2rem 2.5rem;margin-bottom:1.75rem;
    position:relative;overflow:hidden;box-shadow: none;
    animation:slSlideIn .5s ease both;
}
.sl-hero::before {
    content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;
    background:rgba(255,255,255,.12);
    border-radius:50%;animation:slFloat 6s ease-in-out infinite;
}
.sl-hero::after {
    content:'';position:absolute;bottom:-60px;left:15%;width:250px;height:250px;
    background:rgba(255,255,255,.08);
    border-radius:50%;animation:slFloat 8s ease-in-out infinite reverse;
}
.sl-hero-icon {
    width:3.25rem;height:3.25rem;background:rgba(255,255,255,.18);
    backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25);border-radius:.875rem;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.sl-badge {
    display:inline-flex;align-items:center;gap:.4rem;
    background:rgba(255,255,255,.18);backdrop-filter:blur(6px);
    border:1px solid rgba(255,255,255,.2);border-radius:2rem;
    padding:.3rem .9rem;font-size:.75rem;font-weight:700;color:#fff;letter-spacing:.04em;margin-top:.75rem;
}
.sl-card {
    background:#EBF2FF;
    border:1.5px solid rgba(20, 71, 186,.15);border-radius:1.125rem;
    box-shadow: none;padding:1.5rem;margin-bottom:1.5rem;
    animation:slSlideIn .45s ease both;
}
.sl-card-header {
    display:flex;align-items:center;gap:.625rem;font-size:1.05rem;font-weight:800;color:#0C0C0C;
    margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid rgba(20, 71, 186,.12);
}
.sl-card-icon {
    width:2.1rem;height:2.1rem;border-radius:.625rem;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.sl-step { border-radius:1rem;padding:1.125rem;border:1.5px solid transparent;transition:transform .2s,box-shadow .2s; }
.sl-step:hover { transform:translateY(-3px);box-shadow: none; }
.sl-step-num {
    width:2.25rem;height:2.25rem;border-radius:50%;
    display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.05rem;color:#fff;flex-shrink:0;
}
.sl-step ul { list-style:none;padding:0;margin:.625rem 0 0; }
.sl-step ul li { font-size:.82rem;padding:.2rem 0;display:flex;align-items:flex-start;gap:.5rem; }
.sl-step ul li::before { content:'•';flex-shrink:0;font-weight:900; }
.step-blue { background:#EBF2FF;border-color:#BFCFEE; }
.step-blue .sl-step-num { background:#1B57C4; }
.step-blue h3,.step-blue ul li { color:#1B57C4; } .step-blue ul li::before { color:#2D6CDF; }
.step-green { background:#EDFAF2;border-color:#A3D9B4; }
.step-green .sl-step-num { background:#1E8E3E; }
.step-green h3,.step-green ul li { color:#1E8E3E; } .step-green ul li::before { color:#1E8E3E; }
.step-pink { background:#FEF2F2;border-color:#FCA5A5; }
.step-pink .sl-step-num { background:#1B57C4; }
.step-pink h3,.step-pink ul li { color:#2D6CDF; } .step-pink ul li::before { color:#2D6CDF; }
.step-violet { background:#EBF2FF;border-color:#BFCFEE; }
.step-violet .sl-step-num { background:#2D6CDF; }
.step-violet h3,.step-violet ul li { color:#0C2E72; } .step-violet ul li::before { color:#2D6CDF; }
.sl-label { display:block;font-size:.825rem;font-weight:700;color:#0C2E72;margin-bottom:.5rem;letter-spacing:.02em; }
.sl-select,.sl-textarea {
    width:100%;padding:.65rem 1rem;background:#EBF2FF;
    border:1.5px solid rgba(20, 71, 186,.25);border-radius:.75rem;color:#0C0C0C;
    font-size:.875rem;transition:border-color .2s,box-shadow .2s;appearance:none;-webkit-appearance:none;
}
.sl-select:focus,.sl-textarea:focus {
    outline:none;border-color:#2D6CDF;box-shadow: none;
}
.sl-radio-label { display:flex;align-items:center;gap:.625rem;font-size:.875rem;color:#3D3D3D;cursor:pointer;padding:.3rem 0; }
.sl-radio-label input[type=radio] { accent-color:#2D6CDF;width:1.1rem;height:1.1rem; }
.btn-sl-run {
    width:100%;display:flex;align-items:center;justify-content:center;gap:.6rem;
    padding:.875rem 1.5rem;border-radius:.875rem;border:none;cursor:pointer;
    background:#2D6CDF;color:#fff;font-weight:800;font-size:.95rem;
    box-shadow: none;transition:all .2s;
}
.btn-sl-run:hover { transform:translateY(-2px);box-shadow: none; }
.btn-sl-run:disabled { opacity:.6;cursor:not-allowed;transform:none; }
.sl-spinner {
    width:3rem;height:3rem;border:3px solid rgba(20, 71, 186,.15);border-top-color:#2D6CDF;
    border-radius:50%;animation:slSpin .8s linear infinite;margin:0 auto;
}
.sl-stat-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.25rem; }
@media(min-width:768px){ .sl-stat-grid { grid-template-columns:repeat(3,1fr); } }
@media(min-width:1024px){ .sl-stat-grid { grid-template-columns:repeat(6,1fr); } }
.sl-stat { border-radius:.875rem;padding:1rem;text-align:center;border:1.5px solid transparent;transition:transform .2s; }
.sl-stat:hover { transform:translateY(-2px); }
.sl-stat-label { font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.25rem; }
.sl-stat-val { font-size:1.875rem;font-weight:900;line-height:1; }
.stat-gray { background:#F7F7F5;border-color:#E2E2E0; }
.stat-gray .sl-stat-label{color:#737373} .stat-gray .sl-stat-val{color:#0C0C0C}
.stat-blue { background:#EBF2FF;border-color:#BFCFEE; }
.stat-blue .sl-stat-label{color:#1B57C4} .stat-blue .sl-stat-val{color:#1B57C4}
.stat-green { background:#EDFAF2;border-color:#A3D9B4; }
.stat-green .sl-stat-label{color:#1E8E3E} .stat-green .sl-stat-val{color:#1E8E3E}
.stat-pink { background:#FEF2F2;border-color:#FCA5A5; }
.stat-pink .sl-stat-label{color:#1B57C4} .stat-pink .sl-stat-val{color:#2D6CDF}
.stat-viol { background:#EBF2FF;border-color:#BFCFEE; }
.stat-viol .sl-stat-label{color:#2D6CDF} .stat-viol .sl-stat-val{color:#0C2E72}
.stat-gold { background:#FFF8EC;border-color:#E37400; }
.stat-gold .sl-stat-label{color:#E37400} .stat-gold .sl-stat-val{color:#E37400}
.sl-candidate {
    border-radius:1rem;padding:1.25rem;
    background:#EBF2FF;
    border:2px solid rgba(20, 71, 186,.2);box-shadow: none;
    transition:transform .2s,box-shadow .2s;margin-bottom:1rem;
}
.sl-candidate:hover { transform:translateY(-2px);box-shadow: none; }
.sl-candidate.score-high{border-color:#A3D9B4} .sl-candidate.score-good{border-color:#BFCFEE} .sl-candidate.score-fair{border-color:#E37400}
.sl-rank {
    width:2.5rem;height:2.5rem;border-radius:50%;flex-shrink:0;
    background:#E37400;color:#fff;font-weight:900;font-size:1.1rem;
    display:flex;align-items:center;justify-content:center;
}
.sl-round-score { border-radius:.625rem;padding:.5rem;text-align:center;flex:1;min-width:0; }
.sl-round-score p { margin:0;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em; }
.sl-round-score span { font-size:1.25rem;font-weight:900;display:block; }
.rs-blue{background:#EBF2FF} .rs-blue p,.rs-blue span{color:#1B57C4}
.rs-green{background:#EDFAF2} .rs-green p,.rs-green span{color:#1E8E3E}
.rs-pink{background:#FEF2F2} .rs-pink p,.rs-pink span{color:#2D6CDF}
.rs-viol{background:#EBF2FF} .rs-viol p,.rs-viol span{color:#0C2E72}
.sl-list-item { font-size:.82rem;color:#3D3D3D;display:flex;align-items:flex-start;gap:.4rem; }
.sl-list-item::before { content:'•';color:#2D6CDF;font-weight:900;flex-shrink:0; }
.sl-rejection {
    background:#EBF2FF;
    border:1px solid rgba(185, 28, 28,.12);border-radius:.75rem;padding:.875rem;margin-bottom:.5rem;
}
.sl-rej-list { list-style:none;padding:0;margin:.5rem 0 0; }
.sl-rej-list li { font-size:.82rem;color:#3D3D3D;display:flex;align-items:flex-start;gap:.4rem;padding:.1rem 0; }
.sl-rej-list li::before { content:'•';color:#2D6CDF;font-weight:900;flex-shrink:0; }
.rec-strong { background:#EDFAF2;color:#1E8E3E;border:1px solid #A3D9B4;font-size:.75rem;font-weight:800;padding:.25rem .75rem;border-radius:2rem; }
.rec-recommend { background:#EBF2FF;color:#1B57C4;border:1px solid #BFCFEE;font-size:.75rem;font-weight:800;padding:.25rem .75rem;border-radius:2rem; }
.rec-consider { background:#FFF8EC;color:#E37400;border:1px solid #E37400;font-size:.75rem;font-weight:800;padding:.25rem .75rem;border-radius:2rem; }
.rec-default { background:#F0F0EE;color:#3D3D3D;border:1px solid #C8C8C5;font-size:.75rem;font-weight:800;padding:.25rem .75rem;border-radius:2rem; }
.sl-round-hdr { display:flex;align-items:center;gap:.625rem;font-weight:800;font-size:.9rem;margin-bottom:.75rem;flex-wrap:wrap; }
.sl-round-dot { width:1.5rem;height:1.5rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:900;color:#fff;flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="sl-page">
<div style="max-width:1200px;margin:0 auto">

    {{-- Hero --}}
    <div class="sl-hero">
        <div style="display:flex;align-items:flex-start;gap:1rem;position:relative;z-index:1">
            <div class="sl-hero-icon">
                <svg style="width:1.6rem;height:1.6rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:1.875rem;font-weight:900;color:#fff;margin:0;line-height:1.2">Multi-Stage Automated Shortlisting</h1>
                <p style="color:rgba(255,255,255,.8);font-size:.9rem;margin:.2rem 0 0">AI-powered 4-round evaluation pipeline</p>
                <div class="sl-badge">
                    <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    S.C.O.U.T. AI ACTIVE
                </div>
            </div>
        </div>
    </div>

    {{-- Evaluation Pipeline --}}
    <div class="sl-card">
        <div class="sl-card-header">
            <div class="sl-card-icon" style="background:#2D6CDF">
                <svg style="width:1rem;height:1rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            Evaluation Pipeline
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem">
            <div class="sl-step step-blue">
                <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.25rem">
                    <div class="sl-step-num">1</div>
                    <h3 style="margin:0;font-size:.9rem;font-weight:800">Basic Qualification</h3>
                </div>
                <ul>
                    <li>Education verification</li>
                    <li>Experience threshold</li>
                    <li>Legal compliance</li>
                    <li>Location compatibility</li>
                </ul>
            </div>
            <div class="sl-step step-green">
                <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.25rem">
                    <div class="sl-step-num">2</div>
                    <h3 style="margin:0;font-size:.9rem;font-weight:800">Skills &amp; Competency</h3>
                </div>
                <ul>
                    <li>Technical skills match</li>
                    <li>Soft skills evaluation</li>
                    <li>Success trait alignment</li>
                    <li>Competency scoring</li>
                </ul>
            </div>
            <div class="sl-step step-pink">
                <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.25rem">
                    <div class="sl-step-num">3</div>
                    <h3 style="margin:0;font-size:.9rem;font-weight:800">Cultural Fit</h3>
                </div>
                <ul>
                    <li>Value alignment</li>
                    <li>Work style compatibility</li>
                    <li>Communication analysis</li>
                    <li>Team dynamics prediction</li>
                </ul>
            </div>
            <div class="sl-step step-violet">
                <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.25rem">
                    <div class="sl-step-num">4</div>
                    <h3 style="margin:0;font-size:.9rem;font-weight:800">Potential &amp; Growth</h3>
                </div>
                <ul>
                    <li>Learning agility</li>
                    <li>Career trajectory</li>
                    <li>Future potential</li>
                    <li>Long-term value</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Configure Shortlisting --}}
    <div class="sl-card" id="form-card">
        <div class="sl-card-header">
            <div class="sl-card-icon" style="background:#E37400">
                <svg style="width:1rem;height:1rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            Configure Shortlisting
        </div>

        <form id="shortlisting-form" style="display:flex;flex-direction:column;gap:1.125rem">
            <div>
                <label class="sl-label">Select Job Position *</label>
                <div style="position:relative">
                    <select id="job-select" required class="sl-select" style="padding-right:2.5rem">
                        <option value="">Select a job position…</option>
                        @forelse($jobs as $job)
                            <option value="{{ $job->id }}">{{ $job->title }} ({{ $job->pending_count ?? 0 }} applications)</option>
                        @empty
                            <option value="" disabled>No jobs found — post a job first</option>
                        @endforelse
                    </select>
                    <div style="position:absolute;right:.875rem;top:50%;transform:translateY(-50%);pointer-events:none">
                        <svg style="width:16px;height:16px;color:#2D6CDF" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="sl-label">Select Applications</label>
                <div style="display:flex;flex-direction:column;gap:.375rem">
                    <label class="sl-radio-label">
                        <input type="radio" name="selection-method" value="all" checked>
                        <span>All pending applications for this job</span>
                    </label>
                    <label class="sl-radio-label">
                        <input type="radio" name="selection-method" value="specific">
                        <span>Select specific applications</span>
                    </label>
                </div>
            </div>

            <div id="specific-applications-container" style="display:none">
                <label class="sl-label">Application IDs (comma-separated)</label>
                <textarea id="application-ids" rows="3" class="sl-textarea" style="font-family:monospace;font-size:.85rem;resize:vertical" placeholder="e.g., 123, 124, 125"></textarea>
            </div>

            <button type="submit" class="btn-sl-run" id="run-btn">
                <svg style="width:1.1rem;height:1.1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="run-btn-text">Run Shortlisting Pipeline</span>
            </button>
        </form>
    </div>

    {{-- Loading --}}
    <div id="loading-state" style="display:none" class="sl-card">
        <div style="text-align:center;padding:2rem">
            <div class="sl-spinner"></div>
            <p style="color:#3D3D3D;font-weight:700;margin:1.25rem 0 .25rem">Running multi-stage evaluation pipeline…</p>
            <p style="color:#A8A8A8;font-size:.875rem">This may take a few moments</p>
        </div>
    </div>

    {{-- Results --}}
    <div id="results-container" style="display:none">

        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-icon" style="background:#1E8E3E">
                    <svg style="width:1rem;height:1rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                Shortlisting Summary
            </div>
            <div class="sl-stat-grid">
                <div class="sl-stat stat-gray"><div class="sl-stat-label">Total Evaluated</div><div class="sl-stat-val" id="stat-total">0</div></div>
                <div class="sl-stat stat-blue"><div class="sl-stat-label">Round 1 Pass</div><div class="sl-stat-val" id="stat-round1">0</div></div>
                <div class="sl-stat stat-green"><div class="sl-stat-label">Round 2 Pass</div><div class="sl-stat-val" id="stat-round2">0</div></div>
                <div class="sl-stat stat-pink"><div class="sl-stat-label">Round 3 Pass</div><div class="sl-stat-val" id="stat-round3">0</div></div>
                <div class="sl-stat stat-viol"><div class="sl-stat-label">Round 4 Pass</div><div class="sl-stat-val" id="stat-round4">0</div></div>
                <div class="sl-stat stat-gold"><div class="sl-stat-label">Shortlisted</div><div class="sl-stat-val" id="stat-shortlisted">0</div></div>
            </div>
            <div style="padding-top:.875rem;border-top:1px solid rgba(20, 71, 186,.1);font-size:.82rem;color:#737373">
                Processing Time: <span id="processing-time" style="font-weight:800;color:#0C0C0C">0s</span>
            </div>
        </div>

        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-icon" style="background:#E37400">
                    <svg style="width:1rem;height:1rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                Shortlisted Candidates
            </div>
            <div id="shortlisted-list"></div>
        </div>

        <div class="sl-card">
            <div class="sl-card-header">
                <div class="sl-card-icon" style="background:#2D6CDF">
                    <svg style="width:1rem;height:1rem;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                Rejected Candidates by Round
            </div>
            <div style="display:flex;flex-direction:column;gap:1.25rem">
                <div>
                    <div class="sl-round-hdr">
                        <div class="sl-round-dot" style="background:#1B57C4">1</div>
                        <span style="color:#1B57C4">Round 1 — Basic Qualification</span>
                        <span style="background:rgba(15, 55, 153,.1);color:#1B57C4;padding:.15rem .6rem;border-radius:2rem;font-size:.72rem">(<span id="round1-count">0</span> rejected)</span>
                    </div>
                    <div id="round1-rejections"></div>
                </div>
                <div>
                    <div class="sl-round-hdr">
                        <div class="sl-round-dot" style="background:#1E8E3E">2</div>
                        <span style="color:#1E8E3E">Round 2 — Skills &amp; Competency</span>
                        <span style="background:rgba(15, 107, 49,.1);color:#1E8E3E;padding:.15rem .6rem;border-radius:2rem;font-size:.72rem">(<span id="round2-count">0</span> rejected)</span>
                    </div>
                    <div id="round2-rejections"></div>
                </div>
                <div>
                    <div class="sl-round-hdr">
                        <div class="sl-round-dot" style="background:#1B57C4">3</div>
                        <span style="color:#2D6CDF">Round 3 — Cultural Fit</span>
                        <span style="background:rgba(219,39,119,.1);color:#2D6CDF;padding:.15rem .6rem;border-radius:2rem;font-size:.72rem">(<span id="round3-count">0</span> rejected)</span>
                    </div>
                    <div id="round3-rejections"></div>
                </div>
                <div>
                    <div class="sl-round-hdr">
                        <div class="sl-round-dot" style="background:#2D6CDF">4</div>
                        <span style="color:#0C2E72">Round 4 — Potential &amp; Growth</span>
                        <span style="background:rgba(20, 71, 186,.1);color:#0C2E72;padding:.15rem .6rem;border-radius:2rem;font-size:.72rem">(<span id="round4-count">0</span> rejected)</span>
                    </div>
                    <div id="round4-rejections"></div>
                </div>
            </div>
        </div>

        <div style="text-align:center;padding-bottom:1.5rem">
            <button onclick="resetForm()" style="display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 1.5rem;border-radius:.875rem;border:1.5px solid rgba(20, 71, 186,.25);background:#EBF2FF;color:#2D6CDF;font-weight:700;font-size:.875rem;cursor:pointer;transition:all .2s" onmouseover="this.style.borderColor='#2D6CDF'" onmouseout="this.style.borderColor='rgba(20, 71, 186,.25)'">
                <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Run Another Analysis
            </button>
        </div>

    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="selection-method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('specific-applications-container').style.display =
                this.value === 'specific' ? 'block' : 'none';
        });
    });
    document.getElementById('shortlisting-form').addEventListener('submit', function(e) {
        e.preventDefault();
        runShortlisting();
    });
});

async function runShortlisting() {
    const jobId = document.getElementById('job-select').value;
    if (!jobId) { alert('Please select a job position'); return; }

    const selectionMethod = document.querySelector('input[name="selection-method"]:checked').value;
    let applicationIds;

    if (selectionMethod === 'specific') {
        const idsInput = document.getElementById('application-ids').value;
        if (!idsInput.trim()) { alert('Please enter application IDs'); return; }
        applicationIds = idsInput.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
    } else {
        try {
            const res = await fetch(`/employer/scout/jobs/${jobId}/applications`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            applicationIds = (data.applications || []).map(app => app.id);
        } catch (err) {
            console.error(err);
            alert('Failed to fetch applications: ' + err.message);
            return;
        }
    }

    if (!applicationIds || applicationIds.length === 0) {
        alert('No pending applications found for this job. Make sure candidates have applied.');
        return;
    }

    document.getElementById('form-card').style.display = 'none';
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('results-container').style.display = 'none';

    try {
        const res = await fetch('/employer/scout/shortlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ job_id: parseInt(jobId), application_ids: applicationIds })
        });
        const result = await res.json();
        if (!res.ok || result.success === false) throw new Error(result.message || 'Shortlisting pipeline failed');
        displayResults(result);
    } catch (err) {
        console.error(err);
        alert('Shortlisting failed: ' + err.message);
        document.getElementById('form-card').style.display = 'block';
        document.getElementById('loading-state').style.display = 'none';
    }
}

function resetForm() {
    document.getElementById('results-container').style.display = 'none';
    document.getElementById('form-card').style.display = 'block';
}

function displayResults(result) {
    const raw = result.data || result;
    const rr  = raw.rejected_by_round || {};
    const data = {
        shortlisted: raw.shortlisted || [],
        rejected_by_round: {
            round_1: rr.round_1 || [], round_2: rr.round_2 || [],
            round_3: rr.round_3 || [], round_4: rr.round_4 || [],
        }
    };
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('results-container').style.display = 'block';
    document.getElementById('stat-total').textContent       = raw.total_applications || 0;
    document.getElementById('stat-round1').textContent      = raw.round_1_passed || 0;
    document.getElementById('stat-round2').textContent      = raw.round_2_passed || 0;
    document.getElementById('stat-round3').textContent      = raw.round_3_passed || 0;
    document.getElementById('stat-round4').textContent      = raw.round_4_passed || 0;
    document.getElementById('stat-shortlisted').textContent = data.shortlisted.length;
    document.getElementById('processing-time').textContent  = (raw.processing_time || 0) + 's';

    const shortlistedContainer = document.getElementById('shortlisted-list');
    if (data.shortlisted.length === 0) {
        shortlistedContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:#A8A8A8;font-size:.875rem">No candidates passed all 4 rounds</div>';
    } else {
        shortlistedContainer.innerHTML = data.shortlisted.map((c, i) => {
            const scoreClass = c.overall_score >= 85 ? 'score-high' : c.overall_score >= 75 ? 'score-good' : 'score-fair';
            const scoreColor = c.overall_score >= 85 ? '#1E8E3E' : c.overall_score >= 75 ? '#1B57C4' : '#E37400';
            return `
            <div class="sl-candidate ${scoreClass}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem">
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <div class="sl-rank">${i + 1}</div>
                        <div>
                            <div style="font-weight:800;font-size:1rem;color:#0C0C0C">${c.candidate_name}</div>
                            <div style="font-size:.75rem;color:#A8A8A8">Application #${c.application_id}</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:2rem;font-weight:900;color:${scoreColor};line-height:1">${c.overall_score}</div>
                        <div style="font-size:.7rem;color:#A8A8A8;text-transform:uppercase;letter-spacing:.05em">Overall</div>
                    </div>
                </div>
                <div style="margin-bottom:.875rem"><span class="${getRecClass(c.recommendation)}">${c.recommendation || 'N/A'}</span></div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:.875rem">
                    <div class="sl-round-score rs-blue"><p>Round 1</p><span>${c.round_scores?.round_1 ?? '—'}</span></div>
                    <div class="sl-round-score rs-green"><p>Round 2</p><span>${c.round_scores?.round_2 ?? '—'}</span></div>
                    <div class="sl-round-score rs-pink"><p>Round 3</p><span>${c.round_scores?.round_3 ?? '—'}</span></div>
                    <div class="sl-round-score rs-viol"><p>Round 4</p><span>${c.round_scores?.round_4 ?? '—'}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem">
                    <div>
                        <div style="font-size:.75rem;font-weight:800;color:#1E8E3E;margin-bottom:.375rem">Top Strengths</div>
                        ${(c.strengths||[]).slice(0,3).map(s=>`<div class="sl-list-item">${s}</div>`).join('')||'<div style="color:#A8A8A8;font-size:.82rem">None noted</div>'}
                    </div>
                    <div>
                        <div style="font-size:.75rem;font-weight:800;color:#E37400;margin-bottom:.375rem">Considerations</div>
                        ${(c.concerns||[]).length>0?(c.concerns||[]).slice(0,3).map(co=>`<div class="sl-list-item">${co}</div>`).join(''):'<div style="color:#A8A8A8;font-size:.82rem">None noted</div>'}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    displayRejections('round1', data.rejected_by_round.round_1);
    displayRejections('round2', data.rejected_by_round.round_2);
    displayRejections('round3', data.rejected_by_round.round_3);
    displayRejections('round4', data.rejected_by_round.round_4);
    document.getElementById('round1-count').textContent = data.rejected_by_round.round_1.length;
    document.getElementById('round2-count').textContent = data.rejected_by_round.round_2.length;
    document.getElementById('round3-count').textContent = data.rejected_by_round.round_3.length;
    document.getElementById('round4-count').textContent = data.rejected_by_round.round_4.length;
    document.getElementById('results-container').scrollIntoView({ behavior: 'smooth' });
}

function displayRejections(round, rejections) {
    const container = document.getElementById(`${round}-rejections`);
    if (!rejections || rejections.length === 0) {
        container.innerHTML = '<p style="font-size:.82rem;color:#A8A8A8;font-style:italic">No rejections at this round</p>';
        return;
    }
    container.innerHTML = rejections.map(r => {
        const reasons = Array.isArray(r.reason) ? r.reason : [r.reason || 'See evaluation'];
        return `<div class="sl-rejection">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <div style="font-weight:700;color:#0C0C0C;font-size:.875rem">${r.candidate_name}</div>
                    <div style="font-size:.72rem;color:#A8A8A8">Application #${r.application_id}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:1.25rem;font-weight:900;color:#3D3D3D">${r.score}</div>
                    <div style="font-size:.68rem;color:#A8A8A8;text-transform:uppercase;letter-spacing:.05em">Score</div>
                </div>
            </div>
            <div style="margin-top:.625rem">
                <div style="font-size:.75rem;font-weight:800;color:#2D6CDF;margin-bottom:.25rem">Reasons:</div>
                <ul class="sl-rej-list">${reasons.map(re=>`<li>${re}</li>`).join('')}</ul>
            </div>
        </div>`;
    }).join('');
}

function getRecClass(rec) {
    if (!rec) return 'rec-default';
    if (rec.includes('STRONG HIRE')) return 'rec-strong';
    if (rec.includes('RECOMMEND')) return 'rec-recommend';
    if (rec.includes('CONSIDER')) return 'rec-consider';
    return 'rec-default';
}
</script>
@endpush
