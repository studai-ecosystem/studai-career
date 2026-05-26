<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FreelancerProfile;
use App\Models\MarketplaceProject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(20)->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // ── Freelancer Profiles ─────────────────────────────────────────────
        $freelancerData = [
            ['title' => 'Full Stack Laravel & Vue.js Developer', 'bio' => 'Senior full-stack developer with 7+ years building SaaS products, REST APIs and complex web apps using Laravel, Vue.js, React and Tailwind CSS.', 'rate' => 2500, 'level' => 'expert', 'skills' => ['Laravel', 'Vue.js', 'React', 'MySQL', 'Redis', 'AWS'], 'rating' => 4.9, 'reviews' => 87, 'earnings' => 1250000, 'projects' => 63],
            ['title' => 'React Native & Flutter Mobile Developer', 'bio' => 'Mobile-first developer specialising in cross-platform apps published to App Store and Google Play. 5 years, 40+ shipped apps.', 'rate' => 2000, 'level' => 'expert', 'skills' => ['React Native', 'Flutter', 'Firebase', 'Swift', 'Kotlin'], 'rating' => 4.8, 'reviews' => 54, 'earnings' => 980000, 'projects' => 41],
            ['title' => 'UI/UX Designer & Figma Expert', 'bio' => 'Creative UI/UX designer crafting beautiful, user-centric interfaces. Expert in design systems, prototyping and handoff-ready deliverables.', 'rate' => 1500, 'level' => 'expert', 'skills' => ['Figma', 'Adobe XD', 'Prototyping', 'Design Systems', 'Framer'], 'rating' => 5.0, 'reviews' => 112, 'earnings' => 870000, 'projects' => 89],
            ['title' => 'Python & Data Science Developer', 'bio' => 'Machine learning engineer and data scientist. Pandas, scikit-learn, TensorFlow, PyTorch. From EDA to production ML pipelines.', 'rate' => 2800, 'level' => 'expert', 'skills' => ['Python', 'Machine Learning', 'TensorFlow', 'Pandas', 'SQL'], 'rating' => 4.7, 'reviews' => 39, 'earnings' => 760000, 'projects' => 28],
            ['title' => 'WordPress & WooCommerce Specialist', 'bio' => 'Building high-performance WordPress sites and WooCommerce stores for 6 years. Custom themes, plugins and speed optimisation.', 'rate' => 1200, 'level' => 'intermediate', 'skills' => ['WordPress', 'WooCommerce', 'PHP', 'Elementor', 'SEO'], 'rating' => 4.8, 'reviews' => 203, 'earnings' => 640000, 'projects' => 156],
            ['title' => 'Node.js & GraphQL Backend Engineer', 'bio' => 'Backend specialist building scalable APIs with Node.js, NestJS, GraphQL and PostgreSQL. Microservices and serverless architecture.', 'rate' => 2200, 'level' => 'expert', 'skills' => ['Node.js', 'GraphQL', 'NestJS', 'PostgreSQL', 'Docker'], 'rating' => 4.9, 'reviews' => 61, 'earnings' => 920000, 'projects' => 47],
            ['title' => 'SEO & Content Marketing Strategist', 'bio' => 'Helping businesses rank #1 on Google. Technical SEO, link building, content strategy and 300% average traffic growth for clients.', 'rate' => 1000, 'level' => 'expert', 'skills' => ['SEO', 'Content Marketing', 'Ahrefs', 'Google Analytics', 'Copywriting'], 'rating' => 4.8, 'reviews' => 178, 'earnings' => 430000, 'projects' => 134],
            ['title' => 'DevOps & Cloud Infrastructure Engineer', 'bio' => 'AWS/GCP certified DevOps engineer. CI/CD pipelines, Kubernetes, Docker, Terraform. 99.9% uptime track record.', 'rate' => 3000, 'level' => 'expert', 'skills' => ['AWS', 'Docker', 'Kubernetes', 'Terraform', 'CI/CD', 'Linux'], 'rating' => 4.9, 'reviews' => 45, 'earnings' => 1100000, 'projects' => 38],
        ];

        $freelancerUserIds = array_slice($users, 5, count($freelancerData));
        foreach ($freelancerData as $i => $data) {
            $uid = $freelancerUserIds[$i] ?? $users[array_rand($users)];
            FreelancerProfile::updateOrCreate(
                ['user_id' => $uid],
                [
                    'professional_title'  => $data['title'],
                    'bio'                 => $data['bio'],
                    'hourly_rate'         => $data['rate'],
                    'currency'            => 'INR',
                    'skills'              => json_encode($data['skills']),
                    'experience_level'    => $data['level'],
                    'availability'        => 'full_time',
                    'hours_per_week'      => 40,
                    'available_for_remote' => true,
                    'average_rating'      => $data['rating'],
                    'total_reviews'       => $data['reviews'],
                    'total_earnings'      => $data['earnings'],
                    'completed_projects'  => $data['projects'],
                    'success_rate'        => rand(88, 100),
                    'is_verified'         => true,
                    'is_featured'         => $i < 3,
                    'verified_at'         => now()->subMonths(rand(2, 12)),
                    'languages'           => json_encode(['English', 'Hindi']),
                ]
            );
        }

        // ── Projects ────────────────────────────────────────────────────────
        $projects = [
            // Web Development
            ['title' => 'Build a Multi-tenant SaaS Application with Laravel & Vue.js', 'category' => 'web_development', 'description' => 'We need an experienced Laravel developer to build a multi-tenant SaaS platform for HR management. The application should support multiple organisations with isolated data, subscription billing via Stripe, role-based access control, and a modern Vue.js frontend. Must include API documentation and comprehensive test coverage.', 'skills' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe', 'AWS'], 'budget_min' => 80000, 'budget_max' => 150000, 'level' => 'expert', 'days' => 60, 'urgent' => true, 'featured' => true],
            ['title' => 'E-commerce Website with React & Node.js Backend', 'category' => 'web_development', 'description' => 'Looking for a full-stack developer to build a fashion e-commerce store. Features needed: product catalogue with filters, cart & checkout, Razorpay/UPI integration, admin panel, order tracking, inventory management, and mobile-responsive design.', 'skills' => ['React', 'Node.js', 'MongoDB', 'Razorpay', 'Tailwind CSS'], 'budget_min' => 40000, 'budget_max' => 80000, 'level' => 'intermediate', 'days' => 45, 'urgent' => false, 'featured' => true],
            ['title' => 'WordPress WooCommerce Store Migration & Speed Optimization', 'category' => 'web_development', 'description' => 'Our WooCommerce store loads in 8 seconds. We need a WordPress expert to migrate to a new host, optimise images, implement caching (Redis/Varnish), minify assets, and get us to under 2 seconds load time. Google PageSpeed score must reach 90+.', 'skills' => ['WordPress', 'WooCommerce', 'PHP', 'Redis', 'Performance Optimization'], 'budget_min' => 15000, 'budget_max' => 30000, 'level' => 'intermediate', 'days' => 14, 'urgent' => true, 'featured' => false],
            ['title' => 'Build REST API with Laravel + Sanctum for Mobile App', 'category' => 'web_development', 'description' => 'Need a robust REST API built in Laravel for our existing mobile app (React Native). Endpoints for user auth (OTP via SMS), profile management, product listings, orders, push notifications via FCM, and admin reporting. Swagger documentation required.', 'skills' => ['Laravel', 'REST API', 'Sanctum', 'FCM', 'Swagger'], 'budget_min' => 25000, 'budget_max' => 50000, 'level' => 'intermediate', 'days' => 30, 'urgent' => false, 'featured' => false],
            ['title' => 'Custom CRM System for Real Estate Agency', 'category' => 'web_development', 'description' => 'Build a custom CRM for our 20-agent real estate firm. Features: lead management pipeline, property listings database, automated email follow-ups, appointment scheduling, WhatsApp integration, custom reporting dashboard, and mobile app access.', 'skills' => ['Laravel', 'Filament', 'MySQL', 'WhatsApp API', 'Vue.js'], 'budget_min' => 60000, 'budget_max' => 120000, 'level' => 'expert', 'days' => 75, 'urgent' => false, 'featured' => false],

            // Mobile Development
            ['title' => 'Flutter Food Delivery App (iOS + Android) — Clone of Swiggy UI', 'category' => 'mobile_development', 'description' => 'Build a Swiggy-style food delivery app with Flutter. Customer app: restaurants list, menu, cart, Razorpay payment, real-time order tracking via Google Maps. Delivery partner app: order accept/reject, navigation. Admin panel in React. Firebase backend.', 'skills' => ['Flutter', 'Dart', 'Firebase', 'Google Maps', 'Razorpay'], 'budget_min' => 70000, 'budget_max' => 130000, 'level' => 'expert', 'days' => 90, 'urgent' => false, 'featured' => true],
            ['title' => 'React Native Fitness Tracking App with Wearable Integration', 'category' => 'mobile_development', 'description' => 'Develop a fitness tracking app that integrates with Apple Watch and Fitbit. Features: workout logging, calorie tracking, step counter, sleep analysis, custom workout plans, social challenges, and in-app subscriptions.', 'skills' => ['React Native', 'HealthKit', 'Node.js', 'MongoDB', 'In-App Purchase'], 'budget_min' => 55000, 'budget_max' => 100000, 'level' => 'expert', 'days' => 60, 'urgent' => false, 'featured' => false],
            ['title' => 'Android Classifieds App (Olx/Quikr Clone) for Local Market', 'category' => 'mobile_development', 'description' => 'Native Android app for local classifieds with: category-based listings, image upload (up to 10 photos), in-app chat, location-based search, promoted listings, and user ratings. Backend API in Node.js + PostgreSQL.', 'skills' => ['Android', 'Kotlin', 'Node.js', 'PostgreSQL', 'Firebase'], 'budget_min' => 30000, 'budget_max' => 60000, 'level' => 'intermediate', 'days' => 45, 'urgent' => true, 'featured' => false],

            // Design
            ['title' => 'Complete Brand Identity Design — Logo, Style Guide & Marketing Assets', 'category' => 'design', 'description' => 'Startup in EdTech space looking for a complete brand identity: logo design (3 concepts), colour palette, typography system, brand guidelines PDF, business card, letterhead, social media templates (10 designs), and pitch deck template.', 'skills' => ['Figma', 'Adobe Illustrator', 'Brand Design', 'Adobe Photoshop'], 'budget_min' => 20000, 'budget_max' => 45000, 'level' => 'intermediate', 'days' => 21, 'urgent' => false, 'featured' => true],
            ['title' => 'SaaS Dashboard UI/UX Design in Figma — Dark & Light Mode', 'category' => 'design', 'description' => 'Design a comprehensive dashboard UI for our analytics SaaS product. Need: 25+ screens, dark/light mode variants, component library in Figma, interactive prototype, and developer handoff specs. Must follow WCAG 2.1 accessibility standards.', 'skills' => ['Figma', 'UI/UX Design', 'Design Systems', 'Prototyping', 'Accessibility'], 'budget_min' => 30000, 'budget_max' => 65000, 'level' => 'expert', 'days' => 30, 'urgent' => false, 'featured' => false],

            // Data Science / AI
            ['title' => 'Machine Learning Model for Customer Churn Prediction', 'category' => 'data_science', 'description' => 'We have 3 years of customer data (500k rows). Need a data scientist to build and deploy a churn prediction model with 85%+ accuracy. Deliverables: EDA report, model training notebook, FastAPI endpoint, Docker deployment, and monthly retraining pipeline.', 'skills' => ['Python', 'scikit-learn', 'FastAPI', 'Docker', 'PostgreSQL'], 'budget_min' => 40000, 'budget_max' => 80000, 'level' => 'expert', 'days' => 30, 'urgent' => false, 'featured' => true],
            ['title' => 'NLP Chatbot for Customer Support Using GPT-4 API', 'category' => 'data_science', 'description' => 'Build an AI customer support chatbot trained on our product documentation and FAQ database. Integration with our website (embeddable widget), WhatsApp Business API, and escalation to human agents. Conversation history and analytics dashboard.', 'skills' => ['Python', 'OpenAI API', 'LangChain', 'FastAPI', 'Vue.js'], 'budget_min' => 35000, 'budget_max' => 70000, 'level' => 'expert', 'days' => 25, 'urgent' => true, 'featured' => false],

            // Marketing
            ['title' => 'Technical SEO Audit & 6-Month Growth Strategy', 'category' => 'marketing', 'description' => 'Our e-commerce site has dropped 40% in organic traffic. Need a technical SEO expert to audit all 2,000+ pages, fix crawl errors, Core Web Vitals, structured data, internal linking, and create a 6-month content and link building strategy.', 'skills' => ['SEO', 'Technical SEO', 'Ahrefs', 'Google Search Console', 'Screaming Frog'], 'budget_min' => 15000, 'budget_max' => 35000, 'level' => 'expert', 'days' => 20, 'urgent' => true, 'featured' => false],
            ['title' => 'Google Ads & Meta Ads Campaign Management — D2C Brand', 'category' => 'marketing', 'description' => 'Manage paid advertising for our D2C skincare brand. Monthly ad spend: ₹2L. Need campaign strategy, creative briefs, A/B testing, audience segmentation, weekly reporting, and target ROAS of 4x. 6-month engagement.', 'skills' => ['Google Ads', 'Meta Ads', 'Performance Marketing', 'Analytics', 'Copywriting'], 'budget_min' => 20000, 'budget_max' => 40000, 'level' => 'intermediate', 'days' => 180, 'urgent' => false, 'featured' => false],

            // DevOps
            ['title' => 'AWS Infrastructure Setup & Kubernetes Migration', 'category' => 'web_development', 'description' => 'Migrate our monolith Laravel app to AWS EKS with microservices. Set up: VPC, RDS, ElastiCache, S3, CloudFront, ALB, EKS cluster, Helm charts, GitHub Actions CI/CD, CloudWatch monitoring, and auto-scaling policies.', 'skills' => ['AWS', 'Kubernetes', 'Docker', 'Terraform', 'GitHub Actions', 'Laravel'], 'budget_min' => 50000, 'budget_max' => 100000, 'level' => 'expert', 'days' => 30, 'urgent' => false, 'featured' => false],

            // Writing
            ['title' => 'Technical Blog Content for SaaS Product — 20 Articles', 'category' => 'writing', 'description' => 'We need 20 high-quality technical blog posts (1500-2500 words each) targeting developers and CTOs. Topics: Laravel best practices, microservices, DevOps, cloud architecture. Must include code examples, proper SEO, and pass originality checks.', 'skills' => ['Technical Writing', 'SEO Content', 'Laravel', 'DevOps'], 'budget_min' => 15000, 'budget_max' => 30000, 'level' => 'intermediate', 'days' => 45, 'urgent' => false, 'featured' => false],
        ];

        $employerIds = array_slice($users, 0, 5);

        foreach ($projects as $i => $data) {
            $slug = Str::slug($data['title']) . '-' . Str::random(6);
            $publishedAt = Carbon::now()->subDays(rand(1, 30));
            $deadline = Carbon::now()->addDays(rand(10, 60));

            MarketplaceProject::create([
                'employer_id'             => $employerIds[array_rand($employerIds)],
                'title'                   => $data['title'],
                'slug'                    => $slug,
                'description'             => $data['description'],
                'requirements'            => 'Please share your portfolio and relevant experience. Include links to similar projects you have completed. We prefer candidates available for a 30-minute discovery call.',
                'deliverables'            => 'All source code with documentation, deployment guide, and 30 days post-launch support.',
                'project_type'            => 'fixed_price',
                'category'                => $data['category'],
                'skills_required'         => json_encode($data['skills']),
                'budget_min'              => $data['budget_min'],
                'budget_max'              => $data['budget_max'],
                'currency'                => 'INR',
                'experience_level'        => $data['level'],
                'estimated_duration_days' => $data['days'],
                'duration_type'           => 'weeks',
                'status'                  => 'open',
                'is_featured'             => $data['featured'],
                'is_urgent'               => $data['urgent'],
                'allows_remote'           => true,
                'proposals_count'         => rand(0, 18),
                'views_count'             => rand(50, 800),
                'published_at'            => $publishedAt,
                'deadline'                => $deadline,
                'created_at'              => $publishedAt,
                'updated_at'              => $publishedAt,
            ]);
        }

        $this->command->info('✓ Seeded ' . count($projects) . ' marketplace projects and ' . count($freelancerData) . ' freelancer profiles.');
    }
}
