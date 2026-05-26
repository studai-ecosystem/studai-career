<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Company Setup — S.C.O.U.T™ | StudAI Hire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full">

<div x-data="{
    step: 1,
    totalSteps: 4,
    submitting: false,
    aiLoading: false,
    aiField: '',
    aiSuggestions: [],
    showAiModal: false,
    form: {
        headquarters: '',
        founded_year: '',
        description: '',
        mission: '',
        culture_values: [],
        work_style: '',
        team_vibe: '',
        hiring_priorities: [],
        roles_hiring_for: '',
        open_to_remote: false,
        top_perks: [],
    },
    cultureOptions: [
        'Innovation-first', 'Collaboration', 'Ownership mindset', 'Customer obsession',
        'Diversity & inclusion', 'Work-life balance', 'High performance', 'Continuous learning',
        'Transparency', 'Agility', 'Data-driven', 'Mission-driven'
    ],
    hiringOptions: [
        'Cultural fit', 'Technical skills', 'Growth potential', 'Past achievements',
        'Communication skills', 'Leadership potential', 'Domain expertise', 'Attitude & mindset'
    ],
    perkOptions: [
        'Remote / Hybrid work', 'Health insurance', 'Stock options / ESOPs',
        'Learning & development budget', 'Flexible hours', 'Free meals', 'Gym membership',
        'Annual bonus', 'Team retreats', 'Parental leave', 'Mental health support'
    ],
    toggleValue(arr, val) {
        const idx = arr.indexOf(val);
        if (idx > -1) { arr.splice(idx, 1); } else { arr.push(val); }
    },
    progressWidth() {
        return ((this.step - 1) / (this.totalSteps - 1)) * 100;
    },
    async generateSuggestions(field) {
        if (!this.form.headquarters && !this.form.description) {
            const name = document.querySelector('[name=company_name_display]')?.value || '';
        }
        this.aiField = field;
        this.aiLoading = true;
        this.showAiModal = true;
        this.aiSuggestions = [];
        try {
            const resp = await fetch('{{ route('employer.onboarding.ai-suggest') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    field: field,
                    company_name: '{{ auth()->user()->company?->name ?? '' }}',
                    industry: '{{ auth()->user()->company?->industry ?? '' }}',
                    headquarters: this.form.headquarters,
                    founded_year: this.form.founded_year,
                }),
            });
            const data = await resp.json();
            if (data.suggestions) {
                this.aiSuggestions = data.suggestions;
            } else {
                this.aiSuggestions = ['Could not generate suggestions. Please try again.'];
            }
        } catch(e) {
            this.aiSuggestions = ['Could not generate suggestions. Please try again.'];
        } finally {
            this.aiLoading = false;
        }
    },
    applySuggestion(text) {
        if (this.aiField === 'description') this.form.description = text;
        if (this.aiField === 'mission') this.form.mission = text;
        this.showAiModal = false;
    },
}" class="min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-fuchsia-500 to-pink-600 text-white text-xs font-black">S</div>
            <div>
                <p class="text-sm font-bold text-gray-900">S.C.O.U.T™</p>
                <p class="text-[0.6rem] text-gray-400 uppercase tracking-widest">Company Setup</p>
            </div>
        </div>
        <div class="text-xs text-gray-400">Step <span x-text="step"></span> of <span x-text="totalSteps"></span></div>
    </header>

    {{-- Progress bar --}}
    <div class="h-1 bg-gray-100">
        <div class="h-1 bg-gradient-to-r from-fuchsia-500 to-pink-500 transition-all duration-500"
            :style="'width: ' + progressWidth() + '%'"></div>
    </div>

    {{-- Main content --}}
    <main class="flex-1 flex items-start justify-center px-4 py-12">
        <div class="w-full max-w-2xl">

            <form method="POST" action="{{ route('employer.onboarding.save') }}" @submit.prevent="submitting = true; $el.submit()">
                @csrf

                {{-- ═══════════════════════════════════════ --}}
                {{-- STEP 1: Company Basics --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="step === 1" x-transition>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-fuchsia-100 text-fuchsia-700 text-xs font-semibold px-3 py-1 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                            Step 1 — Company Profile
                        </span>
                        <h1 class="text-2xl font-bold text-gray-900">Tell us about your company</h1>
                        <p class="mt-2 text-gray-500 text-sm">This forms the foundation of your Corporate DNA — the more detail, the smarter the AI hiring becomes.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Headquarters / City</label>
                                <input type="text" name="headquarters" x-model="form.headquarters"
                                    placeholder="e.g. Bangalore, India"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year Founded</label>
                                <input type="number" name="founded_year" x-model="form.founded_year"
                                    placeholder="e.g. 2015" min="1800" max="{{ date('Y') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Company Description</label>
                                <button type="button" @click="generateSuggestions('description')"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-fuchsia-600 hover:text-fuchsia-700 bg-fuchsia-50 hover:bg-fuchsia-100 px-2.5 py-1 rounded-full transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    Generate with AI
                                </button>
                            </div>
                            <textarea name="description" x-model="form.description" rows="4"
                                placeholder="What does your company do? What problem do you solve? What makes you different?"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 resize-none"></textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Company Mission / Vision <span class="text-gray-400 font-normal">(optional)</span></label>
                                <button type="button" @click="generateSuggestions('mission')"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-fuchsia-600 hover:text-fuchsia-700 bg-fuchsia-50 hover:bg-fuchsia-100 px-2.5 py-1 rounded-full transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    Generate with AI
                                </button>
                            </div>
                            <textarea name="mission" x-model="form.mission" rows="2"
                                placeholder="What is your company's core mission statement?"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="step = 2"
                            class="inline-flex items-center gap-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 px-6 py-3 text-sm font-bold text-white transition-colors shadow">
                            Next: Culture &amp; Values
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════ --}}
                {{-- STEP 2: Culture & DNA --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="step === 2" x-transition>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-fuchsia-100 text-fuchsia-700 text-xs font-semibold px-3 py-1 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Step 2 — Corporate DNA
                        </span>
                        <h1 class="text-2xl font-bold text-gray-900">What defines your culture?</h1>
                        <p class="mt-2 text-gray-500 text-sm">Orin™ AI uses this to build your <strong>Success Blueprint</strong> — predicting which candidates will thrive, not just survive.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        {{-- Culture values multi-select --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Core Culture Values <span class="text-gray-400 font-normal">(pick all that apply)</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="option in cultureOptions" :key="option">
                                    <button type="button"
                                        @click="toggleValue(form.culture_values, option)"
                                        :class="form.culture_values.includes(option)
                                            ? 'bg-fuchsia-600 text-white border-fuchsia-600'
                                            : 'bg-white text-gray-600 border-gray-300 hover:border-fuchsia-400'"
                                        class="rounded-full border px-3 py-1.5 text-xs font-medium transition-all">
                                        <span x-text="option"></span>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="culture_values" :value="JSON.stringify(form.culture_values)">
                        </div>

                        {{-- Work style --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Work Style</label>
                            <div class="grid grid-cols-3 gap-3">
                                <template x-for="style in ['In-office', 'Hybrid', 'Fully Remote']" :key="style">
                                    <button type="button"
                                        @click="form.work_style = style"
                                        :class="form.work_style === style
                                            ? 'border-fuchsia-500 bg-fuchsia-50 text-fuchsia-700 ring-2 ring-fuchsia-400'
                                            : 'border-gray-200 text-gray-600 hover:border-fuchsia-300'"
                                        class="rounded-xl border-2 p-3 text-sm font-medium text-center transition-all">
                                        <span x-text="style"></span>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="work_style" :value="form.work_style">
                        </div>

                        {{-- Team vibe --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Team Vibe</label>
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="vibe in ['Startup hustle', 'Corporate structured', 'Creative studio', 'Research lab', 'Fast-paced sales', 'Calm & focused']" :key="vibe">
                                    <button type="button"
                                        @click="form.team_vibe = vibe"
                                        :class="form.team_vibe === vibe
                                            ? 'border-fuchsia-500 bg-fuchsia-50 text-fuchsia-700 ring-2 ring-fuchsia-400'
                                            : 'border-gray-200 text-gray-600 hover:border-fuchsia-300'"
                                        class="rounded-xl border-2 p-3 text-xs font-medium text-center transition-all">
                                        <span x-text="vibe"></span>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="team_vibe" :value="form.team_vibe">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" @click="step = 1"
                            class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                            ← Back
                        </button>
                        <button type="button" @click="step = 3"
                            class="inline-flex items-center gap-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 px-6 py-3 text-sm font-bold text-white transition-colors shadow">
                            Next: Hiring Priorities
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════ --}}
                {{-- STEP 3: Hiring Priorities --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="step === 3" x-transition>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-fuchsia-100 text-fuchsia-700 text-xs font-semibold px-3 py-1 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Step 3 — Hiring Intelligence
                        </span>
                        <h1 class="text-2xl font-bold text-gray-900">What matters most when you hire?</h1>
                        <p class="mt-2 text-gray-500 text-sm">Orin™ AI learns your priorities and weights them when scoring every candidate for you.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        {{-- Hiring priorities --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Top Hiring Priorities <span class="text-gray-400 font-normal">(pick your top 3)</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="option in hiringOptions" :key="option">
                                    <button type="button"
                                        @click="toggleValue(form.hiring_priorities, option)"
                                        :class="form.hiring_priorities.includes(option)
                                            ? 'bg-fuchsia-600 text-white border-fuchsia-600'
                                            : 'bg-white text-gray-600 border-gray-300 hover:border-fuchsia-400'"
                                        class="rounded-full border px-3 py-1.5 text-xs font-medium transition-all">
                                        <span x-text="option"></span>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="hiring_priorities" :value="JSON.stringify(form.hiring_priorities)">
                        </div>

                        {{-- Roles --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">What roles are you hiring for?</label>
                            <textarea name="roles_hiring_for" x-model="form.roles_hiring_for" rows="3"
                                placeholder="e.g. Software Engineers, Product Managers, Sales Executives, Data Analysts..."
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 resize-none"></textarea>
                        </div>

                        {{-- Remote --}}
                        <div class="flex items-center gap-3">
                            <button type="button"
                                @click="form.open_to_remote = !form.open_to_remote"
                                :class="form.open_to_remote ? 'bg-fuchsia-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="form.open_to_remote ? 'translate-x-5' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-1"></span>
                            </button>
                            <label class="text-sm font-medium text-gray-700">Open to hiring remote candidates</label>
                            <input type="hidden" name="open_to_remote" :value="form.open_to_remote ? '1' : '0'">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" @click="step = 2"
                            class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                            ← Back
                        </button>
                        <button type="button" @click="step = 4"
                            class="inline-flex items-center gap-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 px-6 py-3 text-sm font-bold text-white transition-colors shadow">
                            Next: Perks &amp; Benefits
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════ --}}
                {{-- STEP 4: Perks & Launch --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="step === 4" x-transition>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-fuchsia-100 text-fuchsia-700 text-xs font-semibold px-3 py-1 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            Step 4 — Final Step
                        </span>
                        <h1 class="text-2xl font-bold text-gray-900">What do you offer candidates?</h1>
                        <p class="mt-2 text-gray-500 text-sm">Perks and benefits attract top candidates. The AI uses this to match people who value what you provide.</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Top Perks &amp; Benefits <span class="text-gray-400 font-normal">(pick all that apply)</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="perk in perkOptions" :key="perk">
                                    <button type="button"
                                        @click="toggleValue(form.top_perks, perk)"
                                        :class="form.top_perks.includes(perk)
                                            ? 'bg-fuchsia-600 text-white border-fuchsia-600'
                                            : 'bg-white text-gray-600 border-gray-300 hover:border-fuchsia-400'"
                                        class="rounded-full border px-3 py-1.5 text-xs font-medium transition-all">
                                        <span x-text="perk"></span>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="top_perks" :value="JSON.stringify(form.top_perks)">
                        </div>

                        {{-- DNA summary preview --}}
                        <div class="rounded-xl bg-gradient-to-br from-fuchsia-50 to-pink-50 border border-fuchsia-200 p-4">
                            <p class="text-xs font-bold text-fuchsia-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                Orin™ Corporate DNA Summary
                            </p>
                            <div class="space-y-1 text-xs text-fuchsia-800">
                                <p>Culture: <span class="font-medium" x-text="form.culture_values.length > 0 ? form.culture_values.slice(0,3).join(', ') : 'Not set yet'"></span></p>
                                <p>Work style: <span class="font-medium" x-text="form.work_style || 'Not set yet'"></span></p>
                                <p>Team vibe: <span class="font-medium" x-text="form.team_vibe || 'Not set yet'"></span></p>
                                <p>Hiring focus: <span class="font-medium" x-text="form.hiring_priorities.length > 0 ? form.hiring_priorities.slice(0,2).join(', ') : 'Not set yet'"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" @click="step = 3"
                            class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                            ← Back
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-fuchsia-600 to-pink-600 hover:from-fuchsia-700 hover:to-pink-700 px-8 py-3 text-sm font-bold text-white transition-all shadow-lg shadow-fuchsia-500/30 disabled:opacity-70">
                            <span x-text="submitting ? 'Setting up your S.C.O.U.T™...' : 'Launch S.C.O.U.T™ Dashboard →'"></span>
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </button>
                    </div>
                </div>

            </form>

            {{-- Skip link --}}
            <div class="mt-6 text-center">
                <a href="{{ route('employer.home') }}" class="text-xs text-gray-400 hover:text-gray-600 underline">
                    Skip for now — I'll complete this later
                </a>
            </div>
        </div>
    </main>
</div>

{{-- AI Suggestions Modal --}}
<div x-show="showAiModal" x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    @keydown.escape.window="showAiModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.outside="showAiModal = false">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-fuchsia-500 to-pink-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">AI-Generated Suggestions</p>
                    <p class="text-xs text-gray-400" x-text="aiField === 'description' ? 'Choose a company description' : 'Choose a mission / vision'"></p>
                </div>
            </div>
            <button type="button" @click="showAiModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-3">
            {{-- Loading state --}}
            <div x-show="aiLoading" class="flex flex-col items-center py-8 gap-3">
                <svg class="w-8 h-8 animate-spin text-fuchsia-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-sm text-gray-500">Generating options for your company...</p>
            </div>

            {{-- Suggestions list --}}
            <template x-if="!aiLoading">
                <div class="space-y-3">
                    <template x-for="(suggestion, i) in aiSuggestions" :key="i">
                        <div class="group relative rounded-xl border-2 border-gray-200 hover:border-fuchsia-400 hover:bg-fuchsia-50/50 p-4 cursor-pointer transition-all"
                            @click="applySuggestion(suggestion)">
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-bold flex items-center justify-center group-hover:bg-fuchsia-600 group-hover:text-white transition-colors" x-text="i + 1"></span>
                                <p class="text-sm text-gray-700 group-hover:text-gray-900 leading-relaxed" x-text="suggestion"></p>
                            </div>
                            <span class="absolute top-3 right-3 text-xs text-fuchsia-500 font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Use this →</span>
                        </div>
                    </template>
                    <button type="button" @click="generateSuggestions(aiField)"
                        class="w-full text-center text-xs text-gray-400 hover:text-fuchsia-600 py-2 transition-colors">
                        ↻ Generate new options
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

</body>
</html>
