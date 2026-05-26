<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== HIRING ROUNDS ===\n";
$rounds = DB::table('hiring_rounds')->get();
foreach ($rounds as $r) {
    echo "job_id={$r->job_id}  round={$r->round_order}  name={$r->name}  type={$r->type}\n";
}

echo "\n=== RECENT JOBS (last 5) ===\n";
$jobs = DB::table('job_listings')->orderByDesc('id')->limit(5)->get(['id', 'title']);
foreach ($jobs as $j) {
    echo "id={$j->id}  title={$j->title}\n";
}
