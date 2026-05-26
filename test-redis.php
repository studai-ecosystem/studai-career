<?php
require 'vendor/autoload.php';
echo "Testing Redis connection...\n";
try {
    $r = new Predis\Client(['host' => '127.0.0.1', 'port' => 6379, 'timeout' => 2]);
    $r->ping();
    echo "Redis OK\n";
} catch (Exception $e) {
    echo "Redis fail: " . $e->getMessage() . "\n";
}
echo "Done\n";
