<?php

namespace Mollie\Api\Http\Data;

/**
 * Rate-limit state reported by Mollie's RateLimit headers.
 */
final class RateLimit
{
    private ?string $policy;

    private ?int $remaining;

    private ?int $restoreSeconds;

    private ?int $burst;

    private ?int $quota;

    private ?int $windowSeconds;

    public function __construct(
        ?string $policy = null,
        ?int $remaining = null,
        ?int $restoreSeconds = null,
        ?int $burst = null,
        ?int $quota = null,
        ?int $windowSeconds = null
    ) {
        $this->policy = $policy;
        $this->remaining = $remaining;
        $this->restoreSeconds = $restoreSeconds;
        $this->burst = $burst;
        $this->quota = $quota;
        $this->windowSeconds = $windowSeconds;
    }

    public function getPolicy(): ?string
    {
        return $this->policy;
    }

    public function getRemaining(): ?int
    {
        return $this->remaining;
    }

    public function getRestoreSeconds(): ?int
    {
        return $this->restoreSeconds;
    }

    public function getBurst(): ?int
    {
        return $this->burst;
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function getWindowSeconds(): ?int
    {
        return $this->windowSeconds;
    }

    /**
     * Parse Mollie's RateLimit and RateLimit-Policy response headers.
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
            $state['name'] ?? $policy['name'] ?? null,
            $state['parameters']['r'] ?? null,
            $state['parameters']['t'] ?? null,
            $state['parameters']['mollie-burst'] ?? null,
            $policy['parameters']['q'] ?? null,
            $policy['parameters']['w'] ?? null
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

        return ['name' => $matches[1], 'parameters' => $parameters];
    }
}
