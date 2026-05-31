<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Blog / Resources (data-driven)
|--------------------------------------------------------------------------
| Renders /blog (index) and /blog/{slug} (article). Each post carries its
| own SEO meta and renders Article JSON-LD for rich search results.
| 'body' is an array of sections: ['h' => heading, 'p' => [paragraphs...]].
*/

return [

    'meta' => [
        'title'    => 'The StudAI Hire Blog — Careers, on autopilot',
        'meta_title' => 'Career & Hiring Blog | StudAI Hire',
        'meta_desc'  => 'Insights on AI job search, resumes, interviews, salary negotiation and the future of autonomous hiring in India — from the StudAI Hire team.',
        'keywords'   => 'career blog India, AI job search tips, resume advice, interview tips, salary negotiation',
        'lede'       => 'Playbooks, deep-dives and field notes on building a career in the age of autonomous AI.',
    ],

    'categories' => ['Job Search', 'Resumes', 'Interviews', 'Negotiation', 'Hiring', 'Product'],

    'posts' => [

        'why-india-needs-an-autonomous-hiring-platform' => [
            'title'    => 'Why India needs its first autonomous AI hiring platform',
            'category' => 'Product',
            'author'   => 'StudAI Hire Team',
            'date'     => '2024-11-04',
            'read'     => '6 min read',
            'accent'   => 'blue',
            'meta_title' => 'Why India Needs an Autonomous AI Hiring Platform | StudAI Hire',
            'meta_desc'  => 'Job search in India is broken by volume and noise. Here’s why an autonomous AI hiring platform changes the game for candidates and employers alike.',
            'keywords'   => 'autonomous hiring India, AI hiring platform, future of recruitment India',
            'excerpt'  => 'The Indian job market moves at incredible speed and scale. Manual applying simply can’t keep up. Here’s the case for autonomy.',
            'body'     => [
                ['h' => 'The volume problem', 'p' => [
                    'A single opening in India can attract a flood of applicants within hours. For candidates, that means racing the clock. For employers, it means drowning in resumes. The traditional, manual model was never built for this scale.',
                    'When everyone applies to everything, signal gets lost. Great candidates blend into the noise, and great roles go unnoticed by the people who’d be perfect for them.',
                ]],
                ['h' => 'Why autonomy, not just automation', 'p' => [
                    'Automation does one task on command. Autonomy pursues a goal. An autonomous agent doesn’t just fire off applications — it watches the market, understands fit, tailors each application and learns what’s working.',
                    'That shift, from tools you operate to an agent that works for you, is what makes a hands-free job search possible.',
                ]],
                ['h' => 'Built for India, first', 'p' => [
                    'StudAI Hire is designed for the speed, scale and diversity of the Indian market — and for both sides of the table. Candidates get an agent that applies for them. Employers get S.C.O.U.T., an autonomous ATS that surfaces the best people.',
                    'That’s what it means to be the country’s first complete autonomous AI hiring platform.',
                ]],
            ],
        ],

        'how-to-beat-the-ats-in-2025' => [
            'title'    => 'How to beat the ATS: a practical guide',
            'category' => 'Resumes',
            'author'   => 'StudAI Hire Team',
            'date'     => '2024-10-21',
            'read'     => '7 min read',
            'accent'   => 'green',
            'meta_title' => 'How to Beat the ATS — Practical Resume Guide | StudAI Hire',
            'meta_desc'  => 'Most resumes are filtered by software before a human sees them. Learn how applicant tracking systems work and how to build a resume that gets through.',
            'keywords'   => 'beat the ATS, ATS resume tips, applicant tracking system, resume keywords',
            'excerpt'  => 'Most resumes are read by software before a human ever sees them. Here’s how to make sure yours gets through.',
            'body'     => [
                ['h' => 'What an ATS actually does', 'p' => [
                    'An applicant tracking system parses your resume into structured data, then matches it against the job requirements. If it can’t read your file cleanly, or can’t find the right signals, you’re filtered out before a recruiter looks.',
                ]],
                ['h' => 'Format for the machine', 'p' => [
                    'Use a clean, single-column structure with standard section headings. Avoid tables, text boxes and graphics that confuse parsers. Save as a standard, machine-readable file. Simple beats clever here.',
                ]],
                ['h' => 'Write for the role', 'p' => [
                    'Mirror the language of the job description where it’s genuinely true of you. Lead with outcomes, not duties. The goal is alignment between what the role asks for and what your resume clearly shows.',
                    'Resume Studio does this automatically — tailoring your resume to every role so you stay readable to both the bots and the humans.',
                ]],
            ],
        ],

        'salary-negotiation-scripts-that-work' => [
            'title'    => 'Salary negotiation: scripts that actually work',
            'category' => 'Negotiation',
            'author'   => 'StudAI Hire Team',
            'date'     => '2024-10-08',
            'read'     => '8 min read',
            'accent'   => 'amber',
            'meta_title' => 'Salary Negotiation Scripts That Work | StudAI Hire',
            'meta_desc'  => 'Don’t leave money on the table. Use these proven, data-backed salary negotiation scripts to confidently win the offer you deserve.',
            'keywords'   => 'salary negotiation script, negotiate job offer India, counter offer email, compensation negotiation',
            'excerpt'  => 'The offer is rarely the final number. Here’s exactly what to say to negotiate with confidence.',
            'body'     => [
                ['h' => 'Anchor on value, not need', 'p' => [
                    'Strong negotiation starts from the value you bring and the market rate for the role — never from personal need. When your ask is grounded in evidence, it’s far harder to dismiss.',
                ]],
                ['h' => 'The opening ask', 'p' => [
                    '“Thank you — I’m genuinely excited about this role. Based on my experience and the market for this position, I was expecting something in the range of X. Can we get there?” Calm, specific, and backed by data.',
                ]],
                ['h' => 'Handling the counter', 'p' => [
                    'Expect pushback and prepare for it. Acknowledge constraints, then redirect to total compensation — bonus, equity, benefits and growth. There’s almost always more than base pay to negotiate.',
                    'Negotiation Coach builds these scripts around your exact offer, so you always know the next thing to say.',
                ]],
            ],
        ],

        'ace-your-next-interview-with-ai-practice' => [
            'title'    => 'Ace your next interview with AI practice',
            'category' => 'Interviews',
            'author'   => 'StudAI Hire Team',
            'date'     => '2024-09-26',
            'read'     => '5 min read',
            'accent'   => 'violet',
            'meta_title' => 'Ace Your Interview with AI Practice | StudAI Hire',
            'meta_desc'  => 'Realistic AI mock interviews and honest feedback help you prepare for the questions that matter. Learn how to practice your way to confidence.',
            'keywords'   => 'AI mock interview, interview preparation India, behavioural interview practice, interview feedback',
            'excerpt'  => 'Confidence in an interview comes from preparation. Here’s how to practice the questions that actually matter.',
            'body'     => [
                ['h' => 'Practice beats hoping', 'p' => [
                    'The candidates who do best aren’t always the most qualified — they’re the most prepared. Rehearsing real questions out loud turns nerves into fluency.',
                ]],
                ['h' => 'Structure your answers', 'p' => [
                    'For behavioural questions, frame your answer around situation, action and result. Keep it specific and concise. A clear structure makes you easy to follow and easy to say yes to.',
                ]],
                ['h' => 'Get feedback that’s honest', 'p' => [
                    'Practising alone, you can’t see your blind spots. Interview AI runs realistic rounds tuned to your role and gives you direct feedback on what to keep and what to fix — before it counts.',
                ]],
            ],
        ],

        'the-future-of-job-search-is-hands-free' => [
            'title'    => 'The future of job search is hands-free',
            'category' => 'Job Search',
            'author'   => 'StudAI Hire Team',
            'date'     => '2024-09-12',
            'read'     => '6 min read',
            'accent'   => 'blue',
            'meta_title' => 'The Future of Job Search Is Hands-Free | StudAI Hire',
            'meta_desc'  => 'Manual applying is exhausting and inefficient. Discover how autonomous AI agents are making the job search hands-free — and what it means for you.',
            'keywords'   => 'future of job search, autonomous job agent, hands-free job search, AI career assistant',
            'excerpt'  => 'Applying to jobs shouldn’t be a second full-time job. Here’s what changes when an agent does it for you.',
            'body'     => [
                ['h' => 'The job of applying', 'p' => [
                    'Searching, tailoring, tracking, following up — the modern job hunt is a project in itself. It’s no wonder so many great candidates burn out before they find the right role.',
                ]],
                ['h' => 'Enter the agent', 'p' => [
                    'An autonomous agent flips the model. You set the goal once; it does the work continuously. It applies while you sleep, tailors every submission and keeps everything organised.',
                ]],
                ['h' => 'What you get back', 'p' => [
                    'Time. Energy. And a steadier pipeline of interviews for roles that actually fit. That’s the promise of a hands-free search — and it’s here today with StudAI Hire.',
                ]],
            ],
        ],

    ],
];
