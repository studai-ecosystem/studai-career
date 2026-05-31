@extends('layouts.dashboard')

@section('title', 'Create Resume')
@section('page-title', 'Create Resume')
@section('page-description', 'Build an ATS-optimized resume with AI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="resumeBuilder()">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center" :class="step >= 1 ? 'text-purple-600' : 'text-gray-400'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="step >= 1 ? 'bg-purple-600 text-white' : 'bg-gray-300'">
                    1
                </div>
                <span class="ml-3 font-medium">Choose Template</span>
            </div>
            <div class="flex-1 h-1 mx-4" :class="step >= 2 ? 'bg-purple-600' : 'bg-gray-300'"></div>
            
            <div class="flex items-center" :class="step >= 2 ? 'text-purple-600' : 'text-gray-400'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="step >= 2 ? 'bg-purple-600 text-white' : 'bg-gray-300'">
                    2
                </div>
                <span class="ml-3 font-medium">Basic Info</span>
            </div>
            <div class="flex-1 h-1 mx-4" :class="step >= 3 ? 'bg-purple-600' : 'bg-gray-300'"></div>
            
            <div class="flex items-center" :class="step >= 3 ? 'text-purple-600' : 'text-gray-400'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="step >= 3 ? 'bg-purple-600 text-white' : 'bg-gray-300'">
                    3
                </div>
                <span class="ml-3 font-medium">AI Enhancement</span>
            </div>
        </div>
    </div>

    <form action="{{ route('resume.store') }}" method="POST" @submit="handleSubmit">
        @csrf

        <!-- Step 1: Template Selection -->
        <div x-show="step === 1" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Choose Your Template</h2>
            
            {{-- Template Preview Modal --}}
            <div x-data="{ open: false, tpl: null }" @keydown.escape.window="open=false">
                <div x-show="open" x-transition.opacity style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.65);display:flex;align-items:center;justify-content:center;padding:24px;" @click.self="open=false">
                    <div style="background:#fff;border-radius:16px;width:100%;max-width:760px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow: none;">
                        {{-- Modal header --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid #E2E2E0;">
                            <div>
                                <h3 x-text="tpl?.name" style="font-size:18px;font-weight:700;color:#0C0C0C;margin:0"></h3>
                                <p x-text="tpl?.description" style="font-size:13px;color:#737373;margin:4px 0 0 0"></p>
                            </div>
                            <div style="display:flex;gap:10px;align-items:center;">
                                <label style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;background:#2D6CDF;color:#fff;font-size:13px;font-weight:700;border-radius:9px;cursor:pointer;border:none;">
                                    <input type="radio" name="template_id" :value="tpl?.id" x-model="formData.template_id" class="sr-only" @change="open=false">
                                    ✓ Use This Template
                                </label>
                                <button type="button" @click="open=false" style="background:#F0F0EE;border:none;border-radius:8px;padding:8px 12px;cursor:pointer;font-size:18px;color:#737373;">✕</button>
                            </div>
                        </div>
                        {{-- Resume preview --}}
                        <div style="overflow-y:auto;padding:24px;background:#F7F7F5;">
                            <div x-show="tpl" style="background:#fff;border-radius:8px;box-shadow: none;overflow:hidden;font-family:'Segoe UI',Arial,sans-serif;">
                                {{-- Dynamic preview rendered from Alpine tpl data --}}
                                <template x-if="tpl">
                                    <div>
                                        {{-- Header --}}
                                        <div :style="'background:'+tpl.primary+';padding:28px 32px;'">
                                            <div style="font-size:26px;font-weight:800;color:#fff;letter-spacing:-.3px;">Priya Sharma</div>
                                            <div :style="'font-size:14px;color:rgba(255,255,255,.8);margin-top:4px;font-weight:500;'" x-text="tpl.name + ' · Sample Resume'"></div>
                                            <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap;">
                                                <span style="font-size:12px;color:rgba(255,255,255,.75);">📧 priya@example.com</span>
                                                <span style="font-size:12px;color:rgba(255,255,255,.75);">📱 +91 98765 43210</span>
                                                <span style="font-size:12px;color:rgba(255,255,255,.75);">📍 Bengaluru, India</span>
                                                <span style="font-size:12px;color:rgba(255,255,255,.75);">🔗 linkedin.com/in/priya</span>
                                            </div>
                                        </div>
                                        {{-- Body --}}
                                        <div :style="tpl.cols==2 ? 'display:flex;' : ''">
                                            {{-- Sidebar (2-col only) --}}
                                            <template x-if="tpl.cols==2">
                                                <div :style="'width:220px;flex-shrink:0;padding:20px 18px;background:'+tpl.secondary+'15;border-right:1px solid #E2E2E0;'">
                                                    <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:8px;'">Skills</div>
                                                    <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:18px;">
                                                        <template x-for="s in ['Laravel','React','Python','MySQL','Docker','AWS','Git','REST API']">
                                                            <span :style="'background:'+tpl.primary+'18;color:'+tpl.primary+';font-size:10px;padding:3px 7px;border-radius:4px;font-weight:600;'" x-text="s"></span>
                                                        </template>
                                                    </div>
                                                    <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:8px;'">Education</div>
                                                    <div style="font-size:11px;font-weight:700;color:#0C0C0C;">B.Tech Computer Science</div>
                                                    <div style="font-size:11px;color:#737373;">IIT Bombay · 2019–2023</div>
                                                    <div style="font-size:11px;color:#737373;">CGPA: 8.7 / 10</div>
                                                    <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin:14px 0 8px;'">Languages</div>
                                                    <div style="font-size:11px;color:#3D3D3D;">English · Native</div>
                                                    <div style="font-size:11px;color:#3D3D3D;">Hindi · Fluent</div>
                                                </div>
                                            </template>
                                            {{-- Main content --}}
                                            <div style="flex:1;padding:20px 24px;">
                                                {{-- Summary --}}
                                                <div :style="'font-size:11px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:6px;'">Professional Summary</div>
                                                <p style="font-size:12px;color:#3D3D3D;line-height:1.6;margin-bottom:18px;">Full-stack software engineer with 4+ years building scalable SaaS products. Expert in Laravel, React, and cloud infrastructure. Led 3 cross-functional teams delivering ₹2Cr+ revenue impact. Passionate about clean code and developer experience.</p>
                                                {{-- Experience --}}
                                                <div :style="'font-size:11px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:10px;'">Work Experience</div>
                                                <div style="margin-bottom:14px;">
                                                    <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                                        <div style="font-size:13px;font-weight:700;color:#0C0C0C;">Senior Software Engineer</div>
                                                        <div style="font-size:11px;color:#737373;">Jan 2023 – Present</div>
                                                    </div>
                                                    <div style="font-size:12px;font-weight:600;color:#737373;margin-bottom:5px;">Infosys · Bengaluru, India</div>
                                                    <ul style="font-size:11px;color:#3D3D3D;padding-left:16px;line-height:1.7;margin:0;">
                                                        <li>Reduced API response time by 45% via Redis caching, improving UX for 50K+ users</li>
                                                        <li>Led migration of monolith to microservices, cutting deployment time from 2h to 12min</li>
                                                        <li>Mentored 5 junior engineers; introduced code-review culture reducing bugs by 30%</li>
                                                    </ul>
                                                </div>
                                                <div style="margin-bottom:14px;">
                                                    <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                                        <div style="font-size:13px;font-weight:700;color:#0C0C0C;">Software Engineer</div>
                                                        <div style="font-size:11px;color:#737373;">Jun 2020 – Dec 2022</div>
                                                    </div>
                                                    <div style="font-size:12px;font-weight:600;color:#737373;margin-bottom:5px;">Razorpay · Bengaluru, India</div>
                                                    <ul style="font-size:11px;color:#3D3D3D;padding-left:16px;line-height:1.7;margin:0;">
                                                        <li>Built payment reconciliation engine processing ₹500Cr/month with 99.99% accuracy</li>
                                                        <li>Integrated 12 banking partners via REST APIs; cut integration time by 60%</li>
                                                    </ul>
                                                </div>
                                                {{-- Skills (single col only) --}}
                                                <template x-if="tpl.cols!=2">
                                                    <div>
                                                        <div :style="'font-size:11px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:8px;'">Skills</div>
                                                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                                                            <template x-for="s in ['Laravel','PHP','React','TypeScript','Python','MySQL','PostgreSQL','Redis','Docker','AWS','Git','REST API','Tailwind CSS','Vue.js']">
                                                                <span :style="'background:'+tpl.primary+'15;color:'+tpl.primary+';font-size:11px;padding:4px 9px;border-radius:5px;font-weight:600;'" x-text="s"></span>
                                                            </template>
                                                        </div>
                                                        <div :style="'font-size:11px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:8px;'">Education</div>
                                                        <div style="font-size:12px;font-weight:700;color:#0C0C0C;">B.Tech Computer Science · IIT Bombay · 2019–2023 · CGPA 8.7</div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        {{-- Accent bottom --}}
                                        <div :style="'height:5px;background:'+tpl.accent+';'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($templates as $template)
                    @php
                        $colors = is_array($template->color_scheme) ? $template->color_scheme : (json_decode($template->color_scheme, true) ?? []);
                        $primary   = $colors['primary']   ?? '#0C0C0C';
                        $secondary = $colors['secondary'] ?? '#3D3D3D';
                        $accent    = $colors['accent']    ?? '#3D3D3D';
                        $layout    = is_array($template->layout_config) ? $template->layout_config : (json_decode($template->layout_config, true) ?? []);
                        $cols      = $layout['columns'] ?? 1;
                        $tplData   = json_encode([
                            'id'          => $template->id,
                            'name'        => $template->name,
                            'description' => $template->description,
                            'primary'     => $primary,
                            'secondary'   => $secondary,
                            'accent'      => $accent,
                            'cols'        => $cols,
                            'ats'         => $template->is_ats_friendly,
                            'premium'     => $template->is_premium,
                            'category'    => $template->category,
                        ]);
                    @endphp
                    <div class="relative group">
                        <label class="cursor-pointer block">
                            <input type="radio" name="template_id" value="{{ $template->id }}"
                                   x-model="formData.template_id" class="sr-only">
                            <div class="border-2 rounded-xl p-0 overflow-hidden transition-all"
                                 :class="formData.template_id == {{ $template->id }} ? 'border-purple-600 ring-2 ring-purple-300' : 'border-gray-200 hover:border-purple-300'">
                                {{-- Mini preview thumbnail --}}
                                <div class="h-52 relative overflow-hidden" style="background:#F7F7F5;">
                                    @if($template->preview_image)
                                        <img src="{{ asset('storage/' . $template->preview_image) }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col select-none" style="font-size:5.5px;">
                                            <div class="px-3 py-2 flex-shrink-0" style="background:{{ $primary }};">
                                                <div style="width:56px;height:6px;background:rgba(255,255,255,.9);border-radius:2px;margin-bottom:3px;"></div>
                                                <div style="width:40px;height:4px;background:rgba(255,255,255,.6);border-radius:2px;margin-bottom:4px;"></div>
                                                <div style="display:flex;gap:10px;">
                                                    <div style="width:30px;height:2.5px;background:rgba(255,255,255,.45);border-radius:1px;"></div>
                                                    <div style="width:30px;height:2.5px;background:rgba(255,255,255,.45);border-radius:1px;"></div>
                                                    <div style="width:30px;height:2.5px;background:rgba(255,255,255,.45);border-radius:1px;"></div>
                                                </div>
                                            </div>
                                            @if($cols == 2)
                                            <div style="display:flex;flex:1;overflow:hidden;">
                                                <div style="width:38%;padding:6px;background:{{ $secondary }}18;border-right:1px solid #E2E2E0;">
                                                    @for($i=0;$i<5;$i++)<div style="width:100%;height:{{ $i%3==0?'3px':'2px' }};background:{{ $i%3==0 ? $accent : '#C8C8C5' }};border-radius:1px;margin-bottom:3px;"></div>@endfor
                                                    <div style="width:100%;height:3px;background:{{ $accent }};border-radius:1px;margin:5px 0 3px;"></div>
                                                    @for($i=0;$i<3;$i++)<div style="width:100%;height:2px;background:#C8C8C5;border-radius:1px;margin-bottom:3px;"></div>@endfor
                                                </div>
                                                <div style="flex:1;padding:6px;">
                                                    @for($s=0;$s<3;$s++)<div style="width:50%;height:3px;background:{{ $primary }};border-radius:1px;margin-bottom:3px;"></div>@for($i=0;$i<3;$i++)<div style="width:100%;height:2px;background:#E2E2E0;border-radius:1px;margin-bottom:2px;"></div>@endfor<div style="margin-bottom:5px;"></div>@endfor
                                                </div>
                                            </div>
                                            @else
                                            <div style="flex:1;padding:8px;">
                                                @for($s=0;$s<4;$s++)<div style="width:35%;height:3px;background:{{ $primary }};border-radius:1px;margin-bottom:3px;"></div>@for($i=0;$i<3;$i++)<div style="width:100%;height:2px;background:#E2E2E0;border-radius:1px;margin-bottom:2px;"></div>@endfor<div style="margin-bottom:6px;"></div>@endfor
                                            </div>
                                            @endif
                                            <div style="height:3px;background:{{ $accent }};flex-shrink:0;"></div>
                                        </div>
                                    @endif
                                    {{-- Selected checkmark --}}
                                    <div x-show="formData.template_id == {{ $template->id }}" style="position:absolute;top:8px;right:8px;background:#2D6CDF;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;">✓</div>
                                    {{-- Category badge --}}
                                    <span style="position:absolute;top:8px;left:8px;background:{{ $primary }};color:#fff;font-size:8px;font-weight:700;padding:3px 7px;border-radius:4px;">{{ ucfirst($template->category ?? 'classic') }}</span>
                                    {{-- Preview button overlay --}}
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <button type="button"
                                                @click.prevent="tpl = {{ $tplData }}; open = true"
                                                style="background:#fff;color:#2D6CDF;font-size:12px;font-weight:700;padding:8px 16px;border-radius:8px;border:2px solid #2D6CDF;cursor:pointer;">
                                            👁 Full Preview
                                        </button>
                                    </div>
                                </div>
                                {{-- Card footer --}}
                                <div style="padding:12px 14px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;">
                                        <h3 style="font-size:14px;font-weight:700;color:#0C0C0C;margin:0;">{{ $template->name }}</h3>
                                        <div style="display:flex;gap:4px;">
                                            @if($template->is_ats_friendly)<span style="font-size:10px;background:#EDFAF2;color:#1E8E3E;padding:2px 7px;border-radius:4px;font-weight:700;">ATS ✓</span>@endif
                                            @if($template->is_premium)<span style="font-size:10px;background:#EBF2FF;color:#2D6CDF;padding:2px 7px;border-radius:4px;font-weight:700;">Pro</span>@endif
                                        </div>
                                    </div>
                                    <p style="font-size:12px;color:#737373;margin:4px 0 10px;">{{ Str::limit($template->description, 60) }}</p>
                                    <button type="button"
                                            @click.prevent="tpl = {{ $tplData }}; open = true"
                                            style="width:100%;padding:7px;border:1.5px solid #BFCFEE;border-radius:7px;background:#EBF2FF;color:#2D6CDF;font-size:12px;font-weight:600;cursor:pointer;">
                                        👁 See Full Resume Preview
                                    </button>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
            </div>{{-- end x-data modal wrapper --}}

            <div class="mt-6 flex justify-end">
                <button type="button" @click="nextStep" class="btn btn-primary">
                    Continue <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Basic Information -->
        <div x-show="step === 2" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resume Title *</label>
                    <input type="text" name="title" x-model="formData.title" 
                           class="form-input w-full" placeholder="e.g., Software Engineer Resume" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="full_name" x-model="formData.full_name" 
                           class="form-input w-full" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" x-model="formData.email" 
                           class="form-input w-full" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" x-model="formData.phone" 
                           class="form-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" x-model="formData.location" 
                           class="form-input w-full" placeholder="City, Country">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" x-model="formData.linkedin_url" 
                           class="form-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">GitHub URL</label>
                    <input type="url" name="github_url" x-model="formData.github_url" 
                           class="form-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio URL</label>
                    <input type="url" name="portfolio_url" x-model="formData.portfolio_url" 
                           class="form-input w-full">
                </div>
            </div>

            @if($targetJob)
                <input type="hidden" name="target_job_id" value="{{ $targetJob->id }}">
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        This resume will be optimized for: <strong>{{ $targetJob->title }}</strong> at {{ $targetJob->company->name }}
                    </p>
                </div>
            @endif

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep" class="btn btn-outline">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back
                </button>
                <button type="button" @click="nextStep" class="btn btn-primary">
                    Continue <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: AI Enhancement -->
        <div x-show="step === 3" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">AI Enhancement</h2>
            
            <div class="space-y-6">
                <!-- AI Skills Generation -->
                <div class="border-2 border-indigo-200 rounded-lg p-6 bg-gradient-to-br from-indigo-50 to-blue-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                                <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">AI Skills Suggestion</h3>
                            <p class="text-gray-600 mt-1">Tell us your job role and AI will suggest relevant skills for your resume</p>
                            <div class="mt-3 flex gap-2">
                                <input type="text" x-model="skillsJobRole" placeholder="e.g. Full Stack Developer, Data Scientist..."
                                    class="form-input flex-1 text-sm" @keydown.enter.prevent="generateSkills()">
                                <button type="button" @click="generateSkills()"
                                    :disabled="skillsLoading || !skillsJobRole.trim()"
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2">
                                    <svg x-show="skillsLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span x-text="skillsLoading ? 'Generating...' : '✨ Suggest Skills'"></span>
                                </button>
                            </div>

                            <!-- Skills output -->
                            <div x-show="suggestedSkills.length > 0" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Click to add skills to your resume:</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="skill in suggestedSkills" :key="skill">
                                        <button type="button"
                                            @click="toggleSkill(skill)"
                                            :class="selectedSkills.includes(skill) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-400'"
                                            class="px-3 py-1 rounded-full border text-sm font-medium transition-colors">
                                            <span x-text="skill"></span>
                                            <span x-show="selectedSkills.includes(skill)" class="ml-1">✓</span>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-500 mt-2" x-show="selectedSkills.length > 0">
                                    <span x-text="selectedSkills.length"></span> skill(s) selected — these will be saved with your resume
                                </p>
                            </div>
                            <!-- Hidden input carries selected skills -->
                            <input type="hidden" name="ai_skills" :value="JSON.stringify(selectedSkills)">
                        </div>
                    </div>
                </div>

                <!-- Option 1: Generate from Profile -->
                <div class="border-2 border-purple-200 rounded-lg p-6 bg-gradient-to-br from-purple-50 to-pink-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                                <i data-lucide="sparkles" class="w-6 h-6 text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">Generate AI Professional Summary</h3>
                            <p class="text-gray-600 mt-1">Let AI create a compelling professional summary based on your profile</p>
                            <label class="flex items-center mt-3">
                                <input type="checkbox" x-model="generateAISummary" class="form-checkbox text-purple-600">
                                <span class="ml-2 text-sm text-gray-700">Generate professional summary with AI</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Manual Summary (Alternative) -->
                <div x-show="!generateAISummary" class="border-2 border-gray-200 rounded-lg p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Professional Summary</label>
                    <textarea name="professional_summary" x-model="formData.professional_summary" 
                              rows="4" class="form-input w-full" 
                              placeholder="Write a brief summary of your professional background..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Or leave blank to generate with AI later</p>
                </div>

                <!-- AI Features Included -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-3">Included AI Features:</h4>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                            AI-powered professional summary generation
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                            Automatic skills extraction from experience
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                            Achievement quantification suggestions
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                            ATS optimization and keyword matching
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                            Job-specific customization (when targeting a role)
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep" class="btn btn-outline">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back
                </button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span x-show="!loading">Create Resume</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function resumeBuilder() {
    return {
        step: 1,
        loading: false,
        generateAISummary: true,
        skillsJobRole: '',
        skillsLoading: false,
        suggestedSkills: [],
        selectedSkills: [],
        formData: {
            template_id: {{ $templates->first()->id ?? 'null' }},
            title: '',
            full_name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}',
            phone: '{{ auth()->user()->profile?->phone ?? "" }}',
            location: '{{ auth()->user()->profile?->location ?? "" }}',
            linkedin_url: '',
            github_url: '',
            portfolio_url: '',
            professional_summary: '',
        },
        
        nextStep() {
            if (this.validateStep()) {
                this.step++;
            }
        },
        
        prevStep() {
            this.step--;
        },
        
        validateStep() {
            if (this.step === 1 && !this.formData.template_id) {
                alert('Please select a template');
                return false;
            }
            if (this.step === 2) {
                if (!this.formData.title || !this.formData.full_name || !this.formData.email) {
                    alert('Please fill in all required fields');
                    return false;
                }
            }
            return true;
        },
        
        handleSubmit(e) {
            this.loading = true;
            // Form will submit normally
        },

        async generateSkills() {
            if (!this.skillsJobRole.trim()) return;
            this.skillsLoading = true;
            this.suggestedSkills = [];
            try {
                const res = await fetch('/resume/ai/suggest-skills', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ job_role: this.skillsJobRole })
                });
                const data = await res.json();
                if (data.skills) {
                    this.suggestedSkills = data.skills;
                    // Pre-select all by default
                    this.selectedSkills = [...data.skills];
                } else {
                    alert(data.error || 'Failed to generate skills');
                }
            } catch (e) {
                alert('Network error. Please try again.');
            } finally {
                this.skillsLoading = false;
            }
        },

        toggleSkill(skill) {
            if (this.selectedSkills.includes(skill)) {
                this.selectedSkills = this.selectedSkills.filter(s => s !== skill);
            } else {
                this.selectedSkills.push(skill);
            }
        }
    }
}
</script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
