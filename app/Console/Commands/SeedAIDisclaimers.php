<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ResponsibleAI\AIDisclaimerService;
use Illuminate\Console\Command;

class SeedAIDisclaimers extends Command
{
    protected $signature = 'ai:seed-disclaimers';

    protected $description = 'Seed the default Responsible AI disclaimer templates into the database.';

    public function handle(AIDisclaimerService $service): int
    {
        $this->info('Seeding default AI disclaimers…');

        try {
            AIDisclaimerService::seedDefaults();
            $this->info('✓ AI disclaimers seeded successfully.');
        } catch (\Throwable $e) {
            $this->error('Failed to seed disclaimers: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
