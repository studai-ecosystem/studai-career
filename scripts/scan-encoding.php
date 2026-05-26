<?php
declare(strict_types=1);

$viewDir = dirname(__DIR__) . '/resources/views';
$files   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));

// Mojibake patterns to detect
$patterns = [
    'â‚¹'  => 'Rupee sign',
    'â„¢'  => 'Trademark',
    'Â·'   => 'Middle dot',
    'Ã—'   => 'Times sign',
    'â€"'  => 'Em/En dash',
    'â€™'  => 'Right single quote',
    'â€œ'  => 'Left double quote',
    'ðŸ'   => '4-byte emoji',
    'âœ¨'  => 'Sparkles emoji',
    'DŸ'   => 'Alternate emoji prefix',
    'Â®'   => 'Registered',
    'Â©'   => 'Copyright',
    'â€¦'  => 'Ellipsis',
    'â"€'  => 'Box drawing',
];

$total = 0;
foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    foreach ($patterns as $p => $name) {
        if (strpos($content, $p) !== false) {
            // Show context
            $pos  = strpos($content, $p);
            $line = substr_count(substr($content, 0, $pos), "\n") + 1;
            echo "[{$name}] {$file->getFilename()}:{$line}\n";
            $total++;
        }
    }
}

echo $total > 0 ? "\n$total remaining corrupted sequences found.\n" : "\nAll clean! No corrupted sequences remain.\n";
