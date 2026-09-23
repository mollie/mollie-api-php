<?php

namespace Tests\Http;

use Mollie\Api\Http\Data\RateLimit;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

final class ResponseRateLimitTest extends TestCase
{
    /** @test */
    public function it_exposes_headers_and_parsed_rate_limit_state(): void
    {
        $factory = new Psr17Factory;
        $psrResponse = $factory->createResponse()
            ->withHeader('RateLimit-Policy', '"get-v2-payments";q=20;w=3;mollie-burst=60')
            ->withHeader('RateLimit', '"get-v2-payments";r=15;t=2;mollie-burst=60');
        $response = $this->response($psrResponse, $factory);

        $rateLimit = $response->rateLimit();

        $this->assertSame('"get-v2-payments";r=15;t=2;mollie-burst=60', $response->header('ratelimit'));
        $this->assertArrayHasKey('RateLimit', $response->headers());
        $this->assertInstanceOf(RateLimit::class, $rateLimit);
        $this->assertSame('get-v2-payments', $rateLimit->getPolicy());
        $this->assertSame(15, $rateLimit->getRemaining());
        $this->assertSame(20, $rateLimit->getQuota());
    }

    /** @test */
    public function it_returns_null_without_rate_limit_headers(): void
    {
        $factory = new Psr17Factory;
        $response = $this->response($factory->createResponse(), $factory);

        $this->assertNull($response->header('RateLimit'));
        $this->assertNull($response->rateLimit());
    }

    private function response(\Psr\Http\Message\ResponseInterface $psrResponse, Psr17Factory $factory): Response
    {
        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();

        return new Response(
            $psrResponse,
            $factory->createRequest('GET', 'https://api.mollie.com/v2/payments'),
            $pendingRequest
        );
    }
}
