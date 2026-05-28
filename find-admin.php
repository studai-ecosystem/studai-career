<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = \App\Models\User::where('account_type', 'admin')->first();
if ($admin) {
    echo "Admin email: {$admin->email}\n";
} else {
    echo "No admin found\n";
}
