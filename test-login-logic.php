<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        die("No user found\n");
    }

    echo "Attempting login for: {$user->email}\n";
    
    // Attempt authentication (simulating Auth::attempt but without hashing check for now)
    Auth::login($user);
    
    echo "Login successful!\n";
    echo "Authenticated User ID: " . Auth::id() . "\n";
    
    // Try to regenerate session (this often fails if there's a session issue)
    session()->regenerate();
    echo "Session regenerated!\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
