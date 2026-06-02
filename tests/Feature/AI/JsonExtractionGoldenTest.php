<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\AIGoldenTest;
use App\Services\AI\AIEvaluationService;
use App\Services\AI\AIService;
use App\Services\AI\EmbeddingService;
use App\Services\AI\PromptRegistryService;
use ReflectionMethod;
use Tests\TestCase;

/**
 * F12: Golden tests for JSON-extraction prompts confirming the schema still
 * matches when the response comes from the Claude Sonnet fallback model, which
 * commonly wraps JSON in ```json fences or surrounds it with brief prose.
 *
 * These tests exercise the pure evaluation path (no persistence) so they do not
 * depend on the test database schema.
 */
class JsonExtractionGoldenTest extends TestCase
{
    /**
     * Resolve the evaluation service with lightweight collaborator stubs.
     */
    private function evaluationService(): AIEvaluationService
    {
        $this->mock(AIService::class, function ($mock) {
            $mock->shouldReceive('generateText')->andReturn('');
        });

        $this->mock(EmbeddingService::class, function ($mock) {
            $mock->shouldReceive('generate')->andReturn(array_fill(0, 1536, 0.1));
            $mock->shouldReceive('cosineSimilarity')->andReturn(0.9);
        });

        $this->mock(PromptRegistryService::class, function ($mock) {
            $mock->shouldReceive('render')->andReturn('Rendered prompt');
            $mock->shouldReceive('getSystemPrompt')->andReturn('System prompt');
        });

        return app(AIEvaluationService::class);
    }

    /**
     * Build an unsaved golden test fixture (no DB persistence required).
     */
    private function schemaTest(): AIGoldenTest
    {
        return new AIGoldenTest([
            'name' => 'orin_question_schema',
            'category' => 'evaluation',
            'input' => 'Generate a question bank entry',
            'expected_output' => '{}',
            'expected_json_schema' => [
                'required' => ['question_text', 'difficulty', 'max_score'],
                'properties' => [
                    'question_text' => ['type' => 'string'],
                    'difficulty'    => ['type' => 'string'],
                    'max_score'     => ['type' => 'integer'],
                ],
            ],
            'evaluation_type' => AIGoldenTest::EVAL_JSON_SCHEMA,
            'is_active' => true,
        ]);
    }

    /**
     * Invoke the protected evaluate() routine against a model output string.
     *
     * @return array<string, mixed>
     */
    private function evaluateOutput(string $output): array
    {
        $service = $this->evaluationService();

        $method = new ReflectionMethod(AIEvaluationService::class, 'evaluate');
        $method->setAccessible(true);

        return $method->invoke($service, $this->schemaTest(), $output);
    }

    /** @test */
    public function it_matches_schema_for_raw_json_primary_model_output(): void
    {
        $output = '{"question_text":"Explain dependency injection","difficulty":"intermediate","max_score":10}';

        $result = $this->evaluateOutput($output);

        $this->assertTrue($result['passed']);
        $this->assertEquals('json_schema', $result['details']['method']);
    }

    /** @test */
    public function it_matches_schema_for_fenced_json_fallback_model_output(): void
    {
        $output = "Here is the question:\n```json\n{\n  \"question_text\": \"Describe SOLID principles\",\n  \"difficulty\": \"advanced\",\n  \"max_score\": 10\n}\n```";

        $result = $this->evaluateOutput($output);

        $this->assertTrue($result['passed'], 'Fenced Claude-style JSON should match the schema.');
        $this->assertEmpty($result['details']['errors'] ?? []);
    }

    /** @test */
    public function it_flags_missing_required_field_in_fallback_output(): void
    {
        // Missing max_score — schema should fail even with valid fences.
        $output = "```json\n{\"question_text\":\"What is a closure?\",\"difficulty\":\"foundational\"}\n```";

        $result = $this->evaluateOutput($output);

        $this->assertFalse($result['passed']);
        $this->assertNotEmpty($result['details']['errors']);
    }

    /** @test */
    public function it_flags_type_mismatch_in_fallback_output(): void
    {
        // max_score returned as string instead of integer.
        $output = "```json\n{\"question_text\":\"Q\",\"difficulty\":\"foundational\",\"max_score\":\"ten\"}\n```";

        $result = $this->evaluateOutput($output);

        $this->assertFalse($result['passed']);
        $this->assertNotEmpty($result['details']['errors']);
    }
}
