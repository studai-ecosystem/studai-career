@extends('layouts.dashboard')

@section('title', 'Post New Job')

@section('content')
<div x-data="jobCreator()" class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- PHASE 1 — AI KICKOFF (shown when aiSuccess is false)         --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div x-show="!aiSuccess" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">

            {{-- Back link --}}
            <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center gap-1.5 text-sm text-purple-300 hover:text-white mb-8 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Jobs
            </a>

            {{-- Hero card --}}
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-10 text-center mb-6">
                {{-- S.C.O.U.T badge --}}
                <div class="inline-flex items-center gap-2 bg-fuchsia-500/20 border border-fuchsia-400/30 text-fuchsia-300 text-xs font-bold px-4 py-1.5 rounded-full mb-6 tracking-widest uppercase">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    S.C.O.U.T&trade; AI Job Builder
                </div>

                <h1 class="text-4xl font-black text-white mb-3">Post a Job in <span class="text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-400 to-purple-400">10 seconds</span></h1>
                <p class="text-purple-200 text-base max-w-lg mx-auto mb-10">Just type your job title. Our AI writes the entire job description, responsibilities, qualifications, skills and salary range for you.</p>

                {{-- Error alert --}}
                <div x-show="aiError" x-transition x-cloak class="mb-6 p-4 bg-red-500/20 border border-red-400/30 rounded-2xl flex items-start gap-3 text-left max-w-xl mx-auto">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-300">Generation Failed</p>
                        <p class="text-xs text-red-400 mt-0.5" x-text="aiError"></p>
                    </div>
                    <button @click="aiError = null" class="text-red-400 hover:text-red-200 flex-shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>

                {{-- Job title input + generate --}}
                <div class="max-w-2xl mx-auto">
                    <div class="flex gap-3">
                        <input x-model="jobTitle" type="text"
                            @keydown.enter.prevent="generateWithAI()"
                            class="flex-1 px-5 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-purple-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent transition-all"
                            placeholder="e.g. Senior Full Stack Developer, Product Manager..." />
                        <button type="button" @click="generateWithAI()"
                            :disabled="aiLoading || !jobTitle.trim()"
                            class="flex-shrink-0 flex items-center gap-2.5 px-7 py-4 rounded-2xl font-bold text-sm transition-all
                                   bg-gradient-to-r from-fuchsia-500 to-purple-600 text-white shadow-lg shadow-fuchsia-500/30
                                   hover:from-fuchsia-400 hover:to-purple-500 hover:shadow-fuchsia-400/40
                                   disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none active:scale-95">
                            <template x-if="!aiLoading">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </template>
                            <template x-if="aiLoading">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <span x-text="aiLoading ? 'Generating...' : ' Generate with AI'"></span>
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-purple-400">Press Enter or click the button. Takes about 10&mdash;15 seconds.</p>
                </div>

                {{-- Skip AI option --}}
                <div class="mt-6">
                    <button type="button" @click="skipAI()"
                        class="inline-flex items-center gap-2 text-sm text-purple-400 hover:text-purple-200 underline underline-offset-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Skip AI � Fill job details manually instead
                    </button>
                </div>

                {{-- Quick options --}}
                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <div>
                        <label class="text-xs text-purple-400 mb-1 block">Experience Level</label>
                        <select x-model="experienceLevel" class="bg-white/10 border border-white/20 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 min-w-[140px]">
                            <option value="entry" class="text-gray-900">Entry Level</option>
                            <option value="mid" selected class="text-gray-900">Mid Level</option>
                            <option value="senior" class="text-gray-900">Senior Level</option>
                            <option value="lead" class="text-gray-900">Lead / Principal</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-purple-400 mb-1 block">Job Type</label>
                        <select x-model="jobType" class="bg-white/10 border border-white/20 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 min-w-[140px]">
                            <option value="full-time" selected class="text-gray-900">Full-time</option>
                            <option value="part-time" class="text-gray-900">Part-time</option>
                            <option value="contract" class="text-gray-900">Contract</option>
                            <option value="internship" class="text-gray-900">Internship</option>
                            <option value="remote" class="text-gray-900">Remote</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Feature pills --}}
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Job Description','Responsibilities','Qualifications','Required Skills','Salary Range'] as $f)
                <span class="text-xs text-purple-300 bg-white/5 border border-white/10 px-4 py-1.5 rounded-full">{{ $f }}</span>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- PHASE 2 — AI PREVIEW + FULL FORM (shown after generation)    --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div x-show="aiSuccess" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>

            {{-- ── AI RESULT PREVIEW CARD ──────────────────────────── --}}
            <div class="bg-gradient-to-br from-fuchsia-900/80 via-purple-900/80 to-indigo-900/80 backdrop-blur-sm border border-fuchsia-500/20 rounded-3xl p-8 mb-8 relative overflow-hidden">
                {{-- Glow blob --}}
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-fuchsia-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 mb-6 relative">
                    <div>
                        <div class="inline-flex items-center gap-1.5 bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold px-3 py-1 rounded-full mb-3">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            S.C.O.U.T&trade; AI Generated
                        </div>
                        <h2 class="text-2xl font-black text-white" x-text="jobTitle"></h2>
                        <p class="text-fuchsia-300 text-sm mt-1" x-text="(experienceLevel || 'Mid') + ' &middot; ' + (jobType || 'Full-time')"></p>
                    </div>
                    <button type="button" @click="resetAI()"
                        class="flex-shrink-0 flex items-center gap-1.5 text-xs text-purple-300 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-2 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Regenerate
                    </button>
                </div>

                {{-- Salary + Skills row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6 relative">
                    {{-- Salary --}}
                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-xs text-purple-300 font-semibold uppercase tracking-wider mb-1">?� Salary Range</p>
                        <p class="text-white font-bold text-lg" x-text="formatSalary(salaryMin) + ' � ' + formatSalary(salaryMax)"></p>
                        <p class="text-fuchsia-300 text-xs mt-0.5" x-text="salaryNote"></p>
                    </div>
                    {{-- Skills --}}
                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-xs text-purple-300 font-semibold uppercase tracking-wider mb-2">?� Required Skills</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="skill in skills.slice(0,6)" :key="skill">
                                <span class="text-xs bg-fuchsia-500/30 border border-fuchsia-400/30 text-fuchsia-200 px-2.5 py-1 rounded-full font-medium" x-text="skill"></span>
                            </template>
                            <span x-show="skills.length > 6" class="text-xs text-purple-400 px-2.5 py-1" x-text="'+' + (skills.length - 6) + ' more'"></span>
                        </div>
                    </div>
                </div>

                {{-- Description preview --}}
                <div class="bg-white/5 rounded-2xl p-5 border border-white/10 mb-4 relative">
                    <p class="text-xs text-purple-300 font-semibold uppercase tracking-wider mb-2">?� Job Overview</p>
                    <p class="text-purple-100 text-sm leading-relaxed line-clamp-4" x-text="description"></p>
                </div>

                {{-- CTA to scroll to form --}}
                <div class="flex items-center justify-between relative">
                    <p class="text-purple-300 text-xs">Everything looks good? Fill in location & details below, then publish.</p>
                    <button type="button" @click="scrollToForm()"
                        class="flex items-center gap-2 bg-fuchsia-600 hover:bg-fuchsia-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-fuchsia-500/20">
                        Edit & Publish
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            {{-- ── FULL EDITABLE FORM ───────────────────────────── --}}
            <div id="edit-form">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-px flex-1 bg-white/10"></div>
                    <span class="text-xs text-purple-400 font-semibold uppercase tracking-widest">Review & Edit Details</span>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>

                <form action="{{ route('employer.jobs.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- ── SECTION 1: BASIC INFO ──────────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">1</span>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Job Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" x-model="jobTitle" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent bg-gray-50"
                                    value="{{ old('title') }}" />
                                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Location <span class="text-red-500">*</span></label>
                                <input type="text" name="location" value="{{ old('location') }}" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent bg-gray-50"
                                    placeholder="e.g. Bangalore, India" />
                                @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Job Type <span class="text-red-500">*</span></label>
                                <select name="job_type" x-model="jobType" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent bg-gray-50">
                                    <option value="full-time">Full-time</option>
                                    <option value="part-time">Part-time</option>
                                    <option value="contract">Contract</option>
                                    <option value="internship">Internship</option>
                                    <option value="remote">Remote</option>
                                </select>
                                @error('job_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Experience Level <span class="text-red-500">*</span></label>
                                <select name="experience_level" x-model="experienceLevel" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent bg-gray-50">
                                    <option value="entry">Entry Level</option>
                                    <option value="mid">Mid Level</option>
                                    <option value="senior">Senior Level</option>
                                    <option value="lead">Lead / Principal</option>
                                </select>
                                @error('experience_level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Expires On <span class="text-red-500">*</span></label>
                                <input type="date" name="expires_at" value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d')) }}"
                                    required min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent bg-gray-50" />
                                @error('expires_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 2: SALARY ──────────────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">2</span>
                            Salary Range
                            <span class="ml-auto text-xs text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full font-semibold"> AI Suggested</span>
                        </h3>
                        <p class="text-xs text-fuchsia-600 mb-4 font-medium ml-9" x-text="salaryNote"></p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Min (&#8377;/year)</label>
                                <input type="number" name="salary_min" x-model="salaryMin" min="0" step="100000"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50" placeholder="800000" />
                                @error('salary_min')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Max (&#8377;/year)</label>
                                <input type="number" name="salary_max" x-model="salaryMax" min="0" step="100000"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50" placeholder="1500000" />
                                @error('salary_max')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 3: DESCRIPTION ─────────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">3</span>
                            Job Description <span class="text-red-500">*</span>
                            <span class="ml-auto text-xs text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full font-semibold"> AI Written</span>
                        </h3>
                        <textarea name="description" rows="8" required x-model="description"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50 resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- ── SECTION 4: RESPONSIBILITIES ────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">4</span>
                            Responsibilities
                            <span class="ml-auto text-xs text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full font-semibold"> AI Written</span>
                        </h3>
                        <textarea name="responsibilities" rows="7" x-model="responsibilities"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50 resize-none">{{ old('responsibilities') }}</textarea>
                        @error('responsibilities')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- ── SECTION 5: QUALIFICATIONS ──────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">5</span>
                            Qualifications
                            <span class="ml-auto text-xs text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full font-semibold"> AI Written</span>
                        </h3>
                        <textarea name="qualifications" rows="6" x-model="qualifications"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50 resize-none">{{ old('qualifications') }}</textarea>
                        @error('qualifications')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- ── SECTION 6: SKILLS ──────────────────────── --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">6</span>
                            Required Skills
                            <span class="ml-auto text-xs text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full font-semibold"> AI Filled</span>
                        </h3>
                        <p class="text-xs text-gray-400 mb-3 ml-9">Click &times; to remove. Type + Enter to add more.</p>
                        <div class="flex flex-wrap gap-2 min-h-[52px] p-3 border border-gray-200 rounded-xl bg-gray-50 mb-3">
                            <template x-for="(skill, index) in skills" :key="index">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-fuchsia-100 text-fuchsia-700 border border-fuchsia-200 rounded-full text-xs font-semibold">
                                    <span x-text="skill"></span>
                                    <button type="button" @click="removeSkill(index)" class="hover:text-red-500 transition-colors">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                    <input type="hidden" name="required_skills[]" :value="skill" />
                                </span>
                            </template>
                        </div>
                        <input type="text" x-model="skillInput"
                            @keydown.enter.prevent="addSkillFromInput()"
                            @keydown.188.prevent="addSkillFromInput()"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50"
                            placeholder="Add more skills &mdash; press Enter" />
                    </div>

                    {{-- -- SECTION 7: APPLICATION DATES ------------------------ --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">7</span>
                            Application Window <span class="text-red-500">*</span>
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Applications Open <span class="text-red-500">*</span></label>
                                <input type="date" name="open_date" x-model="openDate" required
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50" />
                                @error('open_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Applications Close <span class="text-red-500">*</span></label>
                                <input type="date" name="close_date" x-model="closeDate" required
                                    :min="openDate || '{{ now()->addDay()->format('Y-m-d') }}'"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-gray-50" />
                                @error('close_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- -- SECTION 8: HIRING ROUNDS ---------------------------- --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">8</span>
                            Hiring Rounds
                            <button type="button" @click="suggestRounds()"
                                :disabled="roundsLoading || !jobTitle.trim()"
                                class="ml-auto flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full transition-all"
                                style="background:#2D6CDF;color:#fff"
                                :class="roundsLoading && 'opacity-60 cursor-not-allowed'">
                                <svg class="w-3.5 h-3.5" :class="roundsLoading && 'animate-spin'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                <span x-text="roundsLoading ? 'Suggesting...' : 'AI Suggest Rounds'"></span>
                            </button>
                        </h3>
                        <p class="text-xs text-gray-400 mb-5 ml-9">Select the rounds candidates must clear. AI suggests based on the job role.</p>

                        <div class="space-y-3">
                            <template x-for="(round, idx) in rounds" :key="round.type">
                                <div class="rounded-xl border-2 transition-all"
                                     :class="round.enabled ? 'border-fuchsia-500 bg-fuchsia-50' : 'border-gray-700 bg-gray-800'">

                                    {{-- Round Toggle Header --}}
                                    <div class="flex items-center gap-3 p-4 cursor-pointer select-none" @click="round.enabled = !round.enabled">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                                             :class="round.enabled ? 'bg-fuchsia-500 text-white' : 'bg-gray-600 text-gray-200'">
                                            <span x-text="round.icon"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-sm" :class="round.enabled ? 'text-gray-900' : 'text-gray-100'" x-text="round.name"></div>
                                            <div class="text-xs mt-0.5" :class="round.enabled ? 'text-gray-500' : 'text-gray-400'" x-text="round.desc"></div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="relative inline-flex h-7 w-13 items-center rounded-full border-2 transition-colors duration-200"
                                                 :class="round.enabled ? 'bg-fuchsia-500 border-fuchsia-600' : 'bg-gray-600 border-gray-500'"
                                                 style="width:3.25rem">
                                                <span class="inline-block h-5 w-5 transform rounded-full shadow-md transition-transform duration-200"
                                                      :class="round.enabled ? 'bg-white translate-x-6' : 'bg-gray-200 translate-x-1'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Round Date Config (shown when enabled) --}}
                                    <div x-show="round.enabled"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-1"
                                         class="px-4 pb-4">
                                        <div class="border-t border-fuchsia-200 pt-4">
                                            <div class="grid grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Test / Interview Date <span class="text-red-500">*</span></label>
                                                    <input type="date" x-model="round.testDate"
                                                        :min="closeDate || '{{ now()->addDay()->format('Y-m-d') }}'"
                                                        @change="computeEvalDate(round)"
                                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-white" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Evaluation Period</label>
                                                    <select x-model="round.evalDays" @change="computeEvalDate(round)"
                                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-fuchsia-500 bg-white">
                                                        <option value="5">5 days after test</option>
                                                        <option value="10">10 days after test</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div x-show="round.evalDate" class="flex items-center gap-2 text-xs bg-fuchsia-100 text-fuchsia-700 rounded-lg px-3 py-2">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Results &amp; emails sent to candidates on: <strong x-text="round.evalDate" class="ml-1"></strong>
                                            </div>
                                            {{-- Hidden inputs submitted with form --}}
                                            <template x-if="round.enabled">
                                                <span>
                                                    <input type="hidden" :name="'rounds['+idx+'][type]'" :value="round.type">
                                                    <input type="hidden" :name="'rounds['+idx+'][name]'" :value="round.name">
                                                    <input type="hidden" :name="'rounds['+idx+'][test_date]'" :value="round.testDate">
                                                    <input type="hidden" :name="'rounds['+idx+'][eval_days]'" :value="round.evalDays">
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Round order note --}}
                        <p class="text-xs text-gray-400 mt-4">&#9432; Candidates are notified of all rounds when they apply. Emails &amp; in-app notifications are sent automatically after each evaluation date.</p>
                    </div>

                    {{-- -- SECTION 9: STATUS ------------------------------------ --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-fuchsia-100 text-fuchsia-600 text-xs font-black">9</span>
                            Publication Status
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all has-[:checked]:border-fuchsia-500 has-[:checked]:bg-fuchsia-50 border-gray-200 hover:border-fuchsia-300">
                                <input type="radio" name="status" value="published" class="accent-fuchsia-600">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Publish Now</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Visible to candidates immediately</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all has-[:checked]:border-gray-500 has-[:checked]:bg-gray-50 border-gray-200 hover:border-gray-400">
                                <input type="radio" name="status" value="draft" checked class="accent-gray-600">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Save as Draft</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Review and publish later</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ── SUBMIT ─────────────────────────────────── --}}
                    <div class="flex gap-3 pb-10">
                        <button type="submit"
                            class="flex-1 py-4 bg-gradient-to-r from-fuchsia-600 to-purple-600 text-white font-black text-sm rounded-2xl shadow-lg shadow-fuchsia-500/25 hover:from-fuchsia-500 hover:to-purple-500 hover:shadow-fuchsia-400/30 transition-all">
                            � Create Job Posting
                        </button>
                        <a href="{{ route('employer.jobs.index') }}"
                            class="px-6 py-4 bg-white/10 text-purple-200 font-semibold text-sm rounded-2xl hover:bg-white/20 transition-colors border border-white/10">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── AI LOADING OVERLAY ──────────────────────────────────────── --}}
<div x-data x-show="$store.aiOverlay && $store.aiOverlay.show" x-cloak
    class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50">
    <div class="bg-gradient-to-br from-fuchsia-900 to-purple-900 border border-fuchsia-500/30 rounded-3xl shadow-2xl p-10 max-w-sm w-full mx-4 text-center">
        <div class="flex justify-center mb-5">
            <div class="relative">
                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-fuchsia-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-9 h-9 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="absolute inset-0 rounded-full bg-fuchsia-500/30 animate-ping"></div>
            </div>
        </div>
        <h3 class="text-xl font-black text-white mb-2">S.C.O.U.T&trade; AI</h3>
        <p class="text-purple-200 text-sm mb-1">Writing your complete job posting...</p>
        <p class="text-purple-400 text-xs mb-6">Description &middot; Responsibilities &middot; Qualifications &middot; Skills &middot; Salary</p>
        <div class="flex justify-center gap-1.5">
            <div class="w-2.5 h-2.5 bg-fuchsia-400 rounded-full animate-bounce" style="animation-delay:0s"></div>
            <div class="w-2.5 h-2.5 bg-fuchsia-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
            <div class="w-2.5 h-2.5 bg-fuchsia-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
        </div>
    </div>
</div>

<script>
// Register jobCreator as a window function so Alpine can resolve it
// regardless of whether alpine:init has already fired.
window.jobCreator = function() {
    return {
        jobTitle: '{{ old('title', '') }}',
        jobType: 'full-time',
        experienceLevel: 'mid',
        description: @json(old('description', '')),
        responsibilities: @json(old('responsibilities', '')),
        qualifications: @json(old('qualifications', '')),
        salaryMin: '{{ old('salary_min', '') }}',
        salaryMax: '{{ old('salary_max', '') }}',
        salaryNote: '',
        skills: [],
        skillInput: '',
        aiLoading: false,
        aiSuccess: {{ old('description') ? 'true' : 'false' }},
        aiError: null,
        // Application dates
        openDate: '{{ old('open_date', now()->format('Y-m-d')) }}',
        closeDate: '{{ old('close_date', now()->addDays(30)->format('Y-m-d')) }}',
        // Hiring rounds
        roundsLoading: false,
        rounds: [
            { type: 'info_test',    name: 'Company Info Test',       icon: '??', desc: 'Basic questions about your company culture, values & mission',    enabled: false, testDate: '', evalDays: '5', evalDate: '' },
            { type: 'aptitude',     name: 'Aptitude Test',           icon: '??', desc: 'Logical reasoning, numerical & verbal ability assessment',         enabled: false, testDate: '', evalDays: '5', evalDate: '' },
            { type: 'technical',    name: 'Technical Assessment',     icon: '??', desc: 'Role-specific coding challenges & technical problem solving',      enabled: false, testDate: '', evalDays: '5', evalDate: '' },
            { type: 'practical',    name: 'Non-Technical / Practical',icon: '??', desc: 'Case studies, situational judgment & domain-specific tasks',       enabled: false, testDate: '', evalDays: '5', evalDate: '' },
            { type: 'hr_interview', name: 'HR Interview',             icon: '??', desc: 'Culture fit, behavioural interview & compensation discussion',     enabled: false, testDate: '', evalDays: '5', evalDate: '' },
        ],

        formatSalary(val) {
            if (!val) return '�';
            const n = parseInt(val);
            if (n >= 100000) return '?' + (n / 100000).toFixed(1).replace('.0','') + 'L';
            return '?' + n.toLocaleString('en-IN');
        },

        scrollToForm() {
            document.getElementById('edit-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        resetAI() {
            this.aiSuccess = false;
            this.aiError = null;
            this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
        },

        skipAI() {
            this.aiError = null;
            this.aiSuccess = true;
            this.$nextTick(() => {
                document.getElementById('edit-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },

        async generateWithAI() {
            if (!this.jobTitle.trim()) return;
            this.aiLoading = true;
            this.aiError = null;
            Alpine.store('aiOverlay').show = true;

            try {
                const res = await fetch('{{ route('employer.jobs.ai-generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title: this.jobTitle,
                        experience_level: this.experienceLevel || 'mid',
                        job_type: this.jobType || 'full-time',
                    }),
                });

                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message || 'AI generation failed.');

                const d = json.data;
                this.description      = d.description     || '';
                this.responsibilities = d.responsibilities || '';
                this.qualifications   = d.qualifications   || '';
                this.salaryMin        = d.salary_min       || '';
                this.salaryMax        = d.salary_max       || '';
                this.salaryNote       = d.salary_note      || '';

                if (Array.isArray(d.required_skills)) {
                    this.skills = d.required_skills.map(s => s.trim()).filter(Boolean);
                }

                this.aiSuccess = true;
                this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));

                // Auto-suggest rounds after job content is generated
                this.suggestRounds();

            } catch (err) {
                this.aiError = err.message;
            } finally {
                this.aiLoading = false;
                Alpine.store('aiOverlay').show = false;
            }
        },

        addSkillFromInput() {
            const val = this.skillInput.trim().replace(/,$/, '');
            if (val && !this.skills.includes(val)) this.skills.push(val);
            this.skillInput = '';
        },

        removeSkill(index) {
            this.skills.splice(index, 1);
        },

        computeEvalDate(round) {
            if (!round.testDate) { round.evalDate = ''; return; }
            const d = new Date(round.testDate);
            d.setDate(d.getDate() + parseInt(round.evalDays));
            round.evalDate = d.toISOString().split('T')[0];
        },

        async suggestRounds() {
            if (!this.jobTitle.trim()) return;
            this.roundsLoading = true;
            try {
                const res = await fetch('{{ route('employer.jobs.ai-suggest-rounds') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: this.jobTitle, experience_level: this.experienceLevel }),
                });
                const json = await res.json();
                if (json.success && Array.isArray(json.data?.suggested)) {
                    this.rounds.forEach(r => { r.enabled = json.data.suggested.includes(r.type); });
                }
            } catch (e) { /* silent */ } finally {
                this.roundsLoading = false;
            }
        },
    };
};

document.addEventListener('alpine:init', () => {
    Alpine.store('aiOverlay', { show: false });
});
</script>
@endsection
