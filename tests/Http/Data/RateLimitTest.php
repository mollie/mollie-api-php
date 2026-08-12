<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\RateLimit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RateLimitTest extends TestCase
{
    #[DataProvider('documentedHeaders')]
    #[Test]
    public function it_parses_documented_header_examples(
        string $rateLimit,
        string $rateLimitPolicy,
        array $expected,
    ): void {
        $parsed = RateLimit::fromHeaders($rateLimit, $rateLimitPolicy);

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame($expected['policy'], $parsed->policy);
        $this->assertSame($expected['remaining'], $parsed->remaining);
        $this->assertSame($expected['restoreSeconds'], $parsed->restoreSeconds);
        $this->assertSame($expected['burst'], $parsed->burst);
        $this->assertSame($expected['quota'], $parsed->quota);
        $this->assertSame($expected['windowSeconds'], $parsed->windowSeconds);
    }

    public static function documentedHeaders(): array
    {
        return [
            '200 response' => [
                '"get-v2-payments";r=15;t=2;mollie-burst=60',
                '"get-v2-payments";q=20;w=3;mollie-burst=60',
                [
                    'policy' => 'get-v2-payments',
                    'remaining' => 15,
                    'restoreSeconds' => 2,
                    'burst' => 60,
                    'quota' => 20,
                    'windowSeconds' => 3,
                ],
            ],
            '429 response' => [
                '"get-v2-payments";r=0;t=1;mollie-burst=0',
                '"get-v2-payments";q=20;w=1;mollie-burst=60',
                [
                    'policy' => 'get-v2-payments',
                    'remaining' => 0,
                    'restoreSeconds' => 1,
                    'burst' => 0,
                    'quota' => 20,
                    'windowSeconds' => 1,
                ],
            ],
        ];
    }

    #[Test]
    public function it_keeps_missing_parameters_nullable(): void
    {
        $parsed = RateLimit::fromHeaders(
            '"get-v2-payments";r=15',
            '"get-v2-payments";q=20',
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->policy);
        $this->assertSame(15, $parsed->remaining);
        $this->assertNull($parsed->restoreSeconds);
        $this->assertNull($parsed->burst);
        $this->assertSame(20, $parsed->quota);
        $this->assertNull($parsed->windowSeconds);
    }

    #[Test]
    public function it_parses_one_header_when_the_other_is_absent(): void
    {
        $parsed = RateLimit::fromHeaders(
            '"get-v2-payments";r=15;t=2;mollie-burst=60',
            null,
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->policy);
        $this->assertSame(15, $parsed->remaining);
        $this->assertNull($parsed->quota);
        $this->assertNull($parsed->windowSeconds);
    }

    #[Test]
    public function it_matches_policies_in_comma_joined_header_lists(): void
    {
        $parsed = RateLimit::fromHeaders(
            '"get-v2-refunds";r=3, "get-v2-payments";r=7',
            '"post-v2-payments";q=5, "get-v2-payments";q=20',
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->policy);
        $this->assertSame(7, $parsed->remaining);
        $this->assertSame(20, $parsed->quota);
    }

    #[Test]
    public function it_skips_invalid_members_in_comma_joined_header_lists(): void
    {
        $parsed = RateLimit::fromHeaders(
            'invalid, "get-v2-payments";r=15',
            '"get-v2-payments";q=20',
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame(15, $parsed->remaining);
        $this->assertSame(20, $parsed->quota);
    }

    #[Test]
    public function it_keeps_valid_sibling_header_when_one_header_is_malformed(): void
    {
        $parsed = RateLimit::fromHeaders(
            '"get-v2-payments";r=many',
            '"get-v2-payments";q=20',
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->policy);
        $this->assertNull($parsed->remaining);
        $this->assertSame(20, $parsed->quota);
    }

    #[Test]
    public function it_accepts_zero_padded_integers_and_ignores_unknown_extensions(): void
    {
        $parsed = RateLimit::fromHeaders(
            '"get-v2-payments";r=07;future=value',
            '"get-v2-payments";q=020',
        );

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame(7, $parsed->remaining);
        $this->assertSame(20, $parsed->quota);
    }

    #[DataProvider('malformedHeaders')]
    #[Test]
    public function malformed_headers_return_null(?string $rateLimit, ?string $rateLimitPolicy): void
    {
        $this->assertNull(RateLimit::fromHeaders($rateLimit, $rateLimitPolicy));
    }

    public static function malformedHeaders(): array
    {
        return [
            'unclosed policy name' => ['"get-v2-payments;r=15', null],
            'invalid parameter' => ['"get-v2-payments";r=many', null],
            'empty parameter' => ['"get-v2-payments";', null],
            'conflicting policies' => [
                '"get-v2-payments";r=15',
                '"post-v2-payments";q=20',
            ],
        ];
    }

    #[Test]
    public function absent_headers_return_null(): void
    {
        $this->assertNull(RateLimit::fromHeaders(null, null));
    }
}
