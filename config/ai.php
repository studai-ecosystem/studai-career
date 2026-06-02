<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Service Provider
    |--------------------------------------------------------------------------
    |
    | Primary AI provider for StudAI Hire platform.
    | Supported: 'azure', 'anthropic'
    | Default: 'azure' (Azure OpenAI Service with GPT-5.4 — Orin™ Engine)
    |
    */

    'provider' => env('AI_PRIMARY_PROVIDER', 'azure'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'anthropic'),

    /*
    |--------------------------------------------------------------------------
    | Azure OpenAI Configuration (Primary — Orin™ Engine GPT-5.4)
    |--------------------------------------------------------------------------
    */

    'azure' => [
        // Secrets MUST come from the environment only. Never hardcode keys/endpoints here.
        'api_key'       => env('AZURE_OPENAI_API_KEY'),
        'endpoint'      => env('AZURE_OPENAI_ENDPOINT'),
        'deployment_id' => env('AZURE_OPENAI_DEPLOYMENT_ID', 'gpt-5.4'),
        'api_version'   => env('AZURE_OPENAI_API_VERSION', '2025-04-01-preview'),

        // Model configurations — GPT-5.4 (Orin™)
        'models' => [
            'chat'       => (empty(env('AZURE_OPENAI_MODEL'))      ? 'gpt-5.4'                : env('AZURE_OPENAI_MODEL')),
            'chat_mini'  => (empty(env('AZURE_OPENAI_MODEL_MINI')) ? 'gpt-5.4'                : env('AZURE_OPENAI_MODEL_MINI')),
            'embeddings' => (empty(env('AI_MODEL_EMBEDDINGS'))     ? 'text-embedding-3-large' : env('AI_MODEL_EMBEDDINGS')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Azure Anthropic Configuration (Fallback — Claude Sonnet 4.6)
    |--------------------------------------------------------------------------
    */

    'anthropic' => [
        'api_key' => env('AZURE_ANTHROPIC_API_KEY'),
        'endpoint' => env('AZURE_ANTHROPIC_ENDPOINT'),
        'model' => env('AZURE_ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 8192),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration (Legacy/Optional)
    |--------------------------------------------------------------------------
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Maximum time (in seconds) to wait for AI API responses.
    | Prevents hanging requests and improves system reliability.
    |
    */

    'timeout' => [
        'default' => env('AI_REQUEST_TIMEOUT', 30),
        'embeddings' => env('AI_EMBEDDINGS_TIMEOUT', 15),
        'streaming' => env('AI_STREAMING_TIMEOUT', 60),
        'long_running' => env('AI_LONG_RUNNING_TIMEOUT', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Parameters
    |--------------------------------------------------------------------------
    |
    | Default parameters for AI model requests.
    |
    */

    'parameters' => [
        'max_tokens' => env('AI_MAX_TOKENS', 16384),
        'temperature' => env('AI_TEMPERATURE', 0.7),
        'top_p' => 0.95,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
    ],

    // Aliases for AIService backward compatibility — GPT-5.4 (Orin™)
    'default_model' => env('AZURE_OPENAI_MODEL', 'gpt-5.4'),
    'max_tokens' => env('AI_MAX_TOKENS', 16384),
    'temperature' => env('AI_TEMPERATURE', 0.7),

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | AI response caching to reduce API costs and improve performance.
    | TTL in seconds.
    |
    */

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'driver' => env('AI_CACHE_DRIVER', 'redis'),
        'prefix' => 'ai_cache:',
        
        // Cache TTLs by context (in seconds)
        'ttl' => [
            'resume_analysis' => env('AI_CACHE_TTL', 3600), // 1 hour
            'job_matching' => 7200, // 2 hours
            'cover_letter' => 1800, // 30 minutes
            'interview_prep' => 3600, // 1 hour
            'career_advice' => 86400, // 24 hours
            'skills_extraction' => 86400, // 24 hours
            'embeddings' => 604800, // 7 days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Tracking
    |--------------------------------------------------------------------------
    |
    | Track AI API usage for cost management and analytics.
    |
    */

    'tracking' => [
        'enabled' => true,
        'log_table' => 'ai_usage_logs',
        
        // Cost per 1K tokens (update based on your pricing)
        'costs' => [
            'gpt-4o' => [
                'input' => 0.005,  // $5 per 1M input tokens
                'output' => 0.015, // $15 per 1M output tokens
            ],
            'text-embedding-3-large' => [
                'input' => 0.00013, // $0.13 per 1M tokens
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limits by user subscription tier.
    | Credits per month.
    |
    */

    'rate_limits' => [
        'free' => [
            'credits_per_month' => 10,
            'max_requests_per_hour' => 5,
        ],
        'professional' => [
            'credits_per_month' => 200,
            'max_requests_per_hour' => 50,
        ],
        'premium' => [
            'credits_per_month' => 1000,
            'max_requests_per_hour' => 200,
        ],
        'enterprise' => [
            'credits_per_month' => -1, // unlimited
            'max_requests_per_hour' => -1, // unlimited
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Configuration
    |--------------------------------------------------------------------------
    |
    | AI features and their specific configurations.
    |
    */

    'features' => [
        'resume_analyzer' => [
            'enabled' => true,
            'max_file_size' => 5120, // KB
            'supported_formats' => ['pdf', 'docx', 'txt'],
        ],
        'job_matching' => [
            'enabled' => true,
            'min_score' => 0.6, // 60% minimum match
            'max_results' => 50,
        ],
        'cover_letter_generator' => [
            'enabled' => true,
            'tones' => ['professional', 'enthusiastic', 'creative'],
            'lengths' => ['concise', 'standard', 'detailed'],
        ],
        'interview_prep' => [
            'enabled' => true,
            'questions_per_session' => 10,
            'difficulty_levels' => ['entry', 'mid', 'senior', 'expert'],
        ],
        'career_advisor' => [
            'enabled' => true,
            'max_career_paths' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Retry logic for failed API calls.
    |
    */

    'retry' => [
        'max_attempts' => 3,
        'delay' => 1000, // milliseconds
        'backoff' => 'exponential', // or 'linear'
    ],

    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker Configuration
    |--------------------------------------------------------------------------
    |
    | Circuit breaker settings to prevent cascading failures when AI services
    | are unavailable. Protects system stability during outages.
    |
    */

    'circuit_breaker' => [
        'enabled' => env('AI_CIRCUIT_BREAKER_ENABLED', true),
        'failure_threshold' => env('AI_CIRCUIT_BREAKER_FAILURES', 5),   // Open after 5 failures
        'success_threshold' => env('AI_CIRCUIT_BREAKER_SUCCESSES', 2), // Close after 2 successes
        'recovery_timeout' => env('AI_CIRCUIT_BREAKER_TIMEOUT', 30),   // Try recovery after 30s
        'failure_window' => 60, // Count failures within 60 second window
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Behavior
    |--------------------------------------------------------------------------
    |
    | What to do when AI service fails.
    | Primary: Azure OpenAI (GPT-5.1)
    | Fallback: Azure Anthropic (Claude Sonnet 4.5)
    |
    */

    'fallback' => [
        'enabled' => true,
        'use_cached' => true,
        'use_anthropic_if_azure_fails' => true,
        'use_openai_if_all_fails' => false, // Legacy fallback disabled
        'return_basic_response' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Registry
    |--------------------------------------------------------------------------
    |
    | Centralised prompt templates for all AI features. Keeping prompts here
    | (rather than hard-coded in service classes) makes tuning, versioning,
    | and A/B testing significantly easier.
    |
    | Use {placeholders} for dynamic substitution. The calling service is
    | responsible for str_replace / sprintf substitution before sending.
    |
    */

    'prompts' => [

        'resume_analyzer' => [
            'system' => 'You are an expert career coach and ATS (Applicant Tracking System) specialist. '
                . 'Analyse the provided resume and return a structured JSON response.',
            'user'   => "Analyse this resume and return a JSON object with keys: "
                . "overall_score (0-100), ats_score (0-100), strengths (array of strings), "
                . "weaknesses (array of strings), missing_keywords (array), "
                . "suggested_improvements (array of strings).\n\nResume:\n{resume_text}",
        ],

        'cover_letter' => [
            'system' => 'You are an expert cover letter writer. Write compelling, personalised cover letters '
                . 'that pass ATS filters and resonate with human readers.',
            'user'   => "Write a {tone} cover letter for the following job.\n\n"
                . "Job Title: {job_title}\nCompany: {company_name}\n"
                . "Job Description:\n{job_description}\n\n"
                . "Candidate Resume Summary:\n{resume_summary}\n\n"
                . "Length: {length}. Return ONLY the cover letter text.",
        ],

        'job_match_score' => [
            'system' => 'You are an expert job-candidate matching specialist. '
                . 'Evaluate how well a candidate matches a job and return JSON.',
            'user'   => "Score this match (0-100) and return JSON with keys: "
                . "score, matched_skills (array), missing_skills (array), "
                . "recommendation (string, max 2 sentences).\n\n"
                . "Job:\n{job_description}\n\nCandidate Profile:\n{candidate_profile}",
        ],

        'interview_question' => [
            'system' => 'You are a senior technical interviewer. Generate realistic interview questions '
                . 'appropriate for the role and experience level specified.',
            'user'   => "Generate {count} {difficulty}-level interview questions for a {role} position "
                . "at a {company_type} company. Focus on: {focus_areas}.\n\n"
                . "Return JSON: {\"questions\": [{\"question\": \"...\", \"type\": \"...\", "
                . "\"expected_answer_outline\": \"...\"}]}",
        ],

        'salary_negotiation' => [
            'system' => 'You are a compensation negotiation expert. Provide clear, actionable negotiation '
                . 'strategies based on market data and the candidate situation.',
            'user'   => "Provide a salary negotiation strategy for:\n"
                . "Role: {role}\nOffered Salary: {offered_salary}\nMarket Range: {market_range}\n"
                . "Candidate Experience: {experience_years} years\n"
                . "Key Strengths: {strengths}\n\n"
                . "Return JSON with keys: counter_offer (number), scripts (array of strings), "
                . "walk_away_point (number), key_talking_points (array).",
        ],

        'skill_gap_analysis' => [
            'system' => 'You are a career development specialist. Identify skill gaps and recommend '
                . 'targeted learning resources.',
            'user'   => "Analyse the skill gap between this candidate and target role.\n\n"
                . "Current Skills: {current_skills}\nTarget Role: {target_role}\n"
                . "Target Role Requirements: {role_requirements}\n\n"
                . "Return JSON: {\"gaps\": [{\"skill\": \"...\", \"priority\": \"high|medium|low\", "
                . "\"learning_path\": [{\"resource\": \"...\", \"type\": \"course|book|project\", "
                . "\"estimated_hours\": 0}]}]}",
        ],

        'career_advice' => [
            'system' => 'You are a strategic career advisor with expertise across all industries. '
                . 'Give honest, actionable, personalised career guidance.',
            'user'   => "Provide career advice for:\nCurrent Role: {current_role}\n"
                . "Years of Experience: {experience_years}\nTarget Goal: {career_goal}\n"
                . "Industry: {industry}\n\nQuestion: {question}",
        ],

        'autonomous_agent_decision' => [
            'system' => 'You are an AI career agent. Evaluate job opportunities and decide whether to '
                . 'apply on behalf of the user, based on their preferences and profile.',
            'user'   => "Should I apply to this job on behalf of the user?\n\n"
                . "User Preferences:\n{preferences}\n\n"
                . "User Profile Summary:\n{profile_summary}\n\n"
                . "Job Posting:\n{job_posting}\n\n"
                . "Return JSON: {\"decision\": \"apply|skip|escalate\", "
                . "\"confidence\": 0.0-1.0, \"reason\": \"...\"}",
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Candidate Ranking (ScoreAndRankCandidates)
    |--------------------------------------------------------------------------
    |
    | Composite ranking inputs and weights. `required_inputs` lists the
    | Application columns that MUST be non-null before ranking runs. If any
    | are missing, ranking halts and an ops alert is raised instead of
    | silently defaulting the value to 0 (which would skew fairness).
    |
    */

    'ranking' => [
        'required_inputs' => [
            'evaluation_score',
            'skill_match_score',
            'resume_quality_score',
        ],
        'weights' => [
            'evaluation_score'      => 0.45,
            'skill_match_score'     => 0.25,
            'resume_quality_score'  => 0.15,
            'behavioural_fit_score' => 0.15,
        ],
        // Anti-cheat: flag-for-human-review only. No automatic score penalty.
        'apply_cheat_penalty' => env('AI_RANKING_CHEAT_PENALTY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | S.C.O.U.T. Shortlisting Thresholds
    |--------------------------------------------------------------------------
    |
    | Pass thresholds for each gated round of AutomatedShortlistingService.
    | Treated as calibratable placeholders — tune against outcome data
    | (target: 200 hires, logistic regression) rather than hardcoding.
    |
    */

    'scout' => [
        'thresholds' => [
            'basic_qualification' => env('SCOUT_THRESHOLD_R1', 60),
            'skills_competency'   => env('SCOUT_THRESHOLD_R2', 50),
            'cultural_fit'        => env('SCOUT_THRESHOLD_R3', 60),
            'potential_growth'    => env('SCOUT_THRESHOLD_R4', 45),
        ],
        'round_weights' => [
            'basic_qualification' => 0.15,
            'skills_competency'   => 0.35,
            'cultural_fit'        => 0.30,
            'potential_growth'    => 0.20,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Evaluation Retake Policy (F13)
    |--------------------------------------------------------------------------
    |
    | Governs what happens when a candidate starts a second (or later) Orin™
    | evaluation session for the same application.
    |
    |   max_attempts : total number of evaluation sessions permitted per
    |                  application (1 = no retakes by default).
    |   policy       : 'new_bank'  -> generate a fresh question bank on retake
    |                                 (preserves assessment integrity; costs
    |                                 more, bounded by the per-job ceiling).
    |                  'same_bank' -> reuse the previous attempt's questions.
    |
    */

    'evaluation' => [
        'retake' => [
            'max_attempts' => env('AI_EVAL_MAX_ATTEMPTS', 1),
            'policy'       => env('AI_EVAL_RETAKE_POLICY', 'new_bank'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Hire Unit Economics / Cost Ceiling
    |--------------------------------------------------------------------------
    |
    | Soft ceiling for AI spend per job during Stage 4 evaluation generation.
    | When exceeded, the pipeline logs/alerts and prefers cache reuse.
    |
    */

    'cost' => [
        'per_job_ceiling_usd' => env('AI_PER_JOB_COST_CEILING', 50.0),
        'alert_at_pct'        => env('AI_COST_ALERT_PCT', 80),
        // I3: estimated spend for generating one candidate question bank
        // (Stage 4). Used by AICostMeter to enforce the per-job soft ceiling.
        'per_question_bank_usd' => env('AI_QUESTION_BANK_COST', 0.45),
        // E11: per-session soft budget (USD) for mock interview AI usage and the
        // estimated cost of a single answer evaluation. Enforced by AICostMeter.
        'per_mock_session_ceiling_usd' => env('AI_MOCK_SESSION_CEILING', 1.50),
        'per_answer_eval_usd' => env('AI_ANSWER_EVAL_COST', 0.05),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ops Alerting
    |--------------------------------------------------------------------------
    |
    | Channel + webhook for operational alerts (AI fallback triggered,
    | ranking blocked, cost ceiling breached). Logs always; Slack optional.
    |
    */

    'ops_alerts' => [
        'enabled'      => env('AI_OPS_ALERTS_ENABLED', true),
        'log_channel'  => env('AI_OPS_ALERTS_CHANNEL', 'stack'),
        'slack_webhook' => env('AI_OPS_ALERTS_SLACK_WEBHOOK'),
    ],

];

