@extends('layouts.dashboard')
@section('title', 'Review Proposals — ' . $project->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('marketplace.employer.dashboard') }}" class="hover:text-blue-600">Marketplace</a>
                <span>/</span>
                <a href="{{ route('marketplace.employer.projects') }}" class="hover:text-blue-600">My Projects</a>
                <span>/</span>
                <span class="text-gray-800 font-medium">Review Proposals</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Budget: ₹{{ number_format($project->budget_min) }}–₹{{ number_format($project->budget_max ?? $project->budget_min) }}
                · {{ $project->estimated_duration_days ?? '?' }} days
                · {{ $stats['total'] }} proposals
            </p>
        </div>
        <a href="{{ route('marketplace.project.show', $project) }}"
           class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            View Project →
        </a>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @foreach([
            ['label'=>'Total',       'val'=>$stats['total'],       'color'=>'#737373'],
            ['label'=>'Pending',     'val'=>$stats['pending'],     'color'=>'#E37400'],
            ['label'=>'Shortlisted', 'val'=>$stats['shortlisted'], 'color'=>'#2D6CDF'],
            ['label'=>'Offered',     'val'=>$stats['offered'],     'color'=>'#2D6CDF'],
            ['label'=>'Accepted',    'val'=>$stats['accepted'],    'color'=>'#1E8E3E'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-extrabold" style="color:{{ $s['color'] }}">{{ $s['val'] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    @if($stats['avg_score'])
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-5 text-sm text-blue-800 flex items-center gap-2">
        🤖 <strong>AI Match Average:</strong> {{ round($stats['avg_score']) }}/100 across {{ $stats['total'] }} proposals.
        Proposals are ranked highest first.
    </div>
    @endif

    @if($stats['total'] === 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="text-4xl mb-3">📭</div>
        <h3 class="font-bold text-gray-800 text-lg">No proposals yet</h3>
        <p class="text-gray-500 mt-1 text-sm">Proposals will appear here once freelancers apply.</p>
    </div>
    @else

    {{-- Proposal Cards --}}
    <div class="space-y-4" id="proposalList">
        @foreach($proposals as $proposal)
        @php
            $score     = $proposal->ai_match_score;
            $breakdown = $proposal->ai_match_breakdown ?? [];
            $profile   = $proposal->freelancer?->freelancerProfile;
            $name      = $proposal->freelancer?->name ?? 'Freelancer';
            $initials  = strtoupper(substr($name, 0, 2));
            $isOffer   = $proposal->isOfferSent();
            $scoreColor = match(true) {
                $score >= 80 => '#1E8E3E',
                $score >= 60 => '#E37400',
                $score !== null => '#2D6CDF',
                default => '#A8A8A8',
            };
            $scoreBg = match(true) {
                $score >= 80 => '#EDFAF2',
                $score >= 60 => '#FFF8EC',
                $score !== null => '#fef2f2',
                default => '#F7F7F5',
            };
            $statusColor = match($proposal->status) {
                'shortlisted' => $isOffer ? '#2D6CDF' : '#2D6CDF',
                'accepted'    => '#1E8E3E',
                'rejected'    => '#2D6CDF',
                default       => '#E37400',
            };
            $statusLabel = match(true) {
                $proposal->status === 'accepted'   => 'Accepted',
                $isOffer                           => '📨 Offer Sent',
                $proposal->status === 'shortlisted'=> 'Shortlisted',
                $proposal->status === 'rejected'   => 'Rejected',
                default                            => 'Pending Review',
            };
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden proposal-card"
             data-status="{{ $proposal->status }}" data-offer="{{ $isOffer ? '1' : '0' }}">

            {{-- Top bar: score + status --}}
            <div class="flex items-center justify-between px-5 py-2 border-b border-gray-50"
                 style="background:{{ $scoreBg }};">
                <div class="flex items-center gap-3">
                    @if($score !== null)
                    <div class="flex items-center gap-1.5">
                        <span class="text-lg font-extrabold" style="color:{{ $scoreColor }}">{{ $score }}</span>
                        <span class="text-xs text-gray-400">/100 AI Match</span>
                    </div>
                    {{-- Score bar --}}
                    <div class="w-28 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-2 rounded-full transition-all" style="width:{{ $score }}%;background:{{ $scoreColor }};"></div>
                    </div>
                    @else
                    <span class="text-xs text-gray-400">⏳ Scoring…</span>
                    @endif
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                      style="background:{{ $statusColor }}22;color:{{ $statusColor }};">
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="p-5">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                         style="background:#2D6CDF;">{{ $initials }}</div>

                    {{-- Main content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $name }}</h3>
                                <p class="text-sm text-gray-500">{{ $profile?->professional_title ?? 'Freelancer' }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-extrabold text-gray-900 text-lg">₹{{ number_format($proposal->proposed_amount) }}</div>
                                <div class="text-xs text-gray-400">{{ $proposal->estimated_duration_days ?? '?' }} days</div>
                            </div>
                        </div>

                        {{-- Skills matched --}}
                        @if(!empty($breakdown['matched_skills']) || !empty($breakdown['missing_skills']))
                        <div class="flex flex-wrap gap-1.5 mt-2 mb-2">
                            @foreach($breakdown['matched_skills'] ?? [] as $skill)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:#EDFAF2;color:#1E8E3E;border:1px solid #A3D9B4;">✓ {{ $skill }}</span>
                            @endforeach
                            @foreach($breakdown['missing_skills'] ?? [] as $skill)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:#fef2f2;color:#2D6CDF;border:1px solid #FCA5A5;">✗ {{ $skill }}</span>
                            @endforeach
                        </div>
                        @endif

                        @if(!empty($breakdown['reasoning']))
                        <p class="text-xs text-gray-500 italic mb-3">🤖 {{ $breakdown['reasoning'] }}</p>
                        @endif

                        {{-- Cover Letter --}}
                        <div class="relative">
                            <p class="text-sm text-gray-700 line-clamp-3" id="cl-{{ $proposal->id }}">
                                {{ $proposal->cover_letter }}
                            </p>
                            <button onclick="toggleCL({{ $proposal->id }})"
                                    class="text-xs text-blue-600 hover:underline mt-1">Show more</button>
                        </div>

                        {{-- Action Buttons --}}
                        @if($proposal->status === 'accepted')
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('marketplace.contracts.show', $proposal->contract) }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#1E8E3E;">
                                View Contract →
                            </a>
                        </div>
                        @elseif($proposal->status !== 'rejected' && $proposal->status !== 'withdrawn')
                        <div class="mt-4 flex flex-wrap gap-2" id="actions-{{ $proposal->id }}">
                            @if(!$isOffer)
                            <button onclick="proposalAction('{{ route('marketplace.employer.send-offer', $proposal) }}', {{ $proposal->id }}, 'offer')"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                                    style="background:#2D6CDF;">
                                📨 Send Offer
                            </button>
                            @if($proposal->status === 'pending')
                            <button onclick="proposalAction('{{ route('marketplace.employer.shortlist-proposal', $proposal) }}', {{ $proposal->id }}, 'shortlist')"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold border transition hover:bg-blue-50"
                                    style="border-color:#2D6CDF;color:#2D6CDF;">
                                ★ Shortlist
                            </button>
                            @endif
                            <button onclick="proposalAction('{{ route('marketplace.employer.hire', $proposal) }}', {{ $proposal->id }}, 'hire')"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                                    style="background:#1E8E3E;">
                                ✓ Hire Now
                            </button>
                            <button onclick="proposalAction('{{ route('marketplace.employer.reject-proposal', $proposal) }}', {{ $proposal->id }}, 'reject')"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                ✕ Reject
                            </button>
                            @else
                            {{-- Offer sent — waiting --}}
                            <span class="px-4 py-2 rounded-xl text-sm font-medium" style="background:#EBF2FF;color:#2D6CDF;">
                                ⏳ Awaiting freelancer response
                            </span>
                            <button onclick="proposalAction('{{ route('marketplace.employer.hire', $proposal) }}', {{ $proposal->id }}, 'hire')"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                                    style="background:#1E8E3E;">
                                ✓ Hire Directly
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- Toast --}}
<div id="toast" class="fixed top-4 right-4 z-[9999] hidden px-5 py-3 rounded-xl shadow-lg text-white font-semibold text-sm"></div>

<script>
function toggleCL(id) {
    const el = document.getElementById('cl-' + id);
    el.classList.toggle('line-clamp-3');
}

async function proposalAction(url, id, type) {
    const labels = { offer: 'Sending offer…', shortlist: 'Shortlisting…', hire: 'Creating contract…', reject: 'Rejecting…' };
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.textContent = labels[type] || '…';

    try {
        const res  = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const data = await res.json();
        showToast(data.message || 'Done', data.success ? '#1E8E3E' : '#2D6CDF');

        if (data.success) {
            if (type === 'hire' && data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1200);
            } else {
                setTimeout(() => location.reload(), 1000);
            }
        } else {
            btn.disabled = false;
        }
    } catch (e) {
        showToast('Network error. Please try again.', '#2D6CDF');
        btn.disabled = false;
    }
}

function showToast(msg, color) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = color;
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 4000);
}
</script>
@endsection
