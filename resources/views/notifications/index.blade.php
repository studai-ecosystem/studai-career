@extends('layouts.dashboard')

@section('title', 'All Notifications')
@section('page-title', 'Notifications')

@push('styles')
<style>
.notif-page { background:#EBF2FF; min-height:100%; padding:1.5rem; }
.notif-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(20, 71, 186,.1); box-shadow: none; overflow:hidden; }
.notif-item {
    display:flex; align-items:flex-start; gap:1rem;
    padding:1rem 1.25rem;
    border-bottom:1px solid #EBF2FF;
    transition:background .15s;
    position:relative;
    text-decoration:none; color:inherit;
}
.notif-item:last-child { border-bottom:none; }
.notif-item:hover { background:#EBF2FF; }
.notif-item.unread { background:#EBF2FF; }
.notif-dot {
    position:absolute; left:.5rem; top:50%; transform:translateY(-50%);
    width:.5rem; height:.5rem; border-radius:50%;
}
.notif-icon {
    width:2.5rem; height:2.5rem; border-radius:.75rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
}
.notif-title { font-size:.875rem; font-weight:600; color:#0C0C0C; line-height:1.4; }
.notif-title.read { font-weight:500; color:#3D3D3D; }
.notif-time { font-size:.72rem; color:#A8A8A8; margin-top:.2rem; }
.notif-mark-btn {
    font-size:.7rem; font-weight:600; color:#2D6CDF;
    background:none; border:1px solid rgba(20, 71, 186,.25);
    border-radius:9999px; padding:.2rem .65rem;
    cursor:pointer; white-space:nowrap; flex-shrink:0;
    transition:background .15s;
}
.notif-mark-btn:hover { background:#EBF2FF; }
</style>
@endpush

@section('content')
<div class="notif-page">
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">All Notifications</h1>
            @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                    style="background:#2D6CDF;box-shadow: none;">
                    Mark all as read
                </button>
            </form>
            @endif
        </div>

        <div class="notif-card">
            @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $data     = $notif->data ?? [];
                $message  = $data['message'] ?? $data['body'] ?? $data['title'] ?? 'Notification';
                $url      = $data['url'] ?? $data['action_url'] ?? '#';
                $type     = strtolower(class_basename($notif->type));

                $styles = [
                    'interview' => ['bg'=>'#FFF8EC','color'=>'#E37400'],
                    'shortlisted'=> ['bg'=>'#EDFAF2','color'=>'#1E8E3E'],
                    'hired'      => ['bg'=>'#EDFAF2','color'=>'#1E8E3E'],
                    'rejected'   => ['bg'=>'#fef2f2','color'=>'#2D6CDF'],
                    'scout'      => ['bg'=>'#EBF2FF','color'=>'#2D6CDF'],
                    'job'        => ['bg'=>'#EBF2FF','color'=>'#2D6CDF'],
                    'application'=> ['bg'=>'#EBF2FF','color'=>'#2D6CDF'],
                    'default'    => ['bg'=>'#F7F7F5','color'=>'#737373'],
                ];
                $typeKey = 'default';
                foreach (array_keys($styles) as $k) {
                    if (str_contains($type, $k)) { $typeKey = $k; break; }
                }
                $s = $styles[$typeKey];
                $dotColor = $s['color'];
            @endphp
            <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
                @if($isUnread)
                <span class="notif-dot" style="background:{{ $dotColor }}"></span>
                @endif
                <div class="notif-icon" style="background:{{ $s['bg'] }};{{ $isUnread ? 'margin-left:.75rem;' : 'margin-left:1.25rem;' }}">
                    <svg style="width:18px;height:18px;color:{{ $s['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        @if($typeKey === 'interview')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        @elseif(in_array($typeKey,['shortlisted','hired']))
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($typeKey === 'rejected')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($typeKey === 'scout')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        @elseif($typeKey === 'job')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        @endif
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ $url }}" class="{{ $isUnread ? 'notif-title' : 'notif-title read' }}">{{ $message }}</a>
                    <div class="notif-time">{{ $notif->created_at->diffForHumans() }} &middot; {{ $notif->created_at->format('M j, Y g:i A') }}</div>
                </div>
                @if($isUnread)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                    @csrf
                    <button type="submit" class="notif-mark-btn">Mark read</button>
                </form>
                @endif
            </div>
            @empty
            <div class="py-16 text-center">
                <svg class="w-12 h-12 mx-auto mb-4" style="color:#C8C8C5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900">No notifications yet</h3>
                <p class="text-sm text-gray-500 mt-1">You're all caught up! Notifications will appear here.</p>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
        @endif

    </div>
</div>
@endsection
