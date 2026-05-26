@extends('layouts.dashboard')
@section('title', 'Skill Badges')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Skill Badges</h1>

        {{-- My Badges --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="font-bold text-gray-900 mb-4">My Earned Badges</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($badges as $userBadge)
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                        <div class="text-4xl mb-2">🏅</div>
                        <div class="font-semibold text-gray-900 text-sm">{{ $userBadge->badge?->name ?? 'Badge' }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $userBadge->badge?->category }}</div>
                        @if($userBadge->is_verified ?? false)
                            <span class="inline-block mt-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Verified</span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8 text-gray-400">
                        <div class="text-4xl mb-2">🏅</div>
                        <p>No badges yet. Apply for badges below to showcase your skills.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Available Badges --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">Available Badges</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($availableBadges as $badge)
                    <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl hover:border-blue-300 transition">
                        <div class="text-3xl shrink-0">🎖️</div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $badge->name }}</h3>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $badge->description }}</p>
                        </div>
                        <button onclick="applyBadge({{ $badge->id }}, this)"
                                class="shrink-0 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                            Apply
                        </button>
                    </div>
                @empty
                    <p class="col-span-2 text-gray-400 text-center py-6">No badges available right now.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<script>
async function applyBadge(id, btn) {
    btn.disabled = true; btn.textContent = '...';
    const res = await fetch(`/marketplace/freelancer/badges/${id}/apply`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
        body: JSON.stringify({})
    });
    const json = await res.json();
    if (json.success) { btn.textContent = '✓ Applied'; btn.className = btn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-green-600'); }
    else { alert(json.message); btn.disabled = false; btn.textContent = 'Apply'; }
}
</script>
@endsection
