<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Application;
use App\Models\RoundAttempt;
use Illuminate\Support\Facades\DB;

$userId = 24; $jobId = 17;
$cols = DB::getSchemaBuilder()->getColumnListing('applications');
echo "applications cols: " . implode(',', $cols) . "\n";

$existing = Application::where('user_id',$userId)->where('job_id',$jobId)->first();
if (!$existing) {
    $template = Application::where('job_id',$jobId)->first();
    $a = $template->replicate();
    $a->user_id = $userId;
    $a->application_number = 'APP-' . strtoupper(uniqid());
    $a->save();
    echo "Created application id={$a->id} status={$a->status}\n";
} else { echo "Application exists id={$existing->id}\n"; }

// clear any prior attempt so we hit the fresh generate path
RoundAttempt::where('user_id',$userId)->delete();
echo "Cleared attempts for user {$userId}\n";
