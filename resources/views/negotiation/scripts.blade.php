@extends('layouts.dashboard')

@section('title', 'Negotiation Scripts - ' . $strategy->role)

@push('styles')
<style>
    .tab-button {
        transition: all 0.3s ease;
        border-bottom: 2px solid transparent;
        color: #737373;
    }
    .tab-button.active {
        color: #2D6CDF;
        border-bottom-color: #2D6CDF;
    }
    .tab-button:hover:not(.active) {
        color: #3D3D3D;
    }
    .script-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .script-card:hover {
        transform: translateY(-2px);
        box-shadow: none;
    }
    .placeholder {
        background: rgba(20, 71, 186, 0.2);
        color: #2D6CDF;
        padding: 0 4px;
        border-radius: 4px;
        font-weight: 600;
    }
    .tactic-highlight {
        background: rgba(20, 71, 186, 0.15);
        border-left: 3px solid #2D6CDF;
        padding: 8px 12px;
        margin: 12px 0;
        border-radius: 4px;
    }
    .copy-btn {
        transition: all 0.2s ease;
    }
    .copy-btn:hover {
        transform: scale(1.05);
    }
    .copy-btn.copied {
        background: #1E8E3E !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('negotiation.strategy', $strategy->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 mb-4 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Strategy
        </a>
        
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Negotiation Scripts</h1>
                <p class="text-gray-500">{{ $strategy->role }} at {{ $strategy->company_name }}</p>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">{{ $scripts->count() }} scripts</span>
            </div>
        </div>
    </div>

    <!-- Script Cards Grid -->
    <div id="scriptsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($scripts as $script)
        <div class="script-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm" 
             data-communication="{{ $script->script_type }}" 
             data-stage="{{ $script->script_stage }}"
             onclick="viewScriptDetail({{ $script->id }})">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $script->script_name }}</h3>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                            {{ ucfirst(str_replace('_', ' ', $script->script_stage)) }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                            {{ ucfirst(str_replace('_', ' ', $script->script_type)) }}
                        </span>
                    </div>
                    @if($script->tone)
                    <p class="text-xs text-gray-500">Tone: {{ ucfirst($script->tone) }}</p>
                    @endif
                </div>
                
                <div class="w-10 h-10 bg-gradient-to-br from-primary-color/20 to-primary-light/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            
            @if($script->subject_line && $script->script_type === 'email')
            <div class="mb-3">
                <p class="text-xs text-gray-500 mb-1">Subject:</p>
                <p class="text-sm text-gray-600 italic">{{ Str::limit($script->subject_line, 60) }}</p>
            </div>
            @endif
            
            <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                {{ Str::limit(strip_tags($script->opening ?? $script->body), 120) }}
            </p>
            
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ count($script->key_talking_points ?? []) }} talking points</span>
                <span class="text-indigo-600 hover:text-indigo-800 font-medium">View Full Script &rarr;</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Script Detail Modals -->
    @foreach($scripts as $script)
    <div id="scriptModal{{ $script->id }}" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target === this) closeScriptDetail({{ $script->id }})">
        <div class="bg-white rounded-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200 shadow-2xl">
                <div class="sticky top-0 p-6 rounded-t-2xl z-10" style="background:#2D6CDF;">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-2">{{ $script->script_name }}</h2>
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-sm font-medium">
                                {{ ucfirst(str_replace('_', ' ', $script->script_stage)) }}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-sm font-medium">
                                {{ ucfirst(str_replace('_', ' ', $script->script_type)) }}
                            </span>
                            @if($script->tone)
                            <span class="text-white/80 text-sm">Tone: {{ ucfirst($script->tone) }}</span>
                            @endif
                        </div>
                    </div>
                    <button onclick="closeScriptDetail({{ $script->id }})" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Personalization Tool -->
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        <h3 class="text-gray-900 font-semibold">Personalization Settings</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-2">Your Name</label>
                            <input type="text" id="yourName{{ $script->id }}" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:border-indigo-500" placeholder="e.g., John Smith" oninput="updatePreview({{ $script->id }})">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-2">Hiring Manager Name</label>
                            <input type="text" id="managerName{{ $script->id }}" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:border-indigo-500" placeholder="e.g., Sarah Johnson" oninput="updatePreview({{ $script->id }})">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-2">Role Title</label>
                            <input type="text" id="roleTitle{{ $script->id }}" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:border-indigo-500" value="{{ $strategy->role }}" oninput="updatePreview({{ $script->id }})">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-2">Company Name</label>
                            <input type="text" id="companyName{{ $script->id }}" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:border-indigo-500" value="{{ $strategy->company_name }}" oninput="updatePreview({{ $script->id }})">
                        </div>
                    </div>
                </div>

                <!-- Script Preview -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200" id="scriptPreview{{ $script->id }}">
                    @if($script->subject_line && $script->script_type === 'email')
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-2">Subject:</p>
                        <p class="text-gray-900 font-medium">{{ $script->subject_line }}</p>
                    </div>
                    @endif
                    
                    @if($script->opening)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-500 mb-3">Opening</h4>
                        <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $script->opening }}</div>
                    </div>
                    @endif
                    
                    @if($script->body)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-500 mb-3">Main Body</h4>
                        <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $script->body }}</div>
                    </div>
                    @endif
                    
                    @if($script->closing)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-500 mb-3">Closing</h4>
                        <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $script->closing }}</div>
                    </div>
                    @endif
                </div>

                <!-- Key Talking Points -->
                @if($script->key_talking_points && count($script->key_talking_points) > 0)
                <div class="bg-blue-500/10 border-l-4 border-blue-500 rounded-lg p-4">
                    <h4 class="text-gray-900 font-semibold mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Key Talking Points
                    </h4>
                    <ul class="space-y-2">
                        @foreach($script->key_talking_points as $point)
                        <li class="flex items-start text-sm text-gray-700">
                            <span class="text-blue-500 mr-2">&bull;</span>
                            <span>{{ $point }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Phrases to Use & Avoid -->
                <div class="grid md:grid-cols-2 gap-4">
                    @if($script->phrases_to_use && count($script->phrases_to_use) > 0)
                    <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                        <h4 class="text-green-700 font-semibold mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Phrases to Use
                        </h4>
                        <ul class="space-y-2">
                            @foreach($script->phrases_to_use as $phrase)
                            <li class="text-sm text-gray-700 flex items-start">
                                <span class="text-green-500 mr-2">&#10003;</span>
                                <span>"{{ $phrase }}"</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    @if($script->phrases_to_avoid && count($script->phrases_to_avoid) > 0)
                    <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                        <h4 class="text-red-700 font-semibold mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Phrases to Avoid
                        </h4>
                        <ul class="space-y-2">
                            @foreach($script->phrases_to_avoid as $phrase)
                            <li class="text-sm text-gray-700 flex items-start">
                                <span class="text-red-500 mr-2">&times;</span>
                                <span>"{{ $phrase }}"</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-4 border-t border-gray-200">
                    <button onclick="copyScript({{ $script->id }})" class="copy-btn flex-1 py-3 bg-gradient-to-r from-primary-color to-primary-light text-white rounded-lg font-semibold hover:shadow-lg transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                        <span class="copy-text">Copy to Clipboard</span>
                    </button>
                    <button onclick="closeScriptDetail({{ $script->id }})" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
// Filter by communication method
let currentCommunication = null;
let currentStage = 'all';

function filterByCommunication(method) {
    currentCommunication = method;
    applyFilters();
    
    // Update tab styles
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab-button[data-communication="${method}"]`).classList.add('active');
}

function filterByStage(stage) {
    currentStage = stage;
    applyFilters();
    
    // Update stage filter styles
    document.querySelectorAll('.stage-filter').forEach(btn => {
        btn.classList.remove('active', 'bg-indigo-100', 'text-indigo-700');
        btn.classList.add('bg-gray-100', 'text-gray-600');
    });
    const activeStageBtn = document.querySelector(`.stage-filter[data-stage="${stage}"]`);
    if (activeStageBtn) {
        activeStageBtn.classList.add('active', 'bg-indigo-100', 'text-indigo-700');
        activeStageBtn.classList.remove('bg-gray-100', 'text-gray-600');
    }
}

function applyFilters() {
    const scripts = document.querySelectorAll('.script-card');
    scripts.forEach(script => {
        const scriptComm = script.getAttribute('data-communication');
        const scriptStage = script.getAttribute('data-stage');
        
        const matchesComm = !currentCommunication || scriptComm === currentCommunication;
        const matchesStage = currentStage === 'all' || scriptStage === currentStage;
        
        script.style.display = (matchesComm && matchesStage) ? 'block' : 'none';
    });
}

// Script detail modal functions
function viewScriptDetail(scriptId) {
    document.getElementById('scriptModal' + scriptId).classList.remove('hidden');
    document.getElementById('scriptModal' + scriptId).classList.add('flex');
}

function closeScriptDetail(scriptId) {
    document.getElementById('scriptModal' + scriptId).classList.add('hidden');
    document.getElementById('scriptModal' + scriptId).classList.remove('flex');
}

// Personalization preview update
function updatePreview(scriptId) {
    const yourName = document.getElementById('yourName' + scriptId).value || '[Your Name]';
    const managerName = document.getElementById('managerName' + scriptId).value || '[Hiring Manager Name]';
    const roleTitle = document.getElementById('roleTitle' + scriptId).value || '[Role]';
    const companyName = document.getElementById('companyName' + scriptId).value || '[Company]';
    
    const preview = document.getElementById('scriptPreview' + scriptId);
    let content = preview.innerHTML;
    
    // Replace placeholders
    content = content.replace(/\[Your Name\]/g, `<span class="placeholder">${yourName}</span>`);
    content = content.replace(/\[Hiring Manager Name\]/g, `<span class="placeholder">${managerName}</span>`);
    content = content.replace(/\[Role\]/g, `<span class="placeholder">${roleTitle}</span>`);
    content = content.replace(/\[Company\]/g, `<span class="placeholder">${companyName}</span>`);
    
    preview.innerHTML = content;
}

// Copy script to clipboard
async function copyScript(scriptId) {
    const preview = document.getElementById('scriptPreview' + scriptId);
    const text = preview.innerText;
    
    try {
        await navigator.clipboard.writeText(text);
        
        // Visual feedback
        const btn = event.currentTarget;
        const originalText = btn.querySelector('.copy-text').textContent;
        btn.classList.add('copied');
        btn.querySelector('.copy-text').textContent = 'Copied!';
        
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.querySelector('.copy-text').textContent = originalText;
        }, 2000);
    } catch (err) {
        alert('Failed to copy script. Please select and copy manually.');
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="scriptModal"]').forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }
});

// Initialize: show all scripts on page load, no communication filter
document.addEventListener('DOMContentLoaded', function() {
    // Show all scripts initially
    document.querySelectorAll('.script-card').forEach(card => card.style.display = 'block');
});

// Override applyFilters to skip comm filter when 'all' tabs active
function showAllCommunications() {
    currentCommunication = null;
    document.querySelectorAll('.script-card').forEach(card => {
        const scriptStage = card.getAttribute('data-stage');
        const matchesStage = currentStage === 'all' || scriptStage === currentStage;
        card.style.display = matchesStage ? 'block' : 'none';
    });
}
</script>
@endpush
@endsection
