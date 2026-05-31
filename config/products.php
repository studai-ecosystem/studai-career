<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Product / Feature Pages (data-driven)
|--------------------------------------------------------------------------
| Each entry renders a full SEO-optimised product page at /product/{slug}.
| Outcome- and capability-led copy — no statistics.
*/

return [

    'autonomous-agent' => [
        'name'     => 'Autonomous Agent',
        'icon'     => 'bolt',
        'accent'   => 'blue',
        'eyebrow'  => 'The engine of autopilot',
        'title'    => 'An agent that applies for you, around the clock',
        'lede'     => 'Set your goals once. Your AI agent searches, matches, tailors and submits applications on your behalf — every single day, while you live your life.',
        'meta_title'   => 'Autonomous Job Application Agent | StudAI Hire',
        'meta_desc'    => 'StudAI Hire’s Autonomous Agent finds matching roles and applies for you automatically with tailored applications — India’s first hands-free job search agent.',
        'keywords'     => 'autonomous job agent, auto apply jobs, AI job application bot, automated job search India',
        'highlights' => [
            ['title' => 'Always-on applying', 'desc' => 'Your agent works overnight and on weekends, so you never miss a fresh opening.'],
            ['title' => 'Tailored every time', 'desc' => 'Each application is rewritten to match the role — never a generic blast.'],
            ['title' => 'You stay in control', 'desc' => 'Approve roles, set guardrails, pause anytime. The agent acts within your rules.'],
        ],
        'features' => [
            ['icon' => 'search', 'title' => 'Goal-based targeting', 'desc' => 'Tell it the roles, locations, pay band and company type you want — it hunts continuously.'],
            ['icon' => 'doc',    'title' => 'Auto-tailored applications', 'desc' => 'Resume and cover letter rewritten per role, aligned to the job description and keywords.'],
            ['icon' => 'shield', 'title' => 'Guardrails & approvals', 'desc' => 'Choose auto-submit or review-first. Block companies, set daily limits, stay compliant.'],
            ['icon' => 'chart',  'title' => 'Live application tracker', 'desc' => 'See every role applied to, its status and next steps in one clean timeline.'],
        ],
        'steps' => [
            ['title' => 'Set your goals',   'desc' => 'Roles, locations, pay expectations and the kind of company you want to join.'],
            ['title' => 'Agent goes to work', 'desc' => 'It scans the market, matches roles to your profile and tailors each application.'],
            ['title' => 'You get results',  'desc' => 'Interviews land in your inbox. You decide which to take — the agent handles the rest.'],
        ],
        'related' => ['smart-job-search', 'resume-studio', 'interview-ai'],
    ],

    'smart-job-search' => [
        'name'     => 'Smart Job Search',
        'icon'     => 'search',
        'accent'   => 'violet',
        'eyebrow'  => 'Search the way you think',
        'title'    => 'Describe the job you want in plain English',
        'lede'     => 'No more boolean filters and endless tabs. Tell StudAI Hire what you’re looking for — it understands intent and surfaces roles that genuinely fit.',
        'meta_title'   => 'AI-Powered Smart Job Search | StudAI Hire',
        'meta_desc'    => 'Search jobs in natural language. StudAI Hire understands what you mean and surfaces roles that truly match your skills, goals and ambitions.',
        'keywords'     => 'natural language job search, AI job matching, smart job search India, semantic job search',
        'highlights' => [
            ['title' => 'Natural language', 'desc' => '“Remote backend roles at fintechs that value mentorship” just works.'],
            ['title' => 'True-fit matching', 'desc' => 'Ranked by how well a role fits your skills and goals, not keyword soup.'],
            ['title' => 'Fresh & deduped',  'desc' => 'New roles surfaced fast, duplicates and stale listings filtered out.'],
        ],
        'features' => [
            ['icon' => 'bolt',  'title' => 'Intent understanding', 'desc' => 'It reads the meaning behind your search, not just the words.'],
            ['icon' => 'chart', 'title' => 'Fit scoring', 'desc' => 'Every role gets a clear fit signal so you focus where it counts.'],
            ['icon' => 'doc',   'title' => 'One-click hand-off', 'desc' => 'Found a great role? Send it straight to your agent to apply.'],
            ['icon' => 'shield','title' => 'Quality first', 'desc' => 'Spam, duplicate and expired listings are filtered automatically.'],
        ],
        'steps' => [
            ['title' => 'Describe it',   'desc' => 'Type what you want the way you’d say it to a friend.'],
            ['title' => 'See true fits', 'desc' => 'Roles ranked by genuine alignment with your profile.'],
            ['title' => 'Hand to agent', 'desc' => 'Let your autonomous agent apply to the ones you like.'],
        ],
        'related' => ['autonomous-agent', 'resume-studio', 'negotiation-coach'],
    ],

    'resume-studio' => [
        'name'     => 'Resume Studio',
        'icon'     => 'doc',
        'accent'   => 'green',
        'eyebrow'  => 'Pass the bots, impress the humans',
        'title'    => 'ATS-ready resumes, tailored to every role',
        'lede'     => 'Build a resume that sails through applicant tracking systems and reads beautifully to hiring managers — automatically tailored for each application.',
        'meta_title'   => 'AI Resume Builder & ATS Optimiser | StudAI Hire',
        'meta_desc'    => 'Create ATS-friendly resumes tailored to every job. StudAI Hire’s Resume Studio aligns your experience to each role so you get past the bots and shortlisted.',
        'keywords'     => 'ATS resume builder, AI resume optimiser, tailored resume India, resume keyword optimisation',
        'highlights' => [
            ['title' => 'ATS-proof formatting', 'desc' => 'Clean, parseable structure that tracking systems read perfectly.'],
            ['title' => 'Per-role tailoring',   'desc' => 'Highlights the experience that matters most for each job.'],
            ['title' => 'Human-grade polish',   'desc' => 'Strong verbs, clear impact, no fluff — written to be read.'],
        ],
        'features' => [
            ['icon' => 'search','title' => 'Keyword alignment', 'desc' => 'Matches your resume to each job description’s key requirements.'],
            ['icon' => 'chart', 'title' => 'Impact rewriting', 'desc' => 'Turns flat duties into outcome-driven, quantified bullet points.'],
            ['icon' => 'shield','title' => 'ATS score check', 'desc' => 'See how a system will read your resume before you submit.'],
            ['icon' => 'bolt',  'title' => 'Instant variants', 'desc' => 'Generate a tailored version for any role in seconds.'],
        ],
        'steps' => [
            ['title' => 'Import once',   'desc' => 'Upload your existing resume or build from your profile.'],
            ['title' => 'Auto-tailor',   'desc' => 'Studio aligns it to each role you apply for.'],
            ['title' => 'Get shortlisted','desc' => 'Submit a resume built to pass bots and win humans.'],
        ],
        'related' => ['autonomous-agent', 'interview-ai', 'smart-job-search'],
    ],

    'interview-ai' => [
        'name'     => 'Interview AI',
        'icon'     => 'mic',
        'accent'   => 'violet',
        'eyebrow'  => 'Walk in ready',
        'title'    => 'Realistic mock interviews with honest feedback',
        'lede'     => 'Practice with an AI interviewer tuned to your role and company. Get specific, actionable feedback on what to keep and what to fix — before it counts.',
        'meta_title'   => 'AI Mock Interview Practice & Feedback | StudAI Hire',
        'meta_desc'    => 'Practice realistic interviews tailored to your role with StudAI Hire’s Interview AI. Get honest, actionable feedback so you walk into every interview ready.',
        'keywords'     => 'AI mock interview, interview practice India, interview feedback AI, behavioural interview prep',
        'highlights' => [
            ['title' => 'Role-specific', 'desc' => 'Questions tuned to the job, level and company you’re targeting.'],
            ['title' => 'Honest feedback', 'desc' => 'Clear notes on structure, clarity and substance — not vague praise.'],
            ['title' => 'Practice anytime', 'desc' => 'As many rounds as you need, on your schedule, judgment-free.'],
        ],
        'features' => [
            ['icon' => 'doc',   'title' => 'Tailored question sets', 'desc' => 'Behavioural, technical and role-specific prompts that mirror the real thing.'],
            ['icon' => 'chart', 'title' => 'Structured scoring', 'desc' => 'Feedback across communication, structure and depth so you know what to improve.'],
            ['icon' => 'bolt',  'title' => 'Model answers', 'desc' => 'See strong example responses framed for your experience.'],
            ['icon' => 'shield','title' => 'Confidence building', 'desc' => 'Repeat the tough questions until they feel easy.'],
        ],
        'steps' => [
            ['title' => 'Pick the role', 'desc' => 'Choose the job and level you’re interviewing for.'],
            ['title' => 'Run the mock',  'desc' => 'Answer realistic questions in a real interview flow.'],
            ['title' => 'Improve fast',  'desc' => 'Apply targeted feedback and practice until you’re sharp.'],
        ],
        'related' => ['negotiation-coach', 'resume-studio', 'autonomous-agent'],
    ],

    'negotiation-coach' => [
        'name'     => 'Negotiation Coach',
        'icon'     => 'chart',
        'accent'   => 'amber',
        'eyebrow'  => 'Leave nothing on the table',
        'title'    => 'Win the offer you actually deserve',
        'lede'     => 'Get a word-for-word negotiation script grounded in real market data. Know your worth, handle every counter, and close with confidence.',
        'meta_title'   => 'AI Salary Negotiation Coach | StudAI Hire',
        'meta_desc'    => 'Negotiate your offer with confidence. StudAI Hire’s Negotiation Coach gives you data-backed scripts and strategy to win the compensation you deserve.',
        'keywords'     => 'salary negotiation coach, offer negotiation AI, compensation negotiation India, negotiate job offer',
        'highlights' => [
            ['title' => 'Market-backed', 'desc' => 'Your ask is grounded in real compensation ranges for the role.'],
            ['title' => 'Word-for-word', 'desc' => 'Exact scripts for every moment — the ask, the counter, the close.'],
            ['title' => 'Handles pushback', 'desc' => 'Prepared responses for the objections you’ll actually hear.'],
        ],
        'features' => [
            ['icon' => 'chart', 'title' => 'Compensation ranges', 'desc' => 'See where an offer sits against the market for your role and level.'],
            ['icon' => 'doc',   'title' => 'Negotiation scripts', 'desc' => 'Ready-to-use language for email and live conversations.'],
            ['icon' => 'bolt',  'title' => 'Counter strategy', 'desc' => 'A plan for each scenario, from first offer to final yes.'],
            ['icon' => 'shield','title' => 'Total comp view', 'desc' => 'Look beyond base pay — equity, bonus, benefits and more.'],
        ],
        'steps' => [
            ['title' => 'Share the offer', 'desc' => 'Add the role, level and what’s on the table.'],
            ['title' => 'Get your script', 'desc' => 'Receive a data-backed ask and counter strategy.'],
            ['title' => 'Close strong',    'desc' => 'Negotiate with confidence and lock in your worth.'],
        ],
        'related' => ['interview-ai', 'autonomous-agent', 'smart-job-search'],
    ],

    'scout' => [
        'name'     => 'S.C.O.U.T. for Employers',
        'icon'     => 'shield',
        'accent'   => 'slate',
        'eyebrow'  => 'Hiring on autopilot',
        'title'    => 'An autonomous applicant tracking system',
        'lede'     => 'S.C.O.U.T. screens, ranks and shortlists candidates for you — surfacing the best people faster, with less bias and far less busywork.',
        'meta_title'   => 'S.C.O.U.T. — Autonomous Applicant Tracking System | StudAI Hire',
        'meta_desc'    => 'S.C.O.U.T. is StudAI Hire’s autonomous ATS for employers. Screen, rank and shortlist candidates automatically and hire the best people faster.',
        'keywords'     => 'autonomous ATS, AI applicant tracking, AI recruitment India, automated candidate screening, AI hiring platform',
        'highlights' => [
            ['title' => 'Smart screening', 'desc' => 'Every applicant assessed against the role, instantly and consistently.'],
            ['title' => 'Ranked shortlists', 'desc' => 'The strongest candidates surfaced first, with the reasoning shown.'],
            ['title' => 'Less bias, more signal', 'desc' => 'Structured evaluation focused on what actually predicts success.'],
        ],
        'features' => [
            ['icon' => 'search','title' => 'Auto-screening', 'desc' => 'Parses and evaluates every application against your requirements.'],
            ['icon' => 'chart', 'title' => 'Candidate ranking', 'desc' => 'Clear, explainable scoring so you know why someone made the list.'],
            ['icon' => 'doc',   'title' => 'Structured pipelines', 'desc' => 'Stages, notes and collaboration to move candidates through cleanly.'],
            ['icon' => 'bolt',  'title' => 'Faster time-to-hire', 'desc' => 'Spend time with the right people, not sifting through inboxes.'],
        ],
        'steps' => [
            ['title' => 'Post the role',   'desc' => 'Define the job and what great looks like.'],
            ['title' => 'S.C.O.U.T. screens', 'desc' => 'Applications are evaluated and ranked automatically.'],
            ['title' => 'You interview',   'desc' => 'Meet a curated shortlist and make confident decisions.'],
        ],
        'related' => ['autonomous-agent', 'smart-job-search', 'resume-studio'],
    ],
];
