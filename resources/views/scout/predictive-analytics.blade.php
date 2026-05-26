@extends('layouts.dashboard')

@section('title', 'Predictive Performance Analytics - S.C.O.U.T.')

@section('content')
<div class="pa-page">
        <a href="{{ route('employer.scout.dashboard') }}" class="pa-back-btn">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
        {{-- Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1.5rem;flex-wrap:wrap;margin-bottom:1rem">
            <div>
                <h1 class="pa-title">Predictive Performance Analytics</h1>
                <p class="pa-subtitle">Forecast candidate success, tenure, productivity, and career trajectory with AI-powered insights.</p>
            </div>
            <div style="display:flex;align-items:center;gap:.875rem;flex-wrap:wrap;flex-shrink:0">
                <div class="pa-info-box">
                    <div class="pa-info-icon">
                        <svg style="width:1.1rem;height:1.1rem;color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin:0">Last Refreshed</p>
                        <p id="last-refreshed" style="font-size:.8rem;font-weight:800;color:#1a1a2e;margin:0">--</p>
                    </div>
                </div>
                <button id="refresh-dashboard" class="btn-pa-refresh">
                    <svg style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh Predictions</span>
                </button>
            </div>
        </div>

        {{-- Application Selector --}}
        <div class="glass-card" style="border-radius:1.125rem;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:1.25rem 1.5rem;border-bottom:1.5px solid rgba(139,92,246,.1);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                <div>
                    <h2 style="font-size:1.05rem;font-weight:800;color:#1a1a2e;margin:0 0 .2rem">Analyze Candidate Application</h2>
                    <p style="font-size:.82rem;color:#6b7280;margin:0">Select an application from your company to run AI-powered predictive analytics.</p>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                    <select id="application-id-input" class="pa-input" style="min-width:260px;max-width:360px">
                        <option value="">Loading applications…</option>
                    </select>
                    <button id="load-application" class="btn-pa-load">
                        <svg style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Load Insights</span>
                    </button>
                </div>
            </div>
            <div style="padding:1rem 1.5rem 1.25rem;background:linear-gradient(135deg,#fdf4ff,#f5f3ff,#eef2ff)">
                <div class="metric-grid-4">
                    <div class="metric-card">
                        <div class="metric-card__icon" style="background:rgba(99,102,241,.12);color:#6366f1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 8 4-16 3 8h4" />
                            </svg>
                        </div>
                        <div class="metric-card__content">
                            <p class="metric-card__label">Success Probability</p>
                            <p id="metric-success" class="metric-card__value">--%</p>
                            <span id="metric-success-trend" class="metric-card__trend">Awaiting data</span>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon" style="background:rgba(168,85,247,.12);color:#a855f7">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="metric-card__content">
                            <p class="metric-card__label">Tenure Forecast</p>
                            <p id="metric-tenure" class="metric-card__value">-- months</p>
                            <span id="metric-tenure-risk" class="metric-card__trend">Risk pending</span>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon" style="background:rgba(236,72,153,.12);color:#ec4899">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="metric-card__content">
                            <p class="metric-card__label">Ramp-Up Time</p>
                            <p id="metric-productivity" class="metric-card__value">-- weeks</p>
                            <span id="metric-productivity-status" class="metric-card__trend">Awaiting assessment</span>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon" style="background:rgba(245,158,11,.12);color:#f59e0b">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            </svg>
                        </div>
                        <div class="metric-card__content">
                            <p class="metric-card__label">Succession Potential</p>
                            <p id="metric-succession" class="metric-card__value">--%</p>
                            <span id="metric-succession-track" class="metric-card__trend">No prediction</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="glass-card" style="border-radius:1.125rem;overflow:hidden;margin-bottom:1.5rem">
            <div style="display:flex;flex-wrap:wrap;border-bottom:1.5px solid rgba(139,92,246,.1)" role="tablist">
                <button data-tab="overview" class="tab-button active">
                    <span>Overview</span>
                </button>
                <button data-tab="success" class="tab-button">
                    <span>Success Probability</span>
                </button>
                <button data-tab="tenure" class="tab-button">
                    <span>Tenure Forecast</span>
                </button>
                <button data-tab="productivity" class="tab-button">
                    <span>Productivity</span>
                </button>
                <button data-tab="development" class="tab-button">
                    <span>Development Plan</span>
                </button>
                <button data-tab="career" class="tab-button">
                    <span>Career Path</span>
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div id="tab-panels" class="space-y-4">
            {{-- Overview Tab --}}
            <section id="panel-overview" class="tab-panel">
                {{-- Top row: Gauge + Flight Risk + AI Recommendations --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    {{-- Success Gauge --}}
                    <div class="glass-card rounded-2xl p-4 flex flex-col items-center justify-center">
                        <div class="flex items-center justify-between w-full mb-2">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Success Gauge</h3>
                            <span id="overview-confidence" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">Confidence: --</span>
                        </div>
                        <canvas id="overview-success-gauge" style="max-height:140px;max-width:240px;width:100%"></canvas>
                        <p id="overview-success-insight" class="mt-2 text-xs text-gray-500 text-center">Awaiting prediction...</p>
                    </div>

                    {{-- Flight Risk Matrix --}}
                    <div class="glass-card rounded-2xl p-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Flight Risk Matrix</h3>
                        <div style="height:130px;position:relative">
                            <canvas id="overview-flight-risk"></canvas>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-sm text-gray-600">
                            <span>Risk Level: <strong id="overview-risk-label" class="text-gray-800">--</strong></span>
                            <span>Priority: <strong id="overview-priority-score" class="text-gray-800">--</strong></span>
                        </div>
                    </div>

                    {{-- AI Recommendations --}}
                    <div class="glass-card rounded-2xl p-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">AI Recommendations</h3>
                        <ul id="overview-recommendations" class="space-y-2">
                            <li class="recommendation-item text-xs">Load a candidate to receive prioritized recommendations.</li>
                        </ul>
                    </div>
                </div>

                {{-- Bottom row: Tenure + Productivity as stat cards (no charts) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="glass-card rounded-2xl p-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Tenure Outlook</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-blue-50 text-blue-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Predicted</p>
                                <p id="overview-tenure-value" class="text-lg font-bold">--</p>
                            </div>
                            <div class="rounded-xl bg-purple-50 text-purple-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Conf. Range</p>
                                <p id="overview-tenure-range" class="text-lg font-bold">--</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 text-amber-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Risk Alerts</p>
                                <p id="overview-tenure-risks" class="text-lg font-bold">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Productivity Ramp-Up</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-pink-50 text-pink-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Full Productivity</p>
                                <p id="overview-productivity-timeline" class="text-lg font-bold">--</p>
                            </div>
                            <div class="rounded-xl bg-indigo-50 text-indigo-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Support Level</p>
                                <p id="overview-support-level" class="text-lg font-bold">--</p>
                            </div>
                            <div class="rounded-xl bg-teal-50 text-teal-600 px-3 py-2 text-center">
                                <p class="text-xs font-semibold">Milestone</p>
                                <p id="overview-current-milestone" class="text-lg font-bold">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Success Probability Tab --}}
            <section id="panel-success" class="tab-panel hidden">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                    <div class="xl:col-span-2 glass-card rounded-2xl shadow-xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-800">Success Probability Analysis</h3>
                            <span id="success-confidence" class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">Confidence: --</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="rounded-2xl border border-gray-100 p-6 bg-gradient-to-br from-white via-blue-50 to-white flex flex-col items-center justify-center">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500">Success Gauge</h4>
                                <canvas id="success-gauge" class="w-full max-w-xs h-56"></canvas>
                                <p id="success-gauge-insight" class="mt-4 text-sm text-gray-600 text-center">Awaiting prediction...</p>
                            </div>
                            <div class="rounded-2xl border border-gray-100 p-6 bg-white">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500">Factor Breakdown</h4>
                                <ul id="success-factor-list" class="divide-y divide-gray-100">
                                    <li class="py-3 text-sm text-gray-500">Factors will appear after loading predictions.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Comparable Profiles</h3>
                        <ul id="success-comparables" class="space-y-4 text-sm text-gray-600">
                            <li>Load predictions to view comparable high-performing profiles.</li>
                        </ul>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Recommendation Flow</h3>
                        <span id="success-recommendation" class="text-sm text-blue-600 font-medium">Run prediction to view guidance.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="success-action-plan">
                        <div class="action-card">Awaiting data</div>
                    </div>
                </div>
            </section>

            {{-- Tenure Forecast Tab --}}
            <section id="panel-tenure" class="tab-panel hidden">
                <div class="glass-card rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Tenure Probability Distribution</h3>
                            <p class="text-sm text-gray-500">Confidence intervals and retention factors derived from historical patterns.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="tenure-risk-level" class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-600">Risk Level: --</span>
                            <span id="tenure-flight-score" class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-600">Flight Risk: --%</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="h-72">
                                <canvas id="tenure-forecast-chart"></canvas>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="rounded-2xl border border-gray-100 p-6 bg-white">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Retention Drivers</h4>
                                <ul id="tenure-retention-list" class="space-y-3 text-sm text-gray-600">
                                    <li>Load a forecast to see key retention boosters.</li>
                                </ul>
                            </div>
                            <div class="rounded-2xl border border-gray-100 p-6 bg-white">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Risk Indicators</h4>
                                <ul id="tenure-risk-indicators" class="space-y-3 text-sm text-gray-600">
                                    <li>Risk indicators will appear after running a forecast.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Mitigation Strategies</h3>
                        <ul id="tenure-mitigation" class="space-y-4 text-sm text-gray-600">
                            <li>Execute tenure forecast to receive targeted mitigation actions.</li>
                        </ul>
                    </div>
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Tenure Summary</h3>
                        <div class="space-y-4 text-sm text-gray-600" id="tenure-summary">
                            <p>No summary yet. Load predictions for this candidate to view tenure outlook.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Productivity Tab --}}
            <section id="panel-productivity" class="tab-panel hidden">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                    <div class="xl:col-span-2 glass-card rounded-2xl shadow-xl p-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Ramp-Up Trajectory</h3>
                                <p class="text-sm text-gray-500">Visualize expected productivity milestones over the first 90 days.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="productivity-support" class="px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-600">Support Level: --</span>
                                <span id="productivity-status" class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">Status: --</span>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas id="productivity-progress-chart"></canvas>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Milestone Tracker</h3>
                        <div id="productivity-milestones" class="space-y-4 text-sm text-gray-600">
                            <p>Run productivity estimation to view milestone timeline.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Support Recommendations</h3>
                    <div id="productivity-support-actions" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="action-card">Awaiting data</div>
                    </div>
                </div>
            </section>

            {{-- Development Plan Tab --}}
            <section id="panel-development" class="tab-panel hidden">
                <div class="glass-card rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Development Roadmap</h3>
                        <span id="development-plan-status" class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-600">Status: Awaiting</span>
                    </div>
                    <div class="overflow-hidden rounded-2xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gradient-to-r from-blue-50 via-purple-50 to-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Skill / Capability</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Gap Severity</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Recommended Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Timeline</th>
                                </tr>
                            </thead>
                            <tbody id="development-plan-table" class="bg-white divide-y divide-gray-100">
                                <tr>
                                    <td colspan="4" class="px-6 py-5 text-sm text-gray-500 text-center">Generate development plan to populate skills matrix.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Learning Recommendations</h3>
                        <div id="development-learning" class="space-y-5 text-sm text-gray-600">
                            <p>No recommendations yet. Run the development plan to receive personalized learning suggestions.</p>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl shadow-xl p-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Success Metrics</h3>
                        <div id="development-metrics" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                            <div class="metric-tile">
                                <p class="metric-tile__label">Immediate Focus</p>
                                <p id="development-immediate" class="metric-tile__value">--</p>
                            </div>
                            <div class="metric-tile">
                                <p class="metric-tile__label">Short-Term Goals</p>
                                <p id="development-short" class="metric-tile__value">--</p>
                            </div>
                            <div class="metric-tile">
                                <p class="metric-tile__label">Medium-Term Goals</p>
                                <p id="development-medium" class="metric-tile__value">--</p>
                            </div>
                            <div class="metric-tile">
                                <p class="metric-tile__label">Long-Term Goals</p>
                                <p id="development-long" class="metric-tile__value">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Career Path Tab --}}
            <section id="panel-career" class="tab-panel hidden">
                <div class="glass-card rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Career Trajectory</h3>
                            <p class="text-sm text-gray-500">Projected progression across roles, timelines, and readiness signals.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="career-trajectory" class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-600">Trajectory: --</span>
                            <span id="career-succession" class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-600">Succession Potential: --%</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div id="career-path-tree" class="space-y-6">
                                <div class="rounded-2xl border border-gray-100 p-6 bg-white">Generate career path to visualize progression tree.</div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="rounded-2xl border border-gray-100 p-6 bg-white">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Development Requirements</h4>
                                <ul id="career-development" class="space-y-3 text-sm text-gray-600">
                                    <li>Development requirements will display after predicting the career path.</li>
                                </ul>
                            </div>
                            <div class="rounded-2xl border border-gray-100 p-6 bg-white">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Action Items</h4>
                                <ul id="career-action-items" class="space-y-3 text-sm text-gray-600">
                                    <li>Run prediction to view prioritized career development tasks.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Comprehensive Report</h3>
                    <div class="flex flex-wrap items-center gap-3 mb-5">
                        <button id="generate-report" class="btn-pa-report">
                            <svg style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v2h6v-2m3-6a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Generate AI Report</span>
                        </button>
                        <span id="report-generated-at" class="text-sm text-gray-500">No report generated yet.</span>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Executive Summary</h4>
                            <div id="report-summary" class="rounded-2xl border border-gray-100 p-6 bg-white prose prose-sm max-w-none text-gray-600">
                                <p>Generate report to view GPT-powered summary.</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wide text-gray-500 mb-4">Action Priorities</h4>
                            <ul id="report-actions" class="space-y-4 text-sm text-gray-600">
                                <li>No action items yet.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>
</div>

{{-- Loading Overlay --}}
<div id="predictive-loading" class="hidden fixed inset-0 z-40 flex items-center justify-center">
    <div style="background:linear-gradient(135deg,#fdfcff,#f5f3ff);border:1.5px solid rgba(139,92,246,.2);border-radius:1.125rem;box-shadow:0 20px 60px rgba(124,58,237,.2);padding:1.75rem 2rem;display:flex;align-items:center;gap:1.25rem">
        <div class="pa-spinner"></div>
        <div>
            <p id="predictive-loading-message" style="font-size:.875rem;font-weight:800;color:#1a1a2e;margin:0 0 .2rem">Processing predictive models…</p>
            <p style="font-size:.75rem;color:#9ca3af;margin:0">AI models running with caching optimizations.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes paFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@keyframes paSlideIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes paSpin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

.pa-page {
    background:linear-gradient(145deg,#fdf4ff 0%,#f5f3ff 30%,#eef2ff 60%,#eff6ff 100%);
    margin:-28px -32px -32px;
    padding:1.5rem 2rem 2.5rem;
    min-height:calc(100vh - 64px);
    overflow-x:hidden;
}
.metric-grid-4 {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:.875rem;
}
@media(min-width:900px){
    .metric-grid-4 { grid-template-columns:repeat(4,1fr); }
}

/* Hero header */
.pa-back-btn {
    display:inline-flex;align-items:center;gap:.5rem;font-size:.82rem;font-weight:700;
    color:#4c1d95;background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    border:1.5px solid rgba(139,92,246,.2);border-radius:.75rem;padding:.45rem 1rem;
    text-decoration:none;transition:all .2s;margin-bottom:1.25rem;
}
.pa-back-btn:hover { border-color:#7c3aed;transform:translateX(-2px); }

.pa-hero { animation:paSlideIn .5s ease both; }
.pa-title {
    font-size:clamp(1.5rem,4vw,2.25rem);font-weight:900;
    background:linear-gradient(135deg,#7c3aed,#6366f1,#a855f7,#ec4899);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    margin:0 0 .5rem;
}
.pa-subtitle { color:#6b7280;font-size:.95rem;max-width:55ch; }

/* Info box + refresh button */
.pa-info-box {
    display:flex;align-items:center;gap:.875rem;
    background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    border:1.5px solid rgba(139,92,246,.15);border-radius:1rem;
    padding:.875rem 1.25rem;box-shadow:0 2px 12px rgba(99,102,241,.06);
}
.pa-info-icon {
    width:2.5rem;height:2.5rem;border-radius:50%;
    background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(139,92,246,.1));
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.btn-pa-refresh {
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.75rem 1.5rem;border-radius:.875rem;border:none;cursor:pointer;
    background:linear-gradient(135deg,#7c3aed,#a855f7);
    color:#fff;font-weight:800;font-size:.875rem;
    box-shadow:0 4px 16px rgba(124,58,237,.3);transition:all .2s;
}
.btn-pa-refresh:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(124,58,237,.4); }

/* Glass cards */
.glass-card {
    background:linear-gradient(135deg,#fdfcff 0%,#f5f3ff 50%,#fdf4ff 100%);
    border:1.5px solid rgba(139,92,246,.12);border-radius:1.125rem;
    box-shadow:0 4px 24px rgba(99,102,241,.07);
}

/* Metric cards */
.metric-card {
    background:#fff;border:1.5px solid rgba(139,92,246,.1);border-radius:1rem;
    padding:1rem;display:flex;align-items:center;gap:1rem;
    box-shadow:0 2px 10px rgba(99,102,241,.05);transition:transform .2s,box-shadow .2s;
}
.metric-card:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(99,102,241,.1); }
.metric-card__icon {
    width:3rem;height:3rem;border-radius:.875rem;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.metric-card__content { flex:1;min-width:0; }
.metric-card__label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin:0 0 .2rem; }
.metric-card__value { font-size:1.5rem;font-weight:900;color:#1a1a2e;line-height:1;margin:0 0 .15rem; }
.metric-card__trend { font-size:.75rem;font-weight:600;color:#7c3aed; }

/* App input */
.pa-input {
    padding:.65rem 1rem;border:1.5px solid rgba(139,92,246,.2);border-radius:.75rem;
    font-size:.875rem;color:#1a1a2e;
    background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    transition:border-color .2s,box-shadow .2s;width:100%;
}
.pa-input:focus { outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1); }
.btn-pa-load {
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.65rem 1.25rem;border-radius:.75rem;
    border:1.5px solid rgba(139,92,246,.2);cursor:pointer;
    background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    color:#7c3aed;font-weight:700;font-size:.875rem;
    transition:all .2s;white-space:nowrap;
}
.btn-pa-load:hover { background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border-color:#7c3aed; }

/* Tabs */
.tab-button {
    flex:1;padding:.875rem 1rem;font-size:.8rem;font-weight:700;
    color:#9ca3af;background:#fff;border:none;cursor:pointer;
    transition:all .2s;letter-spacing:.03em;text-transform:uppercase;
}
.tab-button:hover { color:#7c3aed;background:rgba(139,92,246,.04); }
.tab-button.active {
    color:#7c3aed;background:linear-gradient(180deg,rgba(139,92,246,.06),rgba(99,102,241,.04));
    box-shadow:inset 0 -3px 0 0 #7c3aed;
}
.tab-panel { background:transparent; }

/* Recommendation & action cards */
.recommendation-item {
    padding:.75rem;border-radius:.75rem;font-size:.83rem;color:#374151;
    background:linear-gradient(135deg,#f5f3ff,#ede9fe);
    border-left:3px solid #a855f7;
}
.action-card {
    background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    border:1.5px solid rgba(139,92,246,.12);border-radius:1rem;
    padding:1.25rem;font-size:.875rem;color:#374151;
}

/* Metric tiles */
.metric-tile {
    background:linear-gradient(135deg,#fdfcff,#f5f3ff);
    border:1.5px solid rgba(139,92,246,.1);border-radius:.875rem;
    padding:.875rem 1rem;
}
.metric-tile__label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin:0 0 .25rem; }
.metric-tile__value { font-size:1.1rem;font-weight:800;color:#1a1a2e; }

/* Loading overlay */
#predictive-loading {
    background:rgba(245,243,255,.85);backdrop-filter:blur(8px);
}
.pa-spinner {
    width:3rem;height:3rem;border:3px solid rgba(124,58,237,.15);
    border-top-color:#7c3aed;border-radius:50%;animation:paSpin .8s linear infinite;
}

/* Confidence badge */
.conf-badge {
    display:inline-flex;align-items:center;padding:.25rem .875rem;
    border-radius:2rem;font-size:.72rem;font-weight:800;letter-spacing:.04em;
    background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.1));
    border:1px solid rgba(99,102,241,.2);color:#4c1d95;
}

/* Section headers within cards */
.pa-section-h { font-size:1rem;font-weight:800;color:#1a1a2e;margin:0 0 1rem; }

/* Generate report button */
.btn-pa-report {
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.65rem 1.25rem;border-radius:.875rem;border:none;cursor:pointer;
    background:linear-gradient(135deg,#10b981,#059669);
    color:#fff;font-weight:800;font-size:.875rem;
    box-shadow:0 4px 14px rgba(16,185,129,.25);transition:all .2s;
}
.btn-pa-report:hover { transform:translateY(-2px);box-shadow:0 8px 20px rgba(16,185,129,.35); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const predictiveState = {
        applicationId: null,
        lastRefreshed: null,
        charts: {},
        gaugeOptions: {
            type: 'doughnut',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${context.parsed}%`
                        }
                    }
                },
                cutout: '70%',
                rotation: -90,
                circumference: 180
            }
        }
    };

    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-tab');
            switchTab(tab);
        });
    });

    document.getElementById('load-application').addEventListener('click', async () => {
        const select = document.getElementById('application-id-input');
        const val = select.value;
        if (!val || val === '') {
            showToast('Please select an application from the dropdown.', 'warning');
            return;
        }
        predictiveState.applicationId = Number(val);
        await refreshAnalytics();
    });

    document.getElementById('refresh-dashboard').addEventListener('click', async () => {
        if (!predictiveState.applicationId) {
            showToast('Select an application before refreshing predictions.', 'warning');
            return;
        }
        await refreshAnalytics();
    });

    document.getElementById('generate-report').addEventListener('click', async () => {
        if (!predictiveState.applicationId) {
            showToast('Load an application before generating report.', 'warning');
            return;
        }
        await generateComprehensiveReport();
    });

    function switchTab(tab) {
        tabButtons.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
        });
        tabPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== `panel-${tab}`);
        });
    }

    async function refreshAnalytics() {
        showLoading('Generating predictive insights...');
        const results = await Promise.allSettled([
            loadSuccessPrediction(),
            loadTenureForecast(),
            loadProductivityEstimate(),
            loadDevelopmentPlan(),
            loadCareerPath()
        ]);
        hideLoading();
        predictiveState.lastRefreshed = new Date();
        updateRefreshTimestamp();
        const failed = results.filter(r => r.status === 'rejected');
        const ok = results.filter(r => r.status === 'fulfilled').length;
        if (failed.length === 0) {
            showToast('Predictive insights updated successfully.', 'success');
        } else if (ok > 0) {
            showToast(`${ok} of ${results.length} insights loaded. Some predictions need more candidate data.`, 'warning');
        } else {
            showToast('Could not load predictions. Please ensure the candidate has sufficient profile data.', 'error');
        }
        failed.forEach(f => console.warn('Prediction failed:', f.reason?.message || f.reason));
    }

    function updateRefreshTimestamp() {
        if (!predictiveState.lastRefreshed) return;
        const formatted = predictiveState.lastRefreshed.toLocaleString();
        document.getElementById('last-refreshed').textContent = formatted;
    }

    async function loadSuccessPrediction() {
        const payload = { application_id: predictiveState.applicationId };
        const { data } = await postJson('/api/scout/predictive/success', payload);
        updateSuccessMetrics(data);
    }

    function updateSuccessMetrics(data) {
        document.getElementById('metric-success').textContent = `${data.success_probability.toFixed(1)}%`;
        document.getElementById('metric-success-trend').textContent = data.category;
        document.getElementById('overview-confidence').textContent = `Confidence: ${data.confidence.toFixed(1)}%`;
        document.getElementById('success-confidence').textContent = `Confidence: ${data.confidence.toFixed(1)}%`;
        renderGauge('overview-success-gauge', data.success_probability, 'Success Probability');
        renderGauge('success-gauge', data.success_probability, 'Success Probability');
        renderFactorList('success-factor-list', data.factor_scores || {});
        renderSuccessRecommendations(data);
        renderComparableProfiles('success-comparables', data.comparable_profiles || []);
        document.getElementById('success-gauge-insight').textContent = data.recommendation || 'Prediction ready.';
        document.getElementById('metric-succession-track').textContent = data.recommendation || 'Actionable insights available.';
    }

    function renderSuccessRecommendations(data) {
        const container = document.getElementById('success-action-plan');
        container.innerHTML = '';
        const insights = data.top_strengths || [];
        const concerns = data.top_concerns || [];
        const suggestionBlocks = [
            {
                title: 'Capitalize on Strengths',
                items: insights.length ? insights : ['No strengths detected yet.']
            },
            {
                title: 'Mitigate Concerns',
                items: concerns.length ? concerns : ['No concerns flagged.']
            },
            {
                title: 'AI Guidance',
                items: [data.recommendation || 'Execute development actions for tailored guidance.']
            }
        ];
        suggestionBlocks.forEach(block => {
            const card = document.createElement('div');
            card.className = 'action-card';
            card.innerHTML = `
                <h4 class="text-sm font-semibold text-gray-700 mb-3">${block.title}</h4>
                <ul class="space-y-2 text-gray-600 text-sm">
                    ${block.items.map(item => `<li class="flex items-start space-x-2"><span class="mt-1 w-2 h-2 rounded-full bg-blue-400"></span><span>${typeof item === 'string' ? item : item.description || JSON.stringify(item)}</span></li>`).join('')}
                </ul>
            `;
            container.appendChild(card);
        });
    }

    function renderComparableProfiles(elementId, profiles) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        if (!profiles.length) {
            container.innerHTML = '<li>No comparable profiles found yet.</li>';
            return;
        }
        profiles.forEach(profile => {
            const item = document.createElement('li');
            item.className = 'bg-white rounded-2xl border border-gray-100 p-4 shadow-sm';
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">${profile.role || 'Similar Role'}</p>
                        <p class="text-xs text-gray-500">${profile.department || 'Department'} &middot; ${profile.tenure || '--'} tenure</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">${profile.success_score ? `${profile.success_score}% match` : ''}</span>
                </div>
                <p class="mt-3 text-sm text-gray-600">${profile.summary || 'Comparable performance data not available.'}</p>
            `;
            container.appendChild(item);
        });
    }

    async function loadTenureForecast() {
        const payload = { application_id: predictiveState.applicationId };
        const { data } = await postJson('/api/scout/predictive/tenure', payload);
        updateTenureMetrics(data);
    }

    function updateTenureMetrics(data) {
        document.getElementById('metric-tenure').textContent = `${data.predicted_tenure_months} months`;
        document.getElementById('metric-tenure-risk').textContent = `${data.risk_level}`;
        document.getElementById('overview-tenure-value').textContent = `${data.predicted_tenure_months} months`;
        document.getElementById('overview-tenure-range').textContent = data.tenure_range;
        document.getElementById('overview-tenure-risks').textContent = data.risk_indicators ? data.risk_indicators.length : 0;
        document.getElementById('tenure-risk-level').textContent = `Risk Level: ${data.risk_level}`;
        document.getElementById('tenure-flight-score').textContent = `Flight Risk: ${data.flight_risk_score.toFixed(1)}%`;
        document.getElementById('overview-risk-label').textContent = data.risk_level;
        document.getElementById('overview-priority-score').textContent = `${data.flight_risk_score.toFixed(1)}`;
        renderTenureChart(data);
        renderFlightRiskMatrix(data);
        renderList('tenure-retention-list', data.retention_factors, 'This factor is supporting retention.');
        renderList('tenure-risk-indicators', data.risk_indicators, 'Risk indicator detected.');
        renderList('tenure-mitigation', data.recommendation ? [data.recommendation] : [], 'Implement mitigation strategy.');
        const summary = document.getElementById('tenure-summary');
        summary.innerHTML = `
            <p><strong>Predicted tenure:</strong> ${data.predicted_tenure_months} months (${data.tenure_category}).</p>
            <p><strong>Flight risk score:</strong> ${data.flight_risk_score.toFixed(1)}% indicating ${data.is_flight_risk ? 'heightened' : 'manageable'} attrition risk.</p>
            <p><strong>Confidence:</strong> ${data.confidence}.</p>
        `;
    }

    async function loadProductivityEstimate() {
        const payload = { application_id: predictiveState.applicationId };
        const { data } = await postJson('/api/scout/predictive/productivity', payload);
        updateProductivityMetrics(data);
    }

    function updateProductivityMetrics(data) {
        document.getElementById('metric-productivity').textContent = `${data.estimated_weeks} weeks`;
        document.getElementById('metric-productivity-status').textContent = data.productivity_category;
        document.getElementById('overview-productivity-timeline').textContent = `${data.estimated_months} months`;
        document.getElementById('overview-support-level').textContent = data.support_needed;
        document.getElementById('overview-current-milestone').textContent = data.current_milestone?.milestone?.label || 'Not started';
        document.getElementById('productivity-support').textContent = `Support Level: ${data.support_needed}`;
        document.getElementById('productivity-status').textContent = `Status: ${data.productivity_category}`;
        renderProductivityChart(data);
        renderMilestones('productivity-milestones', data.productivity_milestones, data.current_milestone);
        renderSupportActions('productivity-support-actions', data.support_actions || data.support_needed);
    }

    async function loadDevelopmentPlan() {
        const payload = { application_id: predictiveState.applicationId };
        const { data } = await postJson('/api/scout/predictive/development', payload);
        updateDevelopmentPlan(data);
    }

    function updateDevelopmentPlan(data) {
        document.getElementById('development-plan-status').textContent = 'Status: Active';
        renderDevelopmentTable(data.skill_gaps || []);
        renderDevelopmentLearning(data.training_recommendations || []);
        renderDevelopmentMetrics(data.development_timeline || {});
        document.getElementById('development-immediate').textContent = (data.development_timeline?.immediate || []).length + ' initiatives';
        document.getElementById('development-short').textContent = (data.development_timeline?.short_term || []).length + ' goals';
        document.getElementById('development-medium').textContent = (data.development_timeline?.medium_term || []).length + ' goals';
        document.getElementById('development-long').textContent = (data.development_timeline?.long_term || []).length + ' goals';
    }

    async function loadCareerPath() {
        const payload = { application_id: predictiveState.applicationId };
        const { data } = await postJson('/api/scout/predictive/career-path', payload);
        updateCareerPath(data);
    }

    function updateCareerPath(data) {
        document.getElementById('metric-succession').textContent = `${data.succession_potential.toFixed(1)}%`;
        document.getElementById('career-trajectory').textContent = `Trajectory: ${data.career_trajectory}`;
        document.getElementById('career-succession').textContent = `Succession Potential: ${data.succession_potential.toFixed(1)}%`;
        renderCareerTree('career-path-tree', data.predicted_roles || []);
        renderList('career-development', data.development_requirements, 'Skill gap to unlock progression.');
        renderList('career-action-items', data.recommendation ? [data.recommendation] : [], 'Action required.');
    }

    async function generateComprehensiveReport() {
        showLoading('Compiling comprehensive AI report...');
        try {
            const response = await fetch(`/api/scout/predictive/report/${predictiveState.applicationId}`, {
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to generate report');
            }
            updateReportSection(result.data);
            predictiveState.lastRefreshed = new Date();
            updateRefreshTimestamp();
            showToast('Comprehensive report generated.', 'success');
        } catch (error) {
            console.error('Report generation failed:', error);
            showToast(error.message || 'Unable to generate report.', 'error');
        } finally {
            hideLoading();
        }
    }

    function updateReportSection(data) {
        document.getElementById('report-generated-at').textContent = `Last generated: ${new Date().toLocaleString()}`;
        document.getElementById('report-summary').innerHTML = data.report || '<p>No summary available.</p>';
        const actions = document.getElementById('report-actions');
        actions.innerHTML = '';
        (data.action_items || ['No prioritized actions returned.']).forEach(item => {
            const li = document.createElement('li');
            li.className = 'rounded-2xl border border-gray-100 bg-white p-4 shadow-sm';
            li.textContent = typeof item === 'string' ? item : item.description || JSON.stringify(item);
            actions.appendChild(li);
        });
    }

    function renderGauge(elementId, value, label) {
        const ctx = document.getElementById(elementId).getContext('2d');
        if (predictiveState.charts[elementId]) {
            predictiveState.charts[elementId].destroy();
        }
        predictiveState.charts[elementId] = new Chart(ctx, {
            ...predictiveState.gaugeOptions,
            data: {
                labels: [label, 'Remaining'],
                datasets: [{
                    data: [value, Math.max(0, 100 - value)],
                    backgroundColor: ['#3b82f6', '#e0e7ff'],
                    borderWidth: 0
                }]
            }
        });
    }

    function renderFactorList(elementId, factors) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        if (!Object.keys(factors).length) {
            container.innerHTML = '<li class="py-3 text-sm text-gray-500">No factor scores available.</li>';
            return;
        }
        Object.entries(factors).sort((a, b) => b[1] - a[1]).forEach(([name, score]) => {
            const li = document.createElement('li');
            li.className = 'py-3 flex items-center justify-between';
            li.innerHTML = `
                <div>
                    <p class="font-semibold text-gray-700">${name}</p>
                    <p class="text-xs text-gray-500">Impact on success</p>
                </div>
                <span class="text-sm font-semibold text-blue-600">${(score * 100).toFixed(1)}%</span>
            `;
            container.appendChild(li);
        });
    }

    function renderFlightRiskMatrix(data) {
        const ctx = document.getElementById('overview-flight-risk').getContext('2d');
        if (predictiveState.charts['overview-flight-risk']) {
            predictiveState.charts['overview-flight-risk'].destroy();
        }
        predictiveState.charts['overview-flight-risk'] = new Chart(ctx, {
            type: 'bubble',
            data: {
                datasets: [{
                    label: 'Flight Risk',
                    data: [{
                        x: data.flight_risk_score,
                        y: data.risk_indicators ? data.risk_indicators.length : 0,
                        r: Math.max(10, data.flight_risk_score / 2)
                    }],
                    backgroundColor: 'rgba(239,68,68,0.2)',
                    borderColor: 'rgba(239,68,68,0.6)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Flight Risk Score' }, min: 0, max: 100 },
                    y: { title: { display: true, text: 'Risk Indicators' }, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    function renderTenureChart(data) {
        const el = document.getElementById('overview-tenure-chart');
        if (!el) return; // removed from overview — skip
        const ctx = el.getContext('2d');
        if (predictiveState.charts['overview-tenure-chart']) {
            predictiveState.charts['overview-tenure-chart'].destroy();
        }
        predictiveState.charts['overview-tenure-chart'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['0', '25', '50', '75', '100'],
                datasets: [{
                    label: 'Probability Density',
                    data: data.probability_curve || [0, 10, 40, 20, 5],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Months' } },
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function renderProductivityChart(data) {
        const ctx = document.getElementById('productivity-progress-chart').getContext('2d');
        if (predictiveState.charts['productivity-progress-chart']) {
            predictiveState.charts['productivity-progress-chart'].destroy();
        }
        const milestones = data.productivity_milestones || [];
        predictiveState.charts['productivity-progress-chart'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: milestones.map((m, index) => m.label || `Milestone ${index + 1}`),
                datasets: [{
                    label: 'Expected Productivity %',
                    data: milestones.map(m => m.target || 0),
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236,72,153,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    }

    function renderMilestones(elementId, milestones = [], current) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        if (!milestones.length) {
            container.innerHTML = '<p>No milestones configured.</p>';
            return;
        }
        milestones.forEach((milestone, index) => {
            const card = document.createElement('div');
            const isCurrent = current && current.index === index;
            card.className = `rounded-2xl border border-gray-100 p-4 ${isCurrent ? 'bg-blue-50 border-blue-200' : 'bg-white'}`;
            card.innerHTML = `
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-gray-800">${milestone.label || `Milestone ${index + 1}`}</p>
                    <span class="text-sm text-gray-500">${milestone.duration || ''}</span>
                </div>
                <p class="mt-2 text-sm text-gray-600">${milestone.description || 'No description provided.'}</p>
                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                    <span>Target: ${milestone.target || '--'}%</span>
                    <span>${milestone.status || ''}</span>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function renderSupportActions(elementId, actions) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        const list = Array.isArray(actions) ? actions : [{ description: actions, priority: 'High' }];
        list.forEach(action => {
            const card = document.createElement('div');
            card.className = 'action-card';
            card.innerHTML = `
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">${action.priority || 'Standard'} priority</p>
                <p class="text-sm text-gray-700">${action.description || action}</p>
            `;
            container.appendChild(card);
        });
    }

    function renderDevelopmentTable(gaps) {
        const tbody = document.getElementById('development-plan-table');
        tbody.innerHTML = '';
        if (!gaps.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-5 text-sm text-gray-500 text-center">No skill gaps detected.</td></tr>';
            return;
        }
        gaps.forEach(gap => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm text-gray-800">${gap.skill || 'Skill'}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${gap.severity || gap.level || 'Moderate'}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${gap.action || gap.recommendation || 'Upskill via structured programs.'}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${gap.timeline || '90 days'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderDevelopmentLearning(recommendations) {
        const container = document.getElementById('development-learning');
        container.innerHTML = '';
        if (!recommendations.length) {
            container.innerHTML = '<p>No learning recommendations available.</p>';
            return;
        }
        recommendations.forEach(rec => {
            const card = document.createElement('div');
            card.className = 'rounded-2xl border border-gray-100 bg-white p-4 shadow-sm';
            card.innerHTML = `
                <p class="text-sm font-semibold text-gray-800">${rec.title || 'Recommendation'}</p>
                <p class="mt-2 text-sm text-gray-600">${rec.description || rec.details || 'Complete suggested learning path.'}</p>
                ${rec.provider ? `<p class="mt-2 text-xs text-gray-500">Provider: ${rec.provider}</p>` : ''}
            `;
            container.appendChild(card);
        });
    }

    function renderDevelopmentMetrics(timeline) {
        const metrics = {
            immediate: 'development-immediate',
            short_term: 'development-short',
            medium_term: 'development-medium',
            long_term: 'development-long'
        };
        Object.entries(metrics).forEach(([key, elementId]) => {
            const value = timeline[key] || [];
            document.getElementById(elementId).textContent = `${value.length} items`;
        });
    }

    function renderCareerTree(elementId, roles) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        if (!roles.length) {
            container.innerHTML = '<div class="rounded-2xl border border-gray-100 p-6 bg-white">No career progression data available.</div>';
            return;
        }
        roles.forEach((role, index) => {
            const card = document.createElement('div');
            card.className = 'rounded-2xl border border-gray-100 bg-white p-6 relative';
            card.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-wide text-gray-500">Stage ${index + 1}</p>
                        <p class="text-lg font-semibold text-gray-800">${role.title || 'Future Role'}</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">Readiness: ${role.readiness || '--'}%</span>
                </div>
                <p class="mt-3 text-sm text-gray-600">${role.description || 'Role description pending.'}</p>
                <div class="mt-4 text-xs text-gray-500 flex items-center justify-between">
                    <span>Timeline: ${role.timeline || 'TBD'} months</span>
                    <span>Confidence: ${role.confidence || '--'}%</span>
                </div>
            `;
            if (index < roles.length - 1) {
                const connector = document.createElement('div');
                connector.className = 'absolute left-1/2 transform -translate-x-1/2 bottom-0 translate-y-3 h-6 w-0.5 bg-gradient-to-b from-blue-200 to-purple-200';
                card.appendChild(connector);
            }
            container.appendChild(card);
        });
    }

    function renderList(elementId, items, fallback) {
        const container = document.getElementById(elementId);
        container.innerHTML = '';
        if (!items || !items.length) {
            container.innerHTML = `<li>${fallback}</li>`;
            return;
        }
        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'flex items-start space-x-3';
            li.innerHTML = `
                <span class="mt-1 w-2 h-2 rounded-full bg-blue-400"></span>
                <span>${typeof item === 'string' ? item : item.description || JSON.stringify(item)}</span>
            `;
            container.appendChild(li);
        });
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        let result;
        try {
            result = await response.json();
        } catch {
            throw new Error(`Server error (${response.status}). Check logs.`);
        }
        if (!result.success) {
            // Strip raw PHP traces for cleaner UX
            const msg = (result.message || 'Request failed').split('\n')[0].substring(0, 120);
            throw new Error(msg);
        }
        return result;
    }

    function showLoading(message) {
        document.getElementById('predictive-loading-message').textContent = message;
        document.getElementById('predictive-loading').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('predictive-loading').classList.add('hidden');
    }

    function showToast(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        // Show visible notification
        const existing = document.getElementById('pa-toast');
        if (existing) existing.remove();
        const colors = {
            success: 'linear-gradient(135deg,#10b981,#059669)',
            error:   'linear-gradient(135deg,#ef4444,#dc2626)',
            warning: 'linear-gradient(135deg,#f59e0b,#d97706)',
            info:    'linear-gradient(135deg,#6366f1,#7c3aed)',
        };
        const toast = document.createElement('div');
        toast.id = 'pa-toast';
        toast.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:.875rem 1.5rem;border-radius:.875rem;color:#fff;font-size:.875rem;font-weight:700;background:${colors[type]||colors.info};box-shadow:0 8px 24px rgba(0,0,0,.15);max-width:360px;animation:paSlideIn .3s ease both`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4500);
    }

    async function populateApplicationsDropdown() {
        const select = document.getElementById('application-id-input');
        try {
            const resp = await fetch('/api/scout/predictive/applications', {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '', 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const data = await resp.json();
            if (data.success && data.applications && data.applications.length > 0) {
                select.innerHTML = '<option value="">— Select a candidate application —</option>';
                data.applications.forEach(app => {
                    const opt = document.createElement('option');
                    opt.value = app.id;
                    opt.textContent = app.label;
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="">No applications found for your company</option>';
            }
        } catch (e) {
            select.innerHTML = '<option value="">Failed to load applications</option>';
            console.error('Failed to load applications', e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchTab('overview');
        populateApplicationsDropdown();
    });
</script>
@endpush
