@extends('layouts.dashboard')
@section('title', 'My Contracts - Employer')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Contracts</h1>
        </div>

        <div class="flex gap-2 mb-6 overflow-x-auto">
            @foreach(['All' => '', 'Active' => 'active', 'Pending' => 'pending', 'Completed' => 'completed', 'Cancelled' => 'cancelled'] as $label => $val)
                <a href="{{ route('marketplace.employer.contracts', $val ? ['status' => $val] : []) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition
                          {{ $currentStatus === $val || (!$currentStatus && $val === '') ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:border-blue-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse($contracts as $contract)
                <a href="{{ route('marketplace.contracts.show', $contract) }}"
                   class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-blue-300 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $contract->project?->title ?? 'Untitled Project' }}</h3>
                            <p class="text-gray-400 text-xs mt-1">
                                Freelancer: {{ $contract->freelancer?->name }} ·
                                Started {{ $contract->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="block font-bold text-gray-900 text-lg">₹{{ number_format($contract->total_amount ?? 0) }}</span>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                {{ match($contract->status) {
                                    'active'    => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default     => 'bg-yellow-100 text-yellow-700'
                                } }}">
                                {{ ucfirst($contract->status) }}
                            </span>
                        </div>
                    </div>

                    @if($contract->milestones && $contract->milestones->count())
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Milestones Progress</span>
                                <span>{{ $contract->milestones->where('status', 'approved')->count() }} / {{ $contract->milestones->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                @php $pct = $contract->milestones->count() ? round($contract->milestones->where('status','approved')->count() / $contract->milestones->count() * 100) : 0; @endphp
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                </a>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">📄</div>
                    <h3 class="font-bold text-gray-900 mb-2">No contracts yet</h3>
                    <p class="text-gray-500 mb-4">Hire a freelancer to start a contract.</p>
                    <a href="{{ route('marketplace.employer.create-project') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                        Post a Project
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
