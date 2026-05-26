<?php
// The file has double-encoded UTF-8 (mojibake):
// Original UTF-8 bytes were misread as Windows-1252 and re-encoded as UTF-8.
// Fix: convert from UTF-8 back to Windows-1252 to recover original UTF-8 bytes,
// then encode non-ASCII as HTML entities for safety.

$file = __DIR__ . '/resources/views/marketplace/index.blade.php';
$content = file_get_contents($file);

// Step 1: Decode the mojibake — convert UTF-8 codepoints back to their
// Windows-1252 byte values (which ARE the original UTF-8 bytes)
$original_bytes = mb_convert_encoding($content, 'Windows-1252', 'UTF-8');

// Step 2: Those bytes are now correct UTF-8. Encode non-ASCII as HTML entities.
$convmap = [0x80, 0x10FFFF, 0, 0xFFFFF];
$result = mb_encode_numericentity($original_bytes, $convmap, 'UTF-8');

// Step 3: Remove the UTF-8 BOM if present at line 1
$result = ltrim($result, "\xEF\xBB\xBF");

file_put_contents($file, $result);
echo "Done. Chars: " . mb_strlen($result) . "\n";
