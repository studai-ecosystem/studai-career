<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$u = App\Models\User::find(2);
$c = $u->company;
echo 'Company: ' . $c->id . PHP_EOL;
$count = App\Models\TalentPoolCandidate::where('company_id',$c->id)->count();
echo 'TalentPool total: ' . $count . PHP_EOL;
try {
    $conv = App\Models\Conversation::where('company_id',$c->id)->count();
    echo 'Conversations: ' . $conv . PHP_EOL;
} catch(Exception $e) {
    echo 'Conv error: ' . $e->getMessage() . PHP_EOL;
}
