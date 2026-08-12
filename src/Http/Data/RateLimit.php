<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

/**
 * Rate-limit state reported by Mollie's RateLimit and RateLimit-Policy headers.
 */
final readonly class RateLimit
{
    public function __construct(
        public ?string $policy = null,
        public ?int $remaining = null,
        public ?int $restoreSeconds = null,
        public ?int $burst = null,
        public ?int $quota = null,
        public ?int $windowSeconds = null,
    ) {
    }

    /**
     * Parse Mollie's RateLimit and RateLimit-Policy response headers.
     *
     * Returns null when both headers are absent or either supplied header is malformed.
     */
    public static function fromHeaders(?string $rateLimit, ?string $rateLimitPolicy): ?self
    {
        if ($rateLimit === null && $rateLimitPolicy === null) {
            return null;
        }

        $state = self::parseHeader($rateLimit);
        $policy = self::parseHeader($rateLimitPolicy);

        if (($rateLimit !== null && $state === null)
            || ($rateLimitPolicy !== null && $policy === null)
        ) {
            return null;
        }

        if ($state !== null && $policy !== null && $state['name'] !== $policy['name']) {
            return null;
        }

        return new self(
            policy: $state['name'] ?? $policy['name'] ?? null,
            remaining: $state['parameters']['r'] ?? null,
            restoreSeconds: $state['parameters']['t'] ?? null,
            burst: $state['parameters']['mollie-burst'] ?? null,
            quota: $policy['parameters']['q'] ?? null,
            windowSeconds: $policy['parameters']['w'] ?? null,
        );
    }

    /**
     * @return array{name: string, parameters: array<string, int>}|null
     */
    private static function parseHeader(?string $header): ?array
    {
        if ($header === null) {
            return null;
        }

        $segments = array_map('trim', explode(';', $header));
        $name = array_shift($segments);

        if ($name === null || ! preg_match('/^"([^"]+)"$/', $name, $matches)) {
            return null;
        }

        $parameters = [];

        foreach ($segments as $segment) {
            if (! preg_match('/^([A-Za-z][A-Za-z0-9-]*)\s*=\s*(\d+)$/', $segment, $parameter)) {
                return null;
            }

            $value = filter_var($parameter[2], FILTER_VALIDATE_INT);

            if ($value === false) {
                return null;
            }

            $parameters[strtolower($parameter[1])] = $value;
        }

        return [
            'name' => $matches[1],
            'parameters' => $parameters,
        ];
    }
}
