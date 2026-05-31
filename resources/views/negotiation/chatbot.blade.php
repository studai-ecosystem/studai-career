@extends('layouts.dashboard')

@section('title', 'AI Salary Negotiation Coach')

@push('styles')
<style>
/* Full-page shell */
.main-content { padding: 0 !important; max-width: 100% !important; }
html, body { overflow: hidden !important; height: 100% !important; }
.main-bg { overflow: hidden !important; }

/* Markdown */
.chat-md p            { margin: 0.35em 0; line-height: 1.65; }
.chat-md p:first-child{ margin-top: 0; }
.chat-md p:last-child { margin-bottom: 0; }
.chat-md h1,.chat-md h2,.chat-md h3 { font-weight: 700; color: #0C2E72; margin: 0.6em 0 0.2em; line-height: 1.3; }
.chat-md h1 { font-size: 1.1em; } .chat-md h2 { font-size: 1.05em; } .chat-md h3 { font-size: 1em; }
.chat-md ul,.chat-md ol { padding-left: 1.3em; margin: 0.35em 0; }
.chat-md li { margin: 0.2em 0; }
.chat-md strong { font-weight: 700; color: #0C2E72; }
.chat-md em     { font-style: italic; color: #1B57C4; }
.chat-md code   { background: #EBF2FF; color: #0C2E72; padding: 0.1em 0.35em; border-radius: 5px; font-size: 0.85em; font-family: monospace; }
.chat-md pre    { background: #0C2E72; color: #BFCFEE; padding: 0.85em 1em; border-radius: 10px; overflow-x: auto; margin: 0.5em 0; font-size: 0.82em; }
.chat-md pre code { background: none; color: inherit; padding: 0; }
.chat-md blockquote { border-left: 3px solid #2D6CDF; padding-left: 0.75em; color: #737373; margin: 0.4em 0; font-style: italic; }
.chat-md a { color: #2D6CDF; text-decoration: underline; }
.chat-md table { width:100%; border-collapse:collapse; margin:0.5em 0; font-size:0.85em; }
.chat-md th,.chat-md td { border:1px solid #BFCFEE; padding:0.3em 0.6em; }
.chat-md th { background:#EBF2FF; font-weight:700; color:#0C2E72; }

/* Follow-up chips */
.followup-chip:hover { background: #2D6CDF !important; color: #fff !important; border-color: #2D6CDF !important; }

/* Copy button */
.copy-btn { opacity:0; transition: opacity 0.2s; }
.bot-bubble-wrap:hover .copy-btn { opacity:1; }

/* Typing dots */
@keyframes chatDotBounce {
  0%,80%,100% { transform: translateY(0); }
  40%         { transform: translateY(-6px); }
}
.dot-bounce { animation: chatDotBounce 1.2s infinite; }

/* Card layout */
#chatbot-card {
    width: 100%;
    max-width: 820px;
    height: calc(100vh - 110px);
    margin: 20px auto 0;
    border-radius: 24px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: none;
}

/* Inline end-session panel */
#end-session-panel { display:none; }
#end-session-panel.open { display:block; }
</style>
@endpush

@section('content')
<div class="flex items-start justify-center" style="height:calc(100vh - 80px);background:#EBF2FF;overflow:hidden;">

<div id="chatbot-card">

    {{-- ── Card Header (purple gradient) ── --}}
    <div class="flex-shrink-0 flex items-center gap-3 px-5 py-4" style="background:#2D6CDF;">
        <a href="{{ route('negotiation.dashboard') }}"
            class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full transition-all"
            style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.25);"
            onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-base flex-shrink-0" style="background:rgba(255,255,255,0.22);border:2px solid rgba(255,255,255,0.4);color:#fff;">₹</div>
        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-sm leading-tight text-white">AI Salary Negotiation Coach</h1>
            <p class="text-xs" style="color:rgba(255,255,255,0.75);">Powered by StudAI</p>
        </div>
        <span class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full" style="background:rgba(255,255,255,0.18);color:#fff;">
            <span class="w-2 h-2 rounded-full" style="background:#1E8E3E;box-shadow: none;"></span>
            Active
        </span>
    </div>

    {{-- ── Messages ── --}}
    <div id="messages" class="flex-1 overflow-y-auto px-5 py-5" style="background:#EBF2FF;display:flex;flex-direction:column;gap:1rem;">

        {{-- Welcome message --}}
        <div class="flex gap-3 items-start">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0" style="background:#2D6CDF;color:#fff;">₹</div>
            <div style="max-width:34rem;">
                <div class="rounded-2xl rounded-tl-sm px-4 py-3 text-sm shadow-sm" style="background:#fff;color:#0C0C0C;">
                    <p class="font-semibold mb-1" style="color:#0C2E72;">Hi! I'm your AI Salary Negotiation Coach 👋</p>
                    <p style="color:#3D3D3D;">I'll help you negotiate a better salary, evaluate offers, and give you exact scripts to use with employers. What would you like to work on?</p>
                </div>
                <p class="text-xs mt-1" style="color:#A8A8A8;">{{ now()->format('g:i A') }}</p>
            </div>
        </div>

        {{-- Starter prompts --}}
        <div id="starter-prompts" class="flex flex-wrap gap-2" style="padding-left:3rem;">
            @foreach([
                'How do I ask for a higher salary?',
                'Help me write a salary negotiation email',
                'What\'s a fair salary for a Software Engineer in Bangalore?',
                'How to handle "This is our best offer" pushback?',
                'What benefits should I negotiate besides salary?',
                'How do I counter a lowball offer?'
            ] as $prompt)
            <button class="followup-chip text-xs px-3 py-2 rounded-full border transition-all cursor-pointer"
                style="background:#EBF2FF;border-color:#BFCFEE;color:#1B57C4;"
                onclick="sendStarter(this)" data-prompt="{{ $prompt }}">{{ $prompt }}</button>
            @endforeach
        </div>

    </div>

    {{-- ── Input Row ── --}}
    <div class="flex-shrink-0 px-4 py-3" style="background:#fff;border-top:1px solid #EBF2FF;">
        <div class="flex items-center gap-2">
            {{-- Text input pill --}}
            <div id="input-box" class="flex-1 flex items-center gap-2 px-4 py-2.5 rounded-full transition-all"
                style="background:#EBF2FF;border:1.5px solid #BFCFEE;"
                onfocusin="this.style.borderColor='#2D6CDF';this.style.boxShadow='0 0 0 3px rgba(20, 71, 186,0.10)'"
                onfocusout="this.style.borderColor='#BFCFEE';this.style.boxShadow='none'">
                <textarea id="user-input" rows="1"
                    placeholder="Ask anything… markdown supported (*bold*, \`code\`, - lists)"
                    class="flex-1 resize-none bg-transparent text-sm outline-none leading-relaxed"
                    style="max-height:100px;color:#0C0C0C;padding:0;border:none;"
                    onkeydown="handleKey(event)"
                    oninput="autoResize(this)"></textarea>
                {{-- Mic icon (decorative) --}}
                <svg class="w-4 h-4 flex-shrink-0" style="color:#2D6CDF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4M12 3a4 4 0 014 4v4a4 4 0 01-8 0V7a4 4 0 014-4z"/>
                </svg>
            </div>
            {{-- Send button --}}
            <button id="send-btn" onclick="sendMessage()"
                class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-semibold text-white transition-all hover:scale-105 flex-shrink-0"
                style="background:#2D6CDF;box-shadow: none;">
                Send
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </div>

        {{-- Footer: message count + End Session --}}
        <div class="flex items-center justify-between mt-2 px-1">
            <p id="msg-count" class="text-xs" style="color:#A8A8A8;">0 messages</p>
            <button onclick="toggleEndPanel()" class="flex items-center gap-1.5 text-xs font-medium px-3 py-1 rounded-full border transition-all" style="border-color:#fca5a5;color:#2D6CDF;background:#FEF2F2;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='#FEF2F2'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                End Session
            </button>
        </div>

        {{-- Inline end-session confirmation --}}
        <div id="end-session-panel" class="mt-2 rounded-2xl border px-4 py-3" style="border-color:#FCA5A5;background:#FEF2F2;">
            <p class="text-sm font-semibold mb-2" style="color:#2D6CDF;">End this session?</p>
            <div class="flex gap-2">
                <a href="{{ route('negotiation.dashboard') }}"
                    class="flex-1 py-2 rounded-xl text-sm font-semibold text-white text-center transition-all"
                    style="background:#2D6CDF;"
                    onmouseover="this.style.background='#2D6CDF'" onmouseout="this.style.background='#2D6CDF'"
                    onclick="localStorage.removeItem('studai_negochat_v1')">
                    Yes, end it
                </a>
                <button onclick="toggleEndPanel()" class="flex-1 py-2 rounded-xl text-sm font-semibold transition-all" style="background:#F0F0EE;color:#3D3D3D;" onmouseover="this.style.background='#E2E2E0'" onmouseout="this.style.background='#F0F0EE'">Cancel</button>
            </div>
        </div>
    </div>

</div>{{-- /chatbot-card --}}
</div>{{-- /outer wrapper --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
const CHAT_URL    = '{{ route("negotiation.chat") }}';
const CSRF        = '{{ csrf_token() }}';
const STORAGE_KEY = 'studai_negochat_v1';
let history    = [];
let messages   = []; // structured store: {type:'user'|'bot', text, followUps}
let thinking   = false;

marked.use({ breaks: true, gfm: true });

/* ── Persistence ───────────────────────────────── */
function saveSession() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ history, messages }));
    } catch(e) {}
}

function loadSession() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return false;
        const data = JSON.parse(raw);
        if (!data.messages || !data.messages.length) return false;

        history  = data.history  || [];
        messages = data.messages || [];

        // Hide starter prompts since we have a conversation
        const starters = document.getElementById('starter-prompts');
        if (starters) starters.style.display = 'none';

        // Re-render all stored messages (without saving again)
        messages.forEach(m => {
            if (m.type === 'user') {
                _renderUserMessage(m.text);
            } else {
                _renderBotMessage(m.text, m.followUps || [], m.showCopy !== false);
            }
        });
        scrollBottom();
        return true;
    } catch(e) { return false; }
}

/* ── End session panel ─────────────────────────── */
function toggleEndPanel() {
    const panel = document.getElementById('end-session-panel');
    panel.classList.toggle('open');
}

/* ── Update message counter ────────────────────── */
function updateMsgCount() {
    const count = messages.filter(m => m.type === 'user').length;
    const el = document.getElementById('msg-count');
    if (el) el.textContent = count + (count === 1 ? ' message' : ' messages');
}

document.addEventListener('DOMContentLoaded', function() {
    // Restore chat from localStorage
    const restored = loadSession();
    if (restored) {
        const welcome = document.querySelector('#messages .flex.gap-3.items-start');
        if (welcome) welcome.style.display = 'none';
        const starters = document.getElementById('starter-prompts');
        if (starters) starters.style.display = 'none';
        updateMsgCount();
    }
});

/* ── Utilities ─────────────────────────────────── */
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}
function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}
function scrollBottom() {
    const msgs = document.getElementById('messages');
    msgs.scrollTop = msgs.scrollHeight;
}
function escapeHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function botAvatar() {
    return `<div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 text-sm" style="background:#2D6CDF;">₹</div>`;
}

/* ── Starters / Follow-ups ─────────────────────── */
function sendStarter(btn) {
    const starters = document.getElementById('starter-prompts');
    if (starters) starters.style.display = 'none';
    document.getElementById('user-input').value = btn.dataset.prompt;
    sendMessage();
}

function showFollowUps(questions) {
    if (!questions || !questions.length) return;
    const msgs = document.getElementById('messages');

    // Header label
    const label = document.createElement('div');
    label.className = 'followup-row text-xs font-semibold';
    label.style.cssText = 'padding-left:3rem;color:#2D6CDF;margin-bottom:4px;';
    label.textContent = '💡 Suggested follow-ups:';
    msgs.appendChild(label);

    const row = document.createElement('div');
    row.className = 'followup-row flex flex-wrap gap-2';
    row.style.paddingLeft = '3rem';
    questions.forEach(q => {
        const btn = document.createElement('button');
        btn.className = 'followup-chip';
        btn.style.cssText = 'background:#fff;border:1.5px solid #BFCFEE;color:#0C2E72;font-size:0.78rem;padding:0.35rem 0.9rem;border-radius:999px;cursor:pointer;transition:all .18s;font-weight:500;display:inline-flex;align-items:center;gap:0.35rem;box-shadow: none;';
        btn.innerHTML = `<span style="font-size:.85rem;color:#2D6CDF;line-height:1;">↩</span>${q}`;
        btn.onmouseover = () => { btn.style.background='#2D6CDF'; btn.style.borderColor='#2D6CDF'; btn.style.color='#fff'; btn.querySelector('span').style.color='#fff'; };
        btn.onmouseout  = () => { btn.style.background='#fff'; btn.style.borderColor='#BFCFEE'; btn.style.color='#0C2E72'; btn.querySelector('span').style.color='#2D6CDF'; };
        btn.onclick = () => {
            document.querySelectorAll('.followup-row').forEach(r => r.remove());
            document.getElementById('user-input').value = q;
            sendMessage();
        };
        row.appendChild(btn);
    });
    msgs.appendChild(row);
    scrollBottom();
}

/* ── Clear ─────────────────────────────────────── */
function clearChat() {
    history  = [];
    messages = [];
    localStorage.removeItem(STORAGE_KEY);
    const msgs = document.getElementById('messages');
    msgs.innerHTML = '';
    updateMsgCount();
    appendBotMessage('Chat cleared! 👋 What would you like to work on?', [
        'How do I ask for a higher salary?',
        'Help me write a salary negotiation email',
        'How to handle a lowball offer?',
        'What benefits can I negotiate?',
    ], false);
}

/* ── Send ──────────────────────────────────────── */
function sendMessage() {
    if (thinking) return;
    const input = document.getElementById('user-input');
    const text  = input.value.trim();
    if (!text) return;

    // Hide starters
    const starters = document.getElementById('starter-prompts');
    if (starters) starters.style.display = 'none';
    // Remove old follow-up rows
    document.querySelectorAll('.followup-row').forEach(r => r.remove());

    appendUserMessage(text);
    input.value = '';
    input.style.height = 'auto';
    history.push({ role: 'user', content: text });

    showTyping();
    thinking = true;
    document.getElementById('send-btn').style.opacity = '0.5';

    fetch(CHAT_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ message: text, history: history.slice(0, -1) })
    })
    .then(r => {
        if (!r.ok && r.status !== 422) {
            return r.text().then(body => {
                // Try to parse as JSON anyway, otherwise throw with status
                try { return JSON.parse(body); } catch(_) { throw new Error('HTTP ' + r.status); }
            });
        }
        return r.json();
    })
    .then(data => {
        hideTyping();
        thinking = false;
        document.getElementById('send-btn').style.opacity = '1';
        const reply    = data.reply    || data.message || 'Sorry, something went wrong.';
        const followUps = data.followUps || [];
        history.push({ role: 'assistant', content: reply });
        appendBotMessage(reply, followUps, true);
    })
    .catch(err => {
        hideTyping();
        thinking = false;
        document.getElementById('send-btn').style.opacity = '1';
        const msg = (err && err.message && err.message.includes('HTTP 419'))
            ? 'Session expired. Please refresh the page and try again.'
            : 'Connection error. Please try again.';
        appendBotMessage(msg, [], false);
    });
}

/* ── Message renderers ─────────────────────────── */
function appendUserMessage(text) {
    messages.push({ type: 'user', text });
    saveSession();
    _renderUserMessage(text);
    updateMsgCount();
}

function _renderUserMessage(text) {
    const msgs = document.getElementById('messages');
    const div  = document.createElement('div');
    div.className = 'flex gap-3 items-start justify-end';
    div.innerHTML = `
        <div style="max-width:38rem;">
            <div class="rounded-2xl rounded-tr-sm px-4 py-3 text-sm text-white shadow-sm" style="background:#2D6CDF;">
                ${escapeHtml(text)}
            </div>
        </div>
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 text-xs" style="background:#737373;">You</div>`;
    msgs.appendChild(div);
    scrollBottom();
}

function appendBotMessage(text, followUps, showCopy) {
    messages.push({ type: 'bot', text, followUps: followUps || [], showCopy: !!showCopy });
    saveSession();
    _renderBotMessage(text, followUps || [], showCopy);
    updateMsgCount();
}

function _renderBotMessage(text, followUps, showCopy) {
    const msgs = document.getElementById('messages');
    const div  = document.createElement('div');
    div.className = 'flex gap-3 items-start bot-bubble-wrap';

    const safeText = text.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    const copyBtnHtml = showCopy ? `
        <button class="copy-btn flex items-center gap-1 text-xs mt-2 transition-colors"
            style="color:#A8A8A8;"
            onmouseover="this.style.color='#2D6CDF'" onmouseout="this.style.color='#A8A8A8'"
            onclick="copyText(this)" data-raw="${safeText}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Copy response
        </button>` : '';

    div.innerHTML = `
        ${botAvatar()}
        <div style="max-width:42rem;min-width:0;">
            <div class="rounded-2xl rounded-tl-sm px-4 py-3 text-sm shadow-sm chat-md" style="background:#fff;color:#0C0C0C;">
                ${marked.parse(text)}
            </div>
            ${copyBtnHtml}
        </div>`;
    msgs.appendChild(div);

    if (followUps && followUps.length) showFollowUps(followUps);
    scrollBottom();
}

/* ── Copy to clipboard ─────────────────────────── */
function copyText(btn) {
    const raw = btn.getAttribute('data-raw')
        .replace(/&quot;/g,'"').replace(/&#39;/g,"'").replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
    navigator.clipboard.writeText(raw).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!`;
        btn.style.color = '#1E8E3E';
        setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
    });
}

/* ── Typing indicator ──────────────────────────── */
function showTyping() {
    const msgs = document.getElementById('messages');
    const div  = document.createElement('div');
    div.id     = 'typing-bubble';
    div.className = 'flex gap-3 items-start';
    div.innerHTML = `
        ${botAvatar()}
        <div class="rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm flex items-center gap-1.5" style="background:#fff;">
            <span class="w-2 h-2 rounded-full dot-bounce" style="background:#2D6CDF;animation-delay:0ms;"></span>
            <span class="w-2 h-2 rounded-full dot-bounce" style="background:#2D6CDF;animation-delay:150ms;"></span>
            <span class="w-2 h-2 rounded-full dot-bounce" style="background:#2D6CDF;animation-delay:300ms;"></span>
        </div>`;
    msgs.appendChild(div);
    scrollBottom();
}
function hideTyping() {
    const t = document.getElementById('typing-bubble');
    if (t) t.remove();
}
</script>
@endpush
@endsection
