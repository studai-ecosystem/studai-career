@extends('layouts.dashboard')
@section('title', 'Invite Freelancer')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('marketplace.freelancer.show', $profile) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Back to Profile</a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            {{-- Freelancer header --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->user?->name ?? 'F') }}&background=1A73E8&color=fff&size=64"
                     class="w-16 h-16 rounded-xl" alt="">
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $profile->user?->name }}</h2>
                    <p class="text-gray-500 text-sm">{{ $profile->professional_title }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-1">
                        <span>⭐ {{ number_format($profile->average_rating ?? 0, 1) }}</span>
                        <span>{{ $profile->success_rate ?? 0 }}% success</span>
                    </div>
                </div>
            </div>

            <h1 class="font-bold text-gray-900 text-xl mb-6">Invite to Project</h1>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
            @endif

            <form action="{{ route('marketplace.employer.send-invitation', $profile) }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Project *</label>
                    <select name="project_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a project...</option>
                        @foreach($myProjects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                    @if($myProjects->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">
                            No open projects. <a href="{{ route('marketplace.employer.create-project') }}" class="underline">Create one first</a>.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message (optional)</label>
                    <textarea name="message" rows="4" placeholder="Hi, I'd love to invite you to bid on my project..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                    Send Invitation
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
