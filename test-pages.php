<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$errors = [];
$ok = [];

function tryPage($label, $fn) {
    global $errors, $ok;
    try {
        $fn();
        $ok[] = "  OK  $label";
    } catch (Throwable $e) {
        $errors[] = " FAIL $label\n       " . get_class($e) . ': ' . $e->getMessage();
    }
}

// Student (Priya Patel - id 10)
$student = User::find(10);
Auth::setUser($student);

tryPage('Student: getRemainingApplications', function() use ($student) { $student->getRemainingApplications(); });
tryPage('Student: getRemainingAICredits', function() use ($student) { $student->getRemainingAICredits(); });
tryPage('Student: canApplyToJobs', function() use ($student) { $student->canApplyToJobs(); });
tryPage('Student: subscription load', function() use ($student) {
    $sub = $student->subscription;
    if (!$sub) throw new RuntimeException('No subscription');
    $sub->subscriptionPlan;
});
tryPage('Student: payment transactions', function() use ($student) {
    $sub = $student->subscription;
    if ($sub) $student->paymentTransactions()->where('user_subscription_id', $sub->id)->latest()->take(10)->get();
});
tryPage('Student: applications with relations', function() use ($student) {
    $student->applications()->with(['job.company'])->latest()->take(10)->get();
});
tryPage('Student: saved jobs count', function() use ($student) { $student->savedJobs()->count(); });
tryPage('Student: profile load', function() use ($student) { $student->profile; });

// Employer (id 2)
$employer = User::find(2);
Auth::setUser($employer);

tryPage('Employer: company load', function() use ($employer) {
    if (!$employer->company_id) throw new RuntimeException('No company_id on employer');
    $employer->company;
});
tryPage('Employer: company jobs', function() use ($employer) {
    if ($employer->company) $employer->company->jobs()->latest()->take(10)->get();
});
tryPage('Employer: applications list', function() use ($employer) {
    if ($employer->company) {
        App\Models\Application::whereHas('job', function($q) use ($employer) {
            $q->where('company_id', $employer->company->id);
        })->with(['user', 'job'])->latest()->take(10)->get();
    }
});

// DB column/table checks
$columns = [
    'payment_transactions.deleted_at' => 'SELECT deleted_at FROM payment_transactions LIMIT 1',
    'user_subscriptions.starts_at'    => 'SELECT starts_at FROM user_subscriptions LIMIT 1',
    'users.account_type'              => 'SELECT account_type FROM users LIMIT 1',
    'profiles table'                  => 'SELECT id FROM profiles LIMIT 1',
    'jobs table'                      => 'SELECT id FROM jobs LIMIT 1',
    'applications table'              => 'SELECT id FROM applications LIMIT 1',
    'companies table'                 => 'SELECT id FROM companies LIMIT 1',
    'subscription_plans table'        => 'SELECT id FROM subscription_plans LIMIT 1',
    'skill_analyses table'            => 'SELECT id FROM skill_analyses LIMIT 1',
    'negotiation_sessions table'      => 'SELECT id FROM negotiation_sessions LIMIT 1',
    'career_coach_sessions table'     => 'SELECT id FROM career_coach_sessions LIMIT 1',
    'resume_templates table'          => 'SELECT id FROM resume_templates LIMIT 1',
    'notifications table'             => 'SELECT id FROM notifications LIMIT 1',
    'resumes table'                   => 'SELECT id FROM resumes LIMIT 1',
    'job_alerts table'                => 'SELECT id FROM job_alerts LIMIT 1',
];

foreach ($columns as $label => $sql) {
    tryPage("DB: $label", function() use ($sql) { DB::select($sql); });
}

// Output
echo "\n=== FEATURE TEST RESULTS ===\n";
echo "\nPASSED (" . count($ok) . "):\n";
foreach ($ok as $m) echo $m . "\n";
if ($errors) {
    echo "\nFAILED (" . count($errors) . "):\n";
    foreach ($errors as $m) echo $m . "\n";
} else {
    echo "\nAll checks passed!\n";
}
echo "\n";
