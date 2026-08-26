<?php

namespace Mollie\Api\Http\Data;

/**
 * Rate-limit state reported by Mollie's RateLimit headers.
 */
final class RateLimit
{
    private const INTEGER_PARAMETERS = [
        'r',
        't',
        'mollie-burst',
        'q',
        'w',
    ];

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

        [$state, $policy] = self::matchingPolicies(
            self::parseHeader($rateLimit),
            self::parseHeader($rateLimitPolicy)
        );

        if ($state === null && $policy === null) {
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
     * @return list<array{name: string, parameters: array<string, int>}>
     */
    private static function parseHeader(?string $header): array
    {
        if ($header === null) {
            return [];
        }

        $policies = [];

        foreach (self::splitListMembers($header) as $member) {
            $policy = self::parsePolicy($member);

            if ($policy !== null) {
                $policies[] = $policy;
            }
        }

        return $policies;
    }

    /**
     * @return array{name: string, parameters: array<string, int>}|null
     */
    private static function parsePolicy(string $member): ?array
    {
        $segments = array_map('trim', explode(';', $member));
        $name = array_shift($segments);

        if ($name === null || ! preg_match('/^"([^"]+)"$/', $name, $matches)) {
            return null;
        }

        $parameters = [];

        foreach ($segments as $segment) {
            if (! preg_match('/^([A-Za-z][A-Za-z0-9-]*)\s*=\s*(.*)$/', $segment, $parameter)) {
                return null;
            }

            $parameterName = strtolower($parameter[1]);

            if (! in_array($parameterName, self::INTEGER_PARAMETERS, true)) {
                continue;
            }

            if (! preg_match('/^\d+$/', $parameter[2])) {
                return null;
            }

            $value = self::parseInteger($parameter[2]);

            if ($value === null) {
                return null;
            }

            $parameters[$parameterName] = $value;
        }

        return ['name' => $matches[1], 'parameters' => $parameters];
    }

    private static function parseInteger(string $digits): ?int
    {
        $digits = ltrim($digits, '0');
        $digits = $digits === '' ? '0' : $digits;
        $value = filter_var($digits, FILTER_VALIDATE_INT);

        return $value === false ? null : $value;
    }

    /**
     * @return list<string>
     */
    private static function splitListMembers(string $header): array
    {
        $members = [];
        $memberStart = 0;
        $insideQuotedString = false;
        $length = strlen($header);

        for ($offset = 0; $offset < $length; $offset++) {
            if ($header[$offset] === '"' && ! self::isEscapedQuote($header, $offset)) {
                $insideQuotedString = ! $insideQuotedString;
            }

            if ($header[$offset] !== ',' || $insideQuotedString) {
                continue;
            }

            $members[] = trim(substr($header, $memberStart, $offset - $memberStart));
            $memberStart = $offset + 1;
        }

        $members[] = trim(substr($header, $memberStart));

        return $members;
    }

    private static function isEscapedQuote(string $header, int $offset): bool
    {
        $precedingBackslashes = 0;

        for ($index = $offset - 1; $index >= 0 && $header[$index] === '\\'; $index--) {
            $precedingBackslashes++;
        }

        return $precedingBackslashes % 2 === 1;
    }

    /**
     * @param list<array{name: string, parameters: array<string, int>}> $states
     * @param list<array{name: string, parameters: array<string, int>}> $policies
     * @return array{array{name: string, parameters: array<string, int>}|null, array{name: string, parameters: array<string, int>}|null}
     */
    private static function matchingPolicies(array $states, array $policies): array
    {
        if ($states === []) {
            return [null, $policies[0] ?? null];
        }

        if ($policies === []) {
            return [$states[0], null];
        }

        foreach ($states as $state) {
            foreach ($policies as $policy) {
                if ($state['name'] === $policy['name']) {
                    return [$state, $policy];
                }
            }
        }

        return [null, null];
    }
}
