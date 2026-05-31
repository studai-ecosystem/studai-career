<x-layouts.dashboard :title="'AI Credits'">

@push('styles')
<style>
.credit-card-hover { transition: transform .2s, box-shadow .2s; }
.credit-card-hover:hover { transform: translateY(-3px); box-shadow: none; }
@keyframes bar-fill { from { width: 0; } }
.bar-anim { animation: bar-fill .8s cubic-bezier(.22,1,.36,1) both; }
</style>
@endpush

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('dashboard') }}" class="text-sm text-ink-tertiary hover:text-ink-primary transition-colors">&larr; Dashboard</a>
        </div>
        <h1 class="text-2xl font-bold text-ink-primary">AI Credits</h1>
        <p class="text-sm text-ink-secondary mt-1">Track how your AI credits are used across features</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        {{-- Credits Remaining --}}
        <div class="rounded-2xl p-5 credit-card-hover" style="background:#1B57C4;box-shadow: none">
            <p class="text-xs font-semibold uppercase tracking-wider text-white/70">Credits Left This Month</p>
            <p class="text-4xl font-bold text-white mt-2">
                {{ $creditsLeft === -1 ? '∞' : $creditsLeft }}
            </p>
            @if($creditsLimit > 0)
                <div class="mt-3 w-full rounded-full h-1.5" style="background:rgba(255,255,255,.25)">
                    @php $pct = $creditsLimit > 0 ? min(100, ($creditsUsed / $creditsLimit) * 100) : 0; @endphp
                    <div class="h-1.5 rounded-full bar-anim" style="width:{{ $pct }}%;background:rgba(255,255,255,.9)"></div>
                </div>
                <p class="text-xs text-white/70 mt-1">{{ $creditsUsed }} used of {{ $creditsLimit }} this month</p>
            @endif
        </div>

        {{-- Used This Month --}}
        <div class="rounded-2xl p-5 credit-card-hover" style="background:#EBF2FF;border:1.5px solid #2D6CDF">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#1B57C4">Used This Month</p>
            <p class="text-4xl font-bold mt-2" style="color:#0C2E72">{{ $thisMonth }}</p>
            <p class="text-xs mt-1" style="color:#1B57C4">credits consumed</p>
        </div>

        {{-- All Time --}}
        <div class="rounded-2xl p-5 credit-card-hover" style="background:#EBF2FF;border:1.5px solid #2D6CDF">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#0C2E72">All Time Total</p>
            <p class="text-4xl font-bold mt-2" style="color:#0C2E72">{{ $totalUsed }}</p>
            <p class="text-xs mt-1" style="color:#1B57C4">credits ever used</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- History Table --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl overflow-hidden" style="border:1.5px solid #BFCFEE;box-shadow: none">
                <div class="px-6 py-4 flex items-center justify-between" style="background:#1B57C4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h2 class="text-sm font-bold text-white">Usage History</h2>
                    </div>
                    <span class="text-xs text-white/70">{{ $logs->total() }} total entries</span>
                </div>

                @if($logs->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background:#EBF2FF">
                            <svg class="w-8 h-8" style="color:#2D6CDF" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-ink-primary mb-1">No AI usage yet</h3>
                        <p class="text-sm text-ink-secondary mb-5">Generate a cover letter or use any AI feature to see your history here.</p>
                        <a href="{{ route('jobs.search') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white rounded-xl" style="background:#2D6CDF">
                            Browse Jobs &rarr;
                        </a>
                    </div>
                @else
                    <div class="divide-y" style="divide-color:#EBF2FF">
                        @foreach($logs as $log)
                            @php $color = $log->action_color; @endphp
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-indigo-50/40 transition-colors">
                                <div class="flex items-center gap-4 min-w-0">
                                    {{-- Icon bubble --}}
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background:{{ $color['bg'] }}">
                                        @if($log->action === 'cover_letter') 📝
                                        @elseif($log->action === 'resume_review') 📄
                                        @elseif($log->action === 'interview_prep') 🎤
                                        @elseif($log->action === 'ai_apply') ⚡
                                        @elseif($log->action === 'skill_analysis') 🧠
                                        @elseif($log->action === 'career_coach') 🎯
                                        @elseif($log->action === 'salary_insight') 💰
                                        @else ✨
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-ink-primary truncate">{{ $log->description }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:{{ $color['bg'] }};color:{{ $color['text'] }}">
                                                {{ $log->action_label }}
                                            </span>
                                            <span class="text-xs text-ink-tertiary">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Credits badge --}}
                                <div class="flex-shrink-0 ml-4 text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-bold" style="background:#FFF8EC;color:#E37400">
                                        &minus;{{ $log->credits_used }}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </span>
                                    <p class="text-xs text-ink-tertiary mt-1">{{ $log->created_at->format('d M, H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="px-6 py-4" style="border-top:1px solid #EBF2FF">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Sidebar: Breakdown by Feature --}}
        <div class="space-y-5">
            <div class="rounded-2xl overflow-hidden" style="border:1.5px solid #2D6CDF;box-shadow: none">
                <div class="px-5 py-4" style="background:#1B57C4">
                    <h3 class="text-sm font-bold text-white">Credits by Feature</h3>
                    <p class="text-xs text-white/70 mt-0.5">How you've used your AI</p>
                </div>
                <div class="p-5 space-y-4" style="background:#EBF2FF">
                    @forelse($byAction as $item)
                        @php
                            $color = \App\Models\AICreditLog::$actionColors[$item->action] ?? ['bg'=>'#F0F0EE','text'=>'#3D3D3D'];
                            $label = \App\Models\AICreditLog::$actionLabels[$item->action] ?? ucfirst(str_replace('_',' ',$item->action));
                            $pct = $totalUsed > 0 ? round(($item->total / $totalUsed) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium" style="color:{{ $color['text'] }}">{{ $label }}</span>
                                <span class="text-xs font-bold text-ink-primary">{{ $item->total }} cr &middot; {{ $item->count }}x</span>
                            </div>
                            <div class="w-full rounded-full h-1.5" style="background:{{ $color['bg'] }}">
                                <div class="h-1.5 rounded-full bar-anim" style="width:{{ $pct }}%;background:{{ $color['text'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink-tertiary text-center py-4">No usage yet</p>
                    @endforelse
                </div>
            </div>

            {{-- Where to use AI --}}
            <div class="rounded-2xl p-5" style="background:#EBF2FF;border:1.5px solid #2D6CDF">
                <h3 class="text-sm font-bold mb-3" style="color:#0C2E72">Where to Use AI Credits</h3>
                <div class="space-y-2.5">
                    @foreach([
                        ['icon'=>'📝','label'=>'Cover Letter','cost'=>1,'url'=>route('jobs.search')],
                        ['icon'=>'⚡','label'=>'One-Click Apply','cost'=>2,'url'=>route('jobs.search')],
                        ['icon'=>'🎤','label'=>'Interview Prep','cost'=>50,'url'=>route('interview.index')],
                        ['icon'=>'🧠','label'=>'Skill Analysis','cost'=>2,'url'=>route('dashboard')],
                    ] as $feat)
                        <a href="{{ $feat['url'] }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-white/60 transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">{{ $feat['icon'] }}</span>
                                <span class="text-sm font-medium text-ink-primary group-hover:text-indigo-700">{{ $feat['label'] }}</span>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:#FFF8EC;color:#E37400">{{ $feat['cost'] }} cr</span>
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('pricing') }}" class="mt-4 block text-center py-2.5 rounded-xl text-sm font-bold text-white" style="background:#2D6CDF">
                    Get More Credits &rarr;
                </a>
            </div>
        </div>
    </div>

</x-layouts.dashboard>
