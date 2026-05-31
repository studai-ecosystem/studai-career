{{-- Resume List Page --}}
@extends('layouts.dashboard')

@section('title', 'Resume Builder')
@section('page-title', 'Resume Builder')
@section('page-description', 'Create ATS-optimized resumes with AI')

@push('styles')
<style>
@keyframes heroGrad { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
@keyframes floatBubble { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-18px) scale(1.08)} }
@keyframes cardPop { from{opacity:0;transform:translateY(20px) scale(.96)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes sparkle { 0%,100%{opacity:0;transform:scale(0) rotate(0deg)} 50%{opacity:1;transform:scale(1) rotate(180deg)} }
@keyframes badgePing { 0%{transform:scale(1);opacity:1} 100%{transform:scale(1.8);opacity:0} }
@keyframes shimmerSlide { from{background-position:-200% center} to{background-position:200% center} }

.resume-hero {
    background: #2D6CDF;
    background-size: 300% 300%;
    animation: heroGrad 8s ease infinite;
}
.hero-bubble {
    animation: floatBubble 4s ease-in-out infinite;
}
.hero-bubble:nth-child(2) { animation-delay: 1.3s; }
.hero-bubble:nth-child(3) { animation-delay: 2.6s; }
.resume-card {
    animation: cardPop .4s cubic-bezier(0.34,1.56,0.64,1) both;
}
.resume-card:nth-child(1){animation-delay:.05s}
.resume-card:nth-child(2){animation-delay:.10s}
.resume-card:nth-child(3){animation-delay:.15s}
.resume-card:nth-child(4){animation-delay:.20s}
.resume-card:nth-child(5){animation-delay:.25s}
.resume-card:nth-child(6){animation-delay:.30s}
.sparkle-1,.sparkle-2,.sparkle-3 {
    animation: sparkle 2.5s ease-in-out infinite;
}
.sparkle-2 { animation-delay:.8s; }
.sparkle-3 { animation-delay:1.6s; }
.ai-badge::before {
    content:'';
    position:absolute;
    inset:0;
    border-radius:inherit;
    animation: badgePing 2s cubic-bezier(0,0,.2,1) infinite;
    background:rgba(20, 71, 186,.4);
}
.create-card {
    background: #EBF2FF;
    border: 2px dashed #BFCFEE;
    transition: all .25s cubic-bezier(0.34,1.56,0.64,1);
}
.create-card:hover {
    border-color: #2D6CDF;
    background: #EBF2FF;
    transform: translateY(-4px) scale(1.01);
    box-shadow: none;
}
.stat-pill {
    background: rgba(20, 71, 186,.08);
    border: 1px solid rgba(20, 71, 186,.12);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    color: #2D6CDF;
    font-weight: 600;
}
.score-excellent { background:#EDFAF2; color:#1E8E3E; }
.score-good      { background:#EBF2FF; color:#1B57C4; }
.score-fair      { background:#FFF8EC; color:#E37400; }
.score-poor      { background:#FEF2F2; color:#2D6CDF; }
</style>
@endpush

@section('content')
{{-- HERO BANNER --}}
<div class="resume-hero relative overflow-hidden rounded-2xl p-7 mb-8 text-white" style="box-shadow: none">
    {{-- floating bubbles --}}
    <div class="hero-bubble absolute -right-6 -top-6 w-40 h-40 rounded-full opacity-20" style="background:rgba(255,255,255,.25)"></div>
    <div class="hero-bubble absolute right-24 -bottom-8 w-24 h-24 rounded-full opacity-15" style="background:rgba(255,255,255,.2)"></div>
    <div class="hero-bubble absolute right-48 top-2 w-12 h-12 rounded-full opacity-20" style="background:rgba(255,255,255,.3)"></div>
    {{-- sparkles --}}
    <div class="sparkle-1 absolute top-4 left-[45%] text-white/60 text-lg">&#10022;</div>
    <div class="sparkle-2 absolute bottom-4 left-[55%] text-white/40 text-sm">&#10022;</div>
    <div class="sparkle-3 absolute top-8 right-[30%] text-white/50 text-xs">&#10022;</div>

    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="flex items-center gap-5">
            {{-- Animated icon --}}
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 relative" style="background:rgba(255,255,255,.2);backdrop-filter:blur(8px);border:2px solid rgba(255,255,255,.3)">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="absolute -top-1 -right-1 text-xs">&#10024;</span>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-2xl font-bold">Resume Builder</h1>
                    <span class="ai-badge relative px-2.5 py-0.5 rounded-full text-xs font-bold" style="background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.4)">AI Powered</span>
                </div>
                <p class="text-purple-100 text-sm">ATS-optimized resumes that get you hired &mdash; crafted with AI</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-white/70">
                    <span>&#9889; Smart suggestions</span>
                    <span>&bull;</span>
                    <span>&#128202; ATS scoring</span>
                    <span>&bull;</span>
                    <span>&#128196; PDF export</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('resume.create') }}" class="group inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-200 hover:-translate-y-0.5"
               style="background:white;color:#2D6CDF;box-shadow: none">
                <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Resume
            </a>
        </div>
    </div>
</div>

<div class="space-y-6">

    @if($resumes->isEmpty())
        {{-- Empty State --}}
        <div class="text-center py-20 bg-white rounded-2xl border border-purple-100" style="box-shadow: none">
            <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 resume-card" style="background:#EBF2FF;box-shadow: none">
                <svg class="w-12 h-12" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Create Your First Resume</h2>
            <p class="text-gray-500 max-w-md mx-auto mb-8 text-sm leading-relaxed">
                Build an ATS-optimized resume with AI assistance. Our smart builder helps you craft professional resumes that get noticed by recruiters.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('resume.create') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-sm text-white transition-all hover:-translate-y-0.5" style="background:#2D6CDF;box-shadow: none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    Create with AI
                </a>
                <a href="#" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-semibold text-sm transition-all hover:-translate-y-0.5" style="background:#EBF2FF;color:#2D6CDF;border:1.5px solid #BFCFEE">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Existing
                </a>
            </div>
        </div>
    @else
        {{-- Resume Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($resumes as $resume)
                @php
                    $tpl     = $resume->template;
                    $tColors = $tpl ? (is_array($tpl->color_scheme) ? $tpl->color_scheme : (json_decode($tpl->color_scheme, true) ?? [])) : [];
                    $tPrimary   = $tColors['primary']   ?? '#2D6CDF';
                    $tSecondary = $tColors['secondary'] ?? '#1B57C4';
                    $tAccent    = $tColors['accent']    ?? '#BFCFEE';
                    $tLayout    = $tpl ? (is_array($tpl->layout_config) ? $tpl->layout_config : (json_decode($tpl->layout_config, true) ?? [])) : [];
                    $tCols      = $tLayout['columns'] ?? 1;
                    $atsClass   = match($resume->ats_score) { 'excellent'=>'score-excellent','good'=>'score-good','fair'=>'score-fair',default=>'score-poor' };
                    $atsPct     = match($resume->ats_score) { 'excellent'=>95,'good'=>75,'fair'=>50,default=>25 };
                @endphp
                <div class="resume-card group rounded-2xl border overflow-hidden transition-colors duration-150" style="background:var(--color-surface);border-color:var(--color-border)" onmouseover="this.style.borderColor='var(--color-border-strong)'" onmouseout="this.style.borderColor='var(--color-border)'">

                    {{-- Preview Thumbnail --}}
                    <div class="h-40 relative overflow-hidden" style="background:#EBF2FF">
                        {{-- Mini resume preview --}}
                        <div class="w-full h-full flex flex-col text-[5px] leading-tight select-none pointer-events-none px-2 py-1.5">
                            <div class="px-2 py-1.5 -mx-2 -mt-1.5 mb-1 flex-shrink-0" style="background:{{ $tPrimary }}">
                                <div class="w-14 h-1.5 rounded mb-0.5" style="background:rgba(255,255,255,.9)"></div>
                                <div class="w-9 h-1 rounded" style="background:rgba(255,255,255,.6)"></div>
                            </div>
                            @if($tCols == 2)
                            <div class="flex flex-1 overflow-hidden">
                                <div class="w-2/5 flex-shrink-0 p-1" style="background:{{ $tSecondary }}22">
                                    <div class="w-full h-0.5 rounded mb-1" style="background:{{ $tAccent }}"></div>
                                    @for($i=0;$i<5;$i++)<div class="w-full h-0.5 rounded mb-0.5" style="background:#E2E2E0"></div>@endfor
                                </div>
                                <div class="flex-1 p-1">
                                    @for($s=0;$s<3;$s++)<div class="w-1/2 h-0.5 rounded mb-0.5" style="background:{{ $tPrimary }}"></div>@for($i=0;$i<3;$i++)<div class="w-full h-0.5 rounded mb-0.5" style="background:#E2E2E0"></div>@endfor<div class="mb-1"></div>@endfor
                                </div>
                            </div>
                            @else
                            <div class="flex-1 pt-1">
                                @for($s=0;$s<4;$s++)<div class="w-1/3 h-0.5 rounded mb-1" style="background:{{ $tPrimary }}"></div>@for($i=0;$i<3;$i++)<div class="w-full h-0.5 rounded mb-0.5" style="background:#E2E2E0"></div>@endfor<div class="mb-1.5"></div>@endfor
                            </div>
                            @endif
                        </div>

                        {{-- Badges --}}
                        @if($resume->is_default)
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold text-white" style="background:#1E8E3E">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Default
                                </span>
                            </div>
                        @endif

                        {{-- Hover Quick Actions --}}
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center gap-2" style="background:rgba(15,10,40,.5);backdrop-filter:blur(2px)">
                            <a href="{{ route('resume.edit', $resume) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-800 transition-all hover:scale-105" style="background:white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <a href="{{ route('resume.preview', $resume) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105" style="background:#2D6CDF">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Preview
                            </a>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 truncate mb-0.5 text-base">{{ $resume->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ $resume->full_name }}</p>

                        {{-- ATS Score --}}
                        @if($resume->ats_score)
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">ATS Score</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $atsClass }}">{{ ucfirst($resume->ats_score) }}</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full mb-4" style="background:#EBF2FF">
                                <div class="h-1.5 rounded-full transition-all duration-700" style="width:{{ $atsPct }}%;background:#2D6CDF"></div>
                            </div>
                        @endif

                        {{-- Completion --}}
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Completion</span>
                            <span class="text-xs font-bold" style="color:#2D6CDF">{{ $resume->getCompletionPercentage() }}%</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full mb-4" style="background:#EBF2FF">
                            <div class="h-1.5 rounded-full transition-all duration-700" style="width:{{ $resume->getCompletionPercentage() }}%;background:#2D6CDF"></div>
                        </div>

                        {{-- Stats Row --}}
                        <div class="flex items-center gap-3 mb-4">
                            <span class="stat-pill flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ $resume->view_count }}
                            </span>
                            <span class="stat-pill flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                {{ $resume->download_count }}
                            </span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $resume->updated_at->diffForHumans() }}</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2" x-data="{ open: false }">
                            <a href="{{ route('resume.edit', $resume) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5" style="background:#2D6CDF;box-shadow: none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <a href="{{ route('resume.preview', $resume) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5" style="background:#EBF2FF;color:#2D6CDF;border:1.5px solid #BFCFEE">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Preview
                            </a>
                            <div class="relative">
                                <button @click="open = !open" class="p-2 rounded-xl transition-all hover:bg-gray-100" style="border:1.5px solid #EBF2FF;color:#737373">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl border py-1" style="z-index:50;box-shadow: none;border-color:#EBF2FF">
                                    <a href="{{ route('resume.export.pdf', $resume) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download PDF
                                    </a>
                                    <a href="{{ route('resume.export.docx', $resume) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Download DOCX
                                    </a>
                                    <form action="{{ route('resume.duplicate', $resume) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            Duplicate
                                        </button>
                                    </form>
                                    <form action="{{ route('resume.set-default', $resume) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            Set as Default
                                        </button>
                                    </form>
                                    <div class="border-t my-1" style="border-color:#EBF2FF"></div>
                                    <form action="{{ route('resume.destroy', $resume) }}" method="POST"
                                          onsubmit="return confirm('Delete this resume?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Create New Card --}}
            <a href="{{ route('resume.create') }}" class="create-card resume-card group flex flex-col items-center justify-center min-h-[320px] rounded-2xl cursor-pointer">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3" style="background:#EBF2FF;box-shadow: none">
                    <svg class="w-8 h-8 transition-transform duration-300 group-hover:rotate-90" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <span class="text-sm font-bold mb-1" style="color:#2D6CDF">Create New Resume</span>
                <span class="text-xs" style="color:#2D6CDF">AI-powered builder</span>
            </a>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $resumes->links() }}
        </div>
    @endif
</div>
@endsection
