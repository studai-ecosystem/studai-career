<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HiringRound;
use App\Models\Application;
use App\Models\User;

foreach (HiringRound::with('job')->get() as $r) {
    $app1 = Application::where('job_id', $r->job_id)->first();
    $u = $app1 ? User::find($app1->user_id) : null;
    echo "round={$r->id} job={$r->job_id} type={$r->type} appUser=" . ($u? "{$u->email}":'NONE') . " url=/jobs/{$r->job_id}/rounds/{$r->id}/test\n";
}
// jobseeker
$js = User::where('email','jobseeker@studai.com')->first();
echo "jobseeker id=" . ($js?->id ?? 'none') . "\n";
if ($js) {
    foreach (Application::where('user_id',$js->id)->get() as $a) {
        echo "  jobseeker applied job_id={$a->job_id}\n";
    }
}
