@extends('layouts.dashboard')

@section('title', 'Talent Pool')
@section('page-title', 'Talent Pool')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0C0C0C">Talent Pool</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your saved candidates and build your hiring pipeline</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Candidates</p>
            <p class="text-3xl font-bold mt-1" style="color:#2D6CDF">{{ $stats['total_candidates'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Contacted This Month</p>
            <p class="text-3xl font-bold mt-1" style="color:#1E8E3E">{{ $stats['contacted_this_month'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Avg Rating</p>
            <p class="text-3xl font-bold mt-1" style="color:#fbbc04">{{ number_format($stats['avg_rating'] ?? 0, 1) }} ★</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sources</p>
            <p class="text-3xl font-bold mt-1" style="color:#2D6CDF">{{ count($stats['by_source']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Name, email or skill..."
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Min Rating</label>
            <select name="rating" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="">Any</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected($rating == $i)>{{ $i }}★+</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg" style="background:#2D6CDF">Filter</button>
        @if($search || $rating || $source)
            <a href="{{ route('employer.talent-pool.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50">Clear</a>
        @endif
    </form>

    {{-- Candidate Grid --}}
    @if($candidates->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($candidates as $entry)
                @php $candidate = $entry->candidate; @endphp
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                             style="background:#2D6CDF">
                            {{ strtoupper(substr($candidate->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $candidate->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $candidate->email ?? '' }}</p>
                        </div>
                    </div>
                    @if($entry->rating)
                        <p class="text-xs text-yellow-500 mb-2">{{ str_repeat('★', $entry->rating) }}{{ str_repeat('☆', 5 - $entry->rating) }}</p>
                    @endif
                    @if($entry->tags && count($entry->tags))
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach(array_slice($entry->tags, 0, 3) as $tag)
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full" style="background:#EBF2FF;color:#2D6CDF">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-xs text-gray-400">
                        Added {{ $entry->created_at?->diffForHumans() ?? 'recently' }}
                        @if($entry->last_contacted_at)
                            · Contacted {{ $entry->last_contacted_at->diffForHumans() }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $candidates->links() }}</div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center shadow-sm">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Candidates Yet</h3>
            <p class="text-gray-400 text-sm">Save promising candidates from applications to build your talent pool.</p>
            <a href="{{ route('employer.applicants.index') }}" class="mt-4 inline-block px-5 py-2 text-white text-sm font-medium rounded-lg" style="background:#2D6CDF">Browse Applicants</a>
        </div>
    @endif
</div>
@endsection
