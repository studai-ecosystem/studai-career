@extends('layouts.dashboard')

@section('title', 'Seasonal Events')
@section('page-title', 'Seasonal Events')
@section('page-description', 'Join limited-time events to earn bonus XP and exclusive rewards')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:#1E8E3E;box-shadow: none">
        <h1 class="text-2xl font-bold mb-1">Seasonal Events</h1>
        <p class="text-green-100 text-sm">Participate in limited-time challenges for exclusive rewards</p>
    </div>

    {{-- Current Event --}}
    @if($currentEvent)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-green-300 dark:border-green-700 shadow-md overflow-hidden">
        <div class="p-5 border-b border-green-100 dark:border-green-800 flex items-center gap-3" style="background:#EDFAF2">
            <span class="text-2xl">🎯</span>
            <div>
                <span class="text-xs font-bold text-green-700 uppercase tracking-wide">Live Now</span>
                <h2 class="font-bold text-green-900 text-lg">{{ $currentEvent->name }}</h2>
            </div>
            <span class="ml-auto text-xs font-bold px-3 py-1.5 bg-green-600 text-white rounded-full animate-pulse">ACTIVE</span>
        </div>
        <div class="p-5">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $currentEvent->description ?? 'Participate to earn bonus XP and exclusive rewards.' }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-5">
                @if($currentEvent->ends_at)
                <span>Ends {{ $currentEvent->ends_at->diffForHumans() }}</span>
                @endif
                @if($currentEvent->bonus_xp_multiplier ?? false)
                <span class="font-bold text-green-600">{{ $currentEvent->bonus_xp_multiplier }}× XP</span>
                @endif
            </div>
            @if($participation)
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    You're participating!
                </div>
            @else
                <button onclick="joinEvent({{ $currentEvent->id }})" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#1E8E3E">
                    Join Event
                </button>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center">
        <span class="text-5xl mb-4 block">⏳</span>
        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">No Active Events</h3>
        <p class="text-gray-500 text-sm">Check back soon for the next seasonal event!</p>
    </div>
    @endif

    {{-- Upcoming Events --}}
    @if($upcomingEvents->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-gray-900 dark:text-white">Upcoming Events</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($upcomingEvents as $event)
            <div class="flex items-center gap-4 p-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#EDFAF2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $event->name }}</p>
                    <p class="text-xs text-gray-500">Starts {{ $event->starts_at?->diffForHumans() ?? 'TBD' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <a href="{{ route('gamification.dashboard') }}" class="inline-block px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
        ← Back to Dashboard
    </a>
</div>

@push('scripts')
<script>
async function joinEvent(eventId) {
    try {
        const res = await fetch(`/gamification/events/${eventId}/join`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        });
        const data = await res.json();
        if (data.success) { location.reload(); }
    } catch (e) { console.error(e); }
}
</script>
@endpush
@endsection
