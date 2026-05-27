<?php
/**
 * Production seed script: Ensures test accounts exist and passwords are known.
 * Uses DB::table() directly to bypass Eloquent SoftDeletes global scope,
 * preventing unique-constraint failures when a soft-deleted user exists.
 * Run once via: php reset-password.php
 */
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$accounts = [
    ['email' => 'admin@studai.com',     'password' => 'password', 'account_type' => 'admin',      'name' => 'Admin'],
    ['email' => 'jobseeker@studai.com', 'password' => 'password', 'account_type' => 'job_seeker', 'name' => 'Test Job Seeker'],
    ['email' => 'employer@studai.com',  'password' => 'password', 'account_type' => 'employer',   'name' => 'Test Employer'],
];

$now = now()->toDateTimeString();

foreach ($accounts as $account) {
    try {
        // Use DB::table() so we find the row even if it is soft-deleted.
        $existing = DB::table('users')->where('email', $account['email'])->first();

        $hashedPassword = Hash::make($account['password']);

        if ($existing) {
            DB::table('users')->where('email', $account['email'])->update([
                'name'              => $account['name'],
                'password'          => $hashedPassword,
                'account_type'      => $account['account_type'],
                'is_active'         => 1,
                'email_verified_at' => $existing->email_verified_at ?? $now,
                'deleted_at'        => null, // restore if soft-deleted
                'updated_at'        => $now,
            ]);
            $restored = $existing->deleted_at !== null ? ' (restored from soft-delete)' : '';
            echo "Updated {$account['email']}: type={$account['account_type']}{$restored}\n";
        } else {
            DB::table('users')->insert([
                'name'              => $account['name'],
                'email'             => $account['email'],
                'password'          => $hashedPassword,
                'account_type'      => $account['account_type'],
                'is_active'         => 1,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
                'deleted_at'        => null,
            ]);
            echo "Created {$account['email']}: type={$account['account_type']}\n";
        }
    } catch (\Throwable $e) {
        fwrite(STDERR, "ERROR seeding {$account['email']}: " . $e->getMessage() . "\n");
    }
}

echo "\nDone. Test accounts ready.\n";
