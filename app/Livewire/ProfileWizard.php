<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Profile;
use App\Services\AI\ResumeAnalyzerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use OpenAI\Laravel\Facades\OpenAI;

class ProfileWizard extends Component
{
    use WithFileUploads;

    // Wizard state
    public $currentStep = 1;
    public $totalSteps = 6;
    
    // Step 1: Resume Upload
    public $resumeFile;
    public $uploadProgress = 0;
    public $analyzing = false;
    public $analysisComplete = false;
    public $analysisData = null;
    public $analysisError = '';
    
    // Step 2: Basic Info
    public $headline = '';
    public $summary = '';
    public array $headlineSuggestions = [];
    public $current_location = '';
    
    // Step 3: Experience
    public $experience = [];
    
    // Step 4: Education
    public $education = [];
    
    // Step 5: Skills
    public $skills = [];
    public $skillSearch = '';
    
    // Step 6: Links & Preferences
    public $linkedin_url = '';
    public $portfolio_url = '';
    public $github_url = '';
    public $expected_salary_min = null;
    public $expected_salary_max = null;
    public $career_goals = '';
    
    // Additional
    public $certifications = [];
    public $projects = [];

    protected $rules = [
        // Step 2
        'headline' => 'required|string|max:255',
        'summary' => 'required|string|max:1000',
        'current_location' => 'nullable|string|max:255',
        
        // Step 3
        'experience.*.title' => 'required|string',
        'experience.*.company' => 'required|string',
        'experience.*.start_date' => 'required|date',
        'experience.*.end_date' => 'nullable|date',
        'experience.*.description' => 'nullable|string',
        
        // Step 4
        'education.*.degree' => 'required|string',
        'education.*.institution' => 'required|string',
        'education.*.field' => 'required|string',
        'education.*.graduation_year' => 'required|integer|min:1950',
        
        // Step 5
        'skills.*.name' => 'required|string',
        'skills.*.proficiency' => 'nullable|in:beginner,intermediate,advanced,expert',
        'skills.*.years' => 'nullable|integer|min:0|max:50',
        
        // Step 6
        'linkedin_url' => 'nullable|url',
        'portfolio_url' => 'nullable|url',
        'github_url' => 'nullable|url',
        'expected_salary_min' => 'nullable|numeric|min:0',
        'expected_salary_max' => 'nullable|numeric|min:0',
        'career_goals' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Restore step from session so a page refresh lands back on the right step
        $this->currentStep = session('profile_wizard_step_' . $user->id, 1);
        
        if ($profile) {
            // Load existing profile data
            $this->headline = $profile->headline ?? '';
            $this->summary = $profile->summary ?? '';
            $this->current_location = $profile->current_location ?? '';
            $this->experience = $profile->experience ?? [];
            $this->education = $profile->education ?? [];
            $this->skills = $profile->skills ?? [];
            $this->certifications = $profile->certifications ?? [];
            $this->projects = $profile->projects ?? [];
            $this->expected_salary_min = $profile->expected_salary_min;
            $this->expected_salary_max = $profile->expected_salary_max;

            // URLs and career goals are stored in the social_links JSON column
            $social = $profile->social_links ?? [];
            $this->linkedin_url  = $social['linkedin']  ?? '';
            $this->portfolio_url = $social['portfolio'] ?? '';
            $this->github_url    = $social['github']    ?? '';
            $this->career_goals  = $social['career_goals'] ?? '';

            // If profile has data, skip straight to step 2 minimum (no need to re-upload)
            if ($this->currentStep === 1 && $profile->headline) {
                $this->currentStep = session('profile_wizard_step_' . $user->id, 2);
            }
        }
    }

    /**
     * Persist the current step to the session
     */
    protected function saveStepToSession(): void
    {
        session(['profile_wizard_step_' . auth()->id() => $this->currentStep]);
    }

    /**
     * Upload and analyze resume
     */
    public function uploadResume()
    {
        $this->validate([
            'resumeFile' => 'required|file|mimes:pdf,doc,docx,txt|max:5120', // 5MB
        ]);

        $this->analysisError = '';

        try {
            $this->analyzing = true;
            $this->uploadProgress = 0;

            $user = auth()->user();

            // Store file
            $path = $this->resumeFile->store('resumes/' . $user->id, 'private');
            $fullPath = Storage::disk('private')->path($path);

            $this->uploadProgress = 50;

            // Analyze with AI
            $resumeAnalyzer = app(ResumeAnalyzerService::class);
            $this->analysisData = $resumeAnalyzer->analyzeResume($fullPath);

            $this->uploadProgress = 100;
            $this->analyzing = false;
            $this->analysisComplete = true;

            // Auto-fill from analysis
            $this->autoFillFromAnalysis();

            // Move to next step
            $this->currentStep = 2;
            $this->saveStepToSession();

        } catch (\Throwable $e) {
            $this->analyzing = false;
            Log::error('Resume analysis failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            $this->analysisError = 'Could not analyze resume automatically. You can skip and fill in your details manually.';
        }
    }

    /**
     * Auto-fill profile from resume analysis
     */
    protected function autoFillFromAnalysis()
    {
        if (!$this->analysisData) {
            return;
        }

        $data = $this->analysisData;

        // Basic info
        if (!empty($data['personal_info']['professional_title'])) {
            $this->headline = $data['personal_info']['professional_title'];
        }

        if (!empty($data['summary'])) {
            $this->summary = $data['summary'];
        }

        if (!empty($data['personal_info']['location'])) {
            $this->current_location = $data['personal_info']['location'];
        }

        // Experience — normalize start/end dates to YYYY-MM-DD for <input type="date">
        if (!empty($data['experience']) && is_array($data['experience'])) {
            $this->experience = array_map(function (array $exp): array {
                return [
                    'title'        => $exp['title'] ?? '',
                    'company'      => $exp['company'] ?? '',
                    'start_date'   => $this->normalizeDate($exp['start_date'] ?? ''),
                    'end_date'     => strtolower($exp['end_date'] ?? '') === 'present' ? '' : $this->normalizeDate($exp['end_date'] ?? ''),
                    'current'      => strtolower($exp['end_date'] ?? '') === 'present',
                    'description'  => $exp['description'] ?? '',
                    'achievements' => is_array($exp['achievements'] ?? null) ? $exp['achievements'] : [],
                ];
            }, $data['experience']);
        }

        // Education — graduation_year must be int
        if (!empty($data['education']) && is_array($data['education'])) {
            $this->education = array_map(function (array $edu): array {
                return [
                    'degree'          => $edu['degree'] ?? '',
                    'institution'     => $edu['institution'] ?? '',
                    'field'           => $edu['field'] ?? '',
                    'graduation_year' => (int) ($edu['graduation_year'] ?? date('Y')),
                    'gpa'             => $edu['gpa'] ?? null,
                ];
            }, $data['education']);
        }

        // Skills — AI returns {technical:[], soft:[], languages:[]} OR a flat array
        if (!empty($data['skills'])) {
            $rawSkills = $data['skills'];
            $normalized = [];

            if (isset($rawSkills['technical']) || isset($rawSkills['soft'])) {
                // Nested format — flatten
                foreach ($rawSkills['technical'] ?? [] as $s) {
                    if (is_array($s)) {
                        $normalized[] = ['name' => $s['name'] ?? '', 'proficiency' => strtolower($s['proficiency'] ?? 'intermediate'), 'years' => (int) ($s['years'] ?? 1)];
                    } elseif (is_string($s)) {
                        $normalized[] = ['name' => $s, 'proficiency' => 'intermediate', 'years' => 1];
                    }
                }
                foreach ($rawSkills['soft'] ?? [] as $s) {
                    $name = is_array($s) ? ($s['name'] ?? '') : $s;
                    if ($name) {
                        $normalized[] = ['name' => $name, 'proficiency' => 'intermediate', 'years' => 1];
                    }
                }
                foreach ($rawSkills['tools'] ?? [] as $s) {
                    $name = is_array($s) ? ($s['name'] ?? '') : $s;
                    if ($name) {
                        $normalized[] = ['name' => $name, 'proficiency' => 'intermediate', 'years' => 1];
                    }
                }
            } elseif (is_array($rawSkills)) {
                // Already flat array of strings or objects
                foreach ($rawSkills as $s) {
                    if (is_array($s) && isset($s['name'])) {
                        $normalized[] = ['name' => $s['name'], 'proficiency' => strtolower($s['proficiency'] ?? 'intermediate'), 'years' => (int) ($s['years'] ?? 1)];
                    } elseif (is_string($s)) {
                        $normalized[] = ['name' => $s, 'proficiency' => 'intermediate', 'years' => 1];
                    }
                }
            }

            if (!empty($normalized)) {
                $this->skills = array_values(array_filter($normalized, fn($s) => !empty($s['name'])));
            }
        }

        // Certifications
        if (!empty($data['certifications']) && is_array($data['certifications'])) {
            $this->certifications = array_map(fn($c) => [
                'name'        => $c['name'] ?? '',
                'issuer'      => $c['issuer'] ?? '',
                'date'        => $this->normalizeDate($c['date'] ?? ''),
                'expiry_date' => null,
            ], $data['certifications']);
        }

        // Projects
        if (!empty($data['projects']) && is_array($data['projects'])) {
            $this->projects = $data['projects'];
        }

        // Links
        if (!empty($data['personal_info']['linkedin'])) {
            $this->linkedin_url = $data['personal_info']['linkedin'];
        }

        if (!empty($data['personal_info']['portfolio'])) {
            $this->portfolio_url = $data['personal_info']['portfolio'];
        }

        if (!empty($data['personal_info']['github'])) {
            $this->github_url = $data['personal_info']['github'];
        }
    }

    /**
     * Convert YYYY-MM or YYYY to YYYY-MM-DD for HTML date inputs
     */
    protected function normalizeDate(string $date): string
    {
        if (empty($date)) {
            return '';
        }
        // Already full date
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        // YYYY-MM → YYYY-MM-01
        if (preg_match('/^\d{4}-\d{2}$/', $date)) {
            return $date . '-01';
        }
        // YYYY → YYYY-01-01
        if (preg_match('/^\d{4}$/', $date)) {
            return $date . '-01-01';
        }
        return '';
    }

    /**
     * Skip resume upload and go straight to manual entry.
     * Pre-seeds one blank entry in each section so forms aren't empty.
     */
    public function skipResume()
    {
        if (empty($this->experience)) {
            $this->addExperience();
        }
        if (empty($this->education)) {
            $this->addEducation();
        }
        $this->currentStep = 2;
        $this->saveStepToSession();
    }

    /**
     * Add new experience entry
     */
    public function addExperience()
    {
        $this->experience[] = [
            'title' => '',
            'company' => '',
            'start_date' => '',
            'end_date' => '',
            'current' => false,
            'description' => '',
            'achievements' => [],
        ];
    }

    /**
     * Remove experience entry
     */
    public function removeExperience($index)
    {
        unset($this->experience[$index]);
        $this->experience = array_values($this->experience);
    }

    /**
     * Add new education entry
     */
    public function addEducation()
    {
        $this->education[] = [
            'degree' => '',
            'institution' => '',
            'field' => '',
            'graduation_year' => date('Y'),
            'gpa' => null,
        ];
    }

    /**
     * Remove education entry
     */
    public function removeEducation($index)
    {
        unset($this->education[$index]);
        $this->education = array_values($this->education);
    }

    /**
     * Add new skill
     */
    public function addSkill()
    {
        if (!empty($this->skillSearch)) {
            $this->skills[] = [
                'name' => $this->skillSearch,
                'proficiency' => 'intermediate',
                'years' => 1,
            ];
            $this->skillSearch = '';
        }
    }

    /**
     * Remove skill
     */
    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    /**
     * Add certification
     */
    public function addCertification()
    {
        $this->certifications[] = [
            'name' => '',
            'issuer' => '',
            'date' => date('Y-m-d'),
            'expiry_date' => null,
        ];
    }

    /**
     * Remove certification
     */
    public function removeCertification($index)
    {
        unset($this->certifications[$index]);
        $this->certifications = array_values($this->certifications);
    }

    /**
     * Add project
     */
    public function addProject()
    {
        $this->projects[] = [
            'name' => '',
            'description' => '',
            'technologies' => [],
            'url' => '',
            'start_date' => '',
            'end_date' => '',
        ];
    }

    /**
     * Remove project
     */
    public function removeProject($index)
    {
        unset($this->projects[$index]);
        $this->projects = array_values($this->projects);
    }

    /**
     * Generate 5 professional headline suggestions using AI from resume data
     */
    public function generateHeadline(): void
    {
        $this->headlineSuggestions = [];
        $this->resetErrorBag('headline');

        try {
            $context = [];

            if ($this->analysisData) {
                $data = is_array($this->analysisData) ? $this->analysisData : (array) $this->analysisData;

                if (!empty($data['skills'])) {
                    $skillsRaw = (array) $data['skills'];
                    $flatSkills = [];
                    foreach ($skillsRaw as $group) {
                        if (is_array($group)) {
                            foreach ($group as $item) {
                                if (is_array($item)) {
                                    $flatSkills[] = $item['language'] ?? $item['name'] ?? '';
                                } elseif (is_string($item)) {
                                    $flatSkills[] = $item;
                                }
                            }
                        } elseif (is_string($group)) {
                            $flatSkills[] = $group;
                        }
                    }
                    $flatSkills = array_filter($flatSkills);
                    if ($flatSkills) {
                        $context[] = 'Skills: ' . implode(', ', array_slice($flatSkills, 0, 8));
                    }
                }

                if (!empty($data['experience'])) {
                    $exp   = (array) $data['experience'];
                    $first = (array) ($exp[0] ?? []);
                    $title   = is_string($first['title']   ?? null) ? $first['title']   : '';
                    $company = is_string($first['company'] ?? null) ? $first['company'] : '';
                    if ($title)   $context[] = 'Latest role: ' . $title;
                    if ($company) $context[] = 'at ' . $company;
                }
            }

            if ($this->summary) $context[] = 'Summary: ' . $this->summary;
            if (empty($context)) $context[] = 'A career professional looking for new opportunities';

            $background = implode('. ', $context);
            $prompt = 'Generate exactly 5 different, creative LinkedIn-style professional headlines (each max 120 characters) for someone with this background: '
                . $background
                . '. Each headline should have a different angle or tone (e.g. achievement-focused, skill-focused, role-focused, impact-focused, passion-focused).'
                . ' Return ONLY a JSON array of exactly 5 strings, like: ["Headline 1","Headline 2","Headline 3","Headline 4","Headline 5"]. No other text, no numbering, no explanation.';

            $endpoint   = config('ai.azure.endpoint');
            $apiKey     = config('ai.azure.api_key');
            $deployment = config('ai.azure.deployment_id', config('ai.default_model', 'gpt-5.4'));
            $apiVersion = config('ai.azure.api_version', '2024-12-01-preview');

            if (empty($endpoint) || empty($apiKey)) {
                throw new \Exception('Azure OpenAI credentials not configured.');
            }

            $url = rtrim($endpoint, '/') . "/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

            $response = Http::timeout(45)
                ->withHeaders([
                    'api-key'      => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional resume writer. Always respond with only a valid JSON array of strings, nothing else.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_completion_tokens' => 300,
                    'temperature'           => 0.85,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Azure OpenAI request failed: ' . $response->body());
            }

            $raw = trim($response->json('choices.0.message.content') ?? '');

            // Strip markdown code fences if present
            $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
            $raw = preg_replace('/\s*```$/', '', $raw);
            $raw = trim($raw);

            $suggestions = json_decode($raw, true);

            // Fallback: split by newlines if JSON fails
            if (!is_array($suggestions)) {
                $suggestions = array_filter(array_map('trim', explode("\n", $raw)));
            }

            $suggestions = array_values(array_slice(
                array_filter(array_map('trim', (array) $suggestions), fn($s) => is_string($s) && $s !== ''),
                0, 5
            ));

            if (!empty($suggestions)) {
                $this->headlineSuggestions = $suggestions;
                $this->headline = $suggestions[0];
            }
        } catch (\Throwable $e) {
            Log::error('generateHeadline failed', ['error' => $e->getMessage()]);
            $this->addError('headline', 'AI generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Select a generated headline suggestion
     */
    public function selectHeadline(int $index): void
    {
        $this->headline = $this->headlineSuggestions[$index] ?? $this->headline;
    }

    /**
     * Navigate to next step
     */
    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->saveStepToSession();
        }
    }

    /**
     * Navigate to previous step
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveStepToSession();
        }
    }

    /**
     * Go to specific step
     */
    public function goToStep($step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->saveStepToSession();
        }
    }

    /**
     * Handle step change from step wizard component
     */
    #[On('stepChanged')]
    public function handleStepChanged(int $step): void
    {
        $this->goToStep($step);
    }

    /**
     * Validate current step
     */
    protected function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 2:
                $this->validate([
                    'headline' => 'required|string|max:255',
                    'summary' => 'required|string|max:1000',
                ]);
                break;
            case 3:
                if (!empty($this->experience)) {
                    $this->validate([
                        'experience.*.title' => 'required|string',
                        'experience.*.company' => 'required|string',
                        'experience.*.start_date' => 'required|date',
                    ]);
                }
                break;
            case 4:
                if (!empty($this->education)) {
                    $this->validate([
                        'education.*.degree' => 'required|string',
                        'education.*.institution' => 'required|string',
                        'education.*.field' => 'required|string',
                        'education.*.graduation_year' => 'required|integer|min:1950',
                    ]);
                }
                break;
            case 5:
                $this->validate([
                    'skills' => 'required|array|min:1',
                ]);
                break;
        }
    }

    /**
     * Save profile
     */
    public function saveProfile()
    {
        try {
            $user = auth()->user();
            $profile = $user->profile ?? new Profile();

            $profile->user_id        = $user->id;
            $profile->headline       = $this->headline;
            $profile->summary        = $this->summary;
            $profile->current_location = $this->current_location;
            $profile->experience     = $this->experience ?: [];
            $profile->education      = $this->education ?: [];
            $profile->skills         = $this->skills ?: [];
            $profile->certifications = $this->certifications ?: [];
            $profile->projects       = $this->projects ?: [];
            $profile->expected_salary_min = $this->expected_salary_min ?: null;
            $profile->expected_salary_max = $this->expected_salary_max ?: null;

            // Store URLs and career goals in the social_links JSON column
            $profile->social_links = [
                'linkedin'     => $this->linkedin_url  ?: '',
                'portfolio'    => $this->portfolio_url ?: '',
                'github'       => $this->github_url    ?: '',
                'career_goals' => $this->career_goals  ?: '',
            ];

            // Update completion score
            $profile->profile_completeness = $this->completionPercentage;

            $profile->save();

            // Clear wizard step session so next visit starts fresh
            session()->forget('profile_wizard_step_' . $user->id);

            session()->flash('message', 'Profile saved successfully!');

            return redirect()->route('profile.career.index');

        } catch (\Exception $e) {
            Log::error('Profile save failed', [
                'error'   => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            session()->flash('error', 'Failed to save profile. Please try again.');
        }
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageProperty()
    {
        $requiredFields = [
            'headline',
            'summary',
            'experience',
            'education',
            'skills',
        ];
        
        $completed = 0;
        foreach ($requiredFields as $field) {
            $value = $this->$field;
            if (!empty($value)) {
                $completed++;
            }
        }
        
        return round(($completed / count($requiredFields)) * 100);
    }

    public function render()
    {
        return view('livewire.profile-wizard');
    }
}
