<?php
/**
 * Patch vendor/symfony/html-sanitizer/HtmlSanitizer.php to use MastermindsParser
 * as a fallback when Dom\HTMLDocument is unavailable (Azure PHP 8.4 without new DOM ext).
 *
 * Run from project root: php scripts/patch-html-sanitizer.php
 */

$file = __DIR__ . '/../vendor/symfony/html-sanitizer/HtmlSanitizer.php';

if (!file_exists($file)) {
    echo "File not found: $file\n";
    exit(1);
}

$content = file_get_contents($file);

$old = 'PHP_VERSION_ID < 80400 ? new MastermindsParser() : new NativeParser()';
$new = "PHP_VERSION_ID < 80400 || !class_exists('Dom\\\\HTMLDocument') ? new MastermindsParser() : new NativeParser()";

if (str_contains($content, 'class_exists')) {
    echo "Patch already applied — skipping.\n";
    exit(0);
}

if (!str_contains($content, $old)) {
    echo "WARNING: Could not find expected string to replace in $file\n";
    echo "The HtmlSanitizer version may have changed. Manual review needed.\n";
    echo "Continuing without patch (AppServiceProvider fallback will handle this).\n";
    exit(0);
}

$patched = str_replace($old, $new, $content);
file_put_contents($file, $patched);

echo "Patch applied successfully to $file\n";
echo "Parser selection now: $new\n";
