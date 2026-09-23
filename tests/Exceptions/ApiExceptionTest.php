<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    #[Test]
    public function message_does_not_include_request_body(): void
    {
        $requestBody = '{"email":"customer@example.com","name":"Jane Doe"}';
        $request = new Request('POST', '', [], $requestBody);

        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn((object) []);
        $response->method('getPsrRequest')->willReturn($request);

        $exception = new ApiException($response, 'API rejected the request.', 422);

        self::assertSame($request, $exception->getRequest());
        self::assertSame($response, $exception->getResponse());
        self::assertStringContainsString('API rejected the request.', $exception->getMessage());
        self::assertStringNotContainsString('Request body:', $exception->getMessage());
        self::assertStringNotContainsString($requestBody, $exception->getMessage());
        self::assertStringNotContainsString('customer@example.com', $exception->getMessage());
        self::assertStringNotContainsString('Jane Doe', $exception->getMessage());
    }
}
