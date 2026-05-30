@extends('layouts.app')

@section('title', 'Video Interview Invitation')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">You've Been Invited!</h1>
                <p class="text-indigo-100 mt-2">Complete a video interview at your convenience</p>
            </div>

            <div class="px-8 py-8">
                @if($invitation->status === 'expired' || ($invitation->deadline && $invitation->deadline->isPast()))
                    <!-- Expired -->
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Invitation Expired</h2>
                        <p class="text-gray-500">This video interview invitation has expired. Please contact the employer to request a new invitation.</p>
                    </div>
                @elseif($invitation->status === 'declined')
                    <!-- Declined -->
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Invitation Declined</h2>
                        <p class="text-gray-500">You have already declined this invitation.</p>
                    </div>
                @elseif($invitation->status === 'accepted')
                    <!-- Accepted already -->
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Already Accepted</h2>
                        <p class="text-gray-500 mb-6">You accepted this invitation{{ $invitation->accepted_at ? ' on ' . $invitation->accepted_at->format('M d, Y') : '' }}.</p>
                        @auth
                            <a href="{{ route('video-interview.record', $invitation->video_interview_session_id) }}"
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                                Continue Interview
                            </a>
                        @endauth
                    </div>
                @else
                    <!-- Pending - show accept/decline -->
                    @if($invitation->message)
                        <div class="bg-indigo-50 rounded-xl p-4 mb-6">
                            <p class="text-sm font-medium text-indigo-800 mb-1">Message from interviewer</p>
                            <p class="text-indigo-700">{{ $invitation->message }}</p>
                        </div>
                    @endif

                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Complete at your convenience</p>
                                <p class="text-sm text-gray-500">Record your answers anytime, from anywhere</p>
                            </div>
                        </div>
                        @if($invitation->deadline)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Deadline</p>
                                    <p class="text-sm text-gray-500">{{ $invitation->deadline->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">AI-powered analysis</p>
                                <p class="text-sm text-gray-500">Your responses will be analysed to highlight your strengths</p>
                            </div>
                        </div>
                    </div>

                    @guest
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                            <p class="text-sm text-amber-800">Please <a href="{{ route('login') }}" class="font-semibold underline">log in</a> or <a href="{{ route('register') }}" class="font-semibold underline">create an account</a> to accept this invitation.</p>
                        </div>
                    @endguest

                    @auth
                        <div class="flex gap-4">
                            <form method="POST" action="{{ route('video-interview.invitation.accept', $invitation) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Accept &amp; Start Interview
                                </button>
                            </form>
                            <form method="POST" action="{{ route('video-interview.invitation.decline', $invitation) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @endauth
                @endif
            </div>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Powered by <span class="font-semibold text-indigo-600">StudAI Hire</span> · Your Career. On Autopilot.
        </p>
    </div>
</div>
@endsection
