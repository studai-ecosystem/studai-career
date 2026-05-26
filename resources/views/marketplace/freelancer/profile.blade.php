@extends('layouts.dashboard')
@section('title', 'My Freelancer Profile')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Freelancer Profile</h1>
            <a href="{{ route('marketplace.freelancer.dashboard') }}" class="text-blue-600 hover:underline text-sm">← Dashboard</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <form id="profileForm" class="space-y-6">
            @csrf

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Professional Title *</label>
                        <input type="text" name="professional_title" value="{{ $profile->professional_title ?? '' }}"
                               placeholder="e.g. Full Stack Laravel Developer"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Professional Bio *</label>
                        <textarea name="bio" rows="4" placeholder="Tell clients about your background and expertise..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $profile->bio ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Experience Level *</label>
                        <select name="experience_level" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="entry" {{ ($profile->experience_level ?? '') === 'entry' ? 'selected' : '' }}>Entry Level</option>
                            <option value="intermediate" {{ ($profile->experience_level ?? '') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="expert" {{ ($profile->experience_level ?? '') === 'expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability *</label>
                        <select name="availability" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="full_time" {{ ($profile->availability ?? '') === 'full_time' ? 'selected' : '' }}>Full-time</option>
                            <option value="part_time" {{ ($profile->availability ?? '') === 'part_time' ? 'selected' : '' }}>Part-time</option>
                            <option value="hourly" {{ ($profile->availability ?? '') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="not_available" {{ ($profile->availability ?? '') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Rates --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4">Rates & Availability</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate (₹)</label>
                        <input type="number" name="hourly_rate" value="{{ $profile->hourly_rate ?? '' }}"
                               placeholder="e.g. 1500"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hours/Week</label>
                        <input type="number" name="hours_per_week" value="{{ $profile->hours_per_week ?? '' }}"
                               placeholder="e.g. 40"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select name="currency" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="INR" {{ ($profile->currency ?? 'INR') === 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            <option value="USD" {{ ($profile->currency ?? '') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="available_for_remote" value="1" {{ ($profile->available_for_remote ?? true) ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-sm text-gray-700">Remote</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="available_for_onsite" value="1" {{ ($profile->available_for_onsite ?? false) ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-sm text-gray-700">On-site</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Skills --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4">Skills *</h2>
                <div id="skillsContainer" class="flex flex-wrap gap-2 mb-3">
                    @foreach(is_array($profile->skills ?? []) ? ($profile->skills ?? []) : json_decode($profile->skills ?? '[]', true) ?? [] as $skill)
                        <span class="skill-tag flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $skill }}
                            <button type="button" onclick="removeSkill(this)" class="text-blue-400 hover:text-blue-600 font-bold ml-1">×</button>
                            <input type="hidden" name="skills[]" value="{{ $skill }}">
                        </span>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <input type="text" id="skillInput" placeholder="Add a skill (e.g. Laravel, React)" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="addSkill()" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">Add</button>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="saveProfile()" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                    Save Profile
                </button>
                <a href="{{ route('marketplace.freelancer.dashboard') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function addSkill() {
    const input = document.getElementById('skillInput');
    const skill = input.value.trim();
    if (!skill) return;
    const container = document.getElementById('skillsContainer');
    const tag = document.createElement('span');
    tag.className = 'skill-tag flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm';
    tag.innerHTML = `${skill} <button type="button" onclick="removeSkill(this)" class="text-blue-400 hover:text-blue-600 font-bold ml-1">×</button><input type="hidden" name="skills[]" value="${skill}">`;
    container.appendChild(tag);
    input.value = '';
}
function removeSkill(btn) { btn.closest('.skill-tag').remove(); }
document.getElementById('skillInput').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addSkill(); }});

async function saveProfile() {
    const form = document.getElementById('profileForm');
    const data = new FormData(form);
    const btn = event.target;
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        const res = await fetch('{{ route('marketplace.freelancer.profile.update') }}', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        const json = await res.json();
        if (json.success) {
            btn.textContent = '✓ Saved!';
            btn.className = btn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-green-600');
        } else { alert(json.message); btn.disabled = false; btn.textContent = 'Save Profile'; }
    } catch(e) { alert('Error saving profile.'); btn.disabled = false; btn.textContent = 'Save Profile'; }
}
</script>
@endsection
