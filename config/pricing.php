<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Pricing Plans (data-driven)
|--------------------------------------------------------------------------
*/

return [
    'meta' => [
        'title'      => 'Pricing — Simple plans for every career',
        'meta_title' => 'Pricing | StudAI Hire',
        'meta_desc'  => 'Simple, transparent pricing for StudAI Hire. Start free and upgrade when your autonomous AI agent is ready to run your full job search.',
        'keywords'   => 'StudAI Hire pricing, AI job search cost, autonomous agent plans India',
        'lede'       => 'Start free. Upgrade when you’re ready to put your career fully on autopilot.',
    ],

    'plans' => [
        [
            'name'     => 'Starter',
            'price'    => '₹0',
            'period'   => 'forever',
            'tagline'  => 'Everything you need to begin.',
            'accent'   => false,
            'cta'      => 'Get started free',
            'features' => [
                'Smart Job Search in natural language',
                'Resume Studio with ATS check',
                'A few autonomous applications each day',
                'Application tracker',
                'Community support',
            ],
        ],
        [
            'name'     => 'Pro',
            'price'    => '₹499',
            'period'   => 'per month',
            'tagline'  => 'Your full career, on autopilot.',
            'accent'   => true,
            'cta'      => 'Start Pro',
            'features' => [
                'Everything in Starter',
                'Unlimited autonomous applications',
                'Per-role resume tailoring',
                'Interview AI with feedback',
                'Negotiation Coach',
                'Priority support',
            ],
        ],
        [
            'name'     => 'Teams',
            'price'    => 'Custom',
            'period'   => 'for employers',
            'tagline'  => 'Hire on autopilot with S.C.O.U.T.',
            'accent'   => false,
            'cta'      => 'Talk to us',
            'features' => [
                'S.C.O.U.T. autonomous ATS',
                'Auto-screening & ranked shortlists',
                'Structured hiring pipelines',
                'Team collaboration',
                'Dedicated onboarding',
            ],
        ],
    ],
];
