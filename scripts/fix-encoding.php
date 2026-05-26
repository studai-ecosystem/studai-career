<?php
declare(strict_types=1);

$viewDir = dirname(__DIR__) . '/resources/views';
$files   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
$fixed   = 0;
$total   = 0;

// Map: corrupted mojibake => clean replacement
$replacements = [
    // Currency
    "\xc3\xa2\xe2\x80\x9a\xc2\xb9" => '&#8377;',      // â‚¹  => ₹
    // Trademark / brand
    "\xc3\xa2\xe2\x80\x9e\xc2\xa2" => '&trade;',       // â„¢  => ™
    "\xc3\x82\xc2\xae"              => '&reg;',         // Â®   => ®
    "\xc3\x82\xc2\xa9"              => '&copy;',        // Â©   => ©
    // Punctuation
    "\xc3\x82\xc2\xb7"              => '&middot;',      // Â·   => ·
    "\xc3\x83\xe2\x80\x94"          => '&times;',       // Ã—   => ×
    "\xc3\xa2\xe2\x82\xac\xe2\x80\x9c" => '&mdash;',   // â€"  => —
    "\xc3\xa2\xe2\x82\xac\xc5\x93" => "'",              // â€™  => '
    "\xc3\xa2\xe2\x82\xac\xc5\x93" => "'",              // â€˜  => '
    "\xc3\xa2\xe2\x82\xac\xc5\x93" => '"',              // â€œ  => "
    "\xc3\xa2\xe2\x82\xac\xc2\xa6" => '...',            // â€¦  => …
    "\xc3\x82\xc2\xa0"              => ' ',              // Â    => nbsp
    // Box drawing (used in Blade comments)
    "\xc3\xa2\xe2\x80\x94\xc2\x80" => '-',              // â"€  => ─
    // Accented chars
    "\xc3\x83\xc2\xa9"              => 'e',              // Ã©  => é
    "\xc3\x83\xc2\xa8"              => 'e',              // Ã¨  => è
    "\xc3\x83\xc2\xa0"              => 'a',              // Ã   => à
    // Star
    "\xc3\xa2\xe2\x98\x85"          => '&#9733;',       // â˜…  => ★
];

// Emoji patterns to strip (mojibake for 4-byte UTF-8 emoji starting with F0 9F)
// These produce ðŸXX in the output — we match them as literal bytes
$emojiPrefixes = [
    "\xc3\xb0\xc5\xb8",   // ðŸ — corrupted F0 9F
    "\x44\xc5\xb8",        // DŸ  — alternate rendering
    "\xc3\xa2\xc5\x93",   // âœ — corrupted E2 9C (3-byte emoji like ✨ ✓)
    "\xc3\xa2\xe2\x98",   // â˜  — corrupted E2 98 (cloud/star/etc)
];

foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $total++;
    $content  = file_get_contents($file->getPathname());
    $original = $content;

    // Apply fixed replacements
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);

    // Strip corrupted emoji sequences: prefix + 2 bytes
    foreach ($emojiPrefixes as $prefix) {
        $len = strlen($prefix);
        $out = '';
        $i   = 0;
        while ($i < strlen($content)) {
            if (substr($content, $i, $len) === $prefix) {
                // Skip prefix + 2 following bytes (the emoji payload bytes)
                $i += $len + 2;
            } else {
                $out .= $content[$i];
                $i++;
            }
        }
        $content = $out;
    }

    // Strip lone trailing Â (C3 82 not followed by another valid continuation)
    $content = preg_replace('/\xc3\x82(?!\xc2[\x80-\xbf])/', '', $content);

    if ($content !== $original) {
        file_put_contents($file->getPathname(), $content);
        $fixed++;
        echo "Fixed: " . $file->getFilename() . "\n";
    }
}

echo "\nDone. Fixed {$fixed} / {$total} blade files.\n";
