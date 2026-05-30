@extends('layouts.dashboard')

@section('title', 'My Rewards')
@section('page-title', 'My Rewards')
@section('page-description', 'Rewards you have redeemed')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 8px 32px rgba(245,158,11,.3)">
        <h1 class="text-2xl font-bold mb-1">My Rewards</h1>
        <p class="text-amber-100 text-sm">Rewards you've unlocked and redeemed</p>
    </div>

    {{-- Active Rewards --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
            <h2 class="font-bold text-gray-900 dark:text-white">Active Rewards</h2>
            <span class="ml-auto text-xs font-medium px-2 py-0.5 bg-green-100 text-green-700 rounded-full">{{ $activeRewards->count() }}</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($activeRewards as $reward)
            <div class="flex items-center gap-4 p-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $reward->name ?? $reward->title ?? 'Reward' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reward->description ?? '' }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 bg-green-100 text-green-700 rounded-full">Active</span>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-sm">No active rewards yet. <a href="{{ route('gamification.rewards') }}" class="text-indigo-600 hover:underline">Browse rewards →</a></div>
            @endforelse
        </div>
    </div>

    {{-- Used Rewards --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block"></span>
            <h2 class="font-bold text-gray-900 dark:text-white">Used Rewards</h2>
            <span class="ml-auto text-xs font-medium px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">{{ $usedRewards->count() }}</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($usedRewards as $reward)
            <div class="flex items-center gap-4 p-4 opacity-60">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-gray-700">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $reward->name ?? $reward->title ?? 'Reward' }}</p>
                    <p class="text-xs text-gray-500">Used {{ $reward->updated_at?->diffForHumans() ?? '' }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">Used</span>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-sm">No used rewards</div>
            @endforelse
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('gamification.rewards') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">Browse More Rewards</a>
        <a href="{{ route('gamification.dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">Dashboard</a>
    </div>
</div>
@endsection
