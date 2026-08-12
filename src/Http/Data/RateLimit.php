<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

/**
 * Rate-limit state reported by Mollie's RateLimit and RateLimit-Policy headers.
 */
final readonly class RateLimit
{
    private const INTEGER_PARAMETERS = [
        'r',
        't',
        'mollie-burst',
        'q',
        'w',
    ];

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
     * Returns null when neither header contains a valid policy, or when the valid
     * policies in both headers do not match.
     */
    public static function fromHeaders(?string $rateLimit, ?string $rateLimitPolicy): ?self
    {
        if ($rateLimit === null && $rateLimitPolicy === null) {
            return null;
        }

        [$state, $policy] = self::matchingPolicies(
            self::parseHeader($rateLimit),
            self::parseHeader($rateLimitPolicy),
        );

        if ($state === null && $policy === null) {
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

        return [
            'name' => $matches[1],
            'parameters' => $parameters,
        ];
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
     * @param  list<array{name: string, parameters: array<string, int>}>  $states
     * @param  list<array{name: string, parameters: array<string, int>}>  $policies
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
