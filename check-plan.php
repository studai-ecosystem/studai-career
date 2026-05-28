<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$plan = \App\Models\SubscriptionPlan::first();
if ($plan) {
    echo "COLUMNS:\n";
    print_r(array_keys($plan->getAttributes()));
} else {
    echo "No plan found\n";
}
