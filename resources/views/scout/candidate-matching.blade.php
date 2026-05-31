@extends('layouts.dashboard')

@section('title', 'Candidate Matching - S.C.O.U.T.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('employer.scout.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm hover:shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">AI-Powered Candidate Matching</h1>
            <p class="mt-2 text-gray-600">Predict candidate success based on your company's DNA</p>
        </div>

        <!-- Candidate Search -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Select Candidate</h2>
            <div class="flex gap-4">
                <input type="text" id="candidate-search" placeholder="Search candidates by name or email..." 
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <button onclick="searchCandidates()" class="px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>Search
                </button>
            </div>
            <div id="candidates-list" class="mt-4 space-y-2"></div>
        </div>

        <!-- Match Results -->
        <div id="match-results" class="hidden">
            <!-- Overall Score -->
            <div class="bg-white rounded-xl shadow-md p-8 mb-8 text-center">
                <p class="text-sm text-gray-600 mb-2">Overall Success Score</p>
                <div class="relative inline-block">
                    <svg class="w-48 h-48">
                        <circle cx="96" cy="96" r="88" stroke="#E2E2E0" stroke-width="8" fill="none"></circle>
                        <circle id="score-circle" cx="96" cy="96" r="88" stroke="url(#gradient)" stroke-width="8" fill="none" 
                            stroke-dasharray="553" stroke-dashoffset="553" transform="rotate(-90 96 96)" class="transition-all duration-1000"></circle>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#2D6CDF;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#2D6CDF;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <p id="overall-score" class="text-5xl font-bold text-gray-900">0</p>
                        <p class="text-sm text-gray-600">/ 100</p>
                    </div>
                </div>
                <p id="recommendation" class="mt-4 text-lg font-semibold"></p>
            </div>

            <!-- Fit Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Cultural Fit</h3>
                        <span id="cultural-score" class="text-2xl font-bold text-pink-600">--</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full">
                        <div id="cultural-bar" class="h-full bg-pink-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="cultural-level" class="mt-2 text-xs text-gray-600"></p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Skill Fit</h3>
                        <span id="skill-score" class="text-2xl font-bold text-blue-600">--</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full">
                        <div id="skill-bar" class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="skill-level" class="mt-2 text-xs text-gray-600"></p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Work Style Fit</h3>
                        <span id="workstyle-score" class="text-2xl font-bold text-purple-600">--</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full">
                        <div id="workstyle-bar" class="h-full bg-purple-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="workstyle-level" class="mt-2 text-xs text-gray-600"></p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Performance</h3>
                        <span id="performance-score" class="text-2xl font-bold text-green-600">--</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full">
                        <div id="performance-bar" class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <p id="performance-level" class="mt-2 text-xs text-gray-600"></p>
                </div>
            </div>

            <!-- Strengths & Concerns -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i data-lucide="thumbs-up" class="w-5 h-5 mr-2 text-green-600"></i>
                        Key Strengths
                    </h3>
                    <ul id="strengths-list" class="space-y-2"></ul>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 text-yellow-600"></i>
                        Potential Concerns
                    </h3>
                    <ul id="concerns-list" class="space-y-2"></ul>
                </div>
            </div>

            <!-- AI Assessment -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl shadow-md p-6 border-2 border-purple-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="sparkles" class="w-5 h-5 mr-2 text-purple-600"></i>
                    AI Holistic Assessment
                </h3>
                <p id="ai-assessment" class="text-gray-700 mb-4"></p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Success Probability</p>
                        <p id="success-probability" class="text-lg font-bold text-purple-900"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Final Recommendation</p>
                        <p id="final-recommendation" class="text-lg font-bold text-purple-900"></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();
const companyId = {{ auth()->user()->company_id ?? 'null' }};

// Allow pressing Enter in search box
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('candidate-search');
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') searchCandidates();
        });
    }
});

async function searchCandidates() {
    const query = document.getElementById('candidate-search').value.trim();
    if (!query) return;

    const listEl = document.getElementById('candidates-list');
    listEl.innerHTML = '<p class="text-sm text-gray-500 py-2">Searching...</p>';

    try {
        const response = await fetch(`/employer/scout/search-candidates?q=${encodeURIComponent(query)}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });
        const result = await response.json();
        const candidates = result.data || [];

        if (!candidates.length) {
            listEl.innerHTML = '<p class="text-sm text-gray-500 py-2">No candidates found.</p>';
            return;
        }

        listEl.innerHTML = candidates.map(c => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-purple-50 cursor-pointer border border-transparent hover:border-purple-200 transition"
                 onclick="analyzeCandidate(${c.id})">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                        ${c.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">${c.name}</p>
                        <p class="text-xs text-gray-500">${c.email}</p>
                    </div>
                </div>
                <span class="text-xs text-purple-600 font-medium bg-purple-100 px-2 py-1 rounded-full">Analyze &rarr;</span>
            </div>
        `).join('');
    } catch (error) {
        listEl.innerHTML = '<p class="text-sm text-red-500 py-2">Search failed. Please try again.</p>';
        console.error('Search failed:', error);
    }
}

async function analyzeCandidate(candidateId) {
    document.getElementById('match-results').classList.add('hidden');
    const listEl = document.getElementById('candidates-list');
    listEl.innerHTML = '<p class="text-sm text-purple-600 font-medium py-2">Running AI analysis...</p>';

    try {
        const response = await fetch(`/api/scout/candidate-match/${candidateId}?company_id=${companyId}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });

        const result = await response.json();
        if (result.success) {
            renderMatchResults(result.data);
            listEl.innerHTML = '';
        } else {
            listEl.innerHTML = `<p class="text-sm text-red-500 py-2">${result.message || 'Analysis failed.'}</p>`;
        }
    } catch (error) {
        listEl.innerHTML = '<p class="text-sm text-red-500 py-2">Analysis failed. Please try again.</p>';
        console.error('Match analysis failed:', error);
    }
}

function renderMatchResults(data) {
    const prediction = data.success_prediction;
    document.getElementById('match-results').classList.remove('hidden');

    // Low-confidence banner
    const existingBanner = document.getElementById('low-confidence-banner');
    if (existingBanner) existingBanner.remove();
    if (prediction.low_confidence) {
        const banner = document.createElement('div');
        banner.id = 'low-confidence-banner';
        banner.style.cssText = 'background:#FFF8EC;border:1px solid #E37400;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.84rem;color:#E37400;display:flex;align-items:center;gap:.5rem';
        banner.innerHTML = '<svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg><span><strong>Low confidence:</strong> Your DNA profile completeness is below 60%. Run DNA Analysis to improve accuracy of these predictions.</span>';
        document.getElementById('match-results').prepend(banner);
    }

    // Overall score
    const score = prediction.overall_success_score;
    document.getElementById('overall-score').textContent = score;
    document.getElementById('recommendation').textContent = prediction.recommendation;
    
    // Animate circle
    const circle = document.getElementById('score-circle');
    const circumference = 553;
    const offset = circumference - (score / 100) * circumference;
    circle.style.strokeDashoffset = offset;

    // Fit scores
    updateFitScore('cultural', prediction.cultural_fit);
    updateFitScore('skill', prediction.skill_fit);
    updateFitScore('workstyle', prediction.work_style_fit);
    updateFitScore('performance', prediction.performance_prediction);

    // Strengths & concerns
    renderList('strengths-list', prediction.strengths, 'green');
    renderList('concerns-list', prediction.concerns, 'yellow');

    // AI assessment
    if (prediction.ai_assessment) {
        document.getElementById('ai-assessment').textContent = prediction.ai_assessment.overall_assessment;
        document.getElementById('success-probability').textContent = prediction.ai_assessment.success_probability;
        document.getElementById('final-recommendation').textContent = prediction.ai_assessment.recommendation;
    }
}

function updateFitScore(type, data) {
    document.getElementById(`${type}-score`).textContent = data.score;
    document.getElementById(`${type}-bar`).style.width = data.score + '%';
    document.getElementById(`${type}-level`).textContent = data.level;
}

function renderList(containerId, items, color) {
    const container = document.getElementById(containerId);
    container.innerHTML = items.map(item => `
        <li class="flex items-start">
            <i data-lucide="check" class="w-4 h-4 text-${color}-600 mr-2 mt-0.5"></i>
            <span class="text-gray-700">${item}</span>
        </li>
    `).join('') || `<li class="text-gray-500">None identified</li>`;
    lucide.createIcons();
}
</script>
@endpush
@endsection
