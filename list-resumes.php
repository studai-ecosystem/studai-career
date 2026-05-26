<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = App\Models\Resume::select('id','user_id','full_name','title','ats_score')->get();
foreach ($rows as $r) {
    echo $r->id . ' | user:' . $r->user_id . ' | ' . ($r->full_name ?? '-') . ' | ' . ($r->title ?? '-') . ' | score:' . ($r->ats_score ?? 'none') . PHP_EOL;
}
echo PHP_EOL . 'Total: ' . $rows->count() . ' resumes' . PHP_EOL;
