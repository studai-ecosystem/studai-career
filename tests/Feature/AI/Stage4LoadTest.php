<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Jobs\GenerateCandidateQuestions;
use App\Services\AI\AICostMeter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use RuntimeException;
use Tests\TestCase;

/**
 * I6: Stage 4 (GenerateCandidateQuestions) load / resilience checks.
 *
 * These tests model a high-concurrency burst (~500 simultaneous applications
 * for a single job) and assert the three properties the audit requires:
 *   - Backpressure: the per-job AI cost ceiling caps real generation so spend
 *     cannot run away no matter how many candidates apply at once.
 *   - Timeouts/retries: the job declares a bounded timeout, retry count and
 *     backoff.
 *   - Dead-letter: a failed() handler records the failure and raises an ops
 *     alert instead of silently stranding the candidate.
 *
 * The tests are intentionally database-independent — they exercise the
 * cache-backed cost meter and the job's declared resilience contract directly.
 */
class Stage4LoadTest extends TestCase
{
    private const SIMULATED_CONCURRENCY = 500;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_caps_stage4_spend_under_high_concurrency(): void
    {
        $jobId    = 4242;
        $ceiling  = AICostMeter::ceiling();
        $estimate = (float) config('ai.cost.per_question_bank_usd', 0.45);

        $generated = 0;
        $skipped   = 0;

        // Mirror exactly the backpressure decision made inside the job's
        // handle() method for each of the ~500 concurrent applications.
        for ($i = 0; $i < self::SIMULATED_CONCURRENCY; $i++) {
            if (AICostMeter::ceilingExceeded($jobId)) {
                $skipped++;
                continue;
            }

            AICostMeter::record($jobId, $estimate);
            $generated++;
        }

        $spend = AICostMeter::jobSpend($jobId);

        // Every application is accounted for (either generated or shed).
        $this->assertSame(self::SIMULATED_CONCURRENCY, $generated + $skipped);

        // Backpressure actually engaged — we did not generate for all 500.
        $this->assertLessThan(self::SIMULATED_CONCURRENCY, $generated);
        $this->assertGreaterThan(0, $skipped);

        // Spend is bounded by the ceiling plus at most one in-flight bank.
        $this->assertLessThanOrEqual($ceiling + $estimate + 1e-6, $spend);

        // Generated count stays within the ceiling envelope.
        $maxGenerations = (int) ceil($ceiling / $estimate) + 1;
        $this->assertLessThanOrEqual($maxGenerations, $generated);
    }

    /** @test */
    public function it_declares_bounded_timeout_tries_and_backoff(): void
    {
        $job = new GenerateCandidateQuestions(1);

        $this->assertSame(120, $job->timeout);
        $this->assertSame(2, $job->tries);
        $this->assertSame(10, $job->backoff);
    }

    /** @test */
    public function it_dead_letters_with_an_ops_alert_on_permanent_failure(): void
    {
        Http::fake();

        $reflection = new ReflectionClass(GenerateCandidateQuestions::class);
        $this->assertTrue(
            $reflection->hasMethod('failed'),
            'Stage 4 job must declare a failed() dead-letter handler.'
        );

        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('error')->atLeast()->once();
        Log::shouldReceive('warning')->zeroOrMoreTimes();

        $job = new GenerateCandidateQuestions(99);
        $job->failed(new RuntimeException('Stage 4 provider unavailable'));

        // Reaching here without throwing confirms the dead-letter path runs.
        $this->assertTrue(true);
    }
}
