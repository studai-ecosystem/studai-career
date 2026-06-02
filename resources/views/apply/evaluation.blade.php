<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Evaluation — {{ $job->title }} | StudAI Hire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#2D6CDF', 'primary-dark': '#1B57C4' } } }
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .option-btn:hover { transform: translateY(-1px); }
        .option-btn.selected { background: #EBF2FF; border-color: #2D6CDF; color: #2D6CDF; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Header --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-primary font-bold text-lg">StudAI Hire</span>
            <span class="ml-1 px-2 py-0.5 bg-blue-50 text-primary text-xs rounded-full">Orin™ Evaluation</span>
        </div>
        <div class="flex items-center gap-4">
            {{-- Timer --}}
            <div class="flex items-center gap-1.5 bg-orange-50 text-orange-700 px-3 py-1.5 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="question-timer">--:--</span>
            </div>
            {{-- Progress --}}
            <span class="text-sm text-gray-500">
                <span id="current-q-num">1</span> / <span id="total-q-num">{{ $session->total_questions }}</span>
            </span>
        </div>
    </div>
    {{-- Progress Bar --}}
    <div class="h-1 bg-gray-200">
        <div id="progress-bar" class="h-full bg-primary transition-all duration-500" style="width: 0%"></div>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-8" id="eval-main">

    {{-- Tab switch warning --}}
    <div id="tab-warning" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
        <span class="text-xl">⚠️</span>
        <div>
            <p class="font-semibold text-red-800">Tab switch detected!</p>
            <p class="text-sm text-red-700">Leaving the evaluation window is noted. Repeated violations may affect your score.</p>
        </div>
    </div>

    {{-- Loading state --}}
    <div id="loading-state" class="text-center py-20">
        <div class="inline-block w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-500">Loading your question...</p>
    </div>

    {{-- Question Card --}}
    <div id="question-card" class="hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
            {{-- Question meta --}}
            <div class="flex items-center gap-2 mb-4">
                <span id="q-difficulty" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600"></span>
                <span id="q-type" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-50 text-primary"></span>
                <span id="q-topic" class="text-xs text-gray-400 ml-auto"></span>
            </div>

            {{-- Question text --}}
            <p class="text-gray-900 text-base font-medium leading-relaxed" id="question-text"></p>
        </div>

        {{-- Answer area --}}
        <div id="answer-area" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            {{-- MCQ Options --}}
            <div id="mcq-options" class="hidden space-y-3">
                {{-- filled by JS --}}
            </div>

            {{-- Text answer --}}
            <div id="text-answer" class="hidden">
                <textarea id="text-input" rows="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none"
                    placeholder="Type your answer here..."></textarea>
                <p class="text-xs text-gray-400 mt-2">Write a clear, structured response. Orin™ evaluates depth and accuracy.</p>
            </div>

            {{-- Code answer --}}
            <div id="code-answer" class="hidden">
                <textarea id="code-input" rows="10"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary focus:border-transparent outline-none resize-none bg-gray-900 text-green-400"
                    placeholder="Write your code here..."></textarea>
                <p class="text-xs text-gray-400 mt-2">Any language is acceptable unless specified in the question.</p>
            </div>

            {{-- Submit button --}}
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-gray-400">All answers are final once submitted.</p>
                <div class="flex items-center gap-3">
                    <button id="finish-eval-btn"
                        class="px-5 py-2.5 border border-gray-300 hover:border-primary text-gray-600 hover:text-primary font-semibold rounded-xl transition-colors">
                        Finish &amp; Submit
                    </button>
                    <button id="submit-answer-btn"
                        class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <span id="submit-btn-text">Next Question →</span>
                        <svg id="submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Completion state --}}
    <div id="complete-state" class="hidden text-center py-16">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Evaluation Complete!</h2>
        <p class="text-gray-500 mt-2">Orin™ is analysing your responses. You'll be notified of results by email.</p>
        <a href="{{ route('apply.show', $token) }}"
            class="inline-block mt-6 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-dark transition-colors">
            Back to Job Listing
        </a>
    </div>

</main>

<script>
const SESSION_TOKEN = '{{ $session->session_token }}';
const TOKEN = '{{ $token }}';
const CSRF = '{{ csrf_token() }}';
const TOTAL_Q = {{ $session->total_questions }};

let currentQuestionId = null;
let selectedOption = null;
let questionStartTime = null;
let timerInterval = null;
let timeLimit = 120;
let tabSwitchCount = 0;

async function loadQuestion() {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('question-card').classList.add('hidden');

    try {
        const resp = await fetch('{{ route("apply.evaluation.question", $token) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ session_token: SESSION_TOKEN }),
        });
        const data = await resp.json();

        if (data.complete) {
            showComplete();
            return;
        }

        renderQuestion(data);
    } catch (e) {
        console.error('Failed to load question', e);
    }
}

function renderQuestion(data) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('question-card').classList.remove('hidden');

    currentQuestionId = data.id;
    selectedOption = null;
    questionStartTime = Date.now();
    timeLimit = data.time_limit_seconds || 120;

    // Meta
    const difficultyColors = { foundational: 'bg-green-100 text-green-700', intermediate: 'bg-yellow-100 text-yellow-700', advanced: 'bg-red-100 text-red-700' };
    const qDiff = document.getElementById('q-difficulty');
    qDiff.textContent = data.difficulty?.charAt(0).toUpperCase() + data.difficulty?.slice(1);
    qDiff.className = 'px-2.5 py-0.5 text-xs font-medium rounded-full ' + (difficultyColors[data.difficulty] || 'bg-gray-100 text-gray-600');

    document.getElementById('q-type').textContent = (data.question_type || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    document.getElementById('q-topic').textContent = data.topic || '';
    document.getElementById('question-text').textContent = data.question_text;

    // Progress
    const qIdx = data.current_index ?? 0;
    document.getElementById('current-q-num').textContent = qIdx + 1;
    document.getElementById('progress-bar').style.width = ((qIdx / TOTAL_Q) * 100) + '%';

    // Answer areas
    document.getElementById('mcq-options').classList.add('hidden');
    document.getElementById('text-answer').classList.add('hidden');
    document.getElementById('code-answer').classList.add('hidden');

    if (data.question_type === 'mcq' && data.options) {
        const opts = typeof data.options === 'string' ? JSON.parse(data.options) : data.options;
        const container = document.getElementById('mcq-options');
        container.innerHTML = '';
        opts.forEach((opt, i) => {
            const btn = document.createElement('button');
            btn.className = 'option-btn w-full text-left px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-700 hover:border-primary transition-all';
            btn.textContent = (String.fromCharCode(65 + i)) + '. ' + opt;
            btn.dataset.value = opt;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                selectedOption = opt;
            });
            container.appendChild(btn);
        });
        container.classList.remove('hidden');
    } else if (data.question_type === 'code_snippet') {
        document.getElementById('code-input').value = '';
        document.getElementById('code-answer').classList.remove('hidden');
    } else {
        document.getElementById('text-input').value = '';
        document.getElementById('text-answer').classList.remove('hidden');
    }

    // Timer
    clearInterval(timerInterval);
    let remaining = timeLimit;
    updateTimer(remaining);
    timerInterval = setInterval(() => {
        remaining--;
        updateTimer(remaining);
        if (remaining <= 0) {
            clearInterval(timerInterval);
            submitAnswer();
        }
    }, 1000);
}

function updateTimer(secs) {
    const m = Math.floor(secs / 60).toString().padStart(2, '0');
    const s = (secs % 60).toString().padStart(2, '0');
    document.getElementById('question-timer').textContent = m + ':' + s;
    const el = document.getElementById('question-timer').parentElement;
    if (secs < 30) {
        el.className = el.className.replace('bg-orange-50 text-orange-700', 'bg-red-50 text-red-700');
    }
}

async function submitAnswer() {
    const btn = document.getElementById('submit-answer-btn');
    const spinner = document.getElementById('submit-spinner');
    btn.disabled = true;
    spinner.classList.remove('hidden');

    const questionType = document.getElementById('q-type').textContent.toLowerCase().replace(/ /g, '_');
    let answer = selectedOption;
    if (!answer) {
        answer = document.getElementById('code-input').value || document.getElementById('text-input').value;
    }

    const timeTaken = Math.floor((Date.now() - questionStartTime) / 1000);
    clearInterval(timerInterval);

    try {
        const resp = await fetch('{{ route("apply.evaluation.answer", $token) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                session_token: SESSION_TOKEN,
                question_id: currentQuestionId,
                answer: answer,
                time_taken_seconds: timeTaken,
            }),
        });
        const data = await resp.json();

        if (data.complete) {
            showComplete();
        } else {
            loadQuestion();
        }
    } catch (e) {
        console.error('Submit failed', e);
        loadQuestion();
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
    }
}

function showComplete() {
    clearInterval(timerInterval);
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('question-card').classList.add('hidden');
    document.getElementById('complete-state').classList.remove('hidden');
    document.getElementById('progress-bar').style.width = '100%';
}

async function finishEvaluation() {
    const btn = document.getElementById('finish-eval-btn');
    btn.disabled = true;

    try {
        const resp = await fetch('{{ route("apply.evaluation.complete", $token) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ session_token: SESSION_TOKEN }),
        });
        const data = await resp.json();

        if (data.is_complete) {
            showComplete();
            return;
        }

        if (data.error === 'incomplete') {
            alert(data.message);
        } else if (data.error) {
            alert(data.error);
        }
    } catch (e) {
        console.error('Finish failed', e);
    } finally {
        btn.disabled = false;
    }
}

document.getElementById('submit-answer-btn').addEventListener('click', submitAnswer);
document.getElementById('finish-eval-btn').addEventListener('click', finishEvaluation);


// Anti-cheat: tab visibility detection
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        tabSwitchCount++;
        document.getElementById('tab-warning').classList.remove('hidden');

        fetch('{{ route("apply.evaluation.anticheat", $token) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ session_token: SESSION_TOKEN, event_type: 'tab_switch' }),
        }).catch(() => {});
    }
});

// Load first question
loadQuestion();
</script>

</body>
</html>
