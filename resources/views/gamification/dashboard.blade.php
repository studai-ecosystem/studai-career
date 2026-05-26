@extends('layouts.dashboard')

@section('title', 'Achievements & Gamification')
@section('page-title', 'Achievements')
@section('page-description', 'Track your progress, earn rewards, level up')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#ec4899 0%,#db2777 35%,#6366f1 70%,#8b5cf6 100%);box-shadow:0 8px 32px rgba(236,72,153,.3)">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 80% 50%,rgba(255,255,255,.4) 0%,transparent 60%);"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="flex items-center gap-5">
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center border-2 border-white/30">
                        <span class="text-4xl font-bold">{{ $profile['level'] }}</span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-amber-400 text-white px-2 py-0.5 rounded-full text-[10px] font-bold">LEVEL</div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-pink-100 text-sm mt-0.5">Keep up the momentum – you're making great progress!</p>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-pink-200 mb-1">
                            <span>Level {{ $profile['level'] }}</span>
                            <span>{{ number_format($profile['xp_current']) }} / {{ number_format($profile['xp_required']) }} XP</span>
                            <span>Level {{ $profile['level'] + 1 }}</span>
                        </div>
                        <div class="h-3 bg-white/20 rounded-full overflow-hidden w-64 max-w-full">
                            <div class="h-full bg-gradient-to-r from-module-negotiation-400 to-module-negotiation-300 rounded-full" style="width:{{ $profile['xp_progress'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold">{{ number_format($profile['total_points']) }}</div>
                    <div class="text-pink-200 text-xs mt-0.5">Total Points</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold flex items-center gap-1"><span>??</span>{{ $profile['current_streak'] }}</div>
                    <div class="text-pink-200 text-xs mt-0.5">Day Streak</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold">#{{ $profile['rank'] ?: '—' }}</div>
                    <div class="text-pink-200 text-xs mt-0.5">Global Rank</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Challenges + Achievements --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Profile Completion --}}
            @if($profileCompletion['percentage'] < 100)
            <div class="bg-white rounded-2xl border border-pink-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-gray-900">Complete Your Profile</h2>
                    <span class="text-xl font-bold text-pink-600">{{ $profileCompletion['percentage'] }}%</span>
                </div>
                <div class="h-2.5 bg-pink-100 rounded-full overflow-hidden mb-4">
                    <div class="h-full bg-gradient-to-r from-pink-500 to-rose-400 rounded-full" style="width:{{ $profileCompletion['percentage'] }}%"></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($profileCompletion['sections'] as $section => $completed)
                    <div class="flex items-center gap-1.5 p-2 rounded-lg text-xs font-medium {{ $completed ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-500' }}">
                        @if($completed)<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @else<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>@endif
                        <span class="capitalize truncate">{{ str_replace('_', ' ', $section) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Daily Challenges --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Daily Challenges</h2>
                    <a href="{{ route('gamification.challenges') }}" class="text-sm font-medium text-pink-600 hover:underline">View All ?</a>
                </div>
                <div class="space-y-3">
                    @forelse($challenges as $userChallenge)
                    @php $challenge = $userChallenge->challenge; @endphp
                    <div class="flex items-center gap-3 p-4 rounded-xl {{ $userChallenge->is_completed ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-100' }}">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0 {{ $userChallenge->is_completed ? 'bg-green-100' : 'bg-pink-100' }}">
                            @if($userChallenge->is_completed) ?
                            @else @switch($challenge->difficulty) @case('easy') ?? @break @case('medium') ? @break @case('hard') ?? @break @endswitch
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $challenge->name }}</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded-full {{ $challenge->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($challenge->difficulty === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ ucfirst($challenge->difficulty) }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex-1 h-1.5 bg-gray-200 rounded-full max-w-[120px]">
                                    <div class="h-full bg-pink-500 rounded-full" style="width:{{ $userChallenge->progress_percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $userChallenge->progress }}/{{ $userChallenge->target }}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-xs font-bold text-pink-600">+{{ $challenge->points_reward }}pts</div>
                            <div class="text-xs text-gray-500">+{{ $challenge->xp_reward }} XP</div>
                            @if($userChallenge->canClaim())
                            <button onclick="claimChallenge({{ $userChallenge->id }})" class="mt-1 px-2.5 py-1 bg-green-500 text-white text-xs font-semibold rounded-lg hover:bg-green-600 transition-colors">Claim!</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-gray-600 text-sm">No challenges today. Check back tomorrow!</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Achievements --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Recent Achievements</h2>
                    <a href="{{ route('gamification.achievements') }}" class="text-sm font-medium text-pink-600 hover:underline">View All ?</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($achievements as $userAchievement)
                    @php $achievement = $userAchievement->achievement; @endphp
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 bg-white shadow-sm">{{ $achievement->icon ?? '??' }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900">{{ $achievement->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $achievement->description }}</div>
                            <div class="text-xs text-amber-600 mt-1">{{ $userAchievement->unlocked_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-8">
                        <div class="text-3xl mb-2">??</div>
                        <div class="text-sm text-gray-600">Complete activities to unlock achievements!</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-5">
            {{-- Live Event --}}
            @if($event)
            <div class="bg-gradient-to-br from-purple-600 to-rose-600 rounded-2xl p-5 text-white">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-2xl">??</span>
                    <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">LIVE EVENT</span>
                </div>
                <h3 class="text-base font-bold mb-1">{{ $event->name }}</h3>
                <p class="text-purple-200 text-xs mb-3">{{ Str::limit($event->description, 100) }}</p>
                <div class="flex items-center justify-between text-xs mb-2">
                    <span>{{ $event->remaining_time }}</span>
                    <span class="font-bold">{{ $event->xp_multiplier }}x XP Bonus</span>
                </div>
                <div class="h-1.5 bg-white/20 rounded-full"><div class="h-full bg-white rounded-full" style="width:{{ $event->progress_percentage }}%"></div></div>
                <a href="{{ route('gamification.events') }}" class="block w-full mt-4 text-center py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-semibold transition-colors">View Event ?</a>
            </div>
            @endif

            {{-- Badges --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Featured Badges</h3>
                    <a href="{{ route('gamification.badges') }}" class="text-xs font-medium text-pink-600 hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-4 gap-2">
                    @forelse($badges as $userBadge)
                    @php $badge = $userBadge->badge; @endphp
                    <div class="relative group">
                        <div class="aspect-square rounded-xl flex items-center justify-center text-2xl border-2 cursor-pointer hover:scale-110 transition-transform"
                             style="background:{{ $badge->color }}15; border-color:{{ $badge->color }}">{{ $badge->icon }}</div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 bg-ink-primary text-white text-[10px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">{{ $badge->name }}</div>
                    </div>
                    @empty
                    <div class="col-span-4 text-center py-4 text-gray-600 text-xs">Earn badges by completing activities!</div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Explore</h3>
                <div class="space-y-1.5">
                    @foreach([
                        ['route'=>'gamification.leaderboards', 'icon'=>'??', 'label'=>'Leaderboards', 'sub'=>'Compete globally'],
                        ['route'=>'gamification.rewards',     'icon'=>'??', 'label'=>'Rewards Store',  'sub'=>number_format($profile['available_points'])."pts to spend"],
                        ['route'=>'gamification.referrals',   'icon'=>'??', 'label'=>'Referrals',      'sub'=>'Invite friends, earn rewards'],
                        ['route'=>'gamification.activity',    'icon'=>'??', 'label'=>'Activity Log',   'sub'=>'View your progress'],
                    ] as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition-colors group">
                        <span class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center text-sm group-hover:bg-pink-200 transition-colors">{{ $link['icon'] }}</span>
                        <div><div class="text-sm font-semibold text-gray-900">{{ $link['label'] }}</div><div class="text-xs text-gray-500">{{ $link['sub'] }}</div></div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Stats Summary --}}
            <div class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-2xl p-5 text-white">
                <h3 class="text-sm font-semibold mb-3">Your Stats</h3>
                <div class="space-y-2.5">
                    @foreach(['Achievements'=>$profile['achievements_count'], 'Badges'=>$profile['badges_count'], 'Best Streak'=>$profile['longest_streak'].' days', 'Global Rank'=>'#'.($profile['rank'] ?: '—')] as $label => $val)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-pink-200">{{ $label }}</span>
                        <span class="font-bold">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function claimChallenge(challengeId) {
    fetch(`/gamification/challenges/${challengeId}/claim`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Claimed! +${data.points} points, +${data.xp} XP`);
            window.location.reload();
        } else {
            alert(data.message || 'Failed to claim reward');
        }
    })
    .catch(error => { console.error('Error:', error); alert('An error occurred'); });
}
</script>
@endpush
@endsection
