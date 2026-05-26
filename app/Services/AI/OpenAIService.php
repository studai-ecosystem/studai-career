<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * Compatibility shim — delegates to AIService (Azure OpenAI).
 * Keeps Interview services working without touching each one.
 */
class OpenAIService extends AIService
{
    /**
     * Drop-in replacement for the old OpenAI generateCompletion() call.
     * Accepts a prompt string and an optional options array.
     */
    public function generateCompletion(string $prompt, array $options = []): string
    {
        return $this->generateText($prompt, null, $options);
    }
}
