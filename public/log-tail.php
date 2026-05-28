<?php
// Temporary diagnostic: shows last Laravel log lines
// Secured via token - remove this file after debugging
if (($_GET['token'] ?? '') !== 'studai-debug-2026') {
    http_response_code(403);
    die('Forbidden');
}

$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    die('Log file not found: ' . $logFile);
}

// Get last 150 lines
$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$last = array_slice($lines, -150);

header('Content-Type: text/plain; charset=utf-8');
echo implode("\n", $last);
