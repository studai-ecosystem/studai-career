<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$at = App\Models\RoundAttempt::where('user_id',24)->where('hiring_round_id',1)->first();
echo 'status=' . $at->status . ' qcount=' . count($at->questions ?? []) . PHP_EOL;
echo 'first=' . json_encode($at->questions[0]['question'] ?? null) . PHP_EOL;

// show last few laravel log lines mentioning AI
$log = __DIR__ . '/storage/logs/laravel.log';
if (is_file($log)) {
    $lines = array_slice(file($log), -40);
    foreach ($lines as $l) {
        if (stripos($l,'AI ') !== false || stripos($l,'fallback') !== false || stripos($l,'question') !== false) echo 'LOG: ' . trim($l) . PHP_EOL;
    }
}
