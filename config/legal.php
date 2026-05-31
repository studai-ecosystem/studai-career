<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StudAI Hire — Legal & Policy Documents (data-driven)
|--------------------------------------------------------------------------
| Renders /legal/{slug} and the canonical short routes (privacy, terms…).
| Plain-language summaries; not a substitute for counsel-reviewed contracts.
| Each doc: title, meta, updated date, intro, sections[{heading, body[]}].
*/

return [

    'privacy' => [
        'title'      => 'Privacy Policy',
        'meta_title' => 'Privacy Policy | StudAI Hire',
        'meta_desc'  => 'How StudAI Hire collects, uses, protects and shares your personal information across our autonomous AI hiring platform.',
        'updated'    => '2024-11-01',
        'intro'      => 'StudAI Hire is operated by StudAI Edutech Pvt. Ltd. (“StudAI One”). Your trust matters to us. This policy explains what we collect, why we collect it, and the choices you have. We keep it in plain language on purpose.',
        'sections'   => [
            ['heading' => 'Information we collect', 'body' => [
                'Account details you provide, such as your name, email and profile information.',
                'Career data you share, including resumes, job preferences and application history.',
                'Usage data that helps us improve the product, such as the features you use and how you interact with the platform.',
            ]],
            ['heading' => 'How we use your information', 'body' => [
                'To operate the autonomous agent and other features on your behalf.',
                'To match you with relevant roles and tailor your applications.',
                'To secure your account, prevent abuse and meet our legal obligations.',
            ]],
            ['heading' => 'How we share information', 'body' => [
                'With employers when you (or your agent, within your rules) submit an application.',
                'With service providers who help us run the platform, under strict confidentiality.',
                'We never sell your personal data.',
            ]],
            ['heading' => 'Your choices and rights', 'body' => [
                'You can access, correct or delete your information at any time from your account.',
                'You can pause or stop the autonomous agent whenever you wish.',
                'To exercise any privacy right, contact us at hello@studai.one.',
            ]],
            ['heading' => 'Data security', 'body' => [
                'We use industry-standard safeguards to protect your information in transit and at rest.',
                'Access to personal data is limited to those who need it to operate the service.',
            ]],
        ],
    ],

    'terms' => [
        'title'      => 'Terms & Conditions',
        'meta_title' => 'Terms & Conditions | StudAI Hire',
        'meta_desc'  => 'The terms that govern your use of the StudAI Hire autonomous AI hiring platform.',
        'updated'    => '2024-11-01',
        'intro'      => 'These terms govern your use of StudAI Hire, a product of StudAI One operated by StudAI Edutech Pvt. Ltd. By creating an account or using the service, you agree to them.',
        'sections'   => [
            ['heading' => 'Using StudAI Hire', 'body' => [
                'You must provide accurate information and keep your account secure.',
                'You are responsible for the goals and guardrails you set for your autonomous agent.',
                'You agree to use the platform lawfully and not to misuse or disrupt the service.',
            ]],
            ['heading' => 'The autonomous agent', 'body' => [
                'The agent acts within the rules and approvals you configure.',
                'You can review, pause or stop the agent at any time.',
                'You remain responsible for applications submitted on your behalf within your settings.',
            ]],
            ['heading' => 'Subscriptions and payments', 'body' => [
                'Paid plans are billed in advance on the cycle you choose.',
                'You can manage or cancel your subscription from your account.',
            ]],
            ['heading' => 'Intellectual property', 'body' => [
                'StudAI Hire and its content remain our property.',
                'You retain ownership of the content you upload, and grant us the rights needed to operate the service for you.',
            ]],
            ['heading' => 'Limitation of liability', 'body' => [
                'The service is provided on an as-is basis. We do our best, but outcomes such as interviews or offers are never guaranteed.',
            ]],
        ],
    ],

    'refund' => [
        'title'      => 'Refund Policy',
        'meta_title' => 'Refund Policy | StudAI Hire',
        'meta_desc'  => 'Our approach to refunds and cancellations for StudAI Hire subscriptions.',
        'updated'    => '2024-11-01',
        'intro'      => 'We want you to be happy with StudAI Hire. Here’s how refunds and cancellations work.',
        'sections'   => [
            ['heading' => 'Cancellations', 'body' => [
                'You can cancel your subscription at any time from your account.',
                'After cancelling, you keep access until the end of your current billing period.',
            ]],
            ['heading' => 'Refunds', 'body' => [
                'If something isn’t right, contact us within a reasonable time and we’ll do our best to make it fair.',
                'Refund requests are reviewed case by case at hello@studai.one.',
            ]],
            ['heading' => 'Renewals', 'body' => [
                'Subscriptions renew automatically unless cancelled before the renewal date.',
            ]],
        ],
    ],

    'cookie' => [
        'title'      => 'Cookie Policy',
        'meta_title' => 'Cookie Policy | StudAI Hire',
        'meta_desc'  => 'How and why StudAI Hire uses cookies and similar technologies.',
        'updated'    => '2024-11-01',
        'intro'      => 'Cookies help our platform work and help us understand how it’s used. This policy explains how we use them.',
        'sections'   => [
            ['heading' => 'What cookies we use', 'body' => [
                'Essential cookies that keep you signed in and the platform working.',
                'Analytics cookies that help us understand and improve the experience.',
            ]],
            ['heading' => 'Managing cookies', 'body' => [
                'You can control cookies through your browser settings.',
                'Disabling essential cookies may affect how the platform works.',
            ]],
        ],
    ],

    'security' => [
        'title'      => 'Security',
        'meta_title' => 'Security at StudAI Hire | StudAI Hire',
        'meta_desc'  => 'How StudAI Hire protects your data and keeps the autonomous hiring platform secure.',
        'updated'    => '2024-11-01',
        'intro'      => 'Security is foundational to a platform that acts on your behalf. Here’s how we protect you.',
        'sections'   => [
            ['heading' => 'Encryption', 'body' => [
                'Your data is encrypted in transit and at rest using industry-standard protocols.',
            ]],
            ['heading' => 'Access controls', 'body' => [
                'Access to systems and data is restricted, logged and reviewed.',
                'The autonomous agent operates strictly within the permissions you grant.',
            ]],
            ['heading' => 'Responsible disclosure', 'body' => [
                'If you believe you’ve found a vulnerability, please report it to hello@studai.one so we can address it quickly.',
            ]],
        ],
    ],

    'acceptable-use' => [
        'title'      => 'Acceptable Use Policy',
        'meta_title' => 'Acceptable Use Policy | StudAI Hire',
        'meta_desc'  => 'The rules for using StudAI Hire fairly, lawfully and respectfully.',
        'updated'    => '2024-11-01',
        'intro'      => 'To keep StudAI Hire safe and useful for everyone, all users agree to these rules.',
        'sections'   => [
            ['heading' => 'You agree not to', 'body' => [
                'Provide false information or impersonate others.',
                'Use the platform to spam, harass or mislead employers or candidates.',
                'Attempt to disrupt, reverse-engineer or abuse the service.',
            ]],
            ['heading' => 'Fair use', 'body' => [
                'Use the autonomous agent in good faith and within reasonable limits.',
                'Respect the terms set by employers and third-party job sources.',
            ]],
        ],
    ],

    'disclaimer' => [
        'title'      => 'Disclaimer',
        'meta_title' => 'Disclaimer | StudAI Hire',
        'meta_desc'  => 'Important information about the StudAI Hire service and outcomes.',
        'updated'    => '2024-11-01',
        'intro'      => 'StudAI Hire is a tool to support your career — not a guarantee of any particular outcome.',
        'sections'   => [
            ['heading' => 'No guaranteed outcomes', 'body' => [
                'We help you apply, prepare and negotiate, but we can’t guarantee interviews, offers or hires.',
            ]],
            ['heading' => 'Third-party content', 'body' => [
                'Job listings and employer information come from many sources and may change without notice.',
            ]],
        ],
    ],
];
