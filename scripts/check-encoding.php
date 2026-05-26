<?php
declare(strict_types=1);

$filesToCheck = [
    'resources/views/jobs/show.blade.php',
    'resources/views/employer/jobs/create.blade.php',
    'resources/views/employer/dashboard/talent-pipeline.blade.php',
    'resources/views/negotiation/strategy.blade.php',
    'resources/views/skills/learning-paths.blade.php',
    'resources/views/scout/predictive-analytics.blade.php',
];

$patterns = ['â‚¹','â„¢','Â·','Ã—','â€"','â€™','â€œ','ðŸ','âœ¨','DŸ','Â®','Â©'];

foreach ($filesToCheck as $f) {
    $path = dirname(__DIR__) . '/' . $f;
    if (!file_exists($path)) {
        echo "MISSING: $f\n";
        continue;
    }
    $content = file_get_contents($path);
    $found = [];
    foreach ($patterns as $p) {
        if (strpos($content, $p) !== false) {
            $found[] = $p;
        }
    }
    if ($found) {
        echo "HAS CORRUPTION ($f): " . implode(', ', $found) . "\n";
    } else {
        echo "CLEAN: $f\n";
    }
}
