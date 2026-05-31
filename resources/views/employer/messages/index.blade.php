@extends('layouts.dashboard')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0C0C0C">Candidate Messages</h1>
            <p class="text-sm text-gray-500 mt-1">Communicate with candidates directly</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Conversations</p>
            <p class="text-3xl font-bold mt-1" style="color:#2D6CDF">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">With Unread</p>
            <p class="text-3xl font-bold mt-1" style="color:#2D6CDF">{{ $stats['unread'] }}</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-100">
            @foreach(['active' => 'Active', 'archived' => 'Archived', 'all' => 'All'] as $val => $label)
                <a href="{{ route('employer.messages.index', ['status' => $val]) }}"
                   class="px-5 py-3 text-sm font-medium transition-colors {{ $status === $val ? 'border-b-2 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}"
                   style="{{ $status === $val ? 'border-color:#2D6CDF;color:#2D6CDF' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Conversation List --}}
        @if($conversations->count() > 0)
            <div class="divide-y divide-gray-50">
                @foreach($conversations as $conv)
                    <a href="{{ route('employer.messages.show', $conv->id) }}"
                       class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                             style="background:#2D6CDF">
                            {{ strtoupper(substr($conv->candidate->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-semibold text-sm text-gray-900">{{ $conv->candidate->name ?? 'Candidate' }}</p>
                                <span class="text-xs text-gray-400">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ $conv->job->title ?? 'General' }}</p>
                            @if($conv->latestMessage)
                                <p class="text-xs text-gray-400 truncate mt-1">{{ Str::limit($conv->latestMessage->body ?? '', 60) }}</p>
                            @endif
                        </div>
                        @if($conv->unread_messages_count > 0)
                            <span class="flex-shrink-0 w-5 h-5 text-white text-xs rounded-full flex items-center justify-center font-bold" style="background:#2D6CDF">
                                {{ $conv->unread_messages_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="p-4">{{ $conversations->links() }}</div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Conversations Yet</h3>
                <p class="text-gray-400 text-sm">Start a conversation with a candidate from their application.</p>
                <a href="{{ route('employer.applicants.index') }}" class="mt-4 inline-block px-5 py-2 text-white text-sm font-medium rounded-lg" style="background:#2D6CDF">View Applicants</a>
            </div>
        @endif
    </div>
</div>
@endsection
