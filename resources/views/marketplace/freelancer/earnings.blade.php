@extends('layouts.dashboard')
@section('title', 'My Earnings')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Earnings & Revenue</h1>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="text-3xl font-bold text-green-600">₹{{ number_format($stats['total_earnings'] ?? 0) }}</div>
                <div class="text-gray-500 text-sm mt-1">Total Earned</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['completed_projects'] ?? 0 }}</div>
                <div class="text-gray-500 text-sm mt-1">Projects Done</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="text-3xl font-bold text-purple-600">{{ number_format($profile->average_rating ?? 0, 1) }}⭐</div>
                <div class="text-gray-500 text-sm mt-1">Avg. Rating</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="text-3xl font-bold text-amber-600">{{ $profile->success_rate ?? 0 }}%</div>
                <div class="text-gray-500 text-sm mt-1">Success Rate</div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="p-5 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Payment History</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPayments as $payment)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $payment->contract?->project?->title ?? 'Project Payment' }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">Released {{ $payment->released_at?->format('M d, Y') ?? '—' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-green-600">+₹{{ number_format($payment->amount ?? 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">💰</div>
                        <p>No payments received yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
