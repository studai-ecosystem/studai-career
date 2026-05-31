<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resume->full_name ?? "Resume" }} - Resume Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $template = $resume->template;
        $colors = $template->color_scheme ?? ['primary'=>'#0C0C0C','secondary'=>'#3D3D3D','accent'=>'#3D3D3D'];
        $primary   = $colors['primary']   ?? '#0C0C0C';
        $secondary = $colors['secondary'] ?? '#3D3D3D';
        $accent    = $colors['accent']    ?? '#3D3D3D';
        $slug      = $template->slug ?? 'professional-classic';
        // Layout variant
        $isTwoCol  = in_array($slug, ['modern-tech','creative-portfolio','healthcare-professional']);
        $isMinimal = $slug === 'minimalist';
    @endphp
    <style>
        :root {
            --c-primary:   {{ $primary }};
            --c-secondary: {{ $secondary }};
            --c-accent:    {{ $accent }};
        }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .resume-page { box-shadow: none !important; margin: 0 !important; }
        }
        .section-title {
            color: var(--c-primary);
            border-bottom: 2px solid var(--c-primary);
            padding-bottom: 4px;
            margin-bottom: 12px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 500;
            background: color-mix(in srgb, var(--c-primary) 12%, white);
            color: var(--c-primary);
            border: 1px solid color-mix(in srgb, var(--c-primary) 25%, white);
        }
        .timeline-dot::before {
            content: '';
            width: 10px; height: 10px;
            border-radius: 50%;
            background: var(--c-primary);
            display: block;
            margin-right: 8px;
            margin-top: 5px;
            flex-shrink: 0;
        }
        .accent-bar { background: var(--c-primary); }
        a { color: var(--c-primary); }
        .header-bg { background-color: var(--c-primary); }
        .header-bg-soft { background-color: color-mix(in srgb, var(--c-primary) 8%, white); border-bottom: 3px solid var(--c-primary); }
    </style>
</head>
<body class="bg-gray-200">

<!-- Action Bar (no-print) -->
<div class="no-print sticky top-0 z-50 bg-white border-b shadow-sm">
    <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <h1 class="text-base font-semibold text-gray-800">{{ $resume->title }}</h1>
            <span class="text-xs px-2 py-1 rounded-full font-medium" style="background:color-mix(in srgb,var(--c-primary) 12%,white);color:var(--c-primary)">{{ $template->name }}</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-700 text-white text-sm rounded-md hover:bg-gray-800"><i class="fas fa-print mr-1"></i> Print</button>
            <a href="{{ route('resume.export.pdf', $resume) }}" class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
            <a href="{{ route('resume.edit', $resume) }}" class="px-4 py-2 text-white text-sm rounded-md hover:opacity-90" style="background:var(--c-primary)"><i class="fas fa-edit mr-1"></i> Edit</a>
        </div>
    </div>
</div>

<!-- Resume Page -->
<div class="max-w-4xl mx-auto my-8 resume-page bg-white shadow-2xl print:shadow-none print:my-0">

@if($isTwoCol)
{{-- ============ TWO-COLUMN LAYOUT (Modern Tech / Creative / Healthcare) ============ --}}
<div class="flex min-h-full">
    <!-- LEFT SIDEBAR -->
    <div class="w-72 flex-shrink-0 p-6 text-white" style="background:var(--c-primary)">
        <!-- Avatar / Initials -->
        <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4" style="background:rgba(255,255,255,.2)">
            {{ strtoupper(substr($resume->full_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strrchr($resume->full_name ?? ' U', ' '), 1, 1)) }}
        </div>
        <h1 class="text-xl font-bold text-center mb-1">{{ $resume->full_name }}</h1>
        @if($resume->professional_summary)
        <p class="text-xs text-white/80 text-center mb-5 leading-relaxed">{{ Str::limit($resume->professional_summary, 200) }}</p>
        @endif

        <!-- Contact -->
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest mb-2 opacity-70">Contact</p>
            @if($resume->email)<p class="text-xs mb-1"><i class="fas fa-envelope w-4"></i> {{ $resume->email }}</p>@endif
            @if($resume->phone)<p class="text-xs mb-1"><i class="fas fa-phone w-4"></i> {{ $resume->phone }}</p>@endif
            @if($resume->location)<p class="text-xs mb-1"><i class="fas fa-map-marker-alt w-4"></i> {{ $resume->location }}</p>@endif
            @if($resume->linkedin_url)<p class="text-xs mb-1 truncate"><i class="fab fa-linkedin w-4"></i> LinkedIn</p>@endif
            @if($resume->github_url)<p class="text-xs mb-1 truncate"><i class="fab fa-github w-4"></i> GitHub</p>@endif
            @if($resume->portfolio_url)<p class="text-xs mb-1 truncate"><i class="fas fa-globe w-4"></i> Portfolio</p>@endif
        </div>

        <!-- Skills in Sidebar -->
        @php $flatSkills = $resume->flat_skills; @endphp
        @if(count($flatSkills) > 0)
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest mb-2 opacity-70">Skills</p>
            <div class="flex flex-wrap gap-1">
                @foreach($flatSkills as $skill)
                <span class="text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,.15)">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Languages in Sidebar -->
        @if($resume->languages && count($resume->languages) > 0)
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest mb-2 opacity-70">Languages</p>
            @foreach($resume->languages as $lang)
            <div class="flex justify-between text-xs mb-1">
                <span>{{ $lang['name'] ?? '' }}</span>
                <span class="opacity-70">{{ $lang['proficiency'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Certifications in Sidebar -->
        @if($resume->certifications && count($resume->certifications) > 0)
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest mb-2 opacity-70">Certifications</p>
            @foreach($resume->certifications as $cert)
            <div class="mb-2">
                <p class="text-xs font-semibold leading-tight">{{ $cert['name'] ?? '' }}</p>
                <p class="text-xs opacity-70">{{ $cert['issuer'] ?? '' }} {{ $cert['issue_date'] ? '• '.$cert['issue_date'] : '' }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- RIGHT MAIN CONTENT -->
    <div class="flex-1 p-8">
        <!-- Experience -->
        @if($resume->experience && count($resume->experience) > 0)
        <div class="mb-6">
            <h2 class="section-title">Work Experience</h2>
            @foreach($resume->experience as $exp)
            <div class="mb-4">
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $exp['position'] ?? '' }}</h3>
                        <p class="text-sm font-semibold" style="color:var(--c-primary)">{{ $exp['company'] ?? '' }}@if($exp['location'] ?? false) — {{ $exp['location'] }}@endif</p>
                    </div>
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $exp['start_date'] ?? '' }}@if($exp['end_date'] ?? false) – {{ $exp['end_date'] }}@endif</span>
                </div>
                @if($exp['employment_type'] ?? false)<span class="text-xs text-gray-500 italic">{{ $exp['employment_type'] }}</span>@endif
                @if($exp['description'] ?? false)<p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $exp['description'] }}</p>@endif
                @if(!empty($exp['achievements']) && is_array($exp['achievements']))
                <ul class="mt-1 space-y-0.5">
                    @foreach($exp['achievements'] as $ach)
                    @if(trim($ach))<li class="text-sm text-gray-700 flex items-start gap-1.5"><span class="mt-1.5 w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:var(--c-primary)"></span>{{ $ach }}</li>@endif
                    @endforeach
                </ul>
                @endif
                @if($exp['technologies'] ?? false)<p class="text-xs text-gray-500 mt-1 italic">Technologies: {{ $exp['technologies'] }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Education -->
        @if($resume->education && count($resume->education) > 0)
        <div class="mb-6">
            <h2 class="section-title">Education</h2>
            @foreach($resume->education as $edu)
            <div class="mb-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $edu['degree'] ?? '' }}</h3>
                        <p class="text-sm font-medium" style="color:var(--c-primary)">{{ $edu['institution'] ?? '' }}@if($edu['location'] ?? false), {{ $edu['location'] }}@endif</p>
                        @if($edu['field'] ?? false)<p class="text-sm text-gray-600">{{ $edu['field'] }}</p>@endif
                        @if($edu['gpa'] ?? false)<p class="text-xs text-gray-500">GPA/Score: {{ $edu['gpa'] }}</p>@endif
                        @if($edu['honors'] ?? false)<p class="text-xs text-gray-500">{{ $edu['honors'] }}</p>@endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $edu['start_year'] ?? '' }}{{ ($edu['start_year'] ?? false) && ($edu['end_year'] ?? false) ? ' – ' : '' }}{{ $edu['end_year'] ?? '' }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Projects -->
        @if($resume->projects && count($resume->projects) > 0)
        <div class="mb-6">
            <h2 class="section-title">Projects</h2>
            @foreach($resume->projects as $proj)
            <div class="mb-3">
                <div class="flex justify-between items-start">
                    <h3 class="font-bold text-gray-900">{{ $proj['name'] ?? '' }}</h3>
                    @if(($proj['start_date'] ?? false) || ($proj['end_date'] ?? false))
                    <span class="text-xs text-gray-500">{{ $proj['start_date'] ?? '' }}{{ ($proj['start_date'] ?? false) && ($proj['end_date'] ?? false) ? ' – ' : '' }}{{ $proj['end_date'] ?? '' }}</span>
                    @endif
                </div>
                @if($proj['description'] ?? false)<p class="text-sm text-gray-700 leading-relaxed">{{ $proj['description'] }}</p>@endif
                @if($proj['technologies'] ?? false)<p class="text-xs text-gray-500 mt-0.5 italic">Stack: {{ $proj['technologies'] }}</p>@endif
                <div class="flex gap-3 mt-0.5">
                    @if($proj['url'] ?? false)<a href="{{ $proj['url'] }}" class="text-xs underline" target="_blank">Live Demo</a>@endif
                    @if($proj['github_url'] ?? false)<a href="{{ $proj['github_url'] }}" class="text-xs underline" target="_blank">GitHub</a>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Achievements -->
        @if($resume->achievements && count($resume->achievements) > 0)
        <div class="mb-6">
            <h2 class="section-title">Achievements & Awards</h2>
            @foreach($resume->achievements as $ach)
            <div class="mb-2">
                <div class="flex justify-between items-start">
                    <h3 class="font-semibold text-gray-900">{{ $ach['title'] ?? '' }}</h3>
                    @if($ach['date'] ?? false)<span class="text-xs text-gray-500">{{ $ach['date'] }}</span>@endif
                </div>
                @if($ach['issuer'] ?? false)<p class="text-sm" style="color:var(--c-primary)">{{ $ach['issuer'] }}</p>@endif
                @if($ach['description'] ?? false)<p class="text-sm text-gray-700">{{ $ach['description'] }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Volunteer -->
        @if($resume->volunteer_work && count($resume->volunteer_work) > 0)
        <div class="mb-6">
            <h2 class="section-title">Volunteer & Extra-Curricular</h2>
            @foreach($resume->volunteer_work as $vol)
            <div class="mb-2">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $vol['role'] ?? '' }}</h3>
                        <p class="text-sm" style="color:var(--c-primary)">{{ $vol['organization'] ?? '' }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $vol['start_date'] ?? '' }}{{ ($vol['start_date'] ?? false) && ($vol['end_date'] ?? false) ? ' – ' : '' }}{{ $vol['end_date'] ?? '' }}</span>
                </div>
                @if($vol['description'] ?? false)<p class="text-sm text-gray-700">{{ $vol['description'] }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@else
{{-- ============ SINGLE-COLUMN LAYOUT (Classic / Executive / Minimalist / Academic / Finance) ============ --}}

<!-- Header -->
@if($isMinimal)
<div class="px-10 pt-10 pb-6 border-b border-gray-200">
    <h1 class="text-3xl font-light tracking-widest text-gray-900 uppercase mb-2">{{ $resume->full_name }}</h1>
    <div class="text-sm text-gray-500 flex flex-wrap gap-x-4 gap-y-1">
        @if($resume->email)<span>{{ $resume->email }}</span>@endif
        @if($resume->phone)<span>{{ $resume->phone }}</span>@endif
        @if($resume->location)<span>{{ $resume->location }}</span>@endif
        @if($resume->linkedin_url)<span>LinkedIn</span>@endif
        @if($resume->github_url)<span>GitHub</span>@endif
    </div>
</div>
@else
<div class="header-bg-soft px-10 pt-8 pb-6">
    <h1 class="text-4xl font-bold mb-2" style="color:var(--c-primary)">{{ $resume->full_name }}</h1>
    <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-gray-600">
        @if($resume->email)<span><i class="fas fa-envelope mr-1" style="color:var(--c-primary)"></i>{{ $resume->email }}</span>@endif
        @if($resume->phone)<span><i class="fas fa-phone mr-1" style="color:var(--c-primary)"></i>{{ $resume->phone }}</span>@endif
        @if($resume->location)<span><i class="fas fa-map-marker-alt mr-1" style="color:var(--c-primary)"></i>{{ $resume->location }}</span>@endif
        @if($resume->linkedin_url)<span><i class="fab fa-linkedin mr-1" style="color:var(--c-primary)"></i>LinkedIn</span>@endif
        @if($resume->github_url)<span><i class="fab fa-github mr-1" style="color:var(--c-primary)"></i>GitHub</span>@endif
        @if($resume->portfolio_url)<span><i class="fas fa-globe mr-1" style="color:var(--c-primary)"></i>Portfolio</span>@endif
    </div>
</div>
@endif

<div class="px-10 py-6 space-y-6">
    <!-- Professional Summary -->
    @if($resume->professional_summary)
    <div>
        <h2 class="section-title">Professional Summary</h2>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $resume->professional_summary }}</p>
    </div>
    @endif

    <!-- Skills -->
    @if(count($flatSkills ?? $resume->flat_skills) > 0)
    @php $flatSkills = $flatSkills ?? $resume->flat_skills; @endphp
    <div>
        <h2 class="section-title">Skills</h2>
        <div class="flex flex-wrap gap-1.5">
            @foreach($flatSkills as $skill)
            <span class="badge">{{ $skill }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Work Experience -->
    @if($resume->experience && count($resume->experience) > 0)
    <div>
        <h2 class="section-title">Work Experience</h2>
        <div class="space-y-4">
            @foreach($resume->experience as $exp)
            <div>
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $exp['position'] ?? '' }}</h3>
                        <p class="text-sm font-semibold" style="color:var(--c-primary)">{{ $exp['company'] ?? '' }}@if($exp['location'] ?? false) · {{ $exp['location'] }}@endif</p>
                    </div>
                    <div class="text-right text-xs text-gray-500 flex-shrink-0 ml-4">
                        <div>{{ $exp['start_date'] ?? '' }}@if($exp['end_date'] ?? false) – {{ $exp['end_date'] }}@endif</div>
                        @if($exp['employment_type'] ?? false)<div class="italic">{{ $exp['employment_type'] }}</div>@endif
                    </div>
                </div>
                @if($exp['description'] ?? false)<p class="text-sm text-gray-700 leading-relaxed">{{ $exp['description'] }}</p>@endif
                @if(!empty($exp['achievements']) && is_array($exp['achievements']))
                <ul class="mt-1.5 space-y-1 pl-3">
                    @foreach($exp['achievements'] as $ach)
                    @if(trim($ach))<li class="text-sm text-gray-700 flex items-start gap-2"><span class="mt-2 w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:var(--c-primary)"></span><span>{{ $ach }}</span></li>@endif
                    @endforeach
                </ul>
                @endif
                @if($exp['technologies'] ?? false)<p class="text-xs text-gray-500 mt-1.5 italic">Technologies: {{ $exp['technologies'] }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Education -->
    @if($resume->education && count($resume->education) > 0)
    <div>
        <h2 class="section-title">Education</h2>
        <div class="space-y-3">
            @foreach($resume->education as $edu)
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900">{{ $edu['degree'] ?? '' }}</h3>
                    <p class="text-sm font-semibold" style="color:var(--c-primary)">{{ $edu['institution'] ?? '' }}@if($edu['location'] ?? false), {{ $edu['location'] }}@endif</p>
                    @if($edu['field'] ?? false)<p class="text-sm text-gray-600">{{ $edu['field'] }}</p>@endif
                    @if($edu['gpa'] ?? false)<p class="text-xs text-gray-500">Score/GPA: {{ $edu['gpa'] }}@if($edu['honors'] ?? false) · {{ $edu['honors'] }}@endif</p>@endif
                    @if($edu['coursework'] ?? false)<p class="text-xs text-gray-500">Coursework: {{ $edu['coursework'] }}</p>@endif
                    @if($edu['activities'] ?? false)<p class="text-xs text-gray-600 mt-0.5">{{ $edu['activities'] }}</p>@endif
                </div>
                <span class="text-xs text-gray-500 flex-shrink-0 ml-4">{{ $edu['start_year'] ?? '' }}{{ ($edu['start_year'] ?? false) && ($edu['end_year'] ?? false) ? ' – ' : '' }}{{ $edu['end_year'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Certifications -->
    @if($resume->certifications && count($resume->certifications) > 0)
    <div>
        <h2 class="section-title">Certifications</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($resume->certifications as $cert)
            <div class="border rounded-lg p-3" style="border-color:color-mix(in srgb,var(--c-primary) 20%,white)">
                <p class="font-semibold text-sm text-gray-900">{{ $cert['name'] ?? '' }}</p>
                <p class="text-xs" style="color:var(--c-primary)">{{ $cert['issuer'] ?? '' }}</p>
                <div class="flex justify-between mt-1">
                    @if($cert['issue_date'] ?? false)<p class="text-xs text-gray-500">Issued: {{ $cert['issue_date'] }}</p>@endif
                    @if($cert['credential_id'] ?? false)<p class="text-xs text-gray-400">ID: {{ $cert['credential_id'] }}</p>@endif
                </div>
                @if($cert['url'] ?? false)<a href="{{ $cert['url'] }}" class="text-xs underline" target="_blank">Verify Certificate</a>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Projects -->
    @if($resume->projects && count($resume->projects) > 0)
    <div>
        <h2 class="section-title">Projects</h2>
        <div class="space-y-3">
            @foreach($resume->projects as $proj)
            <div>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $proj['name'] ?? '' }}</h3>
                        @if($proj['technologies'] ?? false)<p class="text-xs text-gray-500 italic">{{ $proj['technologies'] }}</p>@endif
                    </div>
                    @if(($proj['start_date'] ?? false) || ($proj['end_date'] ?? false))
                    <span class="text-xs text-gray-500 flex-shrink-0 ml-4">{{ $proj['start_date'] ?? '' }}{{ ($proj['start_date'] ?? false) && ($proj['end_date'] ?? false) ? ' – ' : '' }}{{ $proj['end_date'] ?? '' }}</span>
                    @endif
                </div>
                @if($proj['description'] ?? false)<p class="text-sm text-gray-700 leading-relaxed">{{ $proj['description'] }}</p>@endif
                <div class="flex gap-4 mt-1">
                    @if($proj['url'] ?? false)<a href="{{ $proj['url'] }}" class="text-xs font-medium underline" target="_blank"><i class="fas fa-external-link-alt mr-1"></i>Live Demo</a>@endif
                    @if($proj['github_url'] ?? false)<a href="{{ $proj['github_url'] }}" class="text-xs font-medium underline" target="_blank"><i class="fab fa-github mr-1"></i>Source Code</a>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Achievements -->
    @if($resume->achievements && count($resume->achievements) > 0)
    <div>
        <h2 class="section-title">Achievements & Awards</h2>
        <div class="space-y-2">
            @foreach($resume->achievements as $ach)
            <div class="flex items-start gap-2">
                <i class="fas fa-trophy text-xs mt-1 flex-shrink-0" style="color:var(--c-primary)"></i>
                <div>
                    <span class="font-semibold text-sm text-gray-900">{{ $ach['title'] ?? '' }}</span>
                    @if($ach['issuer'] ?? false)<span class="text-sm text-gray-600"> · {{ $ach['issuer'] }}</span>@endif
                    @if($ach['date'] ?? false)<span class="text-xs text-gray-500"> ({{ $ach['date'] }})</span>@endif
                    @if($ach['description'] ?? false)<p class="text-sm text-gray-700">{{ $ach['description'] }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Languages -->
    @if($resume->languages && count($resume->languages) > 0)
    <div>
        <h2 class="section-title">Languages</h2>
        <div class="flex flex-wrap gap-4">
            @foreach($resume->languages as $lang)
            <div class="flex items-center gap-2">
                <span class="font-semibold text-sm text-gray-900">{{ $lang['name'] ?? '' }}</span>
                <span class="badge">{{ $lang['proficiency'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Volunteer Work -->
    @if($resume->volunteer_work && count($resume->volunteer_work) > 0)
    <div>
        <h2 class="section-title">Volunteer & Extra-Curricular</h2>
        <div class="space-y-2">
            @foreach($resume->volunteer_work as $vol)
            <div>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-sm text-gray-900">{{ $vol['role'] ?? '' }}</h3>
                        <p class="text-sm" style="color:var(--c-primary)">{{ $vol['organization'] ?? '' }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $vol['start_date'] ?? '' }}{{ ($vol['start_date'] ?? false) && ($vol['end_date'] ?? false) ? ' – ' : '' }}{{ $vol['end_date'] ?? '' }}</span>
                </div>
                @if($vol['description'] ?? false)<p class="text-sm text-gray-700">{{ $vol['description'] }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div><!-- end px-10 -->
@endif<!-- end single-col -->

</div><!-- end resume-page -->

<div class="no-print text-center py-6 text-gray-400 text-xs">
    Generated by {{ config('app.name') }} &bull; {{ now()->format('F d, Y') }}
</div>
</body>
</html>
