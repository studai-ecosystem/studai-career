<?php
/**
 * QA test-account seeder migration.
 *
 * Creates/updates the three test accounts used for QA, staging, and production
 * smoke-testing.  Using a migration guarantees it runs exactly once, is
 * tracked in the migrations table, and executes via `migrate --force` during
 * every new container startup — solving the timing race where PHP-FPM starts
 * before the startup.sh seeder reaches the account-seeding step.
 *
 * Password for every account: "password"
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();

        $accounts = [
            [
                'email'        => 'admin@studai.com',
                'name'         => 'Admin',
                'account_type' => 'admin',
            ],
            [
                'email'        => 'jobseeker@studai.com',
                'name'         => 'Test Job Seeker',
                'account_type' => 'job_seeker',
            ],
            [
                'email'        => 'employer@studai.com',
                'name'         => 'Test Employer',
                'account_type' => 'employer',
            ],
        ];

        $hashedPassword = Hash::make('password');

        foreach ($accounts as $account) {
            // Use DB::table() so soft-deleted rows are visible/restorable.
            $existing = DB::table('users')
                ->where('email', $account['email'])
                ->first();

            if ($existing) {
                DB::table('users')
                    ->where('email', $account['email'])
                    ->update([
                        'name'              => $account['name'],
                        'password'          => $hashedPassword,
                        'account_type'      => $account['account_type'],
                        'is_active'         => 1,
                        'email_verified_at' => $existing->email_verified_at ?? $now,
                        'deleted_at'        => null,
                        'updated_at'        => $now,
                    ]);
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
            }
        }

        // Ensure the test employer has a linked company.
        $employer = DB::table('users')->where('email', 'employer@studai.com')->first();

        if ($employer && empty($employer->company_id)) {
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
            } else {
                $companyId = $company->id;
            }

            DB::table('users')
                ->where('email', 'employer@studai.com')
                ->update(['company_id' => $companyId, 'updated_at' => $now]);
        }
    }

    /**
     * Intentionally empty — removing test-account rows is not desirable.
     */
    public function down(): void {}
};
