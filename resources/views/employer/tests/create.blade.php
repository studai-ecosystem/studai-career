@extends('layouts.dashboard')
@section('title', 'Create Test — ' . ($test ? 'Edit' : 'New'))
@section('page-title', 'Test Questions')
@section('page-description', 'Set questions for ' . (\App\Models\HiringTest::STAGE_LABELS[$stage] ?? $stage))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('employer.jobs.show', $job->id) }}" class="text-gray-400 hover:text-gray-700">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
      <h1 class="text-xl font-bold text-gray-900">{{ $test ? 'Edit' : 'Create' }} — {{ \App\Models\HiringTest::STAGE_LABELS[$stage] ?? $stage }}</h1>
      <p class="text-sm text-gray-500">{{ $job->title }}</p>
    </div>
  </div>

  @if(session('success'))
  <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('employer.tests.store', [$job->id, $stage]) }}" id="test-form">
    @csrf

    {{-- Test Settings --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
      <h2 class="text-base font-semibold text-gray-900">Test Settings</h2>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Test Title *</label>
        <input type="text" name="title" value="{{ old('title', $test->title ?? '') }}" required
          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400"
          placeholder="e.g. Company Knowledge Assessment">
        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Instructions (shown to candidate)</label>
        <textarea name="instructions" rows="2"
          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400"
          placeholder="e.g. Answer all questions honestly. No external resources allowed.">{{ old('instructions', $test->instructions ?? '') }}</textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Pass Score (%)</label>
          <input type="number" name="pass_score" value="{{ old('pass_score', $test->pass_score ?? 60) }}" min="1" max="100"
            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Time Limit (minutes)</label>
          <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $test->time_limit_minutes ?? 30) }}" min="5" max="180"
            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
        </div>
      </div>
    </div>

    {{-- Questions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-base font-semibold text-gray-900">Questions <span id="q-count" class="text-gray-400 font-normal text-sm"></span></h2>
        <button type="button" onclick="addQuestion()" class="px-4 py-2 text-sm font-semibold text-white rounded-xl" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">+ Add Question</button>
      </div>
      <div id="questions-container" class="space-y-5">
        {{-- Pre-fill existing questions --}}
        @php $existing = old('questions', $test->questions ?? []); @endphp
        @foreach($existing as $qi => $q)
        <div class="q-block border border-gray-200 rounded-xl p-5" data-qi="{{ $qi }}">
          <div class="flex justify-between items-center mb-3">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Question {{ $qi + 1 }}</span>
            <button type="button" onclick="removeQuestion(this)" class="text-red-400 hover:text-red-600 text-xs font-semibold">✕ Remove</button>
          </div>
          <input type="text" name="questions[{{ $qi }}][question]" value="{{ $q['question'] ?? '' }}" required placeholder="Enter your question..."
            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400 mb-3">
          <div class="space-y-2 options-wrap">
            @foreach($q['options'] ?? ['','','',''] as $oi => $opt)
            <div class="flex items-center gap-2">
              <input type="radio" name="questions[{{ $qi }}][correct_index]" value="{{ $oi }}" {{ ($q['correct_index'] ?? -1) == $oi ? 'checked' : '' }} class="accent-violet-600">
              <input type="text" name="questions[{{ $qi }}][options][]" value="{{ $opt }}" required placeholder="Option {{ $oi + 1 }}"
                class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400">
            </div>
            @endforeach
          </div>
          <p class="text-xs text-gray-400 mt-2">☝️ Select the radio button next to the correct answer.</p>
        </div>
        @endforeach
      </div>
      @if(count($existing) === 0)
      <p class="text-sm text-gray-400 text-center py-8" id="empty-msg">Click "Add Question" to start building your test.</p>
      @endif
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="flex gap-3">
      <a href="{{ route('employer.jobs.show', $job->id) }}" class="flex-1 py-3 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl text-center hover:bg-gray-50 transition-colors">Cancel</a>
      <button type="submit" class="flex-1 py-3 text-white text-sm font-semibold rounded-xl" style="background:linear-gradient(135deg,#1A73E8,#6366f1);">Save Test Questions</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
let qIndex = {{ count($existing) }};
const existing = @json($existing);

function updateCount() {
  const blocks = document.querySelectorAll('.q-block');
  document.getElementById('q-count').textContent = '(' + blocks.length + ')';
  document.getElementById('empty-msg') && (document.getElementById('empty-msg').style.display = blocks.length ? 'none' : '');
}
updateCount();

function addQuestion() {
  const qi = qIndex++;
  const el = document.createElement('div');
  el.className = 'q-block border border-gray-200 rounded-xl p-5';
  el.dataset.qi = qi;
  el.innerHTML = `
    <div class="flex justify-between items-center mb-3">
      <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Question ${document.querySelectorAll('.q-block').length + 1}</span>
      <button type="button" onclick="removeQuestion(this)" class="text-red-400 hover:text-red-600 text-xs font-semibold">✕ Remove</button>
    </div>
    <input type="text" name="questions[${qi}][question]" required placeholder="Enter your question..."
      class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400 mb-3">
    <div class="space-y-2 options-wrap">
      ${[0,1,2,3].map(oi => `
        <div class="flex items-center gap-2">
          <input type="radio" name="questions[${qi}][correct_index]" value="${oi}" class="accent-violet-600">
          <input type="text" name="questions[${qi}][options][]" required placeholder="Option ${oi+1}"
            class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400">
        </div>`).join('')}
    </div>
    <p class="text-xs text-gray-400 mt-2">☝️ Select the radio button next to the correct answer.</p>`;
  document.getElementById('questions-container').appendChild(el);
  updateCount();
}

function removeQuestion(btn) {
  btn.closest('.q-block').remove();
  // Re-number labels
  document.querySelectorAll('.q-block').forEach((b,i) => {
    b.querySelector('span').textContent = 'Question ' + (i+1);
  });
  updateCount();
}
</script>
@endpush
@endsection
