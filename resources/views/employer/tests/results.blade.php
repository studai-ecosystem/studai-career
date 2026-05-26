@extends('layouts.dashboard')
@section('title', 'Test Results')
@section('page-title', 'Test Results')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('employer.jobs.show', $job->id) }}" class="text-gray-400 hover:text-gray-700">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="text-xl font-bold text-gray-900">Results — {{ \App\Models\HiringTest::STAGE_LABELS[$stage] ?? $stage }}</h1>
      <p class="text-sm text-gray-500">{{ $job->title }} &middot; Pass Score: {{ $test->pass_score }}%</p>
    </div>
  </div>

  <div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-gray-200 p-5 text-center">
      <div class="text-3xl font-bold text-gray-900">{{ $attempts->count() }}</div>
      <div class="text-sm text-gray-500 mt-1">Total Attempts</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5 text-center">
      <div class="text-3xl font-bold text-green-600">{{ $attempts->where('passed', true)->count() }}</div>
      <div class="text-sm text-gray-500 mt-1">Passed</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5 text-center">
      <div class="text-3xl font-bold text-red-500">{{ $attempts->where('passed', false)->count() }}</div>
      <div class="text-sm text-gray-500 mt-1">Failed</div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-900">Candidate Results</h2>
    </div>
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-50 text-left">
          <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Candidate</th>
          <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</th>
          <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Result</th>
          <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</th>
          <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($attempts as $attempt)
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-4">
            <div class="font-semibold text-gray-900">{{ $attempt->application->user->name ?? 'Unknown' }}</div>
            <div class="text-xs text-gray-500">{{ $attempt->application->user->email ?? '' }}</div>
          </td>
          <td class="px-5 py-4">
            <span class="text-lg font-bold {{ $attempt->score >= $test->pass_score ? 'text-green-600' : 'text-red-500' }}">{{ $attempt->score }}%</span>
          </td>
          <td class="px-5 py-4">
            @if($attempt->passed)
            <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">✅ Passed</span>
            @elseif($attempt->submitted_at)
            <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">❌ Failed</span>
            @else
            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">⏳ In Progress</span>
            @endif
          </td>
          <td class="px-5 py-4 text-gray-500 text-xs">{{ $attempt->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td>
          <td class="px-5 py-4">
            <a href="{{ route('employer.applicants.show', $attempt->application_id) }}" class="text-indigo-600 hover:underline text-xs font-semibold">View Application →</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No attempts yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
