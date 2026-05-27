<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $panel = Filament\Facades\Filament::getDefaultPanel();
    echo "Panel: " . $panel->getId() . PHP_EOL;
    echo "Resources count: " . count($panel->getResources()) . PHP_EOL;
    echo "Pages count: " . count($panel->getPages()) . PHP_EOL;
    echo "Widgets count: " . count($panel->getWidgets()) . PHP_EOL;

    // Try to get navigation badge for each resource
    foreach ($panel->getResources() as $resource) {
        try {
            $badge = $resource::getNavigationBadge();
        } catch (Throwable $e) {
            echo "BADGE FAIL [{$resource}]: " . $e->getMessage() . PHP_EOL;
        }
    }
    echo "Done" . PHP_EOL;
} catch (Throwable $e) {
    echo "PANEL LOAD ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
