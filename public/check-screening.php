<?php
/**
 * Screening table existence check — production verification
 * Access: /check-screening.php?token=studai-debug-2026
 */
if (($_GET['token'] ?? '') !== 'studai-debug-2026') {
    http_response_code(403); die('Forbidden');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$screeningTables = [
    'question_banks',
    'evaluation_sessions',
    'evaluation_answers',
    'hiring_tests',
    'hiring_test_attempts',
    'hiring_rounds',
    'interviews',
    'interview_panelists',
    'interview_panel_scores',
    'background_checks',
    'background_check_packages',
    'video_interview_sessions',
    'video_interview_recordings',
    'applications',
    'job_listings',
    'interview_sessions',
];

$results = [];
foreach ($screeningTables as $table) {
    try {
        $exists = Schema::hasTable($table);
        $count  = $exists ? DB::table($table)->count() : null;
        $results[$table] = [
            'exists' => $exists,
            'rows'   => $count,
            'status' => $exists ? '✅' : '❌ MISSING',
        ];
    } catch (\Throwable $e) {
        $results[$table] = ['exists' => false, 'rows' => null, 'status' => '❌ ERROR: ' . $e->getMessage()];
    }
}

header('Content-Type: text/plain');
echo "=== SCREENING TABLE VERIFICATION — " . date('Y-m-d H:i:s') . " ===\n\n";
echo str_pad('TABLE', 35) . str_pad('EXISTS', 10) . str_pad('ROWS', 10) . "STATUS\n";
echo str_repeat('-', 75) . "\n";
foreach ($results as $table => $info) {
    echo str_pad($table, 35)
       . str_pad($info['exists'] ? 'YES' : 'NO', 10)
       . str_pad($info['rows'] ?? '-', 10)
       . $info['status'] . "\n";
}
$missing = array_filter($results, fn($r) => !$r['exists']);
echo "\n";
echo count($missing) === 0
    ? "✅ ALL SCREENING TABLES EXIST IN PRODUCTION\n"
    : "❌ MISSING TABLES: " . implode(', ', array_keys($missing)) . "\n";
