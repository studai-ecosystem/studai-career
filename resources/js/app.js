import './bootstrap';

// ── MERIDIAN THEME — apply persisted theme before paint to avoid flash ──────
(() => {
    const stored = localStorage.getItem('meridian-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const dark = stored ? stored === 'dark' : prefersDark;
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
})();

// Livewire 3 bundles and manages Alpine internally.
// Do NOT import Alpine separately — it causes duplicate instances.
// Register Alpine components via alpine:init so Livewire's $wire is available.
document.addEventListener('alpine:init', () => {
    // ── MERIDIAN THEME STORE ────────────────────────────────────────────────
    window.Alpine.store('theme', {
        dark: document.documentElement.getAttribute('data-theme') === 'dark',

        init() {
            // Honour OS changes only while the user has made no explicit choice.
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('meridian-theme')) {
                    this.dark = e.matches;
                    this.apply();
                }
            });
        },

        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('meridian-theme', this.dark ? 'dark' : 'light');
            this.apply();
        },

        apply() {
            document.documentElement.setAttribute('data-theme', this.dark ? 'dark' : 'light');
        },
    });

    window.Alpine.data('coachChat', () => ({
        listening: false,
        noSpeechApi: false,
        voiceStatus: 'Listening... speak now',
        errorMsg: '',
        recognition: null,

        init() {
            this.$nextTick(() => this.scrollBottom());
            this.$wire.on('scroll-to-bottom', () => this.scrollBottom());
            this.$wire.on('show-error', (data) => {
                const arr = Array.isArray(data) ? data[0] : data;
                this.errorMsg = arr?.message ?? 'Something went wrong.';
                setTimeout(() => this.errorMsg = '', 6000);
            });
        },

        scrollBottom() {
            setTimeout(() => {
                const c = document.getElementById('messages-container');
                if (c) c.scrollTop = c.scrollHeight;
            }, 100);
        },

        toggleVoice() { this.listening ? this.stopVoice() : this.startVoice(); },

        startVoice() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SR) {
                this.noSpeechApi = true;
                setTimeout(() => this.noSpeechApi = false, 5000);
                return;
            }
            this.recognition = new SR();
            this.recognition.lang = 'en-US';
            this.recognition.interimResults = true;
            this.recognition.continuous = false;
            this.recognition.onstart = () => { this.listening = true; this.voiceStatus = 'Listening... speak now'; };
            this.recognition.onresult = (e) => {
                const t = Array.from(e.results).map(r => r[0].transcript).join('');
                this.$wire.set('message', t);
                this.voiceStatus = t ? '"' + t.slice(0, 40) + '..."' : 'Listening...';
            };
            this.recognition.onend = () => {
                this.listening = false;
                const msg = this.$wire.get('message');
                if (msg && msg.trim().length > 2) {
                    this.$wire.set('isVoiceInput', true);
                    this.$wire.call('sendMessage');
                }
            };
            this.recognition.onerror = (e) => {
                this.listening = false;
                if (e.error !== 'no-speech') {
                    this.errorMsg = 'Voice error: ' + e.error;
                    setTimeout(() => this.errorMsg = '', 5000);
                }
            };
            this.recognition.start();
        },

        stopVoice() {
            if (this.recognition) this.recognition.stop();
            this.listening = false;
        },
    }));
});
