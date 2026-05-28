<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ResponsibleAI\BiasDetectionService;
use Illuminate\Console\Command;

class RunBiasAnalysis extends Command
{
    protected $signature = 'ai:run-bias-analysis
                            {scope=global : Scope of analysis: global, company, or job}
                            {scopeId=0   : Scope entity ID (company_id or job_id). 0 = all.}
                            {--days=30   : Number of days of historical data to analyse}';

    protected $description = 'Run aggregate AI bias detection analysis and store a report.';

    public function handle(BiasDetectionService $service): int
    {
        $scope   = (string) $this->argument('scope');
        $scopeId = (int) $this->argument('scopeId');
        $days    = (int) $this->option('days');

        $this->info("Running bias analysis — scope={$scope}, scopeId={$scopeId}, days={$days}…");

        try {
            $report = $service->runAggregateAnalysis($scope, $scopeId ?: null, $days);

            $this->table(
                ['Attribute', 'Value'],
                [
                    ['Bias Level',        $report->bias_level],
                    ['Severity',          round((float) ($report->bias_severity ?? 0) * 100) . '%'],
                    ['Decisions Analysed', $report->total_decisions_analysed],
                    ['Requires Review',   $report->requires_review ? 'YES' : 'No'],
                    ['Report ID',         $report->id],
                ]
            );

            if ($report->requires_review) {
                $this->warn('⚠  Bias report requires human review. Visit /studai/a-i-bias-reports to review.');
            } else {
                $this->info('✓ No significant bias detected in this period.');
            }
        } catch (\Throwable $e) {
            $this->error('Bias analysis failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
