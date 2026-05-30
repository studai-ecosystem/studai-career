@extends('layouts.dashboard')

@section('title', 'Activity History')
@section('page-title', 'Activity History')
@section('page-description', 'Your complete gamification activity log')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 8px 32px rgba(99,102,241,.3)">
        <h1 class="text-2xl font-bold mb-1">Activity History</h1>
        <p class="text-indigo-200 text-sm">All your points, XP and career milestones in one place</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Activity Feed --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-bold text-gray-900 dark:text-white">Recent Activity</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($activities as $activity)
                <div class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe)">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->activity_type ?? $activity->description ?? 'Activity' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $activity->created_at ? $activity->created_at->diffForHumans() : '' }}</p>
                    </div>
                    @if(!empty($activity->points_earned))
                    <span class="text-sm font-bold text-green-600">+{{ $activity->points_earned }} pts</span>
                    @endif
                </div>
                @empty
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="font-medium">No activity yet</p>
                    <p class="text-sm mt-1">Start applying to jobs and completing tasks to earn points!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Points History --}}
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Points Summary</h3>
                @forelse($pointsHistory as $entry)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $entry->description ?? 'Points earned' }}</span>
                    <span class="text-sm font-bold {{ ($entry->points ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ ($entry->points ?? 0) >= 0 ? '+' : '' }}{{ $entry->points ?? 0 }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No points history yet</p>
                @endforelse
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">XP History</h3>
                @forelse($xpHistory as $entry)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $entry->description ?? 'XP earned' }}</span>
                    <span class="text-sm font-bold text-indigo-600">+{{ $entry->xp ?? 0 }} XP</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No XP history yet</p>
                @endforelse
            </div>

            <a href="{{ route('gamification.dashboard') }}" class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
