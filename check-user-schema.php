<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'employer1@example.com')->first();
if ($user) {
    echo "USER: employer1@example.com\n";
    echo "VERIFY 'password': " . (\Hash::check('password', $user->password) ? 'MATCH' : 'NO MATCH') . "\n";
}
