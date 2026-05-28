@php
    $questionIndex = 0;
    $flattenedQuestions = [];
    $typeLabelMap = [
        'behavioral'  => 'Behavioral',
        'technical'   => 'Technical',
        'situational' => 'Situational',
    ];

    $rawQuestions = $session['questions'] ?? [];

    // Handle if AI returned a flat array instead of nested {behavioral:[],technical:[],situational:[]}
    if (!empty($rawQuestions) && isset($rawQuestions[0]) && is_array($rawQuestions[0])) {
        $grouped = ['behavioral' => [], 'technical' => [], 'situational' => []];
        foreach ($rawQuestions as $q) {
            $t = $q['type'] ?? 'behavioral';
            $grouped[$t][] = $q;
        }
        $rawQuestions = $grouped;
    }

    foreach ($rawQuestions as $type => $items) {
        if (!is_array($items)) {
            continue;
        }
        foreach ($items as $item) {
            $flattenedQuestions[] = array_merge($item, [
                'type'      => $type,
                'typeLabel' => $typeLabelMap[$type] ?? ucfirst((string) $type),
                'index'     => $questionIndex,
            ]);
            $questionIndex++;
        }
    }
    $savedAnswers = $session['answers'] ?? [];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Interview Practice Session') }}
                </h2>
                <p class="text-sm text-gray-500">Role: {{ $session['job_title'] }} &middot; Level: {{ ucfirst($session['experience_level']) }}</p>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                    {{ count($flattenedQuestions) }} Questions
                </span>
                @if(!empty($session['company']))
                    <span class="hidden md:inline">|</span>
                    <span>Company focus: {{ $session['company'] }}</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Safe JSON data transfer using application/json script elements.
                 This is immune to all HTML/JS encoding edge-cases. --}}
            <script type="application/json" id="iv-questions">@json($flattenedQuestions)</script>
            <script type="application/json" id="iv-answers">@json($savedAnswers)</script>
            @php
                $ivMeta = [
                    'sessionId'   => $sessionId,
                    'submitUrl'   => route('interview.submit-answer', $sessionId),
                    'followUpUrl' => route('interview.follow-up', $sessionId),
                    'completeUrl' => route('interview.complete', $sessionId),
                ];
            @endphp
            <script type="application/json" id="iv-meta">@json($ivMeta)</script>

            <script>
                (function () {
                    function parseJsonEl(id) {
                        try { return JSON.parse(document.getElementById(id).textContent); }
                        catch (e) { console.error('IV data parse error [' + id + ']:', e); return null; }
                    }
                    var qs   = parseJsonEl('iv-questions') || [];
                    var ans  = parseJsonEl('iv-answers')   || {};
                    var meta = parseJsonEl('iv-meta')      || {};
                    window.__interviewConfig = {
                        questions:    qs,
                        answers:      ans,
                        sessionId:    meta.sessionId    || '',
                        submitUrl:    meta.submitUrl    || '',
                        followUpUrl:  meta.followUpUrl  || '',
                        completeUrl:  meta.completeUrl  || '',
                    };
                })();
            </script>

            {{-- Alpine component definition (always before the x-data div) --}}
            <script>
                window.interviewSession = function (config) {
                        var cfg = config || {};
                        return {
                            questions: cfg.questions || [],
                            answers: cfg.answers || {},
                            sessionId: cfg.sessionId || '',
                            submitUrl: cfg.submitUrl || '',
                            followUpUrl: cfg.followUpUrl || '',
                            completeUrl: cfg.completeUrl || '',
                            currentIndex: 0,
                            answerText: '',
                            saving: false,
                            showFeedback: true,
                            followUps: [],
                            loadingFollowUps: false,
                            questionSeconds: 0,
                            totalSeconds: 0,
                            timerInterval: null,
                            questionInterval: null,
                            tabSwitchCount: 0,
                            fsViolations: 0,
                            antiCheatVisible: false,
                            antiCheatMessage: '',
                            cameraActive: false,
                            micActive: false,
                            mediaStream: null,
                            cameraError: null,
                            sessionStarted: false,
                            finishing: false,
                            requestingCamera: false,

                            init() {
                                // Normalize question field names (AI sometimes returns 'text' instead of 'question')
                                this.questions = this.questions.map(function (q, i) {
                                    return Object.assign({}, q, {
                                        index: i,
                                        question: q.question || q.text || q.question_text || q.content || q.q || '(Question not available)',
                                        typeLabel: q.typeLabel || (q.type ? q.type.charAt(0).toUpperCase() + q.type.slice(1) : 'General'),
                                    });
                                });

                                if (this.questions.length === 0) {
                                    console.warn('InterviewSession: No questions loaded.');
                                    return;
                                }

                                console.log('InterviewSession: Loaded', this.questions.length, 'questions. First:', this.questions[0]);
                                this.loadAnswer();
                                this.startTimers();

                                // ── Proctoring ──────────────────────────────────────────
                                var acSelf = this;

                                // NOTE: Fullscreen + camera are triggered in startSession() via user gesture button.
                                // Browsers block requestFullscreen() and getUserMedia() without a user gesture.

                                // 1. Detect fullscreen exit (only after session started, not during Finish navigation)
                                document.addEventListener('fullscreenchange', function () {
                                    if (!acSelf.sessionStarted || acSelf.finishing) return;
                                    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                                        acSelf.fsViolations = (acSelf.fsViolations || 0) + 1;
                                        if (acSelf.fsViolations >= 3) {
                                            acSelf.antiCheatMessage = 'You exited fullscreen 3 times. Your assessment is being submitted automatically.';
                                            acSelf.antiCheatVisible = true;
                                            setTimeout(function () { acSelf.finishSession(); }, 2500);
                                        } else {
                                            var left = 3 - acSelf.fsViolations;
                                            acSelf.antiCheatMessage = 'Fullscreen is required. Please return to fullscreen. ' + left + ' more violation' + (left === 1 ? '' : 's') + ' will auto-submit. Click "I understand" to restore fullscreen.';
                                            acSelf.antiCheatVisible = true;
                                        }
                                    }
                                });
                                document.addEventListener('webkitfullscreenchange', function () {
                                    document.dispatchEvent(new Event('fullscreenchange'));
                                });

                                // 2. Restore fullscreen when user dismisses warning
                                acSelf._restoreFS = function () {
                                    var el = document.documentElement;
                                    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                                        if (el.requestFullscreen) el.requestFullscreen();
                                        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                                    }
                                };

                                // 3. Detect tab / window switching (only after session started)
                                document.addEventListener('visibilitychange', function () {
                                    if (!acSelf.sessionStarted || acSelf.finishing) return;
                                    if (document.hidden) { acSelf.handleTabSwitch(); }
                                });

                                // 5. Block keyboard shortcuts: Ctrl+C, Ctrl+A, PrintScreen
                                document.addEventListener('keydown', function (e) {
                                    if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C' || e.key === 'a' || e.key === 'A')) {
                                        var qEl = document.getElementById('question-text');
                                        if (qEl && qEl.contains(document.activeElement || document.elementFromPoint(0,0))) {
                                            e.preventDefault();
                                        }
                                    }
                                    if (e.key === 'PrintScreen') {
                                        e.preventDefault();
                                        acSelf.antiCheatMessage = 'Screenshots are not permitted during this assessment.';
                                        acSelf.antiCheatVisible = true;
                                    }
                                });

                                // 6. Block copy on question area
                                document.addEventListener('copy', function (e) {
                                    var qEl = document.getElementById('question-text');
                                    if (qEl && qEl.contains(window.getSelection().anchorNode)) {
                                        e.preventDefault();
                                        acSelf.antiCheatMessage = 'Copying questions is not permitted during this assessment.';
                                        acSelf.antiCheatVisible = true;
                                    }
                                });

                                // 7. Split-screen detection via window resize (only after session started)
                                window.addEventListener('resize', function () {
                                    if (!acSelf.sessionStarted || acSelf.finishing) return;
                                    var ratio = window.innerWidth / window.screen.width;
                                    if (ratio < 0.75) {
                                        acSelf.antiCheatMessage = 'Split-screen detected. Please return to fullscreen to continue your assessment.';
                                        acSelf.antiCheatVisible = true;
                                        acSelf._restoreFS();
                                    }
                                });
                                // ── End Proctoring ───────────────────────────────────────

                                // Camera/mic is requested in startSession() triggered by the user clicking "Start Interview".
                            },

                            get currentQuestion() {
                                return this.questions[this.currentIndex] || {};
                            },

                            get currentQuestionNumber() {
                                return this.currentIndex + 1;
                            },

                            get totalQuestions() {
                                return this.questions.length;
                            },

                            get progressPercent() {
                                return Math.round(((this.currentIndex + 1) / this.totalQuestions) * 100);
                            },

                            get answeredCount() {
                                return Object.keys(this.answers).length;
                            },

                            get averageScore() {
                                var scores = Object.values(this.answers)
                                    .map(function (entry) { return entry.evaluation ? entry.evaluation.score : undefined; })
                                    .filter(function (score) { return typeof score === 'number'; });
                                if (scores.length === 0) { return 'â€”'; }
                                var total = scores.reduce(function (acc, val) { return acc + val; }, 0);
                                return Math.round(total / scores.length) + '%';
                            },

                            get formattedQuestionTimer() {
                                return this.formatSeconds(this.questionSeconds);
                            },

                            get formattedTotalTimer() {
                                return this.formatSeconds(this.totalSeconds);
                            },

                            get questionBadgeClass() {
                                switch (this.currentQuestion.type) {
                                    case 'behavioral': return 'bg-yellow-100 text-yellow-800';
                                    case 'technical': return 'bg-blue-100 text-blue-800';
                                    case 'situational': return 'bg-purple-100 text-purple-800';
                                    default: return 'bg-gray-100 text-gray-700';
                                }
                            },

                            startTimers() {
                                var self = this;
                                this.timerInterval = setInterval(function () { self.totalSeconds++; }, 1000);
                                this.questionInterval = setInterval(function () { self.questionSeconds++; }, 1000);
                            },

                            resetQuestionTimer() { this.questionSeconds = 0; },

                            formatSeconds(seconds) {
                                var mins = Math.floor(seconds / 60);
                                var secs = seconds % 60;
                                return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                            },

                            loadAnswer() {
                                var existing = this.answers[this.currentIndex];
                                this.answerText = existing ? (existing.answer || '') : '';
                                this.followUps = [];
                                this.resetQuestionTimer();
                            },

                            goTo(index) {
                                if (index < 0 || index >= this.totalQuestions) { return; }
                                this.currentIndex = index;
                                this.loadAnswer();
                                this.showFeedback = true;
                            },

                            previousQuestion() { this.goTo(this.currentIndex - 1); },
                            nextQuestion() { this.goTo(this.currentIndex + 1); },

                            navigatorClass(index) {
                                if (index === this.currentIndex) { return 'bg-indigo-500 text-white shadow'; }
                                if (this.answers[index]) { return 'bg-emerald-500 text-white shadow'; }
                                return 'bg-gray-200 text-gray-600 hover:bg-gray-300';
                            },

                            async saveAnswer() {
                                if (!this.answerText.trim()) {
                                    alert('Please type your answer before requesting feedback.');
                                    return;
                                }
                                this.saving = true;
                                try {
                                    var response = await fetch(this.submitUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            question_index: this.currentIndex,
                                            question: this.currentQuestion.question,
                                            answer: this.answerText,
                                        })
                                    });
                                    if (!response.ok) {
                                        var errBody = null;
                                        try { errBody = await response.json(); } catch (_) {}
                                        if (response.status === 404 && errBody && errBody.error) {
                                            alert(errBody.error + ' Please start a new interview session.');
                                        } else {
                                            throw new Error('Server returned ' + response.status);
                                        }
                                        return;
                                    }
                                    var data = await response.json();
                                    if (data.success) {
                                        this.answers[this.currentIndex] = {
                                            question: this.currentQuestion.question,
                                            answer: this.answerText,
                                            evaluation: data.evaluation || null,
                                            saved_at: new Date().toISOString(),
                                        };
                                        this.showFeedback = true;
                                        // Auto-advance to next question after saving
                                        if (this.currentIndex < this.totalQuestions - 1) {
                                            this.nextQuestion();
                                        }
                                    }
                                } catch (error) {
                                    console.error(error);
                                    alert('We could not save your answer right now. Please try again.');
                                } finally {
                                    this.saving = false;
                                }
                            },

                            async fetchFollowUps() {
                                if (!this.answerText.trim()) {
                                    alert('Provide an answer first to get follow-up questions.');
                                    return;
                                }
                                this.loadingFollowUps = true;
                                this.followUps = [];
                                try {
                                    var response = await fetch(this.followUpUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            question: this.currentQuestion.question,
                                            answer: this.answerText,
                                        })
                                    });
                                    if (!response.ok) { throw new Error('Failed to fetch follow-up questions.'); }
                                    this.followUps = await response.json();
                                } catch (error) {
                                    console.error(error);
                                    alert('Unable to fetch follow-up questions at the moment.');
                                } finally {
                                    this.loadingFollowUps = false;
                                }
                            },

                            skipQuestion() {
                                if (confirm('Skip this question and return later?')) { this.nextQuestion(); }
                            },

                            toggleFeedback() { this.showFeedback = !this.showFeedback; },

                            handleTabSwitch() {
                                if (!this.sessionStarted || this.finishing) return;
                                this.tabSwitchCount++;
                                if (this.tabSwitchCount >= 3) {
                                    this.antiCheatMessage = 'You have switched tabs 3 times. Your assessment is being submitted automatically.';
                                    this.antiCheatVisible = true;
                                    var self = this;
                                    setTimeout(function () { self.finishSession(); }, 2500);
                                } else {
                                    var left = 3 - this.tabSwitchCount;
                                    this.antiCheatMessage = 'Warning ' + this.tabSwitchCount + '/3: Tab switching is not allowed. ' + left + ' more violation' + (left === 1 ? '' : 's') + ' will auto-submit your assessment.';
                                    this.antiCheatVisible = true;
                                }
                            },

                            handlePasteAttempt() {
                                this.antiCheatMessage = 'Copy-pasting is not permitted. Please type your answer directly to ensure authenticity.';
                                this.antiCheatVisible = true;
                            },

                            dismissAntiCheat() { this.antiCheatVisible = false; this._restoreFS && this._restoreFS(); },

                            finishSession() {
                                if (this.answeredCount === 0) {
                                    if (!confirm('You have not saved any answers yet. Are you sure you want to finish?')) { return; }
                                }
                                // Set finishing flag FIRST so anti-cheat listeners ignore the navigation events
                                this.finishing = true;
                                var dest = this.completeUrl;
                                var doNav = function () { window.location.href = dest; };
                                // Exit fullscreen cleanly before navigating (avoids spurious fullscreenchange violation)
                                if (document.fullscreenElement || document.webkitFullscreenElement) {
                                    var exitP = document.exitFullscreen ? document.exitFullscreen() : Promise.resolve();
                                    exitP.then(doNav).catch(doNav);
                                } else {
                                    doNav();
                                }
                            },

                            startSession() {
                                this.sessionStarted = true;
                                // Request fullscreen via user gesture (required by browsers)
                                var el = document.documentElement;
                                var fsPromise = el.requestFullscreen
                                    ? el.requestFullscreen()
                                    : (el.webkitRequestFullscreen ? Promise.resolve(el.webkitRequestFullscreen()) : Promise.resolve());
                                if (fsPromise && fsPromise.catch) {
                                    fsPromise.catch(function (e) { console.warn('Fullscreen denied:', e && e.message); });
                                }
                                // Request camera + mic via user gesture
                                this.requestCamera();
                            },

                            requestCamera() {
                                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                                    this.cameraError = 'This browser does not support camera access. You can still complete the interview.';
                                    return;
                                }
                                this.requestingCamera = true;
                                this.cameraError = null;
                                var self = this;
                                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                                    .then(function (stream) {
                                        self.mediaStream = stream;
                                        self.cameraActive = true;
                                        self.micActive    = true;
                                        self.requestingCamera = false;
                                        var vid = document.getElementById('camera-preview');
                                        if (vid) { vid.srcObject = stream; vid.play(); }
                                    })
                                    .catch(function (err) {
                                        self.requestingCamera = false;
                                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                                            self.cameraError = 'Camera access blocked. Click the lock icon in the browser address bar, set Camera to "Allow", then click Enable Camera below.';
                                        } else if (err.name === 'NotFoundError') {
                                            self.cameraError = 'No camera or microphone found. You can still complete the interview.';
                                        } else {
                                            self.cameraError = 'Could not start camera (' + err.name + '). You can still complete the interview.';
                                        }
                                        console.warn('Camera/mic error:', err.name, err);
                                    });
                            },

                            beforeDestroy() {
                                clearInterval(this.timerInterval);
                                clearInterval(this.questionInterval);
                                if (this.mediaStream) {
                                    this.mediaStream.getTracks().forEach(function (t) { t.stop(); });
                                }
                            }
                        };
                };
            </script>

            <div x-data="interviewSession(window.__interviewConfig)" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Start Interview overlay -- requires user gesture for fullscreen + camera --}}
                <template x-if="!sessionStarted">
                    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="background:rgba(17,24,39,0.96);">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 text-center space-y-5">
                            <div class="text-5xl">&#127919;</div>
                            <h2 class="text-2xl font-bold text-gray-900">Ready to Start Your Interview?</h2>
                            <div class="text-sm text-gray-600 text-left bg-gray-50 rounded-xl p-4 space-y-2">
                                <p class="font-semibold text-gray-800 mb-1">What happens when you click Start:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Your browser will <strong>ask to allow camera &amp; microphone</strong> &mdash; click Allow</li>
                                    <li>The session enters <strong>fullscreen mode</strong> for integrity</li>
                                    <li>Tab switching is monitored (3 warnings = auto-submit)</li>
                                    <li>You have <strong>{{ count($flattenedQuestions) }} questions</strong> to answer</li>
                                </ul>
                                <p class="text-xs text-gray-400 pt-1">Camera is optional &mdash; you can complete the interview without it.</p>
                            </div>
                            <button @click="startSession()" class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 active:scale-95 transition-all shadow-lg">
                                &#128640; Start Interview
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Anti-cheat warning overlay --}}
                <template x-if="antiCheatVisible">
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
                        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 text-center space-y-4">
                            <div class="text-5xl">&#128683;</div>
                            <h3 class="text-lg font-bold text-red-700">Integrity Warning</h3>
                            <p class="text-sm text-gray-700 leading-relaxed" x-text="antiCheatMessage"></p>
                            <template x-if="tabSwitchCount < 3">
                                <button @click="dismissAntiCheat()" class="mt-2 px-6 py-2 bg-indigo-600 text-white rounded-md font-semibold hover:bg-indigo-700 transition">
                                    I understand, continue
                                </button>
                            </template>
                            <template x-if="tabSwitchCount >= 3">
                                <p class="text-sm text-gray-500 italic">Submitting your assessment&#8230;</p>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Empty state guard --}}
                @if(empty($flattenedQuestions))
                    <div class="lg:col-span-3 bg-amber-50 border border-amber-200 rounded-xl p-10 text-center">
                        <div class="text-5xl mb-4">”</div>
                        <h3 class="text-lg font-semibold text-amber-800 mb-2">No questions were generated</h3>
                        <p class="text-sm text-amber-700 mb-6">The AI could not generate questions for this session. Please start a new session and try again.</p>
                        <a href="{{ route('interview.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-md font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-redo mr-2"></i> Start New Session
                        </a>
                    </div>
                @else

                <!-- Primary Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between items-center mb-2 text-sm text-gray-600">
                            <span>Question <span x-text="currentQuestionNumber"></span> of <span x-text="totalQuestions"></span></span>
                            <span>Time on this question: <span x-text="formattedQuestionTimer"></span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-indigo-600 h-2 transition-all duration-500" :style="`width: ${progressPercent}%`"></div>
                        </div>
                    </div>

                    <!-- Question Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">
                        <div class="flex flex-wrap items-start gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide" :class="questionBadgeClass">
                                <i class="fas fa-question-circle mr-2"></i>
                                <span x-text="currentQuestion.typeLabel"></span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700" x-show="currentQuestion.difficulty">
                                <i class="fas fa-signal mr-2"></i>
                                <span class="capitalize" x-text="currentQuestion.difficulty"></span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700" x-show="answers[currentIndex]">
                                <i class="fas fa-star mr-1"></i>
                                Answer saved
                            </span>
                        </div>

                        <div>
                            {{-- Alpine-bound question text. The server-rendered fallback below is hidden once Alpine loads. --}}
                            <h3 id="question-text" class="text-2xl font-bold text-gray-900 select-none" style="user-select:none;-webkit-user-select:none" x-text="currentQuestion.question" @contextmenu.prevent @selectstart.prevent @dragstart.prevent>
                                {{-- server-rendered fallback: shows first question instantly before Alpine binds --}}
                                @if(!empty($flattenedQuestions[0]['question']))
                                    {{ $flattenedQuestions[0]['question'] }}
                                @endif
                            </h3>
                            <template x-if="currentQuestion.context">
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-700" x-text="currentQuestion.context"></p>
                                </div>
                            </template>
                            <template x-if="currentQuestion.topic">
                                <p class="mt-2 text-xs uppercase tracking-wide text-indigo-600">Topic: <span class="font-semibold" x-text="currentQuestion.topic"></span></p>
                            </template>
                            <template x-if="currentQuestion.category && !currentQuestion.topic">
                                <p class="mt-2 text-xs uppercase tracking-wide text-indigo-600">Category: <span class="font-semibold" x-text="currentQuestion.category"></span></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Answer</label>
                            <textarea x-model="answerText" @paste.prevent="handlePasteAttempt()" rows="8" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type your response here... Include specific examples and quantify your impact where possible."></textarea>
                            <p class="mt-2 text-xs text-gray-500">Tip: Speak your answer out loud first, then summarize it here to capture key points.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="button" @click="saveAnswer" :disabled="saving" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold hover:bg-indigo-700 transition disabled:opacity-70">
                                <i class="fas fa-save mr-2"></i>
                                <span x-text="saving ? 'Saving...' : 'Save Answer'"></span>
                            </button>
                            <button type="button" @click="skipQuestion" class="inline-flex items-center px-4 py-2 border border-transparent text-red-600 font-semibold rounded-md hover:bg-red-50 transition">
                                <i class="fas fa-forward mr-2"></i> Skip Question
                            </button>
                        </div>

                        <!-- Navigation -->
                        <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="previousQuestion" :disabled="currentIndex === 0" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 disabled:opacity-50">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" @click="nextQuestion" :disabled="currentIndex === totalQuestions - 1" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 disabled:opacity-50">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <div class="flex-1"></div>
                            <button type="button" @click="finishSession" class="inline-flex items-center px-5 py-2 bg-emerald-600 text-white rounded-md font-semibold hover:bg-emerald-700 transition">
                                <i class="fas fa-flag-checkered mr-2"></i> Finish & View Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Secondary Column -->
                <div class="space-y-6">

                    <!-- Camera Monitor -->
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                <i class="fas fa-video mr-1 text-indigo-600"></i> Camera Monitor
                            </h3>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-1" :class="cameraActive ? 'text-green-600' : 'text-red-500'">
                                    <span class="inline-block w-2 h-2 rounded-full" :class="cameraActive ? 'bg-green-500' : 'bg-red-500'"></span>
                                    <span x-text="cameraActive ? 'Cam Live' : 'Cam Off'"></span>
                                </span>
                                <span class="flex items-center gap-1" :class="micActive ? 'text-green-600' : 'text-red-500'">
                                    <i class="fas" :class="micActive ? 'fa-microphone' : 'fa-microphone-slash'"></i>
                                    <span x-text="micActive ? 'Mic On' : 'Mic Off'"></span>
                                </span>
                            </div>
                        </div>
                        <video id="camera-preview" autoplay muted playsinline x-show="cameraActive"
                               class="w-full rounded-lg bg-gray-900 aspect-video object-cover"
                               style="transform:scaleX(-1);"></video>
                        <template x-if="!cameraActive && !cameraError && !requestingCamera">
                            <div class="aspect-video rounded-lg bg-gray-100 flex flex-col items-center justify-center text-gray-400 text-sm gap-2">
                                <i class="fas fa-video text-2xl"></i>
                                <span>Camera will start with the interview</span>
                            </div>
                        </template>
                        <template x-if="requestingCamera">
                            <div class="aspect-video rounded-lg bg-gray-100 flex flex-col items-center justify-center text-gray-400 text-sm gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl"></i>
                                <span>Requesting camera access&hellip;</span>
                            </div>
                        </template>
                        <template x-if="cameraError">
                            <div class="rounded-lg bg-red-50 flex flex-col items-center justify-center text-red-700 text-xs gap-2 p-3 text-center py-4">
                                <i class="fas fa-video-slash text-2xl"></i>
                                <span x-text="cameraError"></span>
                                <button @click="requestCamera()" class="mt-1 px-4 py-1.5 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-700 transition">
                                    <i class="fas fa-redo mr-1"></i> Enable Camera
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Question Navigator -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Question Navigator</h3>
                            <span class="text-xs text-gray-500">Click to jump</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2">
                            <template x-for="question in questions" :key="question.index">
                                <button type="button" @click="goTo(question.index)" :class="navigatorClass(question.index)" class="aspect-square rounded-md text-xs font-semibold flex items-center justify-center">
                                    <span x-text="question.index + 1"></span>
                                </button>
                            </template>
                        </div>
                        <div class="mt-4 text-xs text-gray-500 space-y-1">
                            <p><span class="inline-block w-3 h-3 bg-indigo-500 rounded-sm mr-2"></span> Current question</p>
                            <p><span class="inline-block w-3 h-3 bg-emerald-500 rounded-sm mr-2"></span> Answer saved</p>
                            <p><span class="inline-block w-3 h-3 bg-gray-200 rounded-sm mr-2"></span> Pending</p>
                        </div>
                    </div>

                    <!-- Session Summary -->
                    <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Session Snapshot</h3>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-center justify-between">
                                <span>Total time elapsed</span>
                                <span class="font-semibold" x-text="formattedTotalTimer"></span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>Questions answered</span>
                                <span class="font-semibold" x-text="answeredCount + ' / ' + totalQuestions"></span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>Integrity alerts</span>
                                <span class="font-semibold" :class="tabSwitchCount > 0 ? 'text-red-600' : 'text-gray-900'" x-text="tabSwitchCount + ' / 3'"></span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-gray-100 text-xs text-gray-500">
                            Answers save instantly. Your Vantage AI skill report is generated when you finish all questions.
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-lightbulb text-indigo-600 text-xl"></i>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Coach's Corner</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                Lead with the Situation/Task, highlight your Action, and quantify the Result.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                Pause after reading the questionâ€”take 20 seconds to outline your answer.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                Mirror company values or job requirements mentioned in the role description.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                Mention tools, metrics, and collaboration partners to show depth and credibility.
                            </li>
                        </ul>
                    </div>
                </div>
                @endif {{-- end @else for empty questions --}}
            </div>
        </div>
    </div>


</x-app-layout>


