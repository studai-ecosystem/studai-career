<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <x-ui.responsive-container size="lg" padding="none">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Build Your Career Profile</h1>
            <p class="text-lg text-gray-600">Complete your profile to unlock AI-powered job matching</p>
        </div>

        {{-- Progress Bar --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                <span class="text-sm font-bold text-primary-600">{{ $this->completionPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-primary-500 to-secondary-500 h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $this->completionPercentage }}%"></div>
            </div>
        </div>

        {{-- Step Wizard Component --}}
        <x-ui.step-wizard 
            :steps="[
                ['title' => 'Resume', 'description' => 'Upload your resume'],
                ['title' => 'Basics', 'description' => 'Professional info'],
                ['title' => 'Experience', 'description' => 'Work history'],
                ['title' => 'Education', 'description' => 'Academic background'],
                ['title' => 'Skills', 'description' => 'Your expertise'],
                ['title' => 'Finish', 'description' => 'Links & preferences'],
            ]"
            :current-step="$currentStep"
            :completed-steps="range(1, $currentStep - 1)"
            :allow-jump-ahead="true"
            size="md"
            class="mb-8"
        />

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Main Content Card --}}
        <div class="bg-white rounded-xl shadow-xl p-8">
            
            {{-- Step 1: Resume Upload --}}
            @if ($currentStep === 1)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Upload Your Resume</h2>
                    <p class="text-gray-600 mb-6">Upload your resume and let our AI extract your professional information automatically — or fill in manually.</p>

                    {{-- Inline error --}}
                    @if ($analysisError)
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-red-800 text-sm font-medium">{{ $analysisError }}</p>
                                <button wire:click="skipResume" class="mt-1 text-sm text-red-700 underline hover:text-red-900">Fill in details manually →</button>
                            </div>
                        </div>
                    @endif

                    {{-- Analysis success banner --}}
                    @if ($analysisComplete)
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-800 text-sm font-medium">Resume analyzed! Your details have been pre-filled. Review and edit each step.</p>
                        </div>
                    @endif

                    <div x-data="{ fileName: '' }" class="border-2 border-dashed rounded-lg p-12 text-center transition-colors"
                         :style="fileName ? 'border-color:#1E8E3E;background:#EDFAF2;' : 'border-color:#C8C8C5;background:#fff;'">

                        {{-- Icon: changes to checkmark when file picked --}}
                        <template x-if="!fileName">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </template>
                        <template x-if="fileName">
                            <div style="margin-bottom:16px;">
                                <svg style="width:56px;height:56px;margin:0 auto 8px;color:#1E8E3E;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p style="font-size:15px;font-weight:700;color:#1E8E3E;margin:0 0 4px 0;" x-text="fileName"></p>
                                <p style="font-size:12px;color:#1E8E3E;">Ready to analyse ✓</p>
                            </div>
                        </template>

                        <div wire:loading.remove wire:target="resumeFile">
                            <input type="file" wire:model="resumeFile" accept=".pdf,.doc,.docx,.txt"
                                   class="hidden" id="resumeUpload"
                                   @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            <label for="resumeUpload" class="cursor-pointer">
                                <span x-show="!fileName" style="color:#2D6CDF;font-weight:600;">Click to upload</span>
                                <span x-show="!fileName" style="color:#737373;"> or drag and drop</span>
                                <span x-show="fileName" style="color:#1E8E3E;font-weight:600;">Change file</span>
                            </label>
                            <p x-show="!fileName" class="text-sm text-gray-500 mt-2">PDF, DOC, DOCX, or TXT (Max 5MB)</p>
                        </div>

                        <div wire:loading wire:target="resumeFile" class="text-primary-600">
                            <svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p>Uploading...</p>
                        </div>
                    </div>

                    @error('resumeFile') 
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p> 
                    @enderror

                    @if ($resumeFile && !$analyzing && !$analysisComplete)
                        <div class="mt-6 flex justify-center">
                            <button wire:click="uploadResume" wire:loading.attr="disabled"
                                    style="display:inline-flex;align-items:center;gap:8px;padding:12px 32px;background:#2D6CDF;color:white;font-weight:700;font-size:15px;border:none;border-radius:12px;cursor:pointer;box-shadow: none;transition:all .2s;">
                                <span wire:loading.remove wire:target="uploadResume">✨ Analyse Resume with AI</span>
                                <span wire:loading wire:target="uploadResume">Analyzing...</span>
                            </button>
                        </div>
                    @endif

                    @if ($analyzing)
                        <div class="mt-6 text-center">
                            <div class="inline-flex items-center gap-3 bg-blue-50 px-6 py-4 rounded-lg">
                                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-blue-900 font-medium">Analyzing your resume with AI... {{ $uploadProgress }}%</span>
                            </div>
                        </div>
                    @endif

                    {{-- Divider --}}
                    <div class="mt-8 flex items-center gap-4">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <span class="text-sm text-gray-400 font-medium">OR</span>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>

                    <div class="mt-4 text-center">
                        <button wire:click="skipResume" 
                                style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#ffffff;color:#3D3D3D;font-weight:600;font-size:14px;border:1.5px solid #EBF2FF;border-radius:12px;cursor:pointer;box-shadow: none;transition:all .2s;">
                            <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Fill in manually
                        </button>
                    </div>
                </div>
            @endif

            {{-- Step 2: Basic Info --}}
            @if ($currentStep === 2)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Basic Information</h2>
                    <p class="text-gray-600 mb-6">Tell us about your professional background</p>

                    <div class="space-y-6">
                        <div x-data="{
                                selected: null,
                                suggestions: @js($headlineSuggestions),
                                pick(i) {
                                    this.selected = i;
                                    var val = this.suggestions[i];
                                    $wire.set('headline', val);
                                    document.getElementById('headline').value = val;
                                    document.getElementById('headline').dispatchEvent(new Event('input'));
                                }
                            }" x-init="$watch('$wire.headlineSuggestions', v => { suggestions = v; selected = 0; if(v && v[0]) pick(0); })">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Professional Headline *</label>
                                <button type="button"
                                        wire:click="generateHeadline"
                                        wire:loading.attr="disabled"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#2D6CDF;color:white;font-size:12px;font-weight:600;border:none;border-radius:8px;cursor:pointer;box-shadow: none;transition:all .2s;">
                                    <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="generateHeadline">✨ AI Generate</span>
                                    <span wire:loading wire:target="generateHeadline" style="display:none">Generating 5 options...</span>
                                </button>
                            </div>
                            <textarea
                                wire:model="headline"
                                id="headline"
                                rows="2"
                                maxlength="255"
                                placeholder="e.g., Senior Software Engineer with 8+ years in cloud architecture"
                                style="width:100%;padding:12px 14px;border:1.5px solid #C8C8C5;border-radius:10px;font-size:14px;color:#0C0C0C;resize:vertical;outline:none;transition:border-color .2s;font-family:inherit;line-height:1.5;"
                                onfocus="this.style.borderColor='#2D6CDF'"
                                onblur="this.style.borderColor='#C8C8C5'"
                            ></textarea>
                            <div style="text-align:right;font-size:11px;color:#A8A8A8;margin-top:3px;">
                                <span x-data x-text="($wire.headline || '').length"></span>/255
                            </div>
                            @error('headline') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                            {{-- AI Suggestions Panel --}}
                            <template x-if="suggestions && suggestions.length > 0">
                                <div style="margin-top:14px;padding:16px;background:#EBF2FF;border:1.5px solid #BFCFEE;border-radius:12px;">
                                    <p style="font-size:11px;font-weight:700;color:#2D6CDF;text-transform:uppercase;letter-spacing:.07em;margin:0 0 10px 0;">
                                        ✨ Click a suggestion to use it — then edit freely
                                    </p>
                                    <div style="display:flex;flex-direction:column;gap:8px;">
                                        <template x-for="(s, i) in suggestions" :key="i">
                                            <button type="button"
                                                    @click="pick(i)"
                                                    :style="selected === i
                                                        ? 'background:#2D6CDF;color:#ffffff;border:2px solid #2D6CDF;font-weight:600;box-shadow: none;text-align:left;padding:11px 14px;border-radius:9px;font-size:13px;line-height:1.45;cursor:pointer;width:100%;'
                                                        : 'background:#ffffff;color:#0C0C0C;border:1.5px solid #BFCFEE;font-weight:400;text-align:left;padding:11px 14px;border-radius:9px;font-size:13px;line-height:1.45;cursor:pointer;width:100%;'">
                                                <span style="opacity:.5;font-size:11px;font-weight:700;margin-right:6px;" x-text="(i+1)+'.'"></span><span x-text="s"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <p style="font-size:11px;color:#A8A8A8;margin:10px 0 0 0;">After selecting, you can edit the text in the field above.</p>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Professional Summary *</label>
                            <x-ui.ai-textarea 
                                wire:model="summary"
                                field="summary"
                                placeholder="Write a compelling summary of your professional experience, skills, and career goals..."
                                :max-length="1000"
                                :rows="6"
                                :show-ai-button="true"
                                :show-enhance-button="true"
                                :show-tone-selector="true"
                            />
                            @error('summary') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Location</label>
                            <div x-data="{
                                query: @entangle('current_location'),
                                open: false,
                                highlighted: 0,
                                all: [
                                    'Remote',
                                    'Ahmedabad, Gujarat','Bengaluru, Karnataka','Bhopal, Madhya Pradesh',
                                    'Bhubaneswar, Odisha','Chandigarh, Punjab','Chennai, Tamil Nadu',
                                    'Coimbatore, Tamil Nadu','Dehradun, Uttarakhand','Delhi','Faridabad, Haryana',
                                    'Ghaziabad, Uttar Pradesh','Gurugram, Haryana','Guwahati, Assam',
                                    'Hyderabad, Telangana','Indore, Madhya Pradesh','Jaipur, Rajasthan',
                                    'Kochi, Kerala','Kolkata, West Bengal','Lucknow, Uttar Pradesh',
                                    'Ludhiana, Punjab','Mangaluru, Karnataka','Mumbai, Maharashtra',
                                    'Mysuru, Karnataka','Nagpur, Maharashtra','Nashik, Maharashtra',
                                    'Noida, Uttar Pradesh','Patna, Bihar','Pune, Maharashtra',
                                    'Raipur, Chhattisgarh','Ranchi, Jharkhand','Surat, Gujarat',
                                    'Thiruvananthapuram, Kerala','Vadodara, Gujarat','Varanasi, Uttar Pradesh',
                                    'Vijayawada, Andhra Pradesh','Visakhapatnam, Andhra Pradesh',
                                    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
                                    'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
                                    'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
                                    'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
                                    'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                                ],
                                get suggestions() {
                                    if (!this.query || this.query.length < 2) return [];
                                    const q = this.query.toLowerCase();
                                    return this.all.filter(c => c.toLowerCase().includes(q)).slice(0, 8);
                                },
                                select(val) { this.query = val; this.open = false; },
                                onInput() { this.open = true; this.highlighted = 0; },
                                onKeydown(e) {
                                    if (!this.open) return;
                                    if (e.key === 'ArrowDown') { e.preventDefault(); this.highlighted = Math.min(this.highlighted + 1, this.suggestions.length - 1); }
                                    else if (e.key === 'ArrowUp') { e.preventDefault(); this.highlighted = Math.max(this.highlighted - 1, 0); }
                                    else if (e.key === 'Enter' && this.suggestions[this.highlighted]) { e.preventDefault(); this.select(this.suggestions[this.highlighted]); }
                                    else if (e.key === 'Escape') { this.open = false; }
                                }
                            }" @click.away="open = false" style="position:relative;">
                                <input
                                    type="text"
                                    x-model="query"
                                    @input="onInput"
                                    @keydown="onKeydown"
                                    @focus="open = suggestions.length > 0"
                                    placeholder="Type your city, e.g. Mumbai, Bengaluru..."
                                    style="width:100%;padding:10px 14px;border:1.5px solid #C8C8C5;border-radius:9px;font-size:14px;color:#0C0C0C;outline:none;transition:border-color .2s;"
                                    autocomplete="off"
                                />
                                <div x-show="open && suggestions.length > 0"
                                     x-transition
                                     style="position:absolute;z-index:999;left:0;right:0;top:calc(100% + 4px);background:#fff;border:1.5px solid #BFCFEE;border-radius:10px;box-shadow: none;overflow:hidden;">
                                    <template x-for="(s, i) in suggestions" :key="s">
                                        <div @click="select(s)"
                                             :style="i === highlighted ? 'background:#2D6CDF;color:#fff;' : 'background:#fff;color:#0C0C0C;'"
                                             style="padding:10px 16px;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                                            <svg style="width:13px;height:13px;opacity:.6;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span x-text="s"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            @error('current_location') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step 3: Experience (truncated for file length - similar to above patterns) --}}
            @if ($currentStep === 3)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Work Experience</h2>
                    <p class="text-gray-600 mb-6">Add your relevant work experience. Use AI to help write descriptions and achievements!</p>
                    
                    <x-ui.experience-builder 
                        wire:model="experience"
                        :max-entries="10"
                    />
                </div>
            @endif

            {{-- Step 4: Education --}}
            @if ($currentStep === 4)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Education</h2>
                    <p class="text-gray-600 mb-6">Add your educational background</p>
                    
                    <x-ui.education-builder 
                        wire:model="education"
                        :max-entries="5"
                    />
                </div>
            @endif

            {{-- Step 5: Skills --}}
            @if ($currentStep === 5)
                <div x-data="{
                    skills: @entangle('skills'),
                    search: '',
                    aiLoading: false,
                    aiError: '',
                    newProf: 'intermediate',
                    common: ['JavaScript','TypeScript','Python','PHP','Java','C#','React','Vue.js','Angular','Next.js',
                             'Node.js','Laravel','Django','HTML','CSS','Tailwind CSS','SQL','PostgreSQL','MySQL',
                             'MongoDB','Redis','Docker','Kubernetes','AWS','Azure','GCP','Git','REST API','GraphQL',
                             'Machine Learning','Data Science','Communication','Leadership','Teamwork','Problem Solving',
                             'Critical Thinking','Figma','Jira','Notion','Postman','GitHub','English','Hindi',
                             'Spanish','French','German','C++','Go','Swift','Kotlin','TypeScript','Bootstrap'],
                    get filtered() {
                        if (this.search.length < 1) return [];
                        const q = this.search.toLowerCase();
                        const existing = this.skills.map(s => s.name.toLowerCase());
                        return this.common.filter(c => c.toLowerCase().includes(q) && !existing.includes(c.toLowerCase())).slice(0,8);
                    },
                    addSkill(name, prof) {
                        prof = prof || this.newProf;
                        if (this.skills.length >= 20) return;
                        if (this.skills.some(s => s.name.toLowerCase() === name.toLowerCase())) return;
                        this.skills.push({ name: name, proficiency: prof, years: 1, category: 'technical' });
                        this.search = '';
                    },
                    removeSkill(i) { this.skills.splice(i, 1); },
                    profColor(p) {
                        return {beginner:'#A8A8A8',intermediate:'#2D6CDF',advanced:'#1E8E3E',expert:'#2D6CDF'}[p]||'#2D6CDF';
                    },
                    async suggestAI() {
                        this.aiLoading = true; this.aiError = '';
                        try {
                            const r = await fetch('/api/ai/suggest-skills', {
                                method:'POST',
                                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                                body: JSON.stringify({ context: $wire.headline, existing_skills: this.skills.map(s=>s.name) })
                            });
                            const d = await r.json();
                            if (d.skills && Array.isArray(d.skills)) {
                                d.skills.forEach(s => this.addSkill(s.name||s, s.proficiency||'intermediate'));
                            } else { this.aiError = d.error || 'No suggestions returned.'; }
                        } catch(e) { this.aiError = 'AI suggestion failed. Please try again.'; }
                        finally { this.aiLoading = false; }
                    }
                }">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Skills & Expertise</h2>
                    <p class="text-gray-600 mb-6">Add your skills. Type to search from common skills or add custom ones.</p>

                    {{-- Search + Add row --}}
                    <div style="display:flex;gap:8px;margin-bottom:12px;position:relative;">
                        <div style="flex:1;position:relative;">
                            <input type="text" x-model="search" placeholder="Search or type a skill..."
                                   style="width:100%;padding:10px 14px;border:1.5px solid #C8C8C5;border-radius:9px;font-size:14px;outline:none;"
                                   onfocus="this.style.borderColor='#2D6CDF'" onblur="this.style.borderColor='#C8C8C5'"
                                   @keydown.enter.prevent="search.trim() && addSkill(search.trim())">
                            {{-- Dropdown suggestions --}}
                            <div x-show="filtered.length > 0" style="position:absolute;z-index:50;left:0;right:0;top:calc(100% + 4px);background:#fff;border:1.5px solid #BFCFEE;border-radius:10px;box-shadow: none;overflow:hidden;">
                                <template x-for="s in filtered" :key="s">
                                    <div @click="addSkill(s)" style="padding:9px 14px;cursor:pointer;font-size:13px;color:#0C0C0C;" onmouseover="this.style.background='#EBF2FF'" onmouseout="this.style.background='#fff'" x-text="s"></div>
                                </template>
                            </div>
                        </div>
                        <button type="button" @click="search.trim() && addSkill(search.trim())"
                                style="padding:10px 20px;background:#2D6CDF;color:#fff;font-weight:600;font-size:13px;border:none;border-radius:9px;cursor:pointer;white-space:nowrap;">
                            + Add
                        </button>
                        <button type="button" @click="suggestAI" :disabled="aiLoading"
                                style="padding:10px 16px;background:#2D6CDF;color:#fff;font-weight:600;font-size:13px;border:none;border-radius:9px;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                            <svg style="width:13px;height:13px" x-show="!aiLoading" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <svg style="width:13px;height:13px" x-show="aiLoading" class="animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity=".75"/></svg>
                            <span x-text="aiLoading ? 'Suggesting...' : '✨ AI Suggest'"></span>
                        </button>
                    </div>

                    <p x-show="aiError" x-text="aiError" style="color:#2D6CDF;font-size:12px;margin-bottom:8px;"></p>

                    {{-- Skills list --}}
                    <div x-show="skills.length === 0" style="padding:32px;text-align:center;color:#A8A8A8;font-size:14px;border:1.5px dashed #E2E2E0;border-radius:12px;">
                        No skills added yet. Search above or click ✨ AI Suggest.
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <template x-for="(skill, i) in skills" :key="i">
                            <div style="display:inline-flex;align-items:center;gap:8px;padding:7px 12px;background:#fff;border:1.5px solid #BFCFEE;border-radius:999px;font-size:13px;color:#0C0C0C;">
                                <span style="width:8px;height:8px;border-radius:50%;flex-shrink:0;" :style="'background:'+profColor(skill.proficiency)"></span>
                                <span x-text="skill.name"></span>
                                <select x-model="skill.proficiency" style="font-size:11px;border:none;outline:none;background:transparent;color:#737373;cursor:pointer;padding:0;">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                                <button type="button" @click="removeSkill(i)" style="color:#A8A8A8;background:none;border:none;cursor:pointer;padding:0;line-height:1;font-size:16px;">&times;</button>
                            </div>
                        </template>
                    </div>

                    <p style="font-size:12px;color:#A8A8A8;margin-top:12px;" x-text="skills.length + ' / 20 skills added'"></p>
                </div>
            @endif

            {{-- Step 6: Links & Preferences --}}
            @if ($currentStep === 6)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Almost Done!</h2>
                    <p class="text-gray-600 mb-6">Add your professional links and preferences</p>
                    
                    <div class="space-y-6">
                        {{-- Social Links Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                    LinkedIn
                                </label>
                                <input type="url" wire:model="linkedin_url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="https://linkedin.com/in/...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                    GitHub
                                </label>
                                <input type="url" wire:model="github_url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="https://github.com/...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm-1 19.231V12H9v-1.969h2V8.188c0-2.013 1.214-3.106 3.06-3.106.859 0 1.758.154 1.758.154v1.969h-.991c-.98 0-1.287.605-1.287 1.227v1.537h2.218l-.354 1.969h-1.864v7.231h3.46C18.521 17.625 20 14.987 20 12c0-4.418-3.582-8-8-8s-8 3.582-8 8c0 3.987 2.921 7.279 6.731 7.872v-5.641z"/></svg>
                                    Portfolio
                                </label>
                                <input type="url" wire:model="portfolio_url" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="https://yoursite.com">
                            </div>
                        </div>
                        
                        {{-- Salary Expectations --}}
                        <div>
                            <h3 class="text-lg font-semibold mb-4">💰 Salary Expectations</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div>
                                    <label style="display:block;font-size:13px;font-weight:600;color:#3D3D3D;margin-bottom:6px;">Minimum (₹ / year)</label>
                                    <input type="number"
                                           wire:model="expected_salary_min"
                                           min="0" step="10000"
                                           placeholder="e.g. 500000"
                                           style="width:100%;padding:11px 14px;border:1.5px solid #C8C8C5;border-radius:9px;font-size:14px;color:#0C0C0C;outline:none;transition:border-color .2s;"
                                           onfocus="this.style.borderColor='#2D6CDF'"
                                           onblur="this.style.borderColor='#C8C8C5'">
                                </div>
                                <div>
                                    <label style="display:block;font-size:13px;font-weight:600;color:#3D3D3D;margin-bottom:6px;">Maximum (₹ / year)</label>
                                    <input type="number"
                                           wire:model="expected_salary_max"
                                           min="0" step="10000"
                                           placeholder="e.g. 1200000"
                                           style="width:100%;padding:11px 14px;border:1.5px solid #C8C8C5;border-radius:9px;font-size:14px;color:#0C0C0C;outline:none;transition:border-color .2s;"
                                           onfocus="this.style.borderColor='#2D6CDF'"
                                           onblur="this.style.borderColor='#C8C8C5'">
                                </div>
                            </div>
                            <p style="font-size:12px;color:#A8A8A8;margin-top:8px;">Enter your expected annual salary range. You can type or use the up/down arrows.</p>
                        </div>
                        
                        {{-- Career Goals --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🎯 Career Goals</label>
                            <x-ui.ai-textarea 
                                wire:model="career_goals"
                                field="career_goals"
                                placeholder="What are your career aspirations? Where do you see yourself in 5 years?"
                                :max-length="1000"
                                :rows="4"
                                :show-ai-button="true"
                                :show-enhance-button="true"
                            />
                        </div>
                        
                        {{-- Completion Card --}}
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">🎉</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-green-900">Your Profile is {{ $this->completionPercentage }}% Complete!</h3>
                                    <p class="text-green-700">Click "Complete Profile" to start receiving AI-powered job matches and career recommendations.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Navigation --}}
            <div class="mt-8 flex justify-between">
                @if ($currentStep > 1)
                    <button wire:click="previousStep" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-all">
                        ← Previous
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button wire:click="nextStep"
                            style="display:inline-flex;align-items:center;gap:6px;padding:12px 28px;background:#2D6CDF;color:white;font-weight:600;font-size:14px;border:none;border-radius:12px;cursor:pointer;box-shadow: none;transition:all .2s;">
                        Next Step →
                    </button>
                @else
                    <button wire:click="saveProfile"
                            style="display:inline-flex;align-items:center;gap:6px;padding:12px 32px;background:#1E8E3E;color:white;font-weight:700;font-size:15px;border:none;border-radius:12px;cursor:pointer;box-shadow: none;transition:all .2s;">
                        ✓ Complete Profile
                    </button>
                @endif
            </div>
        </div>
    </x-ui.responsive-container>
</div>
