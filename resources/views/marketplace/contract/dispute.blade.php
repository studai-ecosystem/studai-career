@extends('layouts.dashboard')

@section('title', 'Dispute Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('marketplace.index') }}" class="hover:text-gray-700">Marketplace</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('marketplace.contracts.show', $contract) }}" class="hover:text-gray-700">Contract</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 font-medium">Dispute #{{ $dispute->id }}</li>
            </ol>
        </nav>

        <!-- Status Banner -->
        <div class="mb-6 rounded-xl border px-6 py-4
            {{ $dispute->status === 'open' ? 'bg-yellow-50 border-yellow-200' :
               ($dispute->status === 'resolved' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200') }}">
            <div class="flex items-center gap-3">
                @if($dispute->status === 'open')
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-800">Dispute Under Review</p>
                        <p class="text-sm text-yellow-700">Our team will review this dispute within 24–48 hours and contact both parties.</p>
                    </div>
                @elseif($dispute->status === 'resolved')
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800">Dispute Resolved</p>
                        <p class="text-sm text-green-700">This dispute has been resolved.</p>
                    </div>
                @else
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">Status: {{ ucfirst($dispute->status) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Dispute Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Dispute Details</h2>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reason</dt>
                            <dd class="mt-1 text-gray-900">{{ $dispute->reason ?? 'Not specified' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-gray-700 whitespace-pre-line">{{ $dispute->description }}</dd>
                        </div>

                        @if($dispute->evidence)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Evidence</dt>
                                <dd class="mt-1 text-gray-700 whitespace-pre-line">{{ $dispute->evidence }}</dd>
                            </div>
                        @endif

                        @if($dispute->disputed_amount)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Disputed Amount</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">&#8377;{{ number_format($dispute->disputed_amount) }}</dd>
                            </div>
                        @endif

                        @if($dispute->resolution_notes)
                            <div class="border-t pt-4">
                                <dt class="text-sm font-medium text-gray-500">Resolution Notes</dt>
                                <dd class="mt-1 text-gray-700">{{ $dispute->resolution_notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Contract Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Related Contract</h2>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">{{ $contract->project->title ?? 'Project' }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Contract value: &#8377;{{ number_format($contract->agreed_amount ?? 0) }}
                            </p>
                        </div>
                        <a href="{{ route('marketplace.contracts.show', $contract) }}"
                           class="text-sm text-indigo-600 font-medium hover:underline">View Contract</a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Parties -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">Parties Involved</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500">Raised By</p>
                            <p class="font-medium text-gray-900">{{ $dispute->raisedBy->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Against</p>
                            <p class="font-medium text-gray-900">{{ $dispute->against->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">Timeline</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Opened</span>
                            <span class="text-gray-900">{{ $dispute->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($dispute->review_started_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Review Started</span>
                                <span class="text-gray-900">{{ $dispute->review_started_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                        @if($dispute->resolved_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Resolved</span>
                                <span class="text-gray-900">{{ $dispute->resolved_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <a href="{{ route('marketplace.contracts.show', $contract) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        Back to Contract
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
