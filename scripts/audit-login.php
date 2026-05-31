<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

foreach (User::select('id', 'email', 'account_type')->get() as $u) {
    echo "{$u->id}\t{$u->account_type}\t{$u->email}\n";
}

// Ensure a known job_seeker + employer for UI audit
$seeker = User::where('account_type', 'job_seeker')->first();
if ($seeker) {
    $seeker->password = Hash::make('audit1234');
    $seeker->save();
    echo "SEEKER_LOGIN={$seeker->email}\n";
}

$employer = User::where('account_type', 'employer')->first();
if ($employer) {
    $employer->password = Hash::make('audit1234');
    $employer->save();
    echo "EMPLOYER_LOGIN={$employer->email}\n";
}
