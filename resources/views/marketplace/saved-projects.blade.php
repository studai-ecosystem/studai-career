@extends('layouts.dashboard')
@section('title', 'Saved Projects')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Saved Projects</h1>
            <a href="{{ route('marketplace.projects') }}" class="text-blue-600 hover:underline text-sm">Browse Projects</a>
        </div>

        <div class="space-y-4">
            @forelse($savedProjects as $saved)
                @php $project = $saved->project; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold text-lg">
                        {{ strtoupper(substr($project->title ?? 'P', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('marketplace.project.show', $project) }}" class="font-semibold text-gray-900 hover:text-blue-600">
                                    {{ $project->title }}
                                </a>
                                <p class="text-gray-400 text-xs mt-0.5">
                                    by {{ $project->employer?->name }} · Saved {{ $saved->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="shrink-0 font-bold text-gray-900">₹{{ number_format($project->budget_min ?? 0) }}+</span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice(is_array($project->skills_required) ? $project->skills_required : json_decode($project->skills_required ?? '[]', true) ?? [], 0, 3) as $skill)
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    <form action="{{ route('marketplace.freelancer.toggle-save-project', $project) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" title="Remove from saved" class="text-gray-400 hover:text-red-400 transition text-xl">🔖</button>
                    </form>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">🔖</div>
                    <h3 class="font-bold text-gray-900 mb-2">No saved projects</h3>
                    <p class="text-gray-500 mb-4">Save projects you're interested in to revisit them later.</p>
                    <a href="{{ route('marketplace.projects') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                        Browse Projects
                    </a>
                </div>
            @endforelse
        </div>
        @if($savedProjects->hasPages()) <div class="mt-6">{{ $savedProjects->links() }}</div> @endif
    </div>
</div>
@endsection
