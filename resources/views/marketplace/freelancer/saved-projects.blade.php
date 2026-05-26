@extends('layouts.dashboard')
@section('title', 'Saved Projects')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Saved Projects</h1>
        <div class="space-y-4">
            @forelse($savedProjects as $saved)
                @php $project = $saved->project; @endphp
                <a href="{{ route('marketplace.project.show', $project) }}"
                   class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-blue-300 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $project->title }}</h3>
                            <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach(array_slice($project->skills_required ?? [], 0, 3) as $skill)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-bold text-gray-900">₹{{ number_format($project->budget_min ?? 0) }}+</span>
                            <p class="text-gray-400 text-xs mt-1">Saved {{ $saved->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-5xl mb-4">🔖</div>
                    <h3 class="font-bold text-gray-900 mb-2">No saved projects</h3>
                    <p class="text-gray-500 mb-4">Save interesting projects to revisit them later.</p>
                    <a href="{{ route('marketplace.projects') }}" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">Browse Projects</a>
                </div>
            @endforelse
        </div>
        @if($savedProjects->hasPages()) <div class="mt-6">{{ $savedProjects->links() }}</div> @endif
    </div>
</div>
@endsection
