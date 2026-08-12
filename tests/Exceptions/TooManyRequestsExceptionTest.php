<?php

namespace Tests\Exceptions;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class TooManyRequestsExceptionTest extends TestCase
{
    /** @test */
    public function it_parses_integer_retry_after_headers(): void
    {
        $exception = TooManyRequestsException::fromResponse($this->responseWithRetryAfter('120'));

        self::assertSame(120, $exception->getRetryAfterSeconds());
    }

    /** @test */
    public function it_parses_http_date_retry_after_headers(): void
    {
        $future = (new \DateTimeImmutable('+90 seconds'))->format('D, d M Y H:i:s \G\M\T');
        $seconds = TooManyRequestsException::fromResponse($this->responseWithRetryAfter($future))
            ->getRetryAfterSeconds();

        self::assertNotNull($seconds);
        self::assertGreaterThanOrEqual(80, $seconds);
        self::assertLessThanOrEqual(100, $seconds);
    }

    /** @test */
    public function it_returns_null_when_retry_after_is_missing(): void
    {
        $exception = TooManyRequestsException::fromResponse($this->responseWithRetryAfter(null));

        self::assertNull($exception->getRetryAfterSeconds());
    }

    /** @test */
    public function it_returns_null_when_retry_after_is_unparseable(): void
    {
        $exception = TooManyRequestsException::fromResponse($this->responseWithRetryAfter('not-a-date'));

        self::assertNull($exception->getRetryAfterSeconds());
    }

    private function responseWithRetryAfter(?string $retryAfter): Response
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn((object) [
            'title' => 'Too Many Requests',
            'detail' => 'Rate limit exceeded.',
        ]);
        $response->method('header')->willReturnCallback(
            static fn (string $name) => strcasecmp($name, 'Retry-After') === 0 ? $retryAfter : null
        );
        $response->method('getPsrRequest')->willReturn(new Request('GET', ''));

        return $response;
    }
}
