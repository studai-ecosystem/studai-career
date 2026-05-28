<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $count = \DB::table('sessions')->count();
    echo "Session count: $count\n";
    
    $userCount = \DB::table('users')->count();
    echo "User count: $userCount\n";
    
    // Check if we can write to session
    \Session::put('test_key', 'test_value');
    \Session::save();
    echo "Session write successful\n";
} catch (\Exception $e) {
    echo "DB/Session Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
