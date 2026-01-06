<?php

namespace Tests\Http\Middleware;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\ForbiddenException;
use Mollie\Api\Exceptions\MethodNotAllowedException;
use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Exceptions\RequestTimeoutException;
use Mollie\Api\Exceptions\ServiceUnavailableException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\UnauthorizedException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Middleware\ConvertResponseToException;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConvertResponseToExceptionTest extends TestCase
{
    /**
     * @dataProvider provideStatusCodesAndExceptions
     *
     * @test
     */
    public function middleware_converts_response_to_appropriate_exception(
        int $statusCode,
        string $expectedExceptionClass,
        ?string $field
    ): void {
        /** @var Response&MockObject $response */
        $response = $this->createMock(Response::class);
        $response->method('successful')->willReturn(false);
        $response->method('status')->willReturn($statusCode);
        $response->method('json')->willReturn((object) [
            'title' => 'Test',
            'detail' => 'Test detail',
            'field' => $field,
        ]);
        $response->method('getPsrRequest')->willReturn(new Request('GET', ''));

        $middleware = new ConvertResponseToException;

        $this->expectException($expectedExceptionClass);

        $middleware($response);
    }

    public static function provideStatusCodesAndExceptions(): array
    {
        $statusMap = [
            ResponseStatusCode::HTTP_UNAUTHORIZED => UnauthorizedException::class,
            ResponseStatusCode::HTTP_FORBIDDEN => ForbiddenException::class,
            ResponseStatusCode::HTTP_NOT_FOUND => NotFoundException::class,
            ResponseStatusCode::HTTP_METHOD_NOT_ALLOWED => MethodNotAllowedException::class,
            ResponseStatusCode::HTTP_REQUEST_TIMEOUT => RequestTimeoutException::class,
            ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY => ValidationException::class,
            ResponseStatusCode::HTTP_TOO_MANY_REQUESTS => TooManyRequestsException::class,
            ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE => ServiceUnavailableException::class,
            999 => ApiException::class,
        ];

        return array_merge(...array_map(
            fn ($status, $exception) => [
                [$status, $exception, null],
                [$status, $exception, 'foo'],
            ],
            array_keys($statusMap),
            array_values($statusMap)
        ));
    }
}
