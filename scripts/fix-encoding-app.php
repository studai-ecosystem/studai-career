<?php
declare(strict_types=1);

$appDir = dirname(__DIR__) . '/app';
$files  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appDir));
$fixed  = 0;

$replacements = [
    "\xc3\xa2\xe2\x80\x9a\xc2\xb9" => '&#8377;',
    "\xc3\xa2\xe2\x80\x9e\xc2\xa2" => '&trade;',
    "\xc3\x82\xc2\xae"              => '&reg;',
    "\xc3\x82\xc2\xa9"              => '&copy;',
    "\xc3\x82\xc2\xb7"              => '&middot;',
    "\xc3\x83\xe2\x80\x94"          => '&times;',
    "\xc3\xa2\xe2\x82\xac\xe2\x80\x9c" => '&mdash;',
    "\xc3\xa2\xe2\x82\xac\xc5\x93" => "'",
    "\xc3\xa2\xe2\x82\xac\xc2\xa6" => '...',
    "\xc3\x82\xc2\xa0"              => ' ',
    "\xc3\xa2\xe2\x98\x85"          => '&#9733;',
];

foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content  = file_get_contents($file->getPathname());
    $original = $content;
    $content  = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($content !== $original) {
        file_put_contents($file->getPathname(), $content);
        $fixed++;
        echo "Fixed: " . $file->getFilename() . "\n";
    }
}

echo "\nDone. Fixed {$fixed} PHP files in app/.\n";
