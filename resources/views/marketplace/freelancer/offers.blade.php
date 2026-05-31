@extends('layouts.dashboard')
@section('title', 'My Offers')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🎁 My Offers</h1>
            <p class="text-sm text-gray-500 mt-1">Companies that want to hire you — accept or decline</p>
        </div>
        <a href="{{ route('marketplace.freelancer.proposals') }}"
           class="text-sm text-blue-600 hover:underline">← My Proposals</a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-xl text-sm font-medium" style="background:#EDFAF2;color:#1E8E3E;border:1px solid #A3D9B4;">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 rounded-xl text-sm font-medium" style="background:#fef2f2;color:#2D6CDF;border:1px solid #FCA5A5;">
        ⚠ {{ session('error') }}
    </div>
    @endif

    @if($offers->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">📭</div>
        <h3 class="font-bold text-gray-800 text-xl mb-2">No offers yet</h3>
        <p class="text-gray-500 text-sm mb-6">When a company selects your proposal and sends you an offer, it will appear here.</p>
        <a href="{{ route('marketplace.projects') }}"
           class="inline-block px-6 py-3 text-white font-bold rounded-xl hover:opacity-90 transition text-sm"
           style="background:#2D6CDF;">Browse Projects</a>
    </div>
    @else

    <div class="space-y-4">
        @foreach($offers as $offer)
        @php
            $project  = $offer->project;
            $employer = $project?->employer;
            $name     = $employer?->name ?? 'Company';
            $initials = strtoupper(substr($name, 0, 2));
            $score    = $offer->ai_match_score;
            $breakdown = $offer->ai_match_breakdown ?? [];
            $daysAgo  = $offer->offer_sent_at?->diffForHumans() ?? 'recently';
        @endphp
        <div class="bg-white rounded-2xl border-2 shadow-sm overflow-hidden" style="border-color:#2D6CDF22;">

            {{-- Offer header --}}
            <div class="px-5 py-3 flex items-center justify-between" style="background:#EBF2FF;">
                <div class="flex items-center gap-2 text-sm font-semibold" style="color:#2D6CDF;">
                    📨 Offer received {{ $daysAgo }}
                </div>
                @if($score !== null)
                <div class="flex items-center gap-1.5 text-sm">
                    <span class="font-bold" style="color:{{ $score >= 80 ? '#1E8E3E' : ($score >= 60 ? '#E37400' : '#2D6CDF') }}">{{ $score }}/100</span>
                    <span class="text-gray-400 text-xs">AI Match</span>
                </div>
                @endif
            </div>

            <div class="p-5">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                         style="background:#2D6CDF;">{{ $initials }}</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 text-lg">{{ $project?->title ?? 'Project' }}</h3>
                        <p class="text-sm text-gray-500">by <strong>{{ $name }}</strong></p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-extrabold text-gray-900">₹{{ number_format($offer->proposed_amount) }}</div>
                        <div class="text-xs text-gray-400">{{ $offer->estimated_duration_days ?? '?' }} days delivery</div>
                    </div>
                </div>

                {{-- Skill breakdown --}}
                @if(!empty($breakdown['matched_skills']) || !empty($breakdown['missing_skills']))
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($breakdown['matched_skills'] ?? [] as $skill)
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:#EDFAF2;color:#1E8E3E;border:1px solid #A3D9B4;">✓ {{ $skill }}</span>
                    @endforeach
                    @foreach($breakdown['missing_skills'] ?? [] as $skill)
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:#fef2f2;color:#2D6CDF;border:1px solid #FCA5A5;">✗ {{ $skill }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Cover letter --}}
                <div class="p-3 rounded-xl mb-4" style="background:#EBF2FF;">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Your Proposal</p>
                    <p class="text-sm text-gray-700 line-clamp-3">{{ $offer->cover_letter }}</p>
                </div>

                {{-- CTA --}}
                <div class="flex gap-3">
                    <form action="{{ route('marketplace.freelancer.offer.accept', $offer) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full py-3 text-white font-bold rounded-xl hover:opacity-90 transition text-sm"
                                style="background:#2D6CDF;"
                                onclick="return confirm('Accept this offer and create a contract with {{ addslashes($name) }}?')">
                            ✓ Accept Offer & Start Contract
                        </button>
                    </form>
                    <form action="{{ route('marketplace.freelancer.offer.decline', $offer) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-5 py-3 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition text-sm"
                                onclick="return confirm('Decline this offer?')">
                            ✕ Decline
                        </button>
                    </form>
                </div>

                <p class="text-xs text-gray-400 mt-3 text-center">
                    ⚠ Once you accept, a binding contract is created and both parties are committed.
                </p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
