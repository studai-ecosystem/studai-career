@extends('layouts.dashboard')
@section('title', 'My Projects')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Projects</h1>
            <a href="{{ route('marketplace.employer.create-project') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                + Post Project
            </a>
        </div>

        <div class="space-y-4">
            @forelse($projects as $project)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold text-lg">
                        {{ strtoupper(substr($project->title, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $project->title }}</h3>
                                <p class="text-gray-400 text-xs mt-0.5">Posted {{ $project->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full font-semibold
                                {{ match($project->status) {
                                    'published'   => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed'   => 'bg-gray-100 text-gray-700',
                                    default       => 'bg-yellow-100 text-yellow-700',
                                } }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                            <span>💬 {{ $project->proposals_count ?? 0 }} proposals</span>
                            <span>₹{{ number_format($project->budget_min) }}–₹{{ number_format($project->budget_max) }}</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex flex-col gap-2">
                        <a href="{{ route('marketplace.employer.manage-project', $project) }}" class="text-sm text-blue-600 hover:underline">Manage</a>
                        <a href="{{ route('marketplace.employer.edit-project', $project) }}" class="text-sm text-gray-500 hover:underline">Edit</a>
                        <form action="{{ route('marketplace.employer.delete-project', $project) }}" method="POST" onsubmit="return confirm('Delete this project?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-400 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="font-bold text-gray-900 mb-2">No projects yet</h3>
                    <p class="text-gray-500 mb-4">Post your first project and receive proposals from talented freelancers.</p>
                    <a href="{{ route('marketplace.employer.create-project') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                        Post a Project
                    </a>
                </div>
            @endforelse
        </div>
        @if($projects->hasPages()) <div class="mt-6">{{ $projects->links() }}</div> @endif
    </div>
</div>
@endsection
