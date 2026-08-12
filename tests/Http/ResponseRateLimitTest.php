<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Http\Data\RateLimit;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResponseRateLimitTest extends TestCase
{
    #[Test]
    public function it_exposes_parsed_rate_limit_headers(): void
    {
        $factory = new Psr17Factory;
        $psrResponse = $factory->createResponse()
            ->withHeader('RateLimit-Policy', '"get-v2-payments";q=20;w=3;mollie-burst=60')
            ->withHeader('RateLimit', '"get-v2-payments";r=15;t=2;mollie-burst=60');

        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();
        $response = new Response(
            $psrResponse,
            $factory->createRequest('GET', 'https://api.mollie.com/v2/payments'),
            $pendingRequest,
        );

        $rateLimit = $response->rateLimit();

        $this->assertInstanceOf(RateLimit::class, $rateLimit);
        $this->assertSame('get-v2-payments', $rateLimit->policy);
        $this->assertSame(15, $rateLimit->remaining);
        $this->assertSame(20, $rateLimit->quota);
    }

    #[Test]
    public function it_returns_null_without_rate_limit_headers(): void
    {
        $factory = new Psr17Factory;

        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();
        $response = new Response(
            $factory->createResponse(),
            $factory->createRequest('GET', 'https://api.mollie.com/v2/payments'),
            $pendingRequest,
        );

        $this->assertNull($response->rateLimit());
    }
}
