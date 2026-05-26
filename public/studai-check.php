<?php
// StudAI Production Diagnostic — auto-removed after debugging
// This file reveals which Laravel bootstrap step hangs in production
set_time_limit(12);
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain');
$t = microtime(true);
function ms() { global $t; return round((microtime(true) - $t) * 1000) . 'ms'; }

echo "[" . ms() . "] PHP OK - " . PHP_VERSION . "\n";

// Step 1: Autoloader
require __DIR__ . '/../vendor/autoload.php';
echo "[" . ms() . "] Autoloader OK\n";

// Step 2: ENV
try {
    $env = parse_ini_file(dirname(__DIR__) . '/.env');
} catch (Throwable $e) {
    $env = [];
}
// Fallback to environment variables (Azure App Settings)
$dbHost     = $env['DB_HOST']     ?? getenv('DB_HOST')     ?: '(not set)';
$dbPort     = $env['DB_PORT']     ?? getenv('DB_PORT')     ?: '3306';
$dbName     = $env['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '(not set)';
$dbUser     = $env['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: '(not set)';
$dbPass     = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '(not set)';
$appEnv     = $env['APP_ENV']     ?? getenv('APP_ENV')     ?: '(not set)';
$sessionDrv = $env['SESSION_DRIVER'] ?? getenv('SESSION_DRIVER') ?: 'database (default)';
$cacheDrv   = $env['CACHE_STORE']   ?? getenv('CACHE_STORE')   ?: 'database (default)';
$redisHost  = $env['REDIS_HOST']    ?? getenv('REDIS_HOST')    ?: '127.0.0.1';
$redisPort  = $env['REDIS_PORT']    ?? getenv('REDIS_PORT')    ?: '6379';
$queueConn  = $env['QUEUE_CONNECTION'] ?? getenv('QUEUE_CONNECTION') ?: 'database (default)';

echo "[" . ms() . "] ENV: APP_ENV=$appEnv, SESSION=$sessionDrv, CACHE=$cacheDrv, QUEUE=$queueConn\n";
echo "[" . ms() . "] DB: host=$dbHost port=$dbPort db=$dbName user=$dbUser\n";
echo "[" . ms() . "] Redis: host=$redisHost port=$redisPort\n";

// Step 3: DB connectivity test (3-second timeout)
echo "[" . ms() . "] Testing DB connection (3s timeout)...\n";
flush();
ob_flush();
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};connect_timeout=3";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $row = $pdo->query("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema='$dbName'")->fetch();
    echo "[" . ms() . "] DB OK - {$row['cnt']} tables\n";
    // Check for sessions table
    $sess = $pdo->query("SHOW TABLES LIKE 'sessions'")->fetch();
    echo "[" . ms() . "] sessions table: " . ($sess ? 'EXISTS' : 'MISSING!') . "\n";
} catch (Throwable $e) {
    echo "[" . ms() . "] DB FAILED: " . $e->getMessage() . "\n";
}

// Step 4: Redis connectivity test (1-second timeout)
if ($redisHost !== '127.0.0.1' || $sessionDrv === 'redis' || $cacheDrv === 'redis' || $queueConn === 'redis') {
    echo "[" . ms() . "] Testing Redis connection (1s timeout)...\n";
    flush();
    ob_flush();
    try {
        $sock = @fsockopen($redisHost, (int)$redisPort, $errno, $errstr, 1);
        if ($sock) {
            fclose($sock);
            echo "[" . ms() . "] Redis OK\n";
        } else {
            echo "[" . ms() . "] Redis FAILED: $errstr (errno=$errno)\n";
        }
    } catch (Throwable $e) {
        echo "[" . ms() . "] Redis ERROR: " . $e->getMessage() . "\n";
    }
}

// Step 5: Cached config check
$configCache = dirname(__DIR__) . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    echo "[" . ms() . "] Config cache EXISTS (" . round(filesize($configCache)/1024) . "KB)\n";
    $cfg = include $configCache;
    echo "[" . ms() . "] Cached session_driver=" . ($cfg['session']['driver'] ?? '?') . "\n";
    echo "[" . ms() . "] Cached cache_store=" . ($cfg['cache']['default'] ?? '?') . "\n";
    echo "[" . ms() . "] Cached queue_connection=" . ($cfg['queue']['default'] ?? '?') . "\n";
} else {
    echo "[" . ms() . "] Config cache MISSING (will load from files on each request)\n";
}

echo "[" . ms() . "] DONE\n";
