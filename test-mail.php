<?php
// Quick SMTP test — run: php test-mail.php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Hello from StudAI Hire — SMTP test at ' . date('Y-m-d H:i:s'), function ($m) {
        $m->to('onestudai@gmail.com')->subject('StudAI SMTP Test');
    });
    echo "✅ Mail sent successfully!\n";
} catch (\Exception $e) {
    echo "❌ Mail failed: " . $e->getMessage() . "\n";
}
