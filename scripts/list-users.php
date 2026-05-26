<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check what columns companies table has
$cols = DB::getSchemaBuilder()->getColumnListing('companies');
echo "Companies columns: " . implode(', ', $cols) . "\n\n";

$users = DB::table('users')->select('id', 'name', 'email')->orderByDesc('id')->limit(15)->get();
echo "=== ALL USERS ===\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | {$u->name} | {$u->email}\n";
}
