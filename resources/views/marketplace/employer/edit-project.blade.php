@extends('layouts.dashboard')
@section('title', 'Edit Project')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('marketplace.employer.manage-project', $project) }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors text-sm">
            ← Back to Project
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Edit Project</h1>
        <p class="text-gray-500 mb-8">Update your project details to attract the right freelancers.</p>

        <form id="edit-project-form" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project Title *</label>
                <input type="text" name="title" value="{{ $project->title }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a category</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ $project->category === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" rows="6" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none">{{ $project->description }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                <textarea name="requirements" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none">{{ $project->requirements }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget Type *</label>
                    <select name="budget_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="fixed" {{ ($project->budget_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                        <option value="hourly" {{ ($project->budget_type ?? '') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (days)</label>
                    <input type="number" name="duration_in_days" value="{{ $project->duration_in_days }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Budget (₹)</label>
                    <input type="number" name="budget_min" value="{{ $project->budget_min }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Budget (₹)</label>
                    <input type="number" name="budget_max" value="{{ $project->budget_max }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="updateProject()" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                    Save Changes
                </button>
                <a href="{{ route('marketplace.employer.manage-project', $project) }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
async function updateProject() {
    const form = document.getElementById('edit-project-form');
    const data = new FormData(form);
    const btn = event.target;
    btn.disabled = true; btn.textContent = 'Saving...';
    // Convert FormData to JSON, override method to PUT
    const obj = {};
    data.forEach((v, k) => { if (k !== '_method') obj[k] = v; });
    const res = await fetch('{{ route('marketplace.employer.update-project', $project) }}', {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(obj)
    });
    const json = await res.json();
    if (json.success) {
        window.location = '{{ route('marketplace.employer.manage-project', $project) }}';
    } else {
        alert(json.message ?? 'Failed to update.');
        btn.disabled = false; btn.textContent = 'Save Changes';
    }
}
</script>
@endsection
