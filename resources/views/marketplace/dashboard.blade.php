@extends('layouts.dashboard')

@section('title', 'Marketplace Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Marketplace Dashboard</h1>
                <p class="text-gray-600 mt-1">Your hub for projects, contracts &amp; earnings</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('marketplace.projects') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    Browse Projects
                </a>
                @if($isFreelancer ?? false)
                    <a href="{{ route('marketplace.freelancer.proposals') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        My Proposals
                    </a>
                @else
                    <a href="{{ route('marketplace.employer.create-project') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        Post a Project
                    </a>
                @endif
            </div>
        </div>

        @if($isFreelancer ?? false)
            <!-- Freelancer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @php $stats = $freelancerStats ?? []; @endphp
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Earnings</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">&#8377;{{ number_format($stats['total_earnings'] ?? 0) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Active Contracts</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['active_contracts'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500">Pending Proposals</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['pending_proposals'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Success Rate</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['success_rate'] ?? 0 }}%</p>
                </div>
            </div>

            <!-- My Proposals -->
            @if(isset($myProposals) && $myProposals->count())
                <div class="bg-white rounded-xl shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Proposals</h2>
                        <a href="{{ route('marketplace.freelancer.proposals') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($myProposals->take(5) as $proposal)
                            <div class="px-6 py-4 flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $proposal->project->title ?? 'Project' }}</p>
                                    <p class="text-sm text-gray-500">&#8377;{{ number_format($proposal->proposed_amount) }} · Submitted {{ $proposal->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $proposal->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($proposal->status === 'hired' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($proposal->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- My Contracts -->
            @if(isset($myContracts) && $myContracts->count())
                <div class="bg-white rounded-xl shadow-sm mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Active Contracts</h2>
                        <a href="{{ route('marketplace.freelancer.contracts') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($myContracts->take(5) as $contract)
                            <div class="px-6 py-4 flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $contract->project->title ?? 'Contract' }}</p>
                                    <p class="text-sm text-gray-500">Started {{ $contract->started_at?->diffForHumans() ?? 'Recently' }}</p>
                                </div>
                                <a href="{{ route('marketplace.contracts.show', $contract) }}"
                                   class="text-sm text-indigo-600 font-medium hover:underline">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recommended Projects -->
            @if(isset($recommendedProjects) && $recommendedProjects->count())
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Recommended for You</h2>
                        <a href="{{ route('marketplace.projects') }}" class="text-sm text-indigo-600 hover:underline">Browse all</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($recommendedProjects as $project)
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('marketplace.project.show', $project) }}"
                                           class="font-medium text-gray-900 hover:text-indigo-600">{{ $project->title }}</a>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $project->description }}</p>
                                        <div class="flex gap-2 mt-2">
                                            @foreach(($project->skills_required ?? []) as $skill)
                                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs rounded-full">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right flex-shrink-0">
                                        <p class="font-semibold text-gray-900">&#8377;{{ number_format($project->budget_min) }}–{{ number_format($project->budget_max) }}</p>
                                        <p class="text-xs text-gray-400">{{ $project->project_type }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @else
            <!-- Employer View -->
            @if(isset($employerStats))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @php $stats = $employerStats ?? []; @endphp
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <p class="text-sm text-gray-500">Active Projects</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['active_projects'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-500">Open Proposals</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['open_proposals'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <p class="text-sm text-gray-500">Contracts Running</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['active_contracts'] ?? 0 }}</p>
                    </div>
                </div>

                @if(isset($myProjects) && $myProjects->count())
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">My Projects</h2>
                            <a href="{{ route('marketplace.employer.projects') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($myProjects as $project)
                                <div class="px-6 py-4 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('marketplace.employer.manage-project', $project) }}"
                                           class="font-medium text-gray-900 hover:text-indigo-600">{{ $project->title }}</a>
                                        <p class="text-sm text-gray-500">{{ $project->proposals_count ?? 0 }} proposals · &#8377;{{ number_format($project->budget_min) }}–{{ number_format($project->budget_max) }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $project->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- No projects yet — onboarding -->
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Post Your First Project</h2>
                    <p class="text-gray-500 mb-6">Connect with top freelance talent and get work done faster.</p>
                    <a href="{{ route('marketplace.employer.create-project') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                        Post a Project
                    </a>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
