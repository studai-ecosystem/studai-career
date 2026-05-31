<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Landing Page Content (fully dynamic)
|--------------------------------------------------------------------------
| Every section, label and string on the homepage is driven from this file.
| Change copy here and the homepage updates — no markup edits required.
| Intentionally outcome- and feature-led: no statistics or vanity metrics.
*/

return [

    'brand' => [
        'name'      => 'StudAI One',
        'logo'      => '/assets/logo/icon.png?v=3',
        'wordmark'  => ['Stud', 'AI', ' One'],
        'tagline'   => 'The Autonomous Career OS',
    ],

    'nav' => [
        'links' => [
            ['label' => 'How it works',  'route' => 'how-it-works'],
            ['label' => 'Features',      'route' => 'features'],
            ['label' => 'Pricing',       'route' => 'pricing'],
            ['label' => 'For employers', 'route' => 'employers'],
        ],
        'sign_in' => ['label' => 'Sign in',    'route' => 'login'],
        'sign_up' => ['label' => 'Get started', 'route' => 'register'],
    ],

    // ── HERO ──────────────────────────────────────────────────────────────
    'hero' => [
        'eyebrow' => 'The Autonomous Career OS',
        'title'   => [
            'lead' => 'Stop applying.',
            'emph' => 'Start arriving.',
        ],
        'lede'    => 'StudAI Hire runs your entire job search for you — finding the right roles, tailoring every application, preparing you for interviews and negotiating the offer. You simply show up and choose.',
        'primary'   => ['label' => 'Get started',     'route' => 'register'],
        'secondary' => ['label' => 'See how it works', 'route' => 'how-it-works'],
        'trust'     => 'Free to start · No credit card required',

        // A calm product surface rendered beside the headline — communicates
        // the experience, not numbers.
        'panel' => [
            'label'    => 'Autopilot',
            'status'   => 'Active',
            'caption'  => 'Working in the background, so you don’t have to.',
            'steps'    => [
                ['state' => 'done',    'title' => 'Found roles that fit you',        'note' => 'Matched to your goals'],
                ['state' => 'active',  'title' => 'Tailoring your applications',     'note' => 'Rewritten for each job'],
                ['state' => 'queued',  'title' => 'Preparing your interviews',       'note' => 'Mock rounds & feedback'],
                ['state' => 'queued',  'title' => 'Negotiating your offer',          'note' => 'Backed by live market data'],
            ],
            'footnote' => 'You stay in control — approve anything, anytime.',
        ],
    ],

    // ── TRUST / LOGO ROW ──────────────────────────────────────────────────
    'logos' => [
        'label' => 'Our members go on to build careers at',
        'items' => ['Google', 'Microsoft', 'Amazon', 'Flipkart', 'Razorpay', 'Swiggy', 'PhonePe', 'Zerodha'],
    ],

    // ── EDITORIAL STATEMENT (big cinematic line) ──────────────────────────
    'statement' => [
        'kicker' => 'The whole journey, on autopilot',
        'lead'   => 'Your entire job hunt,',
        'emph'   => 'handled end to end.',
        'sub'    => 'From the very first match to the final signed offer — StudAI Hire does the work, you simply make the calls.',
        'chips'  => ['Finds the roles', 'Tailors every application', 'Preps your interviews', 'Wins the negotiation'],
    ],

    // ── PILLARS (alternating feature stories) ─────────────────────────────
    'pillars' => [
        'eyebrow' => 'Why StudAI Hire',
        'title'   => 'A career that moves forward on its own.',
        'subtitle'=> 'Three things every great search needs — now handled for you, end to end.',
        'items' => [
            [
                'kicker'  => 'Autonomous Agent',
                'title'   => 'It applies, so you don’t have to.',
                'desc'    => 'Set your direction once. Your agent searches every day, tailors each application to the exact role, and submits it for you — quietly working while you get on with your life.',
                'points'  => [
                    'Finds the roles that genuinely fit',
                    'Tailors every resume and cover letter',
                    'Keeps your search moving, every single day',
                ],
                'cta'     => ['label' => 'Meet your agent', 'route' => 'register'],
                'accent'  => 'blue',
                'visual'  => 'agent',
            ],
            [
                'kicker'  => 'Interview AI',
                'title'   => 'Walk in ready. Walk out chosen.',
                'desc'    => 'Rehearse with an AI interviewer trained on real questions for your role. Get honest, specific feedback — and the quiet confidence that comes from genuine practice.',
                'points'  => [
                    'Role-specific mock interviews',
                    'Clear, constructive feedback',
                    'Practice until it feels easy',
                ],
                'cta'     => ['label' => 'Practice now', 'route' => 'features'],
                'accent'  => 'green',
                'visual'  => 'interview',
            ],
            [
                'kicker'  => 'Negotiation Coach',
                'title'   => 'Never leave money on the table.',
                'desc'    => 'Understand exactly what your offer is worth with live market intelligence, then let your coach script the conversation — word for word — so you ask with confidence.',
                'points'  => [
                    'Live, role-aware market benchmarks',
                    'A script for every moment of the talk',
                    'Confidence to ask for what you’re worth',
                ],
                'cta'     => ['label' => 'See your worth', 'route' => 'features'],
                'accent'  => 'violet',
                'visual'  => 'offer',
            ],
        ],
    ],

    // ── MODULES (clean capability grid) ───────────────────────────────────
    'modules' => [
        'eyebrow' => 'One OS, every part of the hunt',
        'title'   => 'Everything a recruiter, coach and assistant would do.',
        'subtitle'=> 'Six modules working as one system, so nothing about your search slips through the cracks.',
        'items' => [
            [
                'icon'  => 'bolt',
                'tone'  => 'blue',
                'title' => 'Autonomous Agent',
                'desc'  => 'Set your preferences once and let the agent apply to matching roles for you — every application tailored, nothing generic.',
            ],
            [
                'icon'  => 'search',
                'tone'  => 'slate',
                'title' => 'Smart Job Search',
                'desc'  => 'Ask in plain English. The AI surfaces perfect-fit roles and the hidden ones others miss — no endless scrolling.',
            ],
            [
                'icon'  => 'doc',
                'tone'  => 'green',
                'title' => 'Resume Studio',
                'desc'  => 'Resumes that get past the filters and earn callbacks — rewritten to match each role in minutes.',
            ],
            [
                'icon'  => 'mic',
                'tone'  => 'amber',
                'title' => 'Interview AI',
                'desc'  => 'Rehearse with an AI interviewer, get scored, and walk into the real thing calm and prepared.',
            ],
            [
                'icon'  => 'chart',
                'tone'  => 'violet',
                'title' => 'Negotiation Coach',
                'desc'  => 'Know your worth with live market data, then follow a script that lifts your offer.',
            ],
            [
                'icon'  => 'shield',
                'tone'  => 'blue',
                'title' => 'S.C.O.U.T. for employers',
                'desc'  => 'Fairer, faster hiring that surfaces talent others overlook — bias-aware from the first screen.',
            ],
        ],
    ],

    // ── HOW IT WORKS ──────────────────────────────────────────────────────
    'journey' => [
        'eyebrow' => 'How it works',
        'title'   => 'Set it up once. It runs itself.',
        'subtitle'=> 'A few minutes to begin — then your agent works every day after, with you in control of what matters.',
        'steps' => [
            [
                'title' => 'Tell it who you are',
                'desc'  => 'Add your resume or LinkedIn and the AI builds a living career profile — your skills, goals and ambitions.',
                'meta'  => 'In minutes',
            ],
            [
                'title' => 'Set your direction',
                'desc'  => 'Choose the roles, the work and the life you want. Be selective — your agent only chases real fits.',
                'meta'  => 'You’re in control',
            ],
            [
                'title' => 'The agent goes to work',
                'desc'  => 'It finds matches, tailors every application, and submits them for you — quietly, in the background.',
                'meta'  => 'Always on',
            ],
            [
                'title' => 'You interview & arrive',
                'desc'  => 'Rehearse with AI, let your coach win the negotiation, and choose the offer that’s right for you.',
                'meta'  => 'You choose',
            ],
        ],
    ],

    // ── A DAY ON AUTOPILOT (narrative, no numbers) ────────────────────────
    'experience' => [
        'eyebrow' => 'A day on autopilot',
        'title'   => 'While you live your life, your search moves forward.',
        'subtitle'=> 'You set the direction. By the time you look up, things have happened — without you lifting a finger.',
        'timeline' => [
            ['phase' => 'Morning',   'title' => 'Fresh matches, found for you',  'desc' => 'Your agent scans the day’s new listings and shortlists the ones worth your time.'],
            ['phase' => 'Midday',    'title' => 'Applications, tailored & sent',  'desc' => 'Each resume and cover letter is rewritten to mirror the role — then submitted on your behalf.'],
            ['phase' => 'Afternoon', 'title' => 'A recruiter takes notice',       'desc' => 'When your profile gets opened, a thoughtful follow-up keeps you top of mind.'],
            ['phase' => 'Evening',   'title' => 'An interview, on the calendar',  'desc' => 'A round gets booked — and your tailored mock prep is already waiting for you.'],
            ['phase' => 'Night',     'title' => 'Offer intelligence, ready',      'desc' => 'When it’s time to talk money, your market-backed negotiation script is drafted and waiting.'],
        ],
    ],

    // ── OLD WAY vs STUDAI HIRE ────────────────────────────────────────────
    'comparison' => [
        'eyebrow' => 'Why it’s different',
        'title'   => 'The job hunt was exhausting. We rebuilt it.',
        'old' => [
            'label'  => 'The old way',
            'points' => [
                'Refreshing job boards for hours',
                'Rewriting the same resume over and over',
                'Applications vanishing into the void',
                'Walking into interviews unprepared',
                'Accepting the first number offered',
            ],
        ],
        'new' => [
            'label'  => 'With StudAI Hire',
            'points' => [
                'Your agent watches every board for you',
                'Each application tailored automatically',
                'Smart follow-ups keep you top of the pile',
                'AI mock interviews until you’re sharp',
                'A coach who lifts the offer for you',
            ],
        ],
    ],

    // ── TESTIMONIALS ──────────────────────────────────────────────────────
    'testimonials' => [
        'eyebrow' => 'Careers transformed',
        'title'   => 'Real people. Real offers.',
        'items' => [
            [
                'quote'    => 'The agent felt like a full-time job-search assistant. I didn’t fill in a single form myself — and the offers still came.',
                'name'     => 'Priya Sharma',
                'role'     => 'Senior Software Engineer · Google',
                'initials' => 'PS',
            ],
            [
                'quote'    => 'The interview AI cracked my loop. The behavioural prep was scarily on point — I walked in genuinely calm.',
                'name'     => 'Rahul Menon',
                'role'     => 'SDE-2 · Amazon',
                'initials' => 'RM',
            ],
            [
                'quote'    => 'The negotiation coach got me far more than I’d ever have asked for on my own. It paid for itself many times over.',
                'name'     => 'Aditya Rao',
                'role'     => 'Product Manager · PhonePe',
                'initials' => 'AR',
            ],
            [
                'quote'    => 'As a recruiter, S.C.O.U.T. made our hiring faster and fairer — and finally moved our diversity in the right direction.',
                'name'     => 'Anjali Verma',
                'role'     => 'Head of Talent · Razorpay',
                'initials' => 'AV',
            ],
        ],
    ],

    // ── FAQ ───────────────────────────────────────────────────────────────
    'faq' => [
        'eyebrow' => 'Questions, answered',
        'title'   => 'Everything you’re wondering.',
        'items' => [
            ['q' => 'Does the AI really apply to jobs for me?', 'a' => 'Yes. Once you set your targets, the Autonomous Agent finds matching roles and submits tailored applications for you. You can review and approve each one, or let it run fully hands-off.'],
            ['q' => 'Will my applications look generic?',        'a' => 'The opposite. Every resume and cover letter is rewritten to mirror the specific job, so each application reads like you spent real time on it.'],
            ['q' => 'Is it really free to start?',              'a' => 'Yes — create your profile, explore roles and try the core tools free, with no credit card. Upgrade only when you want your agent running at full throttle.'],
            ['q' => 'How does salary negotiation work?',         'a' => 'The Negotiation Coach reads live market data for your role and city, then scripts exactly what to say — so you ask for what you’re worth, with confidence.'],
            ['q' => 'Is my data safe?',                          'a' => 'Your career data is encrypted and never sold. You control what’s shared with employers, and you can delete everything at any time.'],
        ],
    ],

    // ── FINAL CTA ─────────────────────────────────────────────────────────
    'cta' => [
        'title'     => 'Your next role is already out there.',
        'highlight' => 'Let your agent go get it.',
        'subtitle'  => 'Put your career on autopilot today. A few minutes to set up — and an unfair advantage from here on.',
        'primary'   => ['label' => 'Get started free', 'route' => 'register'],
        'secondary' => ['label' => 'Talk to us',       'route' => 'contact'],
    ],

    // ── FOOTER ────────────────────────────────────────────────────────────
    'footer' => [
        'tagline' => 'The Autonomous Career OS — your career, on autopilot.',
        'columns' => [
            [
                'heading' => 'Product',
                'links'   => [
                    ['label' => 'How it works',  'route' => 'how-it-works'],
                    ['label' => 'Features',      'route' => 'features'],
                    ['label' => 'Pricing',       'route' => 'pricing'],
                    ['label' => 'For employers', 'route' => 'employers'],
                ],
            ],
            [
                'heading' => 'Company',
                'links'   => [
                    ['label' => 'About',   'route' => 'about'],
                    ['label' => 'Contact', 'route' => 'contact'],
                ],
            ],
            [
                'heading' => 'Get started',
                'links'   => [
                    ['label' => 'Sign in',     'route' => 'login'],
                    ['label' => 'Get started', 'route' => 'register'],
                ],
            ],
        ],
        'legal' => '© ' . date('Y') . ' StudAI Hire. Built in India.',
    ],

];
