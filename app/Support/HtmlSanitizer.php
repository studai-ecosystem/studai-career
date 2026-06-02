<?php

declare(strict_types=1);

namespace App\Support;

/**
 * F9: Dependency-free HTML sanitiser for AI-generated content that is injected
 * into emails. Parses the HTML and rebuilds it from an explicit allowlist of
 * tags, dropping every attribute (so no href/style/onerror/script vectors
 * survive). This prevents stored/reflected XSS and email-injection attacks
 * originating from model output.
 */
final class HtmlSanitizer
{
    /**
     * Tags permitted in AI-generated feedback / email body content.
     *
     * @var array<int, string>
     */
    private const DEFAULT_ALLOWED = ['h1', 'h2', 'h3', 'h4', 'p', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'br', 'span'];

    /**
     * Clean untrusted HTML down to a safe allowlisted subset.
     *
     * @param array<int, string>|null $allowedTags
     */
    public static function clean(string $html, ?array $allowedTags = null): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        // Strip code fences the model sometimes emits despite instructions.
        $html = preg_replace('/```html|```/i', '', $html) ?? $html;

        // Hard-remove obviously dangerous blocks before DOM parsing.
        $html = preg_replace('#<\s*(script|style|iframe|object|embed|link|meta)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;

        $allowed = $allowedTags ?? self::DEFAULT_ALLOWED;

        $allowable = '<' . implode('><', $allowed) . '>';
        $stripped = strip_tags($html, $allowable);

        // Remove any leftover attributes (event handlers, styles, hrefs, etc.).
        $stripped = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $stripped) ?? $stripped;

        // F9/D3: Neutralise CR/LF and other control characters. AI output that
        // is later reused in any header-like context (subject line, MIME header)
        // must not be able to inject headers via embedded \r\n sequences. Tabs
        // and normal spaces are preserved; every other C0 control byte is dropped.
        $stripped = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $stripped) ?? $stripped;
        $stripped = str_replace(["\r\n", "\r", "\n"], ' ', $stripped);

        return trim($stripped);
    }
}
