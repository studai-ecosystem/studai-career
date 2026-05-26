<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate what TalentPoolController does
try {
    $u = App\Models\User::find(2);
    $c = $u->company;
    $search = null; $tags = []; $rating = null; $source = null;
    
    $query = App\Models\TalentPoolCandidate::where('company_id', $c->id)
        ->where('is_active', true)
        ->with(['candidate.profile', 'addedBy']);
    
    $candidates = $query->paginate(20);
    
    $allTags = App\Models\TalentPoolCandidate::where('company_id', $c->id)
        ->whereNotNull('tags')
        ->pluck('tags')
        ->flatten()
        ->unique()
        ->sort()
        ->values();
    
    echo 'Controller logic OK!' . PHP_EOL;
    echo 'Candidates: ' . $candidates->count() . PHP_EOL;
} catch(Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
