<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Get started — manual job applications with basic AI features',
                'price' => 0.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'razorpay_plan_id' => null,
                'payu_plan_id' => null,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'ai_credits' => 10,              // 10 AI credits/month
                'applications_limit' => 10,      // 10 combined (agent + manual)
                'job_alerts_limit' => 5,
                'priority_support' => false,
                'api_access' => false,
                'api_calls_limit' => 0,
                'features' => [
                    'ai_resume_review' => false,
                    'ai_interview_prep' => false,
                    'ai_cover_letter' => true,    // basic cover letter (uses AI credit)
                    'one_click_apply' => false,
                    'advanced_search' => false,
                    'job_alerts' => true,
                    'ai_agent' => false,          // no autonomous AI agent
                    'profile_visibility' => 'basic',
                    'support_level' => 'community',
                    'recommended_for' => 'Exploring job search basics',
                ],
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Unlimited AI agent applications + 50 AI credits for power job seekers',
                'price' => 499.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'razorpay_plan_id' => null,
                'payu_plan_id' => null,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'ai_credits' => 50,              // 50 AI credits/month
                'applications_limit' => null,    // null = unlimited (agent + manual)
                'job_alerts_limit' => 100,
                'priority_support' => false,
                'api_access' => false,
                'api_calls_limit' => 0,
                'features' => [
                    'ai_resume_review' => true,
                    'ai_interview_prep' => true,
                    'ai_cover_letter' => true,
                    'one_click_apply' => true,
                    'advanced_search' => true,
                    'job_alerts' => true,
                    'ai_agent' => true,           // autonomous AI agent included
                    'profile_visibility' => 'enhanced',
                    'support_level' => 'email',
                    'savings_text' => 'Save 17% with annual billing',
                    'recommended_for' => 'Active job seekers who want AI to apply automatically',
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Unlimited applications + 200 AI credits + priority AI career tools',
                'price' => 999.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'razorpay_plan_id' => null,
                'payu_plan_id' => null,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'ai_credits' => 200,             // 200 AI credits/month
                'applications_limit' => null,    // null = unlimited
                'job_alerts_limit' => null,
                'priority_support' => true,
                'api_access' => true,
                'api_calls_limit' => 10000,
                'features' => [
                    'ai_resume_review' => true,
                    'ai_interview_prep' => true,
                    'ai_cover_letter' => true,
                    'one_click_apply' => true,
                    'advanced_search' => true,
                    'job_alerts' => true,
                    'ai_agent' => true,
                    'profile_visibility' => 'premium',
                    'support_level' => 'priority',
                    'ai_career_coaching' => true,
                    'ai_salary_insights' => true,
                    'ai_skill_gap_analysis' => true,
                    'resume_variants' => true,
                    'application_tracking' => 'advanced',
                    'savings_text' => 'Save 25% with annual billing',
                    'recommended_for' => 'Professionals seeking comprehensive AI career support',
                ],
            ],
            [
                'name' => 'Basic Annual',
                'slug' => 'basic-annual',
                'description' => 'All Basic features — unlimited applications + 600 AI credits/year',
                'price' => 4990.00,
                'currency' => 'INR',
                'billing_period' => 'yearly',
                'razorpay_plan_id' => null,
                'payu_plan_id' => null,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
                'ai_credits' => 600,             // 50 × 12 months
                'applications_limit' => null,    // unlimited
                'job_alerts_limit' => null,
                'priority_support' => false,
                'api_access' => false,
                'api_calls_limit' => 0,
                'features' => [
                    'ai_resume_review' => true,
                    'ai_interview_prep' => true,
                    'ai_cover_letter' => true,
                    'one_click_apply' => true,
                    'advanced_search' => true,
                    'job_alerts' => true,
                    'ai_agent' => true,
                    'profile_visibility' => 'enhanced',
                    'support_level' => 'email',
                    'savings_text' => 'Save ₹998 vs monthly billing',
                    'savings_percentage' => 17,
                    'recommended_for' => 'Job seekers committing to a full year of AI-powered search',
                ],
            ],
            [
                'name' => 'Pro Annual',
                'slug' => 'pro-annual',
                'description' => 'All Pro features with 25% savings on annual billing',
                'price' => 8990.00, // ₹999 × 12 - 25% = ₹8,990 (saves ₹2,998)
                'currency' => 'INR',
                'billing_period' => 'yearly',
                'razorpay_plan_id' => null,
                'payu_plan_id' => null,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'ai_credits' => 2400,            // 200 × 12 months
                'applications_limit' => null,    // unlimited
                'job_alerts_limit' => null,
                'priority_support' => true,
                'api_access' => true,
                'api_calls_limit' => 120000,
                'features' => [
                    'ai_resume_review' => true,
                    'ai_interview_prep' => true,
                    'ai_cover_letter' => true,
                    'one_click_apply' => true,
                    'advanced_search' => true,
                    'job_alerts' => true,
                    'ai_agent' => true,
                    'profile_visibility' => 'premium',
                    'support_level' => 'priority',
                    'ai_career_coaching' => true,
                    'ai_salary_insights' => true,
                    'ai_skill_gap_analysis' => true,
                    'resume_variants' => true,
                    'application_tracking' => 'advanced',
                    'savings_text' => 'Save ₹2,998 vs monthly billing',
                    'savings_percentage' => 25,
                    'recommended_for' => 'Professionals investing in long-term AI career growth',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
        $this->command->info('Created plans: Free, Basic, Pro, Basic Annual, Pro Annual');
    }
}
