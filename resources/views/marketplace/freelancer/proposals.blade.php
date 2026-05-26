@extends('layouts.dashboard')
@section('title', 'My Proposals')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Proposals</h1>
            <a href="{{ route('marketplace.projects') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                Browse Projects
            </a>
        </div>

        {{-- Status tabs --}}
        <div class="flex gap-2 mb-6 overflow-x-auto">
            @foreach(['All' => '', 'Pending' => 'pending', 'Accepted' => 'accepted', 'Rejected' => 'rejected', 'Withdrawn' => 'withdrawn'] as $label => $val)
                <a href="{{ route('marketplace.freelancer.proposals', $val ? ['status' => $val] : []) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition
                          {{ $currentStatus === $val || (!$currentStatus && $val === '') ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:border-blue-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse($proposals as $proposal)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                        {{ strtoupper(substr($proposal->project?->title ?? 'P', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('marketplace.project.show', $proposal->project) }}" class="font-semibold text-gray-900 hover:text-blue-600 line-clamp-1">
                                    {{ $proposal->project?->title }}
                                </a>
                                <p class="text-gray-400 text-xs mt-0.5">
                                    Submitted {{ $proposal->created_at->diffForHumans() }}
                                    @if($proposal->project?->employer)
                                        · by {{ $proposal->project->employer->name }}
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full font-semibold
                                {{ match($proposal->status) {
                                    'accepted'  => 'bg-green-100 text-green-700',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    'shortlisted' => 'bg-blue-100 text-blue-700',
                                    default     => 'bg-yellow-100 text-yellow-700'
                                } }}">
                                {{ ucfirst($proposal->status) }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mt-2 line-clamp-2">{{ $proposal->cover_letter }}</p>
                        <div class="flex items-center gap-4 mt-3 text-sm">
                            <span class="font-semibold text-gray-900">₹{{ number_format($proposal->proposed_amount) }}</span>
                            @if($proposal->estimated_duration_days)
                                <span class="text-gray-400">{{ $proposal->estimated_duration_days }} days</span>
                            @endif
                            @if($proposal->status === 'accepted')
                                <a href="{{ route('marketplace.contracts.show', $proposal->contract) }}" class="text-blue-600 hover:underline font-medium">
                                    View Contract →
                                </a>
                            @elseif($proposal->status === 'pending')
                                <button onclick="withdrawProposal({{ $proposal->id }}, this)"
                                        class="text-red-500 hover:text-red-700 hover:underline">
                                    Withdraw
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="font-bold text-gray-900 mb-2">No proposals yet</h3>
                    <p class="text-gray-500 mb-4">Start bidding on projects to grow your freelancing career.</p>
                    <a href="{{ route('marketplace.projects') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                        Browse Projects
                    </a>
                </div>
            @endforelse
        </div>

        @if($proposals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-6">{{ $proposals->links() }}</div>
        @endif
    </div>
</div>

<script>
async function withdrawProposal(id, btn) {
    if (!confirm('Withdraw this proposal?')) return;
    btn.disabled = true;
    const res = await fetch(`/marketplace/freelancer/proposals/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' }
    });
    const json = await res.json();
    if (json.success) { btn.closest('.bg-white').remove(); }
    else { alert(json.message); btn.disabled = false; }
}
</script>
@endsection
