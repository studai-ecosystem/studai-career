<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Global Site Configuration
|--------------------------------------------------------------------------
| Powers the shared cinematic marketing layout: brand, navigation, footer,
| SEO defaults, social profiles and Organization structured data.
| Edit content here — the whole site updates without markup changes.
*/

return [

    'brand' => [
        'name'      => 'StudAI Hire',
        'company'   => 'StudAI One',
        'legal'     => 'StudAI Edutech Pvt. Ltd.',
        'ecosystem' => 'A product of the StudAI One ecosystem.',
        'logo'      => '/assets/logo/icon.png?v=3',
        'wordmark'  => ['Stud', 'AI', ' Hire'],
        'tagline'   => 'The Autonomous Career OS',
        'pitch'     => "India's first complete autonomous AI hiring platform — it finds the roles, tailors every application, preps your interviews and wins the negotiation, while you stay in control.",
        'email'     => 'hello@studai.one',
        'support'   => 'hello@studai.one',
        'phone'     => '+91 80 4718 2200',
        'address'   => 'WeWork Galaxy, Residency Road, Bengaluru, Karnataka 560025, India',
        'country'   => 'IN',
        'founded'   => '2024',
    ],

    // Used for absolute URLs in sitemap, llms.txt and JSON-LD.
    'url' => env('APP_URL', 'https://studai.one'),

    'social' => [
        'twitter'   => 'https://twitter.com/studaihire',
        'linkedin'  => 'https://www.linkedin.com/company/studaihire',
        'instagram' => 'https://www.instagram.com/studaihire',
        'youtube'   => 'https://www.youtube.com/@studaihire',
        'github'    => 'https://github.com/studai-ecosystem',
    ],

    // ── Primary navigation (mega-style grouping) ───────────────────────────
    'nav' => [
        'product' => [
            'label' => 'Product',
            'links' => [
                ['label' => 'Autonomous Agent', 'route' => 'product', 'param' => 'autonomous-agent', 'desc' => 'Applies for you, around the clock'],
                ['label' => 'Smart Job Search', 'route' => 'product', 'param' => 'smart-job-search', 'desc' => 'Describe the role in plain English'],
                ['label' => 'Resume Studio',    'route' => 'product', 'param' => 'resume-studio',    'desc' => 'ATS-ready, tailored per role'],
                ['label' => 'Interview AI',      'route' => 'product', 'param' => 'interview-ai',      'desc' => 'Realistic mocks with feedback'],
                ['label' => 'Negotiation Coach', 'route' => 'product', 'param' => 'negotiation-coach', 'desc' => 'Win the offer, backed by data'],
                ['label' => 'S.C.O.U.T. for Employers', 'route' => 'product', 'param' => 'scout', 'desc' => 'Autonomous applicant tracking'],
            ],
        ],
        'solutions' => [
            'label' => 'Use cases',
            'links' => [
                ['label' => 'Students',             'route' => 'use-case', 'param' => 'students',            'desc' => 'Land your first role'],
                ['label' => 'Freshers',             'route' => 'use-case', 'param' => 'freshers',            'desc' => 'Break into the industry'],
                ['label' => 'Working professionals','route' => 'use-case', 'param' => 'working-professionals','desc' => 'Level up without the grind'],
                ['label' => 'Career switchers',     'route' => 'use-case', 'param' => 'career-switchers',    'desc' => 'Pivot with confidence'],
                ['label' => 'Returning to work',    'route' => 'use-case', 'param' => 'returning-to-work',   'desc' => 'Restart your momentum'],
                ['label' => 'Employers & recruiters','route' => 'employers',                                'desc' => 'Hire on autopilot'],
            ],
        ],
        'links' => [
            ['label' => 'How it works', 'route' => 'how-it-works'],
            ['label' => 'Pricing',      'route' => 'pricing'],
            ['label' => 'Blog',         'route' => 'blog'],
        ],
        'sign_in' => ['label' => 'Sign in',     'route' => 'login'],
        'sign_up' => ['label' => 'Get started', 'route' => 'register'],
    ],

    // ── Footer ─────────────────────────────────────────────────────────────
    'footer' => [
        'tagline' => "India's first complete autonomous AI hiring platform. Your career, on autopilot.",
        'columns' => [
            [
                'heading' => 'Product',
                'links'   => [
                    ['label' => 'Autonomous Agent', 'route' => 'product', 'param' => 'autonomous-agent'],
                    ['label' => 'Smart Job Search', 'route' => 'product', 'param' => 'smart-job-search'],
                    ['label' => 'Resume Studio',    'route' => 'product', 'param' => 'resume-studio'],
                    ['label' => 'Interview AI',      'route' => 'product', 'param' => 'interview-ai'],
                    ['label' => 'Negotiation Coach', 'route' => 'product', 'param' => 'negotiation-coach'],
                    ['label' => 'Features',          'route' => 'features'],
                ],
            ],
            [
                'heading' => 'Use cases',
                'links'   => [
                    ['label' => 'For students',     'route' => 'use-case', 'param' => 'students'],
                    ['label' => 'For freshers',     'route' => 'use-case', 'param' => 'freshers'],
                    ['label' => 'For professionals','route' => 'use-case', 'param' => 'working-professionals'],
                    ['label' => 'For switchers',    'route' => 'use-case', 'param' => 'career-switchers'],
                    ['label' => 'All use cases',    'route' => 'use-cases'],
                    ['label' => 'For employers',    'route' => 'employers'],
                ],
            ],
            [
                'heading' => 'Company',
                'links'   => [
                    ['label' => 'About',     'route' => 'about'],
                    ['label' => 'How it works','route' => 'how-it-works'],
                    ['label' => 'Blog',      'route' => 'blog'],
                    ['label' => 'Careers',   'route' => 'careers'],
                    ['label' => 'Contact',   'route' => 'contact'],
                    ['label' => 'FAQ',       'route' => 'faq'],
                ],
            ],
            [
                'heading' => 'Legal',
                'links'   => [
                    ['label' => 'Privacy Policy',    'route' => 'privacy'],
                    ['label' => 'Terms of Service',  'route' => 'terms'],
                    ['label' => 'Refund Policy',     'route' => 'refund-policy'],
                    ['label' => 'Cookie Policy',     'route' => 'cookie-policy'],
                    ['label' => 'Security',          'route' => 'security'],
                    ['label' => 'Acceptable Use',    'route' => 'legal', 'param' => 'acceptable-use'],
                ],
            ],
        ],
        'legal' => '© ' . date('Y') . ' StudAI Edutech Pvt. Ltd. · A StudAI One product · Built in India 🇮🇳',
    ],

    // ── SEO defaults (per-page values override these) ──────────────────────
    'seo' => [
        'title'        => "StudAI Hire — India's First Autonomous AI Hiring Platform",
        'description'  => "StudAI Hire is India's first complete autonomous AI hiring platform. Our AI finds roles, tailors applications, runs mock interviews and negotiates your offer — your career, on autopilot.",
        'keywords'     => 'autonomous AI hiring, AI job application, automated job search India, AI career platform, AI resume builder, AI interview practice, salary negotiation AI, autonomous job agent, AI recruitment, StudAI Hire',
        'og_image'     => '/assets/og/studai-hire-og.png',
        'twitter'      => '@studaihire',
        'locale'       => 'en_IN',
        'theme_color'  => '#2563EB',
    ],
];
