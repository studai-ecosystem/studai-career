<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Default cache driver: " . config('cache.default') . "\n";
    
    \Cache::put('test_cache_key', 'test_cache_value', 60);
    $val = \Cache::get('test_cache_key');
    echo "Cache write/read: " . ($val === 'test_cache_value' ? 'SUCCESS' : 'FAILURE') . "\n";

    echo "Table 'cache' check:\n";
    $count = \DB::table('cache')->count();
    echo "Cache table count: $count\n";

} catch (\Exception $e) {
    echo "Cache Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
