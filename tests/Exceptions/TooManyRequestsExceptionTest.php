<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class TooManyRequestsExceptionTest extends TestCase
{
    /** @test */
    public function parses_integer_retry_after_header(): void
    {
        $response = $this->mockResponseWithHeader('120');

        $exception = TooManyRequestsException::fromResponse($response);

        self::assertSame(120, $exception->retryAfterSeconds);
        self::assertSame(120, $exception->getRetryAfterSeconds());
    }

    /** @test */
    public function parses_http_date_retry_after_header(): void
    {
        $future = (new \DateTimeImmutable('+90 seconds'))->format('D, d M Y H:i:s \G\M\T');
        $response = $this->mockResponseWithHeader($future);

        $exception = TooManyRequestsException::fromResponse($response);

        self::assertNotNull($exception->retryAfterSeconds);
        // allow small drift from test runtime
        self::assertGreaterThanOrEqual(80, $exception->retryAfterSeconds);
        self::assertLessThanOrEqual(100, $exception->retryAfterSeconds);
    }

    /** @test */
    public function returns_null_when_retry_after_header_missing(): void
    {
        $response = $this->mockResponseWithHeader(null);

        $exception = TooManyRequestsException::fromResponse($response);

        self::assertNull($exception->retryAfterSeconds);
    }

    /** @test */
    public function returns_null_when_retry_after_header_unparseable(): void
    {
        $response = $this->mockResponseWithHeader('not-a-date');

        $exception = TooManyRequestsException::fromResponse($response);

        self::assertNull($exception->retryAfterSeconds);
    }

    private function mockResponseWithHeader(?string $retryAfter): Response
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn((object) [
            'status' => 429,
            'title' => 'Too Many Requests',
            'detail' => 'Rate limit exceeded.',
        ]);
        $response->method('header')->willReturnCallback(
            static fn (string $name) => strcasecmp($name, 'Retry-After') === 0 ? $retryAfter : null,
        );
        $response->method('status')->willReturn(429);
        $response->method('getPsrRequest')->willReturn(new Request('GET', ''));

        return $response;
    }
}
