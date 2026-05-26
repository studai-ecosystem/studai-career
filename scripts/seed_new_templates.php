<?php

use Illuminate\Support\Facades\DB;

$new = [
    [
        'name' => 'Corporate Navy',
        'slug' => 'corporate-navy',
        'description' => 'Authoritative navy design for banking, consulting, and corporate roles',
        'category' => 'professional',
        'industry' => 'finance',
        'color_scheme' => json_encode(['primary' => '#1e3a5f', 'secondary' => '#2c5282', 'accent' => '#3182ce']),
        'layout_config' => json_encode(['columns' => 1, 'sections' => ['summary', 'experience', 'education', 'skills', 'certifications'], 'spacing' => 'comfortable']),
        'is_ats_friendly' => 1,
        'is_premium' => 0,
        'popularity_score' => 88,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Clean Slate',
        'slug' => 'clean-slate',
        'description' => 'Ultra-clean single-column layout optimised for ATS parsing accuracy',
        'category' => 'minimalist',
        'industry' => 'general',
        'color_scheme' => json_encode(['primary' => '#374151', 'secondary' => '#6b7280', 'accent' => '#9ca3af']),
        'layout_config' => json_encode(['columns' => 1, 'sections' => ['summary', 'experience', 'education', 'skills'], 'spacing' => 'minimal']),
        'is_ats_friendly' => 1,
        'is_premium' => 0,
        'popularity_score' => 85,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Teal Tech',
        'slug' => 'teal-tech',
        'description' => 'Two-column modern layout for software engineers and data professionals',
        'category' => 'modern',
        'industry' => 'technology',
        'color_scheme' => json_encode(['primary' => '#0f766e', 'secondary' => '#0d9488', 'accent' => '#14b8a6']),
        'layout_config' => json_encode(['columns' => 2, 'sections' => ['summary', 'skills', 'experience', 'projects', 'education'], 'spacing' => 'compact']),
        'is_ats_friendly' => 1,
        'is_premium' => 0,
        'popularity_score' => 82,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Bold Professional',
        'slug' => 'bold-professional',
        'description' => 'High-impact typography with strong section headers for senior professionals',
        'category' => 'executive',
        'industry' => 'general',
        'color_scheme' => json_encode(['primary' => '#111827', 'secondary' => '#1f2937', 'accent' => '#4b5563']),
        'layout_config' => json_encode(['columns' => 1, 'sections' => ['summary', 'experience', 'achievements', 'education', 'skills'], 'spacing' => 'spacious']),
        'is_ats_friendly' => 1,
        'is_premium' => 0,
        'popularity_score' => 79,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

$added = 0;
foreach ($new as $template) {
    $exists = DB::table('resume_templates')->where('slug', $template['slug'])->exists();
    if (!$exists) {
        DB::table('resume_templates')->insert($template);
        $added++;
        echo "Added: {$template['name']}\n";
    } else {
        echo "Skipped (exists): {$template['name']}\n";
    }
}
echo "Done. Added {$added} new templates.\n";
