<?php
/**
 * Diagnostic: Simulate Filament job create to find 500 error cause
 * Run: php diag-job-create.php
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Job;
use App\Models\Company;
use App\Models\User;

echo "=== Filament Job Create Diagnostics ===\n\n";

// 1. Check DB columns
$columns = Illuminate\Support\Facades\Schema::getColumnListing('job_listings');
echo "DB Columns (" . count($columns) . "):\n";
echo implode(', ', $columns) . "\n\n";

// 2. Check status ENUM values
try {
    $pdo = Illuminate\Support\Facades\DB::getPdo();
    $stmt = $pdo->query("SHOW COLUMNS FROM job_listings WHERE Field = 'status'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Status column type: " . ($row['Type'] ?? 'unknown') . "\n";
    echo "Status default: " . ($row['Default'] ?? 'none') . "\n\n";
} catch (Exception $e) {
    echo "Error checking status: " . $e->getMessage() . "\n\n";
}

// 3. Try to simulate the exact form data that Filament would submit
$admin = User::where('email', 'admin@studai.com')->first();
$company = Company::first();

if (!$admin || !$company) {
    echo "ERROR: Admin user or company not found!\n";
    exit(1);
}

$formData = [
    'company_id'       => $company->id,
    'posted_by'        => $admin->id,
    'title'            => 'Diagnostic Test Job ' . time(),
    'slug'             => 'diag-test-job-' . time(),
    'employment_type'  => 'full-time',
    'experience_level' => 'senior',
    'work_mode'        => 'hybrid',
    'location'         => null,
    'target_hire_count'=> 1,
    'expires_at'       => null,
    'description'      => '<p>Test description for diagnostic purposes.</p>',
    'requirements'     => [
        ['requirement' => '6+ years of software engineering experience'],
        ['requirement' => 'Expert PHP/Laravel and JavaScript skills'],
        ['requirement' => 'Experience with cloud platforms (AWS/Azure)'],
    ],
    'responsibilities' => [
        ['responsibility' => 'Design and implement scalable backend services'],
        ['responsibility' => 'Lead code reviews and mentor junior engineers'],
        ['responsibility' => 'Collaborate with product and design teams on features'],
    ],
    'nice_to_have'     => [],
    'salary_min'       => null,
    'salary_max'       => null,
    'salary_currency'  => 'INR',
    'benefits'         => [],
    'required_skills'  => [],
    'ai_insights'      => [],
    'quality_score'    => null,
    'status'           => 'draft',
    'is_featured'      => false,
    'is_urgent'        => false,
];

echo "Attempting Job::create() with form data...\n";
echo "Data keys: " . implode(', ', array_keys($formData)) . "\n\n";

try {
    $job = Job::create($formData);
    echo "✅ SUCCESS! Job created with ID: " . $job->id . "\n";
    echo "   title: " . $job->title . "\n";
    echo "   requirements: " . json_encode($job->requirements) . "\n";
    echo "   status: " . $job->status . "\n";
    
    // Clean up
    $job->forceDelete();
    echo "   (cleaned up)\n";
} catch (\Illuminate\Database\QueryException $e) {
    echo "❌ DB QueryException: " . $e->getMessage() . "\n";
    echo "   SQL: " . ($e->getSql() ?? 'N/A') . "\n";
} catch (\Illuminate\Database\Eloquent\MassAssignmentException $e) {
    echo "❌ MassAssignmentException: " . $e->getMessage() . "\n";
    echo "   Add to \$fillable: check Job model\n";
} catch (\Exception $e) {
    echo "❌ Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n" . substr($e->getTraceAsString(), 0, 1000) . "\n";
}

echo "\n=== Checking Last Laravel Log Entry ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = array_filter(explode("\n", file_get_contents($logFile)));
    $lastLines = array_slice($lines, -30);
    echo implode("\n", $lastLines) . "\n";
} else {
    echo "Log file not found at: $logFile\n";
}
