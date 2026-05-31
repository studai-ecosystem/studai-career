@extends('layouts.dashboard')

@section('page-title', 'Post a Job')
@section('page-description', 'Post a job instantly or let Orin&trade; guide you through a detailed setup.')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Profile warning --}}
    @if($profile && $profile->completeness_score < 50)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">?�</span>
                <div>
                    <p class="font-semibold text-yellow-800">Company profile is {{ $profile->completeness_score }}% complete</p>
                    <p class="text-sm text-yellow-700">A fuller profile helps Orin&trade; generate better job descriptions.</p>
                </div>
            </div>
            <a href="{{ route('employer.orin-onboarding') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors">
                Complete Profile
            </a>
        </div>
    @endif

    {{-- ── STEP 1: Role Input ── --}}
    <div id="step-role" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">What role are you hiring for?</h2>
        <p class="text-sm text-gray-400 mb-5">Fill in the details below and click <strong>Post Job Now</strong> &mdash; Orin&trade; will write the full description for you.</p>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                <input id="role-name" type="text" required autofocus
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                    placeholder="e.g. Senior Backend Engineer, Marketing Manager">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Brief Description <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea id="role-description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none"
                    placeholder="Key responsibilities, skills needed, team size..."></textarea>
            </div>

            {{-- Quick-post extra fields (collapsible) --}}
            <div>
                <button type="button" onclick="toggleExtras()" id="extras-toggle"
                    class="text-sm text-primary hover:underline flex items-center gap-1">
                    <svg id="extras-chevron" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    Add more details (salary, work mode, experience level)
                </button>
                <div id="extras-panel" class="hidden mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Salary Range (Rs./yr)</label>
                        <input id="salary" type="text" placeholder="e.g. 8L-15L"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Work Mode</label>
                        <select id="work-mode" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Let Orin&trade; decide</option>
                            <option value="remote">Remote</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="onsite">On-site</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Experience Level</label>
                        <select id="exp-level" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Let Orin&trade; decide</option>
                            <option value="entry">Entry Level</option>
                            <option value="junior">Junior (1-3 yrs)</option>
                            <option value="mid">Mid (3-5 yrs)</option>
                            <option value="senior">Senior (5+ yrs)</option>
                            <option value="lead">Lead / Manager</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Primary CTA --}}
            <button id="quick-post-btn" onclick="quickPost()"
                class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2 text-base shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span id="quick-post-label">Post Job Now</span>
            </button>

            <div class="relative flex items-center gap-3">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">OR</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            {{-- Secondary: advanced chat --}}
            <button id="start-chat-btn"
                class="w-full py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/></svg>
                Guide me with Orin&trade; (detailed Q&amp;A)
            </button>
        </div>
    </div>

    {{-- ── STEP 2: Chat (advanced mode) ── --}}
    <div id="step-chat" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center text-white font-bold">O</div>
            <div>
                <p class="text-white font-semibold text-sm">Orin&trade; AI Job Creator</p>
                <p class="text-blue-200 text-xs" id="role-header-text"></p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button onclick="cancelChat()" class="text-blue-200 hover:text-white text-xs underline">&larr; Back</button>
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            </div>
        </div>

        <div id="chat-messages" class="p-5 space-y-4 h-[380px] overflow-y-auto bg-gray-50"></div>

        <div id="typing-indicator" class="hidden px-5 pb-2 bg-gray-50">
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">O</div>
                <div class="bg-white border border-gray-200 rounded-2xl px-4 py-2.5 inline-flex gap-1 items-center">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full" style="animation:blink 1.4s infinite both;"></span>
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full" style="animation:blink 1.4s .2s infinite both;"></span>
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full" style="animation:blink 1.4s .4s infinite both;"></span>
                </div>
            </div>
        </div>

        {{-- Quick-action chips always visible in chat --}}
        <div id="chat-chips" class="px-4 pt-3 pb-0 bg-white flex flex-wrap gap-2">
            <button onclick="sendMessage('post job')" class="px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-full hover:bg-primary-dark transition-colors">
                &#9889; Post Job Now
            </button>
            <button onclick="sendMessage('GENERATE')" class="px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-full hover:bg-green-700 transition-colors">
                &#10024; Generate Listing
            </button>
            <button onclick="sendMessage('skip to generate')" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-200 transition-colors">
                Skip Q&A
            </button>
        </div>

        <div class="border-t border-gray-100 p-4 bg-white flex gap-3">
            <input id="chat-input" type="text"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder='Type a reply or "post job" to publish now...' autocomplete="off">
            <button id="send-btn"
                class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl text-sm transition-colors">
                Send &rarr;
            </button>
        </div>
    </div>

    {{-- ── STEP 3: Loading ── --}}
    <div id="step-loading" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 relative">
            <svg class="animate-spin w-16 h-16 text-primary" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Orin&trade; is generating your job listing...</h3>
        <p class="text-gray-400 text-sm">Generating job description, application form, and shareable link.</p>
    </div>

    {{-- ── STEP 4: Success ── --}}
    <div id="step-success" class="hidden bg-white rounded-2xl shadow-sm border border-green-200 p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900" id="success-title">Job Posted Successfully!</h2>
        <p class="text-gray-500 text-sm mt-2">Share this link on LinkedIn, WhatsApp, or job boards.</p>

        <div class="mt-5 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1.5 font-medium uppercase tracking-wide">Shareable Application Link</p>
            <div class="flex items-center gap-2">
                <input id="apply-link-input" type="text" readonly
                    class="flex-1 text-sm font-mono text-primary bg-white border border-blue-200 rounded-lg px-3 py-2 focus:outline-none">
                <button id="copy-btn" onclick="copyLink()"
                    class="px-3 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition-colors">
                    Copy
                </button>
            </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('employer.home') }}" class="px-5 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                Go to Dashboard
            </a>
            <a id="view-job-btn" href="#" target="_blank" class="px-5 py-2.5 border border-blue-200 text-primary rounded-xl text-sm font-medium hover:bg-blue-50 transition-colors">
                View Application Page
            </a>
            <button onclick="resetCreator()" class="px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors">
                Post Another Job
            </button>
        </div>
    </div>

    {{-- My Jobs --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Your Job Listings</h3>
            <button onclick="loadMyJobs()" class="text-sm text-primary hover:underline">Refresh</button>
        </div>
        <div id="my-jobs-list" class="space-y-3">
            <p class="text-sm text-gray-400 text-center py-4">Loading...</p>
        </div>
    </div>

</div>

<style>
@keyframes blink { 0%,80%,100%{opacity:0} 40%{opacity:1} }
.chat-bubble-orin { background: white; border: 1px solid #E2E2E0; }
.chat-bubble-user { background: #2D6CDF; color: white; }
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const QUICK_URL = '{{ route("employer.orin-job-creator.quick-post") }}';
const CHAT_URL  = '{{ route("employer.orin-job-creator.chat") }}';
const JOBS_URL  = '{{ route("employer.orin-job-creator.my-jobs") }}';

// Keyword triggers that mean "just post it now"
const POST_NOW_KEYWORDS = ['post job', 'post it', 'publish', 'generate', 'done', 'just post', 'create job', 'submit', 'go ahead', 'skip'];

let conversationHistory = [];
let roleName = '', roleDescription = '';
let isComplete = false;

// ── Toggle extras panel ─────────────────────────────────────────────────────
function toggleExtras() {
    const panel = document.getElementById('extras-panel');
    const chevron = document.getElementById('extras-chevron');
    panel.classList.toggle('hidden');
    chevron.classList.toggle('rotate-90');
}

// ── Quick Post (one-click) ──────────────────────────────────────────────────
async function quickPost() {
    roleName = document.getElementById('role-name').value.trim();
    if (!roleName) {
        document.getElementById('role-name').focus();
        document.getElementById('role-name').classList.add('border-red-400', 'ring-2', 'ring-red-200');
        setTimeout(() => document.getElementById('role-name').classList.remove('border-red-400', 'ring-2', 'ring-red-200'), 2000);
        return;
    }

    roleDescription = document.getElementById('role-description').value.trim();
    const salary   = document.getElementById('salary')?.value.trim() ?? '';
    const workMode = document.getElementById('work-mode')?.value ?? '';
    const expLevel = document.getElementById('exp-level')?.value ?? '';

    // Append extras to description so AI picks them up
    let fullDescription = roleDescription;
    if (salary)   fullDescription += (fullDescription ? '\n' : '') + 'Salary: ' + salary;
    if (workMode) fullDescription += (fullDescription ? '\n' : '') + 'Work mode: ' + workMode;
    if (expLevel) fullDescription += (fullDescription ? '\n' : '') + 'Experience level: ' + expLevel;

    showStep('loading');

    try {
        const resp = await fetch(QUICK_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ role_name: roleName, role_description: fullDescription }),
        });
        const data = await resp.json();

        if (!resp.ok || data.error) {
            showStep('role');
            alert(data.error ?? 'Failed to post job. Please try again.');
            return;
        }

        document.getElementById('apply-link-input').value = data.apply_url;
        document.getElementById('view-job-btn').href = data.apply_url;
        document.getElementById('success-title').textContent = `"${data.job_title}" Posted!`;
        showStep('success');
        loadMyJobs();
    } catch (err) {
        showStep('role');
        alert('Something went wrong. Please try again.');
    }
}

// ── Chat mode ───────────────────────────────────────────────────────────────
document.getElementById('start-chat-btn').addEventListener('click', () => {
    roleName = document.getElementById('role-name').value.trim();
    if (!roleName) { document.getElementById('role-name').focus(); return; }
    roleDescription = document.getElementById('role-description').value.trim();
    showStep('chat');
    document.getElementById('role-header-text').textContent = 'Creating: ' + roleName;
    startConversation();
});

async function startConversation() {
    showTyping();
    try {
        const resp = await apiChat([]);
        hideTyping();
        addMessage('assistant', resp.message);
        conversationHistory.push({ role: 'assistant', content: resp.message });
    } catch {
        hideTyping();
        addMessage('assistant', "Hi! I'll help you create a great job posting for " + roleName + ". You can answer my questions, or click ⚡ Post Job Now anytime to publish immediately.");
    }
}

async function sendMessage(text) {
    const trimmed = text.trim();
    if (isComplete || !trimmed) return;

    // Detect "post job" intent → switch to quick-post
    const lower = trimmed.toLowerCase();
    if (POST_NOW_KEYWORDS.some(kw => lower === kw || lower.startsWith(kw + ' ') || lower.endsWith(' ' + kw))) {
        addMessage('user', trimmed);
        addMessage('assistant', "Got it! Posting your job now...");
        await doQuickPostFromChat();
        return;
    }

    addMessage('user', trimmed);
    conversationHistory.push({ role: 'user', content: trimmed });
    document.getElementById('chat-input').value = '';
    document.getElementById('send-btn').disabled = true;
    showTyping();

    try {
        const resp = await apiChat(conversationHistory);
        hideTyping();
        addMessage('assistant', resp.message);
        conversationHistory.push({ role: 'assistant', content: resp.message });

        if (resp.complete) {
            isComplete = true;
            document.getElementById('apply-link-input').value = resp.apply_url;
            document.getElementById('view-job-btn').href = resp.apply_url;
            document.getElementById('success-title').textContent = `"${resp.job_title}" Posted!`;
            showStep('success');
            loadMyJobs();
        } else if (resp.ready_to_generate) {
            setTimeout(() => {
                const genBtn = document.createElement('button');
                genBtn.className = 'w-full py-2.5 mt-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors';
                genBtn.textContent = 'Generate Job Listing Now';
                genBtn.addEventListener('click', () => sendMessage('GENERATE'));
                document.getElementById('chat-messages').appendChild(genBtn);
                document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
            }, 400);
        }
    } catch {
        hideTyping();
        addMessage('assistant', "Something went wrong. You can click ⚡ Post Job Now above to publish immediately.");
    } finally {
        document.getElementById('send-btn').disabled = isComplete;
    }
}

async function doQuickPostFromChat() {
    showStep('loading');
    try {
        const resp = await fetch(QUICK_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ role_name: roleName, role_description: roleDescription }),
        });
        const data = await resp.json();
        if (!resp.ok || data.error) { showStep('chat'); addMessage('assistant', data.error ?? 'Failed to post. Try again.'); return; }
        document.getElementById('apply-link-input').value = data.apply_url;
        document.getElementById('view-job-btn').href = data.apply_url;
        document.getElementById('success-title').textContent = `"${data.job_title}" Posted!`;
        showStep('success');
        loadMyJobs();
    } catch { showStep('chat'); addMessage('assistant', 'Something went wrong. Please try again.'); }
}

async function apiChat(history) {
    const resp = await fetch(CHAT_URL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ role_name: roleName, role_description: roleDescription, history }),
    });
    if (!resp.ok) throw new Error('API error');
    return resp.json();
}

// ── Helpers ─────────────────────────────────────────────────────────────────
function showStep(name) {
    ['role', 'chat', 'loading', 'success'].forEach(s =>
        document.getElementById('step-' + s)?.classList.add('hidden')
    );
    document.getElementById('step-' + name)?.classList.remove('hidden');
}

function addMessage(role, text) {
    const container = document.getElementById('chat-messages');
    const isOrin = role === 'assistant';
    const wrap = document.createElement('div');
    wrap.className = 'flex items-start gap-2.5 ' + (isOrin ? '' : 'flex-row-reverse');
    if (isOrin) {
        const av = document.createElement('div');
        av.className = 'w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5';
        av.textContent = 'O';
        wrap.appendChild(av);
    }
    const bubble = document.createElement('div');
    bubble.className = 'max-w-[85%] px-4 py-3 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap ' + (isOrin ? 'chat-bubble-orin text-gray-800' : 'chat-bubble-user');
    bubble.textContent = text;
    wrap.appendChild(bubble);
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
}

function showTyping() { document.getElementById('typing-indicator').classList.remove('hidden'); }
function hideTyping()  { document.getElementById('typing-indicator').classList.add('hidden'); }

function cancelChat() {
    showStep('role');
    document.getElementById('chat-messages').innerHTML = '';
    conversationHistory = [];
}

function copyLink() {
    const input = document.getElementById('apply-link-input');
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.textContent = 'Copied!';
        btn.classList.replace('bg-primary', 'bg-green-600');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.replace('bg-green-600', 'bg-primary'); }, 2000);
    });
}

function resetCreator() {
    conversationHistory = []; isComplete = false;
    roleName = ''; roleDescription = '';
    document.getElementById('role-name').value = '';
    document.getElementById('role-description').value = '';
    document.getElementById('chat-messages').innerHTML = '';
    document.getElementById('salary') && (document.getElementById('salary').value = '');
    showStep('role');
}

document.getElementById('send-btn').addEventListener('click', () => sendMessage(document.getElementById('chat-input').value.trim()));
document.getElementById('chat-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); sendMessage(document.getElementById('chat-input').value.trim()); }
});

async function loadMyJobs() {
    try {
        const resp = await fetch(JOBS_URL, { credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': CSRF } });
        const data = await resp.json();
        const container = document.getElementById('my-jobs-list');
        if (!data.jobs.length) {
            container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No jobs yet. Post your first job above.</p>';
            return;
        }
        const phaseColors = { open:'bg-green-100 text-green-700', closed:'bg-orange-100 text-orange-700', evaluating:'bg-blue-100 text-blue-700', ranked:'bg-purple-100 text-purple-700', complete:'bg-gray-100 text-gray-600', draft:'bg-yellow-100 text-yellow-700' };
        container.innerHTML = data.jobs.map(j => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm truncate">${j.title}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs ${phaseColors[j.phase] ?? 'bg-gray-100 text-gray-600'} px-2 py-0.5 rounded-full font-medium">${j.phase}</span>
                        <span class="text-xs text-gray-400">${j.applicants} applicants</span>
                        ${j.close_date ? `<span class="text-xs text-gray-400">Closes ${j.close_date}</span>` : ''}
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-3">
                    <button onclick="navigator.clipboard.writeText('${j.apply_url}')" title="Copy link"
                        class="px-2.5 py-1.5 text-xs bg-blue-50 text-primary rounded-lg hover:bg-blue-100 transition-colors font-medium">
                        Copy Link
                    </button>
                    <a href="${j.apply_url}" target="_blank" class="px-2.5 py-1.5 text-xs border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        View
                    </a>
                </div>
            </div>`).join('');
    } catch { /* silent */ }
}

loadMyJobs();
</script>
@endsection
