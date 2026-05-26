<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate a logged-in employer user (user_id=2, companay@gmail.com)
$request = \Illuminate\Http\Request::create(
    '/employer/applicants/24/pipeline-stage',
    'POST',
    ['stage' => 'company_info_test', 'stage_date' => '2026-05-20', 'stage_notes' => 'Test note'],
    [],
    [],
    ['HTTP_ACCEPT' => 'application/json', 'HTTP_X_CSRF_TOKEN' => 'test']
);

// Log in the employer user
$user = \App\Models\User::find(2);
\Illuminate\Support\Facades\Auth::login($user);
$request->setUserResolver(fn() => $user);

echo "User: {$user->name} | account_type: {$user->account_type} | company_id: {$user->company_id}\n";
echo "Company: " . ($user->company ? $user->company->name : 'NO COMPANY') . "\n\n";

// Test the controller method directly
try {
    $controller = new \App\Http\Controllers\Employer\ApplicantTrackingController();
    
    // Check if app belongs to this company
    $company = $user->company;
    echo "Company ID: " . ($company ? $company->id : 'NULL') . "\n";
    
    $app = \App\Models\Application::with(['user', 'job.company'])
        ->whereHas('job', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->find(24);
    
    echo "Application found: " . ($app ? "YES - Job: {$app->job->title}" : "NO - not in this company") . "\n";
    
    if ($app) {
        echo "User email: " . ($app->user->email ?? 'NO USER') . "\n";
        echo "Notification check - user has() 'notifications': ";
        try {
            $count = $app->user->notifications()->count();
            echo "YES (count: {$count})\n";
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
