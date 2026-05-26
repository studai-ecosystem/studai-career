@extends('layouts.dashboard')
@section('title', 'Schedule Interview')

@push('styles')
<style>
.sch-page { padding:1.75rem; background:linear-gradient(135deg,#f0f4ff 0%,#f5f3ff 50%,#fff0fb 100%); min-height:100%; }
.sch-card { background:#fff; border-radius:1.25rem; border:1px solid rgba(99,102,241,.1); box-shadow:0 4px 24px rgba(99,102,241,.1); padding:2rem; }
.form-group { margin-bottom:1.25rem; }
.form-group label { display:block; font-size:.78rem; font-weight:700; color:#374151; margin-bottom:.4rem; }
.form-control { width:100%; padding:.6rem .875rem; border:1.5px solid #e5e7eb; border-radius:.75rem; font-size:.875rem; color:#1a1a2e; transition:border .15s; box-sizing:border-box; }
.form-control:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.btn-submit { padding:.7rem 2rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:.875rem; font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,.3); transition:transform .15s; }
.btn-submit:hover { transform:translateY(-1px); }
.cand-box { background:#f5f3ff; border-radius:1rem; padding:1rem 1.25rem; margin-bottom:1.75rem; display:flex; align-items:center; gap:1rem; }
.cand-av { width:3rem; height:3rem; border-radius:50%; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="sch-page">
<div style="max-width:760px;margin:0 auto;">

    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <a href="{{ route('employer.interviews.index') }}" style="color:#6b7280;text-decoration:none;font-size:.85rem;">&#8592; Interview Pipeline</a>
    </div>

    <h1 style="font-size:1.4rem;font-weight:800;color:#1a1a2e;margin:0 0 1.5rem;">
        Phase 2 — Schedule Interview
        @if($existingRound > 0)
        <span style="font-size:.9rem;font-weight:600;color:#8b5cf6;margin-left:.5rem;">(Round {{ $existingRound + 1 }})</span>
        @endif
    </h1>

    {{-- Candidate info --}}
    <div class="cand-box">
        <div class="cand-av">{{ strtoupper(substr($application->user->name ?? '?', 0, 1)) }}</div>
        <div>
            <div style="font-weight:700;color:#1a1a2e;">{{ $application->user->name ?? 'Guest' }}</div>
            <div style="font-size:.82rem;color:#6366f1;">{{ $application->job->title ?? 'N/A' }} &bull; {{ $application->user->email ?? '' }}</div>
        </div>
    </div>

    <div class="sch-card">
        @if($errors->any())
        <div style="background:#fff1f2;border:1px solid #fda4af;color:#be123c;padding:.875rem;border-radius:.75rem;margin-bottom:1.25rem;font-size:.85rem;">
            @foreach($errors->all() as $error)<div>&#10005; {{ $error }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('employer.interviews.store') }}">
            @csrf
            <input type="hidden" name="application_id" value="{{ $application->id }}">
            <input type="hidden" name="round" value="{{ $existingRound + 1 }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="form-group">
                    <label>Interview Type *</label>
                    <select name="interview_type" class="form-control" required>
                        <option value="video"     {{ old('interview_type','video') === 'video'     ? 'selected' : '' }}>Video Call</option>
                        <option value="phone"     {{ old('interview_type') === 'phone'     ? 'selected' : '' }}>Phone Screen</option>
                        <option value="onsite"    {{ old('interview_type') === 'onsite'    ? 'selected' : '' }}>Onsite</option>
                        <option value="technical" {{ old('interview_type') === 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="behavioral"{{ old('interview_type') === 'behavioral'? 'selected' : '' }}>Behavioral</option>
                        <option value="panel"     {{ old('interview_type') === 'panel'     ? 'selected' : '' }}>Panel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Duration (minutes) *</label>
                    <select name="duration_minutes" class="form-control" required>
                        <option value="30">30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60" selected>60 minutes</option>
                        <option value="90">90 minutes</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Date &amp; Time *</label>
                <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}" required min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
            </div>

            <div class="form-group">
                <label>Video Meeting Link</label>
                <input type="url" name="meeting_link" class="form-control" value="{{ old('meeting_link') }}" placeholder="https://meet.google.com/...">
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.3rem;">Leave blank to auto-generate an in-platform room link</div>
            </div>

            <div class="form-group">
                <label>Location (if onsite)</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Office address or room number">
            </div>

            <div class="form-group">
                <label>Interview Brief / Notes for Panel</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Key areas to focus on, background context, specific questions...">{{ old('notes') }}</textarea>
            </div>

            <div style="display:flex;gap:1rem;align-items:center;padding-top:.5rem;">
                <button type="submit" class="btn-submit">&#128197; Schedule Interview &amp; Notify Candidate</button>
                <a href="{{ route('employer.interviews.index') }}" style="color:#6b7280;font-size:.85rem;text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>

</div>
</div>
@endsection
