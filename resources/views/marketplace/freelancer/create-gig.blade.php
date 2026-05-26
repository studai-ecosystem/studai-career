@extends('layouts.dashboard')
@section('title', ($gig ? 'Edit Service' : 'Create Service') . ' - StudAI Hire')

@push('styles')
<style>
.pkg-panel { border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; }
.pkg-panel.active-tab { border-color: #1A73E8; }
.form-label { font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: 6px; display:block; }
.form-input { width:100%; padding: 10px 14px; border:1.5px solid #d1d5db; border-radius:10px; font-size:.875rem; transition:.15s; }
.form-input:focus { outline:none; border-color:#1A73E8; box-shadow:0 0 0 3px rgba(26,115,232,.1); }
textarea.form-input { resize:vertical; }
</style>
@endpush

@section('content')
<div class="min-h-screen" style="background:#f9f9f9;">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('marketplace.freelancer.gigs') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $gig ? 'Edit Service' : 'Create New Service' }}</h1>
            <p class="text-gray-500 text-sm">{{ $gig ? 'Update your service listing.' : 'List a service and let companies hire you directly.' }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 px-5 py-4 rounded-xl" style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;">
            <p class="font-semibold text-sm mb-1">Please fix the following:</p>
            <ul class="text-sm list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $gig ? route('marketplace.freelancer.update-gig', $gig) : route('marketplace.freelancer.store-gig') }}"
          method="POST" class="space-y-6">
        @csrf
        @if($gig)
            @method('PUT')
        @endif

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 text-base mb-5 pb-3 border-b border-gray-100">Basic Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Service Title * <span class="text-gray-400 font-normal">(min 15 chars — be specific!)</span></label>
                    <input type="text" name="title" value="{{ old('title', $gig?->title) }}" required minlength="15" maxlength="150"
                           class="form-input"
                           placeholder="e.g. 'I will build a full-stack React + Node.js web application'">
                    <p class="text-xs text-gray-400 mt-1">Start with "I will..." for best results. 15–150 characters.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Category *</label>
                        <select name="category" class="form-input" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $key => $cat)
                                <option value="{{ $key }}" {{ old('category',$gig?->category)===$key?'selected':'' }}>
                                    {{ $cat['icon'] }} {{ $cat['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="active" {{ old('status',$gig?->status??'active')==='active'?'selected':'' }}>✅ Active (visible to companies)</option>
                            <option value="draft" {{ old('status',$gig?->status)==='draft'?'selected':'' }}>📝 Draft (hidden)</option>
                            <option value="paused" {{ old('status',$gig?->status)==='paused'?'selected':'' }}>⏸ Paused</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Description * <span class="text-gray-400 font-normal">(min 50 chars)</span></label>
                    <textarea name="description" rows="6" required minlength="50" class="form-input"
                              placeholder="Describe your service in detail. What will you deliver? What makes you the right person? What technologies/tools do you use?">{{ old('description', $gig?->description) }}</textarea>
                </div>

                <div>
                    <label class="form-label">Tags <span class="text-gray-400 font-normal">(comma-separated)</span></label>
                    <input type="text" name="tags" value="{{ old('tags', implode(', ', $gig?->tags ?? [])) }}"
                           class="form-input" placeholder="Laravel, React, MySQL, API, Figma">
                </div>

                <div>
                    <label class="form-label">What do you need from the buyer?</label>
                    <textarea name="requirements" rows="3" class="form-input"
                              placeholder="e.g. Brand colors, logo files, content to include, API credentials, wireframes, target audience...">{{ old('requirements', $gig?->requirements) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Packages --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 text-base mb-2">Pricing Packages</h2>
            <p class="text-gray-500 text-sm mb-5">Define 3 packages (Basic, Standard, Premium) with different prices and features.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach(['basic'=>'Basic 🟢','standard'=>'Standard 🔵','premium'=>'Premium 🥇'] as $type => $label)
                    @php
                        $existingPkg = null;
                        if ($gig) {
                            foreach ($gig->packages as $p) {
                                if (($p['type'] ?? '') === $type) { $existingPkg = $p; break; }
                            }
                        }
                        $defaults = ['basic'=>['price'=>2999,'days'=>3,'rev'=>2],'standard'=>['price'=>7999,'days'=>7,'rev'=>5],'premium'=>['price'=>14999,'days'=>14,'rev'=>99]];
                        $d = $defaults[$type];
                    @endphp
                    <div class="border-2 rounded-xl p-4" style="border-color:{{ $type==='standard'?'#1A73E8':'#e5e7eb' }};">
                        <h3 class="font-bold text-gray-900 text-sm mb-3">{{ $label }}</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">Package Name</label>
                                <input type="text" name="pkg_{{ $type }}_title"
                                       value="{{ old("pkg_{$type}_title", $existingPkg['title'] ?? ucfirst($type)) }}"
                                       class="form-input" placeholder="{{ ucfirst($type) }} Package">
                            </div>
                            <div>
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" name="pkg_{{ $type }}_price" required min="100"
                                       value="{{ old("pkg_{$type}_price", $existingPkg['price'] ?? $d['price']) }}"
                                       class="form-input" placeholder="{{ $d['price'] }}">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="form-label">Delivery (days)</label>
                                    <input type="number" name="pkg_{{ $type }}_days" required min="1"
                                           value="{{ old("pkg_{$type}_days", $existingPkg['delivery_days'] ?? $d['days']) }}"
                                           class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Revisions</label>
                                    <input type="number" name="pkg_{{ $type }}_revisions" required min="0"
                                           value="{{ old("pkg_{$type}_revisions", $existingPkg['revisions'] ?? $d['rev']) }}"
                                           class="form-input">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Features <span class="text-gray-400 font-normal">(one per line)</span></label>
                                <textarea name="pkg_{{ $type }}_features" rows="4" class="form-input text-xs"
                                          placeholder="Source files&#10;Mobile responsive&#10;2 revisions&#10;Delivery in {{ $d['days'] }} days">{{ old("pkg_{$type}_features", implode("\n", $existingPkg['features'] ?? [])) }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('marketplace.freelancer.gigs') }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl text-white font-bold text-sm hover:opacity-90 transition"
                    style="background:#1A73E8;">
                {{ $gig ? 'Save Changes' : 'Publish Service' }}
            </button>
        </div>
    </form>

</div>
</div>
@endsection
