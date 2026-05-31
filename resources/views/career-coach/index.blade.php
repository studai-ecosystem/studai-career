@extends('layouts.dashboard')

@section('title', 'AI Career Coach')
@section('page-title', 'AI Career Coach')
@section('page-description', 'Your personal AI-powered career advisor')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:#0C2E72;">
        <div class="absolute inset-0" style="background-image:rgba(255,255,255,.15);"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,.2);color:#fff;">AI POWERED</span>
                <h1 class="text-2xl font-bold mt-2 text-white">Career Coach</h1>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,.85);">Your personal AI-powered career advisor &mdash; always on.</p>
            </div>
            <button onclick="startSession('general_advice')" class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all shadow-sm text-sm flex-shrink-0" style="background:#fff;color:#1B57C4;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Start Coaching Session
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Total Sessions</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EDFAF2;">
                <svg class="w-5 h-5" style="color:#1E8E3E;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['active_goals'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Active Goals</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FFF8EC;">
                <svg class="w-5 h-5" style="color:#E37400;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['completed_goals'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Completed Goals</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#EBF2FF;">
                <svg class="w-5 h-5" style="color:#1B57C4;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['checkins_completed'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Check-ins Done</div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">

            {{-- Session Types --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Start a Coaching Session</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php $sessionTypes = [
                        'general_advice'    => ['icon'=>'&#x1F4AC;','label'=>'General Advice',   'desc'=>'Ask anything',          'bg'=>'#EBF2FF','border'=>'#BFCFEE','hbg'=>'#EBF2FF'],
                        'career_planning'   => ['icon'=>'&#x1F5FA;','label'=>'Career Planning',   'desc'=>'Map your path',         'bg'=>'#EBF2FF','border'=>'#BFCFEE','hbg'=>'#BFCFEE'],
                        'skill_development' => ['icon'=>'&#x1F4DA;','label'=>'Skills',             'desc'=>'Level up',              'bg'=>'#EDFAF2','border'=>'#A3D9B4','hbg'=>'#A3D9B4'],
                        'interview_prep'    => ['icon'=>'&#x1F3A4;','label'=>'Interview Prep',     'desc'=>'Practice &amp; prepare','bg'=>'#FEF2F2','border'=>'#FCA5A5','hbg'=>'#FCA5A5'],
                    ]; @endphp
                    @foreach($sessionTypes as $type => $info)
                    <button onclick="startSession('{{ $type }}')"
                            class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 transition-all text-center"
                            style="background:{{ $info['bg'] }};border-color:{{ $info['border'] }};"
                            onmouseover="this.style.background='{{ $info['hbg'] }}'"
                            onmouseout="this.style.background='{{ $info['bg'] }}'">
                        <span class="text-2xl">{!! $info['icon'] !!}</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $info['label'] }}</span>
                        <span class="text-xs text-gray-500">{!! $info['desc'] !!}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Active Goals --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Active Goals</h2>
                    <a href="{{ route('career-coach.goals') }}" class="text-sm font-medium hover:underline" style="color:#2D6CDF;">View All &rarr;</a>
                </div>
                @forelse($goals as $goal)
                <div class="mb-3 p-4 rounded-xl border" style="background:#EBF2FF;border-color:#BFCFEE;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $goal->title }}</h3>
                            <p class="text-xs text-gray-500">{{ $goal->getCategoryLabel() }}</p>
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($goal->priority === 'critical') bg-red-100 text-red-700
                            @elseif($goal->priority === 'high') bg-orange-100 text-orange-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($goal->priority) }}
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-semibold" style="color:#2D6CDF;">{{ $goal->progress_percentage }}%</span>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden" style="background:#EBF2FF;">
                            <div class="h-full rounded-full skill-bar-fill transition-all duration-700" style="width:{{ $goal->progress_percentage }}%;background:#2D6CDF;"></div>
                        </div>
                    </div>
                    @if($goal->target_date)
                    <p class="mt-2 text-xs text-gray-500">
                        Target: {{ $goal->target_date->format('M d, Y') }}
                        @if($goal->isOverdue())<span class="text-red-600 ml-1">(Overdue)</span>
                        @elseif($goal->getDaysRemaining() <= 7)<span class="text-orange-600 ml-1">({{ $goal->getDaysRemaining() }}d left)</span>@endif
                    </p>
                    @endif
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:#EBF2FF;">
                        <svg class="w-6 h-6" style="color:#2D6CDF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-gray-600">No active goals yet</p>
                    <a href="{{ route('career-coach.goals') }}" class="mt-1 text-sm hover:underline inline-block" style="color:#2D6CDF;">Create your first goal &rarr;</a>
                </div>
                @endforelse
            </div>

            {{-- Recent Sessions --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Recent Sessions</h2>
                    <a href="{{ route('career-coach.history') }}" class="text-sm font-medium hover:underline" style="color:#2D6CDF;">View All &rarr;</a>
                </div>
                <div class="space-y-2">
                    @forelse($sessions as $session)
                    <a href="{{ route('career-coach.session.show', $session) }}"
                       class="flex items-center justify-between p-3.5 rounded-xl border border-gray-100 transition-all group"
                       style="background:#F0F0EE;"
                       onmouseover="this.style.background='#EBF2FF';this.style.borderColor='#BFCFEE';"
                       onmouseout="this.style.background='#F0F0EE';this.style.borderColor='#F0F0EE';">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $session->title }}</div>
                            <div class="text-xs text-gray-500">{{ $session->getTypeLabel() }} &middot; {{ $session->message_count }} messages</div>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $session->last_message_at?->diffForHumans() ?? $session->created_at->diffForHumans() }}</span>
                    </a>
                    @empty
                    <p class="text-center py-6 text-sm text-gray-500">No sessions yet. Start your first coaching session above!</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-5">

            {{-- Pending Check-ins --}}
            @if($pendingCheckins->isNotEmpty())
            <div class="rounded-2xl p-5" style="background:#EBF2FF;border:1.5px solid #BFCFEE;">
                <h2 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color:#0C2E72;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Pending Check-ins
                </h2>
                @foreach($pendingCheckins as $checkin)
                <div class="p-3 rounded-xl bg-white border border-purple-100 mb-2.5">
                    <p class="font-semibold text-gray-900 text-sm">Weekly Check-in</p>
                    <p class="text-xs text-gray-500">{{ $checkin->scheduled_for->format('l, M d') }}</p>
                    <div class="mt-2 flex gap-2">
                        <button onclick="startCheckin({{ $checkin->id }})" class="px-3 py-1 text-xs text-white rounded-lg font-medium" style="background:#2D6CDF;" onmouseover="this.style.background='#1B57C4'" onmouseout="this.style.background='#2D6CDF'">Start</button>
                        <button onclick="skipCheckin({{ $checkin->id }})" class="px-3 py-1 text-xs rounded-lg font-medium bg-gray-100 text-gray-600 hover:bg-gray-200">Skip</button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- AI Suggestions --}}
            @if($suggestions->isNotEmpty())
            <div class="rounded-2xl p-5" style="background:#FFF8EC;border:1.5px solid #F0C77A;">
                <h2 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color:#E37400;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    AI Suggestions
                </h2>
                @foreach($suggestions as $suggestion)
                <div class="p-3 rounded-xl bg-white border border-yellow-100 mb-2.5 relative">
                    <button onclick="dismissSuggestion({{ $suggestion->id }})" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <p class="font-semibold text-gray-900 text-sm pr-6">{{ $suggestion->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($suggestion->content, 100) }}</p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h3>
                <div class="space-y-1.5">
                    <a href="{{ route('career-coach.goals') }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors"
                       onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#EBF2FF;">&#x1F3AF;</span>
                        <span class="text-sm font-medium text-gray-900">Manage Goals</span>
                    </a>
                    <a href="{{ route('career-coach.preferences') }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors"
                       onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#F0F0EE;">&#x2699;&#xFE0F;</span>
                        <span class="text-sm font-medium text-gray-900">Preferences</span>
                    </a>
                    <a href="{{ route('career-coach.history') }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors"
                       onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='transparent'">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm flex-shrink-0" style="background:#F0F0EE;">&#x1F4DC;</span>
                        <span class="text-sm font-medium text-gray-900">Session History</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function startSession(type) {
    document.querySelectorAll('button[onclick^="startSession"]').forEach(btn => {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    });
    try {
        const response = await fetch('{{ route("career-coach.session.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ type }),
        });
        const data = await response.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert('Failed to start session: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Failed to start session:', error);
        alert('Failed to start session. Please try again.');
    } finally {
        document.querySelectorAll('button[onclick^="startSession"]').forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '';
        });
    }
}

async function startCheckin(checkinId) {
    try {
        const response = await fetch(`/career-coach/checkins/${checkinId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });
        const data = await response.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (error) {
        console.error('Failed to start check-in:', error);
        alert('Failed to start check-in. Please try again.');
    }
}

async function skipCheckin(checkinId) {
    if (!confirm('Skip this check-in?')) return;
    try {
        await fetch(`/career-coach/checkins/${checkinId}/skip`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });
        location.reload();
    } catch (error) {
        console.error('Failed to skip check-in:', error);
    }
}

async function dismissSuggestion(suggestionId) {
    try {
        await fetch(`/career-coach/suggestions/${suggestionId}/dismiss`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });
        location.reload();
    } catch (error) {
        console.error('Failed to dismiss suggestion:', error);
    }
}
</script>
@endpush
@endsection
