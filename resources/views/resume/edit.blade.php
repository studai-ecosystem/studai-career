<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __("Edit Resume") }}: {{ $resume->title }}
                </h2>
                @if($resume->ats_score)
                <p class="text-sm text-gray-500 mt-0.5">ATS Score: <span class="font-semibold {{ $resume->ats_score >= 80 ? "text-green-600" : ($resume->ats_score >= 60 ? "text-yellow-600" : "text-red-600") }}">{{ $resume->ats_score }}/100</span></p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route("resume.preview", $resume) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                   target="_blank">
                    <i class="fas fa-eye mr-2"></i> Preview
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session("success"))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-green-800">{{ session("success") }}</span>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-medium mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- MAIN EDITOR -->
                <div class="lg:col-span-3 space-y-6">
                    <form id="resume-form" method="POST" action="{{ route("resume.update", $resume) }}">
                        @csrf
                        @method("PUT")
                        <!-- 1. BASIC INFORMATION -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                                <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-user-circle"></i> Basic Information</h3>
                                <p class="text-blue-100 text-sm mt-0.5">Your contact details shown at the top of the resume</p>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Resume Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" value="{{ old("title", $resume->title) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="e.g., Software Engineer Resume — Google 2026">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="full_name" value="{{ old("full_name", $resume->full_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Professional Headline</label>
                                    <input type="text" name="headline" value="{{ old("headline", $resume->headline ?? "") }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Full-Stack Developer | 5+ years | Laravel & React">
                                    <p class="text-xs text-gray-500 mt-1">Short tagline below your name — highly visible to recruiters</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old("email", $resume->email) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" name="phone" value="{{ old("phone", $resume->phone) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="+91 98765 43210">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City / Location</label>
                                    <input type="text" name="location" value="{{ old("location", $resume->location) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Mumbai, India">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Portfolio / Website</label>
                                    <input type="url" name="portfolio_url" value="{{ old("portfolio_url", $resume->portfolio_url) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://yourportfolio.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                                    <input type="url" name="linkedin_url" value="{{ old("linkedin_url", $resume->linkedin_url) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://linkedin.com/in/yourname">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GitHub</label>
                                    <input type="url" name="github_url" value="{{ old("github_url", $resume->github_url) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://github.com/yourusername">
                                </div>
                            </div>
                        </div>
                        <!-- 2. PROFESSIONAL SUMMARY -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-align-left"></i> Professional Summary</h3>
                                    <p class="text-purple-100 text-sm mt-0.5">3-5 sentences — your elevator pitch for the recruiter</p>
                                </div>
                                <button type="button" onclick="generateSummary(this)" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition">
                                    <i class="fas fa-magic mr-1"></i> AI Generate
                                </button>
                            </div>
                            <div class="p-6">
                                <textarea id="summaryInput" name="professional_summary" rows="5" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Results-driven software engineer with 5+ years of experience building scalable web applications. Proficient in PHP, Laravel, and React. Proven track record of reducing load time by 40% and leading teams of 6+ engineers.">{{ old("professional_summary", $resume->professional_summary) }}</textarea>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i> ATS Tip: Include keywords from the job description in your summary.</p>
                                    <span id="summary-count" class="text-xs text-gray-500">0 / 600 chars</span>
                                </div>
                            </div>
                        </div>
                        <!-- 3. WORK EXPERIENCE -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-briefcase"></i> Work Experience</h3>
                                    <p class="text-green-100 text-sm mt-0.5">List most recent first. Use action verbs and quantify achievements.</p>
                                </div>
                                <button type="button" onclick="addExperience()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Job</button>
                            </div>
                            <div class="p-6">
                                <div id="experience-list" class="space-y-5">
                                    @php $expData = $resume->experience ?? []; @endphp
                                    @forelse($expData as $index => $exp)
                                    <div class="experience-item border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                                            <span class="font-medium text-gray-700 text-sm"><i class="fas fa-building mr-2 text-gray-400"></i>{{ $exp["position"] ?? "Position" }} @ {{ $exp["company"] ?? "Company" }}</span>
                                            <button type="button" onclick="removeItem(this,'.experience-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Job Title / Position <span class="text-red-500">*</span></label><input type="text" name="experience[{{ $index }}][position]" value="{{ $exp["position"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Software Engineer"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company Name <span class="text-red-500">*</span></label><input type="text" name="experience[{{ $index }}][company]" value="{{ $exp["company"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Acme Corp"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" name="experience[{{ $index }}][location]" value="{{ $exp["location"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Bangalore / Remote"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employment Type</label><select name="experience[{{ $index }}][employment_type]" class="w-full rounded-md border-gray-300 text-sm"><option {{ ($exp["employment_type"] ?? "") == "Full-time" ? "selected" : "" }}>Full-time</option><option {{ ($exp["employment_type"] ?? "") == "Part-time" ? "selected" : "" }}>Part-time</option><option {{ ($exp["employment_type"] ?? "") == "Contract" ? "selected" : "" }}>Contract</option><option {{ ($exp["employment_type"] ?? "") == "Internship" ? "selected" : "" }}>Internship</option><option {{ ($exp["employment_type"] ?? "") == "Freelance" ? "selected" : "" }}>Freelance</option></select></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="experience[{{ $index }}][start_date]" value="{{ $exp["start_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jan 2022"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><div class="space-y-1"><input type="text" name="experience[{{ $index }}][end_date]" value="{{ $exp["end_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm end-date-input" placeholder="Dec 2024 or Present"><label class="flex items-center gap-1.5 text-xs text-gray-600"><input type="checkbox" onchange="setCurrentJob(this)" {{ ($exp["end_date"] ?? "") == "Present" ? "checked" : "" }} class="rounded"> I currently work here</label></div></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Job Description & Responsibilities</label><textarea name="experience[{{ $index }}][description]" rows="3" class="w-full rounded-md border-gray-300 text-sm" placeholder="Describe key responsibilities...">{{ $exp["description"] ?? "" }}</textarea></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Key Achievements <span class="text-gray-400 font-normal">(one per line — quantify results)</span></label><textarea name="experience[{{ $index }}][achievements]" rows="4" class="w-full rounded-md border-gray-300 text-sm font-mono" placeholder="Reduced API response time by 45% via Redis caching&#10;Led a team of 5 engineers to deliver project ahead of schedule&#10;Increased revenue by Rs.12L through new feature rollout">{{ is_array($exp["achievements"] ?? null) ? implode("\n", $exp["achievements"]) : ($exp["achievements"] ?? "") }}</textarea><p class="text-xs text-gray-400 mt-1"><i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Quantify results (%, Rs., time saved, team size)</p></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Technologies / Skills Used</label><input type="text" name="experience[{{ $index }}][technologies]" value="{{ $exp["technologies"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="PHP, Laravel, MySQL, Redis, Docker, AWS"></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-8 text-sm" id="exp-empty-state"><i class="fas fa-briefcase text-3xl text-gray-200 block mb-2"></i>No work experience added. Click "Add Job" to get started.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 4. EDUCATION -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-graduation-cap"></i> Education</h3>
                                    <p class="text-yellow-100 text-sm mt-0.5">Degrees, diplomas, and relevant coursework</p>
                                </div>
                                <button type="button" onclick="addEducation()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Education</button>
                            </div>
                            <div class="p-6">
                                <div id="education-list" class="space-y-5">
                                    @php $eduData = $resume->education ?? []; @endphp
                                    @forelse($eduData as $index => $edu)
                                    <div class="education-item border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                                            <span class="font-medium text-gray-700 text-sm"><i class="fas fa-university mr-2 text-gray-400"></i>{{ $edu["degree"] ?? "Degree" }} — {{ $edu["institution"] ?? "Institution" }}</span>
                                            <button type="button" onclick="removeItem(this,'.education-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Institution / University <span class="text-red-500">*</span></label><input type="text" name="education[{{ $index }}][institution]" value="{{ $edu["institution"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="IIT Bombay"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Degree / Qualification <span class="text-red-500">*</span></label><input type="text" name="education[{{ $index }}][degree]" value="{{ $edu["degree"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="B.Tech / M.Tech / MBA"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Field of Study / Major</label><input type="text" name="education[{{ $index }}][field]" value="{{ $edu["field"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Computer Science & Engineering"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" name="education[{{ $index }}][location]" value="{{ $edu["location"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mumbai, India"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Year</label><input type="text" name="education[{{ $index }}][start_year]" value="{{ $edu["start_year"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="2019"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Year / Expected</label><input type="text" name="education[{{ $index }}][end_year]" value="{{ $edu["end_year"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="2023 or Ongoing"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">CGPA / Percentage</label><input type="text" name="education[{{ $index }}][gpa]" value="{{ $edu["gpa"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="8.5 / 10 or 85%"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Honors / Distinction</label><input type="text" name="education[{{ $index }}][honors]" value="{{ $edu["honors"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="First Class with Distinction, Dean's List"></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Relevant Coursework</label><input type="text" name="education[{{ $index }}][coursework]" value="{{ $edu["coursework"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Data Structures, Algorithms, DBMS, Machine Learning, Computer Networks"></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Activities & Achievements</label><textarea name="education[{{ $index }}][activities]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="President of Coding Club, National winner at Smart India Hackathon 2022">{{ $edu["activities"] ?? "" }}</textarea></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-8 text-sm" id="edu-empty-state"><i class="fas fa-graduation-cap text-3xl text-gray-200 block mb-2"></i>No education added yet. Click "Add Education" to get started.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 5. SKILLS -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-tools"></i> Skills</h3>
                                    <p class="text-cyan-100 text-sm mt-0.5">Most impactful ATS section — list every relevant skill</p>
                                </div>
                                <button type="button" onclick="extractSkillsAI(this)" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-magic mr-1"></i> AI Extract</button>
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-skill-picker'))" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-th-list mr-1"></i> Select Skills</button>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800"><i class="fas fa-info-circle mr-1"></i><strong>ATS Tip:</strong> Enter comma-separated skills. Use exact keywords from job descriptions. Avoid rating bars — ATS cannot read them.</div>
                                @php
                                    // Use the model accessor which safely flattens any mixed skills format
                                    $flatSkillsList = $resume->flat_skills; // always a plain string[]
                                    $skillsData = $resume->skills ?? [];
                                    $techSkills = $toolsSkills = $softSkills = $otherSkills = "";

                                    // Try to re-categorise from the structured format if available
                                    $categorised = false;
                                    foreach ((array) $skillsData as $s) {
                                        if (is_array($s) && (isset($s['technical']) || isset($s['soft']) || isset($s['tools']))) {
                                            $join = fn($v) => implode(', ', array_filter(array_map('strval', (array)($v ?? []))));
                                            $techSkills  = $join($s['technical'] ?? []);
                                            $toolsSkills = $join($s['tools']    ?? []);
                                            $softSkills  = $join($s['soft']     ?? []);
                                            $otherSkills = $join($s['other']    ?? $s['domain'] ?? []);
                                            $categorised = true;
                                            break;
                                        }
                                    }

                                    if (!$categorised) {
                                        // Fall back: put all flat skills in the technical bucket
                                        $techSkills = implode(', ', $flatSkillsList);
                                    }
                                @endphp
                                @php
                                    function makeTagList(string $raw): string {
                                        return collect(explode(',', $raw))->map(fn($s) => trim($s))->filter()->values()->implode(',');
                                    }
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    {{-- Technical Skills --}}
                                    <div x-data="{
                                        tags: '{{ addslashes(old('skills_technical', $techSkills)) }}'.split(',').map(t=>t.trim()).filter(t=>t),
                                        inp: '',
                                        add() { const v=this.inp.trim().replace(/,$/,''); if(v&&!this.tags.includes(v)) this.tags.push(v); this.inp=''; },
                                        rem(i){ this.tags.splice(i,1); },
                                        key(e){ if(e.key===','||e.key==='Enter'){e.preventDefault();this.add();} if(e.key==='Backspace'&&!this.inp&&this.tags.length) this.rem(this.tags.length-1); }
                                    }" @add-skill.window="if($event.detail.cat==='technical'&&!tags.includes($event.detail.skill)) tags.push($event.detail.skill)">
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span>Technical Skills / Programming</label>
                                        <input type="hidden" name="skills_technical" :value="tags.join(', ')">
                                        <div class="w-full min-h-[80px] border border-gray-300 rounded-md p-2 flex flex-wrap gap-1.5 cursor-text bg-white" @click="$refs.ti.focus()">
                                            <template x-for="(tag,i) in tags" :key="i">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click.stop="rem(i)" class="text-blue-400 hover:text-blue-700 leading-none text-base">&times;</button>
                                                </span>
                                            </template>
                                            <input x-ref="ti" type="text" x-model="inp" @keydown="key" @blur="inp.trim()&&add()" class="flex-1 min-w-[140px] border-none outline-none text-sm bg-transparent" placeholder="Type skill, press comma or Enter…">
                                        </div>
                                    </div>

                                    {{-- Tools & Technologies --}}
                                    <div x-data="{
                                        tags: '{{ addslashes(old('skills_tools', $toolsSkills)) }}'.split(',').map(t=>t.trim()).filter(t=>t),
                                        inp: '',
                                        add() { const v=this.inp.trim().replace(/,$/,''); if(v&&!this.tags.includes(v)) this.tags.push(v); this.inp=''; },
                                        rem(i){ this.tags.splice(i,1); },
                                        key(e){ if(e.key===','||e.key==='Enter'){e.preventDefault();this.add();} if(e.key==='Backspace'&&!this.inp&&this.tags.length) this.rem(this.tags.length-1); }
                                    }" @add-skill.window="if($event.detail.cat==='tools'&&!tags.includes($event.detail.skill)) tags.push($event.detail.skill)">
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>Tools & Technologies</label>
                                        <input type="hidden" name="skills_tools" :value="tags.join(', ')">
                                        <div class="w-full min-h-[80px] border border-gray-300 rounded-md p-2 flex flex-wrap gap-1.5 cursor-text bg-white" @click="$refs.tt.focus()">
                                            <template x-for="(tag,i) in tags" :key="i">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click.stop="rem(i)" class="text-green-400 hover:text-green-700 leading-none text-base">&times;</button>
                                                </span>
                                            </template>
                                            <input x-ref="tt" type="text" x-model="inp" @keydown="key" @blur="inp.trim()&&add()" class="flex-1 min-w-[140px] border-none outline-none text-sm bg-transparent" placeholder="Git, Docker, AWS…">
                                        </div>
                                    </div>

                                    {{-- Soft Skills --}}
                                    <div x-data="{
                                        tags: '{{ addslashes(old('skills_soft', $softSkills)) }}'.split(',').map(t=>t.trim()).filter(t=>t),
                                        inp: '',
                                        add() { const v=this.inp.trim().replace(/,$/,''); if(v&&!this.tags.includes(v)) this.tags.push(v); this.inp=''; },
                                        rem(i){ this.tags.splice(i,1); },
                                        key(e){ if(e.key===','||e.key==='Enter'){e.preventDefault();this.add();} if(e.key==='Backspace'&&!this.inp&&this.tags.length) this.rem(this.tags.length-1); }
                                    }" @add-skill.window="if($event.detail.cat==='soft'&&!tags.includes($event.detail.skill)) tags.push($event.detail.skill)">
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-1"></span>Soft Skills</label>
                                        <input type="hidden" name="skills_soft" :value="tags.join(', ')">
                                        <div class="w-full min-h-[80px] border border-gray-300 rounded-md p-2 flex flex-wrap gap-1.5 cursor-text bg-white" @click="$refs.ts.focus()">
                                            <template x-for="(tag,i) in tags" :key="i">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click.stop="rem(i)" class="text-purple-400 hover:text-purple-700 leading-none text-base">&times;</button>
                                                </span>
                                            </template>
                                            <input x-ref="ts" type="text" x-model="inp" @keydown="key" @blur="inp.trim()&&add()" class="flex-1 min-w-[140px] border-none outline-none text-sm bg-transparent" placeholder="Leadership, Communication…">
                                        </div>
                                    </div>

                                    {{-- Domain Knowledge --}}
                                    <div x-data="{
                                        tags: '{{ addslashes(old('skills_other', $otherSkills)) }}'.split(',').map(t=>t.trim()).filter(t=>t),
                                        inp: '',
                                        add() { const v=this.inp.trim().replace(/,$/,''); if(v&&!this.tags.includes(v)) this.tags.push(v); this.inp=''; },
                                        rem(i){ this.tags.splice(i,1); },
                                        key(e){ if(e.key===','||e.key==='Enter'){e.preventDefault();this.add();} if(e.key==='Backspace'&&!this.inp&&this.tags.length) this.rem(this.tags.length-1); }
                                    }" @add-skill.window="if($event.detail.cat==='domain'&&!tags.includes($event.detail.skill)) tags.push($event.detail.skill)">
                                        <label class="block text-sm font-medium text-gray-700 mb-1"><span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-1"></span>Domain / Industry Knowledge</label>
                                        <input type="hidden" name="skills_other" :value="tags.join(', ')">
                                        <div class="w-full min-h-[80px] border border-gray-300 rounded-md p-2 flex flex-wrap gap-1.5 cursor-text bg-white" @click="$refs.td.focus()">
                                            <template x-for="(tag,i) in tags" :key="i">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click.stop="rem(i)" class="text-orange-400 hover:text-orange-700 leading-none text-base">&times;</button>
                                                </span>
                                            </template>
                                            <input x-ref="td" type="text" x-model="inp" @keydown="key" @blur="inp.trim()&&add()" class="flex-1 min-w-[140px] border-none outline-none text-sm bg-transparent" placeholder="FinTech, Microservices…">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ===== SKILL PICKER MODAL ===== --}}
                        <div x-data="{
                            open: false,
                            activeTab: 'technical',
                            selected: {},
                            tabs: [
                                { key: 'technical', label: 'Technical', color: 'blue' },
                                { key: 'tools',     label: 'Tools & Cloud', color: 'green' },
                                { key: 'soft',      label: 'Soft Skills', color: 'purple' },
                                { key: 'domain',    label: 'Domain', color: 'orange' },
                            ],
                            skillMap: {
                                technical: ['Python','JavaScript','TypeScript','Java','C++','C#','Go','Rust','PHP','Ruby','Swift','Kotlin','Scala','R','MATLAB','Bash','SQL','HTML','CSS','Dart','Flutter','React Native','GraphQL','REST API','gRPC','WebSockets','Microservices','Machine Learning','Deep Learning','NLP','Computer Vision','TensorFlow','PyTorch','Scikit-learn','Pandas','NumPy','Data Analysis','Statistics','React','Angular','Vue.js','Next.js','Nuxt.js','Django','Flask','FastAPI','Laravel','Spring Boot','Express.js','Ruby on Rails','ASP.NET'],
                                tools:     ['Git','GitHub','GitLab','Bitbucket','Docker','Kubernetes','Terraform','Ansible','Jenkins','GitHub Actions','CircleCI','AWS','Azure','GCP','Firebase','Vercel','Netlify','Nginx','Linux','Ubuntu','PostgreSQL','MySQL','MongoDB','Redis','Elasticsearch','DynamoDB','SQLite','Cassandra','Kafka','RabbitMQ','Postman','Jira','Confluence','Figma','VS Code','IntelliJ','Webpack','Vite','npm','yarn','Prometheus','Grafana','Datadog','Sentry'],
                                soft:      ['Leadership','Communication','Problem Solving','Critical Thinking','Team Management','Adaptability','Time Management','Collaboration','Creativity','Attention to Detail','Conflict Resolution','Decision Making','Emotional Intelligence','Negotiation','Presentation Skills','Project Management','Strategic Thinking','Mentoring','Customer Focus','Work Ethic','Self-Motivation','Analytical Thinking','Prioritization','Active Listening','Stakeholder Management'],
                                domain:    ['FinTech','EdTech','HealthTech','E-Commerce','SaaS','B2B','B2C','Banking','Insurance','Logistics','Supply Chain','Retail','Gaming','Media','Telecommunications','Healthcare','Pharmaceuticals','Legal Tech','PropTech','AgriTech','Cybersecurity','Blockchain','Web3','AR/VR','IoT','Robotics','Autonomous Systems','Digital Marketing','SEO','Product Management','UX Design','UI Design','Data Engineering','Business Intelligence','ERP','CRM','HR Tech'],
                            },
                            toggle(tab, skill) {
                                const k = tab + ':' + skill;
                                this.selected[k] = !this.selected[k];
                            },
                            isSelected(tab, skill) { return !!this.selected[tab + ':' + skill]; },
                            addSelected() {
                                Object.keys(this.selected).forEach(k => {
                                    if (!this.selected[k]) return;
                                    const [cat, skill] = k.split(/:(.+)/);
                                    window.dispatchEvent(new CustomEvent('add-skill', { detail: { cat, skill } }));
                                });
                                this.selected = {};
                                this.open = false;
                            },
                            countSelected() { return Object.values(this.selected).filter(Boolean).length; }
                        }" @open-skill-picker.window="open=true">

                            {{-- Backdrop --}}
                            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 z-[1001]" @click="open=false" style="display:none"></div>

                            {{-- Modal Panel --}}
                            <div x-show="open" x-transition class="fixed inset-0 z-[1002] flex items-center justify-center p-4" style="display:none">
                                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col" @click.stop>

                                    {{-- Header --}}
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Select Skills</h3>
                                            <p class="text-xs text-gray-500 mt-0.5">Click skills to select, then click "Add to Resume"</p>
                                        </div>
                                        <button @click="open=false" class="text-gray-400 hover:text-gray-600 transition text-xl leading-none">&times;</button>
                                    </div>

                                    {{-- Tabs --}}
                                    <div class="flex gap-1 px-6 pt-4">
                                        <template x-for="tab in tabs" :key="tab.key">
                                            <button type="button"
                                                @click="activeTab=tab.key"
                                                :class="activeTab===tab.key
                                                    ? 'bg-indigo-600 text-white shadow'
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                                class="px-4 py-1.5 rounded-full text-sm font-medium transition"
                                                x-text="tab.label">
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Skills Grid (scrollable) --}}
                                    <div class="flex-1 overflow-y-auto px-6 py-4">
                                        <template x-for="tab in tabs" :key="tab.key">
                                            <div x-show="activeTab===tab.key" class="flex flex-wrap gap-2">
                                                <template x-for="skill in skillMap[tab.key]" :key="skill">
                                                    <button type="button"
                                                        @click="toggle(tab.key, skill)"
                                                        :class="isSelected(tab.key, skill)
                                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                                            : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                                        class="px-3 py-1.5 rounded-full border text-sm font-medium transition-all"
                                                        x-text="skill">
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                                        <span class="text-sm text-gray-500">
                                            <span x-text="countSelected()"></span> skill<span x-show="countSelected()!==1">s</span> selected
                                        </span>
                                        <div class="flex gap-3">
                                            <button type="button" @click="selected={}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition">Clear</button>
                                            <button type="button" @click="addSelected()"
                                                :disabled="countSelected()===0"
                                                :class="countSelected()===0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-700'"
                                                class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold transition">
                                                Add to Resume
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ===== END SKILL PICKER MODAL ===== --}}

                        <!-- 6. CERTIFICATIONS -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-rose-500 to-pink-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-certificate"></i> Certifications & Licenses</h3>
                                    <p class="text-rose-100 text-sm mt-0.5">Professional certifications boost ATS scores significantly</p>
                                </div>
                                <button type="button" onclick="addCertification()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Certification</button>
                            </div>
                            <div class="p-6">
                                <div id="certifications-list" class="space-y-4">
                                    @php $certData = $resume->certifications ?? []; @endphp
                                    @forelse($certData as $index => $cert)
                                    <div class="certification-item border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm flex items-center gap-2"><i class="fas fa-award text-rose-400"></i>{{ $cert["name"] ?? "Certification" }}</span><button type="button" onclick="removeItem(this,'.certification-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Certification Name <span class="text-red-500">*</span></label><input type="text" name="certifications[{{ $index }}][name]" value="{{ $cert["name"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="AWS Certified Solutions Architect"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Issuing Organization <span class="text-red-500">*</span></label><input type="text" name="certifications[{{ $index }}][issuer]" value="{{ $cert["issuer"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Amazon Web Services"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label><input type="text" name="certifications[{{ $index }}][issue_date]" value="{{ $cert["issue_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="March 2024"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label><input type="text" name="certifications[{{ $index }}][expiry_date]" value="{{ $cert["expiry_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="March 2027 or No Expiry"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Credential ID</label><input type="text" name="certifications[{{ $index }}][credential_id]" value="{{ $cert["credential_id"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="ABC-123456"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Credential URL</label><input type="url" name="certifications[{{ $index }}][url]" value="{{ $cert["url"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://credly.com/badges/..."></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-6 text-sm" id="cert-empty-state"><i class="fas fa-certificate text-3xl text-gray-200 block mb-2"></i>No certifications added. Add AWS, Google Cloud, Meta, Coursera, Udemy certificates here.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 7. PROJECTS -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-code"></i> Projects</h3>
                                    <p class="text-indigo-100 text-sm mt-0.5">Showcase practical experience — critical for freshers & developers</p>
                                </div>
                                <button type="button" onclick="addProject()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Project</button>
                            </div>
                            <div class="p-6">
                                <div id="projects-list" class="space-y-4">
                                    @php $projectData = $resume->projects ?? []; @endphp
                                    @forelse($projectData as $index => $project)
                                    <div class="project-item border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 flex justify-between items-center"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-project-diagram mr-2 text-indigo-400"></i>{{ $project["name"] ?? "Project" }}</span><button type="button" onclick="removeItem(this,'.project-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Project Name <span class="text-red-500">*</span></label><input type="text" name="projects[{{ $index }}][name]" value="{{ $project["name"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="E-commerce Platform"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="projects[{{ $index }}][start_date]" value="{{ $project["start_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jan 2024"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="text" name="projects[{{ $index }}][end_date]" value="{{ $project["end_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mar 2024 or Ongoing"></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description & Impact</label><textarea name="projects[{{ $index }}][description]" rows="3" class="w-full rounded-md border-gray-300 text-sm" placeholder="Built a multi-tenant SaaS platform serving 500+ daily users. Reduced checkout time by 30%.">{{ $project["description"] ?? "" }}</textarea></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Technologies Used</label><input type="text" name="projects[{{ $index }}][technologies]" value="{{ $project["technologies"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Laravel, React, MySQL, Redis, Tailwind CSS, Stripe API"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Live URL</label><input type="url" name="projects[{{ $index }}][url]" value="{{ $project["url"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://myproject.com"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">GitHub / Source</label><input type="url" name="projects[{{ $index }}][github_url]" value="{{ $project["github_url"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://github.com/user/repo"></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-6 text-sm" id="proj-empty-state"><i class="fas fa-code text-3xl text-gray-200 block mb-2"></i>No projects added. Add personal, academic, or open-source projects here.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 8. ACHIEVEMENTS -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-trophy"></i> Achievements & Awards</h3>
                                    <p class="text-amber-100 text-sm mt-0.5">Hackathons, competitions, recognitions, scholarships</p>
                                </div>
                                <button type="button" onclick="addAchievement()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Achievement</button>
                            </div>
                            <div class="p-6">
                                <div id="achievements-list" class="space-y-4">
                                    @php $achievementData = $resume->achievements ?? []; @endphp
                                    @forelse($achievementData as $index => $ach)
                                    <div class="achievement-item border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm flex items-center gap-2"><i class="fas fa-medal text-amber-400"></i>{{ $ach["title"] ?? "Achievement" }}</span><button type="button" onclick="removeItem(this,'.achievement-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Award / Achievement Title <span class="text-red-500">*</span></label><input type="text" name="achievements[{{ $index }}][title]" value="{{ $ach["title"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Winner — Smart India Hackathon 2023"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Issuer / Organization</label><input type="text" name="achievements[{{ $index }}][issuer]" value="{{ $ach["issuer"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Ministry of Education, India"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input type="text" name="achievements[{{ $index }}][date]" value="{{ $ach["date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="December 2023"></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="achievements[{{ $index }}][description]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="Developed an AI-powered solution; won Rs.1L prize among 10,000 teams nationally.">{{ $ach["description"] ?? "" }}</textarea></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-6 text-sm" id="ach-empty-state"><i class="fas fa-trophy text-3xl text-gray-200 block mb-2"></i>No achievements added. Hackathon wins, scholarships, top rankings stand out.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 9. LANGUAGES -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-language"></i> Languages</h3>
                                    <p class="text-teal-100 text-sm mt-0.5">Multilingual skills are a strong differentiator</p>
                                </div>
                                <button type="button" onclick="addLanguage()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Language</button>
                            </div>
                            <div class="p-6">
                                <div id="languages-list" class="space-y-3">
                                    @php $langData = $resume->languages ?? []; @endphp
                                    @forelse($langData as $index => $lang)
                                    <div class="language-item flex items-center gap-4 p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1"><input type="text" name="languages[{{ $index }}][name]" value="{{ $lang["name"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="English"></div>
                                        <div class="w-48"><select name="languages[{{ $index }}][proficiency]" class="w-full rounded-md border-gray-300 text-sm">@foreach(["Native","Fluent","Advanced","Intermediate","Basic"] as $level)<option {{ ($lang["proficiency"] ?? "") == $level ? "selected" : "" }}>{{ $level }}</option>@endforeach</select></div>
                                        <button type="button" onclick="removeItem(this,'.language-item')" class="text-red-500 hover:text-red-700 flex-shrink-0"><i class="fas fa-trash text-sm"></i></button>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-4 text-sm" id="lang-empty-state"><i class="fas fa-language text-3xl text-gray-200 block mb-2"></i>No languages added. Start with English, then add others you speak.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 10. VOLUNTEER WORK -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-slate-600 to-gray-700 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-hands-helping"></i> Volunteer Work & Extra-Curricular</h3>
                                    <p class="text-slate-300 text-sm mt-0.5">Shows initiative, soft skills, and passion beyond your job</p>
                                </div>
                                <button type="button" onclick="addVolunteer()" class="flex-shrink-0 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition"><i class="fas fa-plus mr-1"></i> Add Entry</button>
                            </div>
                            <div class="p-6">
                                <div id="volunteer-list" class="space-y-4">
                                    @php $volunteerData = $resume->volunteer_work ?? []; @endphp
                                    @forelse($volunteerData as $index => $vol)
                                    <div class="volunteer-item border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm">{{ $vol["role"] ?? "Role" }} @ {{ $vol["organization"] ?? "Org" }}</span><button type="button" onclick="removeItem(this,'.volunteer-item')" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Role / Position</label><input type="text" name="volunteer_work[{{ $index }}][role]" value="{{ $vol["role"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mentor / Volunteer Developer"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Organization</label><input type="text" name="volunteer_work[{{ $index }}][organization]" value="{{ $vol["organization"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Code for India / NSS"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="volunteer_work[{{ $index }}][start_date]" value="{{ $vol["start_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="June 2022"></div>
                                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="text" name="volunteer_work[{{ $index }}][end_date]" value="{{ $vol["end_date"] ?? "" }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="Ongoing"></div>
                                            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="volunteer_work[{{ $index }}][description]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mentored 30+ students in web development basics.">{{ $vol["description"] ?? "" }}</textarea></div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-center text-gray-400 py-4 text-sm" id="vol-empty-state"><i class="fas fa-hands-helping text-3xl text-gray-200 block mb-2"></i>Optional but impressive. Add club leadership, NGO work, open-source contributions.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- 11. TEMPLATE -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-white font-semibold text-lg flex items-center gap-2"><i class="fas fa-palette"></i> Resume Template</h3>
                                    <p class="text-gray-400 text-sm mt-0.5">Click a template to select it, then click Save</p>
                                </div>
                                <a href="{{ route('resume.preview', $resume) }}" target="_blank" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm rounded-lg border border-white/30 transition">
                                    <i class="fas fa-eye mr-1"></i> Live Preview
                                </a>
                            </div>
                            <div class="p-6" x-data="{ open: false, tpl: null }" @keydown.escape.window="open=false">
                                {{-- Full Resume Preview Modal --}}
                                <div x-show="open" x-transition.opacity style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.65);display:flex;align-items:center;justify-content:center;padding:24px;" @click.self="open=false">
                                    <div style="background:#fff;border-radius:16px;width:100%;max-width:780px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,.35);">
                                        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid #e5e7eb;flex-shrink:0;">
                                            <div>
                                                <h3 x-text="tpl?.name" style="font-size:18px;font-weight:700;color:#111827;margin:0;"></h3>
                                                <p style="font-size:12px;color:#6b7280;margin:3px 0 0;">Sample resume preview — your real data fills in when saved</p>
                                            </div>
                                            <div style="display:flex;gap:10px;align-items:center;">
                                                <button type="button"
                                                        @click="open=false; document.getElementById('radio-'+tpl.id)?.click(); onTemplateChange(document.getElementById('radio-'+tpl.id), tpl.id)"
                                                        style="padding:9px 20px;background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;font-size:13px;font-weight:700;border-radius:9px;border:none;cursor:pointer;">
                                                    ✓ Use This Template
                                                </button>
                                                <button type="button" @click="open=false" style="background:#f3f4f6;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;font-size:18px;color:#6b7280;line-height:1;">✕</button>
                                            </div>
                                        </div>
                                        <div style="overflow-y:auto;padding:24px;background:#f8fafc;">
                                            <template x-if="tpl">
                                                <div style="background:#fff;border-radius:8px;box-shadow:0 2px 16px rgba(0,0,0,.08);overflow:hidden;font-family:'Segoe UI',Arial,sans-serif;">
                                                    <div :style="'background:'+tpl.primary+';padding:28px 32px;'">
                                                        <div style="font-size:26px;font-weight:800;color:#fff;letter-spacing:-.3px;">{{ auth()->user()->name }}</div>
                                                        <div :style="'font-size:13px;color:rgba(255,255,255,.8);margin-top:4px;'" x-text="tpl.name+' · Resume Preview'"></div>
                                                        <div style="display:flex;gap:18px;margin-top:12px;flex-wrap:wrap;">
                                                            <span style="font-size:11px;color:rgba(255,255,255,.75);">📧 {{ auth()->user()->email }}</span>
                                                            <span style="font-size:11px;color:rgba(255,255,255,.75);">📍 Chennai, India</span>
                                                            <span style="font-size:11px;color:rgba(255,255,255,.75);">🔗 linkedin.com/in/profile</span>
                                                        </div>
                                                    </div>
                                                    <div :style="tpl.cols==2?'display:flex;':''">
                                                        <template x-if="tpl.cols==2">
                                                            <div :style="'width:210px;flex-shrink:0;padding:18px 16px;background:'+tpl.secondary+'12;border-right:1px solid #e5e7eb;'">
                                                                <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:7px;'">Skills</div>
                                                                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:16px;">
                                                                    <template x-for="s in ['Laravel','React','PHP','MySQL','Docker','AWS','Git','REST API']">
                                                                        <span :style="'background:'+tpl.primary+'18;color:'+tpl.primary+';font-size:10px;padding:2px 7px;border-radius:4px;font-weight:600;'" x-text="s"></span>
                                                                    </template>
                                                                </div>
                                                                <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:7px;'">Education</div>
                                                                <div style="font-size:11px;font-weight:700;color:#1f2937;">B.Tech Computer Science</div>
                                                                <div style="font-size:11px;color:#6b7280;">IIT Bombay · 2019–2023</div>
                                                                <div style="font-size:11px;color:#6b7280;margin-bottom:14px;">CGPA: 8.7/10</div>
                                                                <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:7px;'">Languages</div>
                                                                <div style="font-size:11px;color:#374151;">English · Native</div>
                                                                <div style="font-size:11px;color:#374151;">Hindi · Fluent</div>
                                                            </div>
                                                        </template>
                                                        <div style="flex:1;padding:20px 24px;">
                                                            <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:5px;'">Professional Summary</div>
                                                            <p style="font-size:12px;color:#374151;line-height:1.65;margin-bottom:16px;">Full-stack software engineer with 4+ years building scalable SaaS products. Expert in Laravel, React, and cloud infrastructure. Led cross-functional teams delivering ₹2Cr+ revenue impact. Passionate about clean code and developer experience.</p>
                                                            <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:9px;'">Work Experience</div>
                                                            <div style="margin-bottom:14px;">
                                                                <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                                                    <div style="font-size:13px;font-weight:700;color:#111827;">Senior Software Engineer</div>
                                                                    <div style="font-size:11px;color:#6b7280;">Jan 2023 – Present</div>
                                                                </div>
                                                                <div style="font-size:11px;font-weight:600;color:#6b7280;margin-bottom:4px;">Infosys · Bengaluru, India</div>
                                                                <ul style="font-size:11px;color:#374151;padding-left:16px;line-height:1.7;margin:0;">
                                                                    <li>Reduced API response time by 45% via Redis caching, improving UX for 50K+ users</li>
                                                                    <li>Led migration to microservices, cutting deployment from 2h to 12min</li>
                                                                    <li>Mentored 5 junior engineers; reduced bugs by 30%</li>
                                                                </ul>
                                                            </div>
                                                            <div style="margin-bottom:14px;">
                                                                <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                                                    <div style="font-size:13px;font-weight:700;color:#111827;">Software Engineer</div>
                                                                    <div style="font-size:11px;color:#6b7280;">Jun 2020 – Dec 2022</div>
                                                                </div>
                                                                <div style="font-size:11px;font-weight:600;color:#6b7280;margin-bottom:4px;">Razorpay · Bengaluru, India</div>
                                                                <ul style="font-size:11px;color:#374151;padding-left:16px;line-height:1.7;margin:0;">
                                                                    <li>Built payment reconciliation engine processing ₹500Cr/month</li>
                                                                    <li>Integrated 12 banking partners via REST APIs; cut integration time by 60%</li>
                                                                </ul>
                                                            </div>
                                                            <template x-if="tpl.cols!=2">
                                                                <div>
                                                                    <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:7px;'">Skills</div>
                                                                    <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px;">
                                                                        <template x-for="s in ['Laravel','PHP','React','TypeScript','MySQL','PostgreSQL','Redis','Docker','AWS','Git','Tailwind CSS']">
                                                                            <span :style="'background:'+tpl.primary+'15;color:'+tpl.primary+';font-size:11px;padding:3px 9px;border-radius:5px;font-weight:600;'" x-text="s"></span>
                                                                        </template>
                                                                    </div>
                                                                    <div :style="'font-size:10px;font-weight:800;letter-spacing:.08em;color:'+tpl.accent+';text-transform:uppercase;margin-bottom:7px;'">Education</div>
                                                                    <div style="font-size:12px;font-weight:700;color:#111827;">B.Tech Computer Science · IIT Bombay · 2019–2023 · CGPA 8.7/10</div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div :style="'height:5px;background:'+tpl.accent+';'"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Template grid --}}
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="template-grid">
                                    @foreach($templates as $template)
                                    @php
                                        $tc = is_array($template->color_scheme) ? $template->color_scheme : (json_decode($template->color_scheme, true) ?? []);
                                        $tp  = $tc['primary']   ?? '#1a202c';
                                        $ts  = $tc['secondary'] ?? '#2d3748';
                                        $ta  = $tc['accent']    ?? '#4a5568';
                                        $tl  = is_array($template->layout_config) ? $template->layout_config : (json_decode($template->layout_config, true) ?? []);
                                        $tcols = $tl['columns'] ?? 1;
                                        $tplData = json_encode(['id'=>$template->id,'name'=>$template->name,'primary'=>$tp,'secondary'=>$ts,'accent'=>$ta,'cols'=>$tcols]);
                                    @endphp
                                    <div class="group relative">
                                        <label class="cursor-pointer template-card-label block" data-id="{{ $template->id }}">
                                            <input type="radio" id="radio-{{ $template->id }}" name="template_id" value="{{ $template->id }}"
                                                   {{ $resume->template_id == $template->id ? 'checked' : '' }}
                                                   class="sr-only peer template-radio"
                                                   onchange="onTemplateChange(this, {{ $template->id }})">
                                            <div class="template-card border-2 rounded-lg overflow-hidden transition-all duration-200 {{ $resume->template_id == $template->id ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-400' }}" id="tcard-{{ $template->id }}">
                                                {{-- Mini resume thumbnail --}}
                                                <div style="position:relative;padding-bottom:129%;overflow:hidden;background:#f8fafc;">
                                                    @if($template->preview_image)
                                                        <img src="{{ asset('storage/'.$template->preview_image) }}" alt="{{ $template->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                                                    @else
                                                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;font-family:Arial,sans-serif;">
                                                            <div style="padding:5px 6px 4px;flex-shrink:0;background:{{ $tp }};">
                                                                <div style="width:52px;height:5px;background:rgba(255,255,255,.9);border-radius:2px;margin-bottom:2px;"></div>
                                                                <div style="width:36px;height:3px;background:rgba(255,255,255,.6);border-radius:2px;margin-bottom:3px;"></div>
                                                                <div style="display:flex;gap:6px;">
                                                                    <div style="width:26px;height:2px;background:rgba(255,255,255,.4);border-radius:1px;"></div>
                                                                    <div style="width:26px;height:2px;background:rgba(255,255,255,.4);border-radius:1px;"></div>
                                                                    <div style="width:20px;height:2px;background:rgba(255,255,255,.4);border-radius:1px;"></div>
                                                                </div>
                                                            </div>
                                                            @if($tcols == 2)
                                                            <div style="display:flex;flex:1;overflow:hidden;">
                                                                <div style="width:37%;padding:5px 4px;flex-shrink:0;background:{{ $ts }}18;border-right:1px solid #e5e7eb;">
                                                                    <div style="width:100%;height:2.5px;background:{{ $ta }};border-radius:1px;margin-bottom:2px;"></div>
                                                                    @for($i=0;$i<4;$i++)<div style="width:{{ 65+$i*7 }}%;height:2px;background:#d1d5db;border-radius:1px;margin-bottom:2px;"></div>@endfor
                                                                    <div style="width:100%;height:2.5px;background:{{ $ta }};border-radius:1px;margin:4px 0 2px;"></div>
                                                                    @for($i=0;$i<3;$i++)<div style="width:{{ 70+$i*6 }}%;height:2px;background:#d1d5db;border-radius:1px;margin-bottom:2px;"></div>@endfor
                                                                </div>
                                                                <div style="flex:1;padding:5px 5px;">
                                                                    @for($s=0;$s<3;$s++)
                                                                    <div style="width:54%;height:2.5px;background:{{ $tp }};border-radius:1px;margin-bottom:2px;"></div>
                                                                    @for($i=0;$i<3;$i++)<div style="width:{{ 80+$i*6 }}%;height:2px;background:#e2e8f0;border-radius:1px;margin-bottom:2px;"></div>@endfor
                                                                    <div style="margin-bottom:4px;"></div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            @else
                                                            <div style="flex:1;padding:6px 6px;">
                                                                @for($s=0;$s<4;$s++)
                                                                <div style="width:40%;height:2.5px;background:{{ $tp }};border-radius:1px;margin-bottom:2px;"></div>
                                                                @for($i=0;$i<3;$i++)<div style="width:{{ 72+$i*10 }}%;height:2px;background:#e2e8f0;border-radius:1px;margin-bottom:2px;"></div>@endfor
                                                                <div style="margin-bottom:5px;"></div>
                                                                @endfor
                                                            </div>
                                                            @endif
                                                            <div style="height:3px;background:{{ $ta }};flex-shrink:0;"></div>
                                                        </div>
                                                    @endif
                                                    {{-- Active badge --}}
                                                    <div style="position:absolute;top:4px;right:4px;{{ $resume->template_id == $template->id ? '' : 'display:none;' }}" id="check-{{ $template->id }}">
                                                        <span style="background:#4f46e5;color:#fff;font-size:9px;padding:2px 6px;border-radius:10px;font-weight:700;">✓ Active</span>
                                                    </div>
                                                    {{-- Hover preview overlay --}}
                                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity" style="position:absolute;inset:0;background:rgba(0,0,0,.42);display:flex;align-items:center;justify-content:center;">
                                                        <button type="button" @click.prevent="tpl={{ $tplData }};open=true"
                                                                style="background:#fff;color:#4f46e5;font-size:10px;font-weight:700;padding:5px 10px;border-radius:6px;border:2px solid #4f46e5;cursor:pointer;white-space:nowrap;">
                                                            👁 Full Preview
                                                        </button>
                                                    </div>
                                                </div>
                                                <div style="padding:6px 8px 8px;">
                                                    <p style="font-size:11px;font-weight:700;text-align:center;color:#1f2937;margin:0 0 3px;">{{ $template->name }}</p>
                                                    <div style="display:flex;justify-content:center;align-items:center;gap:4px;">
                                                        @if($template->is_ats_friendly ?? false)
                                                        <span style="font-size:9px;color:#16a34a;font-weight:700;"><i class="fas fa-check-circle"></i> ATS</span>
                                                        @endif
                                                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $tp }};display:inline-block;border:1px solid rgba(0,0,0,.15);"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <p style="font-size:11px;color:#9ca3af;margin-top:10px;text-align:center;"><i class="fas fa-info-circle" style="margin-right:3px;"></i>Hover any card to see full preview · Select then <strong>Save All Changes</strong></p>
                            </div>
                        </div>
                        <!-- SUBMIT -->
                        <div class="flex gap-4">
                            <button type="submit" class="flex-1 py-3 px-6 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg flex items-center justify-center gap-2"><i class="fas fa-save"></i> Save All Changes</button>
                            <a href="{{ route("resume.preview", $resume) }}" target="_blank" class="flex-1 py-3 px-6 bg-gray-700 text-white text-center rounded-lg font-semibold hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2"><i class="fas fa-eye"></i> Preview Resume</a>
                        </div>
                    </form>
                </div>
                <!-- SIDEBAR -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- ATS Score -->
                    <div class="bg-white shadow-sm rounded-lg p-5">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-chart-bar text-indigo-600"></i> ATS Score</h3>
                        @php $score = ($resume->ats_score !== null && $resume->ats_score > 0) ? (int)$resume->ats_score : null; @endphp
                        @if($score)
                        <div class="flex justify-between text-xs text-gray-600 mb-1"><span>Score</span><span class="font-bold text-lg {{ $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $score }}/100</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-3"><div class="h-3 rounded-full {{ $score >= 80 ? 'bg-green-500' : ($score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width:{{ $score }}%"></div></div>
                        <p class="text-xs mt-2 {{ $score >= 80 ? 'text-green-700' : ($score >= 60 ? 'text-yellow-700' : 'text-red-700') }}">{{ $score >= 80 ? 'Excellent — ready to apply' : ($score >= 60 ? 'Good — a few improvements needed' : 'Needs improvement — use AI Analyze below') }}</p>
                        @else
                        <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Score</span><span class="font-semibold">Not analyzed</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-3"><div class="h-3 rounded-full bg-gray-300" style="width:0%"></div></div>
                        <p class="text-xs mt-2 text-gray-500">Click <strong>Analyze ATS</strong> in the widget below to get your score</p>
                        @endif
                    </div>
                    <!-- Section Completion -->
                    <div class="bg-white shadow-sm rounded-lg p-5">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-tasks text-green-600"></i> Completion</h3>
                        @php
                            $sections = [
                                ["label"=>"Basic Info","done"=>!empty($resume->full_name)&&!empty($resume->email)],
                                ["label"=>"Summary","done"=>!empty($resume->professional_summary)],
                                ["label"=>"Experience","done"=>!empty($resume->experience)],
                                ["label"=>"Education","done"=>!empty($resume->education)],
                                ["label"=>"Skills","done"=>!empty($resume->skills)],
                                ["label"=>"Certifications","done"=>!empty($resume->certifications)],
                                ["label"=>"Projects","done"=>!empty($resume->projects)],
                                ["label"=>"Languages","done"=>!empty($resume->languages)],
                            ];
                            $done = collect($sections)->where("done",true)->count();
                        @endphp
                        <ul class="space-y-2 text-sm">
                            @foreach($sections as $sec)
                            <li class="flex items-center gap-2">
                                @if($sec["done"])<i class="fas fa-check-circle text-green-500 w-4"></i><span class="text-gray-700">{{ $sec["label"] }}</span>
                                @else<i class="fas fa-circle text-gray-200 w-4"></i><span class="text-gray-400">{{ $sec["label"] }}</span>@endif
                            </li>
                            @endforeach
                            <li class="pt-2 border-t"><div class="flex justify-between text-xs text-gray-600"><span>{{ $done }}/{{ count($sections) }} complete</span><span class="font-semibold">{{ round(($done/count($sections))*100) }}%</span></div><div class="w-full bg-gray-200 rounded-full h-1.5 mt-1"><div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ round(($done/count($sections))*100) }}%"></div></div></li>
                        </ul>
                    </div>
                    <!-- AI Tools -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 shadow-sm rounded-lg p-5">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-robot text-purple-600"></i> AI Tools</h3>
                        <div class="space-y-2">
                            <button type="button" onclick="generateSummary(this)" class="w-full py-2 px-4 bg-white border border-purple-200 rounded-lg text-sm font-medium text-purple-700 hover:bg-purple-50 transition flex items-center gap-2"><i class="fas fa-magic w-4"></i> Generate Summary</button>
                            <button type="button" onclick="extractSkillsAI(this)" class="w-full py-2 px-4 bg-white border border-purple-200 rounded-lg text-sm font-medium text-purple-700 hover:bg-purple-50 transition flex items-center gap-2"><i class="fas fa-lightbulb w-4"></i> Extract Skills from CV</button>
                        </div>
                    </div>
                    <!-- Cover Letter -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 shadow-sm rounded-lg p-5 border border-indigo-100">
                        <h3 class="font-semibold text-gray-800 mb-1 flex items-center gap-2"><i class="fas fa-file-signature text-indigo-600"></i> Cover Letter</h3>
                        <p class="text-xs text-gray-500 mb-3">AI-generated, multiple tones, PDF + Word download</p>
                        <a href="{{ route('resume.cover-letter.show', $resume) }}"
                           class="block w-full py-2.5 px-4 text-center rounded-lg font-semibold text-sm text-white transition"
                           style="background:linear-gradient(135deg,#6366f1,#a855f7);">
                            <i class="fas fa-magic mr-1"></i> Generate Cover Letter
                        </a>
                    </div>
                    <!-- Export -->
                    <div class="bg-white shadow-sm rounded-lg p-5">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-download text-gray-600"></i> Export</h3>
                        <div class="space-y-2">
                            <a href="{{ route("resume.export.pdf",$resume) }}" class="block w-full py-2 px-4 bg-red-600 text-white text-center rounded-lg font-medium hover:bg-red-700 transition text-sm"><i class="fas fa-file-pdf mr-2"></i> Download PDF</a>
                            <a href="{{ route("resume.export.docx",$resume) }}" class="block w-full py-2 px-4 bg-blue-600 text-white text-center rounded-lg font-medium hover:bg-blue-700 transition text-sm"><i class="fas fa-file-word mr-2"></i> Download DOCX</a>
                        </div>
                    </div>
                    <!-- ATS Score Widget -->
                    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                        @php
                            $atsScore = $resume->numeric_ats_score;
                            $atsColor = $atsScore ? ($atsScore >= 80 ? '#34a853' : ($atsScore >= 60 ? '#fbbc04' : '#ea4335')) : '#9ca3af';
                            $atsLabel = $atsScore ? ($atsScore >= 80 ? 'Excellent' : ($atsScore >= 60 ? 'Good' : 'Needs Work')) : 'Not Checked';
                            $atsCats  = $resume->ats_analysis['categories'] ?? [];
                            $atsDash  = $atsScore ? round($atsScore * 2.76) : 0;
                        @endphp
                        <div style="background:linear-gradient(135deg,#1e1b4b,#4c1d95);padding:14px 16px;">
                            <h3 style="color:#fff;font-size:13px;font-weight:700;margin:0;display:flex;align-items:center;gap:6px;">
                                <i class="fas fa-chart-line" style="color:#a78bfa;"></i> ATS Score
                            </h3>
                            <p style="color:rgba(255,255,255,.6);font-size:11px;margin:2px 0 0;">Resume compatibility analysis</p>
                        </div>
                        <div style="padding:18px;text-align:center;">
                            <div style="width:100px;height:100px;margin:0 auto 12px;">
                                <svg width="100" height="100" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="44" fill="none" stroke="#f0f0f0" stroke-width="10"/>
                                    <circle cx="50" cy="50" r="44" fill="none" stroke="{{ $atsColor }}" stroke-width="10"
                                        stroke-dasharray="{{ $atsDash }} 276" stroke-dashoffset="69"
                                        stroke-linecap="round" transform="rotate(-90 50 50)"/>
                                    <text x="50" y="47" text-anchor="middle" font-size="22" font-weight="800" fill="#1a1a2e">{{ $atsScore ?? '—' }}</text>
                                    <text x="50" y="62" text-anchor="middle" font-size="10" fill="#888">{{ $atsScore ? '/100' : '' }}</text>
                                </svg>
                            </div>
                            <div style="font-size:15px;font-weight:800;color:{{ $atsColor }};">{{ $atsLabel }}</div>
                            @if($atsScore && !empty($atsCats))
                            <div style="margin-top:12px;display:flex;flex-direction:column;gap:5px;text-align:left;">
                                @foreach([['Content',$atsCats['content']??[],25],['Skills',$atsCats['skills']??[],20],['Format',$atsCats['format']??[],20],['Sections',$atsCats['sections']??[],20],['Style',$atsCats['style']??[],15]] as [$cn,$cc,$cm])
                                @if(!empty($cc))
                                @php $cs=(int)($cc['score']??0);$cl=$cc['label']??'Needs Work';$cColor=$cl==='Excellent'?'#34a853':($cl==='Good'?'#fbbc04':'#ea4335'); @endphp
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:11px;color:#555;">{{ $cn }}</span>
                                    <div style="display:flex;align-items:center;gap:5px;">
                                        <div style="width:55px;height:4px;background:#eee;border-radius:2px;overflow:hidden;"><div style="width:{{ $cm>0?round($cs/$cm*100):0 }}%;height:100%;background:{{ $cColor }};border-radius:2px;"></div></div>
                                        <span style="font-size:10px;font-weight:700;color:{{ $cColor }};">{{ $cs }}/{{ $cm }}</span>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <p style="font-size:12px;color:#888;margin:8px 0 0;line-height:1.5;">Get a detailed breakdown of how well your resume passes ATS filters.</p>
                            @endif
                            <a href="{{ route('resume.ats.show', $resume) }}" style="display:block;margin-top:14px;padding:11px;background:linear-gradient(135deg,#1A73E8,#0d47a1);color:#fff;border-radius:8px;font-weight:700;font-size:13px;text-decoration:none;">
                                {{ $atsScore ? '📊 Full ATS Report →' : '🔍 Check ATS Score' }}
                            </a>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>
    @push("scripts")
    <script>
    let expCount={{ count($resume->experience??[]) }},eduCount={{ count($resume->education??[]) }},certCount={{ count($resume->certifications??[]) }},projCount={{ count($resume->projects??[]) }},achCount={{ count($resume->achievements??[]) }},langCount={{ count($resume->languages??[]) }},volCount={{ count($resume->volunteer_work??[]) }};
    const summaryInput=document.getElementById("summaryInput"),summaryCount=document.getElementById("summary-count");
    // Template picker
    function onTemplateChange(radio, id) {
        document.querySelectorAll('.template-card').forEach(function(c) {
            c.classList.remove('border-indigo-600','ring-2','ring-indigo-200');
            c.classList.add('border-gray-200');
        });
        document.querySelectorAll('[id^="check-"]').forEach(function(el) { el.classList.add('hidden'); });
        const card = document.getElementById('tcard-' + id);
        if (card) { card.classList.add('border-indigo-600','ring-2','ring-indigo-200'); card.classList.remove('border-gray-200'); }
        const check = document.getElementById('check-' + id);
        if (check) check.classList.remove('hidden');
        showToast('Template selected! Click Save to apply.', 'info');
    }
    function updateSC(){if(summaryInput&&summaryCount)summaryCount.textContent=summaryInput.value.length+" / 600 chars";}
    summaryInput?.addEventListener("input",updateSC);updateSC();
    function removeItem(btn,sel){btn.closest(sel).remove();}
    function setCurrentJob(cb){const inp=cb.closest(".experience-item")?.querySelector(".end-date-input");if(inp)inp.value=cb.checked?"Present":"";}
    function showToast(msg,type="info"){const c={success:"bg-green-600",error:"bg-red-600",info:"bg-blue-600"};const t=document.createElement("div");t.className=`fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg text-white text-sm font-medium shadow-lg ${c[type]}`;t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.remove(),4000);}
    function addExperience(){const n=expCount++;const h=`<div class="experience-item border border-gray-200 rounded-lg overflow-hidden"><div class="bg-gray-50 px-4 py-3 flex justify-between items-center"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-building mr-2 text-gray-400"></i>New Job</span><button type="button" onclick="removeItem(this,'.experience-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-xs font-medium text-gray-600 mb-1">Job Title <span class="text-red-500">*</span></label><input type="text" name="experience[${n}][position]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Software Engineer"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Company <span class="text-red-500">*</span></label><input type="text" name="experience[${n}][company]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Acme Corp"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" name="experience[${n}][location]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Bangalore / Remote"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><select name="experience[${n}][employment_type]" class="w-full rounded-md border-gray-300 text-sm"><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option><option>Freelance</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="experience[${n}][start_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jan 2022"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><div class="space-y-1"><input type="text" name="experience[${n}][end_date]" class="w-full rounded-md border-gray-300 text-sm end-date-input" placeholder="Dec 2024 or Present"><label class="flex items-center gap-1.5 text-xs text-gray-600"><input type="checkbox" onchange="setCurrentJob(this)" class="rounded"> Currently here</label></div></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description & Responsibilities</label><textarea name="experience[${n}][description]" rows="3" class="w-full rounded-md border-gray-300 text-sm" placeholder="Key responsibilities..."></textarea></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Key Achievements (one per line)</label><textarea name="experience[${n}][achievements]" rows="4" class="w-full rounded-md border-gray-300 text-sm font-mono" placeholder="Reduced API response time by 45% via Redis&#10;Led team of 5 engineers&#10;Increased revenue by Rs.12L"></textarea><p class="text-xs text-gray-400 mt-1">Quantify results — %, Rs., time saved, team size</p></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Technologies Used</label><input type="text" name="experience[${n}][technologies]" class="w-full rounded-md border-gray-300 text-sm" placeholder="PHP, Laravel, MySQL, Redis, Docker, AWS"></div></div></div>`;document.getElementById("exp-empty-state")?.remove();document.getElementById("experience-list").insertAdjacentHTML("beforeend",h);}
    function addEducation(){const n=eduCount++;const h=`<div class="education-item border border-gray-200 rounded-lg overflow-hidden"><div class="bg-gray-50 px-4 py-3 flex justify-between items-center"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-university mr-2 text-gray-400"></i>New Education</span><button type="button" onclick="removeItem(this,'.education-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-xs font-medium text-gray-600 mb-1">Institution <span class="text-red-500">*</span></label><input type="text" name="education[${n}][institution]" class="w-full rounded-md border-gray-300 text-sm" placeholder="IIT Bombay"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Degree <span class="text-red-500">*</span></label><input type="text" name="education[${n}][degree]" class="w-full rounded-md border-gray-300 text-sm" placeholder="B.Tech / M.Tech / MBA"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Field of Study</label><input type="text" name="education[${n}][field]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Computer Science"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" name="education[${n}][location]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mumbai, India"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Year</label><input type="text" name="education[${n}][start_year]" class="w-full rounded-md border-gray-300 text-sm" placeholder="2019"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Year</label><input type="text" name="education[${n}][end_year]" class="w-full rounded-md border-gray-300 text-sm" placeholder="2023 or Ongoing"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">CGPA / %</label><input type="text" name="education[${n}][gpa]" class="w-full rounded-md border-gray-300 text-sm" placeholder="8.5/10 or 85%"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Honors</label><input type="text" name="education[${n}][honors]" class="w-full rounded-md border-gray-300 text-sm" placeholder="First Class with Distinction"></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Relevant Coursework</label><input type="text" name="education[${n}][coursework]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Data Structures, DBMS, Machine Learning"></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Activities & Achievements</label><textarea name="education[${n}][activities]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="President Coding Club, Hackathon winner"></textarea></div></div></div>`;document.getElementById("edu-empty-state")?.remove();document.getElementById("education-list").insertAdjacentHTML("beforeend",h);}
    function addCertification(){const n=certCount++;const h=`<div class="certification-item border border-gray-200 rounded-lg p-4"><div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-award text-rose-400 mr-2"></i>New Certification</span><button type="button" onclick="removeItem(this,'.certification-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="grid grid-cols-1 md:grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Certification Name <span class="text-red-500">*</span></label><input type="text" name="certifications[${n}][name]" class="w-full rounded-md border-gray-300 text-sm" placeholder="AWS Certified Solutions Architect"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Issuing Organization <span class="text-red-500">*</span></label><input type="text" name="certifications[${n}][issuer]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Amazon Web Services"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label><input type="text" name="certifications[${n}][issue_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="March 2024"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label><input type="text" name="certifications[${n}][expiry_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="March 2027 or No Expiry"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Credential ID</label><input type="text" name="certifications[${n}][credential_id]" class="w-full rounded-md border-gray-300 text-sm" placeholder="ABC-123456"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Credential URL</label><input type="url" name="certifications[${n}][url]" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://credly.com/badges/..."></div></div></div>`;document.getElementById("cert-empty-state")?.remove();document.getElementById("certifications-list").insertAdjacentHTML("beforeend",h);}
    function addProject(){const n=projCount++;const h=`<div class="project-item border border-gray-200 rounded-lg overflow-hidden"><div class="bg-gray-50 px-4 py-3 flex justify-between items-center"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-project-diagram mr-2 text-indigo-400"></i>New Project</span><button type="button" onclick="removeItem(this,'.project-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3"><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Project Name <span class="text-red-500">*</span></label><input type="text" name="projects[${n}][name]" class="w-full rounded-md border-gray-300 text-sm" placeholder="E-commerce Platform"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="projects[${n}][start_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jan 2024"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="text" name="projects[${n}][end_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mar 2024 or Ongoing"></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description & Impact</label><textarea name="projects[${n}][description]" rows="3" class="w-full rounded-md border-gray-300 text-sm" placeholder="Built a multi-tenant SaaS serving 500+ daily users. Reduced checkout time by 30%."></textarea></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Technologies Used</label><input type="text" name="projects[${n}][technologies]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Laravel, React, MySQL, Redis, Tailwind CSS"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Live URL</label><input type="url" name="projects[${n}][url]" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://myproject.com"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">GitHub / Source</label><input type="url" name="projects[${n}][github_url]" class="w-full rounded-md border-gray-300 text-sm" placeholder="https://github.com/user/repo"></div></div></div>`;document.getElementById("proj-empty-state")?.remove();document.getElementById("projects-list").insertAdjacentHTML("beforeend",h);}
    function addAchievement(){const n=achCount++;const h=`<div class="achievement-item border border-gray-200 rounded-lg p-4"><div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm"><i class="fas fa-medal mr-2 text-amber-400"></i>New Achievement</span><button type="button" onclick="removeItem(this,'.achievement-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="grid grid-cols-1 md:grid-cols-2 gap-3"><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Achievement Title <span class="text-red-500">*</span></label><input type="text" name="achievements[${n}][title]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Winner — Smart India Hackathon 2023"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Issuer / Organization</label><input type="text" name="achievements[${n}][issuer]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Ministry of Education, India"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input type="text" name="achievements[${n}][date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="December 2023"></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="achievements[${n}][description]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="Developed an AI-powered solution; won Rs.1L prize among 10,000 teams nationally."></textarea></div></div></div>`;document.getElementById("ach-empty-state")?.remove();document.getElementById("achievements-list").insertAdjacentHTML("beforeend",h);}
    function addLanguage(){const n=langCount++;const h=`<div class="language-item flex items-center gap-4 p-3 border border-gray-200 rounded-lg"><div class="flex-1"><input type="text" name="languages[${n}][name]" class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g., Hindi"></div><div class="w-48"><select name="languages[${n}][proficiency]" class="w-full rounded-md border-gray-300 text-sm"><option>Native</option><option>Fluent</option><option>Advanced</option><option>Intermediate</option><option>Basic</option></select></div><button type="button" onclick="removeItem(this,'.language-item')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash text-sm"></i></button></div>`;document.getElementById("lang-empty-state")?.remove();document.getElementById("languages-list").insertAdjacentHTML("beforeend",h);}
    function addVolunteer(){const n=volCount++;const h=`<div class="volunteer-item border border-gray-200 rounded-lg p-4"><div class="flex justify-between items-center mb-3"><span class="font-medium text-gray-700 text-sm">New Volunteer / Activity</span><button type="button" onclick="removeItem(this,'.volunteer-item')" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></div><div class="grid grid-cols-1 md:grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Role</label><input type="text" name="volunteer_work[${n}][role]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mentor / Volunteer Developer"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Organization</label><input type="text" name="volunteer_work[${n}][organization]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Code for India / NSS"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="text" name="volunteer_work[${n}][start_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="June 2022"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="text" name="volunteer_work[${n}][end_date]" class="w-full rounded-md border-gray-300 text-sm" placeholder="Ongoing"></div><div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="volunteer_work[${n}][description]" rows="2" class="w-full rounded-md border-gray-300 text-sm" placeholder="Mentored 30+ students in web development basics."></textarea></div></div></div>`;document.getElementById("vol-empty-state")?.remove();document.getElementById("volunteer-list").insertAdjacentHTML("beforeend",h);}
    async function generateSummary(btn){const orig=btn?.innerHTML;if(btn){btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Generating...';}try{const r=await fetch('{{ route("resume.ai.generate-summary",$resume) }}',{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}","Accept":"application/json"}});const d=await r.json();if(d.success&&d.summary){document.getElementById("summaryInput").value=d.summary;updateSC();showToast("AI summary generated!","success");}else{showToast("AI generation failed. Please try again.","error");}}catch(e){showToast("Could not reach AI service.","error");}finally{if(btn){btn.disabled=false;btn.innerHTML=orig;}}}
    async function extractSkillsAI(btn){const orig=btn?.innerHTML;if(btn){btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Extracting...';}try{const r=await fetch('{{ route("resume.ai.extract-skills",$resume) }}',{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}","Accept":"application/json"}});const d=await r.json();if(d.success&&d.skills){const skills=Array.isArray(d.skills)?d.skills.filter(s=>typeof s==="string"):[];const technical=skills.join(", ");if(technical){const el=document.getElementById("skills-technical");el.value=el.value?el.value+", "+technical:technical;}showToast("Skills extracted from your experience!","success");}else{showToast("Skill extraction failed. Add more experience details first.","error");}}catch(e){showToast("Could not reach AI service.","error");}finally{if(btn){btn.disabled=false;btn.innerHTML=orig;}}}
    </script>
    @endpush
</x-app-layout>



