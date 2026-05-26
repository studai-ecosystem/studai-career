@extends('layouts.dashboard')

@section('page-title', 'Analytics')
@section('page-description', 'Track applications, hiring rates, and pipeline performance.')

@section('content')

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('employer.home') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm hover:shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hiring Analytics</h1>
            <p class="text-gray-500 text-sm mt-0.5">Last 12 months of activity</p>
        </div>
        <a href="{{ route('employer.home') }}" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
            ← Dashboard
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Applications</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalApps) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Shortlisted</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($shortlistedApps) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $conversionRates['shortlist_rate'] }}% shortlist rate</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Hired</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($hiredApps) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $conversionRates['hire_rate'] }}% hire rate</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg. Time to Hire</p>
            <p class="text-3xl font-bold text-purple-600">{{ $averageTimeToHire ? round($averageTimeToHire) : '–' }}</p>
            @if($averageTimeToHire)
                <p class="text-xs text-gray-400 mt-1">days</p>
            @endif
        </div>
    </div>

    {{-- Monthly Applications Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Monthly Applications (Last 12 Months)</h2>

        @php
            $maxCount = collect($monthlyData)->max('applications') ?: 1;
        @endphp

        <div class="flex items-end gap-2 h-48">
            @foreach($monthlyData as $month)
                @php
                    $height = $maxCount > 0 ? round(($month['applications'] / $maxCount) * 100) : 0;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1 group">
                    <span class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity font-medium">
                        {{ $month['applications'] }}
                    </span>
                    <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t-md transition-all cursor-default"
                         style="height: {{ max($height, 2) }}%; min-height: 4px;">
                    </div>
                    <span class="text-xs text-gray-400 truncate w-full text-center">
                        {{ \Illuminate\Support\Str::substr($month['month'], 0, 3) }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
            @foreach($monthlyData as $month)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ $month['month'] }}</span>
                    <span class="font-semibold text-gray-900">{{ $month['applications'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Two-column: Applications by Job Type + Conversion Funnel --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Applications by Employment Type --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Applications by Job Type</h2>
            @if(empty($applicationsByJobType))
                <p class="text-sm text-gray-400 py-6 text-center">No data yet</p>
            @else
                @php
                    $maxJobType = max(array_values($applicationsByJobType)) ?: 1;
                    $typeLabels = [
                        'full_time'  => 'Full Time',
                        'part_time'  => 'Part Time',
                        'contract'   => 'Contract',
                        'internship' => 'Internship',
                        'freelance'  => 'Freelance',
                    ];
                    $typeColors = [
                        'full_time'  => 'bg-blue-500',
                        'part_time'  => 'bg-indigo-400',
                        'contract'   => 'bg-purple-400',
                        'internship' => 'bg-pink-400',
                        'freelance'  => 'bg-orange-400',
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach($applicationsByJobType as $type => $count)
                        @php
                            $pct = round(($count / $maxJobType) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 font-medium">{{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</span>
                                <span class="text-gray-500">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="{{ $typeColors[$type] ?? 'bg-blue-400' }} h-2.5 rounded-full transition-all"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Conversion Funnel --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Hiring Funnel</h2>
            <div class="space-y-4">
                {{-- Applied --}}
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 font-medium">Applied</span>
                        <span class="text-gray-900 font-semibold">{{ number_format($totalApps) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width:100%"></div>
                    </div>
                </div>
                {{-- Shortlisted --}}
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 font-medium">Shortlisted</span>
                        <span class="text-gray-900 font-semibold">{{ number_format($shortlistedApps) }}
                            <span class="text-xs font-normal text-gray-400">({{ $conversionRates['shortlist_rate'] }}%)</span>
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-indigo-500 h-3 rounded-full"
                             style="width:{{ $totalApps > 0 ? round(($shortlistedApps / $totalApps) * 100) : 0 }}%"></div>
                    </div>
                </div>
                {{-- Hired --}}
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 font-medium">Hired</span>
                        <span class="text-gray-900 font-semibold">{{ number_format($hiredApps) }}
                            <span class="text-xs font-normal text-gray-400">({{ $conversionRates['hire_rate'] }}%)</span>
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full"
                             style="width:{{ $totalApps > 0 ? round(($hiredApps / $totalApps) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            @if($averageTimeToHire)
                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-sm text-gray-500">Average time to hire</span>
                    <span class="text-sm font-bold text-purple-600">{{ round($averageTimeToHire) }} days</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick links --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('employer.jobs.index') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            View Job Listings
        </a>
        <a href="{{ route('employer.interviews.index') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Manage Interviews
        </a>
        <a href="{{ route('employer.profile.show') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Company Profile
        </a>
    </div>

</div>
@endsection
