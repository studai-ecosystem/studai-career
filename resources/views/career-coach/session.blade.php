@extends('layouts.dashboard')

@section('title', $session->title . ' - AI Career Coach')

@push('styles')
<style>
/* ── Chat overlay (content area only — never covers the sidebar) ── */
#coach-overlay {
    position: fixed;
    top: 80px;
    bottom: 0;
    left: var(--coach-overlay-left, 252px);
    right: 0;
    z-index: 30;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

/* ── Chat panel (floating card) ── */
#coach-chat-wrap {
    width: 100%;
    max-width: 700px;
    height: calc(100vh - 80px - 2rem);
    max-height: 900px;
    background: #fff;
    border-radius: 1.5rem;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 30px 80px rgba(0,0,0,.4), 0 0 0 1px rgba(99,102,241,.15);
    position: relative;
}

/* ── Back button (inside header) ── */
#coach-back-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .9rem .45rem .6rem;
    background: rgba(255,255,255,.22);
    border: 1.5px solid rgba(255,255,255,.35);
    border-radius: 2rem;
    color: #fff;
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
    flex-shrink: 0;
    transition: background .15s;
}
#coach-back-btn:hover { background: rgba(255,255,255,.38); }

/* ── Header ── */
.coach-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    position: relative;
    flex-shrink: 0;
    padding: 1rem 1.25rem;
}

.coach-avatar-ring {
    width: 40px; height: 40px;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.4);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

/* ── Messages ── */
#messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    scroll-behavior: smooth;
    background: linear-gradient(180deg, #f8f7ff 0%, #fdf4ff 100%);
}
#messages-container::-webkit-scrollbar { width: 4px; }
#messages-container::-webkit-scrollbar-track { background: transparent; }
#messages-container::-webkit-scrollbar-thumb { background: rgba(99,102,241,.3); border-radius: 4px; }

/* message entry animation */
.msg-row {
    display: flex;
    animation: msgSlideIn .35s cubic-bezier(.22,.68,0,1.2) both;
    margin-bottom: 1.25rem;
    padding: 0 0.5rem;
}
@keyframes msgSlideIn {
    from { opacity: 0; transform: translateY(16px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.msg-row.user      { justify-content: flex-end; }
.msg-row.assistant { justify-content: flex-start; }

/* AI avatar */
.ai-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #818cf8, #a855f7);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(99,102,241,.25);
    align-self: flex-start;
    margin-top: 2px;
}

/* AI bubble */
.ai-bubble {
    background: rgba(255,255,255,0.97);
    border: 1px solid rgba(99,102,241,.12);
    border-radius: 0 1.1rem 1.1rem 1.1rem;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 12px rgba(99,102,241,.08);
    max-width: 75%;
    transition: box-shadow .2s;
}
.ai-bubble:hover { box-shadow: 0 4px 20px rgba(99,102,241,.15); }

/* User bubble */
.user-bubble {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border-radius: 1.1rem 0 1.1rem 1.1rem;
    padding: .85rem 1.2rem;
    max-width: 72%;
    box-shadow: 0 2px 12px rgba(99,102,241,.35);
    transition: box-shadow .2s;
}
.user-bubble:hover { box-shadow: 0 4px 20px rgba(99,102,241,.45); }
.user-bubble .ts { font-size: .7rem; color: rgba(255,255,255,.65); margin-top: .4rem; }
.user-prose { font-size: .97rem; line-height: 1.6; color: #fff; }
.user-prose p { margin: .3rem 0; }
.user-prose p:first-child { margin-top: 0; }
.user-prose p:last-child { margin-bottom: 0; }
.user-prose strong { color: #fff; font-weight: 700; }
.user-prose em { opacity: .9; }
.user-prose code { background: rgba(255,255,255,.2); padding: .1em .35em; border-radius: .3em; font-size: .82em; font-family: monospace; }
.user-prose ul, .user-prose ol { padding-left: 1.3rem; margin: .3rem 0; }
.user-prose li { margin: .2rem 0; }
.user-prose a { color: rgba(255,255,255,.85); text-decoration: underline; }

/* ── Prose inside AI bubble ── */
.ai-prose { font-size: 1rem; line-height: 1.7; color: #1f2937; }
.ai-prose h1,.ai-prose h2,.ai-prose h3 {
    font-weight: 700; margin: 1rem 0 .4rem;
    background: linear-gradient(90deg,#6366f1,#a855f7);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.ai-prose h1 { font-size: 1.15rem; }
.ai-prose h2 { font-size: 1.05rem; }
.ai-prose h3 { font-size: .95rem; }
.ai-prose p  { margin: .45rem 0; }
.ai-prose ul,.ai-prose ol { padding-left: 1.4rem; margin: .5rem 0; }
.ai-prose li { margin: .3rem 0; }
.ai-prose ul li::marker { color: #8b5cf6; }
.ai-prose ol li::marker { color: #6366f1; font-weight: 600; }
.ai-prose strong { color: #4f46e5; font-weight: 700; }
.ai-prose em { color: #7c3aed; }
.ai-prose code {
    background: #f0f4ff; color: #6366f1;
    padding: .1em .4em; border-radius: .3em;
    font-size: .82em; font-family: monospace;
}
.ai-prose pre {
    background: #1e1b4b; color: #e0e7ff;
    border-radius: .75rem; padding: 1rem;
    overflow-x: auto; margin: .75rem 0;
}
.ai-prose pre code { background: transparent; color: inherit; padding: 0; }
.ai-prose blockquote {
    border-left: 3px solid #8b5cf6;
    padding-left: 1rem; color: #6b7280;
    font-style: italic; margin: .75rem 0;
}
.ai-prose hr { border: none; border-top: 1px solid #e5e7eb; margin: 1rem 0; }
.ai-prose a { color: #6366f1; text-decoration: underline; }
.ai-prose .ts { font-size: .7rem; color: #9ca3af; margin-top: .6rem; display: block; }

/* ── Suggestion options (Claude-style) ── */
.option-cards {
    display: flex;
    flex-direction: column;
    gap: .45rem;
    margin-top: .85rem;
    padding-top: .75rem;
    border-top: 1px solid rgba(99,102,241,.1);
}
.option-card {
    display: flex;
    align-items: center;
    gap: .6rem;
    width: 100%;
    background: #fff;
    border: 1.5px solid rgba(99,102,241,.2);
    border-radius: .75rem;
    padding: .6rem .9rem;
    cursor: pointer;
    font-size: .92rem;
    font-weight: 600;
    color: #4f46e5;
    text-align: left;
    transition: all .18s ease;
    box-shadow: 0 1px 3px rgba(99,102,241,.06);
    position: relative;
    z-index: 10;
    pointer-events: all;
}
.option-card::before {
    content: '→';
    font-size: .8rem;
    opacity: .6;
    flex-shrink: 0;
    transition: transform .18s;
}
.option-card:hover {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 14px rgba(99,102,241,.4);
    transform: translateX(2px);
}
.option-card:hover::before { opacity: 1; transform: translateX(3px); }
.option-card:active { transform: scale(.98); }
.option-card--own {
    color: #6b7280;
    border-style: dashed;
    border-color: rgba(99,102,241,.25);
    background: transparent;
    font-weight: 500;
}
.option-card--own::before { content: ''; }
.option-card--own:hover {
    background: #f5f3ff;
    color: #6366f1;
    border-color: #6366f1;
    border-style: dashed;
    box-shadow: none;
    transform: none;
}

/* ── Typing indicator ── */
#typing-indicator { display: none; margin-bottom: 1rem; }
.dot-wave span {
    width: 8px; height: 8px;
    background: #8b5cf6;
    border-radius: 50%;
    display: inline-block;
    animation: dotWave 1.2s ease-in-out infinite;
}
.dot-wave span:nth-child(2) { animation-delay: .2s; }
.dot-wave span:nth-child(3) { animation-delay: .4s; }
@keyframes dotWave {
    0%,80%,100% { transform: scale(.7); opacity:.5; }
    40%          { transform: scale(1.1); opacity:1; }
}

/* ── Input area ── */
.coach-input-wrap {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(99,102,241,.12);
    padding: 1rem 1.25rem 1.1rem;
    flex-shrink: 0;
}
#message-input {
    width: 100%;
    padding: .75rem 3rem .75rem 1rem;
    border-radius: 1rem;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    resize: none;
    transition: border-color .2s, box-shadow .2s;
    font-size: .9rem;
    color: #1f2937;
    line-height: 1.5;
}
#message-input:focus {
    outline: none;
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12);
}
#send-btn {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border: none;
    border-radius: .9rem;
    padding: .7rem 1.2rem;
    cursor: pointer;
    display: flex; align-items: center; gap: .45rem;
    font-weight: 600; font-size: .88rem;
    box-shadow: 0 2px 10px rgba(99,102,241,.35);
    transition: transform .15s, box-shadow .15s, opacity .15s;
}
#send-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(99,102,241,.45);
}
#send-btn:active:not(:disabled) { transform: translateY(0); }
#send-btn:disabled { opacity: .6; cursor: not-allowed; }
.send-icon { transition: transform .2s; }
#send-btn:hover:not(:disabled) .send-icon { transform: translateX(2px) rotate(-10deg); }
</style>
@endpush

@section('content')
<div id="coach-overlay" x-bind:style="`--coach-overlay-left:${sidebarOpen ? '252px' : '72px'}`">
<div id="coach-chat-wrap">

    {{-- ── Header ── --}}
    <div class="coach-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('career-coach.index') }}" id="coach-back-btn">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <div class="coach-avatar-ring">&#127919;</div>
                <div>
                    <h1 class="font-bold text-white leading-tight text-sm">{{ $session->title }}</h1>
                    <p class="text-xs text-white/60">{{ $session->getTypeLabel() }}</p>
                </div>
            </div>
            <span class="flex items-center gap-1.5 px-2.5 py-1 text-xs rounded-full font-semibold"
                  style="background:rgba(255,255,255,.15);color:#fff;">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                Active
            </span>
        </div>
    </div>

    {{-- ── Error Toast ── --}}
    <div id="error-toast"
         class="hidden fixed top-4 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 max-w-sm"
         style="animation:msgSlideIn .3s ease both">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span id="error-text">Error</span>
        <button onclick="document.getElementById('error-toast').classList.add('hidden')" class="ml-2 text-white/70 hover:text-white">&#x2715;</button>
    </div>

    {{-- ── Messages ── --}}
    <div id="messages-container">
        <div id="msg-list">

        @foreach($messages as $msg)
        <div class="msg-row {{ $msg->role === 'user' ? 'user' : 'assistant' }}">
            @if($msg->role === 'assistant')
                @php
                    $rawContent = $msg->content;
                    $opts = [];
                    if (preg_match('/\*\*Options:\*\*\s*\n((?:[-•→*]\s*.+\n?)+)/i', $rawContent, $optMatch)) {
                        $mainText = trim(substr($rawContent, 0, strpos($rawContent, $optMatch[0])));
                        foreach (explode("\n", trim($optMatch[1])) as $line) {
                            $line = preg_replace('/^[-•→*]\s*/', '', trim($line));
                            if ($line) $opts[] = $line;
                        }
                    } else {
                        $mainText = $rawContent;
                    }
                @endphp
                <div class="flex items-start gap-2.5">
                    <div class="ai-avatar">&#127919;</div>
                    <div class="ai-bubble">
                        <div class="ai-prose">{!! \Illuminate\Support\Str::markdown($mainText) !!}</div>
                        @if(count($opts))
                            <div class="option-cards">
                                @foreach($opts as $opt)
                                    <button type="button" class="option-card" data-option="{{ e($opt) }}">{{ $opt }}</button>
                                @endforeach
                                <button type="button" class="option-card option-card--own" data-option="__own__">&#9999;&#65039; Write your own&hellip;</button>
                            </div>
                        @endif
                        <span class="ts">{{ $msg->created_at->format('g:i A') }}</span>
                    </div>
                </div>
            @else
                <div class="user-bubble">
                    <div class="user-prose">{!! \Illuminate\Support\Str::markdown($msg->content, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                    <div class="ts">{{ $msg->created_at->format('g:i A') }}</div>
                </div>
            @endif
        </div>
        @endforeach

        {{-- Typing indicator --}}
        <div id="typing-indicator" class="msg-row assistant">
            <div class="flex items-start gap-2.5">
                <div class="ai-avatar">&#127919;</div>
                <div class="ai-bubble" style="padding:.75rem 1rem;">
                    <div class="dot-wave flex gap-1.5 items-center">
                        <span></span><span></span><span></span>
                        <span style="background:transparent;width:auto;height:auto;animation:none;font-size:.78rem;color:#9ca3af;margin-left:.35rem;">
                            Thinking&hellip;
                        </span>
                    </div>
                </div>
            </div>
        </div>

        </div><!-- /msg-list -->
    </div>

    {{-- ── Input Area ── --}}
    <div class="coach-input-wrap">
        <div class="flex items-end gap-3">
                <div class="flex-1 relative">
                    <textarea
                        id="message-input"
                        placeholder="Ask anything… markdown supported (*bold*, `code`, - lists)"
                        rows="1"
                        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChat();}"
                        oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,150)+'px';"
                    ></textarea>
                    <button type="button" id="voice-btn" onclick="toggleVoice()"
                        class="absolute right-3 bottom-2.5 p-1 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                        title="Voice input">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5zm6 6c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                        </svg>
                    </button>
                </div>
                <button id="send-btn" onclick="sendChat()">
                    <span id="send-label">Send</span>
                    <svg class="w-4 h-4 send-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </div>

            <div id="voice-status" class="hidden mt-2 flex items-center gap-2 text-sm text-red-600">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse inline-block"></span>
                <span id="voice-status-text">Listening&hellip;</span>
                <button onclick="stopVoice()" class="ml-auto text-xs underline">Stop</button>
            </div>
            <div id="no-speech-msg" class="hidden mt-2 text-xs text-amber-600">
                &#9888;&#65039; Voice input requires Chrome or Edge browser.
            </div>

            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span id="msg-count" class="text-xs text-gray-400">{{ $messages->count() }} messages</span>
                <button onclick="confirmEndSession()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-red-600 border border-red-200 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    End Session
                </button>
            </div>

            {{-- Inline end-session confirmation --}}
            <div id="end-confirm" style="display:none;" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <p class="font-semibold mb-2">End this session?</p>
                <div class="flex gap-2">
                    <button onclick="endSession()" class="flex-1 py-1.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">Yes, end it</button>
                    <button onclick="document.getElementById('end-confirm').style.display='none'" class="flex-1 py-1.5 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors">Cancel</button>
                </div>
            </div>
    </div>

</div><!-- /coach-chat-wrap -->
</div><!-- /coach-overlay -->

@push('scripts')
{{-- marked.js for client-side markdown rendering --}}
<script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
<script>
// Configure marked
marked.setOptions({ breaks: true, gfm: true });

const SESSION_ID = {{ $session->id }};
const CSRF       = '{{ csrf_token() }}';
const MSG_URL    = '/career-coach/session/' + SESSION_ID + '/message';
const END_URL    = '/career-coach/session/' + SESSION_ID + '/end';
let sending  = false;
let recognition = null;
let isVoice  = false;
let msgCount = {{ $messages->count() }};

function scrollBottom() {
    const c = document.getElementById('messages-container');
    if (c) c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' });
}

function showError(msg) {
    document.getElementById('error-text').textContent = msg;
    const t = document.getElementById('error-toast');
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 6000);
}

function now12h() {
    return new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function parseOptions(content) {
    const m = content.match(/\*\*Options:\*\*\s*\n((?:[-•→*]\s*.+\n?)+)/i);
    if (!m) return { text: content, options: [] };
    const text = content.slice(0, m.index).trim();
    const options = m[1].split('\n')
        .map(l => l.replace(/^[-•→*]\s*/, '').trim())
        .filter(Boolean);
    return { text, options };
}

function buildOptionCards(options) {
    if (!options.length) return '';
    const cards = options.map(o => {
        const safe = o.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const attr = o.replace(/&/g,'&amp;').replace(/"/g,'&quot;');
        return `<button type="button" class="option-card" data-option="${attr}">${safe}</button>`;
    }).join('');
    const ownCard = `<button type="button" class="option-card option-card--own" data-option="__own__">✏️ Write your own…</button>`;
    return `<div class="option-cards">${cards}${ownCard}</div>`;
}

window.pickOption = function(text) {
    const input = document.getElementById('message-input');
    if (input) input.value = text;
    sendChat();
};

window.focusOwnInput = function() {
    const input = document.getElementById('message-input');
    if (!input) return;
    input.value = '';
    input.focus();
    input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
};

// Delegated click handler for all option cards
document.addEventListener('click', function(e) {
    const card = e.target.closest('.option-card');
    if (!card) return;
    const opt = card.dataset.option;
    if (opt === '__own__') { window.focusOwnInput(); }
    else if (opt) { window.pickOption(opt); }
});

function appendMessage(role, content, time) {
    const container = document.getElementById('msg-list');
    const typingEl  = document.getElementById('typing-indicator');

    const row = document.createElement('div');
    row.className = 'msg-row ' + role;

    if (role === 'assistant') {
        const { text, options } = parseOptions(content);
        const rendered = marked.parse(text);
        const cards    = buildOptionCards(options);
        row.innerHTML = `
            <div class="flex items-start gap-2.5">
                <div class="ai-avatar">&#127919;</div>
                <div class="ai-bubble">
                    <div class="ai-prose">${rendered}</div>
                    ${cards}
                    <span class="ts">${time}</span>
                </div>
            </div>`;
    } else {
        const rendered = marked.parse(content);
        row.innerHTML = `
            <div class="user-bubble">
                <div class="user-prose">${rendered}</div>
                <div class="ts">${time}</div>
            </div>`;
    }

    container.insertBefore(row, typingEl);
    msgCount++;
    document.getElementById('msg-count').textContent = msgCount + ' messages';

    // For AI replies scroll to the TOP of the new message so user reads from the start.
    // For user messages scroll to the bottom so the input stays visible.
    if (role === 'assistant') {
        setTimeout(() => row.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
    } else {
        scrollBottom();
    }
}

async function sendChat() {
    if (sending) return;
    const input = document.getElementById('message-input');
    const msg   = input.value.trim();
    if (!msg) return;

    sending = true;
    input.value = '';
    input.style.height = 'auto';

    const sendBtn   = document.getElementById('send-btn');
    const sendLabel = document.getElementById('send-label');
    sendBtn.disabled = true;
    sendLabel.textContent = 'Sending…';

    const typingEl = document.getElementById('typing-indicator');
    typingEl.style.display = 'flex';

    appendMessage('user', msg, now12h());
    scrollBottom();

    try {
        const res = await fetch(MSG_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ message: msg, is_voice: isVoice }),
        });
        isVoice = false;
        const data = await res.json();
        if (!res.ok) throw new Error(data.message ?? 'Server error ' + res.status);
        const reply = data.message?.content ?? data.content ?? '';
        if (reply) appendMessage('assistant', reply, now12h());
    } catch (e) {
        showError('Failed to get reply: ' + e.message);
    } finally {
        sending = false;
        sendBtn.disabled = false;
        sendLabel.textContent = 'Send';
        typingEl.style.display = 'none';
        scrollBottom();
    }
}

function confirmEndSession() {
    const el = document.getElementById('end-confirm');
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

async function endSession() {
    const btn = document.querySelector('#end-confirm button');
    if (btn) { btn.textContent = 'Ending…'; btn.disabled = true; }
    try {
        await fetch(END_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
    } catch (e) { /* ignore */ }
    window.location.href = '{{ route("career-coach.index") }}';
}

function toggleVoice() {
    recognition ? stopVoice() : startVoice();
}

function startVoice() {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SR) {
        const el = document.getElementById('no-speech-msg');
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 5000);
        return;
    }
    recognition = new SR();
    recognition.lang = 'en-US';
    recognition.interimResults = true;
    recognition.continuous = false;
    document.getElementById('voice-status').classList.remove('hidden');
    document.getElementById('voice-btn').classList.add('text-red-600', 'animate-pulse');

    recognition.onresult = (e) => {
        const t = Array.from(e.results).map(r => r[0].transcript).join('');
        document.getElementById('message-input').value = t;
        document.getElementById('voice-status-text').textContent = t ? '"' + t.slice(0,40) + '…"' : 'Listening…';
    };
    recognition.onend = () => {
        stopVoice();
        const v = document.getElementById('message-input').value.trim();
        if (v.length > 2) { isVoice = true; sendChat(); }
    };
    recognition.onerror = (e) => {
        stopVoice();
        if (e.error === 'not-allowed' || e.error === 'permission-denied') {
            showError('Microphone blocked. Click the 🔒 lock icon in your browser address bar → allow Microphone → refresh and try again.');
        } else if (e.error !== 'no-speech') {
            showError('Voice error: ' + e.error);
        }
    };
    recognition.start();
}

function stopVoice() {
    if (recognition) { try { recognition.stop(); } catch(e) {} recognition = null; }
    document.getElementById('voice-status').classList.add('hidden');
    document.getElementById('voice-btn').classList.remove('text-red-600', 'animate-pulse');
}

window.addEventListener('DOMContentLoaded', () => {
    // Scroll to the TOP of the last AI message so user reads from start, not mid-message
    const rows = document.querySelectorAll('.msg-row.assistant');
    const last = rows[rows.length - 1];
    if (last) {
        last.scrollIntoView({ behavior: 'instant', block: 'start' });
    } else {
        scrollBottom();
    }
    document.getElementById('message-input').focus();
});
</script>
@endpush
@endsection
