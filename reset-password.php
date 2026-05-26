<?php
/**
 * Production seed script: Ensures test accounts exist and passwords are known.
 * Run once via: php reset-password.php
 */
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$accounts = [
    ['email' => 'admin@studai.com',    'password' => 'password', 'account_type' => 'admin',     'name' => 'Admin'],
    ['email' => 'jobseeker@studai.com','password' => 'password', 'account_type' => 'job_seeker', 'name' => 'Test Job Seeker'],
    ['email' => 'employer@studai.com', 'password' => 'password', 'account_type' => 'employer',   'name' => 'Test Employer'],
];
foreach ($accounts as $account) {
    $user = \App\Models\User::where('email', $account['email'])->first();
    if ($user) {
        $user->password          = \Hash::make($account['password']);
        $user->is_active         = true;
        $user->account_type      = $account['account_type']; // ensure correct type
        $user->email_verified_at = $user->email_verified_at ?? now(); // ensure verified
        $user->save();
        echo "Updated {$account['email']}: is_active=true, password='{$account['password']}', type={$user->account_type}\n";
    } else {
        $user = \App\Models\User::create([
            'name'              => $account['name'],
            'email'             => $account['email'],
            'password'          => \Hash::make($account['password']),
            'account_type'      => $account['account_type'],
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
        echo "Created {$account['email']}: type={$account['account_type']}, password='{$account['password']}'\n";
    }
}
echo "\nDone. Test accounts ready.\n";
