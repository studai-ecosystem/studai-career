<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeedTestAccounts extends Command
{
    protected $signature = 'studai:seed-test-accounts';

    protected $description = 'Ensure production test accounts exist with known passwords (idempotent)';

    public function handle(): int
    {
        $accounts = [
            ['email' => 'admin@studai.com',              'password' => 'password', 'account_type' => 'admin',      'name' => 'Admin'],
            ['email' => 'jobseeker@studai.com',          'password' => 'password', 'account_type' => 'job_seeker', 'name' => 'Test Job Seeker'],
            ['email' => 'employer@studai.com',           'password' => 'password', 'account_type' => 'employer',   'name' => 'Test Employer'],
            ['email' => 'tharinimicrosoft@gmail.com',    'password' => 'password', 'account_type' => 'employer',   'name' => 'NexHire AI'],
            ['email' => 'onestudai@gmail.com',           'password' => 'password', 'account_type' => 'employer',   'name' => 'ACME'],
        ];

        $now = now()->toDateTimeString();

        foreach ($accounts as $account) {
            $hashedPassword = Hash::make($account['password']);
            $existing = DB::table('users')->where('email', $account['email'])->first();

            if ($existing) {
                DB::table('users')->where('email', $account['email'])->update([
                    'name'              => $account['name'],
                    'password'          => $hashedPassword,
                    'account_type'      => $account['account_type'],
                    'is_active'         => 1,
                    'email_verified_at' => $existing->email_verified_at ?? $now,
                    'deleted_at'        => null,
                    'updated_at'        => $now,
                ]);
                $this->info("Updated: {$account['email']} (type={$account['account_type']})");
            } else {
                DB::table('users')->insert([
                    'name'              => $account['name'],
                    'email'             => $account['email'],
                    'password'          => $hashedPassword,
                    'account_type'      => $account['account_type'],
                    'is_active'         => 1,
                    'email_verified_at' => $now,
                    'deleted_at'        => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
                $this->info("Inserted: {$account['email']} (type={$account['account_type']})");
            }
        }

        // Seed test company for employer
        $employer = DB::table('users')->where('email', 'employer@studai.com')->first();
        if ($employer && ! $employer->company_id) {
            $company = DB::table('companies')->where('slug', 'studai-test-company')->first();
            if (! $company) {
                $companyId = DB::table('companies')->insertGetId([
                    'name'         => 'StudAI Test Company',
                    'slug'         => 'studai-test-company',
                    'is_verified'  => 1,
                    'is_featured'  => 0,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            } else {
                $companyId = $company->id;
            }
            DB::table('users')->where('email', 'employer@studai.com')
                ->update(['company_id' => $companyId, 'updated_at' => $now]);
            $this->info("Linked employer@studai.com to company id={$companyId}");
        }

        // Seed NexHire AI company for tharinimicrosoft@gmail.com
        $nexhireUser = DB::table('users')->where('email', 'tharinimicrosoft@gmail.com')->first();
        if ($nexhireUser && ! $nexhireUser->company_id) {
            $nexhireCo = DB::table('companies')->where('slug', 'nexhire-ai')->first();
            if (! $nexhireCo) {
                $nexhireCoId = DB::table('companies')->insertGetId([
                    'name'         => 'NexHire AI',
                    'slug'         => 'nexhire-ai',
                    'is_verified'  => 1,
                    'is_featured'  => 0,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            } else {
                $nexhireCoId = $nexhireCo->id;
            }
            DB::table('users')->where('email', 'tharinimicrosoft@gmail.com')
                ->update(['company_id' => $nexhireCoId, 'updated_at' => $now]);
            $this->info("Linked tharinimicrosoft@gmail.com to NexHire AI company id={$nexhireCoId}");
        }

        // Seed Acme Technologies company for onestudai@gmail.com
        $acmeUser = DB::table('users')->where('email', 'onestudai@gmail.com')->first();
        if ($acmeUser && ! $acmeUser->company_id) {
            $acmeCo = DB::table('companies')->where('slug', 'acme-technologies')->first();
            if (! $acmeCo) {
                $acmeCoId = DB::table('companies')->insertGetId([
                    'name'         => 'Acme Technologies Pvt Ltd',
                    'slug'         => 'acme-technologies',
                    'is_verified'  => 1,
                    'is_featured'  => 0,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            } else {
                $acmeCoId = $acmeCo->id;
            }
            DB::table('users')->where('email', 'onestudai@gmail.com')
                ->update(['company_id' => $acmeCoId, 'updated_at' => $now]);
            $this->info("Linked onestudai@gmail.com to Acme Technologies company id={$acmeCoId}");
        }

        // Verify passwords match
        $this->info('Verifying passwords...');
        foreach ($accounts as $account) {
            $row = DB::table('users')->where('email', $account['email'])->whereNull('deleted_at')->first();
            if ($row && Hash::check($account['password'], $row->password)) {
                $this->info("OK: {$account['email']} password verified");
            } else {
                $this->error("FAIL: {$account['email']} password check failed (row=" . ($row ? 'found' : 'missing') . ')');
            }
        }

        $this->info('Test accounts seeded successfully.');

        return Command::SUCCESS;
    }
}
