<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Resume;

/**
 * Comprehensive local ATS analysis — no AI/network required.
 * Mirrors the cake.me checker's five-category breakdown.
 */
class ATSAnalyzerService
{
    // ---------------------------------------------------------------
    // Common buzzwords & clichés to flag
    // ---------------------------------------------------------------
    private const BUZZWORDS = [
        'motivated','passionate','dynamic','hardworking','team player',
        'detail-oriented','go-getter','results-driven','self-starter',
        'synergy','leverage','innovative','proactive','visionary',
        'guru','ninja','rockstar','wizard','evangelist',
        'think outside the box','hit the ground running','value-add',
        'best-of-breed','bleeding edge','move the needle','deep dive',
        'circle back','take it to the next level','low-hanging fruit',
        'paradigm shift','bandwidth','agile mindset',
    ];

    // Strong action verbs that start good bullet points
    private const ACTION_VERBS = [
        'achieved','architected','automated','built','coached','created',
        'delivered','designed','developed','drove','engineered','established',
        'executed','generated','grew','implemented','improved','increased',
        'launched','led','managed','mentored','migrated','optimized',
        'oversaw','planned','produced','reduced','refactored','saved',
        'scaled','shipped','solved','spearheaded','streamlined','transformed',
    ];

    private const PASSIVE_PATTERNS = [
        '/\bwas responsible for\b/i',
        '/\bresponsible for\b/i',
        '/\bhelped (with|to)\b/i',
        '/\bassisted (with|in)\b/i',
        '/\bworked on\b/i',
        '/\binvolved in\b/i',
        '/\bparticipated in\b/i',
    ];

    // ---------------------------------------------------------------
    // Entry point
    // ---------------------------------------------------------------
    public function analyze(Resume $resume): array
    {
        $content  = $this->buildContent($resume);
        $skills   = $this->analyzeSkills($resume, $content);
        $format   = $this->analyzeFormat($resume, $content);
        $sections = $this->analyzeSections($resume);
        $style    = $this->analyzeStyle($resume, $content);
        $cont     = $this->analyzeContent($resume, $content);

        $score = $cont['score'] + $skills['score'] + $format['score']
               + $sections['score'] + $style['score'];
        $score = max(0, min(100, $score));

        $issues = array_merge(
            $cont['issues'], $skills['issues'],
            $format['issues'], $sections['issues'], $style['issues']
        );

        $recommendations = array_merge(
            $cont['recommendations'], $skills['recommendations'],
            $format['recommendations'], $sections['recommendations'],
            $style['recommendations']
        );

        $highlights = $this->buildHighlights($resume, $cont, $skills, $format, $sections);

        $label = $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : 'Needs Work');

        return [
            'score'           => $score,
            'label'           => $label,
            'summary'         => $this->buildSummary($score, $resume),
            'highlights'      => $highlights,
            'categories'      => [
                'content'  => $cont,
                'skills'   => $skills,
                'format'   => $format,
                'sections' => $sections,
                'style'    => $style,
            ],
            'issues'          => array_slice($issues, 0, 5),
            'recommendations' => array_slice($recommendations, 0, 5),
            'keyword_density' => 0,
            'readability_score' => 0,
        ];
    }

    // ---------------------------------------------------------------
    // Category: Content  (max 25)
    // ---------------------------------------------------------------
    private function analyzeContent(Resume $resume, string $full): array
    {
        $score = 0;
        $issues = [];
        $recs = [];
        $measurableSuggestions = [];
        $grammarIssues = [];

        // --- Measurable results (0-15) ---
        $metricPattern = '/\b(\d[\d,]*\s*(%|percent|million|billion|thousand|k\b|x\b|times|users|customers|hrs?|hours?|days?|weeks?|months?)|increased|decreased|reduced|improved|saved|generated|grew|boosted|cut|doubled|tripled)\b/i';
        preg_match_all($metricPattern, $full, $matches);
        $metricCount = count($matches[0]);

        $measurePass = $metricCount >= 3;
        if ($measurePass) {
            $score += 15;
        } else {
            $score += min(10, $metricCount * 3);
        }

        // Scan experience descriptions for sentences lacking metrics
        foreach ((array)($resume->experience ?? []) as $exp) {
            $desc = $exp['description'] ?? '';
            $sentences = preg_split('/\.\s+/', $desc);
            foreach (array_slice($sentences, 0, 4) as $s) {
                $s = trim($s);
                if (strlen($s) > 20 && !preg_match($metricPattern, $s)) {
                    $measurableSuggestions[] = $s;
                    if (count($measurableSuggestions) >= 3) break 2;
                }
            }
        }

        if (!$measurePass) {
            $recs[] = 'Add specific numbers, percentages, and metrics to quantify your achievements.';
        }

        // --- Professional summary quality (0-10) ---
        $summary = $resume->professional_summary ?? '';
        $wordCount = str_word_count($summary);
        if ($wordCount >= 40 && $wordCount <= 120) {
            $score += 10;
        } elseif ($wordCount > 0) {
            $score += 5;
            if ($wordCount < 40) $recs[] = 'Expand your professional summary to 40–120 words.';
            if ($wordCount > 120) $recs[] = 'Shorten your professional summary to under 120 words.';
        } else {
            $issues[] = 'Missing professional summary.';
            $recs[] = 'Add a 40–120 word professional summary highlighting your value proposition.';
        }

        // --- Grammar proxy: check for common issues ---
        if ($resume->location && preg_match('/,[a-z]/', $resume->location)) {
            $grammarIssues[] = "'{$resume->location}' — capitalise city name after comma.";
        }
        // Check for inconsistent capitalisation in job titles
        foreach ((array)($resume->experience ?? []) as $exp) {
            $pos = $exp['position'] ?? '';
            if ($pos && $pos !== ucwords(strtolower($pos)) && strtoupper($pos) !== $pos) {
                // Has mixed case that doesn't look intentional
            }
        }
        $grammarPass = count($grammarIssues) === 0;

        $label = $score >= 20 ? 'Excellent' : ($score >= 13 ? 'Good' : 'Needs Work');

        return [
            'score'  => $score,
            'max'    => 25,
            'label'  => $label,
            'issues' => $issues,
            'recommendations' => $recs,
            'measurable_results' => [
                'pass'        => $measurePass,
                'count'       => $metricCount,
                'suggestions' => $measurableSuggestions,
            ],
            'spelling_grammar' => [
                'pass'   => $grammarPass,
                'issues' => $grammarIssues,
            ],
        ];
    }

    // ---------------------------------------------------------------
    // Category: Skills  (max 20)
    // ---------------------------------------------------------------
    private function analyzeSkills(Resume $resume, string $full): array
    {
        $score = 0;
        $issues = [];
        $recs = [];

        $flat = $resume->flat_skills;
        $count = count($flat);

        // Hard skills (0-12)
        $hardSkillKeywords = [
            'python','java','javascript','typescript','php','laravel','react','node','sql','mysql',
            'postgresql','mongodb','docker','kubernetes','aws','azure','gcp','git','linux',
            'html','css','api','rest','graphql','ci/cd','devops','machine learning','data analysis',
            'excel','powerpoint','word','salesforce','sap','tableau','power bi',
        ];

        $softSkillKeywords = [
            'communication','leadership','teamwork','problem solving','analytical','project management',
            'time management','adaptability','critical thinking','collaboration','presentation',
            'negotiation','mentoring','coaching','strategic planning','customer service',
        ];

        $fullLower = strtolower($full);
        $hardFound = array_filter($hardSkillKeywords, fn($s) => str_contains($fullLower, $s));
        $softFound = array_filter($softSkillKeywords, fn($s) => str_contains($fullLower, $s));

        $hardCount = count($hardFound);
        $softCount = count($softFound);

        $hardPass = $hardCount >= 5;
        $softPass = $softCount >= 2;

        // Missing hard skills (common ones not found in the resume)
        $flatLower = array_map('strtolower', $flat);
        $missingHard = [];
        if ($count < 5) {
            $candidates = ['REST APIs', 'Version Control (Git)', 'Database Management'];
            foreach ($candidates as $candidate) {
                // Only include if not already present in flat skills (case-insensitive partial match)
                $candidateLower = strtolower($candidate);
                $alreadyHave = false;
                foreach ($flatLower as $fs) {
                    if (str_contains($fs, $candidateLower) || str_contains($candidateLower, $fs)) {
                        $alreadyHave = true;
                        break;
                    }
                }
                // Also check the full resume text
                if (!$alreadyHave && !str_contains($fullLower, $candidateLower)) {
                    $missingHard[] = $candidate;
                }
            }
        }

        $missingSoft = [];
        if ($softCount < 2) {
            $missingSoft = ['Communication', 'Team Collaboration', 'Problem Solving'];
        }

        if ($hardPass) $score += 12; else $score += min(9, $hardCount * 2);
        if ($softPass) $score += 8; else $score += min(6, $softCount * 2);

        if (!$hardPass) {
            $issues[] = 'Add more hard/technical skills — aim for at least 8.';
            $recs[] = 'List specific tools, languages, and technologies you use.';
        }
        if (!$softPass) {
            $recs[] = 'Include 3–5 soft skills relevant to your target role.';
        }
        if ($count < 5) {
            $issues[] = 'Skills section is sparse — add more keywords.';
        }

        $label = $score >= 16 ? 'Excellent' : ($score >= 10 ? 'Good' : 'Needs Work');

        return [
            'score'  => $score,
            'max'    => 20,
            'label'  => $label,
            'issues' => $issues,
            'recommendations' => $recs,
            'hard_skills' => [
                'pass'    => $hardPass,
                'count'   => $hardCount,
                'found'   => array_values(array_slice($hardFound, 0, 8)),
                'missing' => $missingHard,
            ],
            'soft_skills' => [
                'pass'    => $softPass,
                'count'   => $softCount,
                'found'   => array_values(array_slice($softFound, 0, 6)),
                'missing' => $missingSoft,
            ],
            'found' => array_slice($flat, 0, 10),
        ];
    }

    // ---------------------------------------------------------------
    // Category: Format  (max 20)
    // ---------------------------------------------------------------
    private function analyzeFormat(Resume $resume, string $full): array
    {
        $score = 0;
        $issues = [];
        $recs = [];

        // --- Date formatting (0-8) ---
        $allDates = [];
        foreach ((array)($resume->experience ?? []) as $exp) {
            if (!empty($exp['start_date'])) $allDates[] = $exp['start_date'];
            if (!empty($exp['end_date']) && $exp['end_date'] !== 'Present') $allDates[] = $exp['end_date'];
        }
        foreach ((array)($resume->education ?? []) as $edu) {
            if (!empty($edu['start_year'])) $allDates[] = $edu['start_year'];
            if (!empty($edu['end_year']))   $allDates[] = $edu['end_year'];
        }

        // ATS-friendly date formats: "Jan 2022", "01/2022", "2022", "January 2022"
        $friendlyPattern = '/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|January|February|March|April|June|July|August|September|October|November|December)\s+\d{4}$|^\d{4}$|^\d{2}\/\d{4}$/';
        $badDates = array_filter($allDates, fn($d) => !preg_match($friendlyPattern, $d));
        $datePass = count($badDates) === 0 && count($allDates) > 0;

        if ($datePass) {
            $score += 8;
            $dateIssue = null;
        } elseif (count($badDates) > 0) {
            $score += 3;
            $dateIssue = 'Use consistent ATS-friendly date format: "Jan 2022" or "2022". Found: ' . implode(', ', array_slice($badDates, 0, 2));
            $recs[] = $dateIssue;
        } else {
            $score += 4; // no dates at all
            $dateIssue = 'No dates found in experience or education sections.';
        }

        // --- Resume length (0-7) ---
        $wordCount = $resume->getWordCount();
        $lengthPass = $wordCount >= 200 && $wordCount <= 900;
        if ($lengthPass) {
            $score += 7;
            $lengthIssue = null;
        } elseif ($wordCount > 900) {
            $score += 3;
            $lengthIssue = "Resume is too long ({$wordCount} words). Keep under 900 words (1–2 pages).";
            $issues[] = $lengthIssue;
            $recs[] = 'Trim verbose descriptions. Focus on impact, not tasks.';
        } else {
            $score += 2;
            $lengthIssue = "Resume is too short ({$wordCount} words). Add more detail to experience and skills.";
            $recs[] = $lengthIssue;
        }

        // --- Bullet points (0-5) ---
        $bulletSuggestions = [];
        foreach ((array)($resume->experience ?? []) as $exp) {
            $desc = $exp['description'] ?? '';
            // Check if description is a wall of text (no line breaks or short sentences)
            if (strlen($desc) > 100 && substr_count($desc, "\n") < 2) {
                $bulletSuggestions[] = substr($desc, 0, 80) . '…';
            }
        }
        $bulletPass = count($bulletSuggestions) === 0;
        if ($bulletPass) {
            $score += 5;
        } else {
            $score += 2;
            $recs[] = 'Break long experience descriptions into 3–6 concise bullet points.';
        }

        $label = $score >= 16 ? 'Excellent' : ($score >= 10 ? 'Good' : 'Needs Work');

        return [
            'score'  => $score,
            'max'    => 20,
            'label'  => $label,
            'issues' => $issues,
            'recommendations' => $recs,
            'date_formatting' => ['pass' => $datePass, 'issue' => $dateIssue],
            'resume_length'   => ['pass' => $lengthPass, 'issue' => $lengthIssue, 'word_count' => $wordCount],
            'bullet_points'   => ['pass' => $bulletPass, 'suggestions' => $bulletSuggestions],
        ];
    }

    // ---------------------------------------------------------------
    // Category: Sections  (max 20)
    // ---------------------------------------------------------------
    private function analyzeSections(Resume $resume): array
    {
        $score = 0;
        $issues = [];
        $recs = [];
        $present = [];
        $missing = [];

        $checks = [
            'Contact Info'   => !empty($resume->email) && !empty($resume->phone),
            'Location'       => !empty($resume->location),
            'LinkedIn URL'   => !empty($resume->linkedin_url),
            'Summary'        => !empty($resume->professional_summary),
            'Experience'     => !empty($resume->experience),
            'Education'      => !empty($resume->education),
            'Skills'         => !empty($resume->flat_skills),
            'Certifications' => !empty($resume->certifications),
        ];

        $weights = [
            'Contact Info'   => 4,
            'Location'       => 2,
            'LinkedIn URL'   => 2,
            'Summary'        => 3,
            'Experience'     => 4,
            'Education'      => 3,
            'Skills'         => 2,
            'Certifications' => 0, // bonus, not penalised
        ];

        foreach ($checks as $section => $exists) {
            if ($exists) {
                $present[] = $section;
                $score += $weights[$section] ?? 0;
            } else {
                $missing[] = $section;
                if (($weights[$section] ?? 0) > 2) {
                    $issues[] = "Missing {$section} section.";
                    $recs[]   = "Add a {$section} section — ATS systems require it.";
                }
            }
        }

        $score = min(20, $score);
        $label = $score >= 16 ? 'Excellent' : ($score >= 10 ? 'Good' : 'Needs Work');

        return [
            'score'   => $score,
            'max'     => 20,
            'label'   => $label,
            'present' => $present,
            'missing' => $missing,
            'issues'  => $issues,
            'recommendations' => $recs,
        ];
    }

    // ---------------------------------------------------------------
    // Category: Style  (max 15)
    // ---------------------------------------------------------------
    private function analyzeStyle(Resume $resume, string $full): array
    {
        $score = 0;
        $issues = [];
        $recs = [];

        $fullLower = strtolower($full);

        // --- Passive voice / weak language (0-8) ---
        $passiveFound = [];
        foreach (self::PASSIVE_PATTERNS as $pattern) {
            if (preg_match($pattern, $full, $m)) {
                $passiveFound[] = $m[0];
            }
        }
        $voicePass = count($passiveFound) === 0;
        if ($voicePass) {
            $score += 8;
        } else {
            $score += max(0, 8 - count($passiveFound) * 2);
            $recs[] = 'Replace weak phrases like "responsible for" and "worked on" with strong action verbs.';
        }

        $voiceNote = $voicePass
            ? 'Great use of active voice and strong action verbs.'
            : 'Passive phrases detected. Use action verbs (e.g. "Built", "Delivered", "Led").';

        // --- Buzzwords / clichés (0-7) ---
        $foundBuzzwords = [];
        foreach (self::BUZZWORDS as $bw) {
            if (str_contains($fullLower, strtolower($bw))) {
                $foundBuzzwords[] = $bw;
            }
        }
        $buzzPass = count($foundBuzzwords) <= 1;
        if ($buzzPass) {
            $score += 7;
        } else {
            $score += max(0, 7 - (count($foundBuzzwords) - 1) * 2);
            $recs[] = 'Replace generic buzzwords with specific, quantified accomplishments.';
        }

        $label = $score >= 12 ? 'Excellent' : ($score >= 8 ? 'Good' : 'Needs Work');

        return [
            'score'   => $score,
            'max'     => 15,
            'label'   => $label,
            'issues'  => $issues,
            'recommendations' => $recs,
            'voice'   => [
                'pass'  => $voicePass,
                'note'  => $voiceNote,
                'found' => $passiveFound,
            ],
            'buzzwords' => [
                'pass'        => $buzzPass,
                'found'       => array_slice($foundBuzzwords, 0, 6),
                'suggestions' => $buzzPass ? [] : ['Use specific achievements instead.'],
            ],
        ];
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    private function buildContent(Resume $resume): string
    {
        $parts = [
            $resume->full_name ?? '',
            $resume->professional_summary ?? '',
        ];

        foreach ((array)($resume->experience ?? []) as $exp) {
            $parts[] = ($exp['position'] ?? '') . ' ' . ($exp['company'] ?? '');
            $parts[] = $exp['description'] ?? '';
            foreach ((array)($exp['achievements'] ?? []) as $a) {
                $parts[] = is_string($a) ? $a : '';
            }
        }

        foreach ((array)($resume->education ?? []) as $edu) {
            $parts[] = ($edu['degree'] ?? '') . ' ' . ($edu['institution'] ?? '');
        }

        $parts[] = implode(' ', $resume->flat_skills);

        foreach ((array)($resume->certifications ?? []) as $cert) {
            $parts[] = ($cert['name'] ?? '') . ' ' . ($cert['issuer'] ?? '');
        }

        foreach ((array)($resume->projects ?? []) as $proj) {
            $parts[] = ($proj['name'] ?? '') . ' ' . ($proj['description'] ?? '');
        }

        return implode(' ', array_filter($parts));
    }

    private function buildHighlights(Resume $resume, array $cont, array $skills, array $format, array $sections): array
    {
        $h = [];

        if (!empty($resume->experience)) {
            $exp = $resume->experience[0];
            $h[] = 'Has work experience: ' . ($exp['position'] ?? '') . ' at ' . ($exp['company'] ?? '');
        }
        if ($cont['measurable_results']['count'] >= 2) {
            $h[] = 'Good use of measurable results in experience descriptions';
        }
        if ($skills['hard_skills']['pass']) {
            $h[] = 'Strong technical skills coverage (' . $skills['hard_skills']['count'] . ' hard skills detected)';
        }
        if ($sections['score'] >= 16) {
            $h[] = 'All key resume sections are present';
        }
        if ($cont['score'] >= 20) {
            $h[] = 'Professional summary is well-written and appropriately sized';
        }

        return array_slice($h, 0, 3);
    }

    private function buildSummary(int $score, Resume $resume): string
    {
        $name = $resume->full_name ?? 'Your resume';
        if ($score >= 80) {
            return "{$name}'s resume is ATS-ready. Strong sections, good keyword coverage, and measurable achievements. A few minor tweaks could push it even higher.";
        }
        if ($score >= 60) {
            return "{$name}'s resume has a solid foundation. Focus on quantifying achievements and ensuring all key sections are present to improve your score.";
        }
        return "{$name}'s resume needs improvement before it will reliably pass ATS filters. Prioritise adding missing sections, quantifying achievements, and using strong action verbs.";
    }
}
