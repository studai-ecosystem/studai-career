<?php
// Test welcome emails — run: php test-welcome-mail.php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Mail\StudentWelcomeMail;
use App\Mail\CompanyWelcomeMail;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;

// Find an existing student user to test with
$student = User::where('account_type', 'job_seeker')->first();
if ($student) {
    try {
        Mail::to('onestudai@gmail.com')->send(new StudentWelcomeMail($student));
        echo "✅ Student welcome mail sent (test for: {$student->name})\n";
    } catch (\Exception $e) {
        echo "❌ Student mail failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️  No job_seeker user found to test with\n";
}

// Find an existing employer user
$employer = User::where('account_type', 'employer')->with('company')->first();
if ($employer && $employer->company) {
    try {
        Mail::to('onestudai@gmail.com')->send(new CompanyWelcomeMail($employer, $employer->company));
        echo "✅ Company welcome mail sent (test for: {$employer->company->name})\n";
    } catch (\Exception $e) {
        echo "❌ Company mail failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️  No employer user with company found to test with\n";
}
