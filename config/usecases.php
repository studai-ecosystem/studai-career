<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Use Case / Persona Pages (data-driven)
|--------------------------------------------------------------------------
| Renders /use-cases (index) and /use-cases/{slug} (persona pages).
*/

return [

    'students' => [
        'name'    => 'Students',
        'accent'  => 'blue',
        'eyebrow' => 'For students',
        'title'   => 'Land your first real role while you study',
        'lede'    => 'Internships and entry roles move fast. Let your AI agent watch the market and apply the moment something fits — so you can focus on your degree.',
        'meta_title' => 'AI Job Search for Students | StudAI Hire',
        'meta_desc'  => 'StudAI Hire helps students land internships and first jobs on autopilot. Your AI agent finds and applies to roles while you focus on studying.',
        'keywords'   => 'student job search, internship finder India, first job AI, campus placement help',
        'pains'   => ['No time between classes and exams', 'Don’t know how to write a strong resume', 'Missing deadlines on fresh openings'],
        'gains'   => [
            ['title' => 'Apply without the time sink', 'desc' => 'Your agent applies to internships and entry roles while you study.'],
            ['title' => 'A resume that gets read', 'desc' => 'Resume Studio turns coursework and projects into real impact.'],
            ['title' => 'Interview-ready, fast', 'desc' => 'Practice with Interview AI before your first real round.'],
        ],
        'products' => ['autonomous-agent', 'resume-studio', 'interview-ai'],
    ],

    'freshers' => [
        'name'    => 'Freshers',
        'accent'  => 'violet',
        'eyebrow' => 'For freshers',
        'title'   => 'Break into the industry without the burnout',
        'lede'    => 'The first job is the hardest. StudAI Hire handles the volume — applying widely and tailoring each one — so you get more interviews with less grind.',
        'meta_title' => 'AI Job Search for Freshers & Graduates | StudAI Hire',
        'meta_desc'  => 'Freshers get more interviews with StudAI Hire. Your AI agent applies to entry-level roles with tailored applications so you break in faster.',
        'keywords'   => 'fresher jobs India, graduate job search AI, entry level jobs, first job application help',
        'pains'   => ['Hundreds of applications, few replies', 'Generic resume that gets filtered out', 'No idea what interviews are really like'],
        'gains'   => [
            ['title' => 'Volume, done right', 'desc' => 'Apply widely without copy-pasting — every application is tailored.'],
            ['title' => 'Get past the filters', 'desc' => 'ATS-ready resumes built to be shortlisted, not screened out.'],
            ['title' => 'Walk in confident', 'desc' => 'Mock interviews tuned to the roles you’re targeting.'],
        ],
        'products' => ['autonomous-agent', 'resume-studio', 'interview-ai'],
    ],

    'working-professionals' => [
        'name'    => 'Working professionals',
        'accent'  => 'green',
        'eyebrow' => 'For working professionals',
        'title'   => 'Level up without putting your life on hold',
        'lede'    => 'You already have a full-time job. Let your agent run a discreet, always-on search in the background and bring you only the roles worth your time.',
        'meta_title' => 'AI Job Search for Working Professionals | StudAI Hire',
        'meta_desc'  => 'Run a discreet, always-on job search while you work. StudAI Hire’s AI agent surfaces and applies to better roles so you level up without the grind.',
        'keywords'   => 'passive job search, professional job change India, mid career job search AI, discreet job hunt',
        'pains'   => ['No time to search while working', 'Hard to know your real market value', 'Leaving money on the table in offers'],
        'gains'   => [
            ['title' => 'Search runs itself', 'desc' => 'Always-on, in the background, surfacing only strong fits.'],
            ['title' => 'Know your worth', 'desc' => 'Market-backed compensation insight before you ever negotiate.'],
            ['title' => 'Negotiate like a pro', 'desc' => 'Data-driven scripts to win the raise the move deserves.'],
        ],
        'products' => ['autonomous-agent', 'negotiation-coach', 'smart-job-search'],
    ],

    'career-switchers' => [
        'name'    => 'Career switchers',
        'accent'  => 'amber',
        'eyebrow' => 'For career switchers',
        'title'   => 'Pivot into a new field with confidence',
        'lede'    => 'Changing tracks is daunting. StudAI Hire reframes your experience for the role you want and targets the companies open to your story.',
        'meta_title' => 'AI Job Search for Career Switchers | StudAI Hire',
        'meta_desc'  => 'Switching careers? StudAI Hire reframes your experience and targets the right roles so you pivot into a new field with confidence.',
        'keywords'   => 'career change India, career switch help, transferable skills resume, pivot careers AI',
        'pains'   => ['Experience doesn’t obviously map to the new role', 'Resume reads like the old career', 'Unsure which companies will give you a shot'],
        'gains'   => [
            ['title' => 'Reframe your story', 'desc' => 'Resume Studio surfaces transferable skills for the new field.'],
            ['title' => 'Target the open doors', 'desc' => 'Smart search finds teams that value your background.'],
            ['title' => 'Tell it well', 'desc' => 'Interview AI helps you narrate the pivot with conviction.'],
        ],
        'products' => ['resume-studio', 'smart-job-search', 'interview-ai'],
    ],

    'returning-to-work' => [
        'name'    => 'Returning to work',
        'accent'  => 'blue',
        'eyebrow' => 'For returners',
        'title'   => 'Restart your career momentum',
        'lede'    => 'After a break, the search can feel overwhelming. Your AI agent does the heavy lifting and rebuilds your momentum one application at a time.',
        'meta_title' => 'AI Job Search for Returners | StudAI Hire',
        'meta_desc'  => 'Returning to work after a break? StudAI Hire’s AI agent handles the search and helps you frame your story so you restart with confidence.',
        'keywords'   => 'return to work India, career break job search, restart career AI, returnship help',
        'pains'   => ['Explaining a career gap', 'Out of touch with the current market', 'Rebuilding confidence to apply'],
        'gains'   => [
            ['title' => 'The search, handled', 'desc' => 'Your agent applies for you so getting started is effortless.'],
            ['title' => 'Own your gap', 'desc' => 'Frame your break as a strength, not a question mark.'],
            ['title' => 'Rebuild confidence', 'desc' => 'Low-pressure mock interviews to get back in the flow.'],
        ],
        'products' => ['autonomous-agent', 'resume-studio', 'interview-ai'],
    ],

    'employers' => [
        'name'    => 'Employers & recruiters',
        'accent'  => 'slate',
        'eyebrow' => 'For employers',
        'title'   => 'Hire the best people, on autopilot',
        'lede'    => 'S.C.O.U.T. screens and ranks every applicant so your team spends time with the right candidates — not their inbox.',
        'meta_title' => 'AI Hiring Platform for Employers | StudAI Hire',
        'meta_desc'  => 'Employers hire faster with StudAI Hire’s S.C.O.U.T. — an autonomous ATS that screens, ranks and shortlists candidates automatically.',
        'keywords'   => 'AI hiring platform India, autonomous ATS, recruitment automation, candidate screening AI',
        'pains'   => ['Drowning in applications', 'Slow, inconsistent screening', 'Good candidates slipping through'],
        'gains'   => [
            ['title' => 'Screen at scale', 'desc' => 'Every applicant assessed instantly and consistently.'],
            ['title' => 'Surface the best', 'desc' => 'Explainable rankings put the strongest people first.'],
            ['title' => 'Hire faster', 'desc' => 'Cut time-to-shortlist and focus on real conversations.'],
        ],
        'products' => ['scout', 'smart-job-search', 'resume-studio'],
    ],
];
