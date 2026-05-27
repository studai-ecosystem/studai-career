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

// ---- Ensure test employer has a linked company ----
try {
    $employer = DB::table('users')->where('email', 'employer@studai.com')->first();
    if ($employer && ! $employer->company_id) {
        // Find or create the test company
        $company = DB::table('companies')->where('slug', 'studai-test-company')->first();
        if (! $company) {
            $companyId = DB::table('companies')->insertGetId([
                'name'        => 'StudAI Test Company',
                'slug'        => 'studai-test-company',
                'description' => 'Test company for QA and staging purposes.',
                'is_verified' => 1,
                'is_featured' => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            echo "Created test company (id=$companyId)\n";
        } else {
            $companyId = $company->id;
            echo "Test company already exists (id=$companyId)\n";
        }
        DB::table('users')->where('email', 'employer@studai.com')->update(['company_id' => $companyId]);
        echo "Linked employer@studai.com to company id=$companyId\n";
    } else {
        echo "Employer already has company_id={$employer->company_id}\n";
    }
} catch (\Throwable $e) {
    fwrite(STDERR, "ERROR seeding test company: " . $e->getMessage() . "\n");
}
