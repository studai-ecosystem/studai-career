@extends('layouts.dashboard')
@section('title', 'Saved Freelancers')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Saved Freelancers</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($savedFreelancers as $saved)
                @php $profile = $saved->profile ?? $saved; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->user?->name ?? 'F') }}&background=1A73E8&color=fff&size=56"
                         class="w-14 h-14 rounded-xl shrink-0" alt="">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900">{{ $profile->user?->name }}</h3>
                        <p class="text-gray-500 text-sm truncate">{{ $profile->professional_title }}</p>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span>⭐ {{ number_format($profile->average_rating ?? 0, 1) }}</span>
                            <span>{{ $profile->total_reviews ?? 0 }} reviews</span>
                            @if($profile->hourly_rate)
                                <span>₹{{ $profile->hourly_rate }}/hr</span>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-3">
                            <a href="{{ route('marketplace.freelancer.show', $profile) }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">View Profile</a>
                            <a href="{{ route('marketplace.employer.invite', $profile) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition">Invite</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">🔖</div>
                    <h3 class="font-bold text-gray-900 mb-2">No saved freelancers</h3>
                    <p class="text-gray-500 mb-4">Browse talent and save freelancers you're interested in.</p>
                    <a href="{{ route('marketplace.index') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">Browse Talent</a>
                </div>
            @endforelse
        </div>
        @if($savedFreelancers->hasPages()) <div class="mt-6">{{ $savedFreelancers->links() }}</div> @endif
    </div>
</div>
@endsection
