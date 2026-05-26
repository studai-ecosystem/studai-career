@extends('layouts.dashboard')
@section('title', 'Take Test — ' . $round->name)

@section('content')
{{-- ═══════════════════════════════════════════════════════════
     PROCTORED TEST — Full proctor environment
     ═══════════════════════════════════════════════════════════ --}}
<div id="proctor-root" x-data="proctorTest()" x-init="init()"
     class="min-h-screen bg-gray-950 text-white relative">

    {{-- ── PRE-TEST PERMISSIONS SCREEN ─────────────────────────── --}}
    <div x-show="phase === 'setup'" x-transition class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-gray-900 border border-gray-700 rounded-3xl p-10 max-w-lg w-full text-center shadow-2xl">
            <div class="w-16 h-16 rounded-2xl bg-purple-600 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2">Proctored Test Environment</h2>
            <p class="text-gray-400 text-sm mb-8">This test requires camera, microphone and fullscreen mode. Please allow access when prompted.</p>

            <div class="space-y-3 mb-8 text-left">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-800">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         :class="camGranted ? 'bg-green-500' : 'bg-gray-600'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" :class="camGranted ? 'text-green-400' : 'text-gray-300'">Camera</p>
                        <p class="text-xs text-gray-500" x-text="camGranted ? 'Access granted' : 'Required for proctoring'"></p>
                    </div>
                    <span x-show="camGranted" class="ml-auto text-green-400 text-xl font-bold">✓</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-800">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         :class="micGranted ? 'bg-green-500' : 'bg-gray-600'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" :class="micGranted ? 'text-green-400' : 'text-gray-300'">Microphone</p>
                        <p class="text-xs text-gray-500" x-text="micGranted ? 'Access granted' : 'Required for proctoring'"></p>
                    </div>
                    <span x-show="micGranted" class="ml-auto text-green-400 text-xl font-bold">✓</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-800">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-300">Fullscreen Mode</p>
                        <p class="text-xs text-gray-500">Enabled automatically when test starts</p>
                    </div>
                </div>
            </div>

            <div x-show="camGranted" class="mb-6 rounded-2xl overflow-hidden bg-black border border-gray-700 relative" style="aspect-ratio:16/9">
                <video id="setup-preview" autoplay muted playsinline class="w-full h-full object-cover scale-x-[-1]"></video>
                <div class="absolute bottom-2 right-2 flex items-center gap-1.5 bg-black/70 px-2 py-1 rounded-lg">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-xs text-white font-medium">Live</span>
                </div>
            </div>

            <p x-show="permError" x-text="permError" class="text-red-400 text-xs mb-4 p-3 bg-red-900/30 rounded-xl"></p>

            <button @click="requestPermissions()" x-show="!camGranted || !micGranted"
                class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 rounded-2xl font-bold text-sm transition-all mb-3">
                Allow Camera &amp; Microphone
            </button>
            <button @click="startTest()" x-show="camGranted && micGranted"
                class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:shadow-lg hover:shadow-purple-500/30 rounded-2xl font-bold text-sm transition-all">
                Start Test in Fullscreen →
            </button>
            <p class="text-xs text-gray-600 mt-3">By starting you agree to be monitored via camera &amp; microphone for the duration of the test.</p>
        </div>
    </div>

    {{-- ── ACTIVE TEST SCREEN ───────────────────────────────────── --}}
    <div x-show="phase === 'test'" class="flex flex-col min-h-screen">

        <div class="flex items-center justify-between px-6 py-3 bg-gray-900 border-b border-gray-800 flex-shrink-0">
            <div>
                <p class="text-xs font-bold text-purple-400 uppercase tracking-widest">Round {{ $round->round_order }} — {{ $round->job->title }}</p>
                <h1 class="text-base font-bold text-white">{{ $round->name }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <template x-for="i in 3" :key="i">
                    <div class="w-3 h-3 rounded-full transition-colors" :class="i <= violations ? 'bg-red-500' : 'bg-gray-700'"></div>
                </template>
                <span class="text-xs text-gray-400 ml-1">violations</span>
            </div>
            <div class="flex items-center gap-2 bg-gray-800 border border-gray-700 rounded-xl px-4 py-2">
                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-mono text-base font-bold" :class="seconds < 300 ? 'text-red-400 animate-pulse' : 'text-white'" x-text="formatTime()"></span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6 max-w-3xl mx-auto w-full">
            <form method="POST" action="{{ route('candidate.test.submit', [$round->job_id, $round->id]) }}" id="test-form">
                @csrf
                <input type="hidden" name="violations" x-model="violations">
                <div class="space-y-6 pb-32">
                    @foreach($attempt->questions as $index => $q)
                    <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-start gap-3 mb-4">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-600 text-white font-bold text-sm flex items-center justify-center">{{ $index + 1 }}</span>
                            <p class="text-gray-100 font-medium leading-relaxed">{{ $q['question'] }}</p>
                        </div>
                        @if($q['type'] === 'mcq')
                        <div class="space-y-3 ml-11">
                            @foreach($q['options'] as $optIdx => $option)
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-700 cursor-pointer hover:border-purple-500 hover:bg-purple-500/10 transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                                <input type="radio" name="answers[{{ $index }}]" value="{{ $optIdx }}"
                                    class="w-4 h-4 text-purple-500 border-gray-600 bg-gray-800 focus:ring-purple-500">
                                <span class="text-sm text-gray-300">{{ $option }}</span>
                            </label>
                            @endforeach
                        </div>
                        @else
                        <div class="ml-11">
                            <textarea name="answers[{{ $index }}]" rows="4"
                                placeholder="Type your answer here..."
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-sm text-gray-100 placeholder-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- Bottom bar --}}
        <div class="fixed bottom-0 left-0 right-0 bg-gray-900/95 backdrop-blur border-t border-gray-800 px-6 py-3 flex items-center justify-between gap-4 z-40">
            <div class="text-sm text-gray-400">
                <span class="font-semibold text-white">{{ count($attempt->questions) }}</span> questions &bull;
                <span class="text-purple-400" x-text="formatTime() + ' remaining'"></span>
            </div>
            {{-- Camera preview --}}
            <div class="relative rounded-xl overflow-hidden border-2 border-green-500 shadow-lg shadow-green-500/20 flex-shrink-0" style="width:140px;height:79px">
                <video id="footer-cam" autoplay muted playsinline class="w-full h-full object-cover scale-x-[-1]"></video>
                <div class="absolute top-1 left-1 flex items-center gap-1 bg-black/70 px-1.5 py-0.5 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-[10px] text-white font-bold">REC</span>
                </div>
                <div class="absolute bottom-1 right-1">
                    <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <button @click="confirmSubmit()"
                class="inline-flex items-center gap-2 px-7 py-2.5 bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white font-bold rounded-xl shadow-md hover:shadow-purple-500/30 hover:shadow-lg transition-all hover:scale-105 active:scale-100 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Submit Test
            </button>
        </div>
    </div>

    {{-- Warning modal --}}
    <div x-show="showWarning" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm px-4">
        <div class="bg-gray-900 border-2 border-red-500 rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl shadow-red-500/20">
            <div class="w-14 h-14 rounded-2xl bg-red-500/20 border border-red-500 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Proctoring Violation</h3>
            <p class="text-gray-400 text-sm mb-2" x-text="warningMessage"></p>
            <p class="text-red-400 text-sm font-semibold mb-6">Warning <span x-text="violations"></span> of 3 &mdash; test auto-submits at 3.</p>
            <button @click="dismissWarning()" class="w-full py-3 bg-purple-600 hover:bg-purple-700 rounded-xl font-bold text-sm transition-all">
                Return to Test
            </button>
        </div>
    </div>

    {{-- Confirm submit modal --}}
    <div x-show="showConfirm" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm px-4">
        <div class="bg-gray-900 border border-gray-700 rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-2">Submit Test?</h3>
            <p class="text-gray-400 text-sm mb-6">You cannot change your answers after submission.</p>
            <div class="flex gap-3">
                <button @click="showConfirm = false" class="flex-1 py-3 bg-gray-800 hover:bg-gray-700 rounded-xl font-bold text-sm transition-all">Cancel</button>
                <button @click="doSubmit()" class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-fuchsia-600 rounded-xl font-bold text-sm transition-all">Submit</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function proctorTest() {
    return {
        phase: 'setup',
        camGranted: false,
        micGranted: false,
        permError: null,
        stream: null,
        violations: 0,
        showWarning: false,
        showConfirm: false,
        warningMessage: '',
        seconds: {{ max(0, 30 * 60 - ($attempt->started_at ? now()->diffInSeconds($attempt->started_at) : 0)) }},
        timerInterval: null,

        init() {
            document.addEventListener('copy',        e => e.preventDefault());
            document.addEventListener('cut',         e => e.preventDefault());
            document.addEventListener('paste',       e => e.preventDefault());
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && ['c','v','x','a'].includes(e.key.toLowerCase())) e.preventDefault();
                if (e.key === 'PrintScreen') e.preventDefault();
            });
            document.addEventListener('visibilitychange', () => {
                if (this.phase === 'test' && document.hidden)
                    this.registerViolation('Tab switching detected. Stay on this page during the test.');
            });
            window.addEventListener('blur', () => {
                if (this.phase === 'test')
                    this.registerViolation('Window focus lost. Do not switch applications during the test.');
            });
            document.addEventListener('fullscreenchange', () => {
                if (this.phase === 'test' && !document.fullscreenElement) {
                    this.registerViolation('Fullscreen mode was exited. Please remain in fullscreen.');
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            });
        },

        async requestPermissions() {
            this.permError = null;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                this.camGranted = true;
                this.micGranted = true;
                await this.$nextTick();
                const p = document.getElementById('setup-preview');
                if (p) p.srcObject = this.stream;
            } catch (e) {
                this.permError = 'Could not access camera/microphone. Please allow access in your browser settings and try again.';
            }
        },

        async startTest() {
            try { await document.documentElement.requestFullscreen(); } catch (e) {}
            this.phase = 'test';
            await this.$nextTick();
            const fc = document.getElementById('footer-cam');
            if (fc && this.stream) fc.srcObject = this.stream;
            this.timerInterval = setInterval(() => {
                if (this.seconds > 0) { this.seconds--; }
                else { clearInterval(this.timerInterval); this.doSubmit(); }
            }, 1000);
        },

        registerViolation(message) {
            if (this.showWarning) return;
            this.violations++;
            this.warningMessage = message;
            this.showWarning = true;
            if (this.violations >= 3) {
                this.warningMessage = message + ' Maximum violations reached — submitting now.';
                setTimeout(() => this.doSubmit(), 3000);
            }
        },

        dismissWarning() {
            this.showWarning = false;
            if (!document.fullscreenElement && this.phase === 'test')
                document.documentElement.requestFullscreen().catch(() => {});
        },

        confirmSubmit() { this.showConfirm = true; },

        doSubmit() {
            this.phase = 'submitting'; // disable all violation listeners immediately
            this.showConfirm = false;
            this.showWarning = false;
            clearInterval(this.timerInterval);
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
            if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
            document.getElementById('test-form').submit();
        },

        formatTime() {
            const s = Math.max(0, this.seconds);
            return Math.floor(s / 60).toString().padStart(2, '0') + ':' + (s % 60).toString().padStart(2, '0');
        },
    };
}
</script>
@endpush
@endsection
