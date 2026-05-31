<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Onboarding — StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#2D6CDF', 'primary-dark': '#1B57C4' } } } }</script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .chat-bubble-orin { background: #EBF2FF; border: 1px solid #EBF2FF; }
        .chat-bubble-user { background: #2D6CDF; color: white; }
        #chat-messages { scrollbar-width: thin; }
        .typing-dot { animation: blink 1.4s infinite both; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes blink { 0%,80%,100%{opacity:0} 40%{opacity:1} }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100 mb-4">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-sm font-medium text-gray-600">Orin™ AI is online</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Company Intelligence Setup</h1>
        <p class="text-gray-500 text-sm mt-1">Orin™ will guide you through a short conversation to build your company profile.</p>

        @if($profile && $profile->onboarding_complete)
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-3 flex items-center justify-between">
                <span class="text-sm text-green-800 font-medium">Profile {{ $profile->completeness_score }}% complete</span>
                <a href="{{ route('employer.home') }}" class="text-sm text-primary font-medium hover:underline">Go to Dashboard →</a>
            </div>
        @endif
    </div>

    {{-- Chat Window --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div id="chat-messages" class="p-5 space-y-4 h-[480px] overflow-y-auto">
            {{-- Messages inserted by JS --}}
        </div>

        {{-- Typing indicator --}}
        <div id="typing-indicator" class="hidden px-5 pb-2">
            <div class="flex items-center gap-1.5">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">O</div>
                <div class="chat-bubble-orin rounded-2xl px-4 py-2.5 inline-flex gap-1 items-center">
                    <span class="typing-dot w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                    <span class="typing-dot w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                    <span class="typing-dot w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-100 p-4 flex gap-3">
            <input id="chat-input" type="text"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Type your reply..." autocomplete="off">
            <button id="send-btn"
                class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl text-sm transition-colors flex items-center gap-2">
                Send
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </div>
    </div>

    {{-- Skip link --}}
    <div class="text-center mt-4">
        <button id="skip-btn" class="text-sm text-gray-400 hover:text-gray-600 underline">
            Skip for now (you can complete this later)
        </button>
    </div>

</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let conversationHistory = [];
let complete = false;

function addMessage(role, text) {
    const container = document.getElementById('chat-messages');
    const isOrin = role === 'assistant';

    const wrap = document.createElement('div');
    wrap.className = 'flex items-start gap-3 ' + (isOrin ? '' : 'flex-row-reverse');

    if (isOrin) {
        const avatar = document.createElement('div');
        avatar.className = 'w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0';
        avatar.textContent = 'O';
        wrap.appendChild(avatar);
    }

    const bubble = document.createElement('div');
    bubble.className = 'max-w-[85%] px-4 py-3 rounded-2xl text-sm leading-relaxed ' +
        (isOrin ? 'chat-bubble-orin text-gray-800' : 'chat-bubble-user');
    bubble.textContent = text;
    wrap.appendChild(bubble);

    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
}

async function sendMessage(text) {
    if (complete || !text.trim()) return;

    addMessage('user', text);
    conversationHistory.push({ role: 'user', content: text });

    document.getElementById('chat-input').value = '';
    document.getElementById('send-btn').disabled = true;
    document.getElementById('typing-indicator').classList.remove('hidden');

    try {
        const resp = await fetch('{{ route("employer.orin-onboarding.chat") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ history: conversationHistory }),
        });
        const data = await resp.json();

        document.getElementById('typing-indicator').classList.add('hidden');
        addMessage('assistant', data.message);
        conversationHistory.push({ role: 'assistant', content: data.message });

        if (data.complete) {
            complete = true;
            document.getElementById('chat-input').disabled = true;
            document.getElementById('send-btn').disabled = true;
            // Redirect after 3 seconds
            setTimeout(() => { window.location.href = '{{ route("employer.home") }}'; }, 3000);
        }
    } catch (e) {
        document.getElementById('typing-indicator').classList.add('hidden');
        addMessage('assistant', "I'm having a moment — please try again.");
    } finally {
        document.getElementById('send-btn').disabled = complete;
    }
}

document.getElementById('send-btn').addEventListener('click', () => {
    sendMessage(document.getElementById('chat-input').value.trim());
});

document.getElementById('chat-input').addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(document.getElementById('chat-input').value.trim());
    }
});

document.getElementById('skip-btn').addEventListener('click', async () => {
    if (!confirm('Skip onboarding? Orin™ will have less context for generating job postings.')) return;
    await fetch('{{ route("employer.orin-onboarding.skip") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({}),
    });
    window.location.href = '{{ route("employer.home") }}';
});

// Start the conversation immediately
window.addEventListener('load', async () => {
    document.getElementById('typing-indicator').classList.remove('hidden');
    await sendMessage('Hello, I just set up my account.');
    // Remove the "Hello" message that sendMessage added — Orin starts
});

// Actually: just call the API directly with empty history to get the opening message
window.addEventListener('DOMContentLoaded', async () => {
    document.getElementById('typing-indicator').classList.remove('hidden');
    try {
        const resp = await fetch('{{ route("employer.orin-onboarding.chat") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ history: [] }),
        });
        const data = await resp.json();
        document.getElementById('typing-indicator').classList.add('hidden');
        addMessage('assistant', data.message);
        conversationHistory.push({ role: 'assistant', content: data.message });
    } catch {
        document.getElementById('typing-indicator').classList.add('hidden');
        addMessage('assistant', "Hi! I'm Orin™. Let's set up your company profile. What industry is your company in, and how many people are on your team?");
        conversationHistory.push({ role: 'assistant', content: "Hi! I'm Orin™. Let's set up your company profile. What industry is your company in, and how many people are on your team?" });
    }
});
</script>

</body>
</html>
