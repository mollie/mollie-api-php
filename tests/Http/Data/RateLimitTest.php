<?php

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\RateLimit;
use PHPUnit\Framework\TestCase;

final class RateLimitTest extends TestCase
{
    /**
     * @test
     * @dataProvider documentedHeaders
     */
    public function it_parses_documented_header_examples(
        string $rateLimit,
        string $rateLimitPolicy,
        array $expected
    ): void {
        $parsed = RateLimit::fromHeaders($rateLimit, $rateLimitPolicy);

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame($expected['policy'], $parsed->getPolicy());
        $this->assertSame($expected['remaining'], $parsed->getRemaining());
        $this->assertSame($expected['restoreSeconds'], $parsed->getRestoreSeconds());
        $this->assertSame($expected['burst'], $parsed->getBurst());
        $this->assertSame($expected['quota'], $parsed->getQuota());
        $this->assertSame($expected['windowSeconds'], $parsed->getWindowSeconds());
    }

    public function documentedHeaders(): array
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

    /** @test */
    public function it_keeps_missing_parameters_nullable(): void
    {
        $parsed = RateLimit::fromHeaders('"get-v2-payments";r=15', '"get-v2-payments";q=20');

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->getPolicy());
        $this->assertSame(15, $parsed->getRemaining());
        $this->assertNull($parsed->getRestoreSeconds());
        $this->assertNull($parsed->getBurst());
        $this->assertSame(20, $parsed->getQuota());
        $this->assertNull($parsed->getWindowSeconds());
    }

    /** @test */
    public function it_parses_one_header_when_the_other_is_absent(): void
    {
        $parsed = RateLimit::fromHeaders('"get-v2-payments";r=15;t=2;mollie-burst=60', null);

        $this->assertInstanceOf(RateLimit::class, $parsed);
        $this->assertSame('get-v2-payments', $parsed->getPolicy());
        $this->assertSame(15, $parsed->getRemaining());
        $this->assertNull($parsed->getQuota());
    }

    /**
     * @test
     * @dataProvider malformedHeaders
     */
    public function malformed_headers_return_null(?string $rateLimit, ?string $rateLimitPolicy): void
    {
        $this->assertNull(RateLimit::fromHeaders($rateLimit, $rateLimitPolicy));
    }

    public function malformedHeaders(): array
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

    /** @test */
    public function absent_headers_return_null(): void
    {
        $this->assertNull(RateLimit::fromHeaders(null, null));
    }
}
