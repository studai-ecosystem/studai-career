@extends('layouts.dashboard')

@section('title', 'Corporate DNA Dashboard - S.C.O.U.T.')

@push('styles')
<style>
@keyframes dnaFloat { 0%,100%{transform:translateY(0) rotate(0)} 50%{transform:translateY(-12px) rotate(3deg)} }
@keyframes dnaPulse { 0%,100%{opacity:.6;transform:scale(1)} 50%{opacity:1;transform:scale(1.05)} }
@keyframes dnaSlideIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes dnaShimmer { to{background-position:-200% center} }

.dna-page {
    background: #EBF2FF;
    min-height:100vh; padding:1.5rem;
}
.dna-hero {
    background:#2D6CDF;
    border-radius:1.5rem; padding:1.75rem 2rem;
    box-shadow: none;
    position:relative; overflow:hidden;
    animation:dnaSlideIn .4s ease both;
}
.dna-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:220px; height:220px; border-radius:50%;
    background:rgba(255,255,255,.08); pointer-events:none;
}
.dna-hero::after {
    content:''; position:absolute; bottom:-60px; left:20%;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.dna-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.3rem .8rem; border-radius:9999px; font-size:.7rem; font-weight:800; letter-spacing:.06em;
    background:rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.3);
    margin-bottom:.75rem;
}
.dna-title { font-size:1.6rem; font-weight:900; color:#fff; letter-spacing:-.03em; }
.dna-sub   { font-size:.84rem; color:rgba(255,255,255,.78); margin-top:.25rem; font-weight:500; }

.btn-dna-refresh {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.1rem; border-radius:.875rem; font-size:.8rem; font-weight:700;
    background:rgba(255,255,255,.18); color:#fff; border:1.5px solid rgba(255,255,255,.35);
    backdrop-filter:blur(6px); cursor:pointer; transition:all .2s;
}
.btn-dna-refresh:hover { background:rgba(255,255,255,.28); transform:translateY(-2px); }
.btn-dna-analyze {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.25rem; border-radius:.875rem; font-size:.8rem; font-weight:700;
    background:#fff; color:#2D6CDF; border:none; cursor:pointer;
    box-shadow: none; transition:all .2s;
}
.btn-dna-analyze:hover { transform:translateY(-2px); box-shadow: none; }
.btn-dna-analyze:disabled { opacity:.6; cursor:not-allowed; transform:none; }

/* Stat cards */
.dna-stat-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; }
@media(min-width:768px){ .dna-stat-grid { grid-template-columns:repeat(4,1fr); } }
.dna-stat {
    border-radius:1.25rem; padding:1.25rem; position:relative; overflow:hidden;
    box-shadow: none; color:#fff; animation:dnaSlideIn .4s ease both;
    transition:transform .25s, box-shadow .25s;
}
.dna-stat:hover { transform:translateY(-5px) scale(1.02); box-shadow: none; }
.dna-stat::before { content:''; position:absolute; inset:0; background:rgba(255,255,255,.16); pointer-events:none; }
.dna-stat::after { content:''; position:absolute; bottom:-25px; right:-25px; width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,.1); animation:dnaFloat 6s ease-in-out infinite; }
.stat-dna-pink   { background:#2D6CDF; }
.stat-dna-violet { background:#2D6CDF; }
.stat-dna-green  { background:#1E8E3E; }
.stat-dna-amber  { background:#E37400; }
.dna-stat-num  { font-size:2.25rem; font-weight:900; line-height:1; letter-spacing:-.04em; }
.dna-stat-lbl  { font-size:.72rem; color:rgba(255,255,255,.8); margin-top:.2rem; font-weight:600; letter-spacing:.03em; }
.dna-stat-bar-bg   { height:5px; border-radius:9999px; background:rgba(255,255,255,.2); margin-top:.75rem; overflow:hidden; }
.dna-stat-bar-fill { height:100%; border-radius:9999px; background:rgba(255,255,255,.85); transition:width 1.2s cubic-bezier(.22,.68,0,1.2); }

/* Section cards */
.dna-card {
    background:rgba(255,255,255,.88);
    border:1.5px solid rgba(20, 71, 186,.14); border-radius:1.25rem;
    box-shadow: none; overflow:hidden;
    animation:dnaSlideIn .5s ease both;
}
.dna-card-header {
    padding:1rem 1.5rem;
    border-bottom:1.5px solid rgba(20, 71, 186,.1);
    background:rgba(20, 71, 186,.06);
    display:flex; align-items:center; gap:.65rem;
}
.dna-card-icon {
    width:2rem; height:2rem; border-radius:.625rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    box-shadow: none;
}
.dna-card-title { font-size:.9rem; font-weight:800; color:#0C0C0C; letter-spacing:-.01em; }
.dna-card-body  { padding:1.25rem 1.5rem; }

/* Archetype chips */
.archetype-chip {
    display:flex; align-items:center; gap:.6rem;
    padding:.65rem .9rem; border-radius:.75rem;
    background:#EBF2FF;
    border:1px solid rgba(20, 71, 186,.18);
    font-size:.82rem; font-weight:600; color:#1B57C4;
    transition:transform .2s, box-shadow .2s;
}
.archetype-chip:hover { transform:translateX(4px); box-shadow: none; }

/* Trait bars */
.trait-row { padding:.6rem 0; border-bottom:1px solid rgba(20, 71, 186,.07); }
.trait-row:last-child { border-bottom:none; }
.trait-label { font-size:.8rem; font-weight:600; color:#0C0C0C; margin-bottom:.3rem; display:flex; justify-content:space-between; }
.trait-bar-bg { height:7px; background:rgba(20, 71, 186,.1); border-radius:9999px; overflow:hidden; }
.trait-bar-fill { height:100%; border-radius:9999px; background:#2D6CDF; background-size:200%; animation:dnaShimmer 3s linear infinite; transition:width .9s cubic-bezier(.22,.68,0,1.2); }

/* Core value chips */
.value-chip {
    display:inline-flex; padding:.4rem .9rem; border-radius:9999px;
    font-size:.78rem; font-weight:700;
    background:#EBF2FF;
    color:#2D6CDF; border:1px solid rgba(20, 71, 186,.2);
    transition:transform .15s, box-shadow .15s;
}
.value-chip:hover { transform:scale(1.06); box-shadow: none; }

/* Work style / communication rows */
.pattern-row {
    display:flex; align-items:center; gap:.6rem;
    padding:.55rem .75rem; border-radius:.625rem; font-size:.82rem; font-weight:500; color:#3D3D3D;
    transition:background .15s;
}
.pattern-row:hover { background:rgba(20, 71, 186,.06); }
.pattern-dot { width:.5rem; height:.5rem; border-radius:50%; flex-shrink:0; }

/* Metadata card */
.meta-stat { padding:.75rem 1rem; border-radius:.875rem; text-align:center; }
.meta-label { font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#A8A8A8; margin-bottom:.3rem; }
.meta-val   { font-size:1.35rem; font-weight:900; color:#0C0C0C; letter-spacing:-.02em; }

/* Empty state */
.dna-empty {
    text-align:center; padding:5rem 2rem;
    background:rgba(255,255,255,.9);
    border:1.5px solid rgba(20, 71, 186,.14); border-radius:1.5rem;
    box-shadow: none;
}
.dna-empty-icon {
    width:5rem; height:5rem; border-radius:1.5rem; margin:0 auto 1.5rem;
    background:#EBF2FF;
    display:flex; align-items:center; justify-content:center;
    box-shadow: none;
    animation:dnaPulse 2.5s ease-in-out infinite;
}
</style>
@endpush

@section('content')
<div class="dna-page">
<div class="space-y-5">

    {{-- Hero --}}
    <div class="dna-hero">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="dna-badge">
                    <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    S.C.O.U.T. AI
                </div>
                <div class="dna-title">Corporate DNA Dashboard</div>
                <div class="dna-sub">AI-powered organizational analysis for smarter hiring</div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <button onclick="refreshAnalysis()" class="btn-dna-refresh" id="refresh-btn">
                    <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
                <button onclick="analyzeDNA(event)" class="btn-dna-analyze" id="analyze-btn">
                    <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span id="analyze-btn-text">Run DNA Analysis</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="dna-stat-grid">
        <div class="dna-stat stat-dna-pink" style="animation-delay:.05s">
            <div style="font-size:.7rem;font-weight:700;letter-spacing:.06em;color:rgba(255,255,255,.75);margin-bottom:.4rem;text-transform:uppercase">DNA Health Score</div>
            <div class="dna-stat-num" id="health-score">--</div>
            <div class="dna-stat-bar-bg"><div class="dna-stat-bar-fill" id="health-progress" style="width:0%"></div></div>
        </div>
        <div class="dna-stat stat-dna-violet" style="animation-delay:.1s">
            <div style="font-size:.7rem;font-weight:700;letter-spacing:.06em;color:rgba(255,255,255,.75);margin-bottom:.4rem;text-transform:uppercase">Completeness</div>
            <div class="dna-stat-num" id="completeness-score">--</div>
            <div class="dna-stat-lbl" id="completeness-status">Loading…</div>
        </div>
        <div class="dna-stat stat-dna-green" style="animation-delay:.15s">
            <div style="font-size:.7rem;font-weight:700;letter-spacing:.06em;color:rgba(255,255,255,.75);margin-bottom:.4rem;text-transform:uppercase">Confidence</div>
            <div class="dna-stat-num" id="confidence-level">--</div>
            <div class="dna-stat-lbl" id="confidence-desc">Loading…</div>
        </div>
        <div class="dna-stat stat-dna-amber" style="animation-delay:.2s">
            <div style="font-size:.7rem;font-weight:700;letter-spacing:.06em;color:rgba(255,255,255,.75);margin-bottom:.4rem;text-transform:uppercase">Data Quality</div>
            <div class="dna-stat-num" id="data-quality">--</div>
            <div class="dna-stat-lbl" id="quality-desc">Loading…</div>
        </div>
    </div>

    {{-- Main Content --}}
    <div id="dna-content-area">

        {{-- Radar + Archetypes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="dna-card" style="animation-delay:.25s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#2D6CDF">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </div>
                    <div class="dna-card-title">Cultural DNA Radar</div>
                </div>
                <div class="dna-card-body" style="height:18rem">
                    <canvas id="cultural-dna-chart" style="width:100%;height:100%"></canvas>
                </div>
            </div>

            <div class="dna-card" style="animation-delay:.3s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#2D6CDF">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="dna-card-title">Cultural Archetypes</div>
                </div>
                <div class="dna-card-body space-y-2" id="archetypes-container">
                    <div style="text-align:center;padding:2rem;color:#BFCFEE;font-size:.84rem">Loading archetypes…</div>
                </div>
            </div>
        </div>

        {{-- Success Traits + Core Values --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
            <div class="dna-card" style="animation-delay:.35s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#1E8E3E">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div class="dna-card-title">Top Success Traits</div>
                </div>
                <div class="dna-card-body" id="success-traits-container">
                    <div style="text-align:center;padding:2rem;color:#BFCFEE;font-size:.84rem">Loading traits…</div>
                </div>
            </div>

            <div class="dna-card" style="animation-delay:.4s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#2D6CDF">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <div class="dna-card-title">Core Values</div>
                </div>
                <div class="dna-card-body">
                    <div class="flex flex-wrap gap-2" id="core-values-container">
                        <div style="color:#BFCFEE;font-size:.84rem">Loading values…</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Work Style + Communication --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
            <div class="dna-card" style="animation-delay:.45s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#2D6CDF">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="dna-card-title">Work Style Preferences</div>
                </div>
                <div class="dna-card-body space-y-1" id="work-style-container">
                    <div style="color:#BFCFEE;font-size:.84rem;text-align:center;padding:1rem">Loading…</div>
                </div>
            </div>

            <div class="dna-card" style="animation-delay:.5s">
                <div class="dna-card-header">
                    <div class="dna-card-icon" style="background:#2D6CDF">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div class="dna-card-title">Communication Patterns</div>
                </div>
                <div class="dna-card-body space-y-1" id="communication-container">
                    <div style="color:#BFCFEE;font-size:.84rem;text-align:center;padding:1rem">Loading…</div>
                </div>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="dna-card mt-5" style="animation-delay:.55s">
            <div class="dna-card-header">
                <div class="dna-card-icon" style="background:#737373">
                    <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="dna-card-title">Analysis Information</div>
            </div>
            <div class="dna-card-body">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="meta-stat" style="background:#EBF2FF">
                        <div class="meta-label">Last Analyzed</div>
                        <div class="meta-val" id="last-analyzed" style="font-size:1rem">--</div>
                    </div>
                    <div class="meta-stat" style="background:#EDFAF2">
                        <div class="meta-label">Employees Analyzed</div>
                        <div class="meta-val" id="employees-analyzed">--</div>
                    </div>
                    <div class="meta-stat" style="background:#EBF2FF">
                        <div class="meta-label">Hires Analyzed</div>
                        <div class="meta-val" id="hires-analyzed">--</div>
                    </div>
                </div>
                <div style="padding:1rem 1.25rem;border-radius:.875rem;background:rgba(20, 71, 186,.07);border:1px solid rgba(20, 71, 186,.12)">
                    <div style="font-size:.7rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#BFCFEE;margin-bottom:.5rem">AI Analysis Summary</div>
                    <p style="font-size:.84rem;color:#3D3D3D;line-height:1.6" id="ai-summary">Loading analysis summary…</p>
                </div>
            </div>
        </div>

    </div>{{-- /dna-content-area --}}

</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let culturalChart = null;
const companyId = {{ auth()->user()->company_id ?? 'null' }};

// Server-side pre-loaded data (avoids API auth issues on initial render)
const serverDNAData = @json($dnaProfileData ?? null);

document.addEventListener('DOMContentLoaded', () => {
    if (serverDNAData) {
        renderDNAProfile(serverDNAData);
    } else {
        loadDNAProfile();
    }
});

async function loadDNAProfile() {
    if (!companyId) { showEmptyState('No company linked to your account.'); return; }
    try {
        const r = await fetch(`/api/scout/dna-profile?company_id=${companyId}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        if (!r.ok) { showEmptyState('No DNA profile found. Click "Run DNA Analysis" to get started.'); return; }
        const result = await r.json();
        if (result.success && result.data) renderDNAProfile(result.data);
        else showEmptyState(result.message);
    } catch(e) { console.error(e); showEmptyState('Failed to load DNA profile.'); }
}

function renderDNAProfile(data) {
    const profile  = data.dna_profile;
    const metrics  = data.health_metrics;
    const insights = data.cultural_insights;
    const meta     = data.analysis_metadata;

    document.getElementById('health-score').textContent      = metrics.dna_health_score ?? 0;
    document.getElementById('health-progress').style.width   = (metrics.dna_health_score ?? 0) + '%';
    document.getElementById('completeness-score').textContent= profile.dna_completeness_score ?? 0;
    document.getElementById('completeness-status').textContent= metrics.completion_status ?? '—';
    document.getElementById('confidence-level').textContent   = profile.analysis_confidence ?? 0;
    document.getElementById('confidence-desc').textContent    = metrics.confidence_level ?? '—';
    document.getElementById('data-quality').textContent       = (metrics.data_quality ?? '—').replace(/^[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/u, '');
    document.getElementById('quality-desc').textContent       = metrics.data_quality ?? '—';

    renderRadarChart(profile.cultural_dna ?? []);
    renderArchetypes(insights.archetypes ?? []);
    renderTraits(insights.top_success_traits ?? []);
    renderValues(profile.core_values ?? []);
    renderList('work-style-container', profile.work_style_preferences ?? [], '#2D6CDF');
    renderList('communication-container', profile.communication_patterns ?? [], '#2D6CDF');

    document.getElementById('last-analyzed').textContent     = meta.last_analyzed ? new Date(meta.last_analyzed).toLocaleDateString() : 'Never';
    document.getElementById('employees-analyzed').textContent= profile.total_employees_analyzed ?? 0;
    document.getElementById('hires-analyzed').textContent    = profile.total_hires_analyzed ?? 0;
    const summary = profile.ai_analysis_summary;
    document.getElementById('ai-summary').textContent        = (typeof summary === 'object' && summary !== null) ? (summary.summary ?? JSON.stringify(summary)) : (summary ?? 'No summary available');
}

function renderRadarChart(dna) {
    const ctx = document.getElementById('cultural-dna-chart');
    if (culturalChart) culturalChart.destroy();
    const labels = dna.slice(0,8).map(i => i.trait ?? '');
    const scores = dna.slice(0,8).map(i => i.score ?? 0);
    culturalChart = new Chart(ctx, {
        type: 'radar',
        data: { labels, datasets: [{ label:'Cultural DNA', data:scores,
            backgroundColor:'rgba(20, 71, 186,.18)', borderColor:'rgba(20, 71, 186,.9)', borderWidth:2,
            pointBackgroundColor:'rgba(20, 71, 186,1)', pointBorderColor:'#fff',
            pointHoverBackgroundColor:'#fff', pointHoverBorderColor:'rgba(20, 71, 186,1)' }]},
        options: { scales:{r:{beginAtZero:true,max:100,ticks:{stepSize:20},grid:{color:'rgba(20, 71, 186,.1)'},pointLabels:{font:{size:10,weight:'600'},color:'#2D6CDF'}}},
            plugins:{legend:{display:false}}, elements:{line:{tension:.3}} }
    });
}

function renderArchetypes(archetypes) {
    const el = document.getElementById('archetypes-container');
    el.innerHTML = archetypes.length
        ? archetypes.map(a => `<div class="archetype-chip"><svg style="width:14px;height:14px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>${a}</div>`).join('')
        : '<p style="color:#BFCFEE;font-size:.84rem;text-align:center;padding:1rem">No archetypes identified</p>';
}

function renderTraits(traits) {
    const el = document.getElementById('success-traits-container');
    // traits may be an array of {trait, score, prevalence} objects OR a plain object {name: score}
    let items = [];
    if (Array.isArray(traits)) {
        items = traits;
    } else if (traits && typeof traits === 'object') {
        items = Object.entries(traits).map(([name, score]) => ({trait: name, score}));
    }
    el.innerHTML = items.length
        ? items.map((t,i) => {
            const label = t.trait ?? t; const score = t.score ?? null;
            return `<div class="trait-row"><div class="trait-label"><span>${i+1}. ${label}</span>${score ? `<span style="color:#1E8E3E;font-weight:800">${score}</span>` : ''}</div>${score ? `<div class="trait-bar-bg"><div class="trait-bar-fill" style="width:${score}%"></div></div>` : ''}</div>`;
        }).join('')
        : '<p style="color:#BFCFEE;font-size:.84rem;text-align:center;padding:1rem">No traits identified</p>';
}

function renderValues(values) {
    const el = document.getElementById('core-values-container');
    el.innerHTML = values.length
        ? values.map(v => `<span class="value-chip">${v}</span>`).join('')
        : '<p style="color:#BFCFEE;font-size:.84rem">No core values defined</p>';
}

function renderList(id, items, dotColor) {
    const el = document.getElementById(id);
    el.innerHTML = items.length
        ? items.map(item => `<div class="pattern-row"><span class="pattern-dot" style="background:${dotColor}"></span>${item}</div>`).join('')
        : `<p style="color:#BFCFEE;font-size:.84rem;text-align:center;padding:1rem">No data</p>`;
}

async function analyzeDNA(event) {
    if (!companyId) return;
    const btn = document.getElementById('analyze-btn');
    const txt = document.getElementById('analyze-btn-text');
    btn.disabled = true;
    txt.textContent = 'Analyzing…';
    try {
        const r = await fetch('/api/scout/analyze-dna', {
            method:'POST', credentials:'same-origin',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With':'XMLHttpRequest',
                'Content-Type':'application/json', 'Accept':'application/json',
            },
            body: JSON.stringify({company_id:companyId, force_refresh:true})
        });
        const result = await r.json();
        if (result.success) {
            await loadDNAProfile();
        } else {
            const msg = result.message ?? result.errors ?? 'Unknown error';
            alert('Analysis failed: ' + (typeof msg === 'object' ? JSON.stringify(msg) : msg));
        }
    } catch(e) { console.error(e); alert('Network error: ' + e.message); }
    finally { btn.disabled = false; txt.textContent = 'Run DNA Analysis'; }
}

function refreshAnalysis() {
    const btn = document.getElementById('refresh-btn');
    if (btn) { btn.disabled = true; btn.style.opacity = '.6'; }
    window.location.reload();
}

function showEmptyState(msg) {
    const area = document.getElementById('dna-content-area');
    if (!area) return;
    area.innerHTML = `
        <div class="dna-empty">
            <div class="dna-empty-icon">
                <svg style="width:2.25rem;height:2.25rem;color:#2D6CDF" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
            <h3 style="font-size:1.2rem;font-weight:900;color:#0C0C0C;margin-bottom:.5rem">No DNA Profile Found</h3>
            <p style="color:#A8A8A8;font-size:.84rem;margin-bottom:1.5rem">${msg ?? 'DNA profile not found. Please run DNA analysis first.'}</p>
            <button onclick="analyzeDNA(event)" style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.75rem;border-radius:.875rem;font-size:.875rem;font-weight:700;background:#2D6CDF;color:#fff;border:none;cursor:pointer;box-shadow: none;transition:all .2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span id="analyze-btn-text">Run DNA Analysis</span>
            </button>
        </div>`;
}
</script>
@endpush
@endsection
