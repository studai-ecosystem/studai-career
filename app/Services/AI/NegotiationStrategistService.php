<?php

namespace App\Services\AI;

use App\Models\NegotiationStrategy;
use App\Models\User;
use App\Traits\InteractsWithAI;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NegotiationStrategistService
{
    use InteractsWithAI;
    /**
     * Generate comprehensive negotiation strategy
     */
    public function generateStrategy(User $user, array $offerData): NegotiationStrategy
    {
        // Gather market research
        $marketData = $this->gatherMarketResearch(
            $offerData['role'],
            $offerData['location'],
            $offerData['years_experience']
        );

        // Calculate optimal negotiation range
        $negotiationRange = $this->calculateOptimalRange(
            $offerData['offered_salary'],
            $marketData,
            $offerData['current_salary'] ?? null
        );

        // Persist the calculated percentile back into marketData for DB storage
        $marketData['offered_percentile'] = $negotiationRange['offered_percentile'];

        // Analyze user's strongest negotiation points
        $strengthAnalysis = $this->analyzeNegotiationStrength($user, $offerData);

        // Get company intelligence
        $companyIntelligence = $this->getCompanyIntelligence($offerData['company_name']);

        // Determine timing and tactics
        $tacticalRecommendations = $this->determineTactics(
            $negotiationRange,
            $strengthAnalysis,
            $companyIntelligence
        );

        // Generate AI insights
        $aiInsights = $this->generateAiInsights(
            $user,
            $offerData,
            $marketData,
            $strengthAnalysis,
            $tacticalRecommendations
        );

        // Create strategy record
        $strategy = NegotiationStrategy::create([
            'user_id' => $user->id,
            'role' => $offerData['role'],
            'company_name' => $offerData['company_name'],
            'location' => $offerData['location'],
            'offered_salary' => $offerData['offered_salary'],
            'current_salary' => $offerData['current_salary'] ?? null,
            'years_experience' => $offerData['years_experience'],

            // Market data — AI-powered, role-specific
            'market_median'            => $marketData['median'],
            'market_percentile_25'     => $marketData['percentile_25'],
            'market_percentile_75'     => $marketData['percentile_75'],
            'market_percentile_90'     => $marketData['percentile_90'],
            'offered_salary_percentile'=> $negotiationRange['offered_percentile'],
            'company_salary_data'      => [
                'percentile_10'  => $marketData['percentile_10'] ?? null,
                'trend'          => $marketData['trend'] ?? 'stable',
                'yoy_change'     => $marketData['yoy_change'] ?? 0,
                'demand'         => $marketData['demand'] ?? 'medium',
                'ai_rationale'   => $marketData['ai_rationale'] ?? '',
                'source'         => $marketData['source'] ?? 'estimate',
                // 3-tier ranges with probabilities
                'conservative'      => $negotiationRange['conservative'],
                'competitive'       => $negotiationRange['competitive'],
                'aggressive'        => $negotiationRange['aggressive'],
                'prob_conservative' => $negotiationRange['prob_conservative'],
                'prob_competitive'  => $negotiationRange['prob_competitive'],
                'prob_aggressive'   => $negotiationRange['prob_aggressive'],
            ],

            // Negotiation range (primary — competitive tier)
            'optimal_ask'        => $negotiationRange['optimal'],
            'minimum_acceptable' => $negotiationRange['minimum'],
            'stretch_goal'       => $negotiationRange['stretch'],
            'confidence_score'   => $negotiationRange['confidence'],

            // Strength analysis
            'strongest_points'   => $strengthAnalysis['strengths'],
            'value_propositions' => $strengthAnalysis['value_props'],
            'risk_factors'       => $strengthAnalysis['risks'],

            // Tactical recommendations
            'recommended_timing'           => $tacticalRecommendations['timing'],
            'timing_rationale'             => $tacticalRecommendations['timing_rationale'],
            'recommended_tone'             => $tacticalRecommendations['tone'],
            'recommended_tactics'          => $tacticalRecommendations['tactics'],
            'benefits_to_negotiate'        => $tacticalRecommendations['alternative_benefits'],
            'total_comp_optimization'      => $tacticalRecommendations['total_comp'],

            // Company intelligence
            'company_culture_analysis'        => $companyIntelligence['culture'],
            'hiring_manager_perspective'      => $companyIntelligence['manager_perspective'],
            'company_negotiation_flexibility' => $companyIntelligence['flexibility'],

            // AI insights
            'ai_summary'   => $aiInsights['summary'],
            'ai_rationale' => $aiInsights['rationale'],
            'ai_warnings'  => $aiInsights['warnings'],

            'status'       => 'active',
            'generated_at' => now(),
        ]);

        return $strategy;
    }

    /**
     * Gather AI-powered, role-specific market research with realistic salary distribution.
     */
    protected function gatherMarketResearch(string $role, string $location, int $years_experience): array
    {
        $cacheKey = 'market_research_v3_' . md5("{$role}_{$location}_{$years_experience}");

        return Cache::remember($cacheKey, 86400, function () use ($role, $location, $years_experience) {
            $level = $this->mapExperienceLevel($years_experience);

            try {
                $prompt = "You are a compensation intelligence analyst with deep knowledge of the Indian job market (2024-2025).\n\n"
                    . "Role: {$role}\nLocation: {$location}\nExperience: {$years_experience} years ({$level} level)\n\n"
                    . "Provide realistic salary benchmark data in LPA (Lakhs Per Annum) for this SPECIFIC role and city.\n"
                    . "Consider: role seniority, specialization, city cost-of-living, industry demand, and supply.\n"
                    . "Do NOT use generic fixed ratios — use realistic market knowledge.\n\n"
                    . "Return ONLY valid JSON:\n"
                    . '{"p10":NUMBER,"p25":NUMBER,"median":NUMBER,"p75":NUMBER,"p90":NUMBER,'
                    . '"trend":"growing|stable|declining","yoy_change":NUMBER,"demand":"high|medium|low",'
                    . '"rationale":"1 sentence explaining the range based on market reality"}';

                $data = $this->aiJSON($prompt, 'Return only valid JSON. All salary values in LPA (Indian Lakhs Per Annum). Be precise and realistic per the specific role.');

                $median = (float) ($data['median'] ?? 0);
                if ($median < 2 || $median > 500) {
                    throw new \RuntimeException('AI returned implausible median: ' . $median);
                }

                return [
                    'median'            => round($median, 2),
                    'percentile_10'     => round((float) ($data['p10'] ?? $median * 0.75), 2),
                    'percentile_25'     => round((float) ($data['p25'] ?? $median * 0.87), 2),
                    'percentile_75'     => round((float) ($data['p75'] ?? $median * 1.18), 2),
                    'percentile_90'     => round((float) ($data['p90'] ?? $median * 1.38), 2),
                    'offered_percentile'=> 0,
                    'trend'             => $data['trend'] ?? 'stable',
                    'yoy_change'        => (float) ($data['yoy_change'] ?? 0),
                    'demand'            => $data['demand'] ?? 'medium',
                    'ai_rationale'      => $data['rationale'] ?? '',
                    'source'            => 'ai_intelligence',
                ];
            } catch (\Exception $e) {
                Log::warning('AI market research failed, using calibrated fallback', ['error' => $e->getMessage()]);
                return $this->calibratedFallback($role, $location, $years_experience);
            }
        });
    }

    /**
     * Calibrated fallback with role-specific, experience-adjusted estimates.
     * Produces varied, realistic distributions (not fixed ratios).
     */
    protected function calibratedFallback(string $role, string $location, int $years): array
    {
        $base = $this->estimateBaseSalary($role, $location);

        // Experience multiplier — entry vs senior has different spread
        $expMultiplier = match (true) {
            $years <= 1  => 0.75,
            $years <= 3  => 0.90,
            $years <= 6  => 1.00,
            $years <= 10 => 1.18,
            default      => 1.35,
        };
        $median = round($base * $expMultiplier, 2);

        // Role-type affects spread width (niche roles have wider spreads)
        $roleLower = strtolower($role);
        $isNiche = str_contains($roleLower, 'ai') || str_contains($roleLower, 'ml') ||
                   str_contains($roleLower, 'architect') || str_contains($roleLower, 'principal') ||
                   str_contains($roleLower, 'data scientist');
        $spreadLow  = $isNiche ? 0.78 : 0.83;
        $spreadHigh = $isNiche ? 1.32 : 1.20;
        $spreadTop  = $isNiche ? 1.55 : 1.38;

        return [
            'median'            => $median,
            'percentile_10'     => round($median * 0.68, 2),
            'percentile_25'     => round($median * $spreadLow, 2),
            'percentile_75'     => round($median * $spreadHigh, 2),
            'percentile_90'     => round($median * $spreadTop, 2),
            'offered_percentile'=> 0,
            'trend'             => 'stable',
            'yoy_change'        => 0,
            'demand'            => 'medium',
            'ai_rationale'      => '',
            'source'            => 'calibrated_estimate',
        ];
    }

    /**
     * Calculate optimal negotiation range
     */
    /**
     * Calculate 3-tier negotiation range (conservative / competitive / aggressive)
     * with realistic success probabilities.
     */
    protected function calculateOptimalRange(float $offeredSalary, array $marketData, ?float $currentSalary): array
    {
        $p25   = $marketData['percentile_25'];
        $median = $marketData['median'];
        $p75   = $marketData['percentile_75'];
        $p90   = $marketData['percentile_90'];

        $offeredPercentile = $this->calculatePercentile($offeredSalary, $marketData);

        // --- 3-Tier Strategy ---
        // Conservative: achievable with low risk (~80%+ probability)
        // Competitive:  the real target (~60% probability)
        // Aggressive:   stretch, requires strong leverage (~35% probability)

        if ($offeredPercentile < 25) {
            $conservative  = round($median * 0.95, 2);           // just under median
            $competitive   = round($p75, 2);                      // 75th pct
            $aggressive    = round($p90, 2);                      // 90th pct
            $probCons      = 88; $probComp = 68; $probAgg = 42;
            $confidence    = 90;
        } elseif ($offeredPercentile < 50) {
            $conservative  = round(max($median, $offeredSalary * 1.07), 2);
            $competitive   = round($p75, 2);
            $aggressive    = round($p90, 2);
            $probCons      = 82; $probComp = 62; $probAgg = 38;
            $confidence    = 82;
        } elseif ($offeredPercentile < 75) {
            $conservative  = round($offeredSalary * 1.05, 2);
            $competitive   = round(max($p75, $offeredSalary * 1.12), 2);
            $aggressive    = round($p90, 2);
            $probCons      = 78; $probComp = 58; $probAgg = 32;
            $confidence    = 74;
        } else {
            $conservative  = round($offeredSalary * 1.03, 2);
            $competitive   = round($offeredSalary * 1.08, 2);
            $aggressive    = round(max($p90, $offeredSalary * 1.15), 2);
            $probCons      = 72; $probComp = 50; $probAgg = 28;
            $confidence    = 60;
        }

        // Adjust upward if switching jobs (current salary floor)
        if ($currentSalary && $currentSalary > 0) {
            $floor        = $currentSalary * 1.10;
            $conservative = max($conservative, $floor);
            $competitive  = max($competitive, $currentSalary * 1.18);
            $aggressive   = max($aggressive,  $currentSalary * 1.30);
        }

        // Ensure ordering
        $conservative = min($conservative, $competitive);
        $competitive  = min($competitive, $aggressive);
        $minimum      = round($conservative * 0.97, 2); // absolute walkaway

        return [
            'optimal'            => $competitive,           // kept for DB compatibility
            'minimum'            => $minimum,
            'stretch'            => $aggressive,
            'confidence'         => $confidence,
            'offered_percentile' => round($offeredPercentile, 2),
            // 3-tier
            'conservative'       => $conservative,
            'competitive'        => $competitive,
            'aggressive'         => $aggressive,
            'prob_conservative'  => $probCons,
            'prob_competitive'   => $probComp,
            'prob_aggressive'    => $probAgg,
        ];
    }

    /**
     * Calculate salary percentile
     */
    protected function calculatePercentile(float $salary, array $marketData): float
    {
        $p25 = $marketData['percentile_25'];
        $p75 = $marketData['percentile_75'];
        $p90 = $marketData['percentile_90'];
        $median = $marketData['median'];

        if ($salary <= $p25) {
            return ($salary / $p25) * 25;
        } elseif ($salary <= $median) {
            return 25 + (($salary - $p25) / ($median - $p25)) * 25;
        } elseif ($salary <= $p75) {
            return 50 + (($salary - $median) / ($p75 - $median)) * 25;
        } elseif ($salary <= $p90) {
            return 75 + (($salary - $p75) / ($p90 - $p75)) * 15;
        } else {
            return min(100, 90 + (($salary - $p90) / ($p90 * 0.1)) * 10);
        }
    }

    /**
     * Analyze user's negotiation strength
     */
    protected function analyzeNegotiationStrength(User $user, array $offerData): array
    {
        $strengths = [];
        $valueProps = [];
        $risks = [];

        $profile = $user->profile;

        // Experience-based strengths
        if ($offerData['years_experience'] >= 10) {
            $strengths[] = [
                'category' => 'experience',
                'point' => 'Extensive industry experience',
                'leverage' => 'high',
            ];
            $valueProps[] = 'Over 10 years of proven track record in the industry';
        } elseif ($offerData['years_experience'] >= 5) {
            $strengths[] = [
                'category' => 'experience',
                'point' => 'Solid mid-level experience',
                'leverage' => 'medium',
            ];
        }

        // Skills-based strengths
        if ($profile) {
            $skills = is_array($profile->skills) ? $profile->skills : json_decode($profile->skills ?? '[]', true);
            $hotSkills = $this->identifyHotSkills($skills);
            
            foreach ($hotSkills as $skill) {
                $strengths[] = [
                    'category' => 'skills',
                    'point' => "High-demand skill: {$skill['name']}",
                    'leverage' => 'high',
                ];
                $valueProps[] = "Expertise in {$skill['name']}, which is experiencing {$skill['growth']}% growth in demand";
            }
        }

        // Education-based strengths
        if ($profile && !empty($profile->education)) {
            $education = is_array($profile->education) ? $profile->education : json_decode($profile->education ?? '[]', true);
            
            foreach ($education as $edu) {
                if (isset($edu['degree']) && in_array($edu['degree'], ['Master', 'PhD', 'MBA'])) {
                    $strengths[] = [
                        'category' => 'education',
                        'point' => "Advanced degree: {$edu['degree']}",
                        'leverage' => 'medium',
                    ];
                }
            }
        }

        // Current employment status
        if (isset($offerData['current_salary']) && $offerData['current_salary'] > 0) {
            $strengths[] = [
                'category' => 'alternatives',
                'point' => 'Currently employed with competitive salary',
                'leverage' => 'high',
            ];
            $valueProps[] = 'Leaving a stable position, bringing proven performance';
        } else {
            $risks[] = [
                'category' => 'alternatives',
                'factor' => 'Currently seeking employment',
                'impact' => 'May reduce negotiation leverage',
            ];
        }

        // Market conditions
        $roleInDemand = $this->isRoleInDemand($offerData['role']);
        if ($roleInDemand) {
            $strengths[] = [
                'category' => 'market',
                'point' => 'Role is in high demand',
                'leverage' => 'medium',
            ];
        }

        return [
            'strengths' => $strengths,
            'value_props' => $valueProps,
            'risks' => $risks,
        ];
    }

    /**
     * Get company intelligence
     */
    protected function getCompanyIntelligence(string $companyName): array
    {
        $cacheKey = "company_intelligence_" . md5($companyName);

        return Cache::remember($cacheKey, 86400, function () use ($companyName) {
            // In production, this would integrate with company databases, Glassdoor, etc.
            // For now, we'll use AI to generate insights
            
            try {
                $analysis = $this->ai(
                    "Analyze {$companyName}'s typical salary negotiation flexibility and company culture. Keep response under 200 words with specific, actionable insights.",
                    'You are a company culture and negotiation analyst. Provide brief, actionable insights about company negotiation flexibility and culture.',
                    ['temperature' => 0.7]
                );

                // Parse flexibility level
                $flexibility = 'medium'; // default
                if (stripos($analysis, 'highly flexible') !== false || stripos($analysis, 'very flexible') !== false) {
                    $flexibility = 'high';
                } elseif (stripos($analysis, 'not flexible') !== false || stripos($analysis, 'rigid') !== false) {
                    $flexibility = 'low';
                }

                return [
                    'culture' => [
                        'analysis' => $analysis,
                        'key_values' => $this->extractKeyValues($analysis),
                    ],
                    'manager_perspective' => "Based on {$companyName}'s culture, the hiring manager is likely focused on finding the right fit and may have some flexibility within their approved budget range.",
                    'flexibility' => $flexibility,
                ];
            } catch (\Exception $e) {
                Log::error('Company intelligence generation failed', [
                    'company' => $companyName,
                    'error' => $e->getMessage()
                ]);

                return $this->getFallbackCompanyIntelligence();
            }
        });
    }

    /**
     * Determine negotiation tactics
     */
    protected function determineTactics(array $negotiationRange, array $strengthAnalysis, array $companyIntelligence): array
    {
        $tactics = [];
        $alternativeBenefits = [];
        
        // Determine timing based on confidence and offer strength
        $confidence = $negotiationRange['confidence'];
        if ($confidence >= 80) {
            $timing = 'within_24h';
            $timingRationale = 'Strong negotiation position allows for quick response while maintaining enthusiasm.';
        } elseif ($confidence >= 60) {
            $timing = 'within_48h';
            $timingRationale = 'Take time to gather market data and prepare compelling justification.';
        } else {
            $timing = 'within_week';
            $timingRationale = 'Carefully consider all factors and potentially seek alternative offers.';
        }

        // Determine tone based on company culture
        $flexibility = $companyIntelligence['flexibility'];
        if ($flexibility === 'high') {
            $tone = 'collaborative';
            $tactics[] = 'collaborative_problem_solving';
            $tactics[] = 'value_demonstration';
        } elseif ($flexibility === 'low') {
            $tone = 'professional';
            $tactics[] = 'data_driven_justification';
            $tactics[] = 'alternative_benefits_focus';
            
            // Add alternative benefits for inflexible companies
            $alternativeBenefits = [
                'sign_on_bonus' => 'One-time signing bonus',
                'equity' => 'Stock options or RSUs',
                'performance_bonus' => 'Higher performance bonus percentage',
                'additional_pto' => 'Additional vacation days',
                'professional_development' => 'Learning and development budget',
                'remote_work' => 'Remote work flexibility',
            ];
        } else {
            $tone = 'confident';
            $tactics[] = 'market_anchoring';
            $tactics[] = 'skills_leverage';
        }

        // Add tactics based on strengths
        $strengthCount = count($strengthAnalysis['strengths']);
        if ($strengthCount >= 5) {
            $tactics[] = 'multiple_value_points';
        }

        if (count($strengthAnalysis['value_props']) > 0) {
            $tactics[] = 'unique_value_proposition';
        }

        // Total compensation optimization
        $totalComp = [
            'base_salary' => $negotiationRange['optimal'],
            'target_bonus' => 15, // percentage
            'equity_value' => 0,
            'benefits_value' => 0,
        ];

        return [
            'timing' => $timing,
            'timing_rationale' => $timingRationale,
            'tone' => $tone,
            'tactics' => $tactics,
            'alternative_benefits' => $alternativeBenefits,
            'total_comp' => $totalComp,
        ];
    }

    /**
     * Generate AI-powered insights
     */
    protected function generateAiInsights(User $user, array $offerData, array $marketData, array $strengthAnalysis, array $tacticalRecommendations): array
    {
        // Use instant computed insights to avoid AI timeout on strategy creation.
        // Rich AI insights can be fetched on-demand from the strategy detail page.
        return $this->buildComputedInsights($offerData, $marketData, $strengthAnalysis, $tacticalRecommendations);
    }

    /**
     * Build deterministic insights from computed data — no AI call, instant response.
     */
    protected function buildComputedInsights(array $offerData, array $marketData, array $strengthAnalysis, array $tacticalRecommendations): array
    {
        $percentile = round($marketData['offered_percentile']);
        $offered    = number_format($offerData['offered_salary']);
        $median     = number_format($marketData['median']);
        $role       = $offerData['role'];
        $company    = $offerData['company_name'];

        if ($percentile < 40) {
            $summary   = "The offer of ₹{$offered} LPA for {$role} at {$company} is below the market median (₹{$median} LPA, {$percentile}th percentile). You have strong leverage to negotiate upward.";
            $rationale = "At the {$percentile}th percentile, this offer leaves significant room for negotiation. Market data supports requesting a salary closer to the 50th–75th percentile range.";
            $warnings  = ['Accepting below-median salary can set a lower baseline for future raises.'];
        } elseif ($percentile < 65) {
            $summary   = "The offer of ₹{$offered} LPA is near the market median for {$role} (₹{$median} LPA, {$percentile}th percentile). Negotiation is viable — aim for the 65th–80th percentile.";
            $rationale = "A competitive but not exceptional offer. Highlighting your experience and unique skills can justify a 5–15% increase without risking the offer.";
            $warnings  = [];
        } else {
            $summary   = "The offer of ₹{$offered} LPA is above market median for {$role} (₹{$median} LPA, {$percentile}th percentile). Focus negotiation on non-salary benefits and growth opportunities.";
            $rationale = "This is a strong offer. Negotiating total compensation — remote flexibility, equity, learning budget, or early performance reviews — may yield more value than pushing salary.";
            $warnings  = ['Aggressive salary pushback on an above-market offer may signal misalignment.'];
        }

        // Add strength-based context
        if (!empty($strengthAnalysis['strengths'])) {
            $topStrength = $strengthAnalysis['strengths'][0]['point'] ?? null;
            if ($topStrength) {
                $rationale .= " Key leverage point: {$topStrength}.";
            }
        }

        // Timing advice
        if (!empty($tacticalRecommendations['timing'])) {
            $rationale .= " Recommended timing: {$tacticalRecommendations['timing']}.";
        }

        return [
            'summary'  => $summary,
            'rationale' => $rationale,
            'warnings' => $warnings,
        ];
    }

    /**
     * Build prompt for AI insights
     */
    protected function buildInsightsPrompt(User $user, array $offerData, array $marketData, array $strengthAnalysis): string
    {
        $prompt = "Analyze this job offer and provide strategic negotiation advice:\n\n";
        $prompt .= "**Job Offer:**\n";
        $prompt .= "- Role: {$offerData['role']}\n";
        $prompt .= "- Company: {$offerData['company_name']}\n";
        $prompt .= "- Offered Salary: $" . number_format($offerData['offered_salary']) . "\n";
        $prompt .= "- Market Median: $" . number_format($marketData['median']) . "\n";
        $prompt .= "- Offer Percentile: " . round($marketData['offered_percentile']) . "th\n";
        $prompt .= "- Experience: {$offerData['years_experience']} years\n\n";
        
        if (!empty($strengthAnalysis['strengths'])) {
            $prompt .= "**Candidate Strengths:**\n";
            foreach (array_slice($strengthAnalysis['strengths'], 0, 3) as $strength) {
                $prompt .= "- {$strength['point']}\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "Provide:\n";
        $prompt .= "1. Executive summary (2-3 sentences) on negotiation viability\n";
        $prompt .= "2. Key strategic recommendations\n";
        $prompt .= "3. Potential risks or warnings\n\n";
        $prompt .= "Be specific and actionable. Focus on maximizing value while maintaining positive relationship.";

        return $prompt;
    }

    /**
     * Helper: Map years of experience to level
     */
    protected function mapExperienceLevel(int $years): string
    {
        if ($years < 2) return 'junior';
        if ($years < 5) return 'mid';
        if ($years < 10) return 'senior';
        return 'lead';
    }

    /**
     * Helper: Estimate base salary if no data available
     */
    protected function estimateBaseSalary(string $role, string $location): float
    {
        // Indian market salary estimates in LPA (Lakhs Per Annum)
        $baseEstimates = [
            'senior engineer'   => 35,
            'senior developer'  => 33,
            'lead engineer'     => 40,
            'lead developer'    => 38,
            'principal'         => 50,
            'staff engineer'    => 48,
            'engineering manager' => 55,
            'manager'           => 40,
            'engineer'          => 22,
            'developer'         => 20,
            'designer'          => 18,
            'analyst'           => 16,
            'data scientist'    => 25,
            'data engineer'     => 22,
            'devops'            => 22,
            'architect'         => 45,
            'product manager'   => 30,
            'scrum master'      => 18,
            'qa'                => 14,
            'tester'            => 12,
        ];

        $estimate = 18; // default LPA
        $roleLower = strtolower($role);
        foreach ($baseEstimates as $keyword => $value) {
            if (stripos($roleLower, $keyword) !== false) {
                $estimate = $value;
                break;
            }
        }

        // Indian city adjustments
        $locationLower = strtolower($location);
        if (str_contains($locationLower, 'bengaluru') || str_contains($locationLower, 'bangalore')) {
            $estimate *= 1.15;
        } elseif (str_contains($locationLower, 'mumbai') || str_contains($locationLower, 'pune')) {
            $estimate *= 1.10;
        } elseif (str_contains($locationLower, 'hyderabad') || str_contains($locationLower, 'chennai')) {
            $estimate *= 1.05;
        } elseif (str_contains($locationLower, 'delhi') || str_contains($locationLower, 'gurgaon') || str_contains($locationLower, 'noida')) {
            $estimate *= 1.08;
        }

        return round($estimate, 2);
    }

    /**
     * Helper: Identify hot skills
     */
    protected function identifyHotSkills(array $skills): array
    {
        $hotSkills = [];

        foreach ($skills as $skill) {
            $skillName = is_array($skill) ? ($skill['name'] ?? $skill) : $skill;
            
            // SkillTrend data no longer available
            $trend = null;

            if ($trend) {
                $hotSkills[] = [
                    'name' => $skillName,
                    'growth' => round((float) $trend->growth_rate, 1),
                ];
            }
        }

        return array_slice($hotSkills, 0, 3); // Top 3
    }

    /**
     * Helper: Check if role is in demand
     */
    protected function isRoleInDemand(string $role): bool
    {
        // Simplified check - in production would use more sophisticated logic
        return true; // Most roles have some demand
    }

    /**
     * Helper: Extract key values from text
     */
    protected function extractKeyValues(string $text): array
    {
        $values = [];
        
        if (stripos($text, 'innovation') !== false) $values[] = 'Innovation';
        if (stripos($text, 'collaboration') !== false) $values[] = 'Collaboration';
        if (stripos($text, 'growth') !== false) $values[] = 'Growth';
        if (stripos($text, 'data-driven') !== false) $values[] = 'Data-Driven';
        
        return $values;
    }

    /**
     * Helper: Extract warnings from AI response
     */
    protected function extractWarnings(string $text): array
    {
        $warnings = [];
        
        if (stripos($text, 'risk') !== false || stripos($text, 'warning') !== false) {
            preg_match_all('/(?:risk|warning|caution):?\s*([^.]+)/i', $text, $matches);
            $warnings = array_slice($matches[1] ?? [], 0, 3);
        }

        return $warnings;
    }

    /**
     * Fallback insights when AI unavailable
     */
    protected function getFallbackInsights(array $offerData, array $marketData): array
    {
        $percentile = $marketData['offered_percentile'];
        
        if ($percentile < 50) {
            $summary = "The offer is below market median. You have strong negotiation leverage to request a higher salary.";
            $rationale = "Market data shows this offer is at the {$percentile}th percentile. Requesting the market median or higher is well-justified.";
        } else {
            $summary = "The offer is competitive and at/above market median. Modest negotiation is still possible.";
            $rationale = "The offer is at the {$percentile}th percentile. Focus on demonstrating unique value to justify additional compensation.";
        }

        return [
            'summary' => $summary,
            'rationale' => $rationale,
            'warnings' => [],
        ];
    }

    /**
     * Fallback company intelligence
     */
    protected function getFallbackCompanyIntelligence(): array
    {
        return [
            'culture' => [
                'analysis' => 'Company culture analysis unavailable. Proceed with professional, data-driven approach.',
                'key_values' => ['Professionalism', 'Merit-based'],
            ],
            'manager_perspective' => 'Hiring manager likely has budget constraints but values finding the right candidate.',
            'flexibility' => 'medium',
        ];
    }
}
