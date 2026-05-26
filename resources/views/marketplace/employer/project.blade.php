@extends('layouts.dashboard')
@section('title', 'Manage Project')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('marketplace.employer.dashboard') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                {{ match($project->status) {
                    'published' => 'bg-green-100 text-green-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-gray-100 text-gray-700',
                    default => 'bg-yellow-100 text-yellow-700',
                } }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Proposals --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">Proposals ({{ $proposals->count() }})</h2>
                    <a href="{{ route('marketplace.employer.edit-project', $project) }}" class="text-blue-600 hover:underline text-sm">Edit Project</a>
                </div>

                @forelse($proposals as $proposal)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($proposal->freelancer?->name ?? 'F') }}&background=1A73E8&color=fff&size=48"
                                 class="w-12 h-12 rounded-xl" alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $proposal->freelancer?->name ?? 'Freelancer' }}</p>
                                        <p class="text-gray-400 text-xs">{{ $proposal->freelancerProfile?->professional_title }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-bold text-gray-900">₹{{ number_format($proposal->proposed_amount) }}</span>
                                        <p class="text-xs text-gray-400">{{ $proposal->estimated_days }} days</p>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm mt-2 line-clamp-3">{{ $proposal->cover_letter }}</p>
                                <div class="flex gap-2 mt-3">
                                    @if($proposal->status === 'pending')
                                        <form action="{{ route('marketplace.employer.hire', $proposal) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition">Hire</button>
                                        </form>
                                        <form action="{{ route('marketplace.employer.reject-proposal', $proposal) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-500">{{ ucfirst($proposal->status) }}</span>
                                    @endif
                                    @if($proposal->freelancerProfile)
                                        <a href="{{ route('marketplace.freelancer.show', $proposal->freelancerProfile) }}" class="px-4 py-2 text-blue-600 text-sm hover:underline">View Profile</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
                        <div class="text-4xl mb-2">📭</div>
                        <p class="text-gray-500">No proposals yet. Share your project to attract freelancers.</p>
                    </div>
                @endforelse
            </div>

            {{-- Project Details --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Project Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Budget</dt>
                            <dd class="font-semibold">₹{{ number_format($project->budget_min) }} – ₹{{ number_format($project->budget_max) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Type</dt>
                            <dd class="font-semibold">{{ ucfirst(str_replace('_', ' ', $project->budget_type ?? 'fixed')) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Duration</dt>
                            <dd class="font-semibold">{{ $project->duration_in_days ? $project->duration_in_days . ' days' : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Posted</dt>
                            <dd class="font-semibold">{{ $project->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                    <div class="border-t border-gray-100 mt-4 pt-4">
                        <p class="text-xs text-gray-500 mb-2">Skills Required</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach(is_array($project->skills_required) ? $project->skills_required : json_decode($project->skills_required ?? '[]', true) ?? [] as $skill)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
